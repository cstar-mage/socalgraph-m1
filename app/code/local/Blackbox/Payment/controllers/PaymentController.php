<?php

class Blackbox_Payment_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = strtolower($this->getRequest()->getActionName());
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        return false;
    }

    /**
     * Send Ajax redirect response
     *
     * @return Mage_Checkout_OnepageController
     */
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    public function indexAction()
    {
        $this->_redirect('*/*/create', ['_current' => true]);
    }

    public function createAction()
    {
        if (!$this->_init()) {
            return;
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $defaultBilling = $customer->getDefaultBillingAddress();
        if ($defaultBilling && $defaultBilling->validate() === true) {
            $result = $this->getOnepage()->saveBilling($defaultBilling->getData(), $defaultBilling->getId(), false);
            if ($result) {
                Mage::getSingleton('checkout/session')->addError($result['message']);
                $this->_redirectError('');
                return;
            }
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::registry('current_order');
        $result = $this->getOnepage()->saveShipping($order->getShippingAddress()->getData(), 0);
        if ($result) {
            Mage::getSingleton('checkout/session')->addError($result['message']);
            $this->_redirectError('');
            return;
        }

        $result = $this->getOnepage()->saveShippingMethod($order->getShippingMethod());
        if ($result) {
            Mage::getSingleton('checkout/session')->addError($result['message']);
            $this->_redirectError('');
            return;
        }

//        $this->getOnepage()->getQuote()->collectTotals()->save();

        $this->loadLayout()->renderLayout();
    }

    /**
     * Save checkout billing address
     */
    public function createPostAction()
    {
        if (!$this->_init()) {
            return;
        }

        if ($this->_expireAjax()) {
            return;
        }

        if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            try {
                /** @var Mage_Sales_Model_Order $order */
                $order = Mage::registry('current_order');
                $this->getOnepage()->setOriginalOrder($order);

                do {
                    $data = $this->getRequest()->getPost('billing', array());
                    $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

                    if (isset($data['email'])) {
                        $data['email'] = trim($data['email']);
                    }
                    $result = $this->getOnepage()->saveBilling($data, $customerAddressId, true);
                    if ($result) {
                        break;
                    }

                    $data = $this->getRequest()->getPost('payment', array());
                    if (isset($data['cc_exp_year']) && strlen($data['cc_exp_year']) == 2) {
                        $data['cc_exp_year'] = '20' . $data['cc_exp_year'];
                    }
                    $result = $this->getOnepage()->savePayment($data);
                    if ($result) {
                        break;
                    }

                    // get section and redirect data
                    $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
                    if ($redirectUrl) {
                        throw new \Exception('Payment methods with checkout redirect are not supported.');
                    }

                    $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
                    if ($requiredAgreements) {
                        $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                        $diff = array_diff($requiredAgreements, $postedAgreements);
                        if ($diff) {
                            $result['success'] = false;
                            $result['error'] = -1;
                            $result['message'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                            break;
                        }
                    }

//                    $data = $this->getRequest()->getPost('payment', array());
//                    if ($data) {
//                        $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
//                            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
//                            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
//                            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
//                            | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
//                        $this->getOnepage()->getQuote()->getPayment()->importData($data);
//                    }

                    $this->getOnepage()->saveOrder();

                    $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
                    if ($redirectUrl) {
                        $result['redirect'] = $redirectUrl;
                    }
                    $result['success'] = true;
                    $result['error']   = false;
                } while (false);
            } catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $this->getOnepage()->getQuote()->save();

            $this->_prepareDataJSON($result);
        }
    }

    /**
     * Prepare JSON formatted data for response to client
     *
     * @param $response
     * @return Zend_Controller_Response_Abstract
     */
    protected function _prepareDataJSON($response)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    protected function _initQuote(Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($quote->getAllItems() as $item) {
            $item->isDeleted(true);
        }

        /** @var Mage_Sales_Model_Convert_Order $converter */
        $converter = Mage::getModel('sales/convert_order');

        $converter->toQuote($order, $quote);

        $copyFields = [
            'subtotal',
            'base_subtotal',
            'weight'
        ];
        foreach ($copyFields as $field) {
            $quote->setDataUsingMethod($field, $order->getDataUsingMethod($field));
        }
        $quote->assignCustomer(Mage::getSingleton('customer/session')->getCustomer());

        $copyFields = [
            'base_price' => 'base_price',
            'price' => 'price',
            'qty_ordered' => 'qty',
            'base_price_incl_tax' => 'base_price_incl_tax',
            'price_incl_tax' => 'price_incl_tax'
        ];
        foreach ($order->getAllItems() as $item) {
            $qItem = $converter->itemToQuoteItem($item)->setQuote($quote);
            foreach ($copyFields as $from => $to) {
                $qItem->setDataUsingMethod($to, $item->getDataUsingMethod($from));
            }
            $quote->addItem($qItem);
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($billingAddress = $customer->getDefaultBillingAddress()) {
            $quote->getBillingAddress()->setData($billingAddress->getData());
        }

        $shippingAddress = $converter->toQuoteShippingAddress($order);
        $copyFields = [
            'customer_email' => 'email',
            'customer_firstname' => 'firstname',
            'customer_lastname' => 'lastname',
            'customer_middlename' => 'middlename',
            'customer_prefix' => 'prefix',
            'customer_suffix' => 'suffix',
            'tax_amount' => 'tax_amount',
            'base_tax_amount' => 'base_tax_amount',
            'grand_total' => 'grand_total',
            'base_grand_total' => 'base_grand_total',
            'subtotal' => 'subtotal',
            'base_subtotal' => 'base_subtotal',
            'subtotal_incl_tax' => 'base_subtotal_incl_tax',
            'shipping_method' => 'shipping_method',
            'shipping_description' => 'shipping_description',
            'shipping_amount' => 'shipping_amount',
            'base_shipping_amount' => 'base_shipping_amount',
            'discount_amount' => 'discount_amount',
            'base_discount_amount' => 'base_discount_amount',
            'shipping_tax_amount' => 'shipping_tax_amount',
            'base_shipping_tax_amount' => 'base_shipping_tax_amount',
            'weight' => 'weight'
        ];
        foreach ($copyFields as $from => $to) {
            $shippingAddress->setDataUsingMethod($to, $order->getDataUsingMethod($from));
        }
        $quote->setShippingAddress($shippingAddress);
    }

    protected function _init()
    {
        if (!($order = $this->_initOrder())/* || !($invoice = $this->_initInvoice($order))*/) {
            $this->_redirectError('');
            return false;
        }

        $this->_initQuote($order);

        return true;
    }

    protected function _redirectError($defaultUrl)
    {
        if (!$defaultUrl) {
            /** @var Blackbox_CinemaCloud_Helper_Data $helper */
            $helper = Mage::helper('cinemacloud');
            if ($helper->isCustomerSalesRep()) {
                $defaultUrl = 'customer/salesRep';
            } else if ($helper->isCustomerCSR()) {
                $defaultUrl = 'customer/csr';
            } else {
                $defaultUrl = 'customer/account';
            }
        }
        $this->_redirectReferer(Mage::getUrl($defaultUrl));
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl()
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }

//        if (!$this->_isUrlInternal($refererUrl)) {
//            $refererUrl = Mage::app()->getStore()->getBaseUrl();
//        }
        return $refererUrl;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _initOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The order no longer exists.'));
            return false;
        }

        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Initialize invoice model instance
     *
     * @var Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice(Mage_Sales_Model_Order $order)
    {
        $invoice = false;
        if (!$order->canInvoice() || $order->getTotalInvoiced() > $order->getGrandTotal()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The order does not allow creating an invoice.'));
            return false;
        }
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
        if (!$invoice->getTotalQty()) {
            Mage::throwException($this->__('Cannot create an invoice without products.'));
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Get one page checkout model
     *
     * @return Blackbox_Payment_Model_Checkout_Type_Payment
     */
    public function getOnepage()
    {
        return Mage::getSingleton('blackbox_payment/checkout_type_payment');
    }
}