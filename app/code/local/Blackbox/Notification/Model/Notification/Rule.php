<?php

/**
 * @method string getCustomerIds()
 * @method $this setCustomerIds(string $value)
 * @method string getContentTemplate()
 * @method $this setContentTemplate(string $value)
 * @method
 *
 * Class Blackbox_Notification_Model_Notification_Rule
 */
class Blackbox_Notification_Model_Notification_Rule extends Blackbox_Notification_Model_Rule_Abstract
{
    protected $_designConfig;

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('blackbox_notification/notification_rule');
        $this->setIdFieldName('rule_id');

        $this->_initTypes();
    }

    protected function _initTypes()
    {
        unset($this->_types[self::TYPE_PRODUCT_LOW_STOCK]);
        unset($this->_typeConditions[self::TYPE_PRODUCT_LOW_STOCK]);

        $typeConditions = new Varien_Object($this->_typeConditions);
        $types = new Varien_Object($this->_types);
        $emailNodes = new Varien_Object($this->_emailNodes);

        Mage::dispatchEvent('head_notification_types_init', [
            'type_conditions' => $typeConditions,
            'types' => $types,
        ]);
        $this->_typeConditions = $typeConditions->getData();
        $this->_types = $types->getData();
    }

    protected function _beforeSave()
    {
        if (is_array($this->getCustomerIds())) {
            $this->setCustomerIds(implode(',', $this->getCustomerIds()));
        }
        return parent::_beforeSave();
    }
}