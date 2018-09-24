<?php
/* require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'functions.php';
require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'upload.php'; */

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_ColorController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Color Manager'), Mage::helper('adminhtml')->__('Color Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('onlinedesign/color')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('color_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('onlinedesign/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Color Manager'), Mage::helper('adminhtml')->__('Category Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Color News'), Mage::helper('adminhtml')->__('Category News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_color_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_color_edit_tabs'));

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
		if ($data_db = $this->getRequest()->getPost()) {
			$id = $this->getRequest()->getParam('id'); 

			$collection = Mage::getModel('onlinedesign/color')->getCollection()
						->addFieldToFilter('hex', $data_db['hex']);
			if(sizeof($collection)) {
				Mage::getSingleton('adminhtml/session')->addError($this->__("The color %s you added exists. Please add other color.", $data_db["hex"]));
				$this->_redirect('*/*/edit', array('id' => $id));
				return;
			}
			
			$model = Mage::getModel('onlinedesign/color');		
			$model->setData($data_db)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				
				$model->save();
								
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('onlinedesign')->__('Color has been saved successfully'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
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
		$id = $this->getRequest()->getParam('id');
		if( $id > 0 ) {
			try {
				$model = Mage::getModel('onlinedesign/color');
				 
				$model->setId($id)
					->delete();
					
				/* delete color */
				/* $this->removeColor($id); */
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Color has been deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $productdesignIds = $this->getRequest()->getParam('onlinedesign');
        if(!is_array($productdesignIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($productdesignIds as $productdesignId) {
                    $onlinedesign = Mage::getModel('onlinedesign/color')->load($productdesignId);
                    $onlinedesign->delete();
					/* delete color */
					/* $this->removeColor($productdesignId); */
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($productdesignIds)
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
        $productdesignIds = $this->getRequest()->getParam('onlinedesign');
        if(!is_array($productdesignIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productdesignIds as $productdesignId) {
                    $onlinedesign = Mage::getSingleton('onlinedesign/color')
                        ->load($productdesignId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($productdesignIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'color.csv';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_color_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'color.xml';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_color_grid')
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

	
	protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/onlinedesign/color');
    }
	
	public function add_colorAction() {
		$model = Mage::getModel('onlinedesign/color');

		$data_db = array();		
		$data_db["color_name"] = trim($this->getRequest()->getParam('color_name'));
		$data_db["hex"] = trim($this->getRequest()->getParam('hex'));
		$model->setData($data_db);
		
		$result = array();
		
		$collection = Mage::getModel('onlinedesign/color')->getCollection()
			->addFieldToFilter("hex", $data_db["hex"]);
		
		if(sizeof($collection)) {
			$result["error"] = 1;
			$result["message"] = $this->__("This color exists. Please change other color...");
			echo json_encode($result);
			exit();
		}
		
		try {
			$model->save();
			$result["error"] = 0;
			$result["color_id"] = $model->getId();
		} catch (Exception $e) {
			$result["error"] = 1;
		}
		echo json_encode($result);
		exit();
	}
	
	public function upd_colorAction() {
		$model = Mage::getModel('onlinedesign/color');

		$data_db = array();		
		$data_db["color_name"] = trim($this->getRequest()->getParam('color_name'));
		$data_db["hex"] = trim($this->getRequest()->getParam('hex'), "#");
		$id = $this->getRequest()->getParam('id');
		
		$model->setData($data_db)->setId($id);
		
		$result = array();
		
		$collection = Mage::getModel('onlinedesign/color')->getCollection()
			->addFieldToFilter("hex", $data_db["hex"]);
		
		if(sizeof($collection)) {
			$result["error"] = 1;
			$result["message"] = $this->__("This color exists. Please change other color...");
			echo json_encode($result);
			exit();
		}
		
		try {
			$model->save();
			$result["error"] = 0;
			$result["color_id"] = $model->getId();
		} catch (Exception $e) {
			$result["error"] = 1;
		}
		echo json_encode($result);
		exit();
	}
	
	public function del_colorAction() {
		$model = Mage::getModel('onlinedesign/color');
		$id = $this->getRequest()->getParam('id');

		$result = array();
		try {
			$model->setId($id)->delete();
			$result["error"] = 0;
		} catch (Exception $e) {
			$result["error"] = 1;
		}
		echo json_encode($result);
		exit();
	}
}