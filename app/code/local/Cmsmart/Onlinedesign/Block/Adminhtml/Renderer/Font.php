<?php
/*------------------------------------
* Product Video Extention
* Author  CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support: http://cmsmart.net/support_ticket/
* Forum - http://cmsmart.net/forum
* Version 3.0.0
-----------------------------------------------------*/
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Font extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
		$model = Mage::getModel('onlinedesign/font')->load($row['font_id']);
    	$alias =  $model->getAlias();
		
		$helper = Mage::helper('onlinedesign/data');
		$list = $helper->nbdesigner_read_json_setting($helper->plugin_path_data(). DS . 'fonts.json');
		$url = "";
		$font_id = $row['font_id'];
		$font_index_found = $helper->indexFound($font_id, $list, "id");
		if (isset($list[$font_index_found])) {
			$font_data = $list[$font_index_found];
			$url = $font_data["url"];
		}
		
    	$result = '
			<style type="text/css">
				@font-face {font-family: '.$alias.';src: local("☺"), url("'.$url.'")}	
			</style>
		';
		$result .= '
			<span style="font-family: '.$alias.', sans-serif;font-size: 30px;">Abc Xyz</span>
		';
		
    	return $result;
    }

}