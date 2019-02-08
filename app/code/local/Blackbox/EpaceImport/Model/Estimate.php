<?php
/**
 * Estimate model
 *
 * Supported events:
 *  epacei_estimate_load_after
 *  epacei_estimate_save_before
 *  epacei_estimate_save_after
 *  epacei_estimate_delete_before
 *  epacei_estimate_delete_after
 *
 * @method Blackbox_EpaceImport_Model_Resource_Estimate _getResource()
 * @method Blackbox_EpaceImport_Model_Resource_Estimate getResource()
 * @method string getState()
 * @method string getStatus()
 * @method Blackbox_EpaceImport_Model_Estimate setStatus(string $value)
 * @method string getCouponCode()
 * @method Blackbox_EpaceImport_Model_Estimate setCouponCode(string $value)
 * @method string getProtectCode()
 * @method Blackbox_EpaceImport_Model_Estimate setProtectCode(string $value)
 * @method string getShippingDescription()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingDescription(string $value)
 * @method int getIsVirtual()
 * @method Blackbox_EpaceImport_Model_Estimate setIsVirtual(int $value)
 * @method int getStoreId()
 * @method Blackbox_EpaceImport_Model_Estimate setStoreId(int $value)
 * @method int getCustomerId()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerId(int $value)
 * @method float getBaseDiscountAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseDiscountAmount(float $value)
 * @method float getBaseDiscountCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseDiscountCanceled(float $value)
 * @method float getBaseDiscountInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseDiscountInvoiced(float $value)
 * @method float getBaseDiscountRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseDiscountRefunded(float $value)
 * @method float getBaseGrandTotal()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseGrandTotal(float $value)
 * @method float getBaseShippingAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingAmount(float $value)
 * @method float getBaseShippingCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingCanceled(float $value)
 * @method float getBaseShippingInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingInvoiced(float $value)
 * @method float getBaseShippingRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingRefunded(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingTaxRefunded(float $value)
 * @method float getBaseSubtotal()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseSubtotal(float $value)
 * @method float getBaseSubtotalCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseSubtotalCanceled(float $value)
 * @method float getBaseSubtotalInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseSubtotalInvoiced(float $value)
 * @method float getBaseSubtotalRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseSubtotalRefunded(float $value)
 * @method float getBaseTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTaxAmount(float $value)
 * @method float getBaseTaxCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTaxCanceled(float $value)
 * @method float getBaseTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTaxInvoiced(float $value)
 * @method float getBaseTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTaxRefunded(float $value)
 * @method float getBaseToGlobalRate()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseToGlobalRate(float $value)
 * @method float getBaseToEstimateRate()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseToEstimateRate(float $value)
 * @method float getBaseTotalCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalCanceled(float $value)
 * @method float getBaseTotalInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalInvoiced(float $value)
 * @method float getBaseTotalInvoicedCost()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalInvoicedCost(float $value)
 * @method float getBaseTotalOfflineRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalOfflineRefunded(float $value)
 * @method float getBaseTotalOnlineRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalOnlineRefunded(float $value)
 * @method float getBaseTotalPaid()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalPaid(float $value)
 * @method float getBaseTotalQty()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalQty(float $value)
 * @method float getBaseTotalRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalRefunded(float $value)
 * @method float getDiscountAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setDiscountAmount(float $value)
 * @method float getDiscountCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setDiscountCanceled(float $value)
 * @method float getDiscountInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setDiscountInvoiced(float $value)
 * @method float getDiscountRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setDiscountRefunded(float $value)
 * @method float getGrandTotal()
 * @method Blackbox_EpaceImport_Model_Estimate setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingAmount(float $value)
 * @method float getShippingCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingCanceled(float $value)
 * @method float getShippingInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingInvoiced(float $value)
 * @method float getShippingRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingRefunded(float $value)
 * @method float getShippingTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingTaxAmount(float $value)
 * @method float getShippingTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingTaxRefunded(float $value)
 * @method float getStoreToBaseRate()
 * @method Blackbox_EpaceImport_Model_Estimate setStoreToBaseRate(float $value)
 * @method float getStoreToEstimateRate()
 * @method Blackbox_EpaceImport_Model_Estimate setStoreToEstimateRate(float $value)
 * @method float getSubtotal()
 * @method Blackbox_EpaceImport_Model_Estimate setSubtotal(float $value)
 * @method float getSubtotalCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setSubtotalCanceled(float $value)
 * @method float getSubtotalInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setSubtotalInvoiced(float $value)
 * @method float getSubtotalRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setSubtotalRefunded(float $value)
 * @method float getTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setTaxAmount(float $value)
 * @method float getTaxCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setTaxCanceled(float $value)
 * @method float getTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setTaxInvoiced(float $value)
 * @method float getTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setTaxRefunded(float $value)
 * @method float getTotalCanceled()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalCanceled(float $value)
 * @method float getTotalInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalInvoiced(float $value)
 * @method float getTotalOfflineRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalOfflineRefunded(float $value)
 * @method float getTotalOnlineRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalOnlineRefunded(float $value)
 * @method float getTotalPaid()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalPaid(float $value)
 * @method float getTotalQty()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalQty(float $value)
 * @method float getTotalRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalRefunded(float $value)
 * @method int getCanShipPartially()
 * @method Blackbox_EpaceImport_Model_Estimate setCanShipPartially(int $value)
 * @method int getCanShipPartiallyItem()
 * @method Blackbox_EpaceImport_Model_Estimate setCanShipPartiallyItem(int $value)
 * @method int getCustomerIsGuest()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerIsGuest(int $value)
 * @method int getCustomerNoteNotify()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerNoteNotify(int $value)
 * @method int getBillingAddressId()
 * @method Blackbox_EpaceImport_Model_Estimate setBillingAddressId(int $value)
 * @method int getCustomerGroupId()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerGroupId(int $value)
 * @method int getEditIncrement()
 * @method Blackbox_EpaceImport_Model_Estimate setEditIncrement(int $value)
 * @method int getEmailSent()
 * @method Blackbox_EpaceImport_Model_Estimate setEmailSent(int $value)
 * @method int getForcedDoShipmentWithInvoice()
 * @method Blackbox_EpaceImport_Model_Estimate setForcedDoShipmentWithInvoice(int $value)
 * @method int getGiftMessageId()
 * @method Blackbox_EpaceImport_Model_Estimate setGiftMessageId(int $value)
 * @method int getPaymentAuthorizationExpiration()
 * @method Blackbox_EpaceImport_Model_Estimate setPaymentAuthorizationExpiration(int $value)
 * @method int getPaypalIpnCustomerNotified()
 * @method Blackbox_EpaceImport_Model_Estimate setPaypalIpnCustomerNotified(int $value)
 * @method int getQuoteAddressId()
 * @method Blackbox_EpaceImport_Model_Estimate setQuoteAddressId(int $value)
 * @method int getQuoteId()
 * @method Blackbox_EpaceImport_Model_Estimate setQuoteId(int $value)
 * @method int getShippingAddressId()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingAddressId(int $value)
 * @method float getAdjustmentNegative()
 * @method Blackbox_EpaceImport_Model_Estimate setAdjustmentNegative(float $value)
 * @method float getAdjustmentPositive()
 * @method Blackbox_EpaceImport_Model_Estimate setAdjustmentPositive(float $value)
 * @method float getBaseAdjustmentNegative()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseAdjustmentNegative(float $value)
 * @method float getBaseAdjustmentPositive()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseAdjustmentPositive(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingDiscountAmount(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseSubtotalInclTax(float $value)
 * @method Blackbox_EpaceImport_Model_Estimate setBaseTotalDue(float $value)
 * @method float getPaymentAuthorizationAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setPaymentAuthorizationAmount(float $value)
 * @method float getShippingDiscountAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method Blackbox_EpaceImport_Model_Estimate setSubtotalInclTax(float $value)
 * @method Blackbox_EpaceImport_Model_Estimate setTotalDue(float $value)
 * @method float getWeight()
 * @method Blackbox_EpaceImport_Model_Estimate setWeight(float $value)
 * @method string getCustomerDob()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerDob(string $value)
 * @method string getIncrementId()
 * @method Blackbox_EpaceImport_Model_Estimate setIncrementId(string $value)
 * @method string getAppliedRuleIds()
 * @method Blackbox_EpaceImport_Model_Estimate setAppliedRuleIds(string $value)
 * @method string getBaseCurrencyCode()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseCurrencyCode(string $value)
 * @method string getCustomerEmail()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerEmail(string $value)
 * @method string getCustomerFirstname()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerFirstname(string $value)
 * @method string getCustomerMiddlename()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerMiddlename(string $value)
 * @method string getCustomerLastname()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerLastname(string $value)
 * @method string getCustomerPrefix()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerPrefix(string $value)
 * @method string getCustomerSuffix()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerSuffix(string $value)
 * @method string getCustomerTaxvat()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerTaxvat(string $value)
 * @method string getDiscountDescription()
 * @method Blackbox_EpaceImport_Model_Estimate setDiscountDescription(string $value)
 * @method string getExtCustomerId()
 * @method Blackbox_EpaceImport_Model_Estimate setExtCustomerId(string $value)
 * @method string getExtEstimateId()
 * @method Blackbox_EpaceImport_Model_Estimate setExtEstimateId(string $value)
 * @method string getGlobalCurrencyCode()
 * @method Blackbox_EpaceImport_Model_Estimate setGlobalCurrencyCode(string $value)
 * @method string getHoldBeforeState()
 * @method Blackbox_EpaceImport_Model_Estimate setHoldBeforeState(string $value)
 * @method string getHoldBeforeStatus()
 * @method Blackbox_EpaceImport_Model_Estimate setHoldBeforeStatus(string $value)
 * @method string getEstimateCurrencyCode()
 * @method Blackbox_EpaceImport_Model_Estimate setEstimateCurrencyCode(string $value)
 * @method string getOriginalIncrementId()
 * @method Blackbox_EpaceImport_Model_Estimate setOriginalIncrementId(string $value)
 * @method string getRelationChildId()
 * @method Blackbox_EpaceImport_Model_Estimate setRelationChildId(string $value)
 * @method string getRelationChildRealId()
 * @method Blackbox_EpaceImport_Model_Estimate setRelationChildRealId(string $value)
 * @method string getRelationParentId()
 * @method Blackbox_EpaceImport_Model_Estimate setRelationParentId(string $value)
 * @method string getRelationParentRealId()
 * @method Blackbox_EpaceImport_Model_Estimate setRelationParentRealId(string $value)
 * @method string getRemoteIp()
 * @method Blackbox_EpaceImport_Model_Estimate setRemoteIp(string $value)
 * @method Blackbox_EpaceImport_Model_Estimate setShippingMethod(string $value)
 * @method string getStoreCurrencyCode()
 * @method Blackbox_EpaceImport_Model_Estimate setStoreCurrencyCode(string $value)
 * @method string getStoreName()
 * @method Blackbox_EpaceImport_Model_Estimate setStoreName(string $value)
 * @method string getXForwardedFor()
 * @method Blackbox_EpaceImport_Model_Estimate setXForwardedFor(string $value)
 * @method string getCustomerNote()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerNote(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_EpaceImport_Model_Estimate setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Blackbox_EpaceImport_Model_Estimate setUpdatedAt(string $value)
 * @method int getTotalItemCount()
 * @method Blackbox_EpaceImport_Model_Estimate setTotalItemCount(int $value)
 * @method int getCustomerGender()
 * @method Blackbox_EpaceImport_Model_Estimate setCustomerGender(int $value)
 * @method float getHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingHiddenTaxAmount(float $value)
 * @method float getHiddenTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setHiddenTaxInvoiced(float $value)
 * @method float getBaseHiddenTaxInvoiced()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseHiddenTaxInvoiced(float $value)
 * @method float getHiddenTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setHiddenTaxRefunded(float $value)
 * @method float getBaseHiddenTaxRefunded()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseHiddenTaxRefunded(float $value)
 * @method float getShippingInclTax()
 * @method Blackbox_EpaceImport_Model_Estimate setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method Blackbox_EpaceImport_Model_Estimate setBaseShippingInclTax(float $value)
 *
 * @category    Mage
 * @package     Blackbox_EpaceImport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Model_Estimate extends Mage_Sales_Model_Abstract
{
    /**
     * Identifier for history item
     */
    const ENTITY                                = 'estimate';

    /**
     * Estimate statuses
     */
    const STATUS_OPEN                   = 1;
    const STATUS_CONVERTED_TO_JOB       = 2;
    const STATUS_CUSTOMER_SUBMITTED     = 3;
    const STATUS_NEED_INFO              = 4;
    const STATUS_PRICE_COMPLETE         = 5;
    const STATUS_CANCELLED              = 6;
    const STATUS_RE_QUOTE               = 7;

    /**
     * Estimate flags
     */
    const ACTION_FLAG_CANCEL                    = 'cancel';
    /*
     * Identifier for history item
     */
    const HISTORY_ENTITY_NAME = 'estimate';

    protected $_eventPrefix = 'epacei_estimate';
    protected $_eventObject = 'estimate';

    protected $_items           = null;
    protected $_statusHistory   = null;

    protected $_relatedObjects  = array();
    protected $_estimateCurrency   = null;
    protected $_baseCurrency    = null;

    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = array();

    /**
     * Flag: if after estimate placing we can send new email to the customer.
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
        $this->_init('epacei/estimate');
    }

    /**
     * Clear estimate object data
     *
     * @param string $key data key
     * @return Blackbox_EpaceImport_Model_Estimate
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
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function setActionFlag($action, $flag)
    {
        $this->_actionFlag[$action] = (boolean) $flag;
        return $this;
    }

    /**
     * Load estimate by system increment identifier
     *
     * @param string $incrementId
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load estimate by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return Blackbox_EpaceImport_Model_Estimate
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

    /**
     * Retrieve estimate cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        if (!$this->_canVoidEstimate()) {
            return false;
        }

        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }
        if ($allInvoiced) {
            return false;
        }

        $status = $this->getStatus();
        if ($this->isCanceled() || $status === self::STATUS_CANCELLED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if comment can be added to estimate history
     *
     * @return bool
     */
    public function canComment()
    {
        return true;
    }

    /**
     * Retrieve estimate edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * Retrieve estimate configuration model
     *
     * @return Blackbox_EpaceImport_Model_Estimate_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('epacei/estimate_config');
    }

    protected function _setStatus($status, $comment = '')
    {
        $this->setData('status', $status);
        $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
        return $this;
    }

    /**
     * Retrieve label of estimate status
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
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $history = $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    /*
     * Add a comment to estimate
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Blackbox_EpaceImport_Model_Estimate_Status_History
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
        $history = Mage::getModel('epacei/estimate_status_history')
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
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function setHistoryEntityName( $entityName )
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }

    /**
     * Cancel estimate
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->registerCancellation();

            Mage::dispatchEvent('estimate_cancel_after', array('estimate' => $this));
        }

        return $this;
    }

    /**
     * Prepare estimate totals to cancellation
     * @param string $comment
     * @param bool $graceful
     * @return Blackbox_EpaceImport_Model_Estimate
     * @throws Mage_Core_Exception
     */
    public function registerCancellation($comment = '', $graceful = true)
    {
        if ($this->canCancel() || $this->isPaymentReview()) {
            $cancelStatus = self::STATUS_CANCELLED;

            $this->setSubtotalCanceled($this->getSubtotal() - $this->getSubtotalInvoiced());
            $this->setBaseSubtotalCanceled($this->getBaseSubtotal() - $this->getBaseSubtotalInvoiced());

            $this->setTaxCanceled($this->getTaxAmount() - $this->getTaxInvoiced());
            $this->setBaseTaxCanceled($this->getBaseTaxAmount() - $this->getBaseTaxInvoiced());

            $this->setShippingCanceled($this->getShippingAmount() - $this->getShippingInvoiced());
            $this->setBaseShippingCanceled($this->getBaseShippingAmount() - $this->getBaseShippingInvoiced());

            $this->setDiscountCanceled(abs($this->getDiscountAmount()) - $this->getDiscountInvoiced());
            $this->setBaseDiscountCanceled(abs($this->getBaseDiscountAmount()) - $this->getBaseDiscountInvoiced());

            $this->setTotalCanceled($this->getGrandTotal() - $this->getTotalPaid());
            $this->setBaseTotalCanceled($this->getBaseGrandTotal() - $this->getBaseTotalPaid());

            $this->_setStatus($cancelStatus, $comment);
        } elseif (!$graceful) {
            Mage::throwException(Mage::helper('sales')->__('Estimate does not allow to be canceled.'));
        }
        return $this;
    }

    public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('epacei/estimate_item_collection')
                ->setEstimateFilter($this);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setEstimate($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get random items collection with related children
     *
     * @param int $limit
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Item_Collection
     */
    public function getItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit);
    }

    /**
     * Get random items collection without related children
     *
     * @param int $limit
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Item_Collection
     */
    public function getParentItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit, true);
    }

    /**
     * Get random items collection with or without related children
     *
     * @param int $limit
     * @param bool $nonChildrenOnly
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Item_Collection
     */
    protected function _getItemsRandomCollection($limit, $nonChildrenOnly = false)
    {
        $collection = Mage::getModel('epacei/estimate_item')->getCollection()
            ->setEstimateFilter($this)
            ->setRandomEstimate();

        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }
        $products = array();
        foreach ($collection as $item) {
            $products[] = $item->getProductId();
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addIdFilter($products)
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds())
            /* Price data is added to consider item stock status using price index */
            ->addPriceData()
            ->setPageSize($limit)
            ->load();

        foreach ($collection as $item) {
            $product = $productsCollection->getItemById($item->getProductId());
            if ($product) {
                $item->setProduct($product);
            }
        }

        return $collection;
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

    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    public function getItemByQuoteItemId($quoteItemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$quoteItemId) {
                return $item;
            }
        }
        return null;
    }

    public function addItem(Blackbox_EpaceImport_Model_Estimate_Item $item)
    {
        $item->setEstimate($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Whether the estimate has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ('0' == $item->getIsNominal()) {
                return false;
            }
        }
        return true;
    }

