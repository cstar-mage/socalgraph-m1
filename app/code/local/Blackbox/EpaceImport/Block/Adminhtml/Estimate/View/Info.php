<?php
/**
 * Estimate history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Estimate_View_Info extends Blackbox_EpaceImport_Block_Adminhtml_Estimate_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setEstimate($this->getParentBlock()->getEstimate());

        foreach ($this->getParentBlock()->getEstimateInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        parent::_beforeToHtml();
    }

    public function getEstimateStoreName()
    {
        if ($this->getEstimate()) {
            $storeId = $this->getEstimate()->getStoreId();
            if (is_null($storeId)) {
                $deleted = Mage::helper('adminhtml')->__(' [deleted]');
                return nl2br($this->getEstimate()->getStoreName()) . $deleted;
            }
            $store = Mage::app()->getStore($storeId);
            $name = array(
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName()
            );
            return implode('<br/>', array_map(array($this, 'escapeHtml'), $name));
        }
        return null;
    }

    public function getCustomerGroupName()
    {
        if ($this->getEstimate()) {
            return Mage::getModel('customer/group')->load((int)$this->getEstimate()->getCustomerGroupId())->getCode();
        }
        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getEstimate()->getCustomerIsGuest() || !$this->getEstimate()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getEstimate()->getCustomerId()));
    }

    public function getViewUrl($estimateId)
    {
        return $this->getUrl('*/epacei_estimate/view', array('estimate_id'=>$estimateId));
    }

    /**
     * Find sort estimate for account data
     * Sort Estimate used as array key
     *
     * @param array $data
     * @param int $sortEstimate
     * @return int
     */
    protected function _prepareAccountDataSortEstimate(array $data, $sortEstimate)
    {
        if (isset($data[$sortEstimate])) {
            return $this->_prepareAccountDataSortEstimate($data, $sortEstimate + 1);
        }
        return $sortEstimate;
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = array();

        /* @var $config Mage_Eav_Model_Config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            $estimateKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $estimateValue = $this->getEstimate()->getData($estimateKey);
            if ($estimateValue != '') {
                $customer->setData($attribute->getAttributeCode(), $estimateValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortEstimate  = $attribute->getSortEstimate() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortEstimate  = $this->_prepareAccountDataSortEstimate($accountData, $sortEstimate);
                $accountData[$sortEstimate] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Get link to edit estimate address page
     *
     * @param Blackbox_EpaceImport_Model_Estimate_Address $address
     * @param string $label
     * @return string
     */
    public function getAddressEditLink($address, $label='')
    {
        if (empty($label)) {
            $label = $this->__('Edit');
        }
        $url = $this->getUrl('*/epace_estimate/address', array('address_id'=>$address->getId()));
        return '<a href="'.$url.'">' . $label . '</a>';
    }

    /**
     * Whether Customer IP address should be displayed on epacei documents
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !Mage::getStoreConfigFlag('epacei/general/hide_customer_ip', $this->getEstimate()->getStoreId());
    }
}
