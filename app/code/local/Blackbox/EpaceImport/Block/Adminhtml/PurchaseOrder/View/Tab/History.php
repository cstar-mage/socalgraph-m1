<?php
/**
 * PurchaseOrder history tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Tab_History
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('blackbox/epacei/purchase_order/view/tab/history.phtml');
    }

    /**
     * Retrieve purchase order model instance
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        return Mage::registry('current_purchase_order');
    }

    /**
     * Compose and get purchase order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     *
     * @return array
     */
    public function getFullHistory()
    {
        $purchaseOrder = $this->getPurchaseOrder();

        $history = array();
        foreach ($purchaseOrder->getAllStatusHistory() as $purchaseOrderComment){
            $history[] = $this->_prepareHistoryItem(
                $purchaseOrderComment->getStatusLabel(),
                $purchaseOrderComment->getIsCustomerNotified(),
                $purchaseOrderComment->getCreatedAtDate(),
                $purchaseOrderComment->getComment()
            );
        }

        foreach ($purchaseOrder->getCreditmemosCollection() as $_memo){
            $history[] = $this->_prepareHistoryItem(
                $this->__('Credit memo #%s created', $_memo->getIncrementId()),
                $_memo->getEmailSent(),
                $_memo->getCreatedAtDate()
            );

            foreach ($_memo->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Credit memo #%s comment added', $_memo->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($purchaseOrder->getShipmentsCollection() as $_shipment){
            $history[] = $this->_prepareHistoryItem(
                $this->__('Shipment #%s created', $_shipment->getIncrementId()),
                $_shipment->getEmailSent(),
                $_shipment->getCreatedAtDate()
            );

            foreach ($_shipment->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Shipment #%s comment added', $_shipment->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($purchaseOrder->getInvoiceCollection() as $_invoice){
            $history[] = $this->_prepareHistoryItem(
                $this->__('Invoice #%s created', $_invoice->getIncrementId()),
                $_invoice->getEmailSent(),
                $_invoice->getCreatedAtDate()
            );

            foreach ($_invoice->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    $this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($purchaseOrder->getTracksCollection() as $_track){
            $history[] = $this->_prepareHistoryItem(
                $this->__('Tracking number %s for %s assigned', $_track->getNumber(), $_track->getTitle()),
                false,
                $_track->getCreatedAtDate()
            );
        }

        usort($history, array(__CLASS__, "_sortHistoryByTimestamp"));
        return $history;
    }

    /**
     * Status history date/datetime getter
     *
     * @param array $item
     * @param string $dateType
     * @param string $format
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = 'medium')
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($item['created_at'], $format);
        }
        return $this->helper('core')->formatTime($item['created_at'], $format);
    }

    /**
     * Status history item title getter
     *
     * @param array $item
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return (isset($item['title']) ? $this->escapeHtml($item['title']) : '');
    }

    /**
     * Check whether status history comment is with customer notification
     *
     * @param array $item
     * @param boolean $isSimpleCheck
     * @return bool
     */
    public function isItemNotified(array $item, $isSimpleCheck = true)
    {
        if ($isSimpleCheck) {
            return !empty($item['notified']);
        }
        return isset($item['notified']) && false !== $item['notified'];
    }

    /**
     * Status history item comment getter
     *
     * @param array $item
     * @return string
     */
    public function getItemComment(array $item)
    {
        $strItemComment = '';
        if (isset($item['comment'])) {
            $allowedTags = array('b', 'br', 'strong', 'i', 'u', 'a');
            /** @var Mage_Adminhtml_Helper_Sales $salesHelper */
            $salesHelper = Mage::helper('adminhtml/sales');
            $strItemComment = $salesHelper->escapeHtmlWithLinks($item['comment'], $allowedTags);
        }
        return $strItemComment;
    }

    /**
     * Map history items as array
     *
     * @param string $label
     * @param bool $notified
     * @param Zend_Date $created
     * @param string $comment
     * @return array
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return array(
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created
        );
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('epacei')->__('Comments History');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('epacei')->__('PurchaseOrder History');
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/commentsHistory', array('_current' => true));
    }

    /**
     * Can Show Tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param array $historyItem
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] == Blackbox_EpaceImport_Model_PurchaseOrder_Status_History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Comparison For Sorting History By Timestamp
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function _sortHistoryByTimestamp($a, $b)
    {
        $createdAtA = $a['created_at'];
        $createdAtB = $b['created_at'];

        /** @var $createdAta Zend_Date */
        if ($createdAtA->getTimestamp() == $createdAtB->getTimestamp()) {
            return 0;
        }
        return ($createdAtA->getTimestamp() < $createdAtB->getTimestamp()) ? -1 : 1;
    }
}
