<?php

class Blackbox_Epace_Helper_Object extends Mage_Core_Helper_Abstract
{
    protected $cacheEnabled = true;

    protected $cache = [];

    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    public function setCacheEnabled($enabled)
    {
        $this->cacheEnabled = (bool) $enabled;

        return $this;
    }

    public function load($type, $id)
    {
        if ($this->cacheEnabled) {
            if (isset($this->cache[$type][$id])) {
                return $this->cache[$type][$id];
            }
            return $this->cache[$type][$id] = Mage::getModel($type)->load($id);
        } else {
            return Mage::getModel($type)->load($id);
        }
    }
}