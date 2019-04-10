<?php
/**
 * Adminhtml sales purchase orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Adminhtml_Epace_PurchaseOrderController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('epacei/purchaseOrder')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Purchase Orders'), $this->__('Purchase Orders'));
        return $this;
    }

    /**
     * Initialize purchase order model instance
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder || false
     */
    protected function _initPurchaseOrder()
    {
        $id = $this->getRequest()->getParam('purchase_order_id');
        $purchaseOrder = Mage::getModel('epacei/purchaseOrder')->load($id);

        if (!$purchaseOrder->getId()) {
            $this->_getSession()->addError($this->__('This purchase order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('purchase_order', $purchaseOrder);
        Mage::register('current_purchase_order', $purchaseOrder);
        return $purchaseOrder;
    }

    /**
     * Purchase Orders grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Purchase Orders'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Purchase Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View purchase order detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Purchase Orders'));

        $purchaseOrder = $this->_initPurchaseOrder();
        if ($purchaseOrder) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $purchaseOrder->getRealPurchaseOrderId()));

            $this->renderLayout();
        }
    }

    /**
     * Add purchase order comment action
     */
    public function addCommentAction()
    {
        if ($purchaseOrder = $this->_initPurchaseOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $purchaseOrder->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $purchaseOrder->save();
                $purchaseOrder->sendPurchaseOrderUpdateEmail($notify, $comment);

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
                    'message'   => $this->__('Cannot add purchase order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate purchase order history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initPurchaseOrder();
        $html = $this->getLayout()->createBlock('epacei/adminhtml_purchaseOrder_view_tab_history')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Print all documents for selected purchase orders
     */
    public function pdfdocsAction(){
        $purchaseOrderIds = $this->getRequest()->getPost('purchase_order_ids');
        $flag = false;
        if (!empty($purchaseOrderIds)) {
            foreach ($purchaseOrderIds as $purchaseOrderId) {
                $invoices = Mage::getResourceModel('epacei/purchaseOrder_invoice_collection')
                    ->setPurchaseOrderFilter($purchaseOrderId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/purchaseOrder_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('epacei/purchaseOrder_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('epacei/purchaseOrder_shipment_collection')
                    ->setPurchaseOrderFilter($purchaseOrderId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/purchaseOrder_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('epacei/purchaseOrder_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('epacei/purchaseOrder_creditmemo_collection')
                    ->setPurchaseOrderFilter($purchaseOrderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('epacei/purchaseOrder_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('epacei/purchaseOrder_pdf_creditmemo')->getPdf($creditmemos);
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
                $this->_getSession()->addError($this->__('There are no printable documents related to selected purchase orders.'));
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
//            case 'cancel':
//                $aclResource = 'epacei/purchase_order/actions/cancel';
//                break;
//            case 'view':
//                $aclResource = 'epacei/purchase_order/actions/view';
//                break;
//            case 'addcomment':
//                $aclResource = 'epacei/purchase_order/actions/comment';
//                break;
            default:
                $aclResource = 'epacei/purchase_order';
                break;

        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export purchase order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'purchase_orders.csv';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_purchaseOrder_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export purchase order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'purchase_orders.xml';
        $grid       = $this->getLayout()->createBlock('epacei/adminhtml_purchaseOrder_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function versionsGridAction()
    {
        $purchaseOrder = $this->_initPurchaseOrder();
        if ($purchaseOrder) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function orderPurchaseOrdersGridAction()
    {
        $purchaseOrder = $this->_initOrder();
        if ($purchaseOrder) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
}
