<?php

if (Mage::helper('core')->isModuleEnabled('Blackbox_Checkout')) {
    class Blackbox_OrderApproval_Model_Sales_OrderDynamic extends Blackbox_Checkout_Model_Sales_Order {}
} else {
    class Blackbox_OrderApproval_Model_Sales_OrderDynamic extends Mage_Sales_Model_Order {}
}

class Blackbox_OrderApproval_Model_Sales_Order extends Blackbox_OrderApproval_Model_Sales_OrderDynamic
{
    const STATUS_WAITING_CUSTOMER = 'waiting_customer';
    const STATUS_WAITING_APPROVAL = 'pending';

    protected $_approvals;
    protected $_disapprovals;
    /**
     * Retrieve order approvals collection
     *
     * @return Blackbox_OrderApproval_Model_Resource_Approval_Collection
     */
    public function getApprovalCollection()
    {
        if (is_null($this->_approvals)) {
            $this->_approvals = Mage::getResourceModel('order_approval/approval_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_approvals as $approval) {
                    $approval->setOrder($this);
                }
            }
        }
        return $this->_approvals;
    }

    /**
     * Check order approvals availability
     *
     * @return bool
     */
    public function hasApprovals()
    {
        return $this->getApprovalCollection()->count();
    }

    /**
     * Create new approval with maximum qty for approval for each item
     *
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @param Mage_Customer_Model_Customer $user
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function prepareApproval($qtys = array(), $rule, $user)
    {
        $approval = Mage::getModel('order_approval/service_order', $this)->prepareApproval($qtys, $rule, $user);
        return $approval;
    }

    public function canApprove()
    {
        return $this->getStatus() != self::STATUS_WAITING_CUSTOMER && Mage::helper('order_approval')->canApprove($this);
    }

    /**
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @return mixed
     */
    public function canApproveByRule($rule)
    {
        return $this->getStatus() != self::STATUS_WAITING_CUSTOMER && Mage::helper('order_approval')->canApproveByRule($this, $rule);
    }

    /**
     * Retrieve order disapprovals collection
     *
     * @return Blackbox_OrderApproval_Model_Resource_Disapproval_Collection
     */
    public function getDisapprovalCollection()
    {
        if (is_null($this->_disapprovals)) {
            $this->_disapprovals = Mage::getResourceModel('order_approval/disapproval_collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_disapprovals as $disapproval) {
                    $disapproval->setOrder($this);
                }
            }
        }
        return $this->_disapprovals;
    }

    /**
     * Check order disapprovals availability
     *
     * @return bool
     */
    public function hasDisapprovals()
    {
        return $this->getDisapprovalCollection()->count();
    }

    /**
     * Create new disapproval
     *
     * @param Blackbox_OrderApproval_Model_Rule $rule
     * @param Mage_Customer_Model_Customer $user
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function prepareDisapproval($rule, $user)
    {
        $disapproval = Mage::getModel('order_approval/service_order', $this)->prepareDisapproval($rule, $user);
        return $disapproval;
    }

    public function canDisapprove()
    {
        return Mage::helper('order_approval')->canApprove($this);
    }

    public function reset()
    {
        $this->_approvals = null;
        $this->_disapprovals = null;
        return parent::reset();
    }

    public function registerCancellation($comment = '', $graceful = true) {
        $canCancel = $this->canCancel();
        $paymentReview = $this->isPaymentReview();

        parent::registerCancellation($comment, $graceful);

        if ($canCancel || $paymentReview) {
            foreach ($this->getDisapprovalCollection() as $disapproval) {
                if ($disapproval->canCancel()) {
                    $disapproval->cancel();
                    $this->addRelatedObject($disapproval);
                }
            }
        }
        return $this;
    }

    public function cancel($comment = '')
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation($comment);

            Mage::dispatchEvent('order_cancel_after', array('order' => $this));
        }

        return $this;
    }

    public function getEditHistory()
    {
        if ($id = $this->getEditCommentId()) {
            return Mage::getModel('sales/order_status_history')->load($id);
        }

        return null;
    }
}