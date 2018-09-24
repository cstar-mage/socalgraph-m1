<?php
/* require_once 'plugins'.DS.'web-to-print-online-designer'.DS.'includes'.DS.'class-qrcode.php'; */
require_once Mage::getBaseDir('lib') .DS. 'Onlinedesign'.DS.'includes'.DS.'class-qrcode.php';
/* require_once 'plugins'.DS.'web-to-print-online-designer'.DS.'includes'.DS.'class.nbdesigner.php'; */
require_once Mage::getBaseDir('lib') .DS. 'Onlinedesign'.DS.'includes'.DS.'class.nbdesigner.php';
/* require_once 'plugins'.DS.'web-to-print-online-designer'.DS.'utils'.DS.'class.helper.php'; */
require_once Mage::getBaseDir('lib') .DS. 'Onlinedesign'.DS.'includes'.DS.'class.helper.php';
require_once Mage::getBaseDir('lib') .DS. 'Onlinedesign'.DS.'includes'.DS.'class-util.php';

class Cmsmart_Onlinedesign_IndexController extends Mage_Core_Controller_Front_Action
{
	public function designAction()
    {
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('onlinedesign/onlinedesign')->setTemplate('onlinedesign/nbdesigner-frontend-template.phtml')->toHtml()
		);
	}
	
	public function nbdesigner_get_product_infoAction()
    {
		$nbdesigner   =   new Nbdesigner_Plugin();
		$action = $this->getRequest()->getParam('action');
		if($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_get_art){
			$nbdesigner->nbdesigner_get_art();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_customer_upload){
			$nbdesigner->nbdesigner_customer_upload();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_copy_image_from_url){
			$nbdesigner->nbdesigner_copy_image_from_url();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_get_qrcode){
			$nbdesigner->nbdesigner_get_qrcode();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_get_pattern){
			$nbdesigner->nbdesigner_get_pattern();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbd_save_customer_design){
			$nbdesigner->nbd_save_customer_design();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_editor_html){
			$nbdesigner->nbd_save_customer_design();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_load_admin_design){
			$nbdesigner->nbdesigner_load_admin_design();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_get_product_info){
			$nbdesigner->nbdesigner_get_product_info();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_save_design_to_pdf){
			$nbdesigner->nbdesigner_save_design_to_pdf();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_get_font){
			$nbdesigner->nbdesigner_get_font();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_delete_font){
			$nbdesigner->nbdesigner_delete_font();
		} elseif($action == Cmsmart_Onlinedesign_Model_Events::nbdesigner_add_google_font){
			$nbdesigner->nbdesigner_add_google_font();
		}
		die();
	}
	
	public function nbdesigner_detail_orderAction() {
		$helper = Mage::helper('onlinedesign/data');
		$order_id = $this->getRequest()->getParam('order_id');	
		$download_all = $this->getRequest()->getParam('download-all');	
		
        if($order_id){
            if($download_all){
                $zip_files = array();	
				$order = Mage::getModel('sales/order')->load($order_id);
				$order->getAllVisibleItems();
				$orderItems = $order->getItemsCollection()->addAttributeToSelect('*')->load();

				foreach($orderItems as $item){
					$sid = $item->getNbdesignerJson();
					if(($sid != null || $sid != "") && $item->getNbdesignerPid()) {
						//$path = $helper->plugin_path_data().'/designs/' . $sid . '/nb_order/' .$item->getNbdesignerPid();
						$path = $item->getNbdesignerJson();
						$path_arr = json_decode($path);
						$re_path = explode("thumbs", $path_arr[0]);
						$thumb_path_to_zip = $re_path[0]."thumbs"; 
						$list_images = $helper->nbdesigner_list_thumb($thumb_path_to_zip, 1);
						if(count($list_images) > 0){
							foreach($list_images as $key => $image){
								$zip_files[] = $image;
							}
						}
					}
                }
				/* zend_debug::dump($zip_files);
				die; */
                $pathZip = $helper->plugin_path_data().'/downloads/customer-design-'.$order_id.'.zip';
				$nameZip = 'customer-design-'.$order_id.'.zip';
                $helper->zip_files_and_download($zip_files, $pathZip, $nameZip);
            }
        }
    }
}