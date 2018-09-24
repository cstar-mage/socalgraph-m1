<?php

class Blackbox_Notification_Adminhtml_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Email Notifications'));

        Mage::register('current_blackbox_notification_rule', Mage::getModel('blackbox_notification/rule'));
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            Mage::registry('current_blackbox_notification_rule')->load($id);
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('blackbox_notification/rule')
            ->_addBreadcrumb(Mage::helper('blackbox_notification')->__('Email Notifications'), Mage::helper('blackbox_notification')->__('Email Notifications'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Email Notifications'))->_title($this->__('Notifications Rules'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('blackbox_notification')->__('Notifications'), Mage::helper('blackbox_notification')->__('Email Notifications'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blackbox_notification/rule');
        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blackbox_notification')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        //$model->getActions()->setJsFormObject('rule_actions_fieldset');

        Mage::register('current_blackbox_notification_rule', $model);

        $this->_initAction()->getLayout()->getBlock('rule_edit')
             ->setData('action', $this->getUrl('*/*/save'));

        $this
            ->_addBreadcrumb(
                $id ? Mage::helper('blackbox_notification')->__('Edit Rule')
                    : Mage::helper('blackbox_notification')->__('New Rule'),
                $id ? Mage::helper('blackbox_notification')->__('Edit Rule')
                    : Mage::helper('blackbox_notification')->__('New Rule'))
            ->renderLayout();

    }

    /**
     * Promo quote save action
     *
     */
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                /** @var $model Blackbox_Notification_Model_Rule */
                $model = Mage::getModel('blackbox_notification/rule');
                Mage::dispatchEvent(
                    'blackbox_notification_controller_notification_prepare_save',
                    array('request' => $this->getRequest()));
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('blackbox_notification')->__('Wrong rule specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $model->loadPost($data);

                $useAutoGeneration = (int)!empty($data['use_auto_generation']);
                $model->setUseAutoGeneration($useAutoGeneration);

                $session->setPageData($model->getData());

                $model->save();
                $session->addSuccess(Mage::helper('blackbox_notification')->__('The rule has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*/new');
                }
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('blackbox_notification')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('blackbox_notification/rule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blackbox_notification')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('blackbox_notification')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('blackbox_notification')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('blackbox_notification/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('blackbox_notification/rule'))
            ->setPrefix('actions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function newConditionBlockHtmlAction()
    {
        $this->getResponse()->setBody($this->getNewConditionBlockHtml());
    }

    protected function getNewConditionBlockHtml()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');

        $type = $this->getRequest()->getParam('type');
        $model = Mage::getModel('blackbox_notification/rule');
        $model->setType($type);

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('blackbox_notification')->__('Conditions')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('blackbox_notification')->__('Apply To'),
            'title' => Mage::helper('blackbox_notification')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        return $fieldset->getChildrenHtml();
    }

    protected function getNewTemplateSelectOptionsHtml()
    {
        $type = $this->getRequest()->getParam('type');
        $model = Mage::getModel('blackbox_notification/rule'); /* @var Blackbox_Notification_Model_Rule $model */
        $model->setType($type);

        $options = Mage::getModel('adminhtml/system_config_source_email_template')->setPath($model->getEmailTemplateNode())->toOptionArray();
        $html = '';
        foreach($options as $option) {
            $html .= '<option value="' . $option['value'] . '">' . htmlentities($option['label']) . '</option>';
        }
        return $html;
    }

    public function getNewTypeSettingsJsonAction()
    {
        $settings = array(
            'condition_block_html' => $this->getNewConditionBlockHtml(),
            'template_select_options_html' => $this->getNewTemplateSelectOptionsHtml()
        );

        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->setBody(json_encode($settings));
    }

    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    /**
     * Returns result of current user permission check on resource and privilege
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/notification/rule');
    }
}
