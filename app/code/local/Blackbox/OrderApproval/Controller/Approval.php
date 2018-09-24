<?php

/**
 * Adminhtml Order Approval controller
 */
class Blackbox_OrderApproval_Controller_Approval extends Blackbox_OrderApproval_Controller_Abstract
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
     * @return Blackbox_OrderApproval_Controller_Approval
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Approvals'),$this->__('Approvals'));
        return $this;
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('order_approval/adminhtml_approval_grid')->toHtml()
        );
    }

    /**
     * Approvals grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Approvals'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('order_approval/adminhtml_approval'))
            ->renderLayout();
    }

    /**
     * Approval information page
     */
    public function viewAction()
    {
        if ($approvalId = $this->getRequest()->getParam('approval_id')) {
            $this->_forward('view', 'adminhtml_order_approval', null, array('come_from'=>'approval'));
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($approvalId = $this->getRequest()->getParam('approval_id')) {
            if ($approval = Mage::getModel('order_approval/approval')->load($approvalId)) {
                $approval->sendEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($approval, Blackbox_OrderApproval_Model_Approval::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess(Mage::helper('order_approval')->__('The message has been sent.'));
                $this->_redirect('*/adminhtml_approval/view', array(
                    'order_id'  => $approval->getOrder()->getId(),
                    'approval_id'=> $approvalId,
                ));
            }
        }
    }

    public function printAction()
    {
        if ($approvalId = $this->getRequest()->getParam('approval_id')) {
            if ($approval = Mage::getModel('order_approval/approval')->load($approvalId)) {
                $pdf = Mage::getModel('order_approval/order_pdf_approval')->getPdf(array($approval));
                $this->_prepareDownloadResponse('approval'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function pdfapprovalsAction(){
        $approvalsIds = $this->getRequest()->getPost('approval_ids');
        if (!empty($approvalsIds)) {
            $approvals = Mage::getResourceModel('order_approval/approval_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $approvalsIds))
                ->load();
            if (!isset($pdf)){
                $pdf = Mage::getModel('order_approval/order_pdf_approval')->getPdf($approvals);
            } else {
                $pages = Mage::getModel('order_approval/order_pdf_approval')->getPdf($approvals);
                $pdf->pages = array_merge ($pdf->pages, $pages->pages);
            }

            return $this->_prepareDownloadResponse('approval'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                '.pdf', $pdf->render(), 'application/pdf');
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/approval');
    }

}
