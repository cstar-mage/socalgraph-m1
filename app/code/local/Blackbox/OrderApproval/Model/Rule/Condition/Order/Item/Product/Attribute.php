<?php

/**
 * Product rule condition data model
 *
 * @package Blackbox_OrderApproval
 */
class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Product_Attribute
    extends Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('order_approval/rule_condition_order_item_product_attribute');
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

        return parent::validate($product);
    }
}
