<?php

class Cmsmart_Onlinedesign_Helper_Config extends Mage_Core_Helper_Abstract
{
	public function getCurrentStoreId(){
		return Mage::app()->getStore()->getStoreId();
	}
	
	/* General */
	
	public function isEnableModule(){
		return Mage::getStoreConfig('onlinedesign/general/enabled', $this->getCurrentStoreId());
	}
	
	public function getDesignLabel(){
		return Mage::getStoreConfig('onlinedesign/general/btn_name', $this->getCurrentStoreId());
	}
	
	public function getThumbSize(){
		$thumb_size = explode("x", Mage::getStoreConfig('onlinedesign/general/thumb_size', $this->getCurrentStoreId()));
		return $thumb_size;
	}
	
	public function getThumbQuality(){
		return Mage::getStoreConfig('onlinedesign/general/thumb_quality', $this->getCurrentStoreId());
	}
	
	public function getDefaultDPI(){
		return Mage::getStoreConfig('onlinedesign/general/default_dpi', $this->getCurrentStoreId());
	}
	
	public function getUnit(){
		return Mage::getStoreConfig('onlinedesign/general/unit', $this->getCurrentStoreId());
	}
	
	public function getHideOnMobile(){
		return Mage::getStoreConfig('onlinedesign/general/hide_on_mobile', $this->getCurrentStoreId());
	}
	
	public function getAdminEmail(){
		return Mage::getStoreConfig('onlinedesign/general/owner_email', $this->getCurrentStoreId());
	}
	
	/* Text */
	
	public function EnableAddText(){
		return Mage::getStoreConfig('onlinedesign/text_options/enable_add_text', $this->getCurrentStoreId());
	}
	
	public function getDefaultText(){
		return Mage::getStoreConfig('onlinedesign/text_options/default_text', $this->getCurrentStoreId());
	}
	
	public function getDefaultColor(){
		return Mage::getStoreConfig('onlinedesign/text_options/default_color', $this->getCurrentStoreId());
	} 
	
	/* Clip Art */
	
	public function EnableClipArt(){
		return Mage::getStoreConfig('onlinedesign/clip_art/enable_clipart', $this->getCurrentStoreId());
	} 
	
	/* Image */
	
	public function EnableAddImage(){
		return Mage::getStoreConfig('onlinedesign/image_options/enable_add_image', $this->getCurrentStoreId());
	} 
	
	public function EnableUploadImage(){
		return Mage::getStoreConfig('onlinedesign/image_options/enable_upload_image', $this->getCurrentStoreId());
	}
	
	public function getLoginRequire(){
		return Mage::getStoreConfig('onlinedesign/image_options/login_require', $this->getCurrentStoreId());
	}
	
	public function getUploadMaxSize(){
		return Mage::getStoreConfig('onlinedesign/image_options/upload_max', $this->getCurrentStoreId());
	}
	
	public function getUploadMinSize(){
		return Mage::getStoreConfig('onlinedesign/image_options/upload_min', $this->getCurrentStoreId());
	}
	
	public function EnableInsertImageUrl(){
		return Mage::getStoreConfig('onlinedesign/image_options/enable_image_url', $this->getCurrentStoreId());
	}
	
	public function EnableInsertImageFacebook(){
		return Mage::getStoreConfig('onlinedesign/image_options/enable_facebook', $this->getCurrentStoreId());
	}
	
	public function getFApiKey(){
		return Mage::getStoreConfig('onlinedesign/general/facebook_api_key', $this->getCurrentStoreId());
	}
	
	public function EnableInsertImageWebcame(){
		return Mage::getStoreConfig('onlinedesign/image_options/enable_capture_webcame', $this->getCurrentStoreId());
	}
	
	public function EnableInsertImageTerm(){
		return Mage::getStoreConfig('onlinedesign/image_options/show_term', $this->getCurrentStoreId());
	}
	
	public function getImageTextTerm(){
		return Mage::getStoreConfig('onlinedesign/image_options/term_text', $this->getCurrentStoreId());
	}
	
	/* Free Draw */
	
	public function EnableFreedraw(){
		return Mage::getStoreConfig('onlinedesign/free_draw/enable_free_draw', $this->getCurrentStoreId());
	}
	
	/* Qr Code */
	
	public function EnableQRCode(){
		return Mage::getStoreConfig('onlinedesign/qr_code/enable_qrcode', $this->getCurrentStoreId());
	}
	
	public function getQRText(){
		return Mage::getStoreConfig('onlinedesign/qr_code/qr_text', $this->getCurrentStoreId());
	}
	
	/* Color */

	public function getShowAllColor(){
		return Mage::getStoreConfig('onlinedesign/color/show_all_color', $this->getCurrentStoreId());
	}
	
}