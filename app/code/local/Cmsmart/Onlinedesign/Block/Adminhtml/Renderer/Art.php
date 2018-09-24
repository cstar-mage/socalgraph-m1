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
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Art extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$result = '';

		$helper = Mage::helper('onlinedesign/data');
		$list = $helper->nbdesigner_read_json_setting($helper->plugin_path_data().DS. 'arts.json');
		$art_id = $row['art_id'];
		$art_index_found = $helper->indexFound($art_id, $list, "id");
		if (isset($list[$art_index_found])) {
			$art_data = $list[$art_index_found];
			$result = '<img src="'. Mage::getBaseUrl('media') . '/nbdesigner/'  . $art_data['path'].'" width="100" />';
		}
		
    	return $result;
    }

}