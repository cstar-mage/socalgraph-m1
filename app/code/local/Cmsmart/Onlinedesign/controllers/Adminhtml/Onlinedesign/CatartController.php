<?php
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'functions.php';
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'upload.php';

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_CatartController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/clipcat')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('onlinedesign/catart')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('cat_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('onlinedesign/cartart');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Category Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Category News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_catart_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_catart_edit_tabs'));

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
		$helper = Mage::helper('onlinedesign/data');
		if ($data_db = $this->getRequest()->getPost()) {
			$id = $this->getRequest()->getParam('id');
			
			$model = Mage::getModel('onlinedesign/catart');		
			$model->setData($data_db)
				->setId($id);
			
			try {
				$model->save();
				
				$path = $helper->plugin_path_data().DS. 'art_cat.json';
				
				$list = $helper->nbdesigner_read_json_setting($path);
				$cat = array(
					'name' => $data_db['title'],
					'id' => $model->getId()
				);
				$helper->nbdesigner_update_json_setting($path, $cat, $cat['id']);
		
				/************************************************/
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('onlinedesign')->__('Art was successfully saved'));
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
				$model = Mage::getModel('onlinedesign/catart');
				 
				$model->setId($id)
					->delete();
					
				$this->nbdesigner_delete_art_cat($id);
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Art was successfully deleted'));
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
                    $productdesign = Mage::getModel('onlinedesign/catart')->load($productdesignId);
                    $productdesign->delete();
					
					$this->nbdesigner_delete_art_cat($productdesignId);
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
        $productdesignIds = $this->getRequest()->getParam('productdesign');
        if(!is_array($productdesignIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productdesignIds as $productdesignId) {
                    $productdesign = Mage::getSingleton('productdesign/catart')
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
        $fileName   = 'catart.csv';
        $content    = $this->getLayout()->createBlock('productdesign/adminhtml_catart_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'catart.xml';
        $content    = $this->getLayout()->createBlock('productdesign/adminhtml_catart_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('admin/productdesign/catart');
    }
	
	public function nbdesigner_delete_art_cat($id) {
		$helper = Mage::helper('onlinedesign/data');
        $path = $helper->plugin_path_data().DS. 'art_cat.json';
        $helper->nbdesigner_delete_json_setting($path, $id, false);
        $art_path = $helper->plugin_path_data().DS. 'arts.json';
        $helper->nbdesigner_update_json_setting_depend($art_path, $id);
    }
}