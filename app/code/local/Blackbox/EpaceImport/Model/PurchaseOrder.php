<?php
/**
 * PurchaseOrder model
 *
 * Supported events:
 *  epacei_purchase_order_load_after
 *  epacei_purchase_order_save_before
 *  epacei_purchase_order_save_after
 *  epacei_purchase_order_delete_before
 *  epacei_purchase_order_delete_after
 *
 * @method Blackbox_EpaceImport_Model_Resource_PurchaseOrder _getResource()
 * @method Blackbox_EpaceImport_Model_Resource_PurchaseOrder getResource()
 * @method string getState()
 * @method string getStatus()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStatus(string $value)
 * @method string getCouponCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCouponCode(string $value)
 * @method string getProtectCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setProtectCode(string $value)
 * @method string getShippingDescription()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingDescription(string $value)
 * @method int getIsVirtual()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setIsVirtual(int $value)
 * @method int getStoreId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStoreId(int $value)
 * @method int getCustomerId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerId(int $value)
 * @method float getBaseDiscountAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseDiscountAmount(float $value)
 * @method float getBaseDiscountCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseDiscountCanceled(float $value)
 * @method float getBaseDiscountInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseDiscountInvoiced(float $value)
 * @method float getBaseDiscountRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseDiscountRefunded(float $value)
 * @method float getBaseGrandTotal()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseGrandTotal(float $value)
 * @method float getBaseShippingAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingAmount(float $value)
 * @method float getBaseShippingCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingCanceled(float $value)
 * @method float getBaseShippingInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingInvoiced(float $value)
 * @method float getBaseShippingRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingRefunded(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingTaxRefunded(float $value)
 * @method float getBaseSubtotal()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseSubtotal(float $value)
 * @method float getBaseSubtotalCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseSubtotalCanceled(float $value)
 * @method float getBaseSubtotalInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseSubtotalInvoiced(float $value)
 * @method float getBaseSubtotalRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseSubtotalRefunded(float $value)
 * @method float getBaseTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTaxAmount(float $value)
 * @method float getBaseTaxCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTaxCanceled(float $value)
 * @method float getBaseTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTaxInvoiced(float $value)
 * @method float getBaseTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTaxRefunded(float $value)
 * @method float getBaseToGlobalRate()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseToGlobalRate(float $value)
 * @method float getBaseToPurchaseOrderRate()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseToPurchaseOrderRate(float $value)
 * @method float getBaseTotalCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalCanceled(float $value)
 * @method float getBaseTotalInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalInvoiced(float $value)
 * @method float getBaseTotalInvoicedCost()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalInvoicedCost(float $value)
 * @method float getBaseTotalOfflineRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalOfflineRefunded(float $value)
 * @method float getBaseTotalOnlineRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalOnlineRefunded(float $value)
 * @method float getBaseTotalPaid()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalPaid(float $value)
 * @method float getBaseTotalQty()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalQty(float $value)
 * @method float getBaseTotalRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalRefunded(float $value)
 * @method float getDiscountAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setDiscountAmount(float $value)
 * @method float getDiscountCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setDiscountCanceled(float $value)
 * @method float getDiscountInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setDiscountInvoiced(float $value)
 * @method float getDiscountRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setDiscountRefunded(float $value)
 * @method float getGrandTotal()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingAmount(float $value)
 * @method float getShippingCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingCanceled(float $value)
 * @method float getShippingInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingInvoiced(float $value)
 * @method float getShippingRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingRefunded(float $value)
 * @method float getShippingTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingTaxAmount(float $value)
 * @method float getShippingTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingTaxRefunded(float $value)
 * @method float getStoreToBaseRate()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStoreToBaseRate(float $value)
 * @method float getStoreToPurchaseOrderRate()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStoreToPurchaseOrderRate(float $value)
 * @method float getSubtotal()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setSubtotal(float $value)
 * @method float getSubtotalCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setSubtotalCanceled(float $value)
 * @method float getSubtotalInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setSubtotalInvoiced(float $value)
 * @method float getSubtotalRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setSubtotalRefunded(float $value)
 * @method float getTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTaxAmount(float $value)
 * @method float getTaxCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTaxCanceled(float $value)
 * @method float getTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTaxInvoiced(float $value)
 * @method float getTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTaxRefunded(float $value)
 * @method float getTotalCanceled()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalCanceled(float $value)
 * @method float getTotalInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalInvoiced(float $value)
 * @method float getTotalOfflineRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalOfflineRefunded(float $value)
 * @method float getTotalOnlineRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalOnlineRefunded(float $value)
 * @method float getTotalPaid()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalPaid(float $value)
 * @method float getTotalQty()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalQty(float $value)
 * @method float getTotalRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalRefunded(float $value)
 * @method int getCanShipPartially()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCanShipPartially(int $value)
 * @method int getCanShipPartiallyItem()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCanShipPartiallyItem(int $value)
 * @method int getCustomerIsGuest()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerIsGuest(int $value)
 * @method int getCustomerNoteNotify()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerNoteNotify(int $value)
 * @method int getBillingAddressId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBillingAddressId(int $value)
 * @method int getCustomerGroupId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerGroupId(int $value)
 * @method int getEditIncrement()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setEditIncrement(int $value)
 * @method int getEmailSent()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setEmailSent(int $value)
 * @method int getForcedDoShipmentWithInvoice()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setForcedDoShipmentWithInvoice(int $value)
 * @method int getGiftMessageId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setGiftMessageId(int $value)
 * @method int getPaymentAuthorizationExpiration()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setPaymentAuthorizationExpiration(int $value)
 * @method int getPaypalIpnCustomerNotified()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setPaypalIpnCustomerNotified(int $value)
 * @method int getQuoteAddressId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setQuoteAddressId(int $value)
 * @method int getQuoteId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setQuoteId(int $value)
 * @method int getShippingAddressId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingAddressId(int $value)
 * @method float getAdjustmentNegative()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setAdjustmentNegative(float $value)
 * @method float getAdjustmentPositive()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setAdjustmentPositive(float $value)
 * @method float getBaseAdjustmentNegative()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseAdjustmentNegative(float $value)
 * @method float getBaseAdjustmentPositive()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseAdjustmentPositive(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingDiscountAmount(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseSubtotalInclTax(float $value)
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseTotalDue(float $value)
 * @method float getPaymentAuthorizationAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setPaymentAuthorizationAmount(float $value)
 * @method float getShippingDiscountAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setSubtotalInclTax(float $value)
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalDue(float $value)
 * @method float getWeight()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setWeight(float $value)
 * @method string getCustomerDob()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerDob(string $value)
 * @method string getIncrementId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setIncrementId(string $value)
 * @method string getAppliedRuleIds()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setAppliedRuleIds(string $value)
 * @method string getBaseCurrencyCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseCurrencyCode(string $value)
 * @method string getCustomerEmail()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerEmail(string $value)
 * @method string getCustomerFirstname()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerFirstname(string $value)
 * @method string getCustomerMiddlename()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerMiddlename(string $value)
 * @method string getCustomerLastname()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerLastname(string $value)
 * @method string getCustomerPrefix()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerPrefix(string $value)
 * @method string getCustomerSuffix()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerSuffix(string $value)
 * @method string getCustomerTaxvat()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerTaxvat(string $value)
 * @method string getDiscountDescription()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setDiscountDescription(string $value)
 * @method string getExtCustomerId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setExtCustomerId(string $value)
 * @method string getExtPurchaseOrderId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setExtPurchaseOrderId(string $value)
 * @method string getGlobalCurrencyCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setGlobalCurrencyCode(string $value)
 * @method string getHoldBeforeState()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setHoldBeforeState(string $value)
 * @method string getHoldBeforeStatus()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setHoldBeforeStatus(string $value)
 * @method string getOrderCurrencyCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setPurchaseOrderCurrencyCode(string $value)
 * @method string getOriginalIncrementId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setOriginalIncrementId(string $value)
 * @method string getRelationChildId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setRelationChildId(string $value)
 * @method string getRelationChildRealId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setRelationChildRealId(string $value)
 * @method string getRelationParentId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setRelationParentId(string $value)
 * @method string getRelationParentRealId()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setRelationParentRealId(string $value)
 * @method string getRemoteIp()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setRemoteIp(string $value)
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingMethod(string $value)
 * @method string getStoreCurrencyCode()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStoreCurrencyCode(string $value)
 * @method string getStoreName()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setStoreName(string $value)
 * @method string getXForwardedFor()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setXForwardedFor(string $value)
 * @method string getCustomerNote()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerNote(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setUpdatedAt(string $value)
 * @method int getTotalItemCount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setTotalItemCount(int $value)
 * @method int getCustomerGender()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setCustomerGender(int $value)
 * @method float getHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingHiddenTaxAmount(float $value)
 * @method float getHiddenTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setHiddenTaxInvoiced(float $value)
 * @method float getBaseHiddenTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseHiddenTaxInvoiced(float $value)
 * @method float getHiddenTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setHiddenTaxRefunded(float $value)
 * @method float getBaseHiddenTaxRefunded()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseHiddenTaxRefunded(float $value)
 * @method float getShippingInclTax()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method Blackbox_EpaceImport_Model_PurchaseOrder setBaseShippingInclTax(float $value)
 */
