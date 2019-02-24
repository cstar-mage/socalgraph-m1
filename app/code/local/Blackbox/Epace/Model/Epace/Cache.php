<?php

class Blackbox_Epace_Model_Epace_Cache
{
    protected $cache = [];

    /**
     * @param $type
     * @param $id
     * @return Blackbox_Epace_Model_Epace_AbstractObject
     */
    public function load($type, $id)
    {
        $className = Mage::getConfig()->getModelClassName($type);
        if (!isset($this->cache[$className][$id])) {
            /** @var Blackbox_Epace_Model_Epace_AbstractObject $object */
            $object = Mage::getModel($type, $this);
            $object->load($id);
            if (!$object->getId()) {
                $object = false;
            }
            $this->cache[$className][$id] = $object;
        }
        return $this->cache[$className][$id];
    }

    public function add($className, $id, $object)
    {
        $this->cache[$className][$id] = $object;
    }
}