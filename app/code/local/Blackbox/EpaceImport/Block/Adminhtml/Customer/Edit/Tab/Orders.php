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
            ->addFieldToFilter(['customer_id', 'sales_person_id', 'csr_id'], [$customerId = Mage::registry('current_customer')->getId(), $customerId, $customerId])
            ->setIsCustomerMode(true);
        $collection->getSelect()->join(
            ['o' => $collection->getResource()->getReadConnection()->select()->from($collection->getResource()->getTable('sales/order'), ['entity_id', 'sales_person_id', 'csr_id', 'original_quoted_price', 'subtotal', 'estimate_id'])],
            'main_table.entity_id = o.entity_id',
            ['sales_person_id', 'csr_id', 'estimate_price' => 'original_quoted_price', 'subtotal', 'estimate_id']
        );
        $collection->setIsCustomerMode(true);

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumnAfter('estimate_price', [
            'header'    => Mage::helper('customer')->__('Estimate Price'),
            'index'     => 'estimate_price',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ], 'shipping_name');
        $this->addColumnAfter('subtotal', [
            'header'    => Mage::helper('customer')->__('Sold For'),
            'index'     => 'subtotal',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ], 'estimate_price');
        parent::_prepareColumns();
        $this->_columns['billing_name']->setHeader(Mage::helper('customer')->__('Client'));
        $this->_columns['shipping_name']->setHeader(Mage::helper('customer')->__('Customer Name'));
        return $this;
    }
}
