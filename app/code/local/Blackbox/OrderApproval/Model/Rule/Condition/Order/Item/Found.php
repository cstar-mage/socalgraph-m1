<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Found extends Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_item_found');
    }

    /**
     * Load value options
     *
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Found
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1 => '1'
        ));
        return $this;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function getValueElementRenderer()
    {
        if (strpos($this->getValueElementType(), '/')!==false) {
            return Mage::getBlockSingleton($this->getValueElementType());
        }
        return Mage::getBlockSingleton('rule/editable');
    }

    public function getNumericValueElement()
    {
        return $this->getForm()->addField('action:'.$this->getId().':value', 'text', array(
            'name'=>'rule[actions]['.$this->getId().'][value]',
            'value'=>$this->getValue(),
            'value_name'=>$this->getValueName(),
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('order_approval')->__("If greater or equal then  %s items in the order with %s of these conditions true:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $object
     * @param Mage_Sales_Model_Order_Item $item
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        $all = $this->getAggregator()==='all';
        $qty = (int)$this->getValue();
        $foundQty = 0;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if ($found) {
                $foundQty += $this->getItemQty($item);
                if ($foundQty >= $qty) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function getItemQty($item)
    {
        while ($item->getParentItem()) {
            $item = $item->getParentItem();
        }
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            return $item->getQtyOrdered();
        }
        return $item->getQty();
    }
}
