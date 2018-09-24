<?php

class Blackbox_Checkout_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    protected $_customerEmailExistsMessage = '';

    public function __construct()
    {
        parent::__construct();
        $this->_customerEmailExistsMessage = Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
    }

    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        if ($data['customer_address_id']) {
            $customerAddress = Mage::getModel('customer/address')->load($data['customer_address_id']);
            if (!$customerAddress->getId() || $customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                return array('error' => -1, 'message' => Mage::helper('checkout')->__('Customer Address is not valid.'));
            }

            $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
            $addressForm->setEntity($address);
            $addressErrors  = $addressForm->validateData($address->getData());
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
        } else {
            $storelocator = Mage::getModel('storelocator/storelocator')->load($data['storelocator_id']);
            if (!$storelocator->getId()) {
                return array('error' => -1, 'message' => Mage::helper('checkout')->__('Unknown storelocator'));
            }

            $customer = $this->getQuote()->getCustomer();

            $data = array(
                'prefix' => $customer->getPrefix(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'suffix' => $customer->getSuffix(),
                'email' => $storelocator->getEmail(),
                'street' => [
                    $storelocator->getAddress()
                ],
                'city' => $storelocator->getCity(),
                'region_id' => $storelocator->getStateId(),
                'region' => $storelocator->getState(),
                'postcode' => $storelocator->getZipcode(),
                'country_id' => $storelocator->getCountry(),
                'telephone' => $customer->getTelephone(),
                'fax' => $storelocator->getFax(),
            );

            $addressForm->setEntity($address);
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressForm->compactData($addressData);
            //unset billing address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }
            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(0);
            $address->setStorelocatorId($storelocator->getId());
            $address->setStorelocatorName($storelocator->getName());

            $address->setEmail($data['email']);
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $address->implodeStreetAddress();

        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

            switch ($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress(true);
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress(true);
                    $shippingMethod = $shipping->getShippingMethod();

                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setSaveInAddressBook(0)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    $this->_setCartCouponCode();
                    break;
            }
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        if (!$this->getQuote()->isVirtual() && $this->getCheckout()->getStepData('shipping', 'complete') == true) {
            //Recollect Shipping rates for shipping methods
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $address->setCollectShippingRates(true);
            }
        }

        $this->getCheckout()
            ->setStepData('billing', 'allow', true)
            ->setStepData('billing', 'complete', true)
            ->setStepData('shipping', 'allow', true);

        return array();
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $data['customer_address_id']
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $arr = array_values($data);

        /** @var Mage_Sales_Model_Quote_Address[] $addresses */
        $addresses = $this->getQuote()->getAllShippingAddresses();

        $addressesIds = array_map(function($address) { return $address->getId(); }, $addresses);
        $saveAddressesIds = array_map(function($data) { return $data['address_id']; }, $arr);

        if (count($addressesIds) != count($saveAddressesIds) || !empty(array_diff($addressesIds, $saveAddressesIds))) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Addresses do not match with saved.'));
        }

        $customer = $this->getQuote()->getCustomer();
        $fields = [
            'prefix',
            'firstname',
            'lastname',
            'suffix',
            'telephone'
        ];

        $i = 0;
        foreach ($addresses as $address) {
            foreach ($fields as $field) {
                if (!$address->getData($field)) {
                    $address->setData($field, $customer->getData($field));
                }
            }
            if ($address->hasItems() && ($result = $address->validate()) !== true) {
                return array(
                    'error' => -1,
                    'message' => implode(' ', array_map(function($error) { return Mage::helper('checkout')->__($error); }, $result))
                );
            }
            $address->setCollectShippingRates(true);
            $i++;
        }
        
        $result = $this->updateAddressDates($data);
        if ($result !== true) {
            return $result;
        }

        $result = $this->updateAddressItemQty($data, false);
        if ($result !== true) {
            return $result;
        }

        $this->_setCartCouponCode();

        if ($this->getQuote()->getBillingAddress()->validate() !== true) {
            $defaultBilling = $this->getQuote()->getCustomer()->getDefaultBillingAddress();
            if ($defaultBilling && $defaultBilling->validate() === true) {
                $addressData = $defaultBilling->getData();
            } else {
                $addressData = $this->getQuote()->getShippingAddress(true)->getData();
            }
            unset($addressData['address_id']);
            unset($addressData['address_type']);
            unset($addressData['created_at']);
            unset($addressData['updated_at']);
            $this->getQuote()->getBillingAddress()
                ->addData($addressData);
        }

        $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->collectQuoteItemsData()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        return array();
    }

    public function saveShippingAddress($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $arr = $data;
        $addresses = array();

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $added = false;
        foreach ($arr as $addressData) {
            $addressId = $addressData['address_id'];

            if (!$addressId) {
                $address = Mage::getModel('sales/quote_address');
            } else {
                $address = $this->getQuote()->getAddressById($addressId);
            }

            if ($addressData['customer_address_id']) {
                $customerAddress = Mage::getModel('customer/address')->load($addressData['customer_address_id']);
                if (!$customerAddress->getId() || $customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => -1, 'message' => Mage::helper('checkout')->__('Customer Address is not valid.'));
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            } else {
                $storelocator = Mage::getModel('storelocator/storelocator')->load($addressData['storelocator_id']);
                if (!$storelocator->getId()) {
                    return array('error' => -1, 'message' => Mage::helper('checkout')->__('Unknown storelocator.'));
                }

                $customer = $this->getQuote()->getCustomer();

                $data = array(
                    'prefix' => $customer->getPrefix(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'suffix' => $customer->getSuffix(),
                    'email' => $storelocator->getEmail(),
                    'company' => $storelocator->getName(),
                    'street' => [
                        $storelocator->getAddress()
                    ],
                    'city' => $storelocator->getCity(),
                    'region_id' => $storelocator->getStateId(),
                    'region' => $storelocator->getState(),
                    'postcode' => $storelocator->getZipcode(),
                    'country_id' => $storelocator->getCountry(),
                    'telephone' => $customer->getTelephone(),
                    'fax' => $storelocator->getFax(),
                );

                $addressForm->setEntity($address);
                // emulate request object
                $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
                $addressForm->compactData($addressData);
                // unset shipping address attributes which were not shown in form
                foreach ($addressForm->getAttributes() as $attribute) {
                    if (!isset($data[$attribute->getAttributeCode()])) {
                        $address->setData($attribute->getAttributeCode(), NULL);
                    }
                }

                $address->setCustomerAddressId(null);
                // Additional form data, not fetched by extractData (as it fetches only attributes)
                $address->setSaveInAddressBook(0);
                $address->setSameAsBilling(0);
                $address->setStorelocatorId($storelocator->getId());
                $address->setStorelocatorName($storelocator->getName());
                $address->setEmail($storelocator->getEmail());

            }
            $address->implodeStreetAddress();
            $address->setCollectShippingRates(true);

            if ($addressData['date_advanced_enable'] && ($data['date_advanced']['event_date'] || $data['date_advanced']['shipping_date'])) {
                $dates = array_map(function($date) {
                    return date('Y-m-d', strtotime($date));
                }, $addressData['date_advanced']);
                if (!empty($dates = $this->_getValidOrderDates($dates))) {
                    $address->addData($dates);
                }
            }

            if ($address->isObjectNew()) {
                if (!$added && count($this->getQuote()->getAllShippingAddresses()) <= 1) {
                    $firstAddress = $this->getQuote()->getShippingAddress(false);
                    /** @var Mage_Sales_Model_Quote_Item $item */
                    foreach ($this->getQuote()->getAllVisibleItems() as $item) {
                        $aItem = $firstAddress->getItemByQuoteItemId($item->getId());
                        if ($aItem) {
                            $aItem->setQty($item->getQty());
                        } else {
                            $firstAddress->addItem($item);
                        }
                    }
                    $firstAddress->setDataChanges(true);
                }
                $this->getQuote()->addShippingAddress($address, true);
                $added = true;
            }

            $address->collectShippingRates();

            $addresses[] = $address;
        }

        try {
            $this->_updateQuoteItemsQty();
        } catch (Mage_Core_Exception $e) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__($e->getMessage()));
        } catch (Exception $e) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('An error has occurred while updating address item qtys'));
        }

        $this->getQuote()->collectTotals()->collectQuoteItemsData()->save();

        return array('address_ids' => array_map(function ($address) { return $address->getId(); }, $addresses));
    }

    public function updateAddressItemQty($data, $save = true)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $arr = $data;
        foreach ($arr as $data) {
            $addressId = $data['address_id'];

            if (!$addressId) {
                return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid address_id.'));
            }

            $address = $this->getQuote()->getAddressById($addressId);

            if (!$address) {
                return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid address_id.'));
            }

            foreach ($data['items'] as $qItemId => $qty) {
                $qItem = $this->getQuote()->getItemById($qItemId);
                if (!$qItem) {
                    continue;
                    return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid quote item.'));
                }
                $aItem = $address->getItemByQuoteItemId($qItemId);
                if ($aItem) {
                    if ($qty > 0) {
                        $aItem->setQty($qty);
                    } else {
                        $aItem->isDeleted(true);
                        $address->clearCachedItems();
                    }
                } else if ($qty > 0) {
                    $address->addItem($qItem, $qty);
                }
            }
        }

        try {
            $this->_updateQuoteItemsQty(true);
            if ($save) {
                $this->getQuote()->collectTotals()->collectQuoteItemsData()->save();
            }
        } catch (Mage_Core_Exception $e) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__($e->getMessage()));
        } catch (Exception $e) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('An error has occurred while updating address item qtys'));
        }

        return true;
    }

    public function removeShippingAddress($addressId)
    {
        $address = $this->getQuote()->getAddressById($addressId);
        if (!$address || $address->getAddressType() != 'shipping') {
            Mage::throwException('Wrong address');
        }
        $address->isDeleted(true);
        $this->_updateQuoteItemsQty(true)->getQuote()->collectTotals()->collectQuoteItemsData()->save();
        return true;
    }

    public function validate()
    {
        $quote  = $this->getQuote();

        if ($quote->getCheckoutMethod() == self::METHOD_GUEST && !$quote->isAllowedGuestCheckout()) {
            Mage::throwException(Mage::helper('checkout')->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
        }
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (count($this->getQuote()->getAllShippingAddresses()) <= 1) {
            return parent::saveShippingMethod(current($shippingMethod));
        }
        foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
            if (!$address->hasItems()) {
                continue;
            }
            $rate = $address->getShippingRateByCode($shippingMethod[$address->getId()]);
            if (!$rate) {
                return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
            }
            $address->setShippingMethod($shippingMethod[$address->getId()]);
        }

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }

