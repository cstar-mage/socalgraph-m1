<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');

class Blackbox_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * Make sure customer is valid, if logged in
     * By default will add error messages and redirect to customer edit form
     *
     * @param bool $redirect - stop dispatch and redirect?
     * @param bool $addErrors - add error messages?
     * @return bool
     */
    protected function _preDispatchValidateCustomer($redirect = true, $addErrors = true)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn() && !Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote()))
        {
            $this->_redirect('customer/account/login');
            return false;
        }
        if (parent::_preDispatchValidateCustomer($redirect, $addErrors)) {
            $validationResult = new Varien_Object(array(
                'error' => false,
                'redirect' => 'checkout/cart/',
                'session' => 'checkout/session',
            ));
            Mage::dispatchEvent('checkout_onepage_validation', array('validation_result' => $validationResult));

            if ($validationResult->getError()) {
                if ($addErrors) {
                    Mage::getSingleton($validationResult->getSession())->addError($validationResult->getError());
                }
                if ($redirect) {
                    $this->_redirect($validationResult->getRedirect());
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }
                return false;
            }

            return true;
        }
        return false;
    }

    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['cart_html'] = $this->getCartHtml();
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->_prepareDataJSON($result);
        }
    }

    public function saveShippingAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

//        if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
//            return;
//        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            try {
                $result = $this->getOnepage()->saveShippingAddress($data);
                if (!isset($result['error'])) {
                    $result['cart_html'] = $this->getCartHtml();
                }
            } catch (Mage_Core_Exception $e) {
                $result['error'] = true;
                $result['message'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = true;
                $result['message'] = 'An error has occured.';
            }
            $this->_prepareDataJSON($result);
        }
    }

    public function updateAddressItemQtyAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

//        if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
//            return;
//        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $result = [];
            try {
                $this->getOnepage()->updateAddressItemQty($data);
//                if (!isset($result['error'])) {
//                    $result['progress_html'] = $this->getLayout()->createBlock('blackbox_checkout/checkout_onepage_products')->toHtml();
//                }

                $result['cart_html'] = $this->getCartHtml();
            } catch (Mage_Core_Exception $e) {
                $result['error'] = true;
                $result['message'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = true;
                $result['message'] = 'An error has occured.';
            }
            $this->_prepareDataJSON($result);
        }
    }

    public function removeShippingAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $result = [];
            try {
                $addressId = $this->getRequest()->getPost('address_id');
                $this->getOnepage()->removeShippingAddress($addressId);
                $result['success'] = true;
                $result['cart_html'] = $this->getCartHtml();
            } catch (Mage_Core_Exception $e) {
                $result['error'] = true;
                $result['message'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = true;
                $result['message'] = 'An error has occured.';
            }
            $this->_prepareDataJSON($result);
        }
    }

    /**
     * Shipping method save action
     */
//    public function saveShippingMethodAction()
//    {
//        if ($this->_expireAjax()) {
//            return;
//        }
//
//        if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
//            return;
//        }
//
//        if ($this->getRequest()->isPost()) {
//            $data = $this->getRequest()->getPost('shipping_method', '');
//            $result = $this->getOnepage()->saveShippingMethod($data);
//            // $result will contain error data if shipping method is empty
//            if (!$result) {
//                Mage::dispatchEvent(
//                    'checkout_controller_onepage_save_shipping_method',
//                    array(
//                        'request' => $this->getRequest(),
//                        'quote'   => $this->getOnepage()->getQuote()));
//                $this->getOnepage()->getQuote()->collectTotals();
//
//                $result = $this->getOnepage()->savePayment();
//
//                if (!isset($result['error'])) {
//                    // get section and redirect data
//                    $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
//                    if ($redirectUrl) {
//                        $result['redirect'] = $redirectUrl;
//                    } else {
//                        $this->loadLayout('checkout_onepage_review');
//                        $result['goto_section'] = 'review';
//                        $result['update_section'] = array(
//                            'name' => 'review',
//                            'html' => $this->_getReviewHtml()
//                        );
//                    }
//                }
//            }
//            $this->getOnepage()->getQuote()->collectTotals()->save();
//            $this->_prepareDataJSON($result);
//        }
//    }

    public function addressHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');

        $this->loadLayout();
        $html = $this->getLayout()->getBlock('checkout.onepage.shipping.form')->setShippingId($id)->toHtml();

        $this->getResponse()->setBody($html);
    }

    public function searchAddressGridAction()
    {
        $html = $this->getLayout()->createBlock('blackbox_checkout/address_grid')->toHtml();

        $this->getResponse()->setBody($html);
    }

    /**
     * @return Blackbox_Checkout_Model_Checkout_Type_Onepage
     */
    public function getOnepage()
    {
        return parent::getOnepage();
    }

    protected function getCartHtml()
    {
        return $this->loadLayout('checkout_onepage_cart')->getLayout()->getBlock('checkout.onepage.cart')->toHtml();
    }
}