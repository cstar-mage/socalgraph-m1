<?php

/**
 * Order Approval Approve Model
 *
 * @method Blackbox_OrderApproval_Model_Resource_Approval _getResource()
 * @method Blackbox_OrderApproval_Model_Resource_Approval getResource()
 * @method int getStoreId()
 * @method Blackbox_OrderApproval_Model_Approval setStoreId(int $value)
 * @method float getTotalQty()
 * @method Blackbox_OrderApproval_Model_Approval setTotalQty(float $value)
 * @method int getOrderId()
 * @method Blackbox_OrderApproval_Model_Approval setOrderId(int $value)
 * @method int getState()
 * @method Blackbox_OrderApproval_Model_Approval setSate(int $value)
 * @method int getEmailSent()
 * @method Blackbox_OrderApproval_Model_Approval setEmailSent(int $value)
 * @method int getUserId()
 * @method Blackbox_OrderApproval_Model_Approval setUserId(int $value)
 * @method int getRuleId()
 * @method Blackbox_OrderApproval_Model_Approval setRuleId(int $value)
 * @method string getIncrementId()
 * @method Blackbox_OrderApproval_Model_Approval setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_OrderApproval_Model_Approval setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Blackbox_OrderApproval_Model_Approval setUpdatedAt(string $value)
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Approval extends Mage_Sales_Model_Abstract
{
    /**
     * Approval states
     */
    const STATE_OPEN       = 1;
    const STATE_CANCELED   = 2;

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/approval/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/approval/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/approval/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/approval/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/approval/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/approval/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/approval_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/approval_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/approval_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/approval_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/approval_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/approval_comment/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_APPROVAL_CREATED      = 'approval_created';

    /*
     * Identifier for order history item
     */
    const HISTORY_ENTITY_NAME = 'approval';

    protected static $_states;

    protected $_items;
    protected $_comments;
    protected $_order;
    protected $_user;
    protected $_rule;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_rounders = array();

    protected $_saveBeforeDestruct = false;

    protected $_eventPrefix = 'order_approval';
    protected $_eventObject = 'approval';

    /**
     * Whether the pay() was called
     * @var bool
     */
    protected $_wasApproveCalled = false;

    public function __construct()
    {
        register_shutdown_function(array($this, 'destruct'));
        parent::__construct();
    }

    /**
     * Uploader clean on shutdown
     */
    public function destruct()
    {
        if ($this->_saveBeforeDestruct) {
            $this->save();
        }
    }

    /**
     * Initialize approve resource model
     */
    protected function _construct()
    {
        $this->_init('order_approval/approval');
    }

    /**
     * Load approve by increment id
     *
     * @param string $incrementId
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function loadByIncrementId($incrementId)
    {
        $ids = $this->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();

        if (!empty($ids)) {
            reset($ids);
            $this->load(current($ids));
        }
        return $this;
    }

//    /**
//     * Retrieve invoice configuration model
//     *
//     * @return Blackbox_OrderApproval_Model_Approval_Config
//     */
//    public function getConfig()
//    {
//        return Mage::getSingleton('sales/order_invoice_config');
//    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for approve
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Blackbox_OrderApproval_Model_Approval
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the approve for created for
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order->setHistoryEntityName(self::HISTORY_ENTITY_NAME);
    }

    /**
     * Retrieve the increment_id of the order
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return Mage::getModel('sales/order')->getResource()->getIncrementId($this->getOrderId());
    }



    /**
     * Declare user for approve
     *
     * @param   Mage_Customer_Model_Customer $order
     * @return  Blackbox_OrderApproval_Model_Approval
     */
    public function setUser(Mage_Customer_Model_Customer $user)
    {
        $this->_user = $user;
        $this->setUserId($user->getId());
        return $this;
    }

    /**
     * Retrieve the user the approve created by
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getUser()
    {
        if (!$this->_user instanceof Mage_Customer_Model_Customer) {
            $this->_user = Mage::getModel('customer/customer')->load($this->getUserId());
        }
        return $this->_user;
    }



    /**
     * Declare rule for approve
     *
     * @param   Blackbox_OrderApproval_Model_Rule $rule
     * @return  Blackbox_OrderApproval_Model_Approval
     */
    public function setRule(Blackbox_OrderApproval_Model_Rule $rule)
    {
        $this->_rule = $rule;
        $this->setRuleId($rule->getId());
        return $this;
    }

    /**
     * Retrieve the rule the approve created on
     *
     * @return Blackbox_OrderApproval_Model_Rule
     */
    public function getRule()
    {
        if (!$this->_rule instanceof Blackbox_OrderApproval_Model_Rule) {
            $this->_rule = Mage::getModel('order_approval/rule')->load($this->getRuleId());
        }
        return $this->_rule;
    }

