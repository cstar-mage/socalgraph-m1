<?php
/**
 * Abstract Rule customer condition data model
 *
 * @method string getAttribute()
 * @method string getOperator()
 *
 */
abstract class Blackbox_OrderApproval_Model_Rule_Condition_Customer_Abstract extends Blackbox_OrderApproval_Model_Rule_Condition_Abstract
{
    /**
     * Rule condition SQL builder
     *
     * @var Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder
     */
    protected $_ruleResourceHelper;

    /**
     * All attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * Will be set only for not global scope attribute
     *
     * @var array
     */
    protected $_entityAttributeValues = null;

    /**
     * Prepare bind array of ids from string or array
     *
     * @param string|int|array $value
     * @return array
     */
    public function bindArrayOfIds($value)
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        $value = array_map('trim', $value);
        $value = array_filter($value, 'is_numeric');

        return $value;
    }

    /**
     * Prepare sql where by condition
     *
     * @return string
     */
    public function prepareConditionSql()
    {
        $alias     = 'cpf';
        $attribute = $this->getAttribute();
        $value     = $this->getValueParsed();
        $operator = $this->getOperator();

        /** @var $ruleResource Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder */
        $ruleResource = $this->getRuleResourceHelper();

        return $ruleResource->getOperatorCondition($alias . '.' . $attribute, $operator, $value);
    }

    /**
     * Rule condition SQL builder setter
     *
     * @param Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder $ruleHelper
     */
    public function setRuleResourceHelper(Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder $ruleHelper)
    {
        $this->_ruleResourceHelper = $ruleHelper;
    }

    /**
     * Rule condition SQL builder getter
     *
     * @return Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder
     */
    public function getRuleResourceHelper()
    {
        if (!$this->_ruleResourceHelper) {
            $this->_ruleResourceHelper = Mage::getModel('rule/resource_rule_condition_sqlBuilder');
        }
        return $this->_ruleResourceHelper;
    }

    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('customer', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('customer/customer'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    /**
     * Load attribute options
     *
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Customer_Abstract
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = Mage::getResourceSingleton('customer/customer')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($customerAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */

            if ($attribute->getFrontendLabel()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            } else {
                $attributes[$attribute->getAttributeCode()] = $this->_convertIdToName($attribute->getAttributeCode());
            }
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Prepares values options to be used as select options or hashed array
     * Result is stored in following keys:
     *  'value_select_options' - normal select array: array(array('value' => $value, 'label' => $label), ...)
     *  'value_option' - hashed array: array($value => $label, ...),
     *
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Customer_Abstract
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                if ($attributeObject->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                if ($attributeObject->getAttributeCode() == 'group_id') {
                    $selectOptions = $attributeObject->getSource()->setIncludeNotLoggedGroup(true)->getAllOptions($addEmptyOption);
                } else {
                    $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
                }
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option
     *
     * @param mixed $option
     * @return string
     */
    public function getValueOption($option=null)
    {
        $this->_prepareValueOptions();
        return $this->getData('value_option'.(!is_null($option) ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve attribute element
     *
     * @return Varien_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Collect validated attributes
     *
     * @param Mage_Customer_Model_Resource_Customer_Collection $customerCollection
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Customer_Abstract
     */
    public function collectValidatedAttributes($customerCollection)
    {
        $attribute = $this->getAttribute();
        if ($this->getAttributeObject()->isScopeGlobal()) {
            $attributes = $this->getRule()->getCollectedAttributes();
            $attributes[$attribute] = true;
            $this->getRule()->setCollectedAttributes($attributes);
            $customerCollection->addAttributeToSelect($attribute, 'left');
        } else {
            $this->_entityAttributeValues = $customerCollection->getAllAttributeValues($attribute);
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
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        if (array_search($this->getAttributeObject()->getAttributeCode(), array('created_at', 'updated_at')) !== false) {
            return 'date';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }

        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }
        return false;
    }

    /**
     * Load array
     *
     * @param array $arr
     * @return Blackbox_OrderApproval_Model_Rule_Condition_Customer
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        $isContainsOperator = !empty($arr['operator']) && in_array($arr['operator'], array('{}', '!{}'));
        if ($attribute && $attribute->getBackendType() == 'decimal' && !$isContainsOperator) {
            if (isset($arr['value'])) {
                if (!empty($arr['operator'])
                    && in_array($arr['operator'], array('!()', '()'))
                    && false !== strpos($arr['value'], ',')) {

                    $tmp = array();
                    foreach (explode(',', $arr['value']) as $value) {
                        $tmp[] = Mage::app()->getLocale()->getNumber($value);
                    }
                    $arr['value'] =  implode(',', $tmp);
                } else {
                    $arr['value'] =  Mage::app()->getLocale()->getNumber($arr['value']);
                }
            } else {
                $arr['value'] = false;
            }
            $arr['is_value_parsed'] = isset($arr['is_value_parsed'])
                ? Mage::app()->getLocale()->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate customer attribute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        if (!($object instanceof Mage_Customer_Model_Customer)) {
            $object = Mage::getModel('customer/customer')->load($object->getId());
        }

        if (! isset($this->_entityAttributeValues[$object->getId()])) {
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
}