class Blackbox_EpaceImport_Model_PurchaseOrder extends Mage_Sales_Model_Abstract
{
    /**
     * Identifier for history item
     */
    const ENTITY                                = 'purchase_order';

    /**
     * PurchaseOrder statuses
     */
    const STATUS_CLOSED                 = 'C';
    const STATUS_OPEN                   = 'O';
    const STATUS_PENDING                = 'P';
    const STATUS_RECEIVED               = 'R';
    const STATUS_CANCELLED              = 'X';
    const STATUS_RECONCILED             = 'Z';

    /**
     * PurchaseOrder flags
     */
    const ACTION_FLAG_CANCEL                    = 'cancel';
    /*
     * Identifier for history item
     */
    const HISTORY_ENTITY_NAME = 'purchase_order';

    protected $_eventPrefix = 'epacei_purchase_order';
    protected $_eventObject = 'purchase_order';

    protected $_items           = null;
    protected $_statusHistory   = null;

    protected $_relatedObjects  = array();
    protected $_purchaseOrderCurrency   = null;
    protected $_baseCurrency    = null;

    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = array();

    /**
     * Flag: if after purchase order placing we can send new email to the customer.
     *
     * @var bool
     */
    protected $_canSendNewEmailFlag = true;

    /*
     * Identifier for history item
     *
     * @var string
     */
    protected $_historyEntityName = self::HISTORY_ENTITY_NAME;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('epacei/purchaseOrder');
    }

    /**
     * Clear purchase order object data
     *
     * @param string $key data key
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function unsetData($key=null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_items = null;
        }
        return $this;
    }

    /**
     * Retrieve can flag for action (edit, unhold, etc..)
     *
     * @param string $action
     * @return boolean|null
     */
    public function getActionFlag($action)
    {
        if (isset($this->_actionFlag[$action])) {
            return $this->_actionFlag[$action];
        }
        return null;
    }

    /**
     * Set can flag value for action (edit, unhold, etc...)
     *
     * @param string $action
     * @param boolean $flag
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function setActionFlag($action, $flag)
    {
        $this->_actionFlag[$action] = (boolean) $flag;
        return $this;
    }

    /**
     * Load purchase order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }

    public function getContactName()
    {
        return $this->getContactFirstname() . ' ' . $this->getContactLastname();
    }

    /**
     * Retrieve purchase order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        $status = $this->getStatus();
        if ($this->isCanceled() || $status === self::STATUS_CANCELLED || $status === self::STATUS_CLOSED || $status === self::STATUS_RECEIVED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if comment can be added to purchase order history
     *
     * @return bool
     */
    public function canComment()
    {
        return true;
    }

    /**
     * Retrieve purchase order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * Retrieve shipping method
     *
     * @param bool $asObject return carrier code and shipping method data as object
     * @return string|Varien_Object
     */
    public function getShippingMethod($asObject = false)
    {
        $shippingMethod = parent::getShippingMethod();
        if (!$asObject) {
            return $shippingMethod;
        } else {
            $segments = explode('_', $shippingMethod, 2);
            if (!isset($segments[1])) {
                $segments[1] = $segments[0];
            }
            list($carrierCode, $method) = $segments;
            return new Varien_Object(array(
                'carrier_code' => $carrierCode,
                'method'       => $method
            ));
        }
    }

    /**
     * Retrieve purchase order configuration model
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('epacei/purchaseOrder_config');
    }

    protected function _setStatus($status, $comment = '')
    {
        $this->setData('status', $status);
        $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
        return $this;
    }

    /**
     * Retrieve label of purchase order status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * Add status change information to history
     * @deprecated after 1.4.0.0-alpha3
     *
     * @param  string $status
     * @param  string $comment
     * @param  bool $isCustomerNotified
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $history = $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    /*
     * Add a comment to purchase order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Blackbox_EpaceImport_Model_PurchaseOrder_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
        $history = Mage::getModel('epacei/purchaseOrder_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     * Overrides entity id, which will be saved to comments history status
     *
     * @param string $status
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function setHistoryEntityName( $entityName )
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }

    /**
     * Cancel purchase order
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->registerCancellation();

            Mage::dispatchEvent('purchase_order_cancel_after', array('purchase_order' => $this));
        }

        return $this;
    }

    /**
     * Prepare purchase order totals to cancellation
     * @param string $comment
     * @param bool $graceful
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     * @throws Mage_Core_Exception
     */
    public function registerCancellation($comment = '', $graceful = true)
    {
        if ($this->canCancel()) {
            $cancelStatus = self::STATUS_CANCELLED;

            $this->_setStatus($cancelStatus, $comment);
        } elseif (!$graceful) {
            Mage::throwException(Mage::helper('sales')->__('PurchaseOrder does not allow to be canceled.'));
        }
        return $this;
    }

    public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('epacei/purchaseOrder_item_collection')
                ->setPurchaseOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setPurchaseOrder($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * @return Blackbox_EpaceImport_Model_PurchaseOrder_Item[]
     */
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
        return $this->getItemsCollection()->getItemById($itemId);
    }

    public function addItem(Blackbox_EpaceImport_Model_PurchaseOrder_Item $item)
    {
        $item->setPurchaseOrder($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

/*********************** STATUSES ***************************/

    /**
     * Enter description here...
     *
     * @return Blackbox_EpaceImport_Model_Resource_PurchaseOrder_Status_History_Collection
     */
    public function getStatusHistoryCollection($reload=false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = Mage::getResourceModel('epacei/purchaseOrder_status_history_collection')
                ->setPurchaseOrderFilter($this)
                ->setPurchaseOrder('created_at', 'desc')
                ->setPurchaseOrder('entity_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setPurchaseOrder($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Return collection of purchase order status history items.
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder_Status_History[]
     */
    public function getAllStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * Return collection of visible on frontend purchase order status history items.
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }

    /**
     * Set the purchase order status history object and the purchase order object to each other
     * Adds the object to the status history collection, which is automatically saved when the purchase order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param Blackbox_EpaceImport_Model_PurchaseOrder_Status_History $status
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function addStatusHistory(Blackbox_EpaceImport_Model_PurchaseOrder_Status_History $history)
    {
        $history->setPurchaseOrder($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->getStatusHistoryCollection()->addItem($history);
        }
        return $this;
    }


    /**
     * Enter description here...
     *
     * @return string
     */
    public function getRealPurchaseOrderId()
    {
        $this->getEpacePurchaseOrderId();
    }

    /**
     * Get currency model instance. Will be used currency with which purchase order placed
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getPurchaseOrderCurrency()
    {
        if (is_null($this->_purchaseOrderCurrency)) {
            $this->_purchaseOrderCurrency = Mage::getModel('directory/currency')->load($this->getOrderCurrencyCode());
        }
        return $this->_purchaseOrderCurrency;
    }

    /**
     * Get formated price value including purchase order currency rate to purchase order website currency
     *
     * @param   float $price
     * @param   bool  $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getPurchaseOrderCurrency()->formatPrecision($price, $precision, array(), true, $addBrackets);
    }

    /**
     * Retrieve text formated price value includeing purchase order rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->getPurchaseOrderCurrency()->formatTxt($price);
    }

    /**
     * Retrieve purchase order website currency for working with base prices
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getBaseCurrency()
    {
        if (is_null($this->_baseCurrency)) {
            $this->_baseCurrency = Mage::getModel('directory/currency')->load($this->getBaseCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    public function formatBasePrice($price)
    {
        return $this->formatBasePricePrecision($price, 2);
    }

    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    public function isCurrencyDifferent()
    {
        return $this->getOrderCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Get formated purchase order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    /**
     * @return Blackbox_EpaceImport_Model_Address
     */
    public function getShipToAddress()
    {
        if (!$this->getData('ship_to_address') && $this->getData('ship_to_address_id')) {
            $address = Mage::getModel('epacei/address')->load($this->getData('ship_to_address_id'));
            if ($address->getId()) {
                $this->setData('ship_to_address', $address);
            }
        }
        return $this->getData('ship_to_address');
    }

    /**
     * @return Blackbox_EpaceImport_Model_Address
     */
    public function getVendorAddress()
    {
        if (!$this->getData('vendor_address') && $this->getData('vendor_address_id')) {
            $address = Mage::getModel('epacei/address')->load($this->getData('vendor_address_id'));
            if ($address->getId()) {
                $this->setData('vendor_address', $address);
            }
        }
        return $this->getData('vendor_address');
    }

    public function setShipToAddress($address)
    {
        $this->setShipToAddressId($address->getId())
            ->setData('ship_to_address', $address);
        return $this;
    }

    public function setVendorAddress($address)
    {
        $this->setVendorAddressId($address->getId())
            ->setData('vendor_address', $address);
        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = array($store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName());
            $this->setStoreName(implode("\n", $name));
        }

        /**
         * Process items dependency for new purchase order
         */
        if (!$this->getId()) {
            $itemsCount = 0;
            foreach ($this->getAllItems() as $item) {
                $parent = $item->getQuoteParentItemId();
                if ($parent && !$item->getParentItem()) {
                    $item->setParentItem($this->getItemByQuoteItemId($parent));
                } elseif (!$parent) {
                    $itemsCount++;
                }
            }
            // Set items count
            $this->setTotalItemCount($itemsCount);
        }

        if ($address = $this->getData('ship_to_address')) {
            if (!$address->getId()) {
                $address->save();
            }
            $this->setShipToAddressId($address->getId());
        } else {
            if ($this->hasShipToAddressId() && $this->getShipToAddressId() === null) {
                $this->unsShipToAddressId();
            }
        }

        if ($address = $this->getData('vendor_address')) {
            if (!$address->getId()) {
                $address->save();
            }
            $this->setVendorAddressId($address->getId());
        } else {
            if ($this->hasVendorAddressId() && $this->getVendorAddressId() === null) {
                $this->unsVendorAddressId();
            }
        }

        $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
        return $this;
    }

    /**
     * Save purchase order related objects
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    protected function _afterSave()
    {
        if (null !== $this->_items) {
            $this->_items->save();
        }
        if (null !== $this->_statusHistory) {
            $this->_statusHistory->save();
        }
        foreach ($this->getRelatedObjects() as $object) {
            $object->save();
        }
        return parent::_afterSave();
    }

    public function getStoreGroupName()
    {
        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            return $this->getStoreName(1); // 0 - website name, 1 - store group name, 2 - store name
        }
        return $this->getStore()->getGroup()->getName();
    }

    /**
     * Resets all data in object
     * so after another load it will be complete new object
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = array();
        $this->_items = null;
        $this->_statusHistory = null;
        $this->_relatedObjects = array();
        $this->_purchaseOrderCurrency = null;
        $this->_baseCurrency = null;

        return $this;
    }

    /**
     * Check whether purchase order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return ($this->getStatus() === self::STATUS_CANCELLED);
    }

    /**
     * Protect purchase order delete from not admin scope
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return Mage_Core_Model_Abstract
     */
    public function afterCommitCallback()
    {
        return Mage_Core_Model_Abstract::afterCommitCallback();
    }
}
