<?php

/**
 * RolesPermissions Model Observer
 *
 * @package     Blackbox_RolesPermissions
 */
class Blackbox_RolesPermissions_Model_Observer extends Mage_Review_Model_Observer
{

    /**
     * Sales Rule Validator
     *
     * @var Blackbox_RolesPermissions_Model_Validator
     */
    protected $_validator;

    /**
     * Get quote item validator/processor object
     *
     * @deprecated
     * @param   Varien_Event $event
     * @return  Blackbox_RolesPermissions_Model_Validator
     */
    public function getValidator($scope, $website = null)
    {
        if (is_null($website)) {
            $websiteId = Mage::app()->getWebsite()->getId();
        } else if (is_object($website)) {
            $websiteId = $website->getId();
        } else {
            $websiteId = $website;
        }

        if (!$this->_validator[$scope]) {
            $this->_validator[$scope] = Mage::getModel('rolespermissions/validator')
                ->init($websiteId, $scope);
        }
        return $this->_validator[$scope];
    }

    public function checkProductAccessRights($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $this->_checkProductAccess($event->getProduct());
    }

    public function checkQuoteProductAddAccessRights($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        /** @var Mage_Sales_Model_Quote_Item[] $items */
        $items = $event->getItems();
        foreach ($items as $item) {
            $this->_checkProductAccess($item->getProduct());
        }
    }

    public function checkCategoryAccessRights($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $this->_checkCategoryAccess($event->getCategory());
    }

    public function processProductCollection($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $this->_processCollectionAbstract($event->getCollection(), 'product');
    }

    public function catalogBlockProductCollectionBeforeToHtml(Varien_Event_Observer $observer)
    {
        $this->processProductCollection($observer);
        return parent::catalogBlockProductCollectionBeforeToHtml($observer);
    }


    public function processCategoryCollection($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $this->_processCollectionAbstract($event->getCategoryCollection(), 'category');
    }

    public function checkCmsPageAccessRights($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $validator = $this->getValidator(Blackbox_RolesPermissions_Model_Rule::SCOPE_CMS_PAGES);
        if ($validator->process($event->getPage())->isAccessDenied()) {
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward();
            throw $e;
        }
    }

    public function checkCmsBlockAccessRights($event)
    {
        if (!Mage::helper('rolespermissions')->isEnabled(true)) {
            return;
        }
        $block = $event->getBlock();
        if ($block->getIsActive()) {
            $validator = $this->getValidator(Blackbox_RolesPermissions_Model_Rule::SCOPE_CMS_BLOCKS);
            if ($validator->process($block)->isAccessDenied()) {
                $block->setIsActive(false);
            }
        }
    }

    public function customerSaveAfter($event)
    {
        $customer = $event->getCustomer();

        $this->_customerSaveAdmin($customer);

        $this->_customerSaveAfterAbstract($customer, 'category', 'catalog/category', Blackbox_RolesPermissions_Model_Rule::SCOPE_CATEGORIES, 'category_id', function ($entity) {
            return (new Varien_Object())->setData(array('category_id' => $entity->getId()));
        });

        $this->_customerSaveAfterAbstract($customer, 'product', 'catalog/product', Blackbox_RolesPermissions_Model_Rule::SCOPE_PRODUCTS, 'product_id');
    }

    protected function _customerSaveAfterAbstract($customer, $entityName, $entityModel, $scope, $entityIdField, callable $processEntityCallback = null)
    {
        Mage::helper('rolespermissions')->disable();
        try {
            $deniedEntitiesOld = array();
            $deniedEntitiesNew = array();

            $denied = $this->_getDeniedEntityModel($entityName)->getCollection()->addFieldToFilter('customer_id', $customer->getId());
            foreach ($denied as $item) {
                $item->delete();
                $deniedEntitiesOld[] = $item->getData($entityIdField);
            }

            $entities = Mage::getModel($entityModel)->getCollection();
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getId() == '0') {
                    continue;
                }
                /* @var Blackbox_RolesPermissions_Model_Validator $validator */
                $validator = Mage::getSingleton('rolespermissions/validator')
                    ->init($website->getId(), $scope, $customer);

                foreach ($entities as $entity) {
                    if ($processEntityCallback) {
                        $param = $processEntityCallback($entity);
                    } else {
                        $param = $entity;
                    }
                    if ($validator->process($param)->isAccessDenied()) {
                        $this->_getDeniedEntityModel($entityName)->setData(array(
                            $entityIdField => $entity->getId(),
                            'customer_id' => $customer->getId(),
                            'website_id' => $website->getId()
                        ))->save();
                        $deniedEntitiesNew[] = $entity->getId();
                    }
                }
            }

