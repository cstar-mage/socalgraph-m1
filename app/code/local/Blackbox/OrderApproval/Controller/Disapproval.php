<?php

/**
 * Adminhtml Order Disapproval controller
 */
class Blackbox_OrderApproval_Controller_Disapproval extends Blackbox_OrderApproval_Controller_Abstract
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Blackbox_OrderApproval');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Blackbox_OrderApproval_Controller_Disapproval
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Disapprovals'),$this->__('Disapprovals'));
        return $this;
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('order_approval/adminhtml_disapproval_grid')->toHtml()
        );
    }

    /**
     * Approvals grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Disapprovals'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('order_approval/adminhtml_disapproval'))
            ->renderLayout();
    }

    /**
     * Disapproval information page
     */
    public function viewAction()
    {
        if ($disapprovalId = $this->getRequest()->getParam('disapproval_id')) {
            $this->_forward('view', 'adminhtml_order_disapproval', null, array('come_from'=>'disapproval'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($disapprovalId = $this->getRequest()->getParam('disapproval_id')) {
            if ($disapproval = Mage::getModel('order_approval/disapproval')->load($disapprovalId)) {
                $disapproval->sendEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($disapproval, Blackbox_OrderApproval_Model_Disapproval::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess(Mage::helper('order_approval')->__('The message has been sent.'));
                $this->_redirect('*/adminhtml_disapproval/view', array(
                    'order_id'  => $disapproval->getOrder()->getId(),
                    'disapproval_id'=> $disapprovalId,
                ));
            }
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/disapproval');
    }

}
