<?php
class Cmsmart_Onlinedesign_Block_Adminhtml_Renderer_Orderdesigner extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
		$onlinedesign_pid_arr = array();
		$order_pid_arr = array();
		$product = Mage::getModel('onlinedesign/onlinedesign')
					->getCollection();
		foreach($product as $p) {
			$onlinedesign_pid_arr[] = $p->getProductId();
		}
		
		$status = 0;
		$order = Mage::getModel('sales/order')->loadByIncrementId($row["increment_id"]);
	    $items = $order->getAllVisibleItems();
	    foreach($items as $item):
			//$order_pid_arr[] = $item->getProductId();
			if($item->getNbdesignerJson() != null || $item->getNbdesignerJson() != "" && in_array($item->getNbdesignerPid(), $onlinedesign_pid_arr)){
				$path = $item->getNbdesignerJson();
				$path_arr = json_decode($path);
				if(file_exists($path_arr[0])){
					$status = 1;
					break;
				}
			}
	    endforeach;
		
    	$result = '<span style="font-weight: bold; color: #CDCDCD;text-transform: uppercase;">No Design</span>';
		/* for($i = 0; $i < count($onlinedesign_pid_arr); $i++){
			if(in_array($onlinedesign_pid_arr[$i], $order_pid_arr)){
				$status = 1;
				break;
			}
		} */
		if($status) {
			$result = '<span style="font-weight: bold; text-transform: uppercase;">Has Design</span>';
		}
		
    	return $result;
    }

}