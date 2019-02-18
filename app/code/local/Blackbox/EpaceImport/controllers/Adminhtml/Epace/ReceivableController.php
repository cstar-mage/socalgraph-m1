<?php
/**
 * Adminhtml sales receivables controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Adminhtml_Epace_ReceivableController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('view', 'index');

    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Blackbox_EpaceImport');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('epacei/receivable')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Receivables'), $this->__('Receivables'));
        return $this;
    }

    /**
     * Initialize receivable model instance
     *
     * @return Blackbox_EpaceImport_Model_Receivable || false
     */
    protected function _initReceivable()
    {
        $id = $this->getRequest()->getParam('receivable_id');
        $receivable = Mage::getModel('epacei/receivable')->load($id);

        if (!$receivable->getId()) {
            $this->_getSession()->addError($this->__('This receivable no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('epacei_receivable', $receivable);
        Mage::register('current_receivable', $receivable);
        return $receivable;
    }

    /**
     * Receivables grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Receivables'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Receivable grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View receivable detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Receivables'));

        $receivable = $this->_initReceivable();
        if ($receivable) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $receivable->getRealReceivableId()));

            $this->renderLayout();
        }
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initReceivable();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epacei/adminhtml_receivable_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initReceivable();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epacei/adminhtml_receivable_view_tab_shipments')->toHtml()
        );
    }

    /**
     * Generate receivable history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initReceivable();
        $html = $this->getLayout()->createBlock('epacei/adminhtml_receivable_view_tab_history')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Print all documents for selected receivables
     */
    public function pdfdocsAction(){
        $receivableIds = $this->getRequest()->getPost('receivable_ids');
        $flag = false;
        if (!empty($receivableIds)) {
            foreach ($receivableIds as $receivableId) {
                $invoices = Mage::getResourceModel('epacei/receivable_invoice_collection')
                    ->setReceivableFilter($receivableId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/receivable_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('epacei/receivable_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('epacei/receivable_shipment_collection')
                    ->setReceivableFilter($receivableId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/receivable_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('epacei/receivable_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('epacei/receivable_creditmemo_collection')
                    ->setReceivableFilter($receivableId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/receivable_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('epacei/receivable_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'docs'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf',
                    $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected receivables.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'view':
                $aclResource = 'epacei/receivable/actions/view';
                break;
            default:
                $aclResource = 'epacei/receivable';
                break;

        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export receivable grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'receivables.csv';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_receivable_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export receivable grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'receivables.xml';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_receivable_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
