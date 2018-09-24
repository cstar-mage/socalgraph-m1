<?php

class Cmsmart_Onlinedesign_Model_Observer {
    public function checkoutCartProductAddAfter($observer)
    {
        $item = $observer->getEvent()->getQuoteItem();
		/* $data_design = Mage::getSingleton('core/session')->getNbdesignerSrc();  */
		$pid = Mage::getSingleton('core/session')->getNbdesignerPid();
		$design_json_file = $_SESSION['nbdesigner']['nbdesigner_'.$pid];
		/* $sid = Mage::getSingleton('core/session')->getNbdesignerSession(); */
		
        /* $item->setData('nbdesigner_src', $data_design); */
        $item->setData('nbdesigner_sku', $item->getSku());
        $item->setData('nbdesigner_json', $design_json_file);
        $item->setData('nbdesigner_pid', $pid);
        /* $item->setData('nbdesigner_session', $sid); */
		Mage::log("Checkout");
		/* Mage::getSingleton('core/session')->unsNbdesignerSrc();
		Mage::getSingleton('core/session')->unsDesignJsonFile(); */
		Mage::getSingleton('core/session')->unsNbdesignerPid();
		Mage::getSingleton('core/session')->unsNbdesignerJson();
		/* Mage::getSingleton('core/session')->unsNbdesignerSession(); */
		session_regenerate_id();
        return $this;
    }
}