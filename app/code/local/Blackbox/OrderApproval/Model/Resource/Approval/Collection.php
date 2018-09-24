<?php

/**
 * Flat sales order approval collection
 *
 * @category    Mage
 * @package     Mage_Sales
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_approval_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval');
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     *
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
    }
}
