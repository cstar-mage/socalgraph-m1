<?php

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_OnlinedesignController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function operatorAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function primaryAction()
    {
		$primary_str = "primary";
		$pid = $this->getRequest()->getParam('pid');
		$template_folder = $this->getRequest()->getParam('template_folder');
		
		$admin_path = Mage::getBaseDir("media").'/nbdesigner/admindesign/'.$pid;
		
		$collection_primary = Mage::getModel('onlinedesign/templates')
							->getCollection() 
							->addFieldToFilter('folder', $primary_str)
							->addFieldToFilter('product_id', $pid);
		$collection_extra = Mage::getModel('onlinedesign/templates')
							->getCollection() 
							->addFieldToFilter('folder', $template_folder)
							->addFieldToFilter('product_id', $pid);
		
		$primary = array();
		$current = array();
		
		foreach($collection_primary as $c){
			$primary["primary_template_id"] = $c->getId();
			$primary["primary_user_id"] = $c->getUserId();
			break;
		}
		
		foreach($collection_extra as $c){
			$current["extra_template_id"] = $c->getId();
			$current["extra_user_id"] = $c->getUserId();
			$current["extra_folder"] = $c->getFolder();
			break;
		}
		
		/* set primary for current template */
		$model = Mage::getModel('onlinedesign/templates');
		$model->setFolder($primary_str)->setUserId($primary["primary_user_id"])->setPriority(1)->setId($current["extra_template_id"]);
		$model->save();
		/* set extra for primary template */
		$model = Mage::getModel('onlinedesign/templates');
		$model->setFolder($current["extra_folder"])->setUserId($current["extra_user_id"])->setPriority(0)->setId($primary["primary_template_id"]);
		$model->save();
		
		/* rename primary to primary_ */
		$rename = rename($admin_path."/".$primary_str, $admin_path."/".$primary_str."_");
		/* rename template_folder to primary */
		$rename = rename($admin_path."/".$template_folder, $admin_path."/".$primary_str);
		/* rename primary_ to template_folder */
		$rename = rename($admin_path."/".$primary_str."_", $admin_path."/".$template_folder);
		
		echo "successfully";
    }
	
	public function delete_templateAction()
    {
		$pid = $this->getRequest()->getParam('pid');
		$template_folder = $this->getRequest()->getParam('template_folder');
		
		$collection_extra = Mage::getModel('onlinedesign/templates')
							->getCollection() 
							->addFieldToFilter('folder', $template_folder)
							->addFieldToFilter('product_id', $pid);
		
		$current = array();
		
		foreach($collection_extra as $c){
			$current["extra_template_id"] = $c->getId();
			break;
		}
		
		/* set primary for current template */
		$model = Mage::getModel('onlinedesign/templates');
		$model->setId($current["extra_template_id"]);
		$model->delete();
		
		echo "successfully";
    }

	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        /* $this->renderLayout(); */
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_grid')->toHtml()
		);
    }
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
				
		/* $model  = Mage::getModel('onlinedesign/onlinedesign')->load($id); */
		$model  = Mage::getModel('catalog/product')->load($id);

		if ($model->getId() || $id != 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
					
			$onlinedesignIds = Mage::getModel('onlinedesign/onlinedesign')->_getOnlineDesignByProduct($id);
			$onlinedesignId = 0;
			$model_design = null;
			if(sizeof($onlinedesignIds)) {
				foreach ($onlinedesignIds as $od) {
					$onlinedesignId = $od->getId();
					$model_design  = Mage::getModel('onlinedesign/onlinedesign')->load($onlinedesignId);
					break;
				}
			}
			
			Mage::register('onlinedesign_data', $model_design);

			$this->loadLayout();
			$this->_setActiveMenu('onlinedesign/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('onlinedesign')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$pid = $this->getRequest()->getParam('id');
	  		
			$onlinedesignIds = Mage::getModel('onlinedesign/onlinedesign')->_getOnlineDesignByProduct($pid);

			if(sizeof($onlinedesignIds)) {
				foreach ($onlinedesignIds as $od) {
					$od->delete();
				}
			}
				
	  		$data_actual = array();
			
			$data_actual['status'] = $data['_nbdesigner_enable']; 
			$dpi = $data['_nbdesigner_dpi']; 
			if(!is_numeric($dpi) || $dpi == "") $dpi = 96;
			$dpi = abs($dpi);
			$data_actual['dpi'] = $dpi;

			$_designer_setting = array();
			
			for($i=0; $i<count($data['_designer_setting']); $i++){
				if(isset($data['_designer_setting'][$i]["orientation_name"]) && $data['_designer_setting'][$i]["orientation_name"] != "") {
					$_designer_setting[] = $data['_designer_setting'][$i];
				}		
			}
			
			/* $setting = serialize($data['_designer_setting']);  */
			$setting = serialize($_designer_setting); 
			
			$data_actual['content_design'] = $setting;	
			
			$option = serialize($data['_nbdesigner_option']); 
			$data_actual['nbdesigner_option'] = $option;	
			
			$data_actual['product_id'] = $pid;	
			
			$model = Mage::getModel('onlinedesign/onlinedesign');		
			$model->setData($data_actual);
			
			try {
				/* if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				} */	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('onlinedesign')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getProductId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('onlinedesign')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('onlinedesign/onlinedesign');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $onlinedesignIds = $this->getRequest()->getParam('onlinedesign');
        if(!is_array($onlinedesignIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($onlinedesignIds as $onlinedesignId) {
                    $onlinedesign = Mage::getModel('onlinedesign/onlinedesign')->load($onlinedesignId);
                    $onlinedesign->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($onlinedesignIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $onlinedesignIds = $this->getRequest()->getParam('onlinedesign');
        if(!is_array($onlinedesignIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($onlinedesignIds as $onlinedesignId) {
                    $onlinedesign = Mage::getSingleton('onlinedesign/onlinedesign')
                        ->load($onlinedesignId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($onlinedesignIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'onlinedesign.csv';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'onlinedesign.xml';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_onlinedesign_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}