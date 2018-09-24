<?php

/**
 * Adminhtml sales disapprovals controller
 */
class Blackbox_OrderApproval_Adminhtml_DisapprovalController extends Blackbox_OrderApproval_Controller_Disapproval
{
    /**
     * Export disapproval grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'disapprovals.csv';
        $grid       = $this->getLayout()->createBlock('order_approval/adminhtml_disapproval_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export disapproval grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'disapprovals.xml';
        $grid       = $this->getLayout()->createBlock('order_approval/adminhtml_disapproval_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
