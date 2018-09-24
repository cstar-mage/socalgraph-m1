<?php
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'functions.php';
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'upload.php';

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_GooglefontController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/googlefont')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Fonts Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('onlinedesign/font')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('font_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('onlinedesign/googlefont');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Font Manager'), Mage::helper('adminhtml')->__('Font Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Font News'), Mage::helper('adminhtml')->__('Font News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_googlefont_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_googlefont_edit_tabs'));

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
			
			try {
				/************************************************/
				$helper = Mage::helper('onlinedesign/data');
				/* $name = $this->getRequest()->getParam('name'); */
				$name = $data_db["nbdesigner_google_font_seach"];
				zend_debug::dump($data_db);die;
				/* $id = $this->getRequest()->getParam('id'); */
				$path_font = $helper->plugin_path_data() .DS. 'googlefonts.json';
				$list = $helper->nbdesigner_read_json_setting($path_font);
				$id = count($list);	
				$data = array("name" => $name, "id" => $id);
				$helper->nbdesigner_update_json_setting($path_font, $data, $id);
				
				$data_db = array();
				$data_db['name'] = $name;
				$data_db['font_type'] = "google";
				$data_db['category'] = "0"; /* google category */
				$model = Mage::getModel('onlinedesign/font');	
				$model->setData($data_db);
				$model->save();
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
 
	// public function deleteAction() {
		// $id = $this->getRequest()->getParam('id');
		// if( $id > 0 ) {
			// try {
				// $model = Mage::getModel('onlinedesign/font');
				// $model->setId($id)->delete();		
				// $this->nbdesigner_delete_font($id);
					 
				// Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Art was successfully deleted'));
				// $this->_redirect('*/*/');
			// } catch (Exception $e) {
				// Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				// $this->_redirect('*/*/edit', array('id' => $id));
			// }
		// }
		// $this->_redirect('*/*/');
	// }

    public function massDeleteAction() {
        $productdesignIds = $this->getRequest()->getParam('onlinedesign');
		$gfont_arr = array();
		if(!is_array($productdesignIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                for ($i=0; $i<count($productdesignIds); $i++) {
					$gfont_arr[] = $this->getGFontName($productdesignIds[$i]);
				}
				for ($i=0; $i<count($gfont_arr); $i++) {
					$this->nbdesigner_delete_font($gfont_arr[$i]);
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
                    $productdesign = Mage::getSingleton('onlinedesign/font')
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
        $fileName   = 'font.csv';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_font_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'font.xml';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_font_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('admin/onlinedesign/fonts');
    }

	public function nbdesigner_delete_font($name) {
		$helper = Mage::helper('onlinedesign/data');
        $path = $helper->plugin_path_data(). DS . 'googlefonts.json';
        $helper->nbdesigner_delete_gfont_json_setting($path, $name);
    }
	
	public function getGFontName($id) {
		$helper = Mage::helper('onlinedesign/data');
        $path = $helper->plugin_path_data(). DS . 'googlefonts.json';
        
		$list = $helper->nbdesigner_read_json_setting($path);
		foreach ($list as $val) {
			if($val["id"] == $id) {
				return $val["name"];
			}
		}
		return;
    }
}