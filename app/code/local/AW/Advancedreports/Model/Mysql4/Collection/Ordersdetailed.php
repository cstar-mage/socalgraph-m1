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


class AW_Advancedreports_Model_Mysql4_Collection_Ordersdetailed
    extends AW_Advancedreports_Model_Mysql4_Collection_Abstract
    implements AW_Advancedreports_Model_Mysql4_Collection_Profit_Interface
{
    /**
     * Reinitialize select
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Stockvssold
     */
    public function reInitSelect($isAllStores = false)
    {
        if ($isAllStores) {
            $currencyRate = "main_table.store_to_base_rate";
        } else {
            $currencyRate = new Zend_Db_Expr("1");
        }

        $filterField = Mage::helper('advancedreports')->confOrderDateFilter();
        $orderTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order');

        $this->getSelect()->reset();

        $this->getSelect()->from(
            array('main_table' => $orderTable),
            array(
                'order_id'           => 'entity_id',
                'order_increment_id' => 'increment_id',
                'order_status'       => 'status',
                'order_created_at'   => $filterField,
                'customer_email'     => 'customer_email',
                'order_coupon'       => 'coupon_code',
            )
        );

        $this->getSelect()
            ->columns(array(
                    'base_xsubtotal' => "COALESCE(main_table.base_subtotal * $currencyRate, 0)",
                    'base_xdiscount_amount' => "COALESCE(main_table.base_discount_amount * $currencyRate, 0)",
                    'base_xtax_amount' => "COALESCE(main_table.base_tax_amount * $currencyRate, 0)",
                    'base_xshipping_amount' => "COALESCE(main_table.base_shipping_amount * $currencyRate, 0)",
                    'base_xgrand_total' => "COALESCE(((main_table.base_subtotal * $currencyRate)
                                  + (main_table.base_tax_amount * $currencyRate)
                                  + (main_table.base_discount_amount * $currencyRate)
                                  + (main_table.base_shipping_amount * $currencyRate)), 0)",
                    'base_xtotal_invoiced' => "COALESCE(main_table.base_total_invoiced * $currencyRate, 0)",
                    'base_xtotal_refunded' => "COALESCE(main_table.base_total_refunded * $currencyRate, 0)",
                )
            );

        $this->addPointsAndCreditInfo($currencyRate);

        $this->getSelect()
            ->group("order_id");


        return $this;
    }

    private function addPointsAndCreditInfo($currencyRate)
    {
        $this->addAwStorecreditInfo();
        $this->addAwPointsInfo();

        $this->getSelect()
            ->columns(array(
                    'base_xpoints_credit' => $this->getPointsCreditExpression($currencyRate),
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
        $expression = "COALESCE(($sum),0)";
        return $expression;
    }

    public function addOrderItems()
    {
        $filterField = Mage::helper('advancedreports')->confOrderDateFilter();
        $itemTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_item');

        $this->getSelect()
            ->join(
                array('item' => $itemTable),
                "(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)",
                array(
                    'xqty_ordered'      => 'COALESCE(SUM(IFNULL(item2.qty_ordered, item.qty_ordered)), 0)',
                    'xqty_invoiced'      => 'COALESCE(SUM(IFNULL(item2.qty_invoiced, item.qty_invoiced)), 0)',
                    'xqty_shipped'      => 'COALESCE(SUM(IFNULL(item.qty_shipped, item2.qty_shipped)), 0)',
                    'xqty_refunded'      => 'COALESCE(SUM(IFNULL(item2.qty_refunded, item.qty_refunded)), 0)',
                )
            );
        $this->getSelect()
            ->joinLeft(
                array('item2' => $itemTable),
                "(item2.order_id = main_table.entity_id AND item2.parent_item_id IS NOT NULL AND item2.parent_item_id = item.item_id AND item.product_type IN ('configurable'))",
                array()
            )
            ->order("main_table.{$filterField} DESC");

        return $this;
    }

    public function addAddress()
    {
        $salesFlatOrderAddress = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_address');
        $this->getSelect()
            ->joinLeft(
                array('flat_order_addr_ship' => $salesFlatOrderAddress),
                "flat_order_addr_ship.parent_id = main_table.entity_id AND flat_order_addr_ship.address_type = 'shipping'",
                array()
            )
            ->joinLeft(
                array('flat_order_addr_bil' => $salesFlatOrderAddress),
                "flat_order_addr_bil.parent_id = main_table.entity_id AND flat_order_addr_bil.address_type = 'billing'",
                array()
            );

        //order country id
        $this->getSelect()->columns(
            array(
                'order_country'  => 'COALESCE(flat_order_addr_ship.country_id, flat_order_addr_bil.country_id, "")',
                'order_region'   => 'COALESCE(flat_order_addr_ship.region, flat_order_addr_bil.region, "")',
                'order_city'     => 'COALESCE(flat_order_addr_ship.city, flat_order_addr_bil.city, "")',
                'order_postcode' => 'COALESCE(flat_order_addr_ship.postcode, flat_order_addr_bil.postcode, "")',
                'order_street'    => 'COALESCE(flat_order_addr_ship.street, flat_order_addr_bil.street, "")',
                'order_telephone'    => 'COALESCE(flat_order_addr_ship.telephone, flat_order_addr_bil.telephone, "")',
            )
        );

        //customer_name
        $this->getSelect()->columns(
            array(
                'customer_name' => "IFNULL(
                    CONCAT(main_table.customer_firstname,' ',main_table.customer_lastname),
                    CONCAT(flat_order_addr_bil.firstname, ' ', flat_order_addr_bil.lastname)
                )")
        );
        return $this;
    }

    /**
     * Add customer info
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Sales
     */
    public function addCustomerInfo()
    {
        $customerEntity = Mage::helper('advancedreports/sql')->getTable('customer_entity');
        $customerGroup = Mage::helper('advancedreports/sql')->getTable('customer_group');

        $this->getSelect()
            ->joinLeft(array('c_entity' => $customerEntity), "main_table.customer_id = c_entity.entity_id", array())
            ->joinLeft(
                array('c_group' => $customerGroup),
                "IFNULL(c_entity.group_id, 0) = c_group.customer_group_id",
                array('customer_group' => "c_group.customer_group_code")
            );

        return $this;
    }

    /**
     * Add shipment info (latest order's shipment date)
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Sales
     */
    public function addShipmentInfo()
    {
        $shipmentTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_shipment');

        $this->getSelect()
            ->joinLeft(
                array('shipment' => new Zend_Db_Expr("(SELECT order_id, MAX(created_at) AS created_at FROM {$shipmentTable} GROUP BY order_id)")),
                "shipment.order_id = main_table.entity_id",
                array('shipment_date' => 'shipment.created_at')
            );

        return $this;
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
        if ($isAllStores) {
            $currencyRate = "order.store_to_base_rate";
        } else {
            $currencyRate = new Zend_Db_Expr("1");
        }

        $costAttr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'cost');
        $costTable = $costAttr->getBackendTable();
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
                array(
                    'total_cost'             => "COALESCE(t.total_cost, 0)",
                    'total_profit'           => "COALESCE(t.total_profit, 0)",
                    'total_margin'           => "COALESCE(t.total_margin, 0)",
                    'total_revenue_excl_tax' => "COALESCE(t.total_revenue_excl_tax, 0)",
                    'total_revenue'          => "COALESCE(t.total_revenue, 0)",
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