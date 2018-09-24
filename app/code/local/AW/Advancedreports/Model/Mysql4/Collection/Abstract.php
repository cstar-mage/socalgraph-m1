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


class AW_Advancedreports_Model_Mysql4_Collection_Abstract extends AW_Advancedreports_Model_Mysql4_Order_Collection
{

    protected $_period;
    protected $_periodFormat;

    protected $_storeIds;


    public function getPeriods()
    {
        return array(
            'day'     => Mage::helper('advancedreports')->__('Day'),
            'week'    => Mage::helper('advancedreports')->__('Week'),
            'month'   => Mage::helper('advancedreports')->__('Month'),
            'quarter' => Mage::helper('advancedreports')->__('Quarter'),
            'year'    => Mage::helper('advancedreports')->__('Year'),
        );
    }

    public function setPeriod($period)
    {
        $this->_period = $period;
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
    }

    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    /**
     * Not use inside all whose wrappers
     *
     * @deprecated
     *
     * @param $table
     *
     * @return string
     */
    public function getTable($table)
    {
        return parent::getTable($table);
    }

    /**
     * Set up date filter to collection of grid
     *
     * @param Datetime $from
     * @param Datetime $to
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Abstract
     */
    public function setDateFilter($from, $to)
    {
        $filterField = Mage::helper('advancedreports')->confOrderDateFilter();
        $this->_from = $from;
        $this->_to = $to;
        $this->getSelect()
            ->where("main_table.{$filterField} >= ?", $from)
            ->where("main_table.{$filterField} <= ?", $to)
        ;
        return $this;
    }

    /**
     * Set up order state filter
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Abstract
     */
    public function setState()
    {
        $this->addAttributeToFilter('status', explode(",", Mage::helper('advancedreports')->confProcessOrders()));

        return $this;
    }

    public function setSize($size)
    {
        $this->_totalRecords = $size;
        return $this;
    }
}