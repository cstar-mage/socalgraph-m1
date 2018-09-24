<?php

/**
 * Order approval comment model
 *
 * @method Blackbox_OrderApproval_Model_Resource_Approval_Comment _getResource()
 * @method Blackbox_OrderApproval_Model_Resource_Approval_Comment getResource()
 * @method int getParentId()
 * @method Blackbox_OrderApproval_Model_Approval_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Blackbox_OrderApproval_Model_Approval_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Blackbox_OrderApproval_Model_Approval_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Blackbox_OrderApproval_Model_Approval_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_OrderApproval_Model_Approval_Comment setCreatedAt(string $value)
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApprova
 */
class Blackbox_OrderApproval_Model_Approval_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Approval instance
     *
     * @var Blackbox_OrderApproval_Model_Approval
     */
    protected $_approval;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval_comment');
    }

    /**
     * Declare approval instance
     *
     * @param   Blackbox_OrderApproval_Model_Approval $approval
     * @return  Blackbox_OrderApproval_Model_Approval_Comment
     */
    public function setApproval(Blackbox_OrderApproval_Model_Approval $approval)
    {
        $this->_approval = $approval;
        return $this;
    }

    /**
     * Retrieve approval instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return $this->_approval;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getApproval()) {
            return $this->getApproval()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return Blackbox_OrderApproval_Model_Approval_Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getApproval()) {
            $this->setParentId($this->getApproval()->getId());
        }

        return $this;
    }
}
