<?php

class Blackbox_Checkout_Model_Sales_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    /**
     * Validate quote data before converting to order
     *
     * @return Mage_Sales_Model_Service_Quote
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            if (count($this->getQuote()->getAllShippingAddresses()) <= 1) {
                $address = $this->getQuote()->getShippingAddress();
                $addressValidation = $address->validate();
                if ($addressValidation !== true) {
                    Mage::throwException(
                        Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                    );
                }
                $method= $address->getShippingMethod();
                $rate  = $address->getShippingRateByCode($method);
                if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                    Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
                }
            } else {
                $valid = false;
                foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                    if (!$address->hasItems()) {
                        continue;
                    }
                    $addressValidation = $address->validate();
                    if ($addressValidation !== true) {
                        Mage::throwException(
                            Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                        );
                    }
                    $method= $address->getShippingMethod();
                    $rate  = $address->getShippingRateByCode($method);
                    if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                        Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
                    }
                    $valid = true;
                }
                if (!$this->getQuote()->isVirtual() && !$valid) {
                    Mage::throwException(Mage::helper('sales')->__('Please specify a shipping address.'));
                }
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                Mage::helper('sales')->__('Please check billing address information. %s', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('sales')->__('Please select a valid payment method.'));
        }

        return $this;
    }

    /**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return Mage_Sales_Model_Order
     */
    public function submitOrder()
    {
        $this->_deleteNominalItems();
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('core/resource_transaction');
        if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        $transaction->addObject($quote);

        $quote->reserveOrderId();
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress(true));
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
        if (!$isVirtual) {
            $shippingAddresses = $quote->getAllShippingAddresses();
            $orderAddresses = array();
            foreach($shippingAddresses as $address) {
                $orderAddresses[] = ($orderAddress = $this->_convertor->addressToOrderAddress($address));
                if ($address->getCustomerAddress()) {
                    $orderAddress->setCustomerAddress($address->getCustomerAddress());
                }
            }
            $order->setShippingAddresses($orderAddresses);
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($this->_orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $order->setQuote($quote);

        $transaction->addObject($order);
        $transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        /**
         * We can use configuration data for declare new order status
         */
        Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        Mage::dispatchEvent('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        try {
            $transaction->save();
            $this->_inactivateQuote();
            Mage::dispatchEvent('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
        } catch (Exception $e) {

            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                // reset customer ID's on exception, because customer not saved
                $quote->getCustomer()->setId(null);
            }

            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            Mage::dispatchEvent('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
        Mage::dispatchEvent('sales_model_service_quote_submit_after', array('order'=>$order, 'quote'=>$quote));
        $this->_order = $order;
        return $order;
    }

    /**
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    public function submitMetaOrder()
    {
        $this->_deleteNominalItems();
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('core/resource_transaction');
        if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        $transaction->addObject($quote);

        $quote->reserveOrderId();
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress(true));
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
        if (!$isVirtual) {
            $fields = array(
                'subtotal_incl_tax',
                'base_subtotal_incl_tax',
                'weight',
                'subtotal',
                'tax_amount',
                'discount_amount',
                'shipping_amount',
                'shipping_incl_tax',
                'shipping_tax_amount',
                'grand_total',
                'base_subtotal',
                'base_tax_amount',
                'base_discount_amount',
                'base_shipping_amount',
                'base_shipping_incl_tax',
                'base_shipping_tax_amount',
                'base_grand_total',
                'hidden_tax_amount',
                'base_hidden_tax_amount',
                'shipping_hidden_tax_amount',
                'base_shipping_hidden_tax_amount',
                'base_shipping_hidden_tax_amnt',
                'shipping_discount_amount',
                'base_shipping_discount_amount',
                'subtotal_incl_tax',
                'base_subtotal_incl_tax'
            );
            foreach ($fields as $field) {
                $this->_orderData[$field] = 0;
            }

            $shippingAddresses = $quote->getAllShippingAddresses();
            $orderAddresses = array();
            foreach($shippingAddresses as $address) {
                foreach ($fields as $field) {
                    $this->_orderData[$field] += (float)$address->getData($field);
                }
                $orderAddresses[] = ($orderAddress = $this->_convertor->addressToOrderAddress($address));
                if ($address->getCustomerAddress()) {
                    $orderAddress->setCustomerAddress($address->getCustomerAddress());
                }
            }
            $order->setShippingAddresses($orderAddresses);
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($this->_orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            if ($item->getQty() == 0) {
                continue;
            }
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $order->setQuote($quote);

        $order->setStatus('metaorder')->setState('metaorder');

        $transaction->addObject($order);
        //$transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        /**
         * We can use configuration data for declare new order status
         */
        //Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        //Mage::dispatchEvent('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        try {
            $transaction->save();
            $this->_inactivateQuote();
            //Mage::dispatchEvent('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
        } catch (Exception $e) {

            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                // reset customer ID's on exception, because customer not saved
                $quote->getCustomer()->setId(null);
            }

            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            //Mage::dispatchEvent('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
        //Mage::dispatchEvent('sales_model_service_quote_submit_after', array('order'=>$order, 'quote'=>$quote));
        $this->_order = $order;
        return $order;
    }
}