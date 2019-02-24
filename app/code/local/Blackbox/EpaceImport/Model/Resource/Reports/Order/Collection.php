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
            ->where('job_value IS NOT NULL');

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
                'category' => $categoryExpr,
                'total' => "SUM({$expr})",
                'count' => 'SUM(entity_id)'
            ])
            ->group('category')
            ->having('category IS NOT NULL AND category <> \'\'')
            ->order('total');

        return $this;
    }

    protected function _getSalesProfitExpression()
    {
        $adapter = $this->getConnection();
        return $adapter->getIfNullSql('main_table.job_value', 0) . ' - ' . $adapter->getIfNullSql('main_table.subtotal', 0);
    }

    protected function _getCategoryExpression()
    {
        $types = Mage::helper('epacei')->getJobTypes();
        $types = array_map('strtolower', $types);
        $categoryTypes = [];

        foreach ($this->_getCategories() as $category) {
            $lowerCategory = strtolower($category);
            foreach ($types as $id => $description) {
                if (strpos($description, $lowerCategory) !== false) {
                    $categoryTypes[$category][] = $id;
                }
            }
        }

        $sql = 'case';
        foreach ($categoryTypes as $category => $types) {
            $sql .= ' when job_type IN (' . implode(',', $types) . ') then \'' . $category . '\'';
        }
        $sql .= ' end';

        return new Zend_Db_Expr($sql);
    }

    protected function _getCategories()
    {
        return [
            'Litho',
            'Large Format',
            'Digital Printing',
            'Pre-press',
            'Buyout'
        ];
    }

    protected function _afterLoadData()
    {
        if ($this->categories) {
            foreach ($this->_getCategories() as $category) {
                foreach ($this->_data as $row) {
                    if ($row['category'] == $category) {
                        continue 2;
                    }
                }
                $this->_data[] = [
                    'category' => $category,
                    'total' => 0,
                    'count' => 0
                ];
            }
        }
    }
}