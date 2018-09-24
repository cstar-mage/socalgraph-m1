<?php

/**
 * Order Approval comment collection
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Comment_Collection
    extends Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_approval_comment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('order_approval/approval_comment');
    }

    /**
     * Set approval filter
     *
     * @param int $approvalId
     * @return Blackbox_OrderApproval_Model_Resource_Approval_Comment_Collection
     */
    public function setApprovalFilter($approvalId)
    {
        return $this->setParentFilter($approvalId);
    }
}
