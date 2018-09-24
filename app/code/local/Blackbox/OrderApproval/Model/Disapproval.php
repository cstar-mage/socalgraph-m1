<?php

/**
 * Order Disapproval Model
 *
 * @method Blackbox_OrderApproval_Model_Resource_Disapproval _getResource()
 * @method Blackbox_OrderApproval_Model_Resource_Disapproval getResource()
 * @method int getStoreId()
 * @method Blackbox_OrderApproval_Model_Disapproval setStoreId(int $value)
 * @method int getOrderId()
 * @method Blackbox_OrderApproval_Model_Disapproval setOrderId(int $value)
 * @method int getCustomerId()
 * @method Blackbox_OrderApproval_Model_Disapproval setCustomerId(int $value)
 * @method int getState()
 * @method Blackbox_OrderApproval_Model_Disapproval setState(int $value)
 * @method int getEmailSent()
 * @method Blackbox_OrderApproval_Model_Disapproval setEmailSent(int $value)
 * @method int getUserId()
 * @method Blackbox_OrderApproval_Model_Disapproval setUserId(int $value)
 * @method int getRuleId()
 * @method Blackbox_OrderApproval_Model_Disapproval setRuleId(int $value)
 * @method int getCommentId()
 * @method Blackbox_OrderApproval_Model_Disapproval setCommentId(int $value)
 * @method string getOrderIncrementId()
 * @method Blackbox_OrderApproval_Model_Disapproval setOrderIncrementId(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_OrderApproval_Model_Disapproval setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Blackbox_OrderApproval_Model_Disapproval setUpdatedAt(string $value)
 *
 * @category    Blackbox
 * @package     Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Disapproval extends Mage_Core_Model_Abstract
{
    /**
     * Approval states
     */
    const STATE_OPEN       = 1;
    const STATE_REAPPROVE   = 2;
    const STATE_CANCELED   = 3;
    const STATE_CLOSED   = 4;

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/disapproval/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/disapproval/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/disapproval/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/disapproval/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/disapproval/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/disapproval/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/disapproval_cancel/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/disapproval_cancel/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/disapproval_cancel/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/disapproval_cancel/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/disapproval_cancel/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/disapproval_cancel/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_APPROVAL_CREATED      = 'disapproval_created';

    /*
     * Identifier for order history item
     */
    const HISTORY_ENTITY_NAME = 'disapproval';

    protected static $_states;

    protected $_order;
    protected $_user;
    protected $_rule;

    protected $_hasComment = false;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_rounders = array();

    protected $_saveBeforeDestruct = false;

    protected $_eventPrefix = 'order_disapproval';
    protected $_eventObject = 'disapproval';

    /**
     * Whether the pay() was called
     * @var bool
     */
    protected $_wasDisapproveCalled = false;

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
     * Initialize disapproval resource model
     */
    protected function _construct()
    {
        $this->_init('order_approval/disapproval');
    }

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
     * Declare order for disapproval
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Blackbox_OrderApproval_Model_Disapproval
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId())
            ->setOrderIncrementId($order->getIncrementId());
        return $this;
    }

    /**
     * Retrieve the order the disapproval for created for
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
     * Declare user for disapproval
     *
     * @param   Mage_Customer_Model_Customer $order
     * @return  Blackbox_OrderApproval_Model_Disapproval
     */
    public function setUser(Mage_Customer_Model_Customer $user)
    {
        $this->_user = $user;
        $this->setUserId($user->getId());
        return $this;
    }

    /**
     * Retrieve the user the disapproval created by
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
     * Declare rule for disapproval
     *
     * @param   Blackbox_OrderApproval_Model_Rule $rule
     * @return  Blackbox_OrderApproval_Model_Disapproval
     */
    public function setRule(Blackbox_OrderApproval_Model_Rule $rule)
    {
        $this->_rule = $rule;
        $this->setRuleId($rule->getId());
        return $this;
    }

    /**
     * Retrieve the rule the disapproval created on
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

    /**
     * Check disapproval reapprove action availability
     *
     * @return bool
     */
    public function canReapprove()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check disapproval cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Cancel disapproval action
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function cancel()
    {
        $this->setState(self::STATE_CANCELED);
        Mage::dispatchEvent('order_disapproval_cancel', array($this->_eventObject=>$this));
        return $this;
    }

//    /**
//     * Retrieve disapproval states array
//     *
//     * @return array
//     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Active'),
                self::STATE_REAPPROVE       => Mage::helper('sales')->__('Reapprove'),
                self::STATE_CLOSED       => Mage::helper('sales')->__('Closed'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            );
        }
        return self::$_states;
    }

    /**
     * Retrieve disapproval state name by state identifier
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

    public function setComment($comment)
    {
        $this->_hasComment = true;
        return parent::setComment($comment);
    }

    public function getComment()
    {
        if ($this->_hasComment) {
            return parent::getComment();
        }
        if ($commentId = $this->getCommentId()) {
            $orderStatusHistory = Mage::getModel('sales/order_status_history')->load($commentId);
            $comment = $orderStatusHistory->getComment();
            $this->setComment($comment);
            return $comment;
        }
        return null;
    }

    /**
     * Register disapproval
     *
     * Apply to order, order items etc.
     *
     * @return $this
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(Mage::helper('sales')->__('Cannot register existing disapprove'));
        }

        $order = $this->getOrder();

        $history = $order->addStatusHistoryComment($this->getComment());
        $this->setHistory($history);

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }

        Mage::dispatchEvent('sales_order_disapproval_register', array($this->_eventObject=>$this, 'order' => $order));
        return $this;
    }

    /**
     * Send email with disapproval data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('order_approval')->canSendNewDisapprovalEmail($storeId)) {
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
                'disapproval'      => $this,
                'comment'      => $comment,
            )
        );
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with disapproval update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendDisapprovalCommentEmail($storeId)) {
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
                'disapproval'      => $this,
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
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_order = null;
        $this->_saveBeforeDestruct = false;
        $this->_hasComment = false;
        return $this;
    }

    /**
     * Before object save manipulations
     *
     * @return Blackbox_OrderApproval_Model_Disapproval
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        if (!$this->getOrderIncrementId() && $this->getOrder()) {
            $this->setOrderIncrementId($this->getOrder()->getIncrementId());
        }

        if (!$this->getUserId() && $this->getUser()) {
            $this->setUserId($this->getUser()->getId());
        }

        if (!$this->getRuleId() && $this->getRule()) {
            $this->setRuleId($this->getRule()->getId());
        }

        if ($history = $this->getHistory()) {
            if (!$history->getId()) {
                $history->save();
            }
            $this->setCommentId($history->getId());
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
        return parent::_afterSave();
    }
}
