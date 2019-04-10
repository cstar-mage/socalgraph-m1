<?php

/**
 * Purchase order address model
 *
 * @method Blackbox_EpaceImport_Model_Resource_Address _getResource()
 * @method Blackbox_EpaceImport_Model_Resource_Address getResource()
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getCustomerAddressId()
 * @method $this setCustomerAddressId(int $value)
 * @method int getQuoteAddressId()
 * @method $this setQuoteAddressId(int $value)
 * @method $this setRegionId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getFax()
 * @method $this setFax(string $value)
 * @method $this setRegion(string $value)
 * @method string getPostcode()
 * @method $this setPostcode(string $value)
 * @method string getLastname()
 * @method $this setLastname(string $value)
 * @method string getCity()
 * @method $this setCity(string $value)
 * @method string getEmail()
 * @method $this setEmail(string $value)
 * @method string getTelephone()
 * @method $this setTelephone(string $value)
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getFirstname()
 * @method $this setFirstname(string $value)
 * @method string getAddressType()
 * @method $this setAddressType(string $value)
 * @method string getPrefix()
 * @method $this setPrefix(string $value)
 * @method string getMiddlename()
 * @method $this setMiddlename(string $value)
 * @method string getSuffix()
 * @method $this setSuffix(string $value)
 * @method string getCompany()
 * @method $this setCompany(string $value)
 */
class Blackbox_EpaceImport_Model_Address extends Mage_Customer_Model_Address_Abstract
{
    protected $_purchaseOrder;
    
    protected $_eventPrefix = 'epacei_address';
    protected $_eventObject = 'address';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('epacei/address');
    }

    /**
     * Set order
     *
     * @return $this
     */
    public function setPurchaseOrder(Blackbox_EpaceImport_Model_PurchaseOrder $order)
    {
        $this->_purchaseOrder = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return Blackbox_EpaceImport_Model_PurchaseOrder
     */
    public function getPurchaseOrder()
    {
        if (!$this->_purchaseOrder) {
            $this->_purchaseOrder = Mage::getModel('epacei/purchaseOrder')->load($this->getParentId());
        }
        return $this->_purchaseOrder;
    }

    /**
     * Before object save manipulations
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getPurchaseOrder()) {
            $this->setParentId($this->getPurchaseOrder()->getId());
        }

        // Init customer address id if customer address is assigned
        if ($this->getCustomerAddress()) {
            $this->setCustomerAddressId($this->getCustomerAddress()->getId());
        }

        return $this;
    }
}
