<?php

class Blackbox_EpaceImport_Block_Adminhtml_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('shipping_name')
            ->addFieldToFilter(['customer_id', 'sales_person_id'], [$customerId = Mage::registry('current_customer')->getId(), $customerId])
            ->setIsCustomerMode(true);
        $collection->getSelect()->join(
            ['o' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('sales/order'), ['entity_id', 'sales_person_id'])],
            'main_table.entity_id = o.entity_id',
            'sales_person_id'
        );

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}
