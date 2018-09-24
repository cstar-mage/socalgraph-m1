<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Color extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$color_load = Mage::getModel('onlinedesign/color')->load($row['color_id']);
		$html = '
			<span style="margin: 5px auto; display: block; height: 23px; width: 50px; background: #'.$color_load->getHex().'; border: 1px solid #CCCCCC;"></span>
		';
		return $html;
	}
}