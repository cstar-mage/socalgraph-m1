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


interface AW_Advancedreports_Model_Mysql4_Collection_Profit_Interface
{
    /**
     * Set up profit columns for collection
     * ATTENTION: use this method only for collections with joined 'item' => 'sales_flat_order_item' table
     *
     * @param $dateFrom
     * @param $dateTo
     * @param bool $isAllStores
     *
     * @return mixed
     */
    public function addProfitInfo($dateFrom, $dateTo, $isAllStores = false);

    /**
     * Retrieve expression for total cost calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalCostExpression($currencyRate);

    /**
     * Retrieve expression for total discount calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalDiscountExpression($currencyRate);

    /**
     * Retrieve expression for total tax calculation
     *
     * @param mixed $currencyRate
     * @return string
     */
    public function getTotalTaxExpression($currencyRate);
}