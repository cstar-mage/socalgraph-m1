<?php

class Blackbox_Notification_Model_Core_Email_Template_Filter extends Blackbox_Checkout_Model_Core_Email_Template_Filter//Mage_Core_Model_Email_Template_Filter
{
    public function redirectDirective($construction)
    {
        if (count($this->_templateVars)==0) {
            // If template preprocessing
            return $construction[0];
        }

        //$params = $this->_getIncludeParameters($construction[2]);

        $redirect = $this->_getRedirect();

        if (!$redirect) {
            return '';
        }

        if (!$redirect->getId()) {
            $redirect->save();
        }

        /** @var Mage_Core_Model_Url $url */
        $url = Mage::getModel('core/url');

        return htmlentities($url->getUrl('email/redirect', array('id' => $redirect->getId())));
    }

    /**
     * @return Blackbox_Notification_Model_Email_Redirect
     */
    protected function _getRedirect()
    {
        $order = $this->_templateVars['order'];
        $rule = $this->_templateVars['rule'];

        if (!$order || !$rule) {
            return null;
        }

        /** @var Blackbox_Notification_Helper_Data $helper */
        $helper = Mage::helper('blackbox_notification');
        return $helper->getEmailRedirect($order, $rule);
    }
}