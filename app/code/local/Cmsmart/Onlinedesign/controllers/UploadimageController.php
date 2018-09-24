<?php
class Cmsmart_Onlinedesign_UploadimageController extends Mage_Core_Controller_Front_Action
{
	public function uploadAction()
    {
		$helper = Mage::helper('onlinedesign/data');
        $filename = $this->getRequest()->getParam('filename');
        $data_image = $this->getRequest()->getParam('data_image');  

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
					
		$temp = explode(';base64,', $data_image);
        $buffer = base64_decode($temp[1]);
		$full_name = $upload_path . '/' . $filename;
		$helper->nbdesigner_save_data_to_image($full_name, $buffer);
		$image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/nbdesigner/'.$year."/".$month."/".$filename;
		$response = array();
		$response['mes'] = 'success';
		$response['url'] = $image_url;
		echo json_encode($response);
		die;
	}
	
	public function upload_existsAction()
    {
		$helper = Mage::helper('onlinedesign/data');
        $source_file = $this->getRequest()->getParam('filename');

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
		
		$response = array();
		$response['mes'] = '';
		$response['url'] = '';
			
		if($source_file) {
			$filename = basename($source_file);
			$data = base64_encode(file_get_contents($source_file));
			$buffer = base64_decode($data);
			$full_name = $upload_path . '/' . $filename;
			$helper->nbdesigner_save_data_to_image($full_name, $buffer);
			$image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/nbdesigner/'.$year."/".$month."/".$filename;
			$response['mes'] = 'success';
			$response['url'] = $image_url;
		}
		
		echo json_encode($response);
		die;
	}
	
	public function upload_myimagesAction()
    {
		$pid = $this->getRequest()->getParam('id');
		$product_id_path = Mage::getBaseDir("media").DS.'nbdesigner'.DS.'productimages'.DS.$pid;
		if(!is_dir($product_id_path)){
			mkdir($product_id_path, 0777);
		}
		
		$filetype = array('jpeg','jpg','png','PNG','JPEG','JPG');
		foreach ($_FILES as $key ){
			$name =time().$key['name'];
			/* $path='local_cdn/'.$name; */
			$path = $product_id_path.DS.$name;
			$file_ext =  pathinfo($name, PATHINFO_EXTENSION);
			if(in_array(strtolower($file_ext), $filetype)){
				if($key['name']<1000000) {
					@move_uploaded_file($key['tmp_name'],$path);
					echo $name;
				} else {
					echo "FILE_SIZE_ERROR";
				}
			} else {
				echo "FILE_TYPE_ERROR";
			} // Its simple code.Its not with proper validation.
		}
	}
}