<?php
/**
 * Receivable history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_Receivable_View_Info extends Blackbox_EpaceImport_Block_Adminhtml_Receivable_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setReceivable($this->getParentBlock()->getReceivable());

        foreach ($this->getParentBlock()->getReceivableInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        parent::_beforeToHtml();
    }

    public function getReceivableStoreName()
    {
        if ($this->getReceivable()) {
            $storeId = $this->getReceivable()->getStoreId();
            if (is_null($storeId)) {
                $deleted = Mage::helper('adminhtml')->__(' [deleted]');
                return nl2br($this->getReceivable()->getStoreName()) . $deleted;
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
        if ($this->getReceivable()) {
            return Mage::getModel('customer/group')->load((int)$this->getReceivable()->getCustomerGroupId())->getCode();
        }
        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getReceivable()->getCustomerIsGuest() || !$this->getReceivable()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getReceivable()->getCustomerId()));
    }

    public function getViewUrl($receivableId)
    {
        return $this->getUrl('*/epacei_receivable/view', array('receivable_id'=>$receivableId));
    }

    /**
     * Find sort receivable for account data
     * Sort Receivable used as array key
     *
     * @param array $data
     * @param int $sortReceivable
     * @return int
     */
    protected function _prepareAccountDataSortReceivable(array $data, $sortReceivable)
    {
        if (isset($data[$sortReceivable])) {
            return $this->_prepareAccountDataSortReceivable($data, $sortReceivable + 1);
        }
        return $sortReceivable;
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
            $receivableKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $receivableValue = $this->getReceivable()->getData($receivableKey);
            if ($receivableValue != '') {
                $customer->setData($attribute->getAttributeCode(), $receivableValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortReceivable  = $attribute->getSortReceivable() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortReceivable  = $this->_prepareAccountDataSortReceivable($accountData, $sortReceivable);
                $accountData[$sortReceivable] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Get link to edit receivable address page
     *
     * @param Blackbox_EpaceImport_Model_Receivable_Address $address
     * @param string $label
     * @return string
     */
    public function getAddressEditLink($address, $label='')
    {
        if (empty($label)) {
            $label = $this->__('Edit');
        }
        $url = $this->getUrl('*/epace_receivable/address', array('address_id'=>$address->getId()));
        return '<a href="'.$url.'">' . $label . '</a>';
    }

    /**
     * Whether Customer IP address should be displayed on epacei documents
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !Mage::getStoreConfigFlag('epacei/general/hide_customer_ip', $this->getReceivable()->getStoreId());
    }
}
