<?php

class Blackbox_EpaceImport_Model_Resource_Sales_Sale_Collection extends Mage_Sales_Model_Resource_Sale_Collection
{
    /**
     * Customer model
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_salesPerson;

    /**
     * Set filter by sales person
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Sales_Model_Resource_Sale_Collection
     */
    public function setSalesPersonFilter(Mage_Customer_Model_Customer $customer)
    {
        $this->_salesPerson = $customer;
        return $this;
    }


    /**
     * Before load action
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _beforeLoad()
    {
        $this->getSelect()
            ->from(
                array('sales' => Mage::getResourceSingleton('sales/order')->getMainTable()),
                array(
                    'store_id',
                    'lifetime'      => new Zend_Db_Expr('SUM(sales.base_grand_total)'),
                    'base_lifetime' => new Zend_Db_Expr('SUM(sales.base_grand_total * sales.base_to_global_rate)'),
                    'avgsale'       => new Zend_Db_Expr('AVG(sales.base_grand_total)'),
                    'base_avgsale'  => new Zend_Db_Expr('AVG(sales.base_grand_total * sales.base_to_global_rate)'),
                    'num_orders'    => new Zend_Db_Expr('COUNT(sales.base_grand_total)')
                )
            )
            ->group('sales.store_id');

        if ($this->_customer instanceof Mage_Customer_Model_Customer && $this->_salesPerson instanceof Mage_Customer_Model_Customer) {
            $this->addFieldToFilter([
                'sales.customer_id',
                'sales.sales_person_id'
            ], [
                $this->_customer->getId(),
                $this->_salesPerson->getId()
            ]);
        } else if ($this->_customer instanceof Mage_Customer_Model_Customer && !$this->_salesPerson) {
            $this->addFieldToFilter('sales.customer_id', $this->_customer->getId());
        } else if ($this->_salesPerson instanceof Mage_Customer_Model_Customer) {
            $this->addFieldToFilter('sales.sales_person_id', $this->_salesPerson->getId());
        }

        if (!is_null($this->_orderStateValue)) {
            $condition = '';
            switch ($this->_orderStateCondition) {
                case 'IN' :
                    $condition = 'in';
                    break;
                case 'NOT IN' :
                    $condition = 'nin';
                    break;
            }
            $this->addFieldToFilter('state', array($condition => $this->_orderStateValue));
        }

        Mage::dispatchEvent('sales_sale_collection_query_before', array('collection' => $this));
        return $this;
    }
}