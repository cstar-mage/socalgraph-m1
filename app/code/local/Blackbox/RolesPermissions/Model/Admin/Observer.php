<?php

class Blackbox_RolesPermissions_Model_Admin_Observer extends Mage_Admin_Model_Observer
{
    public function actionPreDispatchAdmin($observer)
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            return parent::actionPreDispatchAdmin($observer);
        }

        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = Mage::app()->getRequest();
        $user = $session->getUser();

        $requestedActionName = strtolower($request->getActionName());
        $openActions = array(
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'logout',
            'refresh' // captcha refresh
        );
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
        } else {
            if ($user) {
                $user->reload();
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();

            if ($customer->getId()) {
                if (!$session->getAcl()) {
                    $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                }
                $allow = !Mage::getSingleton('rolespermissions/validator')
                    ->init($customer->getWebsiteId(), Blackbox_RolesPermissions_Model_Rule::SCOPE_ADMIN, $customer)
                    ->process((new Varien_Object())->setCode('dashboard'))->isAccessDenied();
            }

            if (!$user || !$user->getId() || $user->getCustomerId() != $customer->getId() || isset($allow) && !$allow) {
                if ($customer->getId() && $allow) {
                    $session->setUser($this->_getAdminUser($customer));
                } else {
                    if ($user) {
                        $user->setId(null);
                    }
                    if ($request->getPost('login')) {

                        /** @var Mage_Core_Model_Session $coreSession */
                        $coreSession = Mage::getSingleton('core/session');

                        if ($coreSession->validateFormKey($request->getPost("form_key"))) {
                            $postLogin = $request->getPost('login');
                            $username = isset($postLogin['username']) ? $postLogin['username'] : '';
                            $password = isset($postLogin['password']) ? $postLogin['password'] : '';

                            if ($request->getControllerModule() == 'Mage_Oauth_Adminhtml') {
                                /** @var Mage_Customer_Model_Session $customerSession */
                                $customerSession = Mage::getSingleton('customer/session');
                                $customerSession->login($username, $password);
                                if ($customerSession->isLoggedIn()) {
                                    $session->setUser($this->_getAdminUser($customerSession->getCustomer()));
                                } else {
                                    $session->addError(Mage::helper('adminhtml')->__('Invalid User Name or Password.'));
                                }
                            } else {
                                $session->login($username, $password, $request);
                            }
                            $request->setPost('login', null);
                        } else {
                            if ($request && !$request->getParam('messageSent')) {
                                Mage::getSingleton('adminhtml/session')->addError(
                                    Mage::helper('adminhtml')->__('Invalid Form Key. Please refresh the page.')
                                );
                                $request->setParam('messageSent', true);
                            }
                        }

                        $coreSession->renewFormKey();
                    }
                    if (!$request->getInternallyForwarded()) {
                        $request->setInternallyForwarded();
                        if ($request->getParam('isIframe')) {
                            $request->setParam('forwarded', true)
                                ->setControllerName('index')
                                ->setActionName('deniedIframe')
                                ->setDispatched(false);
                        } elseif ($request->getParam('isAjax')) {
                            $request->setParam('forwarded', true)
                                ->setControllerName('index')
                                ->setActionName('deniedJson')
                                ->setDispatched(false);
                        } else {
                            $request->setParam('forwarded', true)
                                ->setRouteName('adminhtml')
                                ->setControllerName('index')
                                ->setActionName('loginRedirect')
                                ->setDispatched(false);

                            Mage::getSingleton('customer/session')->addError(Mage::helper('rolespermissions')->__('Permission Denied'));
                        }
                        return false;
                    }
                }
            }
        }

        $session->refreshAcl();
    }

    protected function _getAdminUser($customer)
    {
        return Mage::helper('rolespermissions')->getAdminUser($customer);
    }
}
