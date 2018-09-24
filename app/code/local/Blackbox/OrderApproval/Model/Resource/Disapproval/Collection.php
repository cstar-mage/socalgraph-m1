<?php

/**
 * Flat sales order disapproval collection
 *
 * @category    Mage
 * @package     Mage_Sales
 */
class Blackbox_OrderApproval_Model_Resource_Disapproval_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_disapproval_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_disapproval_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    protected $_orderJoined = false;

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/disapproval');
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     *
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
    }

    public function addStatusFilter($status)
    {
        return $this->addFieldToFilter('state', $status);
    }

    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', $storeId);
    }
}