            $affected = array_unique(array_merge($deniedEntitiesNew, $deniedEntitiesOld));
            Mage::dispatchEvent('rolespermission_reindex_customer_after', array(
                'entity' => $entityName,
                'affected_entities' => $affected
            ));
        } finally {
            Mage::helper('rolespermissions')->enable();
        }
    }

    public function productSaveAfter($event)
    {
        $product = $event->getProduct();

        $this->_entitySaveAfterAbstract($product,'product', Blackbox_RolesPermissions_Model_Rule::SCOPE_PRODUCTS, 'product_id');
    }

    public function categorySaveAfter($event)
    {
        $category = $event->getCategory();

        $this->_entitySaveAfterAbstract($category, 'category', Blackbox_RolesPermissions_Model_Rule::SCOPE_CATEGORIES, 'category_id', function ($entity) {
            return (new Varien_Object())->setData(array('category_id' => $entity->getId()));
        });
    }

    public function removePricesFromGrid($observer)
    {
        if (Mage::helper('rolespermissions')->canViewPrices()) {
            return;
        }
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            foreach ($block->getColumns() as $index => $column) {
                if ($column->getType() == 'currency' || $column->getType() == 'price') {
                    $block->removeColumn($index);
                }
            }
        }
    }

    protected function _entitySaveAfterAbstract($entity, $entityName, $scope, $entityIdField, callable $processEntityCallback = null)
    {
        Mage::helper('rolespermissions')->disable();
        try {
            $denied = $this->_getDeniedEntityModel($entityName)->getCollection()->addFieldToFilter($entityIdField, $entity->getId());
            foreach ($denied as $item) {
                $item->delete();
            }

            $customers = Mage::getResourceModel('customer/customer_collection')->addAttributeToSelect('*');
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getId() == '0') {
                    continue;
                }
                /* @var Blackbox_RolesPermissions_Model_Validator $validator */
                $validator = Mage::getSingleton('rolespermissions/validator')
                    ->init($website->getId(), $scope, Mage::getModel('customer/customer')->setGroupId(0));


                if ($processEntityCallback) {
                    $param = $processEntityCallback($entity);
                } else {
                    $param = $entity;
                }
                if ($validator->process($param)->isAccessDenied()) {
                    $this->_getDeniedEntityModel($entityName)->setData(array(
                        $entityIdField => $entity->getId(),
                        'website_id' => $website->getId()
                    ))->save();
                }

                foreach ($customers as $customer) {
                    $validator->setCustomer($customer);
                    if ($processEntityCallback) {
                        $param = $processEntityCallback($entity);
                    } else {
                        $param = $entity;
                    }
                    if ($validator->process($param)->isAccessDenied()) {
                        $this->_getDeniedEntityModel($entityName)->setData(array(
                            $entityIdField => $entity->getId(),
                            'customer_id' => $customer->getId(),
                            'website_id' => $website->getId()
                        ))->save();
                        $deniedEntitiesNew[] = $entity->getId();
                    }
                }
            }
        } finally {
            Mage::helper('rolespermissions')->enable();
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $item
     */
    protected function _checkProductAccess($item)
    {
        Mage::helper('rolespermissions')->disable();
        try {
            if ($item->getStoreId()) {
                $website = Mage::app()->getStore($item->getStoreId())->getWebsite();
            } else {
                $website = null;
            }
            $validator = $this->getValidator(Blackbox_RolesPermissions_Model_Rule::SCOPE_PRODUCTS, $website);

            $denied = false;
            if ($validator->process($item)->isAccessDenied()) {
                $denied = true;
            }

            if (!$denied) {
                $found = false;
                $categories = $item->getCategoryCollection();
                if (count($categories)) {
                    foreach ($categories as $category) {
                        if ($this->_checkCategoryAccess($category, false)) {
                            $found = true;
                            break;
                        }
                    }
                    $denied = !$found;
                }
            }

            if ($denied) {
                $customer = $this->_getGlobalCustomer();
                if ($customer) {
                    $customer = $customer->getName() . ' (ID ' . $customer->getId() . ')';
                } else {
                    $customer = 'not authorized customer';
                }
                Mage::throwException("Not enough rights to view product {$item->getId()}  for $customer");
            }
        } finally {
            Mage::helper('rolespermissions')->enable();
        }
        return;
    }

    /**
     * @param Mage_Catalog_Model_Category $item
     */
    protected function _checkCategoryAccess($item, $throwException = true)
    {
        Mage::helper('rolespermissions')->disable();
        try {
            if ($item->getStoreId()) {
                $website = Mage::app()->getStore($item->getStoreId())->getWebsite();
            } else {
                $website = null;
            }
            $validator = $this->getValidator(Blackbox_RolesPermissions_Model_Rule::SCOPE_CATEGORIES, $website);

            $parentItems = $item->getParentCategories();

            $items = array_merge(array($item), $parentItems);
            foreach ($items as $_item) {
                if ($validator->process($_item)->isAccessDenied()) {
                    if (!$throwException) {
                        return false;
                    }
                    $customer = $this->_getGlobalCustomer();
                    if ($customer) {
                        $customer = $customer->getName() . ' (ID ' . $customer->getId() . ')';
                    } else {
                        $customer = 'not authorized customer';
                    }
                    Mage::throwException("Not enough rights to view category {$item->getId()}  for $customer");
                }
            }
        } finally {
            Mage::helper('rolespermissions')->enable();
        }
        return true;
    }

    protected function _processCollectionAbstract($collection, $entityName)
    {
        if (!Mage::helper('rolespermissions')->isEnabled()) {
            return;
        }

        $deniedTable = Mage::getSingleton('core/resource')->getTableName('rolespermissions/denied_' . $entityName);
        $websiteId = Mage::app()->getStore($collection->getStoreId())->getWebsiteId();
        $customer = $this->_getGlobalCustomer();
        if ($customer) {
            $customerId = '= ' . $customer->getId();
        } else {
            $customerId = 'IS NULL';
        }

        $collection->getSelect()->joinLeft(
            array('denied' => $deniedTable),
            "e.entity_id = denied.{$entityName}_id AND denied.website_id = $websiteId AND denied.customer_id $customerId",
            array('denied.id')
        )
            ->where('denied.id IS NULL');
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getGlobalCustomer()
    {
        $customer = null;
        if (Mage::app()->getStore()->isAdmin()) {
            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('admin/session')->getUser()->getCustomer();
            }
        } else {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
            }
        }
        if (!$customer && Mage::getSingleton('api/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('api/session')->getUser()->getCustomer();
        }

        return $customer;
    }

    protected function _getDeniedEntityModel($entityName)
    {
        return Mage::getModel('rolespermissions/denied_' . $entityName);
    }

    protected function _customerSaveAdmin($customer)
    {
        $admin = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->getFirstItem();

        if ($admin && $admin->getId()) {
            $admin->addData(array(
                'firstname' => $customer->getFirstname(),
                'lastname'    => $customer->getLastname(),
                'email'     => $customer->getEmail(),
            ));
            if ($customer->getPasswordHash()) {
                $admin->setPassword($customer->getPasswordHash());
                $admin->setOrigData('password', $customer->getPasswordHash());
            }
            $admin->save();
        }
    }
}