<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Imagesdesign extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
		$helper = Mage::helper('onlinedesign/data');
		$pid = $this->getRequest()->getParam('id');
		$result = "";
    	$path_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'nbdesigner/admindesign/'.$pid.'/'.$row['folder'].'/preview/';
    	$path = Mage::getBaseDir("media").DS.'nbdesigner'.DS.'admindesign'.DS.$pid.DS.$row['folder'].DS.'preview'.DS;
		
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				$files[] = $entry;
			}
			$images=preg_grep('/\.png$/i', $files); 
			foreach($images as $image){
				$result .= '<img width="100px" style="margin-right: 15px; border: 1px solid #ccc" src="'.$path_url.DS.$image.'" border="0" />';
			}
			closedir($handle);
		}
		
    	return $result;
    }

}