<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
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
     * Initialize order model instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate || false
     */
    protected function _initEstimate()
    {
        $id = $this->getRequest()->getParam('estimate_id');
        $estimate = Mage::getModel('epacei/estimate')->load($id);

        if (!$estimate->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('epacei_estimate', $estimate);
        Mage::register('current_order', $estimate);
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
     * View order detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Estimates'));

        $estimate = $this->_initEstimate();
        if ($estimate) {

            $isActionsNotPermitted = $estimate->getActionFlag(
                Blackbox_EpaceImport_Model_Estimate::ACTION_FLAG_PRODUCTS_PERMISSION_DENIED
            );
            if ($isActionsNotPermitted) {
                $this->_getSession()->addError($this->__('You don\'t have permissions to manage this order because of one or more products are not permitted for your website.'));
            }

            $this->_initAction();

            $this->_title(sprintf("#%s", $estimate->getRealEstimateId()));

            $this->renderLayout();
        }
    }

    /**
     * Add order comment action
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
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initEstimate();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epacei/adminhtml_estimate_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initEstimate();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epacei/adminhtml_estimate_view_tab_shipments')->toHtml()
        );
    }

    /**
     * Generate creditmemos grid for ajax request
     */
    public function creditmemosAction()
    {
        $this->_initEstimate();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('epacei/adminhtml_estimate_view_tab_creditmemos')->toHtml()
        );
    }

    /**
     * Generate order history for ajax request
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
     * Cancel selected orders
     */
    public function massCancelAction()
    {
        $estimateIds = $this->getRequest()->getPost('estimate_ids', array());
        $countCancelEstimate = 0;
        $countNonCancelEstimate = 0;
        foreach ($estimateIds as $estimateId) {
            $estimate = Mage::getModel('epacei/estimate')->load($estimateId);
            if ($estimate->canCancel()) {
                $estimate->cancel()
                    ->save();
                $countCancelEstimate++;
            } else {
                $countNonCancelEstimate++;
            }
        }
        if ($countNonCancelEstimate) {
            if ($countCancelEstimate) {
                $this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelEstimate));
            } else {
                $this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelEstimate) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelEstimate));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction()
    {
        $estimateIds = $this->getRequest()->getPost('estimate_ids', array());
        $countHoldEstimate = 0;

        foreach ($estimateIds as $estimateId) {
            $estimate = Mage::getModel('epacei/estimate')->load($estimateId);
            if ($estimate->canHold()) {
                $estimate->hold()
                    ->save();
                $countHoldEstimate++;
            }
        }

        $countNonHoldEstimate = count($estimateIds) - $countHoldEstimate;

        if ($countNonHoldEstimate) {
            if ($countHoldEstimate) {
                $this->_getSession()->addError($this->__('%s order(s) were not put on hold.', $countNonHoldEstimate));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were put on hold.'));
            }
        }
        if ($countHoldEstimate) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been put on hold.', $countHoldEstimate));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $estimateIds = $this->getRequest()->getPost('estimate_ids', array());
        $countUnholdEstimate = 0;
        $countNonUnholdEstimate = 0;

        foreach ($estimateIds as $estimateId) {
            $estimate = Mage::getModel('epacei/estimate')->load($estimateId);
            if ($estimate->canUnhold()) {
                $estimate->unhold()
                    ->save();
                $countUnholdEstimate++;
            } else {
                $countNonUnholdEstimate++;
            }
        }
        if ($countNonUnholdEstimate) {
            if ($countUnholdEstimate) {
                $this->_getSession()->addError($this->__('%s order(s) were not released from holding status.', $countNonUnholdEstimate));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were released from holding status.'));
            }
        }
        if ($countUnholdEstimate) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdEstimate));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Change status for selected orders
     */
    public function massStatusAction()
    {

    }

    /**
     * Print documents for selected orders
     */
    public function massPrintAction()
    {
        $estimateIds = $this->getRequest()->getPost('estimate_ids');
        $document = $this->getRequest()->getPost('document');
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction(){
        $estimateIds = $this->getRequest()->getPost('estimate_ids');
        $flag = false;
        if (!empty($estimateIds)) {
            foreach ($estimateIds as $estimateId) {
                $invoices = Mage::getResourceModel('epacei/estimate_invoice_collection')
                    ->setEstimateFilter($estimateId)
                    ->load();
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/estimate_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('epacei/estimate_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction(){
        $estimateIds = $this->getRequest()->getPost('estimate_ids');
        $flag = false;
        if (!empty($estimateIds)) {
            foreach ($estimateIds as $estimateId) {
                $shipments = Mage::getResourceModel('epacei/estimate_shipment_collection')
                    ->setEstimateFilter($estimateId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/estimate_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('epacei/estimate_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print creditmemos for selected orders
     */
    public function pdfcreditmemosAction(){
        $estimateIds = $this->getRequest()->getPost('estimate_ids');
        $flag = false;
        if (!empty($estimateIds)) {
            foreach ($estimateIds as $estimateId) {
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
                    'creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print all documents for selected orders
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
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Atempt to void the order payment
     */
    public function voidPaymentAction()
    {
        if (!$estimate = $this->_initEstimate()) {
            return;
        }
        try {
            $estimate->getPayment()->void(
                new Varien_Object() // workaround for backwards compatibility
            );
            $estimate->save();
            $this->_getSession()->addSuccess($this->__('The payment has been voided.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to void the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/view', array('estimate_id' => $estimate->getId()));
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
            case 'hold':
                $aclResource = 'epacei/estimate/actions/hold';
                break;
            case 'unhold':
                $aclResource = 'epacei/estimate/actions/unhold';
                break;
            case 'email':
                $aclResource = 'epacei/estimate/actions/email';
                break;
            case 'cancel':
                $aclResource = 'epacei/estimate/actions/cancel';
                break;
            case 'view':
                $aclResource = 'epacei/estimate/actions/view';
                break;
            case 'addcomment':
                $aclResource = 'epacei/estimate/actions/comment';
                break;
            case 'creditmemos':
                $aclResource = 'epacei/estimate/actions/creditmemo';
                break;
            case 'reviewpayment':
                $aclResource = 'epacei/estimate/actions/review_payment';
                break;
            default:
                $aclResource = 'epacei/estimate';
                break;

        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_estimate_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_estimate_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
