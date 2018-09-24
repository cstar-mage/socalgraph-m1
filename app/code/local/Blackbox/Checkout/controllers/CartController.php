<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'CartController.php');

class Blackbox_Checkout_CartController extends Mage_Checkout_CartController
{
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            if ($params['order-advance'] && isset($params['shipping'])) {
                $addresses = $this->_saveShippingAddresses($params['shipping'], true);
                foreach($addresses as $address) {
                    $address->save()->setDataChanges(true);
                }

                $originalQtys = [];
                foreach ($this->_getQuote()->getAllItems() as $item) {
                    $originalQtys[$item->getId()] = $item->getQty();
                }
            } else if (!$cart->getQuote()->hasItems()) {
                $firstAdd = true;
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);

            $addedItems = Mage::getSingleton('blackbox_checkout/observer')->getQuoteItems();

            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            if (isset($addresses) && !empty($addresses) && isset($addedItems)) {
                $cart->getQuote()->save(); // need save items to correct adding address items
                $cart->getQuote()->unsetItemsCollection(); // need reload items collection to correct getting quote item from address item in collect totals

                foreach($addedItems as $item) {
                    $qty = isset($originalQtys[$item->getId()]) ? $item->getQty() - $originalQtys[$item->getId()] : $item->getQty();
//                    if (count($addresses) > 1) {
//                        $item->setQty($item->getQty() + $qty * (count($addresses) - 1))->save();
//                    }
                    foreach ($addresses as $address) {
                        if ($aItem = $address->getItemByQuoteItemId($item->getId())) {
                            $aItem->setQty($aItem->getQty() + $qty);
                        } else {
                            $address->addItem($item, $qty);
                        }
                    }
                }
                foreach ($addresses as $address) {
                    $address->setSameAsBilling(0);
                }
                $this->_updateQuoteItemsQty();
            } else if (isset($firstAdd)) {
                $cart->getQuote()->save();
                $cart->getQuote()->unsetItemsCollection();

                $addresses = $cart->getQuote()->getAllShippingAddresses();
                foreach($addedItems as $item) {
                    $item->save();
                    foreach ($addresses as $address) {
                        $address->addItem($item, $item->getQty());
                    }
                }
                $this->_updateQuoteItemsQty();
            } else if (count($cart->getQuote()->getAllShippingAddresses()) > 1) {
                $cart->getQuote()->save();
                $cart->getQuote()->unsetItemsCollection();

                $address = $cart->getQuote()->getShippingAddress(false);
                foreach ($addedItems as $item) {
                    $address->addItem($item, $item->getQty());
                }
                $address->setDataChanges(true);
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    public function catalogProductAddressAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->loadLayout();

        $html = $this->getLayout()->getBlock('product.info.shipping.form')->setShippingId($id)->toHtml();

        $this->getResponse()->setBody($html);

    }

    public function searchAddressGridAction()
    {
        $html = $this->getLayout()->createBlock('blackbox_checkout/address_grid')->setMultiple(true)->setUrl('checkout/cart/searchAddressGrid')->toHtml();

        $this->getResponse()->setBody($html);
    }

    public function addAllAction()
    {
        $result = [];
        try {
            $storelocatorId = $this->getRequest()->getPost('storelocator_id');
            if (!$storelocatorId) {
                Mage::throwException(Mage::helper('checkout')->__('Missing parameter storelocator'));
            }
            $storelocator = Mage::getModel('storelocator/storelocator')->load($storelocatorId);
            if (!$storelocator->getId()) {
                Mage::throwException(Mage::helper('checkout')->__('Wrong store specified'));
            }

            $address = $this->_saveShippingAddresses(array($storelocator->getId()))[0];
            $address->save()->setSameAsBilling(0);
            $this->_getCart()->save();

            $result['success'] = true;
        } catch (Mage_Core_Exception $e) {
            $result['success'] = false;
            $result['message'] = Mage::helper('checkout')->__($e->getMessage());
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = Mage::helper('checkout')->__('An error has occured');
        }
        $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(json_encode($result));
    }

