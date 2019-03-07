<?php

class Blackbox_EpaceImport_Model_resource_Reports_Order_Collection extends Mage_Reports_Model_Resource_Order_Collection
{
    protected $categories = false;

    public function calculateMonthlySales($isFilter = 0)
    {
        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $adapter = $this->getConnection();

        if (Mage::getStoreConfig('sales/dashboard/use_aggregated_data')) {
            $this->setMainTable('sales/order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $adapter->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                0);
            $this->getSelect()->columns(array(
                'monthly' => 'SUM(main_table.total_revenue_amount)',
                'average'  => $averageExpr
            ));

            if (!$isFilter) {
                $this->addFieldToFilter('store_id',
                    array('eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId())
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses)
                ->where('main_table.period >= ?', date('Y-m-d', strtotime('-1 month')));
        } else {
            $this->setMainTable('sales/order');
            $this->removeAllFieldsFromSelect();

            $expr = $this->_getSalesAmountExpression();

            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }

            $this->getSelect()
                ->columns(array(
                    'monthly' => "SUM({$expr})",
                    'average'  => "AVG({$expr})"
                ))
                ->where('main_table.status NOT IN(?)', $statuses)
                ->where('main_table.state NOT IN(?)', array(
                        Mage_Sales_Model_Order::STATE_NEW,
                        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
                )
                ->where('main_table.created_at > ?', date('Y-m-d', strtotime('-1 month')));
        }
        return $this;
    }

    public function calculateProfitPerJob($isFilter = 0)
    {
        $this->setMainTable('sales/order');
        $this->removeAllFieldsFromSelect();

        $expr = $this->_getSalesProfitExpression();

        if ($isFilter == 0) {
            $expr = '(' . $expr . ') * main_table.base_to_global_rate';
        }

        $this->getSelect()
            ->columns(array(
                'lifetime' => "SUM({$expr})",
                'average'  => "AVG({$expr})"
            ))
            ->where('original_quoted_price IS NOT NULL');

        return $this;
    }

    public function calculateCategoryBreakdown($isFilter = 0)
    {
        $this->categories = true;

        $this->setMainTable('sales/order');
        $this->removeAllFieldsFromSelect();

        $expr = $this->_getSalesAmountExpression();

        if ($isFilter == 0) {
            $expr = '(' . $expr . ') * main_table.base_to_global_rate';
        }

        $categoryExpr = $this->_getCategoryExpression();

        $this->getSelect()
            ->columns([
                'job_type',
                'total' => "SUM({$expr})",
                'count' => 'SUM(entity_id)'
            ])
            ->where('job_type is not null')
            ->group('job_type')
            ->order('total DESC');

        return $this;
    }

    protected function _getSalesProfitExpression()
    {
        $adapter = $this->getConnection();
        return $adapter->getIfNullSql('main_table.original_quoted_price', 0) . ' - ' . $adapter->getIfNullSql('main_table.subtotal', 0);
    }

    protected function _getCategoryExpression()
    {
        $types = Mage::helper('epacei')->getJobTypes();

        $sql = 'case';
        foreach ($types as $id => $name) {
            $sql .= ' when job_type = ' . $id . ' then \'' . $name . '\'';
        }
        $sql .= ' end';

        return new Zend_Db_Expr($sql);
    }
}