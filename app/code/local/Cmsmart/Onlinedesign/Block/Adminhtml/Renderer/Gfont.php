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
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Gfont extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$result = '';
    	$result = '
			<link href="https://fonts.googleapis.com/css?family='.$row['name'].'" rel="stylesheet" type="text/css">
		';
		$result .= '<span style="font-family: '.$row['name'].', sans-serif ;font-size: 30px;">Abc Xyz</span>';
		
    	return $result;
    }

}