<?php

class Blackbox_RolesPermissions_Model_Indexer
{
    public function applyProductRule()
    {
        $this->_applyRuleAbstract('product', 'catalog/product', Blackbox_RolesPermissions_Model_Rule::SCOPE_PRODUCTS, 'product_id');
    }

    public function applyCategoryRule()
    {
        $this->_applyRuleAbstract('category', 'catalog/category', Blackbox_RolesPermissions_Model_Rule::SCOPE_CATEGORIES, 'category_id', function ($entity) {
            return (new Varien_Object())->setData(array('category_id' => $entity->getId()));
        });
    }

    protected function _applyRuleAbstract($entityName, $entityModel, $scope, $entityIdField, callable $processEntityCallback = null)
    {
        Mage::helper('rolespermissions')->disable();
        try {
            $deniedEntitiesNew = array();

            /** @var Zend_Db_Adapter_Abstract $writeAdapter */
            $writeAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');
            $table = Mage::getSingleton('core/resource')->getTableName($writeAdapter->getTableName('rolespermissions/denied_' . $entityName));
            $select = $writeAdapter->select()
                ->from($table, array($entityIdField))
                ->group($entityIdField);

            $deniedEntitiesOld = $writeAdapter->fetchCol($select);

            Mage::getResourceModel('rolespermissions/denied_' . $entityName)->truncate();

            $customers = Mage::getModel('customer/customer')->getCollection();
            $entities = Mage::getModel($entityModel)->getCollection();
            foreach (Mage::app()->getWebsites() as $website) {
                if ($website->getId() == '0') {
                    continue;
                }
                /* @var Blackbox_RolesPermissions_Model_Validator $validator */
                $validator = Mage::getSingleton('rolespermissions/validator')
                    ->init($website->getId(), $scope, Mage::getModel('customer/customer')->setGroupId(0));

                foreach ($entities as $entity) {
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
                        $deniedEntitiesNew[] = $entity->getId();
                    }
                }

                foreach ($customers as $customer) {
                    $validator->setCustomer(Mage::getModel('customer/customer')->load($customer->getId()));
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

    protected function _getDeniedEntityModel($entityName)
    {
        return Mage::getModel('rolespermissions/denied_' . $entityName);
    }
}