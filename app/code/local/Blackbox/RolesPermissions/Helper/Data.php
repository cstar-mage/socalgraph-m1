<?php

/**
 * RolesPermissions data helper
 */
class Blackbox_RolesPermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_options;
    protected $_canViewPrices = null;
    protected $_disable = 0;

    public function isEnabled($disableCheck = false)
    {
        return Mage::getStoreConfigFlag('rolespermissions_settings/general/enable') && (!$disableCheck || !$this->isDisabled());
    }

    public function canViewPrices()
    {
        if ($this->_canViewPrices === null) {
            if (!$this->isEnabled()) {
                $this->_canViewPrices = true;
            } else {

                /*$allowedGroups = explode(',', Mage::getStoreConfig('rolespermissions_settings/price/groups'));
                $customerGroups = explode(',', Mage::getSingleton('customer/session')->getCustomerGroupId());

                foreach ($customerGroups as $group) {
                    if ($this->_canViewPrices = (array_search($group, $allowedGroups) !== false)) {
                        break;
                    }
                }*/

                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $result = !Mage::getSingleton('rolespermissions/validator')
                    ->init(Mage::app()->getWebsite()->getId(), Blackbox_RolesPermissions_Model_Rule::SCOPE_ADMIN, $customer)
                    ->process((new Varien_Object())->setCode('price'))->isAccessDenied();

                $this->_canViewPrices = $result || $this->hasPermission('REVIEW_FINANCIALS', $customer);
            }
        }

        return $this->_canViewPrices;
    }

    /**
     * @param Mage_Customer_Model_Customer|null $customer
     * @return bool
     */
    public function hasAccessToAdminDashboard($customer = null)
    {
        if (!$customer) {
            $session = Mage::getSingleton('customer/session');
            if (!$session->isLoggedIn()) {
                return false;
            }
            $customer = $session->getCustomer();
        }
        return !Mage::getSingleton('rolespermissions/validator')
            ->init(0, Blackbox_RolesPermissions_Model_Rule::SCOPE_ADMIN, $customer)
            ->process((new Varien_Object())->setCode('dashboard'))->isAccessDenied();
    }

    public function hasPermission($permission, $customer = null)
    {
        if (!$customer) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        $userPermissions = $customer->getUserPermissions();
        return !empty($userPermissions) && array_search($permission, $userPermissions) !== false;
    }

    /**
     * Return scope options
     * @return array
     */
    public function getScopeOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();

            $options = $this->getScopeOptionsArray();
            foreach ($options as $value => $label) {
                $this->_options[] = array(
                    'value' => $value,
                    'label' => $label
                );
            }
        }
        return $this->_options;
    }

    /**
     * Return scope option array
     * @return array
     */
    public function getScopeOptionsArray()
    {
        return array(
            'cms_pages' => 'CMS Pages',
            'cms_blocks' => 'CMS Blocks',
            'categories' => 'Categories',
            'products' => 'Products',
            'admin' => 'Admin',
            'api' => 'Api'
        );
    }

    public function disable()
    {
        $this->_disable++;
    }

    public function enable()
    {
        $this->_disable--;
    }

    public function isDisabled()
    {
        return $this->_disable > 0;
    }

    public function getAdminUser($customer)
    {
        if (!$customer || !$customer->getId()) {
            return null;
        }

        $admin = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->getFirstItem();

        if (!$admin || !$admin->getId()) {
            $admin = $this->_createAdminUser($customer);
            Mage::getSingleton('admin/session')->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        }

        return $admin;
    }

    protected function _createAdminUser($customer)
    {
        $user = Mage::getModel('admin/user')
            ->setData(array(
                'username'  => 'customer_admin_' . $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname'    => $customer->getLastname(),
                'email'     => $customer->getEmail(),
                'password'  => 'qwerty',
                'is_active' => 1,
                'customer_id' => $customer->getId()
            ))->save();

        $user->setRoleIds(array(1))  //Administrator role id is 1 ,Here you can assign other roles ids
        ->setRoleUserId($user->getUserId())
            ->saveRelations();

        return $user;
    }
}
