<?php

abstract class Blackbox_HelpDesk_Model_Resource_Zendesk_Abstract extends Mage_Core_Model_Resource_Abstract
{
    private $api;

    protected $saveFields = [];

    protected function _init($apiModelName, $idFieldName = null)
    {
        $this->api = Mage::getSingleton($apiModelName);
    }

    /**
     * @param Varien_Object $object
     * @return $this
     */
    public abstract function save($object);

    /**
     * @param Varien_Object $object
     * @return $this
     */
    public abstract function load($object, $id, $field = null);

    public function getIdFieldName()
    {
        return 'id';
    }

    /**
     * @param Varien_Object $object
     * @return array
     */
    protected function _prepareDataForSave($object, $forUpdate = false)
    {
        $data = [];
        if ($forUpdate) {
            $origData = $object->getOrigData();
        }
        foreach ($this->saveFields as $objectField => $field) {
            if (is_numeric($objectField)) {
                $objectField = $field;
            }

            if ($object->hasData($objectField) && (!$forUpdate || !array_key_exists($objectField, $origData) || $object->getData($objectField) != $origData[$objectField])) {
                $data[$field] = $object->getData($objectField);
            }
        }
        if ($object->hasData('custom_fields')) {
            $customFields = $object->getData('custom_fields');
            foreach ($customFields as $key => $value) {
                if (is_array($value)) {
                    $data['custom_fields'][] = $value;
                } else {
                    $data['custom_fields'][] = [
                        'id' => $key,
                        'value' => $value
                    ];
                }
            }
        }
        return $data;
    }

    protected function _assignData($object, $data)
    {
        $renameFields = array_flip($this->saveFields);
        foreach ($data as $field => $value) {
            if (!isset($renameFields[$field]) || is_numeric($renameFields[$field])) {
                $object->setData($field, $value);
            } else {
                $object->setData($renameFields[$field], $value);
            }
        }
    }

    protected function _camelize($name)
    {
        return uc_words($name, '');
    }

    /**
     * @return Zendesk_Zendesk_Model_Api_Abstract
     */
    public function _getReadAdapter()
    {
        return $this->api;
    }

    /**
     * @return Zendesk_Zendesk_Model_Api_Abstract
     */
    public function _getWriteAdapter()
    {
        return $this->api;
    }

    protected function _prepareDataForTable(Varien_Object $object, $table)
    {
        $qwe = 1;
    }
}