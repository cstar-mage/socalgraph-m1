<?php

class Wizkunde_WebSSO_Model_Backend_Session extends Mage_Admin_Model_Session
{
    /**
     * Try to login user in admin
     *
     * @param  string $username
     * @param  string $password
     * @param  Mage_Core_Controller_Request_Http $request
     * @return Mage_Admin_Model_User|null
     */
    public function login($username, $password, $request = null)
    {
        if (!Mage::helper('websso/data')->checkAdminEnabled()) {
            return parent::login($username, $password, $request);
        }

        if (empty($username)) {
            return;
        }

        try {
            /** @var $user Mage_Admin_Model_User */
            $user = $this->_factory->getModel('websso/backend_user');
            $user->login($username, $password);

            if ($user->getId()) {
                $this->renewSession();

                if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                    Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                }
                $this->setIsFirstPageAfterLogin(true);
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

                $encryptedHash = Mage::getModel('websso/backend_crypto')->encrypt($user->getId(), Mage::getStoreConfig('websso/advanced/secret'), true);

                $redirectUrl = Mage::getModel('adminhtml/url')->getUrl('adminhtml/dashboard/index') . '?lu=' . $encryptedHash;

                Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
                $this->_response->clearHeaders()
                    ->setRedirect($redirectUrl)
                    ->sendHeadersAndExit();
            } else {
                Mage::throwException(Mage::helper('adminhtml')->__('Invalid User Name or Password.'));
            }
        } catch (Mage_Core_Exception $e) {
            Mage::dispatchEvent('admin_session_user_login_failed',
                array('user_name' => $username, 'exception' => $e));
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $request->setParam('messageSent', true);
            }
        }

        return $user;
    }
}