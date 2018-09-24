<?php

/**
 * Adminhtml disapproval view
 *
 * @category   Blackbox
 * @package    Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Block_Adminhtml_Order_Disapproval_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Admin session
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    public function __construct()
    {
        $this->_objectId    = 'disapproval_id';
        $this->_controller  = 'adminhtml_order_disapproval';
        $this->_mode        = 'view';
        $this->_session = Mage::getSingleton('admin/session');

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if ($this->_isAllowedAction('cancel') && $this->getDisapproval()->canCancel()) {
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('sales')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->_isAllowedAction('emails')) {
            $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
                Mage::helper('sales')->__('Are you sure you want to send Disapproval email to customer?')
            );
            $this->addButton('send_notification', array(
                'label'     => Mage::helper('sales')->__('Send Email'),
                'onclick'   => 'confirmSetLocation(\'' . $confirmationMessage . '\', \'' . $this->getEmailUrl() . '\')'
            ));
        }
    }

    /**
     * Retrieve approval model instance
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function getDisapproval()
    {
        return Mage::registry('current_disapproval');
    }

    public function getHeaderText()
    {
        if ($this->getDisapproval()->getEmailSent()) {
            $emailSent = Mage::helper('order_approval')->__('the disapproval email was sent');
        }
        else {
            $emailSent = Mage::helper('order_approval')->__('the disapproval email is not sent');
        }
        return Mage::helper('order_approval')->__('Disapproval #%1$s | %2$s | %4$s (%3$s)', $this->getDisapproval()->getIncrementId(), $this->getDisapproval()->getStateName(), $emailSent, $this->formatDate($this->getDisapproval()->getCreatedAtDate(), 'medium', true));
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            'adminhtml/sales_order/view',
            array(
                'order_id'  => $this->getDisapproval()->getOrderId(),
                'active_tab'=> 'order_disapprovals'
            ));
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('disapproval_id'=>$this->getDisapproval()->getId()));
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email', array(
            'order_id'  => $this->getDisapproval()->getOrder()->getId(),
            'approval_id'=> $this->getDisapproval()->getId(),
        ));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'approval_id' => $this->getDisapproval()->getId()
        ));
    }

    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getDisapproval()->getBackUrl()) {
                return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getDisapproval()->getBackUrl()
                    . '\')');
            }
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/adminhtml_disapproval/') . '\')');
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
