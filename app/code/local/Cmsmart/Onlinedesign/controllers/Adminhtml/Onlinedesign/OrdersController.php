<?php

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_OrdersController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/orders')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function viewAction() {
		$this->loadLayout();
        $this->renderLayout();
	}

	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        /* $this->renderLayout(); */
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('onlinedesign/adminhtml_orders_grid')->toHtml()
		);
    }
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('order_id');
				
		/* $model  = Mage::getModel('onlinedesign/onlinedesign')->load($id); */
		$model  = Mage::getModel('sales/order')->load($id);

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
			$this->_setActiveMenu('onlinedesign/orders');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_orders_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_orders_edit_tabs'));

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
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
			
			$pid = $this->getRequest()->getParam('id');
	  		
			$onlinedesignIds = Mage::getModel('onlinedesign/onlinedesign')->_getOnlineDesignByProduct($pid);

			if(sizeof($onlinedesignIds)) {
				foreach ($onlinedesignIds as $od) {
					$od->delete();
				}
			}
				
	  		$data_actual = array();
			
			$data_actual['status'] = $data['status']; 
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