<?php

class Wizkunde_WebSSO_Model_Backend_Observer extends Blackbox_RolesPermissions_Model_Admin_Observer
{
    const FLAG_NO_LOGIN = 'no-login';

    /**
     * Handler for controller_action_predispatch event
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionPreDispatchAdmin($observer)
    {
        if (Mage::helper('websso/data')->checkAdminEnabled() === false) {
            return parent::actionPreDispatchAdmin($observer);
        }

        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('websso/backend_session');

        if ($session->isLoggedIn() == false) {
            /** @var $request Mage_Core_Controller_Request_Http */
            $request = Mage::app()->getRequest();

            if ($request->has('lu')) {
                $userId = Mage::getModel('websso/backend_crypto')->decrypt($request->getParam('lu'), Mage::getStoreConfig('websso/advanced/secret'), true);

                if (is_numeric($userId) == false) {
                    return false;
                }

                $user = Mage::getModel('websso/backend_user')->load($userId);
                $session->setUser($user);
            }

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
                if (isset($user)) {
                    $user->reload();
                }
                if (!isset($user) || !$user->getId()) {
                    $postLogin = $request->getPost('login');
                    $username = isset($postLogin['username']) ? $postLogin['username'] : '';
                    $session->login($username, $request);
                    $request->setPost('login', null);

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
                                ->setActionName('login')
                                ->setDispatched(false);
                        }
                        return false;
                    }
                }
            }

            $session->refreshAcl();
        }

        return $observer;
    }
}
