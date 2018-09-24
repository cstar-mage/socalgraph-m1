<?php

class Blackbox_Registration_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_APPROVE_EMAIL_TEMPLATE   = 'newsletter/approval/email_template';
    const XML_PATH_APPROVE_EMAIL_IDENTITY   = 'newsletter/approval/email_identity';
    const XML_PATH_APPROVE_EMAIL_RECIPIENTS   = 'newsletter/approval/email_identity';

    public function getEmailTemplateId()
    {
        return Mage::getStoreConfig(self::XML_PATH_APPROVE_EMAIL_TEMPLATE);
    }

    //public function get
}