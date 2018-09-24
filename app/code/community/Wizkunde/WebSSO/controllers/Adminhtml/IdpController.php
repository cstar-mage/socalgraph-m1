<?php

/**
 * Class Wizkunde_WebSSO_Adminhtml_IdpController
 */
class Wizkunde_WebSSO_Adminhtml_IdpController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();

        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('websso/idp');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This mapping no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Identity Provider'));

        $data = Mage::getSingleton('adminhtml/session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('websso', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Identity Provider') : $this->__('New Identity Provider'), $id ? $this->__('Edit Identity Provider') : $this->__('New Identity Provider'))
            ->_addContent($this->getLayout()->createBlock('websso/adminhtml_idp_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    /**
     *
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $model = Mage::getSingleton('websso/idp');
            $model->setData($postData);

            try {
                $model->save();

                // IF its not a new server, we need to first delete old mappings
                $collection = Mage::getResourceModel('websso/claim_collection');
                $collection->addFieldToFilter('server_id', $model->getId());

                foreach($collection->getItems() as $item) {
                    $item->delete();
                }

                foreach($this->transposeMappings($postData) as $mapping) {
                    $mappingModel = Mage::getModel('websso/claim');
                    $mappingModel->setData($mapping);
                    $mappingModel->setServerId($model->getId());
                    $mappingModel->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The mapping has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this identity provider.'));
            }

            Mage::getSingleton('adminhtml/session')->setAttributeData($postData);
            $this->_redirectReferer();
        }
    }

    private function transposeMappings($data)
    {

        $returnData = array();
        if(isset($data['internal']) && is_array($data['internal'])) {
            foreach($data['internal'] as $iterator => $internal) {
                if($internal != '') {
                    if(isset($data['external']) && isset($data['external'][$iterator])) {
                        if($data['external'][$iterator] != '') {
                            $externalData = array(
                                'value' => $data['external'][$iterator],
                                'transform' => $data['transform'][$iterator],
                                'extra' => $data['extra'][$iterator]
                            );

                            $returnData[] = array('internal' => $internal, 'external' => serialize($externalData));
                        }
                    }
                }
            }
        }

        return $returnData;
    }

    public function messageAction()
    {
        $data = Mage::getModel('websso/idp')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('websso/wizkunde_websso_idp')
            ->_title($this->__('Wizkunde WebSSO'))->_title($this->__('Identity Providers'))
            ->_addBreadcrumb($this->__('WebSSO'), $this->__('WebSSO'))
            ->_addBreadcrumb($this->__('Identity Providers'), $this->__('Identity Providers'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('websso/wizkunde_websso_idp');
    }
}