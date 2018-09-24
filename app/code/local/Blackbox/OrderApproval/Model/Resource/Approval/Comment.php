<?php

/**
 * Order approval comment resource
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Comment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_comment_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval_comment', 'entity_id');
    }
}
