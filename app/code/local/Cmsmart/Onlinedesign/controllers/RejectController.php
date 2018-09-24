<?php
class Cmsmart_Onlinedesign_RejectController extends Mage_Core_Controller_Front_Action
{
	public function nbdesigner_design_approveAction()
    {
        $order_id = $this->getRequest()->getParam('order_id');
        $_design_file = $this->getRequest()->getParam('_nbdesigner_design_file');  
        $_design_action = $this->getRequest()->getParam('nbdesigner_order_file_approve');  
		$response = array();
		$class = "";
		if (is_numeric($order_id) && isset($_design_file) && is_array($_design_file)) {

			$model = Mage::getModel('onlinedesign/reject');
			$data = array();
			foreach ($_design_file as $pid){
				$model->delRecord($order_id, $pid);
				
				$data['oid'] = $order_id;
				$data['pid'] = $pid;
				$data['action'] = $_design_action;
				$model->setData($data);
				$model->save();
				$class .= '.row-'.$pid.'|';
			}
		}
		
		$response['mes'] = 'success';
		$response['action'] = $_design_action;
		$response['change_color'] = $class;
		echo json_encode($response);
		die;
	}
	
	public function sendMailAction() {
		$mail = Mage::getModel('core/email');
		$mail->setToName('Cong Nguyen');
		$mail->setToEmail('thecongit88@gmail.com');
		$mail->setBody('Mail Text');
		$mail->setSubject('Mail Subject');
		$mail->setFromEmail('mageexten@gmail.com');
		$mail->setFromName("Msg to Show on Subject");
		$mail->setType('html');// YOu can use Html or text as Mail format

		//try {
			$mail->send();
			Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
		/* }
		catch (Exception $e) {
			Mage::getSingleton('core/session')->addError('Unable to send.');
		} */
	}
	
	public function mailAction(){
		ini_set("SMTP","aspmx.l.google.com");
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		$headers .= "From: test@gmail.com" . "\r\n";
		mail("thecongit88@gmail.com","test subject","test body",$headers);
		die("abc");
	}
}