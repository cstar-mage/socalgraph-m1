<?php


/**
 * Order approval item resource
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_item_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval_item', 'entity_id');
    }
}
