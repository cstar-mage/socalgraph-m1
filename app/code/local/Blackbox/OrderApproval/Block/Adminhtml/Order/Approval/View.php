<?php

/**
 * Adminhtml approval view
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Approval_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Admin session
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    public function __construct()
    {
        $this->_objectId    = 'approval_id';
        $this->_controller  = 'adminhtml_order_approval';
        $this->_mode        = 'view';
        $this->_session = Mage::getSingleton('admin/session');

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->_isAllowedAction('cancel') && $this->getApproval()->canCancel()) {
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->_isAllowedAction('emails')) {
            $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
                Mage::helper('sales')->__('Are you sure you want to send Approval email to customer?')
            );
            $this->addButton('send_notification', array(
                'label'     => Mage::helper('sales')->__('Send Email'),
                'onclick'   => 'confirmSetLocation(\'' . $confirmationMessage . '\', \'' . $this->getEmailUrl() . '\')'
            ));
        }

        if ($this->getApproval()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
        }
    }

    /**
     * Retrieve approval model instance
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function getApproval()
    {
        return Mage::registry('current_approval');
    }

    public function getHeaderText()
    {
        if ($this->getApproval()->getEmailSent()) {
            $emailSent = Mage::helper('order_approval')->__('the approval email was sent');
        }
        else {
            $emailSent = Mage::helper('order_approval')->__('the approval email is not sent');
        }
        return Mage::helper('order_approval')->__('Approval #%1$s | %2$s | %4$s (%3$s)', $this->getApproval()->getIncrementId(), $this->getApproval()->getStateName(), $emailSent, $this->formatDate($this->getApproval()->getCreatedAtDate(), 'medium', true));
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            'adminhtml/sales_order/view',
            array(
                'order_id'  => $this->getApproval()->getOrderId(),
                'active_tab'=> 'order_approvals'
            ));
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('approval_id'=>$this->getApproval()->getId()));
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email', array(
            'order_id'  => $this->getApproval()->getOrder()->getId(),
            'approval_id'=> $this->getApproval()->getId(),
        ));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'approval_id' => $this->getApproval()->getId()
        ));
    }

    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getApproval()->getBackUrl()) {
                return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getApproval()->getBackUrl()
                    . '\')');
            }
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/adminhtml_approval/') . '\')');
        }
        return $this;
    }

    /**
     * Check whether is allowed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return $this->_session->isAllowed('sales/order/actions/' . $action);
    }
}
