<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.6.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    protected $emailTemplate;

    # CONTENT REPLACEMENT CONSTRUCTION
    const AWRMA_CONTENT = '{{var content}}';
    # RECIPIENTS
    const AWRMA_RECIPIENT_ADMIN = 'admin';
    const AWRMA_RECIPIENT_CUSTOMER = 'customer';
    const AWRMA_RECIPIENT_CHATBOX = 'chatbox';


    protected function _construct()
    {
        $this->emailTemplate = Mage::getModel('core/email_template');
        parent::_construct();
    }

    /**
     * Load base template for recipient and replace text AWRMA_CONTENT with
     * $template in it
     *
     * @param string $template
     * @param string $recipient
     *
     * @return AW_Rma_Model_Email_Template
     */
    public function setAWRMATemplate($template, $recipient = self::AWRMA_RECIPIENT_CUSTOMER)
    {
        switch ($recipient) {
            case self::AWRMA_RECIPIENT_ADMIN:
                $defaultTemplate = Mage::helper('awrma/config')->getAdminBaseTemplate(
                    $this->emailTemplate->getDesignConfig()->getStore()
                );
                break;
            case self::AWRMA_RECIPIENT_CUSTOMER:
                $defaultTemplate = Mage::helper('awrma/config')->getCustomerBaseTemplate(
                    $this->emailTemplate->getDesignConfig()->getStore()
                );
                break;
            case self::AWRMA_RECIPIENT_CHATBOX:
            default:
                $defaultTemplate = null;
                break;
        }
        if (!is_null($defaultTemplate)) {
            $defaultTemplates = self::getDefaultTemplates();
            if (!isset($defaultTemplates[$defaultTemplate])) {
                $this->emailTemplate->load($defaultTemplate);
            } else {
                $storeLocale = Mage::getStoreConfig('general/locale/code', $this->emailTemplate->getDesignConfig()->getStore());
                $this->emailTemplate->loadDefault($defaultTemplate, $storeLocale);
            }
            $this->emailTemplate->setTemplateText(str_replace(self::AWRMA_CONTENT, $template, $this->emailTemplate->getTemplateText()));
        } else {
            $this->emailTemplate->setTemplateText($template);
        }
        return $this;
    }

    /**
     * Sends email
     *
     * @param $sender
     * @param $email
     * @param $name
     * @param $vars
     * @param $storeId
     *
     * @return AW_Rma_Model_Email_Template
     */
    public function sendEmail($sender, $email, $name, $vars = array(), $storeId = null)
    {
        $this->emailTemplate->setSentSuccess(false);

        if (!$email) {
            return $this->emailTemplate;
        }

        if (($storeId === null) && $this->emailTemplate->getDesignConfig()->getStore()) {
            $storeId = $this->emailTemplate->getDesignConfig()->getStore();
        }

        if (!is_array($sender)) {
            $this->emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $sender . '/name', $storeId));
            $this->emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $sender . '/email', $storeId));
        } else {
            $this->emailTemplate->setSenderName($sender['name']);
            $this->emailTemplate->setSenderEmail($sender['email']);
        }

        if (!isset($vars['store'])) {
            $vars['store'] = Mage::app()->getStore($storeId);
        }

        if ($this->emailTemplate->getProcessedTemplate($vars)) {
            $this->emailTemplate->setSentSuccess($this->emailTemplate->send($email, $name, $vars));
        } else {
            $this->emailTemplate->setSentSuccess(true);
        }
        return $this;
    }

    public function addAttachment($name, $content)
    {
        /** @var Zend_Mime_Part $attach */
        $attach = $this->emailTemplate->getMail()->createAttachment($content);
        $attach->filename = $name;

        return $this;
    }
}
