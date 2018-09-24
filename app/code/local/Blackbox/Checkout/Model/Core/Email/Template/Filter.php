<?php

class Blackbox_Checkout_Model_Core_Email_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
    const CONSTRUCTION_EACHORDER_PATTERN = '/{{eachorder\s*(.*?)}}(.*?){{\\/eachorder\s*}}/si';

    public function filter($value)
    {
        if (preg_match_all(self::CONSTRUCTION_EACHORDER_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index => $construction) {
                $replacedValue = '';
                try {
                    $replacedValue = $this->eachorderDirective($construction);
                } catch (Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }

        return parent::filter($value);
    }

    public function eachorderDirective($construction)
    {
        if (preg_match('/^(\S*)\s*(.*)/si', $construction[1], $match)) {
            $var = $match[1];
            $delimiter = $match[2];
        } else {
            return $construction[2];
        }

        $order = $this->_templateVars[$var];
        if (!$order || !$order->getAssociatedOrders()) {
            return $construction[2];
        }

        $result = '';
        $first = true;

        foreach(Mage::helper('blackbox_checkout')->getAssociatedOrders($order) as $_order) {
            $this->_templateVars[$var] = $_order;
            if (!$first && $delimiter) {
                $result .= $delimiter;
            }
            $result .= parent::filter($construction[2]);
            $first = false;
        }

        $this->_templateVars[$var] = $order;

        return $result;
    }
}