<?php
/** *
 * @method Blackbox_EpaceImport_Model_Resource_Receivable _getResource()
 * @method Blackbox_EpaceImport_Model_Resource_Receivable getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method float getBaseGrandTotal()
 * @method $this setBaseGrandTotal(float $value)
 * @method float getShippingTaxAmount()
 * @method $this setShippingTaxAmount(float $value)
 * @method float getTaxAmount()
 * @method $this setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method $this setBaseTaxAmount(float $value)
 * @method float getStoreToOrderRate()
 * @method $this setStoreToOrderRate(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method $this setBaseShippingTaxAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method $this setBaseDiscountAmount(float $value)
 * @method float getBaseToOrderRate()
 * @method $this setBaseToOrderRate(float $value)
 * @method float getGrandTotal()
 * @method $this setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method $this setShippingAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method $this setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method $this setBaseSubtotalInclTax(float $value)
 * @method float getStoreToBaseRate()
 * @method $this setStoreToBaseRate(float $value)
 * @method float getBaseShippingAmount()
 * @method $this setBaseShippingAmount(float $value)
 * @method float getTotalQty()
 * @method $this setTotalQty(float $value)
 * @method float getBaseToGlobalRate()
 * @method $this setBaseToGlobalRate(float $value)
 * @method float getSubtotal()
 * @method $this setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method $this setBaseSubtotal(float $value)
 * @method float getDiscountAmount()
 * @method $this setDiscountAmount(float $value)
 * @method int getBillingAddressId()
 * @method $this setBillingAddressId(int $value)
 * @method int getIsUsedForRefund()
 * @method $this setIsUsedForRefund(int $value)
 * @method int getOrderId()
 * @method $this setOrderId(int $value)
 * @method int getInvoiceId()
 * @method $this setInvoiceId(int $value)
 * @method int getEmailSent()
 * @method $this setEmailSent(int $value)
 * @method int getCanVoidFlag()
 * @method $this setCanVoidFlag(int $value)
 * @method int getState()
 * @method $this setState(int $value)
 * @method int getShippingAddressId()
 * @method $this setShippingAddressId(int $value)
 * @method string getCybersourceToken()
 * @method $this setCybersourceToken(string $value)
 * @method string getStoreCurrencyCode()
 * @method $this setStoreCurrencyCode(string $value)
 * @method string getTransactionId()
 * @method $this setTransactionId(string $value)
 * @method string getOrderCurrencyCode()
 * @method $this setOrderCurrencyCode(string $value)
 * @method string getBaseCurrencyCode()
 * @method $this setBaseCurrencyCode(string $value)
 * @method string getGlobalCurrencyCode()
 * @method $this setGlobalCurrencyCode(string $value)
 * @method string getIncrementId()
 * @method $this setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method float getHiddenTaxAmount()
 * @method $this setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method $this setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method $this setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmount()
 * @method $this setBaseShippingHiddenTaxAmount(float $value)
 * @method float getShippingInclTax()
 * @method $this setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method $this setBaseShippingInclTax(float $value)
 */
class Blackbox_EpaceImport_Model_Receivable extends Mage_Core_Model_Abstract
{
    /**
     * Invoice states
     */
    const STATE_OPEN        = 0;
    const STATE_CLOSED      = 1;
    const STATE_DISPUTED    = 2;

    protected static $_states;
    
    protected $_rounders = [];

    protected $_order;
    protected $_invoice;

    protected $_eventPrefix = 'epacei_receivable';
    protected $_eventObject = 'receivable';

    /**
     * Initialize invoice resource model
     */
    protected function _construct()
    {
        $this->_init('epacei/receivable');
    }

    /**
     * Load invoice by increment id
     *
     * @param string $incrementId
     * @return Mage_Sales_Model_Order_Invoice
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
     * Declare order for invoice
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Invoice
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the invoice for created for
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order;
    }
    
    public function setInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        $this->setInvoiceId($invoice->getId())
            ->setOrderId($invoice->getOrderId());
        return $this;
    }
    
    public function getInvoice()
    {
        if (!$this->_invoice instanceof Mage_Sales_Model_Order_Invoice) {
            $this->_invoice = Mage::getModel('sales/order_invoice')->load($this->getInvoiceId());
        }
        return $this->_invoice;
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
     * Round price considering delta
     *
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_rounders[$type])) {
                $this->_rounders[$type] = Mage::getModel('core/calculator', $this->getStore());
            }
            $price = $this->_rounders[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * Retrieve receivable states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Open'),
                self::STATE_CLOSED       => Mage::helper('sales')->__('Closed'),
                self::STATE_DISPUTED   => Mage::helper('sales')->__('Disputed'),
            );
        }
        return self::$_states;
    }

    /**
     * Retrieve receivable state name by state identifier
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
     * Reset invoice object
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_order = null;
        $this->_invoice = null;
        return $this;
    }

    /**
     * Before object save manipulations
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        return $this;
    }
}
