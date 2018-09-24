<?php

/**
 * Abstract Rule cms page condition data model
 *
 * @method string getAttribute()
 * @method string getOperator()
 *
 * @package Blackbox_RolesPermissions
 */
abstract class Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract extends Mage_Rule_Model_Condition_Abstract
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
     * Attribute data key that indicates whether it should be used for rules
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

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
                ->getAttribute('cms_page', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('cms/page'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    /**
     * Load attribute options
     *
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
            'identifier' => 'Identifier'
        );
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Prepares values options to be used as select options or hashed array
     * Result is stored in following keys:
     *  'value_select_options' - normal select array: array(array('value' => $value, 'label' => $label), ...)
     *  'value_option' - hashed array: array($value => $label, ...),
     *
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract
     */
    protected function _prepareValueOptions()
    {
        return $this;
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
                $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
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
     * @param Mage_Cms_Model_Resource_Page_Collection $cmsPageCollection
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract
     */
    public function collectValidatedAttributes($cmsPageCollection)
    {
        $attribute = $this->getAttribute();

        if ($this->getAttributeObject()->isScopeGlobal()) {
            $attributes = $this->getRule()->getCollectedAttributes();
            $attributes[$attribute] = true;
            $this->getRule()->setCollectedAttributes($attributes);
            $cmsPageCollection->addAttributeToSelect($attribute, 'left');
        } else {
            $this->_entityAttributeValues = $cmsPageCollection->getAllAttributeValues($attribute);
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
     * @return Blackbox_RolesPermissions_Model_Rule_Condition_Cms_Page_Abstract
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
     * Validate cms page attrbute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        if (!($object instanceof Mage_Cms_Model_Page)) {
            $object = Mage::getModel('cms/page')->load($object->getId());
        }

        return $this->getValueParsed() == $object->getData($attrCode);
    }
}