/*********************** STATUSES ***************************/

    /**
     * Enter description here...
     *
     * @return Blackbox_EpaceImport_Model_Resource_Estimate_Status_History_Collection
     */
    public function getStatusHistoryCollection($reload=false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = Mage::getResourceModel('epacei/estimate_status_history_collection')
                ->setEstimateFilter($this)
                ->setEstimate('created_at', 'desc')
                ->setEstimate('entity_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setEstimate($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Return collection of estimate status history items.
     *
     * @return array
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
     * Return collection of visible on frontend estimate status history items.
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
     * Set the estimate status history object and the estimate object to each other
     * Adds the object to the status history collection, which is automatically saved when the estimate is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param Blackbox_EpaceImport_Model_Estimate_Status_History $status
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function addStatusHistory(Blackbox_EpaceImport_Model_Estimate_Status_History $history)
    {
        $history->setEstimate($this);
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
    public function getRealEstimateId()
    {
        $id = $this->getData('real_estimate_id');
        if (is_null($id)) {
            $id = $this->getIncrementId();
        }
        return $id;
    }

    /**
     * Get currency model instance. Will be used currency with which estimate placed
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getEstimateCurrency()
    {
        if (is_null($this->_estimateCurrency)) {
            $this->_estimateCurrency = Mage::getModel('directory/currency')->load($this->getEstimateCurrencyCode());
        }
        return $this->_estimateCurrency;
    }

    /**
     * Get formated price value including estimate currency rate to estimate website currency
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
        return $this->getEstimateCurrency()->formatPrecision($price, $precision, array(), true, $addBrackets);
    }

    /**
     * Retrieve text formated price value includeing estimate rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->getEstimateCurrency()->formatTxt($price);
    }

    /**
     * Retrieve estimate website currency for working with base prices
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

    /**
     * Retrieve estimate website currency for working with base prices
     * @deprecated  please use getBaseCurrency instead.
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getStoreCurrency()
    {
        return $this->getData('store_currency');
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
        return $this->getEstimateCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Retrieve estimate total due value
     *
     * @return float
     */
    public function getTotalDue()
    {
        $total = $this->getGrandTotal()-$this->getTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve estimate total due value
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        $total = $this->getBaseGrandTotal()-$this->getBaseTotalPaid();
        $total = Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    public function getData($key='', $index=null)
    {
        if ($key == 'total_due') {
            return $this->getTotalDue();
        }
        if ($key == 'base_total_due') {
            return $this->getBaseTotalDue();
        }
        return parent::getData($key, $index);
    }

    /**
     * Retrieve array of related objects
     *
     * Used for estimate saving
     *
     * @return array
     */
    public function getRelatedObjects()
    {
        return $this->_relatedObjects;
    }

    /**
     * Retrieve customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = Mage::helper('customer')->getFullCustomerName($this);
        } else {
            $customerName = Mage::helper('sales')->__('Guest');
        }
        return $customerName;
    }

    /**
     * Add New object to related array
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Blackbox_EpaceImport_Model_Estimate
     */
    public function addRelatedObject(Mage_Core_Model_Abstract $object)
    {
        $this->_relatedObjects[] = $object;
        return $this;
    }

    /**
     * Get formated estimate created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_checkState();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = array($store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName());
            $this->setStoreName(implode("\n", $name));
        }

        if (!$this->getIncrementId()) {
            $incrementId = Mage::getSingleton('eav/config')
                ->getEntityType('estimate')
                ->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }

        /**
         * Process items dependency for new estimate
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
        if ($this->getCustomer()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }

        if ($this->hasBillingAddressId() && $this->getBillingAddressId() === null) {
            $this->unsBillingAddressId();
        }

        if ($this->hasShippingAddressId() && $this->getShippingAddressId() === null) {
            $this->unsShippingAddressId();
        }

        $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
        return $this;
    }

    /**
     * Check estimate state before saving
     */
    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()) {
            if (0 == $this->getBaseGrandTotal() || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, true, '', $userNotification);
                }
            }
            /**
             * Estimate can be closed just in case when we have refunded amount.
             * In case of "0" grand total estimate checking ForcedCanCreditmemo flag
             */
            elseif (floatval($this->getTotalRefunded()) || (!$this->getTotalRefunded()
                && $this->hasForcedCanCreditmemo())
            ) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                }
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, true, '', $userNotification);
        }
        return $this;
    }

    /**
     * Save estimate related objects
     *
     * @return Blackbox_EpaceImport_Model_Estimate
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
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = array();
        $this->_items = null;
        $this->_statusHistory = null;
        $this->_relatedObjects = array();
        $this->_estimateCurrency = null;
        $this->_baseCurrency = null;

        return $this;
    }

    public function getIsNotVirtual()
    {
        return !$this->getIsVirtual();
    }

    /**
     * Check whether estimate is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return ($this->getStatus() === self::STATUS_CANCELLED);
    }

    /**
     * Protect estimate delete from not admin scope
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}
