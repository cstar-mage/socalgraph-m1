<?php
/**
 * PurchaseOrder history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_View_Info extends Blackbox_EpaceImport_Block_Adminhtml_PurchaseOrder_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setPurchaseOrder($this->getParentBlock()->getPurchaseOrder());

        foreach ($this->getParentBlock()->getPurchaseOrderInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        parent::_beforeToHtml();
    }

    public function getPurchaseOrderStoreName()
    {
        if ($this->getPurchaseOrder()) {
            $storeId = $this->getPurchaseOrder()->getStoreId();
            if (is_null($storeId)) {
                $deleted = Mage::helper('adminhtml')->__(' [deleted]');
                return nl2br($this->getPurchaseOrder()->getStoreName()) . $deleted;
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
        if ($this->getPurchaseOrder()) {
            return Mage::getModel('customer/group')->load((int)$this->getPurchaseOrder()->getCustomerGroupId())->getCode();
        }
        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getPurchaseOrder()->getCustomerIsGuest() || !$this->getPurchaseOrder()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getPurchaseOrder()->getCustomerId()));
    }

    public function getViewUrl($purchaseOrderId)
    {
        return $this->getUrl('*/epacei_purchase_order/view', array('purchase_order_id'=>$purchaseOrderId));
    }

    /**
     * Find sort purchase order for account data
     * Sort PurchaseOrder used as array key
     *
     * @param array $data
     * @param int $sortPurchaseOrder
     * @return int
     */
    protected function _prepareAccountDataSortPurchaseOrder(array $data, $sortPurchaseOrder)
    {
        if (isset($data[$sortPurchaseOrder])) {
            return $this->_prepareAccountDataSortPurchaseOrder($data, $sortPurchaseOrder + 1);
        }
        return $sortPurchaseOrder;
    }

    public function getContactData()
    {
        $fields = [
            'street' => 'Street',
            'city' => 'City',
            'region' => 'Region',
            'postcode' => 'Postcode',
            'country_id' => 'Country',
            'telephone' => 'Telephone',
            'po_number' => 'PO Number'
        ];

        $data = [];

        $po = $this->getPurchaseOrder();
        foreach ($fields as $field => $label) {
            $method = 'get' . uc_words($field, '');
            $value = $po->$method();
            if (!$value) {
                continue;
            }

            $data[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        return $data;
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
            $purchaseOrderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $purchaseOrderValue = $this->getPurchaseOrder()->getData($purchaseOrderKey);
            if ($purchaseOrderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $purchaseOrderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortPurchaseOrder  = $attribute->getSortPurchaseOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortPurchaseOrder  = $this->_prepareAccountDataSortPurchaseOrder($accountData, $sortPurchaseOrder);
                $accountData[$sortPurchaseOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Get link to edit purchase order address page
     *
     * @param Blackbox_EpaceImport_Model_PurchaseOrder_Address $address
     * @param string $label
     * @return string
     */
    public function getAddressEditLink($address, $label='')
    {
        if (empty($label)) {
            $label = $this->__('Edit');
        }
        $url = $this->getUrl('*/epace_purchase_order/address', array('address_id'=>$address->getId()));
        return '<a href="'.$url.'">' . $label . '</a>';
    }

    /**
     * Whether Customer IP address should be displayed on epacei documents
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !Mage::getStoreConfigFlag('epacei/general/hide_customer_ip', $this->getPurchaseOrder()->getStoreId());
    }
}