//    /**
//     * Retrieve billing address
//     *
//     * @return Mage_Sales_Model_Order_Address
//     */
//    public function getBillingAddress()
//    {
//        return $this->getOrder()->getBillingAddress();
//    }

//    /**
//     * Retrieve shipping address
//     *
//     * @return Mage_Sales_Model_Order_Address
//     */
//    public function getShippingAddress()
//    {
//        return $this->getOrder()->getShippingAddress();
//    }

//    /**
//     * Check invoice cancel state
//     *
//     * @return bool
//     */
//    public function isCanceled()
//    {
//        return $this->getState() == self::STATE_CANCELED;
//    }

//    /**
//     * Check invice capture action availability
//     *
//     * @return bool
//     */
//    public function canCapture()
//    {
//        return $this->getState() != self::STATE_CANCELED
//            && $this->getState() != self::STATE_PAID
//            && $this->getOrder()->getPayment()->canCapture();
//    }

//    /**
//     * Check invice void action availability
//     *
//     * @return bool
//     */
//    public function canVoid()
//    {
//        $canVoid = false;
//        if ($this->getState() == self::STATE_PAID) {
//            $canVoid = $this->getCanVoidFlag();
//            /**
//             * If we not retrieve negative answer from payment yet
//             */
//            if (is_null($canVoid)) {
//                $canVoid = $this->getOrder()->getPayment()->canVoid($this);
//                if ($canVoid === false) {
//                    $this->setCanVoidFlag(false);
//                    $this->_saveBeforeDestruct = true;
//                }
//            }
//            else {
//                $canVoid = (bool) $canVoid;
//            }
//        }
//        return $canVoid;
//    }

//    /**
//     * Check invoice cancel action availability
//     *
//     * @return bool
//     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

//    /**
//     * Check invoice refund action availability
//     *
//     * @return bool
//     */
//    public function canRefund()
//    {
//        if ($this->getState() != self::STATE_PAID) {
//            return false;
//        }
//        if (abs($this->getBaseGrandTotal() - $this->getBaseTotalRefunded()) < .0001) {
//            return false;
//        }
//        return true;
//    }

//    /**
//     * Capture invoice
//     *
//     * @return Blackbox_OrderApproval_Model_Approval
//     */
//    public function capture()
//    {
//        $this->getOrder()->getPayment()->capture($this);
//        if ($this->getIsPaid()) {
//            $this->pay();
//        }
//        return $this;
//    }

    /**
     * Pay invoice
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function approve()
    {
        if ($this->_wasApproveCalled) {
            return $this;
        }
        $this->_wasApproveCalled = true;

//        $invoiceState = self::STATE_PAID;
//        if ($this->getOrder()->getPayment()->hasForcedState()) {
//            $invoiceState = $this->getOrder()->getPayment()->getForcedState();
//        }

//        $this->setState($invoiceState);

//        $this->getOrder()->getPayment()->pay($this);
//        $this->getOrder()->setTotalPaid(
//            $this->getOrder()->getTotalPaid()+$this->getGrandTotal()
//        );
//        $this->getOrder()->setBaseTotalPaid(
//            $this->getOrder()->getBaseTotalPaid()+$this->getBaseGrandTotal()
//        );
        Mage::dispatchEvent('order_approval_approve', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Whether pay() method was called (whether order and payment totals were updated)
     * @return bool
     */
    public function wasApproveCalled()
    {
        return $this->_wasApproveCalled;
    }

