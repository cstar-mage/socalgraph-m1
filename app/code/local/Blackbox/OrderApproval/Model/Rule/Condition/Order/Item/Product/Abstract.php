<?php

class Blackbox_OrderApproval_Model_Rule_Condition_Order_Item_Product_Abstract
    extends Mage_Rule_Model_Condition_Product_Abstract
{
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['attribute_set_id'] = Mage::helper('catalogrule')->__('Attribute Set');
        $attributes['category_ids'] = Mage::helper('catalogrule')->__('Category');
        $attributes['parent_category_ids'] = Mage::helper('catalogrule')->__('Category In Parents');
    }

    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids': case 'parent_category_ids':
            $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
            break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
                . $image
                . '" alt="" class="v-middle rule-chooser-trigger" title="'
                . Mage::helper('core')->quoteEscape(Mage::helper('rule')->__('Open Chooser'))
                . '" /></a>';
        }
        return $html;
    }

    /**
     * Collect validated attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();
        if ('category_ids' != $attribute && 'parent_category_ids' != $attribute) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes = $this->getRule()->getCollectedAttributes();
                $attributes[$attribute] = true;
                $this->getRule()->setCollectedAttributes($attributes);
                $productCollection->addAttributeToSelect($attribute, 'left');
            } else {
                $this->_entityAttributeValues = $productCollection->getAllAttributeValues($attribute);
            }
        }

        return $this;
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttributeObject()->getAttributeCode() == 'parent_category_ids') {
            return 'category';
        }
        return parent::getInputType();
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        $attribute = $this->getAttribute();
        if ($attribute == 'parent_category_ids') {
            $attribute = 'category_ids';
        }
        switch ($attribute) {
            case 'sku': case 'category_ids':
            $url = 'adminhtml/promo_widget/chooser'
                .'/attribute/'.$attribute;
            if ($this->getJsFormObject()) {
                $url .= '/form/'.$this->getJsFormObject();
            }
            break;
        }
        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if ($this->getAttribute() == 'parent_category_ids') {
            return true;
        }
        return parent::getExplicitApply();
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        if (!($object instanceof Mage_Catalog_Model_Product)) {
            $object = Mage::getModel('catalog/product')->load($object->getId());
        }

        if ('parent_category_ids' == $attrCode) {
            return $this->validateAttribute($this->_getParentCategories($object->getCategoryIds()));
        } else if ('category_ids' == $attrCode) {
            return $this->validateAttribute($object->getCategoryIds());
        } elseif (! isset($this->_entityAttributeValues[$object->getId()])) {
            if (!$object->getResource()) {
                return false;
            }
            $attr = $object->getResource()->getAttribute($attrCode);

            if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                $this->setValue(strtotime($this->getValue()));
                $value = strtotime($object->getData($attrCode));
                return $this->validateAttribute($value);
            }

            if ($attr && $attr->getFrontendInput() == 'multiselect') {
                $value = $object->getData($attrCode);
                $value = strlen($value) ? explode(',', $value) : array();
                return $this->validateAttribute($value);
            }

            return parent::validate($object);
        } else {
            $result = false; // any valid value will set it to TRUE
            // remember old attribute state
            $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;

            foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                $attr = $object->getResource()->getAttribute($attrCode);
                if ($attr && $attr->getBackendType() == 'datetime') {
                    $value = strtotime($value);
                } else if ($attr && $attr->getFrontendInput() == 'multiselect') {
                    $value = strlen($value) ? explode(',', $value) : array();
                }

                $object->setData($attrCode, $value);
                $result |= parent::validate($object);

                if ($result) {
                    break;
                }
            }

            if (is_null($oldAttrValue)) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }

            return (bool) $result;
        }
    }

    protected function _getParentCategories(array $categoryIds)
    {
        $result = $categoryIds;
        foreach ($categoryIds as $categoryId)
        {
            $parentId = Mage::getModel('catalog/category')->load($categoryId)->getParentId();
            if ($parentId) {
                $result = array_merge($result, $this->_getParentCategories(array($parentId)));
            }
        }
        return $result;
    }
}