//    public function savePayment($data = null)
//    {
//        if (empty($data)) {
//            $data['method'] = 'nopayment';
//        }
//        return parent::savePayment($data);
//    }

    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        /** @var Blackbox_Checkout_Model_Sales_Service_Quote $service */
        $service = Mage::getModel('sales/service_quote', $this->getQuote());

        if (count($this->getQuote()->getAllAddresses()) > 2) {
            $this->getQuote()->collectQuoteItemsData();

            $order = $service->submitMetaOrder();

            $orders = $this->createOrders();
            $service->getOrder()->setAssociatedOrders(implode(',',$orders))->save();

            if ($order->getCanSendNewEmailFlag()) {
                try {
                    $order->queueNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
                ->setLastSuccessQuoteId($this->getQuote()->getId())
                ->clearHelperData();
            $this->_checkoutSession->setLastOrderId($order->getId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        } else {
            if (!$this->getQuote()->getTotalsCollectedFlag()) {
                $this->getQuote()->collectTotals();
            }
            $service->submitAll();

            if ($isNewCustomer) {
                try {
                    $this->_involveNewCustomer();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
                ->setLastSuccessQuoteId($this->getQuote()->getId())
                ->clearHelperData();

            $order = $service->getOrder();
            if ($order) {
                Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                    array('order' => $order, 'quote' => $this->getQuote()));

                /**
                 * a flag to set that there will be redirect to third party after confirmation
                 * eg: paypal standard ipn
                 */
                $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
                /**
                 * we only want to send to customer about new order when there is no redirect to third party
                 */
                if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                    try {
                        $order->queueNewOrderEmail();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }

                // add order information to the session
                $this->_checkoutSession->setLastOrderId($order->getId())
                    ->setRedirectUrl($redirectUrl)
                    ->setLastRealOrderId($order->getIncrementId());

                // as well a billing agreement can be created
                $agreement = $order->getPayment()->getBillingAgreement();
                if ($agreement) {
                    $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
                }
            }

            // add recurring profiles information to the session
            $profiles = $service->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach ($profiles as $profile) {
                    $ids[] = $profile->getId();
                }
                $this->_checkoutSession->setLastRecurringProfileIds($ids);
                // TODO: send recurring profile emails
            }

            Mage::dispatchEvent(
                'checkout_submit_all_after',
                array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
            );
        }

        return $this;
    }

    public function createOrders()
    {
        $orderIds = array();
        $orderIncrementIds = array();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        /** @var Mage_Sales_Model_Order[] $orders */
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        try {
            foreach ($shippingAddresses as $address) {
                if (!$address->hasItems()) {
                    $address->isDeleted();
                    continue;
                }
                $order = $this->_prepareOrder($address);

                $orders[] = $order;
                Mage::dispatchEvent(
                    'checkout_type_multishipping_create_orders_single',
                    array('order'=>$order, 'address'=>$address)
                );
            }

            foreach ($orders as $order) {
                $order->place();
                $order->save();
//                if ($order->getCanSendNewEmailFlag()){
//                    $order->queueNewOrderEmail();
//                }
                $orderIds[] = $order->getId();
                $orderIncrementIds[$order->getId()] = $order->getIncrementId();
            }

            Mage::getSingleton('core/session')->setOrderIds($orderIncrementIds);
            Mage::getSingleton('checkout/session')->setLastQuoteId($this->getQuote()->getId())->setOrderIds($orderIncrementIds);

            $this->getQuote()
                ->setIsActive(false)
                ->save();

            Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));

            return $orderIds;
        } catch (Exception $e) {
            Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
            throw $e;
        }
    }

    /**
     * @return Blackbox_Checkout_Model_Sales_Quote
     */
    public function getQuote()
    {
        return parent::getQuote(); // TODO: Change the autogenerated stub
    }

    /**
     * Prepare order based on quote address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Order
     * @throws  Mage_Checkout_Exception
     */
    protected function _prepareOrder(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $this->getQuote();
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
        $quote->collectTotals();

        $convertQuote = Mage::getSingleton('sales/convert_quote');
        $order = $convertQuote->addressToOrder($address);
        $order->setQuote($quote);
        $order->setBillingAddress(
            $convertQuote->addressToOrderAddress($quote->getBillingAddress())
        );

        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        } else {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
        }

        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
        if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
            $order->getPayment()->setMethod('free');
        }

        foreach ($address->getAllItems() as $item) {
            $_quoteItem = $item->getQuoteItem();
            if (!$_quoteItem) {
                throw new Mage_Checkout_Exception(Mage::helper('checkout')->__('Item not found or already ordered'));
            }
            $item->setProductType($_quoteItem->getProductType())
                ->setProductOptions(
                    $_quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($_quoteItem->getProduct())
                );
            $data = $_quoteItem->getData();
            unset($data[$_quoteItem->getIdFieldName()]);
            foreach ($data as $key => $value) {
                if (is_scalar($value) && !$item->hasData($key)) {
                    $item->setData($key, $value);
                }
            }
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        return $order;
    }

    protected function _updateQuoteItemsQty($updateQuoteItemFromAddressItemIfSingleAddress = false)
    {
        $items = [];

        if (count($this->getQuote()->getAllShippingAddresses()) <= 1) {
            foreach ($this->getQuote()->getShippingAddress(false)->getAllItems(true) as $aItem) {
                if ($updateQuoteItemFromAddressItemIfSingleAddress) {
                    $items[$aItem->getQuoteItemId()] += (int)$aItem->getQty();
                }
                $aItem->isDeleted(true);
                $aItem->getAddress()->clearCachedItems();
            }

            if ($updateQuoteItemFromAddressItemIfSingleAddress) {
                /** @var Mage_Sales_Model_Quote_Item $item */
                foreach ($this->getQuote()->getAllVisibleItems() as $item) {
                    $item->setQty((int)$items[$item->getId()]);
                }
            }

            return $this;
        }

        /** @var Blackbox_Checkout_Model_Sales_Quote_Address $address */
        foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
            /** @var Mage_Sales_Model_Quote_Address_Item $item */
            foreach ($address->getAllItems(true) as $item)
            {
                $items[$item->getQuoteItemId()] += (int)$item->getQty();
            }
        }
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $item->setQty($items[$item->getId()] ?: 0);
        }

        return $this;
    }

    protected function _getValidOrderDates($dates)
    {
        return array_filter($dates, function($value) {
            return strtotime($value) > time();
        });
    }
    
    protected function updateAddressDates($arr)
    {
        foreach ($arr as $data) {
            if ($data['date_advanced_enable'] && ($data['date_advanced']['event_date'] || $data['date_advanced']['shipping_date'])) {
                $dates = array_map(function($date) {
                    return date('Y-m-d', strtotime($date));
                }, $data['date_advanced']);
                $dates = $this->_getValidOrderDates($dates);
                if (empty($dates)) {
                    continue;
                }
                
                $address = $this->getQuote()->getAddressById($data['address_id']);
                if (!$address) {
                    return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid address_id.'));
                }
                $address->addData($dates);
            }
        }

        return true;
    }
}