<?php

class Blackbox_RolesPermissions_Model_Rule_Condition_Product_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * "Product attribute match a value" conditions group
     */
    const PRODUCT_ATTRIBUTES_TYPE_PRODUCT = 'product_attribute_match';

    /**
     * "Product attribute is set" conditions group
     */
    const PRODUCT_ATTRIBUTES_TYPE_ISSET = 'product_attribute_isset';

    /**
     * Products attributes info
     * @var array
     */
    protected $_productAttributesInfo = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_Catalog_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog');
    }

    /**
     * Add an attribute condition to the conditions group
     * @param $conditionType
     * @param $conditionModel
     * @param $attributeCode
     * @param $attributeLabel
     *
     * @return $this
     */
    protected function _addAttributeToConditionGroup($conditionType, $conditionModel, $attributeCode, $attributeLabel)
    {
        if (!array_key_exists($conditionType, $this->_productAttributesInfo)) {
            $this->_productAttributesInfo[$conditionType] = array();
        }

        $conditionKey = sprintf('%s|%s', $conditionModel, $attributeCode);

        $this->_productAttributesInfo[$conditionType][$conditionKey] = array(
            'label' => $attributeLabel,
            'value' => $conditionKey
        );

        return $this;
    }

    /**
     * Retrieve a conditions by group_id
     * @param $conditionsGroup
     *
     * @return array
     */
    protected function _getAttributeConditions($conditionsGroup)
    {
        $this->_initializeProductAttributesInfo();
        return array_key_exists($conditionsGroup, $this->_productAttributesInfo)
            ? $this->_productAttributesInfo[$conditionsGroup]
            : array();
    }

    /**
     * CHeck whether the product attribute information exists and initialize it if missing
     * @return $this
     */
    protected function _initializeProductAttributesInfo()
    {
        if (is_null($this->_productAttributesInfo)) {
            $this->_productAttributesInfo = array();
            $productAttributes = Mage::getModel('rolespermissions/rule_condition_product')
                ->loadAttributeOptions()
                ->getAttributeOption();
            foreach ($productAttributes as $attributeCode => $attributeLabel) {
                $this->_addAttributeToConditionGroup(
                    self::PRODUCT_ATTRIBUTES_TYPE_PRODUCT,
                    'rolespermissions/rule_condition_product',
                    $attributeCode,
                    $attributeLabel
                )->_addAttributeToConditionGroup(
                    self::PRODUCT_ATTRIBUTES_TYPE_ISSET,
                    'rolespermissions/rule_condition_product_attribute_assigned',
                    $attributeCode,
                    $attributeLabel
                );
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
        $this->setType('rolespermissions/rule_condition_product_combine');
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
                    'value' => 'rolespermissions/rule_condition_product_combine'
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

    /**
     * Collect all validated attributes
     * @param $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }

    /**
     * Validate a condition with the checking of the child value
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $valid = parent::validate($object);
        if (!$valid && $product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $children = $object->getChildren();
            if (is_array($children) and isset($children[0])) {
                $child = $children[0];

                /** @var Mage_Catalog_Model_Product $childProduct */
                $childProduct = Mage::getModel('catalog/product')->load($child->getProductId());
                $childProduct
                    ->setQuoteItemQty($object->getQty())
                    ->setQuoteItemPrice($object->getPrice())
                    ->setQuoteItemRowTotal($object->getBaseRowTotal());

                if (!$childProduct->isVisibleInSiteVisibility()) {
                    $childProduct->setCategoryIds($product->getCategoryIds());
                }

                $valid = parent::validate($childProduct);
            }
        }

        return $valid;
    }
}
