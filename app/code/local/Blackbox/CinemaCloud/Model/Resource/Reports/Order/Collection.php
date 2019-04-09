<?php

class Blackbox_CinemaCloud_Model_Resource_Reports_Order_Collection extends Mage_Reports_Model_Resource_Order_Collection
{
    /**
     * Get sales amount expression
     *
     * @return string
     */
    protected function _getSalesAmountExpression()
    {
        if (is_null($this->_salesAmountExpression)) {
            $adapter = $this->getConnection();
            $expressionTransferObject = new Varien_Object(array(
                'expression' => '%s - %s',
                'arguments' => array(
                    $adapter->getIfNullSql('main_table.base_grand_total', 0),
                    $adapter->getIfNullSql('main_table.base_tax_amount', 0),
                )
            ));

//            Mage::dispatchEvent('sales_prepare_amount_expression', array(
//                'collection' => $this,
//                'expression_object' => $expressionTransferObject,
//            ));
            $this->_salesAmountExpression = vsprintf(
                $expressionTransferObject->getExpression(),
                $expressionTransferObject->getArguments()
            );
        }

        return $this->_salesAmountExpression;
    }
}