<?php

/**
 * @method Blackbox_Notification_Model_Resource_Notification_Collection getItems()
 *
 * Class Blackbox_Notification_Block_Head_Notification
 */
class Blackbox_Notification_Block_Head_Notification extends Mage_Core_Block_Template
{
    /** @var  Blackbox_Notification_Block_Head_Notification_Renderer */
    protected $renderer;
    /** @var Blackbox_Notification_Model_Resource_Notification_Collection */
    protected $newItems;

    protected function _beforeToHtml()
    {
        $this->renderer = $this->getChild('renderer');

        /** @var Blackbox_Notification_Model_Resource_Notification_Collection $items */
        $items = Mage::getResourceModel('blackbox_notification/notification_collection');
        $items->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomerId())
            ->addOrder('notification_id', 'DESC')
            ->setPageSize(Mage::getStoreConfig('notification/settings/count'))
            ->setCurPage(1);

        $this->setItems($items);

        return parent::_beforeToHtml();
    }

    /**
     * @param Blackbox_Notification_Model_Notification $item
     * @return string
     */
    public function getItemHtml($item)
    {
        return $this->renderer->setItem($item)->toHtml();
    }

    public function getNewItems()
    {
        if (!$this->newItems) {
            $this->newItems = Mage::getResourceModel('blackbox_notification/notification_collection');
            $this->newItems->addFieldToFilter('viewed', 0)
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomerId());
        }
        return $this->newItems;
    }
}