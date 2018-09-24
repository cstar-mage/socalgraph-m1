<?php

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_OrderApproval_Adminhtml_ApprovalController extends Blackbox_OrderApproval_Controller_Approval
{
    /**
     * Export approval grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'approvals.csv';
        $grid       = $this->getLayout()->createBlock('order_approval/adminhtml_approval_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export approval grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'approvals.xml';
        $grid       = $this->getLayout()->createBlock('order_approval/adminhtml_approval_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