//    /**
//     * Void invoice
//     *
//     * @return Blackbox_OrderApproval_Model_Approval
//     */
//    public function void()
//    {
//        $this->getOrder()->getPayment()->void($this);
//        $this->cancel();
//        return $this;
//    }

    /**
     * Cancel invoice action
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function cancel()
    {
        $order = $this->getOrder();
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }

        if (Mage::helper('order_approval')->needApprove($order)) {
            $order->setIsApproved(0);
        }

        $this->setState(self::STATE_CANCELED);
        Mage::dispatchEvent('order_approval_cancel', array($this->_eventObject=>$this));
        return $this;
    }

//    /**
//     * Invoice totals collecting
//     *
//     * @return Blackbox_OrderApproval_Model_Approval
//     */
//    public function collectTotals()
//    {
//        foreach ($this->getConfig()->getTotalModels() as $model) {
//            $model->collect($this);
//        }
//        return $this;
//    }

    /**
     * Get invoice items collection
     *
     * @return Mage_Sales_Model_Mysql4_Order_Invoice_Item_Collection
     */
    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('order_approval/approval_item_collection')
                ->setApprovalFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setApproval($this);
                }
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function addItem(Blackbox_OrderApproval_Model_Approval_Item $item)
    {
        $item->setApproval($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());

        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

//    /**
//     * Retrieve invoice states array
//     *
//     * @return array
//     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Active'),
//                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            );
        }
        return self::$_states;
    }

    /**
     * Retrieve invoice state name by state identifier
     *
     * @param   int $stateId
     * @return  string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return Mage::helper('sales')->__('Unknown State');
    }

    /**
     * Register invoice
     *
     * Apply to order, order items etc.
     *
     * @return unknown
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(Mage::helper('sales')->__('Cannot register existing approve'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            }
            else {
                $item->isDeleted(true);
            }
        }

        $order = $this->getOrder();

        if (Mage::helper('order_approval')->isOrderApproved($order)) {
            $order->setIsApproved(1);
        }

//        $captureCase = $this->getRequestedCaptureCase();
//        if ($this->canCapture()) {
//            if ($captureCase) {
//                if ($captureCase == self::CAPTURE_ONLINE) {
//                    $this->capture();
//                }
//                elseif ($captureCase == self::CAPTURE_OFFLINE) {
//                    $this->setCanVoidFlag(false);
//                    $this->pay();
//                }
//            }
//        } elseif(!$order->getPayment()->getMethodInstance()->isGateway() || $captureCase == self::CAPTURE_OFFLINE) {
//            if (!$order->getPayment()->getIsTransactionPending()) {
//                $this->setCanVoidFlag(false);
//                $this->pay();
//            }
//        }

//        $order->setQtyApproved($order->getQtyApproved() + $this->getTotalQty());

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }

        Mage::dispatchEvent('sales_order_approval_register', array($this->_eventObject=>$this, 'order' => $order));
        return $this;
    }

    /**
     * Checking if the approve is last
     *
     * @return bool
     */
    public function isLast()
    {
        foreach ($this->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds comment to invoice with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param bool $notify
     * @param bool $visibleOnFront
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function addComment($comment, $notify=false, $visibleOnFront=false)
    {
        if (!($comment instanceof Blackbox_OrderApproval_Model_Approval_Comment)) {
            $comment = Mage::getModel('order_approval/approval_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setApproval($this)
            ->setStoreId($this->getStoreId())
            ->setParentId($this->getId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = true;
        return $this;
    }

    public function getCommentsCollection($reload=false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = Mage::getResourceModel('order_approval/approval_comment_collection')
                ->setApprovalFilter($this->getId())
                ->setCreatedAtOrder();
            /**
             * When approve created with adding comment, comments collection
             * must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setApproval($this);
                }
            }
        }
        return $this->_comments;
    }

    /**
     * Send email with approval data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('order_approval')->canSendNewApprovalEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'approval'      => $this,
                'comment'      => $comment,
            )
        );
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with approval update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendApprovalCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'approval'      => $this,
                'comment'      => $comment,
            )
        );
        $mailer->send();

        return $this;
    }

    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Reset approval object
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_items = null;
        $this->_comments = null;
        $this->_order = null;
        $this->_saveBeforeDestruct = false;
        $this->_wasApproveCalled = false;
        return $this;
    }

    /**
     * Before object save manipulations
     *
     * @return Blackbox_OrderApproval_Model_Approval
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        if (!$this->getUserId() && $this->getUser()) {
            $this->setUserId($this->getUser()->getId());
        }

        if (!$this->getRuleId() && $this->getRule()) {
            $this->setRuleId($this->getRule()->getId());
        }

        return $this;
    }

    /**
     * After object save manipulation
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _afterSave()
    {

        if (null !== $this->_items) {
            /**
             * Save invoice items
             */
            foreach ($this->_items as $item) {
                $item->setOrderItem($item->getOrderItem());
                $item->save();
            }
        }

        if (null !== $this->_comments) {
            foreach($this->_comments as $comment) {
                $comment->save();
            }
        }

        return parent::_afterSave();
    }
}
