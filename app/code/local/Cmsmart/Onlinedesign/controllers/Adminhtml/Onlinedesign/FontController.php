<?php
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'functions.php';
//require_once dirname(dirname(__FILE__)).DS.'includes' .DS. 'upload.php';

class Cmsmart_Onlinedesign_Adminhtml_Onlinedesign_FontController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('onlinedesign/font')
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
			$this->_setActiveMenu('onlinedesign/font');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Font Manager'), Mage::helper('adminhtml')->__('Font Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Font News'), Mage::helper('adminhtml')->__('Font News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('onlinedesign/adminhtml_font_edit'))
				->_addLeft($this->getLayout()->createBlock('onlinedesign/adminhtml_font_edit_tabs'));

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
			
			if(!$id){
				if(!isset($_FILES['filename']['name']) || $_FILES['filename']['name'] == '' || $_FILES['filename']['size'] <= 0) {
					Mage::getSingleton('adminhtml/session')->addError("Please attach font");
					$this->_redirect('*/*/new');
					return;
				} else {
					$data_db["title"] = $_FILES['filename']['name'];
				}
			}
			
			$model = Mage::getModel('onlinedesign/font');	
			
			$alias = 'nbfont' . substr(md5(rand(0, 999999)), 0, 10);
			$data_db['alias'] = $alias;
			$model->setData($data_db)
				->setId($id);
			
			try {
				$model->save();
				$font_id = $model->getId();	
				/************************************************/
			
				$notice = '';
				$cats = ["0"];
				$current_font_cat_id = 0;
				$update = false;
				$list = $helper->nbdesigner_read_json_setting($helper->plugin_path_data(). DS . 'fonts.json');	
				$cat = $helper->nbdesigner_read_json_setting($helper->plugin_path_data(). DS . 'font_cat.json');
				$data_font_google = $helper->nbdesigner_read_json_setting($helper->plugin_path_data(). DS . 'googlefonts.json');
				$list_all_google_font = $helper->nbdesigner_get_list_google_font();
				$current_cat = filter_input(INPUT_GET, "cat_id", FILTER_VALIDATE_INT);
				if (is_array($cat))
					$current_font_cat_id = sizeof($cat);
				
				if ($id) {
					/* $font_id = $model->getId(); */
					$font_index_found = $helper->indexFound($font_id, $list, "id");
					$update = true;
					if (isset($list[$font_index_found])) {
						$font_data = $list[$font_index_found];
						$cats = $font_data->cat;
					}
				}
			  
				$font = array();
				$font['name'] = $data_db["title"];
				$font['alias'] = $alias;
				$font['id'] = $font_id;
				$font['cat'] = $cats;
				
				$arr_cat = array();
				$arr_cat[] = "0"; 
				
				if (isset($data_db["category"]))
					$arr_cat[] = $data_db["category"];

				$font['cat'] = $arr_cat;
				
				if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '' && $_FILES['filename']['size']) {
					
					$font['type'] = $helper->nbdesigner_get_extension($_FILES['filename']['name']);              
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
					$uploader->setAllowedExtensions(array('woff', 'ttf'));
					$uploader->setAllowRenameFiles(false);
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$uploader->save($upload_path, $_FILES['filename']['name']);

					$font['file'] = $helper->plugin_path_data().DS.$year.DS.$month.DS.$_FILES['filename']['name'];
					$font['url'] = $helper->getMediaPath()."/".$year."/".$month."/".$_FILES['filename']['name'];
                    $font['path'] = $year."/".$month."/".$_FILES['filename']['name'];
	
				} else {
					$font_index_found = $helper->indexFound($font_id, $list, "id");
					if (isset($list[$font_index_found])) {
						$font_data = $list[$font_index_found];	
						$font['file'] = $font_data["file"];
						$font['url'] = $font_data["url"];
                        $font['path'] = $font_data['path'];
					}
				}
				
				if ($update) {
					$helper->nbdesigner_update_list_fonts($font, "update", $font_id);
				} else {
					$helper->nbdesigner_update_list_fonts($font, "add", $font_id);
				}
				
				/************************************************/
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('onlinedesign')->__('Font was successfully saved'));
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
				$model = Mage::getModel('onlinedesign/font');
				$model->setId($id)->delete();		
				/* delete font */
				$this->nbdesigner_delete_font($id);
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Font was successfully deleted'));
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
                    $productdesign = Mage::getModel('onlinedesign/font')->load($productdesignId);
                    $productdesign->delete();
					/* delete font */
					$this->nbdesigner_delete_font($productdesignId);
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
	
	public function nbdesigner_delete_font($id) {
		$helper = Mage::helper('onlinedesign/data');
       /*  $type = $_POST['type'];
        if ($type == 'custom') { */
            $path = $helper->plugin_path_data(). DS . 'fonts.json';
            $list = $helper->nbdesigner_read_json_setting($path);
			$id_found = $helper->indexFound($id, $list, "id");
            $file_font = $list[$id_found]["file"];
            unlink($file_font);
        /* } else
            $path = $helper->plugin_path_data().DS. 'data' .DS. 'googlefonts.json'; */
        $helper->nbdesigner_delete_json_setting($path, $id);

    }
	
	public function googlefontAction() {
		die("abc");
		$helper = Mage::helper('onlinedesign/data');
        $name = $_POST['name'];
        $id = $_POST['id'];
        $path_font = $helper->plugin_path_data() .DS. 'googlefonts.json';
        $data = array("name" => $name, "id" => $id);
        $helper->nbdesigner_update_json_setting($path_font, $data, $id);
        echo 'success';
		die();
    }
}