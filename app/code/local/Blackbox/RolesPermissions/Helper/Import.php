<?php

class Blackbox_RolesPermissions_Helper_Import extends Mage_Core_Helper_Abstract
{
    public function getGroupsSettings()
    {
        $result = array();
        foreach ($this->getGroupsFullSettings() as $name => $value) {
            if (isset($value['short_name'])) {
                $name = $value['short_name'];
            }
            $result[$name] = (array)$value['rules'];
        }
        return $result;
    }

    public function getGroupsFullSettings()
    {
        return array(
            'Commercial Advantage' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'OTR',
                    'commercial/point-of-sale.html',
                    'SmartSolution Certified',
                )
            ),
            'Consumer Advantage' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'OTR',
                    'commercial/point-of-sale.html',
                    'SmartSolution Certified',
                )
            ),
            'Advantage Commercial Associate Dealers' => array(
                'short_name' => 'Adv. Commerc. Associate Dealers',
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'OTR',
                    'Consumer',
                    'Advantage',
                    'Point of Sale',
                    'SmartSolution Certified'
                )
            ),
            'Advantage Consumer Associate Dealers' => array(
                'short_name' => 'Adv. Consum. Associate Dealers',
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'OTR',
                    'Advantage',
                    'Commercial',
                    'Point of Sale',
                    'SmartSolution Certified'
                )
            ),
            'Yokohama Commercial Dealers' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage',
                    'consumer/point-of-sale.html'
                )
            ),
            'Yokohama Commercial Distributors' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage',
                    'consumer/point-of-sale.html',
                )
            ),
            'Yokohama Commercial Sales' => array(
                'rules' => array(
                    'Trade Show',
                    'SmartSolution Certified',
                    'consumer/point-of-sale.html'
                )
            ),
            'Yokohama Consumer Dealers' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage'
                )
            ),
            'Yokohama Consumer Distributors' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage'
                )
            ),
            'Yokohama Consumer Sales' => array(
                'rules' => array(
                    'Trade Show',
                    'SmartSolution Certified',
                )
            ),
            'Yokohama Corporate' => array(

            ),
            'Yokohama OTR Dealers' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage',
                    'consumer/point-of-sale.html'
                )
            ),
            'Yokohama OTR Distributors' => array(
                'rules' => array(
                    'Kits',
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage',
                    'consumer/point-of-sale.html'
                )
            ),
            'Yokohama OTR Sales' => array(
                'rules' => array(
                    'Trade Show',
                    'SmartSolution Certified',
                    'Advantage',
                    'consumer/point-of-sale.html'
                )
            )
        );
    }

    public function getGroupNames()
    {
        $result = array();
        foreach ($this->getGroupsFullSettings() as $name => $value) {
            $result[] = array(
                'full' => $name,
                'short' => isset($value['short_name']) ? $value['short_name'] : $name,
            );
        }
        return $result;
    }

    public function createGroupCategoryPermissions($groupName, array $hideCategories)
    {
        $rule = Mage::getModel('rolespermissions/rule')->setData(array(
            'scope' => 'categories',
            'simple_action' => 'deny',
            'name' => $groupName,
            'description' => $this->_getCategoryDescription($groupName, $hideCategories),
            'is_active' => 1,
            'sort_order' => 100,
            'website_ids' => $this->getWebsiteIds(false)
        )); /* @var Blackbox_RolesPermissions_Model_Rule $rule */

        $this->_addCategoryActions($rule, $hideCategories);
        $this->_addCategoryConditions($rule, $this->getCustomerGroupByName($groupName)->getId());

        $rule->save();
    }

    public function getCustomerGroupByName($name)
    {
        $group = Mage::getModel('customer/group')->load($name, 'customer_group_code');
        if (!$group->getId())
        {
            $group = Mage::getModel('customer/group')->addData(array(
                'customer_group_code' => $name,
                'tax_class_id' => 3
            ))->save();
        }

        return $group;
    }

    public function getWebsiteIds($withAdmin)
    {
        if (!isset($this->_websiteIds)) {
            $this->_websiteIds = array();
            foreach (Mage::app()->getWebsites($withAdmin) as $website) {
                $this->_websiteIds[] = $website->getId();
            }
        }

        return $this->_websiteIds;
    }

    public function createUserPermissionsRule($name, $permission, array $resources, $action = 'deny', $sortOrder = 0, $websiteIds = array(0), array $additionalParams = array())
    {
        $data = array(
            'scope' => 'admin',
            'simple_action' => $action,
            'name' => $name,
            'description' => '',
            'is_active' => 1,
            'sort_order' => $sortOrder,
            'website_ids' => $websiteIds
        );
        $data = array_merge($data, $additionalParams);

        $rule = Mage::getModel('rolespermissions/rule')->setData($data); /* @var Blackbox_RolesPermissions_Model_Rule $rule */

        $this->_addAdminActions($rule, $resources);
        $this->_addUserPermissionsCondition($rule, $permission);

        $rule->save();
    }

    protected function _addCategoryActions($rule, $hideCategories)
    {
        $names = array();
        $urls = array();

        foreach($hideCategories as $group) {
            if (false !== strpos($group, '.html')) {
                $urls[] = $group;
            } else {
                $names[] = $group;
            }
        }

        $combine = $rule->getActions()->setAggregator('any');

        if (count($names) > 0) {
            $this->_addCondition($combine, 'rolespermissions/rule_condition_category', array(
                'operator' => '()',
                'attribute' => 'name',
                'value' => implode(', ', $names)
            ));
        }

        if (count($urls) > 0) {
            $this->_addCondition($combine, 'rolespermissions/rule_condition_category', array(
                'operator' => '()',
                'attribute' => 'url_path',
                'value' => implode(', ', $urls)
            ));
        }
    }

    protected function _addCategoryConditions($rule, $groupId)
    {
        $combine = $rule->getConditions();

        $this->_addCondition($combine, 'rolespermissions/rule_condition_customer', array(
           'operator' => '==',
            'attribute' => 'group_id',
            'value' => $groupId
        ));
    }

    protected function _addAdminActions($rule, $resources)
    {
        $actions = $rule->getActions();
        foreach ($resources as $resource) {
            $this->_addCondition($actions, 'rolespermissions/rule_condition_admin', array(
               'attribute' => $resource
            ));
        }
    }

    protected function _addUserPermissionsCondition($rule, $permission)
    {
        $conditions = $rule->getConditions();
        $this->_addCondition($conditions, 'rolespermissions/rule_condition_customer_user_permissions', array(
            'operator'  => '!=',
            'value'     => $permission,
        ));
    }

    protected function _getCategoryDescription($groupName, $hideCategories)
    {
        return 'Rule for customer group "' . $groupName . '".
Hide categories: ' . implode(', ', $hideCategories) . '.';
    }

    protected function _addCondition($parent, $type, array $options)
    {
        $condition = Mage::getModel($type)->addData($options);
        $parent->addCondition($condition);
    }
}