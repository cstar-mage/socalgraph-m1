<?php

class Blackbox_Notification_Model_Head_Notification_Email_Filter_Template extends Mage_Core_Model_Email_Template_Filter
{
    public function adminurlDirective($construction)
    {
        $parameters = $this->_getIncludeParameters($construction[2]);
        $temp = [];
        foreach ($parameters as $key => $value)
        {
            $temp[] = $key . '="' . $value . '"';
        }
        return '{{adminurl ' . implode(' ', $temp) . ' }}';
    }
}