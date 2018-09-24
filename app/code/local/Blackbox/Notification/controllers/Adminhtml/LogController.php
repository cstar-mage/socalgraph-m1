<?php

class Blackbox_Notification_Adminhtml_LogController extends Mage_Adminhtml_Controller_Action
{
    protected function _initLog()
    {
        $this->_title($this->__('Notifications'))->_title($this->__('Log'));

        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('notification_id')) {
            $id = (int)$this->getRequest()->getParam('notification_id');
        }

        if (!$id) {
            return false;
        }

        $log = Mage::getModel('blackbox_notification/log')->load($id);
        if (!$log->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('blackbox_notification')->__('This log no longer exists.'));
            return false;
        }

        Mage::register('current_blackbox_notification_log', $log);

        return $log;
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('notification/log')
            ->_addBreadcrumb(Mage::helper('blackbox_notification')->__('Notifications'), Mage::helper('blackbox_notification')->__('Notifications'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Notifications'))->_title($this->__('Log'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('blackbox_notification')->__('Notifications'), Mage::helper('blackbox_notification')->__('Notifications'))
            ->renderLayout();
    }

    public function editAction()
    {
        $this->_forward('view');
    }

    public function viewAction()
    {
        $this->_initLog();

        $this->_initAction();

        $this
            ->_addBreadcrumb(Mage::helper('blackbox_notification')->__('View Log Details'), Mage::helper('blackbox_notification')->__('View Log Details'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * Returns result of current user permission check on resource and privilege
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/notification/log');
    }
}
