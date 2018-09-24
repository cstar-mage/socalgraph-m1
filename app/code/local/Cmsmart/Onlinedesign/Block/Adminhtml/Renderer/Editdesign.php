<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Editdesign extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
		$helper = Mage::helper('onlinedesign/data');
		$pid = $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($pid);	
		$priority = "primary";
		if($row['folder'] != "primary"){
			$priority = "extra";
		}
		
		$primary_url = Mage::helper('adminhtml')->getUrl('*/*/primary').";".$pid.";".$row['folder'];			
		$delete_url = Mage::helper('adminhtml')->getUrl('*/*/delete_template').";".$pid.";".$row['folder'];			
		$link_admindesign = $product->getProductUrl()."?product_id=".$pid.'&template_folder='.$row['folder'].'&priority='.$priority.'&task=edit_template';
		
		$result = '<a href="'.$link_admindesign.'" target="_blank">Edit</a><br />';		
		if($row['folder'] != "primary"){
			$result .= '<a onclick="primaryBtn(this); return false;" href="javascript:void(0)" target="_blank" data-rev="'.$primary_url.'">Primary</a><br />';		
			$result .= '<a onclick="delete_template(this); return false;" href="javascript:void(0)" target="_blank" data-rev="'.$delete_url.'">Delete</a><br />';		
		}
		return $result;
    }

}