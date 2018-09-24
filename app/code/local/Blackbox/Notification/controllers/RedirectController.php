<?php

class Blackbox_Notification_RedirectController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::helper('websso')->setBeforeLoginUrl(Mage::helper('core/url')->getCurrentUrl());
            Mage::getSingleton('core/session')->setAfterLoginRedirect(Mage::helper('core/url')->getCurrentUrl());

            $this->_forward('login', 'account', 'customer');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Blackbox_Notification_Model_Email_Redirect $redirect */
        $redirect = Mage::getModel('blackbox_notification/email_redirect')->load($id);

        if (!$redirect->getId()) {
            $this->norouteAction();
            return;
        }

        $redirectUrl = $redirect->generateRedirectUrl(Mage::getModel('customer/session')->getCustomer());

        if ($redirectUrl) {
            $this->_redirectUrl($redirectUrl);
        } else {
            $this->norouteAction();
        }
    }
}