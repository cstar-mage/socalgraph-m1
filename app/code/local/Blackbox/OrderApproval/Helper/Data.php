<?php

class Blackbox_OrderApproval_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function canApprove($order)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->getCanApprove($order, Mage::getSingleton('customer/session')->getCustomer());
    }

    public function needApprove($order)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->validateOrder($order);
    }

    /**
     * Whether the order has not approved items that current customer can approve by specified rule
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Order_Item $order
     * @param Blackbox_OrderApproval_Model_Rule $rule
     */
    public function canApproveByRule($order, $rule)
    {
        if (!$rule->getConditions()->validate(Mage::getSingleton('customer/session')->getCustomer())) {
            return false;
        }

        foreach ($order->getAllItems() as $item) {
            if ($rule->getActions()->validate($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    public function getItemApproveRule($item)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($item->getOrder()->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->getItemApproveRule($item, Mage::getSingleton('customer/session')->getCustomer());
    }

    public function isOrderApproved($order)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->isApproved($order);
    }

    public function canApproveItem($item, $rule)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($item->getOrder()->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->canApproveItemByRule($item, $rule, Mage::getSingleton('customer/session')->getCustomer());
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @param Blackbox_OrderApproval_Model_Rule|int $rule
     * @return bool
     */
    public function canApproveItemByRule($item, $rule, $checkCustomer = true)
    {
        if (!is_object($rule)) {
            $rule = Mage::getModel('order_approval/rule')->load($rule);
        }

        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($item->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->canApproveItemByRule($item, $rule, $checkCustomer ? Mage::getSingleton('customer/session')->getCustomer() : null);
    }

    public function getAvailableRules($order)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->getAvailableRules($order, Mage::getSingleton('customer/session')->getCustomer());
    }

    public function hasItemsCanInvoice($order)
    {
        $validator = Mage::getSingleton('order_approval/validator'); /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
        return $validator->init($websiteId)->hasItemsCanInvoice($order, Mage::getSingleton('customer/session')->getCustomer());
    }

    /**
     * Check allow to send new approve email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewApprovalEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Blackbox_OrderApproval_Model_Approval::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send approval comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendApprovalCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Blackbox_OrderApproval_Model_Approval::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new disapproval email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewDisapprovalEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Blackbox_OrderApproval_Model_Disapproval::XML_PATH_EMAIL_ENABLED, $store);
    }

    public function getQuoteAddressesWithQtyApproval()
    {
        /* @var Blackbox_OrderApproval_Model_Validator $validator*/
        $validator = Mage::getSingleton('order_approval/validator');
        $validator->init(Mage::app()->getWebsite()->getId());

        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');

        $result = [];

        /** @var Mage_Sales_Model_Quote_Address $address */
        foreach ($cart->getQuote()->getAllShippingAddresses() as $address) {
            $found = false;
            /** @var Mage_Sales_Model_Quote_Item $item */
            foreach ($address->getAllVisibleItems() as $item) {
                $items = [];
                if ($validator->needQtyApprove($item)) {
                    if ($item instanceof Mage_Sales_Model_Quote_Item) {
                        $items[] = $item->getId();
                    } else {
                        $items[] = $item->getQuoteItemId();
                    }
                    $found = true;
                }
            }
            if ($found) {
                $result[$address->getId()] = [
                    'need_qty_approve' => true,
                    'items' => $items
                ];
            }
        }
        return $result;
    }
}