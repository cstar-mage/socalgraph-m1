<?php

class Blackbox_Notification_Model_Head_Notification_Filter_Template extends Varien_Filter_Template
{
    public function adminurlDirective($construction)
    {
        $parameters = $this->_getIncludeParameters($construction[2]);
        $path = $parameters['path'];
        unset($parameters['path']);
        return Mage::helper('adminhtml')->getUrl($path, $parameters);
    }
}