<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';

class Blackbox_Registration_AccountController extends Mage_Customer_AccountController
{
    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            /**
             * @var $flowPassword Mage_Customer_Model_Flowpassword
             */
            $flowPassword = $this->_getModel('customer/flowpassword');
            $flowPassword->setEmail($email)->save();

            if (!$flowPassword->checkCustomerForgotPasswordFlowEmail($email)) {
                $this->_getSession()
                    ->addError($this->__('You have exceeded requests to times per 24 hours from 1 e-mail.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            if (!$flowPassword->checkCustomerForgotPasswordFlowIp()) {
                $this->_getSession()->addError($this->__('You have exceeded requests to times per hour from 1 IP.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId() && $customer->getApproved()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            $this->_getSession()
                ->addSuccess( $this->_getHelper('customer')
                    ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                        $this->_getHelper('customer')->escapeHtml($email)));
            $this->_redirect('*/*/');
            return;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }

    protected function _getCustomerErrors($customer)
    {
        $password = $customer->generatePassword(Mage_Customer_Model_Customer::MAXIMUM_PASSWORD_LENGTH);
        $request = $this->getRequest();
        $request->setPost('password', $password);
        $request->setPost('confirmation', $password);
        return parent::_getCustomerErrors($customer);
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $customer->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId(),
                $this->getRequest()->getPost('password')
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } else {
            $session->addSuccess($this->__('Account approval is required. Please, wait until the administrator will approve your account.'));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        }
        $this->_redirectSuccess($url);
        return $this;
    }
}