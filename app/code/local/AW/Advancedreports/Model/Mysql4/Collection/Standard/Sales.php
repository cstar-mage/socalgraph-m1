<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedreports
 * @version    2.8.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedreports_Model_Mysql4_Collection_Standard_Sales
    extends AW_Advancedreports_Model_Mysql4_Collection_Abstract
    implements AW_Advancedreports_Model_Mysql4_Collection_Profit_Interface
{
    /**
     * Reinitialize select
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Product
     */
    public function reInitSelect()
    {
        $orderTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order');

        $this->getSelect()->reset();
        $this->getSelect()->from(array('main_table' => $orderTable), array());
        return $this;
    }

    /**
     * Add order columns
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Standard_Sales
     */
    public function addSumColumns($isAllStores = false)
    {
        $currencyRate = $this->_getCurrencyRate($isAllStores, "main_table");

        $this->getSelect()->columns(
            array(
                'orders'      => "COUNT(main_table.entity_id)", # Just because it's unique
                'subtotal'    => "SUM(main_table.base_subtotal * $currencyRate)",
                'tax'         => "SUM(main_table.base_tax_amount * $currencyRate)",
                'discount'    => "SUM(main_table.base_discount_amount * $currencyRate)",
                'shipping'    => "SUM(main_table.base_shipping_amount * $currencyRate)",
                'total'       => "(SUM(main_table.base_subtotal * $currencyRate)
                                  + SUM(main_table.base_tax_amount * $currencyRate)
                                  + SUM(main_table.base_discount_amount * $currencyRate)
                                  + SUM(main_table.base_shipping_amount * $currencyRate))",
                'invoiced'    => "SUM(main_table.base_total_invoiced * $currencyRate)",
                'refunded'    => "SUM(main_table.base_total_refunded * $currencyRate)",
                'items_count' => "SUM(main_table.total_qty_ordered)",
                'int_1'       => "ROUND(1)",
            )
        );

        $this->addPointsAndCreditInfo($currencyRate);

        return $this;
    }

    private function addPointsAndCreditInfo($currencyRate)
    {
        $this->addAwStorecreditInfo();
        $this->addAwPointsInfo();

        $this->getSelect()
            ->columns(array(
                    'points_credit' => $this->getPointsCreditExpression($currencyRate),
                )
            );
    }

    private function addAwStorecreditInfo()
    {
        if (Mage::helper('advancedreports')->isAwStorecreditInstalled()) {
            $storecreditTable = Mage::helper('advancedreports/sql')->getTable('aw_storecredit/quote_storecredit');
            $this->getSelect()
                ->joinLeft(
                    array('aw_storecredit' => $storecreditTable),
                    "main_table.quote_id = aw_storecredit.quote_entity_id",
                    "aw_storecredit.base_storecredit_amount"
                );
        }
    }

    private function addAwPointsInfo()
    {
        if (Mage::helper('advancedreports')->isAwPointsInstalled()) {
            $pointsTable =  Mage::helper('advancedreports/sql')->getTable('points/transaction_orderspend');
            $this->getSelect()
                ->joinLeft(
                    array('aw_points' => $pointsTable),
                    "main_table.increment_id = aw_points.order_increment_id",
                    "aw_points.base_points_to_money"
                );
        }
    }

    private function getPointsCreditExpression($currencyRate)
    {
        $terms = array();
        if (Mage::helper('advancedreports')->isEEVersion()) {
            $terms[] = "(main_table.base_customer_balance_amount * $currencyRate)";
            $terms[] = "(main_table.base_reward_currency_amount * $currencyRate)";
        }
        if (Mage::helper('advancedreports')->isAwStorecreditInstalled()) {
            $terms[] = "IFNULL((aw_storecredit.base_storecredit_amount * $currencyRate), 0)";
        }
        if (Mage::helper('advancedreports')->isAwPointsInstalled()) {
            $terms[] = "IFNULL((ABS(aw_points.base_points_to_money) * $currencyRate), 0)";
        }

        $sum = "NULL";
        if (count($terms)) {
            $sum = implode(" + ", $terms);
        }
        $expression = "SUM($sum)";
        return $expression;
    }

    public function addItems()
    {
        $itemTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_item');

        $this->getSelect()
            ->join(
                array('item' => $itemTable),
                "main_table.entity_id = item.order_id AND item.parent_item_id IS NULL",
                array(
                    'items_count' => 'SUM(item.qty_ordered)',
                )
            );
        return $this;
    }

    /**
     * Group by Entity_Id
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Standard_Sales
     */
    public function addGroupByEntityId()
    {
        $this->getSelect()->group("main_table.entity_id");
        return $this;
    }

    /**
     * Group by INT_1
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Standard_Sales
     */
    public function addGroupByIntOne()
    {
        $this->getSelect()->group('int_1');
        return $this;
    }


    protected function _getCurrencyRate($isAllStores, $orderTableAlias)
    {
        if ($isAllStores) {
            $currencyRate = "{$orderTableAlias}.store_to_base_rate";
        } else {
            $currencyRate = new Zend_Db_Expr("'1'");
        }
        return $currencyRate;
    }

    /**
     * Set up profit columns for collection
     * ATTENTION: use this method only for collections with joined 'item' => 'sales_flat_order_item' table
     *
     * @param $dateFrom
     * @param $dateTo
     * @param bool $isAllStores
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Abstract
     */
    public function addProfitInfo($dateFrom, $dateTo, $isAllStores = false)
    {
        $currencyRate = $this->_getCurrencyRate($isAllStores, "order");

        $itemTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_item');
        $orderTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order');

        $filterField = Mage::helper('advancedreports')->confOrderDateFilter();
        $needToIncludeTaxesInProfit = Mage::helper('advancedreports')->isNeedToIncludeTaxesInProfit();

        $orderStatusList = explode(",", Mage::helper('advancedreports')->confProcessOrders());
        $orderStatusList = implode("','", $orderStatusList);

        $groupBy = 'item.product_id';
        $additionalJoinCondition = '1=1';
        $skuTypeCondition = '1=1';
        $storeIdsCondition = '1=1';
        $itemProductIdField = "IFNULL(item.product_id, item2.product_id)";
        $typeList = "'configurable', 'bundle'";

        if ($storeIds = $this->getStoreIds()) {
            $storeIdsCondition = "(order.store_id in ('" . implode("','", $storeIds) . "'))";
        }

        $itemProductIdField = "IFNULL(item2.product_id, item.product_id)";
        $groupBy = 'item.order_id';

        $totalCostExpression = $this->getTotalCostExpression($currencyRate);
        $totalDiscountExpression = $this->getTotalDiscountExpression($currencyRate);
        $totalTaxExpression = $this->getTotalTaxExpression($currencyRate);

        $this->getSelect()
            ->joinLeft(
                new Zend_Db_Expr(
                    "(
                        SELECT 
                            {$totalCostExpression} as total_cost,
                            (
                                SUM(IFNULL(item2.base_row_total, item.base_row_total) * $currencyRate)" .
                                ($needToIncludeTaxesInProfit ? "+ {$totalTaxExpression}" : "" ) .
                                "- {$totalDiscountExpression}
                                - {$totalCostExpression}
                            ) AS `total_profit`,
                            (
                                100
                                * (
                                    SUM(IFNULL(item2.base_row_total, item.base_row_total) * $currencyRate)" .
                                    ($needToIncludeTaxesInProfit ? "+ {$totalTaxExpression}" : "" ) .
                                    "- {$totalDiscountExpression}
                                    - {$totalCostExpression}
                                )
                                / (
                                    SUM(IFNULL(item2.base_row_total, item.base_row_total) * $currencyRate)" .
                                    ($needToIncludeTaxesInProfit ? "+ {$totalTaxExpression}" : "" ) .
                                    "- {$totalDiscountExpression}
                                )
                            ) AS `total_margin`,
                            (
                                SUM(IFNULL(item2.base_row_total, item.base_row_total) * $currencyRate)
                                - {$totalDiscountExpression}
                            ) AS `total_revenue_excl_tax`,
                            (
                                SUM(IFNULL(item2.base_row_total, item.base_row_total) * $currencyRate)
                                + {$totalTaxExpression}
                                - {$totalDiscountExpression}
                            ) AS `total_revenue`,
                            {$itemProductIdField} AS `item_product_id`,
                            item.order_id AS `item_order_id`,
                            item.parent_item_id AS `parent_item_id`
                        FROM 
                            {$itemTable} AS `item`
                            INNER JOIN 
                                {$orderTable} AS `order` 
                                ON order.entity_id = item.order_id
                            LEFT JOIN 
                                {$itemTable} AS `item2` 
                                ON order.entity_id = item2.order_id 
                                AND item2.item_id = item.parent_item_id 
                                AND item2.product_type IN ({$typeList})
                            LEFT JOIN 
                                (
                                    SELECT  parent_item_id, 
                                            SUM(bundle_item.base_cost) AS base_cost, 
                                            SUM(base_price) AS base_price, 
                                            SUM(bundle_item.base_discount_amount) AS base_discount_amount
                                    FROM {$itemTable} AS bundle_item
                                        INNER JOIN 
                                            {$orderTable} AS `order` 
                                            ON order.entity_id = bundle_item.order_id
                                    WHERE 
                                        (
                                            order.{$filterField} >= '{$dateFrom}' 
                                            AND order.{$filterField} <= '{$dateTo}'
                                        ) 
                                        AND 
                                        (
                                            order.status IN ('{$orderStatusList}')
                                        )
                                    GROUP BY parent_item_id
                                ) 
                                AS `bundle_item_cost` 
                                ON item.item_id = bundle_item_cost.parent_item_id
                        WHERE 
                            (
                                (
                                    item.product_type <> 'configurable' 
                                    AND item2.product_type <> 'bundle'
                                ) 
                                OR 
                                (
                                    item.product_type <> 'configurable' 
                                    AND item2.product_type IS NULL
                                )
                            )
                            AND {$skuTypeCondition} 
                            AND 
                            (
                                order.{$filterField} >= '{$dateFrom}' 
                                AND order.{$filterField} <= '{$dateTo}'
                            ) 
                            AND 
                            (
                                order.status IN ('{$orderStatusList}')
                            )
                            AND {$storeIdsCondition}
                        GROUP BY {$groupBy}
                    )"
                ),
                "main_table.entity_id = t.item_order_id 
                AND {$additionalJoinCondition}",
                array()
            );

        $currencyRate = $this->_getCurrencyRate($isAllStores, "main_table");

        $this->getSelect()
            ->columns(
                array(
                    'total_cost'             => "COALESCE(SUM(t.total_cost), 0)",
                    'total_profit'           => "COALESCE(SUM(t.total_profit), 0)",
                    'total_margin'           => "COALESCE(
                                                    (   
                                                        100 * 
                                                        (
                                                            SUM(" . (   $needToIncludeTaxesInProfit ?
                                                                        "t.total_revenue" :
                                                                        "t.total_revenue_excl_tax" ) .
                                                            ") 
                                                            - SUM(t.total_cost)
                                                        )
                                                        / 
                                                        SUM(" . (   $needToIncludeTaxesInProfit ?
                                                                    "t.total_revenue" :
                                                                    "t.total_revenue_excl_tax" ) .
                                                        ")
                                                    ), 
                                                    0 )",
                    'total_revenue_excl_tax' => "COALESCE(SUM(t.total_revenue_excl_tax), 0)",
                    'total_revenue'          => "COALESCE(SUM(t.total_revenue), 0)",
                )
            );

        return $this;
    }

    /**
     * Retrieve expression for total cost calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalCostExpression($currencyRate)
    {
        return "
        (
            SUM(
                IFNULL(
                    COALESCE(
                        IF(bundle_item_cost.base_price > 0, bundle_item_cost.base_cost, NULL), 
                        item.base_cost, 
                        item2.base_cost
                    ) * $currencyRate, 
                    0
                ) 
                * IFNULL(item.qty_ordered, item2.qty_ordered)
            )
        )";
    }

    /**
     * Retrieve expression for total discount calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalDiscountExpression($currencyRate)
    {
        return "
        (
            SUM(
                COALESCE(
                    IF(bundle_item_cost.base_price > 0, bundle_item_cost.base_discount_amount, NULL), 
                    item2.base_discount_amount, 
                    item.base_discount_amount
                ) * $currencyRate
            )
        )";
    }

    /**
     * Retrieve expression for total tax calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalTaxExpression($currencyRate)
    {
        return "
        ( 
            SUM(IFNULL(item2.base_tax_amount, item.base_tax_amount) * $currencyRate)
            + SUM(COALESCE(item2.base_hidden_tax_amount, item.base_hidden_tax_amount, 0) * $currencyRate)
            + SUM(IFNULL(item2.base_weee_tax_applied_amount, item.base_weee_tax_applied_amount) * $currencyRate) 
        )";
    }
}