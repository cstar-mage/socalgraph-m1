<?php

abstract class Blackbox_Notification_Model_Email_Redirect_Type_Abstract
{
    public function generateUrl(Blackbox_Notification_Model_Email_Redirect $redirect, Mage_Customer_Model_Customer $customer)
    {
        $config = $redirect->getConfig();

        $url = false;
        foreach ($config as $groupId => $redirectTemplate) {
            if ($groupId === '') {//default
                continue;
            }
            if ($groupId == $customer->getGroupId()) {
                $url = $redirectTemplate;
                break;
            }
        }

        if ($url === false && $config['']) {
            $url = $config[''];
        }

        if ($url) {
            $url = $this->processTemplate($url, $redirect->getParams());
        }

        return $url;
    }

    protected function processTemplate($template, $params)
    {
        return preg_replace_callback('/\{([^\}]*)\}/', function($match) use ($params) {
            return $params[$match[1]];
        }, $template);
    }
}