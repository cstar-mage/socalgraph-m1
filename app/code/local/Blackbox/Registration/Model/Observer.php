<?php

class Blackbox_Registration_Model_Observer
{
//    const XML_PATH_ACCOUNT_REQUEST_EMAIL_TEMPLATE   = 'newsletter/account_request/template';
//    const XML_PATH_ACCOUNT_REQUEST_EMAIL_IDENTITY   = 'newsletter/account_request/identity';
//    const XML_PATH_ACCOUNT_REQUEST_EMAIL_RECIPIENTS   = 'newsletter/account_request/recipients';
//    const XML_PATH_ACCOUNT_REQUEST_EMAIL_COPY_METHOD   = 'newsletter/account_request/copy_method';

    const XML_PATH_ACCOUNT_APPROVE_EMAIL_TEMPLATE = 'newsletter/account_approval/template';
    const XML_PATH_ACCOUNT_APPROVE_EMAIL_IDENTITY = 'newsletter/account_approval/identity';

    const NOTIFICATION_TYPE_REGISTRATION = 2228;

    public function customerLogin($observer)
    {
        $customer = $observer->getCustomer();
        if (!$customer->hasData('approved')) {
            $customer->load($customer->getId());
        }
        if (!$customer->getApproved()) {
            $session = Mage::getSingleton('customer/session');
            $session->setCustomer(Mage::getModel('customer/customer'))->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            Mage::getSingleton('core/session')->renewFormKey();
            $session->addError('Account approval is required. Please, wait until the administrator will approve your account.');
            Mage::app()->getResponse()->setRedirect(Mage::getUrl("customer/account/login"))->sendResponse();
            die;
        }
    }

    public function customerRegisterSuccess($observer)
    {
        $customer = $observer->getCustomer();

        /** @var Blackbox_Notification_Model_Validator $validator */
        $validator = Mage::getSingleton('blackbox_notification/validator');

        $validator->processNotification(self::NOTIFICATION_TYPE_REGISTRATION, null, [
            'customer' => $customer
        ]);
    }

//    public function customerRegisterSuccess($observer)
//    {
//        $customer = $observer->getCustomer();
//
//        $template = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_REQUEST_EMAIL_TEMPLATE);
//        $sender = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_REQUEST_EMAIL_IDENTITY);
//        if (!$template || !$sender) {
//            return;
//        }
//
//        $recipients = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_REQUEST_EMAIL_RECIPIENTS);
//        if (!$recipients) {
//            return;
//        }
//        $recipients = explode(',', $recipients);
//
//        $translate = Mage::getSingleton('core/translate');
//        /* @var $translate Mage_Core_Model_Translate */
//        $translate->setTranslateInline(false);
//
//        /** @var Mage_Core_Model_Email_Template $email */
//        $email = Mage::getModel('core/email_template');
//        $firstSkipped = false;
//        if (Mage::getStoreConfig(self::XML_PATH_ACCOUNT_REQUEST_EMAIL_COPY_METHOD) == 'bcc') {
//            foreach ($recipients as $key => $recipient) {
//                if ($firstSkipped) {
//                    $email->addBcc($recipient);
//                    unset($recipients[$key]);
//                } else {
//                    $firstSkipped = true;
//                }
//            }
//        }
//
//        foreach ($recipients as $recipient) {
//            $email->setDesignConfig(array('area' => 'frontend'))
//                ->sendTransactional(
//                    $template,
//                    $sender,
//                    $recipient,
//                    null,
//                    ['customer' => $customer]
//                );
//        }
//
//        $translate->setTranslateInline(true);
//    }

    public function adminhtmlCustomerSaveAfter($observer)
    {
        $customer = $observer->getCustomer();
        if (!(!$customer->getOrigData('approved') && $customer->getApproved())) {
            return;
        }

        if (!$customer->getPassword()) {
            $customer->setApproved(0);
            Mage::getSingleton('adminhtml/session')->addError('Specify password for account');
            return;
        }

        $template = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_APPROVE_EMAIL_TEMPLATE);
        $sender = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_APPROVE_EMAIL_IDENTITY);
        if (!$template || !$sender) {
            Mage::getSingleton('adminhtml/session')->addError('Email was not sent. Configure email template and email sender.');
            $customer->setApproved(0)->save();
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        /** @var Mage_Core_Model_Email_Template $email */
        $email = Mage::getModel('core/email_template');

        $email->setDesignConfig(array('area' => 'frontend'))
            ->sendTransactional(
                $template,
                $sender,
                $customer->getEmail(),
                null,
                ['customer' => $customer]
            );

        $translate->setTranslateInline(true);
        Mage::getSingleton('adminhtml/session')->addSuccess('Email with password was sent.');
    }
    
    public function initNotification($observer)
    {
        $observer->getTypeConditions()->setData(self::NOTIFICATION_TYPE_REGISTRATION, 'blackbox_notification/rule_condition_blank');
        $observer->getTypes()->setData(self::NOTIFICATION_TYPE_REGISTRATION, 'New Customer Registration');
        $observer->getEmailNodes()->setData(self::NOTIFICATION_TYPE_REGISTRATION, 'newsletter_account_request');
    }
}