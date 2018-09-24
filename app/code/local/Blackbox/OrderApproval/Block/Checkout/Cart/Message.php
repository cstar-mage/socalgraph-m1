<?php

class Blackbox_OrderApproval_Block_Checkout_Cart_Message extends Mage_Core_Block_Abstract
{
    protected function _prepareLayout()
    {
        /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $validator = Mage::getSingleton('order_approval/validator');
        $validator->init(Mage::app()->getWebsite()->getId());

        $quote = $this->_getCart()->getQuote();

        $addedMessages = [];

        $found = false;
        /** @var Mage_Sales_Model_Quote_Address $address */
        foreach ($quote->getAllShippingAddresses() as $address) {
            /** @var Mage_Sales_Model_Quote_Item $item */
            foreach ($address->getAllVisibleItems() as $item) {
                if ($validator->needQtyApprove($item)) {
                    if (!($item instanceof Mage_Sales_Model_Quote_Item)) {
                        $item = $item->getQuoteItem();
                    }
                    if ($item && !$addedMessages[$item->getId()]) {
                        $item->addMessage('Need qty approve.');
                        $addedMessages[$item->getId()] = true;
                    }
                    $found = true;
                }
            }
        }
        if ($found) {
            Mage::getSingleton('checkout/session')->addNotice($this->__('Some of your orders need qty approve.'));
        }
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
}