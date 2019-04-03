<?php
/**
 * Adminhtml sales estimates controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Adminhtml_Epace_EstimateController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('epacei/estimate')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Estimates'), $this->__('Estimates'));
        return $this;
    }

    /**
     * Initialize estimate model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate || false
     */
    protected function _initEstimate()
    {
        $id = $this->getRequest()->getParam('estimate_id');
        $estimate = Mage::getModel('epacei/estimate')->load($id);

        if (!$estimate->getId()) {
            $this->_getSession()->addError($this->__('This estimate no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('epacei_estimate', $estimate);
        Mage::register('current_estimate', $estimate);
        return $estimate;
    }

    /**
     * Estimates grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Estimates'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Estimate grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View estimate detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Estimates'));

        $estimate = $this->_initEstimate();
        if ($estimate) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $estimate->getRealEstimateId()));

            $this->renderLayout();
        }
    }

    /**
     * Add estimate comment action
     */
    public function addCommentAction()
    {
        if ($estimate = $this->_initEstimate()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $estimate->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $estimate->save();
                $estimate->sendEstimateUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add estimate history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate estimate history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initEstimate();
        $html = $this->getLayout()->createBlock('epacei/adminhtml_estimate_view_tab_history')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Print all documents for selected estimates
     */
    public function pdfdocsAction(){
        $estimateIds = $this->getRequest()->getPost('estimate_ids');
        $flag = false;
        if (!empty($estimateIds)) {
            foreach ($estimateIds as $estimateId) {
                $invoices = Mage::getResourceModel('epacei/estimate_invoice_collection')
                    ->setEstimateFilter($estimateId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/estimate_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('epacei/estimate_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('epacei/estimate_shipment_collection')
                    ->setEstimateFilter($estimateId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/estimate_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('epacei/estimate_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('epacei/estimate_creditmemo_collection')
                    ->setEstimateFilter($estimateId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/estimate_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('epacei/estimate_pdf_creditmemo')->getPdf($creditmemos);
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
                $this->_getSession()->addError($this->__('There are no printable documents related to selected estimates.'));
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
            case 'cancel':
                $aclResource = 'epacei/estimate/actions/cancel';
                break;
            case 'view':
                $aclResource = 'epacei/estimate/actions/view';
                break;
            case 'addcomment':
                $aclResource = 'epacei/estimate/actions/comment';
                break;
            default:
                $aclResource = 'epacei/estimate';
                break;

        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export estimate grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'estimates.csv';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_estimate_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export estimate grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'estimates.xml';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_estimate_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function versionsGridAction()
    {
        $estimate = $this->_initEstimate();
        if ($estimate) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
}
