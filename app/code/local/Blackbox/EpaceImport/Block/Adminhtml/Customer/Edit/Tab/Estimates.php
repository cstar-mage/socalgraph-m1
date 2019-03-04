<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_Estimates extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_estimates_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        /** @var Blackbox_EpaceImport_Model_Resource_Estimate_Collection $collection */
        $collection = Mage::getResourceModel('epacei/estimate_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('epace_estimate_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('sales_person_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('estimate_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToFilter(['customer_id', 'sales_person_id'], [$customerId = Mage::registry('current_customer')->getId(), $customerId]);

        $collection->getSelect()->columns([
            'customer' => 'CONCAT(COALESCE(customer_firstname, \'\'), COALESCE(customer_lastname, \'\'))',
        ]);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('customer')->__('Estimate #'),
            'width'     => '100',
            'index'     => 'epace_estimate_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('customer')->__('Purchase On'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('customer', [
            'header'    => Mage::helper('customer')->__('Customer'),
            'index'     => 'customer',
        ]);

        $this->addColumn('sales_person', [
            'header'    => Mage::helper('customer')->__('Sales Person'),
            'index'     => 'sales_person_id',
            'type'      => 'options',
            'options'   => Mage::helper('epacei')->getSalesRepsOptions()
        ]);

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Estimate Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'estimate_currency_code',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('customer')->__('Bought From'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view' => true
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/epace_estimate/view', array('estimate_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/estimates', array('_current' => true));
    }

}
