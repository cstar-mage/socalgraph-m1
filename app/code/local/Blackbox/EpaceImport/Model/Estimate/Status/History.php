<?php
/**
 * Estimate status history comments
 *
 * @method Blackbox_EpaceImport_Model_Resource_Estimate_Status_History _getResource()
 * @method Blackbox_EpaceImport_Model_Resource_Estimate_Status_History getResource()
 * @method int getParentId()
 * @method Blackbox_EpaceImport_Model_Estimate_Status_History setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method int getIsVisibleOnFront()
 * @method Blackbox_EpaceImport_Model_Estimate_Status_History setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Blackbox_EpaceImport_Model_Estimate_Status_History setComment(string $value)
 * @method string getStatus()
 * @method Blackbox_EpaceImport_Model_Estimate_Status_History setStatus(string $value)
 * @method string getCreatedAt()
 * @method Blackbox_EpaceImport_Model_Estimate_Status_History setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Blackbox_EpaceImport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Model_Estimate_Status_History extends Mage_Sales_Model_Abstract
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Estimate instance
     *
     * @var Blackbox_EpaceImport_Model_Estimate
     */
    protected $_estimate;

    /**
     * Whether setting order again is required (for example when setting non-saved yet order)
     * @deprecated after 1.4, wrong logic of setting order id
     * @var bool
     */
    private $_shouldSetEstimateBeforeSave = false;

    protected $_eventPrefix = 'sales_estimate_status_history';
    protected $_eventObject = 'status_history';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('epacei/estimate_status_history');
    }

    /**
     * Set order object and grab some metadata from it
     *
     * @param   Blackbox_EpaceImport_Model_Estimate $order
     * @return  Blackbox_EpaceImport_Model_Estimate_Status_History
     */
    public function setEstimate(Blackbox_EpaceImport_Model_Estimate $order)
    {
        $this->_estimate = $order;
        $this->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return Blackbox_EpaceImport_Model_Estimate_Status_History
     */
    public function setIsCustomerNotified($flag = null)
    {
        if (is_null($flag)) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }

        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Retrieve order instance
     *
     * @return Blackbox_EpaceImport_Model_Estimate
     */
    public function getEstimate()
    {
        return $this->_estimate;
    }

    /**
     * Retrieve status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if($this->getEstimate()) {
            return $this->getEstimate()->getConfig()->getStatusLabel($this->getStatus());
        }
    }

    /**
     * Get store object
     *
     * @return unknown
     */
    public function getStore()
    {
        if ($this->getEstimate()) {
            return $this->getEstimate()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Set order again if required
     *
     * @return Blackbox_EpaceImport_Model_Estimate_Status_History
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getEstimate()) {
            $this->setParentId($this->getEstimate()->getId());
        }

        return $this;
    }
}
