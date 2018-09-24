<?php
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'functions.php';
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'upload.php';

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_ArtController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/clipart')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Arts Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('onlinedesign/art')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('art_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('onlinedesign/clipart');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Art Manager'), Mage::helper('adminhtml')->__('Art Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Art News'), Mage::helper('adminhtml')->__('Art News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_art_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_art_edit_tabs'));

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
		$id = $this->getRequest()->getParam('id');
		if ($data_db = $this->getRequest()->getPost()) {
			$fName = explode(".", $_FILES['filename']['name']);
			if($data_db["title"] == "") {
				$data_db["title"] = trim($fName[0]);
			}
			
			/* if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
			} */
				
			$model = Mage::getModel('onlinedesign/art');	
			
			$model->setData($data_db)
				->setId($id);
			
			try {
				$model->save();
					
				/************************************************/
				$notice = '';
				$current_art_cat_id = 0;
				$art_id = 0;
				$update = false;
				$cats = ["0"];
				$list = $helper->nbdesigner_read_json_setting($helper->plugin_path_data().DS. 'arts.json');
				$cat = $helper->nbdesigner_read_json_setting($helper->plugin_path_data().DS.'art_cat.json');

				if (is_array($cat))
					$current_art_cat_id = sizeof($cat);
				if ($id) {
					$art_id = $model->getId();
					
					$art_index_found = $helper->indexFound($art_id, $list, "id");
					
					$update = true;
					if (isset($list[$art_index_found])) {
						$art_data = $list[$art_index_found];	
						$cats = $art_data->cat;
					}
				}				
				
			
				$art = array();
				$art['name'] = $data_db["title"];
				$art['id'] = $model->getId();
				$art['cat'] = $cats;
				
				$arr_cat = array();
				$arr_cat[] = "0"; 
				
				if (isset($data_db["category"]))
					$arr_cat[] = $data_db["category"];
				
				$art['cat'] = $arr_cat;

				if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '' && $_FILES['filename']['size']) {
					$uploaded_file_name = basename($_FILES['filename']['name']);
					
					$path1 = Mage::getBaseDir("media").DS.'nbdesigner';
					if(!is_dir($path1)){
						mkdir($path1, 0777, TRUE);
					}
					
					$date 	= new DateTime();
					$year	= $date->format('Y');
					
					$path2 = $path1.DS.$year;
					if(!is_dir($path2)){
						mkdir($path2, 0777);
					}
					
					$month 	= $date->format('m');
					$upload_path = $path2.DS.$month;
					if(!is_dir($upload_path)){
						mkdir($upload_path, 0777);
					}
					
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
					$uploader->setAllowedExtensions(array('svg'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$uploader->save($upload_path, $_FILES['filename']['name']);

					$art['file'] = $helper->plugin_path_data().DS.$year.DS.$month.DS.$_FILES['filename']['name'];
					$art['url'] = $helper->getMediaPath().DS.$year.DS.$month.DS.$_FILES['filename']['name'];
                    $art['path'] = "$year/$month/{$_FILES['filename']['name']}";
				} else {
					$art_id = $model->getId();
					$art_index_found = $helper->indexFound($art_id, $list, "id");
					if (isset($list[$art_index_found])) {
						$art_data = $list[$art_index_found];	
						$art['file'] = $art_data["file"];
						$art['url'] = $art_data["url"];
                        $art['path'] = $art_data['path'];
					}
				}
				
				if ($update) {
					$helper->nbdesigner_update_list_arts($art, $art_id, "update");
				} else {
					$art_id = $model->getId();
					$helper->nbdesigner_update_list_arts($art, $art_id, "add");
				}
				
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
				$model = Mage::getModel('onlinedesign/art');
				$model->setId($id)->delete();		
				/* delete art */
				$this->nbdesigner_delete_art($id);
					 
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
                    $productdesign = Mage::getModel('onlinedesign/art')->load($productdesignId);
                    $productdesign->delete();
					/* delete art */
					$this->nbdesigner_delete_art($productdesignId);
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
                    $productdesign = Mage::getSingleton('onlinedesign/art')
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
        $fileName   = 'art.csv';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_art_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'art.xml';
        $content    = $this->getLayout()->createBlock('onlinedesign/adminhtml_art_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('admin/onlinedesign/arts');
    }
	
	public function nbdesigner_delete_art($id) {
		$helper = Mage::helper('onlinedesign/data');
        $path = $helper->plugin_path_data() .DS. 'arts.json';
        $list = $helper->nbdesigner_read_json_setting($path);
		$id_found = $helper->indexFound($id, $list, "id");
        $file_art = $list[$id_found]["file"];
        unlink($file_art);
        $helper->nbdesigner_delete_json_setting($path, $id);

    }
}