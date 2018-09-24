<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Designer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$result = '<span style="font-weight: bold; color: #CDCDCD;text-transform: uppercase;">No Design</span>';
		$status = Mage::helper('onlinedesign/data')->getStatusDesign($row['entity_id']);
		if($status == 1) {
			$result = '<span style="font-weight: bold; text-transform: uppercase;">Has Design</span>';
		}
		
    	return $result;
    }

}