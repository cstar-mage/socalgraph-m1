<?php

class Blackbox_Checkout_Helper_Preorder extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEBUG = 'checkout/preorder/debug';
    const XML_PATH_EVENT_DAY_DAYS = 'checkout/preorder/event_date_days';
    const XML_PATH_SHIPPING_DAY_DAYS = 'checkout/preorder/shipping_date_days';

    const STATUS_PREORDER = 'preorder';
    const STATE_PREORDER = 'new';

    public function isDebug()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEBUG);
    }

    public function getEventDayDays()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_DAY_DAYS);
    }

    public function getShippingDayDays()
    {
        return Mage::getStoreConfig(self::XML_PATH_SHIPPING_DAY_DAYS);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function timeHasCome($order)
    {
        return strtotime($order->getEventDate()) - $this->getEventDayDays() * 86400 <= time() || strtotime($order->getShippingDate()) - $this->getShippingDayDays() * 86400 <= time();
    }
}