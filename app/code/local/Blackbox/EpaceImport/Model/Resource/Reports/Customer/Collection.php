<?php

class Blackbox_EpaceImport_Model_Resource_Reports_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    public function calculateSalesRepsMonthlySales($limit = null)
    {
        $this->_addAttributeJoin('firstname', 'left')
            ->_addAttributeJoin('middlename', 'left')
            ->_addAttributeJoin('lastname', 'left');

        $customerFieldSql = $this->getConnection()->getConcatSql([
            'at_firstname.value',
            'at_middlename.value',
            'at_lastname.value'
        ], ' ');

        /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
        $orderCollection = Mage::getResourceModel('sales/order_collection');
        $select = $orderCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns([
                'sales_person_id',
                'orders_count' => 'count(entity_id)',
                'grand_total' => 'sum(base_grand_total)',
            ])
            ->where('created_at > ?', date('y-m-d', strtotime('-1 month')))
            ->group('sales_person_id')
            ->order('grand_total DESC');
        if (!$limit) {
            $select->limit($limit);
        }

        $this->getSelect()->join(['orders' => $select], 'sales_person_id = e.entity_id', ['orders_count', 'grand_total'])->columns([
            'customer' => $customerFieldSql,
        ]);

        return $this;
    }

    public function calculateCustomerSales($limit = null)
    {
        $this->_addAttributeJoin('firstname', 'left')
            ->_addAttributeJoin('middlename', 'left')
            ->_addAttributeJoin('lastname', 'left');

        $customerFieldSql = $this->getConnection()->getConcatSql([
            'at_firstname.value',
            'at_middlename.value',
            'at_lastname.value'
        ], ' ');

        $estimatesSelect = $this->getConnection()->select()->from($this->getTable('epacei/estimate'), ['customer_id', 'estimates' => 'base_grand_total']);
        $ordersSelect = $this->getConnection()->select()->from($this->getTable('sales/order'), ['customer_id', 'orders' => 'base_grand_total', 'billed' => 'total_paid']);

        $this->getSelect()->joinLeft(['estimates' => $estimatesSelect], 'estimates.customer_id = e.entity_id', ['estimates'])
            ->joinLeft(['orders' => $ordersSelect], 'orders.customer_id = e.entity_id', ['orders', 'billed'])
            ->group('e.entity_id')
            ->columns([
                'estimates' => 'sum(estimates)',
                'orders' => 'sum(orders)',
                'billed' => 'sum(billed)',
                'customer' => $customerFieldSql
            ])
            ->order('billed DESC');

        return $this;
    }
}