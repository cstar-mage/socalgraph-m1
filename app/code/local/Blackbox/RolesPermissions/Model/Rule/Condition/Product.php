<?php

/**
 * Product rule condition data model
 *
 * @category Mage
 * @package Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rolespermissions/rule_condition_product');
    }
    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = ($object instanceof Mage_Catalog_Model_Product) ? $object : $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal());

        return parent::validate($product);
    }
}
