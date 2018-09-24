<?php

/**
 * Order approval item collection
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Resource_Approval_Item_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'order_approval_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_approval_item_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval_item');
    }

    /**
     * Set approval filter
     *
     * @param int $approvalId
     * @return Blackbox_OrderApproval_Model_Resource_Approval_Item_Collection
     */
    public function setApprovalFilter($approvalId)
    {
        $this->addFieldToFilter('parent_id', $approvalId);
        return $this;
    }
}
