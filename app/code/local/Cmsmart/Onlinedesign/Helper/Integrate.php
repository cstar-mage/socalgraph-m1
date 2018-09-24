<?php

class Cmsmart_Onlinedesign_Helper_Integrate extends Mage_Core_Helper_Abstract
{
	public function showDesignOrder($_item) {

		$order = Mage::getModel('sales/order')->load($_item->getOrderId());
		$order->getAllVisibleItems();
		$orderItems = $order->getItemsCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('sku', array('eq'=>$_item->getSku()))
			->load();

		$data_design = "";
		foreach($orderItems as $sItem) {
			if(($sItem->getNbdesignerJson() != null || $sItem->getNbdesignerJson() != "") && $sItem->getNbdesignerSku() == $_item->getSku()){
				$data_design = $sItem->getNbdesignerJson();
			}
		}
		
		$helper = Mage::helper('onlinedesign/data');
		
		$html = $helper->getTilteList() . '<br /><div>';
		$list = json_decode($data_design);

		foreach ($list as $img) {
			$src = $helper->nbdesigner_create_secret_image_url($img);
			$html .= '<img width="60" height="60" style="border-radius: 3px; border: 1px solid #ddd; margin-top: 5px; margin-right: 5px; display: inline-block;" src="' . $src . '"/>';
		}
		$html .= '</div>';
		echo $html;
	}
	
	public function showVirtualProductDesign($_item){
		/* online product design */
		$data_design = "";
		if(Mage::helper('onlinedesign/config')->isEnableModule()) {
			$src_json_arr = array();
			$session        = Mage::getSingleton('checkout/session');
			$quote_id       = $session->getQuoteId();
			$quote_item_id       = $_item->getId();
			$quote = Mage::getModel('sales/quote')->load($quote_id);
			$quote->getAllVisibleItems();
			$quoteItems = $quote->getItemsCollection();
			foreach($quoteItems as $qItem) {		
				if($qItem->getId() == $quote_item_id && $qItem->getNbdesignerJson() != ""){
					$data_design = $qItem->getNbdesignerJson();
				}
			}
			$list = json_decode($data_design);
			$html = "";
			if($list) {
				$helper = Mage::helper('onlinedesign/data');
				$html = $helper->getTilteList().'<br /><div>';
				$iData = $_item->getProduct();
				foreach ($list as $img) {
					$src = $helper->nbdesigner_create_secret_image_url($img);
					$html .= '<img width="60" height="60" style="border-radius: 3px; border: 1px solid #ddd; margin-top: 5px; margin-right: 5px; display: inline-block;" src="' . $src . '"/>';
				}
				$html .= '</div>';
			}
			return $html;
		}
	}
}
?>