    public function removeStorelocatorAction()
    {
        $result = [];
        try {
            $storelocatorId = $this->getRequest()->getPost('storelocator_id');
            if (!$storelocatorId) {
                Mage::throwException(Mage::helper('checkout')->__('Missing parameter storelocator'));
            }
            $storelocator = Mage::getModel('storelocator/storelocator')->load($storelocatorId);
            if (!$storelocator->getId()) {
                Mage::throwException(Mage::helper('checkout')->__('Wrong store specified'));
            }

            foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                if ($address->getStorelocatorId() == $storelocator->getId()) {
                    $address->isDeleted(true);
                }
            }

            $this->_updateQuoteItemsQty(true);
            $this->_getCart()->save();

            $result['success'] = true;
            $result['cart_html'] = $this->_getCartHtml();
            $result['formkey'] = Mage::getSingleton('core/session')->getFormKey();
        } catch (Mage_Core_Exception $e) {
            $result['success'] = false;
            $result['message'] = Mage::helper('checkout')->__($e->getMessage());
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = Mage::helper('checkout')->__('An error has occured');
        }
        $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(json_encode($result));
    }

    public function ajaxDeleteAddressItemAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $result = array();
        if ($id) {
            try {
                /** @var Mage_Sales_Model_Quote_Address $address */
                foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                    if ($address->getItemById($id)) {
                        $address->removeItem($id);
                    }
                }
                $this->_updateQuoteItemsQty(true);
                /** @var Mage_Sales_Model_Quote_Address $address */
                foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                    if (!$address->getItemQty()) {
                        $address->isDeleted(true);
                    }
                }
                $this->_getCart()->save();

                $result['qty'] = $this->_getCart()->getSummaryQty();

                $this->loadLayout();
                $result['content'] = $this->getLayout()->getBlock('minicart_content')->toHtml();

                $result['success'] = 1;
                $result['message'] = $this->__('Item was removed successfully.');
                Mage::dispatchEvent('ajax_cart_remove_item_success', array('id' => $id));
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not remove the item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');


            if (count($this->_getQuote()->getAllShippingAddresses()) <= 1) {
                $cartData = $cartData[0];
                $changeAddress = false;
                foreach ($cartData as $data) {
                    if ($data['address_id']) {
                        $changeAddress = true;
                        break;
                    }
                }
                if (!$changeAddress) {
                    $this->getRequest()->setParam('cart', $cartData);
                    return parent::_updateShoppingCart();
                }

                $firstAddress = $this->_getQuote()->getShippingAddress(false);
                /** @var Mage_Sales_Model_Quote_Item $item */
                foreach ($this->_getQuote()->getAllVisibleItems() as $item) {
                    $aItem = $firstAddress->getItemByQuoteItemId($item->getId());
                    if (!$aItem) {
                        $firstAddress->addItem($item);
                    }
                }
                $firstAddress->setDataChanges(true);
                if (!$firstAddress->getId()) {
                    $firstAddress->save()->setDataChanges(true);
                }
                $cartData = [
                    $firstAddress->getId() => $cartData
                ];
            }


            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                foreach ($cartData as $addressId => $addressData) {
                    foreach ($addressData as $index => $data) {
                        if (isset($data['qty'])) {
                            $addressData[$index]['qty'] = $filter->filter(trim($data['qty']));
                        }
                    }
                }

                $newCartData = []; // data after moving items to another addresses
                $newCartDataMoved = []; // moved data after moving items
                foreach ($cartData as $addressId => $addressData) {
                    foreach ($addressData as $index => $data) {
                        if (!$data['address_id']) {
                            $newCartData[$addressId][$index] = $data;
                            continue;
                        }
                        $newCartData[$addressId][$index]['qty'] = 0;
                        $newAddress = $this->_saveShippingAddresses([$data['address_id']], true)[0];
                        if (!$newAddress->getId()) {
                            $newAddress->save()->setDataChanges(true);
                        }
                        $newCartDataMoved[$newAddress->getId()][$index] = $data;
                    }
                }

                foreach ($newCartDataMoved as $addressId => $addressData) {
                    foreach ($addressData as $index => $data) {
                        if (isset($newCartData[$addressId][$index])) {
                            $newCartData[$addressId][$index]['qty'] += $data['qty'];
                        } else {
                            $newCartData[$addressId][$index] = $data;
                        }
                    }
                }

                foreach ($newCartData as $addressId => $addressData) {
                    $address = $cart->getQuote()->getAddressById($addressId);
                    if (!$address) {
                        Mage::throwException('Wrong address.');
                    }
                    foreach ($addressData as $index => $data) {
                        if (isset($data['qty']) && $data['qty'] == '0') {
                            $aItem = $address->getItemByQuoteItemId($index);
                            if ($aItem) {
                                $aItem->isDeleted(true);
                            }
                            continue;
                        }
                        $aItem = $address->getItemByQuoteItemId($index);
                        if ($aItem) {
                            $aItem->setQty($data['qty']);
                        } else {
                            $address->addItem($cart->getQuote()->getItemById($index), $data['qty']);
                        }
                    }
                }

                $this->_updateQuoteItemsQty(true);

                /** @var Mage_Sales_Model_Quote_Address $address */
                foreach ($cart->getQuote()->getAllShippingAddresses() as $address) {
                    if (!$address->getItemQty()) {
                        $address->isDeleted(true);
                    }
                }

                $cart->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    /**
     * @param $arr
     * @return Mage_Sales_Model_Quote_Address[]
     */
    protected function _saveShippingAddresses($addressIds, $checkExisting = false)
    {
        $addresses = [];
        $added = false;
        foreach ($addressIds as $addressId) {
            if ($checkExisting) {
                $found = false;
                foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                    if ($addressId < 0) {
                        if ($address->getCustomerAddressId() == -$addressId) {
                            $found = $address;
                            break;
                        }
                    } else {
                        if ($address->getStorelocatorId() == $addressId) {
                            $found = $address;
                            break;
                        }
                    }
                }
                if ($found) {
                    $addresses[] = $found;
                    continue;
                }
            }
            $address = Mage::getModel('sales/quote_address');

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm    = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntityType('customer_address')
                ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

            if ($addressId < 0) {
                $customerAddress = Mage::getModel('customer/address')->load(-$addressId);
                if (!$customerAddress->getId() || $customerAddress->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                    Mage::throwException('Customer Address is not valid.');
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    Mage::throwException(implode(' ', $addressErrors));
                }
            } else {
                $storelocator = Mage::getModel('storelocator/storelocator')->load($addressId);
                if (!$storelocator->getId()) {
                    Mage::throwException('Wrong address');
                }
                $data = array(
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
                    'telephone' => $storelocator->getPhone(),
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
                $address->setStorelocatorId($addressId);
                $address->setStorelocatorName($storelocator->getName());
            }

            $address->implodeStreetAddress();
            $address->setCollectShippingRates(true);

            if ($address->isObjectNew()) {
                if (!$added && count($this->_getQuote()->getAllShippingAddresses()) <= 1) {
                    $firstAddress = $this->_getQuote()->getShippingAddress(false);
                    /** @var Mage_Sales_Model_Quote_Item $item */
                    foreach ($this->_getQuote()->getAllVisibleItems() as $item) {
                        $aItem = $firstAddress->getItemByQuoteItemId($item->getId());
                        if ($aItem) {
                            $aItem->setQty($item->getQty());
                        } else {
                            $firstAddress->addItem($item);
                        }
                    }
                    $firstAddress->setDataChanges(true);
                }
                $this->_getQuote()->addShippingAddress($address, true);
                $added = true;
            }

            $addresses[] = $address;
        }
        
        return $addresses;
    }

    protected function _updateQuoteItemsQty($updateQuoteItemFromAddressItemIfSingleAddress = false)
    {
        $items = [];

        if (count($this->_getQuote()->getAllShippingAddresses()) <= 1) {
            foreach ($this->_getQuote()->getShippingAddress(false)->getAllItems(true) as $aItem) {
                if ($updateQuoteItemFromAddressItemIfSingleAddress) {
                    $items[$aItem->getQuoteItemId()] += (int)$aItem->getQty();
                }
                $aItem->isDeleted(true);
                $aItem->getAddress()->clearCachedItems();
            }

            if ($updateQuoteItemFromAddressItemIfSingleAddress) {
                /** @var Mage_Sales_Model_Quote_Item $item */
                foreach ($this->_getQuote()->getAllVisibleItems() as $item) {
                    if ($items[$item->getId()]) {
                        $item->setQty($items[$item->getId()]);
                    } else {
                        $item->isDeleted(true);
                    }
                }
            }

            return $this;
        }

        /** @var Blackbox_Checkout_Model_Sales_Quote_Address $address */
        foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
            /** @var Mage_Sales_Model_Quote_Address_Item $item */
            foreach ($address->getAllItems(true) as $item)
            {
                $items[$item->getQuoteItemId()] += (int)$item->getQty();
            }
        }
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($this->_getQuote()->getAllVisibleItems() as $item) {
            if ($items[$item->getId()]) {
                $item->setQty($items[$item->getId()]);
            } else {
                $item->isDeleted(true);
            }
        }

        return $this;
    }

    protected function _getCartHtml()
    {
        return $this->loadLayout('default')->getLayout()->getBlock('minicart_head')->toHtml();
    }
}