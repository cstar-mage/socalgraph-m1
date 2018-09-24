<?php

class Blackbox_Notification_Model_Rule_Condition_Product_Combine
    extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    /**
     * CHeck whether the product attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeProductAttributesInfo()
    {
        if (is_null($this->_productAttributesInfo)) {
            $this->_productAttributesInfo = array();
            $productAttributes = Mage::getModel('blackbox_notification/rule_condition_product')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($productAttributes as $attributeCode => $attributeLabel) {
                if ($this->_getIsQuoteItemAttribute($attributeCode)) {
                    $this->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_QUOTE_ITEM,
                        'blackbox_notification/rule_condition_product',
                        $attributeCode,
                        $attributeLabel
                    );
                } else {
                    $this->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_PRODUCT,
                        'blackbox_notification/rule_condition_product',
                        $attributeCode,
                        $attributeLabel
                    )->_addAttributeToConditionGroup(
                        self::PRODUCT_ATTRIBUTES_TYPE_ISSET,
                        'salesrule/rule_condition_product_attribute_assigned',
                        $attributeCode,
                        $attributeLabel
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Initialize a rule condition
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('blackbox_notification/rule_condition_product_combine');
    }

    /**
     * Generate a conditions data
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'label' => Mage::helper('catalog')->__('Conditions Combination'),
                    'value' => 'blackbox_notification/rule_condition_product_combine'
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Cart Item Attribute'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_QUOTE_ITEM)
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Product Attribute'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_PRODUCT),
                ),
                array(
                    'label' => $this->_getHelper()->__('Product Attribute Assigned'),
                    'value' => $this->_getAttributeConditions(self::PRODUCT_ATTRIBUTES_TYPE_ISSET)
                )
            )
        );
        return $conditions;
    }
}