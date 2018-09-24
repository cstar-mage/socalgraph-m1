<?php

abstract class Blackbox_Notification_Model_Validator_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Rule source collection
     *
     * @var array
     */
    protected $_rules;

    public function __construct()
    {
        parent::__construct();
        $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
    }

    /**
     * @return Blackbox_Notification_Model_Resource_Rule_Collection_Abstract
     */
    protected abstract function _getCollectionModel();

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param   int $websiteId
     * @param   string $scope
     * @return  $this
     */
    public function init($websiteId, $type)
    {
        $this->setWebsiteId($websiteId);
        $this->setType($type);

        $key = $websiteId;
        if (!isset($this->_rules[$key][$type])) {
            $this->_rules[$key][$type] = $this->_getCollectionModel()
                ->setValidationFilter($websiteId, $type)
                ->load();
        }


        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return Blackbox_Notification_Model_Resource_Rule_Collection
     */
    protected function _getRules()
    {
        $key = $this->getWebsiteId();
        $type = $this->getType();
        return $this->_rules[$key][$type];
    }

    public function processStockItem(Mage_CatalogInventory_Model_Stock_Item $item)
    {
        $this->init($this->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_PRODUCT_LOW_STOCK);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($item)) {
                $this->_sendNotification($rule, array(
                    'product' => $item->getProduct()
                ));
            }
        }
    }

    public function processOrderedItem($item)
    {
        $this->init($this->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_ORDER_EXCEED_QTY);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($item)) {
                $this->_sendNotification($rule, array(
                    'product' => $item->getProduct()
                ));
            }
        }
    }

    public function processAddedToCartProduct(Mage_Catalog_Model_Product $product)
    {
        $this->init($this->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_PRODUCT_ADD_TO_CART);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($product)) {
                $this->_sendNotification($rule, array(
                    'product' => $product
                ));
            }
        }
    }

    public function processCreateOrderRequiringApproval(Mage_Sales_Model_Order $order)
    {
        $this->init($this->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_APPROVAL_ORDER_CREATE);

        $storeId = $order->getStore()->getId();

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();

            foreach ($this->_getRules() as $rule) {
                /* @var Blackbox_Notification_Model_Rule_Abstract $rule */
                if ($rule->getConditions()->validate($order)) {
                    $this->_sendNotification($rule, array(
                        'order' => $order,
                        'billing' => $order->getBillingAddress(),
                        'payment_html' => $paymentBlockHtml
                    ));
                }
            }
        } finally {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        }
    }

    public function processOrderApproved(Mage_Sales_Model_Order $order)
    {
        $this->init($this->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_APPROVAL_ORDER_APPROVE);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($order)) {
                $this->_sendNotification($rule, array(
                    'order' => $order
                ), $order->getCustomerEmail());
            }
        }
    }

    public function processSalesEmail($object, $params, $type, $customerEmail)
    {
        $result = false;
        $this->init($object->getStore()->getWebsiteId(), $type);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($object)) {
                $this->_sendNotification($rule, $params, $customerEmail);
                $result = true;
            }
        }
        return $result;
    }

    public function processEditOrder(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Status_History $history)
    {
        $this->init(Mage::app()->getStore($order->getStoreId())->getWebsiteId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_ORDER_EDIT);

        foreach ($this->_getRules() as $rule) { /* @var Blackbox_Notification_Model_Rule_Abstract $rule*/
            if ($rule->getConditions()->validate($order)) {
                $this->_sendNotification($rule, array(
                    'order' => $order,
                    'history' => $history
                ), $order->getCustomerEmail());
            }
        }
    }

    public function processHelpdeskTicketNew(Blackbox_HelpDesk_Model_Ticket $ticket)
    {
        $this->init(Mage::app()->getStore()->getId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_HELPDESK_NEW);

        foreach ($this->_getRules() as $rule) {
            if ($rule->getConditions()->validate($ticket)) {
                $this->_sendNotification($rule, array(
                    'ticket' => $ticket,
                ));
            }
        }
    }

    public function processHelpdeskSupportResponse(Blackbox_HelpDesk_Model_Ticket $ticket, Blackbox_HelpDesk_Model_Comment $comment)
    {
        $this->init(Mage::app()->getStore()->getId(), Blackbox_Notification_Model_Rule_Abstract::TYPE_HELPDESK_SUPPORT_RESPONSE);

        foreach ($this->_getRules() as $rule) {
            if ($rule->getConditions()->validate($ticket)) {
                $this->_sendNotification($rule, array(
                    'ticket' => $ticket,
                    'comment' => $comment,
                ));
            }
        }
    }

    public function processNotification($type, $validateObject, $params, $additionalEmail = null)
    {
        if ($validateObject == null) {
            $validateObject = new Varien_Object();
        }
        $this->init(Mage::app()->getStore()->getId(), $type);

        foreach ($this->_getRules() as $rule) {
            if ($rule->getConditions()->validate($validateObject)) {
                $this->_sendNotification($rule, $params, $additionalEmail);
            }
        }
    }

    /**
     * @param Blackbox_Notification_Model_Rule_Abstract $rule
     * @param array $params
     */
    protected abstract function _sendNotification(Blackbox_Notification_Model_Rule_Abstract $rule, $params, $additionalEmail = null);
}