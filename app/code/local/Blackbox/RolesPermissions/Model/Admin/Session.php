<?php

class Blackbox_RolesPermissions_Model_Admin_Session extends Mage_Admin_Model_Session
{
    protected $_validator = null;

    /**
     * Check current user permission on resource and privilege
     *
     * Mage::getSingleton('admin/session')->isAllowed('admin/catalog')
     * Mage::getSingleton('admin/session')->isAllowed('catalog')
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            return parent::isAllowed($resource, $privilege);
        }

        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
            if (!preg_match('/^admin/', $resource)) {
                $resource = 'admin/' . $resource;
            }

            try {
                if ($validator = $this->_getValidator()) {
                    $validator->process((new Varien_Object())->setCode($resource));

                    if ($validator->isFound()) {
                        return !$validator->isAccessDenied();
                    }
                }

                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
            } catch (Exception $e) {
                try {
                    if (!$acl->has($resource)) {
                        return $acl->isAllowed($user->getAclRole(), null, $privilege);
                    }
                } catch (Exception $e) { }
            }
        }
        return false;
    }

    /**
     * @return bool|Blackbox_RolesPermissions_Model_Validator
     */
    protected function _getValidator()
    {
        if (!isset($this->_validator)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            if ($customer->getId()) {
                $this->_validator = $validator = Mage::getSingleton('rolespermissions/validator')
                    ->init(Mage::app()->getWebsite()->getId(), Blackbox_RolesPermissions_Model_Rule::SCOPE_ADMIN, $customer);
            } else {
                $this->_validator = false;
            }
        }

        return $this->_validator;
    }
}