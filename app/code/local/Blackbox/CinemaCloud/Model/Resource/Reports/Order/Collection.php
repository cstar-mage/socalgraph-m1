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
                'expression' => '%s',
                'arguments' => array(
                    $adapter->getIfNullSql('main_table.job_value', 0)
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

    protected function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter = 0)
    {
        parent::_prepareSummaryLive($range, $customStart, $customEnd, $isFilter);

        $this->removeWhereForField('main_table.state');
        $this->getSelect()->where('main_table.state NOT IN (?)', [
                Mage_Sales_Model_Order::STATE_CANCELED
            ]
        );

        return $this;
    }

    protected function _calculateTotalsLive($isFilter = 0)
    {
        parent::_calculateTotalsLive($isFilter);

        $this->removeWhereForField('main_table.state');
        $this->getSelect()->where('main_table.state NOT IN (?)', [
                Mage_Sales_Model_Order::STATE_CANCELED
            ]
        );

        return $this;
    }

    protected function removeWhereForField($field)
    {
        $where = $this->getSelect()->getPart(Zend_Db_Select::WHERE);
        foreach ($where as $key => $condition) {
            if (strpos($condition, $field) !== false) {
                unset($where[$key]);
            }
        }
        foreach ($where as &$condition) {
            if (preg_match('/^\\s*(?:AND|OR)\\s*(.*)$/', $condition, $match)) {
                $condition = $match[1];
            }

            break;
        }
        $this->getSelect()->setPart(Zend_Db_Select::WHERE, $where);
    }
}