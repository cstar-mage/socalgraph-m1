<?php 
$helper = Mage::helper('onlinedesign/config');
$main_js_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'nb_onlinedesign/';
$base_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
$baseMediaDir = Mage::getBaseDir('media');
$baseMediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

if (!defined('NBDESIGNER_BASE_URL')) {
    define('NBDESIGNER_BASE_URL', $base_url);
}

if (!defined('NBDESIGNER_PLUGIN_URL')) {
    define('NBDESIGNER_PLUGIN_URL', $main_js_url);
}

if (!defined('NBDESIGNER_DATA_DIR')) {   
    define('NBDESIGNER_DATA_DIR', $baseMediaDir . '/nbdesigner');
}

if (!defined('NBDESIGNER_DATA_URL')) {   
    define('NBDESIGNER_DATA_URL', $baseMediaUrl . '/nbdesigner');
}

if (!defined('NBDESIGNER_FONT_DIR')) {   
    define('NBDESIGNER_FONT_DIR', NBDESIGNER_DATA_DIR . '/fonts');
}
if (!defined('NBDESIGNER_FONT_URL')) {   
    define('NBDESIGNER_FONT_URL', NBDESIGNER_DATA_URL . '/fonts');
}
if (!defined('NBDESIGNER_DOWNLOAD_DIR')) {   
    define('NBDESIGNER_DOWNLOAD_DIR', NBDESIGNER_DATA_DIR . '/download');
}
if (!defined('NBDESIGNER_DOWNLOAD_URL')) {   
    define('NBDESIGNER_DOWNLOAD_URL', NBDESIGNER_DATA_URL . '/download');
}
if (!defined('NBDESIGNER_TEMP_DIR')) {   
    define('NBDESIGNER_TEMP_DIR', NBDESIGNER_DATA_DIR . '/temp');
}
if (!defined('NBDESIGNER_TEMP_URL')) {   
    define('NBDESIGNER_TEMP_URL', NBDESIGNER_DATA_URL . '/temp');
}
if (!defined('NBDESIGNER_ADMINDESIGN_DIR')) {   
    define('NBDESIGNER_ADMINDESIGN_DIR', NBDESIGNER_DATA_DIR . '/admindesign');
}
if (!defined('NBDESIGNER_ADMINDESIGN_URL')) {   
    define('NBDESIGNER_ADMINDESIGN_URL', NBDESIGNER_DATA_URL . '/admindesign');
}
if (!defined('NBDESIGNER_PDF_DIR')) {   
    define('NBDESIGNER_PDF_DIR', NBDESIGNER_DATA_DIR . '/pdfs');
}
if (!defined('NBDESIGNER_PDF_URL')) {   
    define('NBDESIGNER_PDF_URL', NBDESIGNER_DATA_URL . '/pdfs');
}
if (!defined('NBDESIGNER_CUSTOMER_DIR')) {   
    define('NBDESIGNER_CUSTOMER_DIR', NBDESIGNER_DATA_DIR . '/designs');
}
if (!defined('NBDESIGNER_PLUGIN_DIR')) {   
    define('NBDESIGNER_PLUGIN_DIR', $baseMediaDir . '/nbdesigner/');
}
?>
<?php /* =========== End Define Path  ============= */ ?>
<?php
function is_rtl(){
	return false;
}

if (!function_exists('get_current_user_id')) {
	function get_current_user_id(){
		/* $customer_id = 0;
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			$customer_id = $customerData->getId();
		}
		
		return $customer_id; */
		return  session_id();
	}
}

if (!function_exists('is_user_logged_in')) {
	function is_user_logged_in() {
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			return 0;
		}else{
			return 1;
		}
	}
}

if (!function_exists('nbdesigner_get_option')) {
	function nbdesigner_get_option($key){
		return nbdesigner_get_default_setting($key);
	}
}

if (!function_exists('get_option')) {
	function get_option($key){
		return nbdesigner_get_default_setting($key);
	}
}

if (!function_exists('nbdesigner_get_all_frontend_setting')) {
	function nbdesigner_get_all_frontend_setting(){
		$default = default_frontend_setting();
		foreach ($default as $key => $val){
			$default[$key] = nbdesigner_get_option($key);
		}
		return $default;
	}
}

if (!function_exists('default_frontend_setting')) {
	
function default_frontend_setting(){
    $default = array(
        'nbdesigner_text_change_font' => 1,
        'nbdesigner_text_italic' => 1,
        'nbdesigner_text_bold' => 1,
        'nbdesigner_text_underline' => 1,
        'nbdesigner_text_through' => 1,
        'nbdesigner_text_overline' => 1,
        'nbdesigner_text_align_left' => 1,
        'nbdesigner_text_align_right' => 1,
        'nbdesigner_text_align_center' => 1,
        'nbdesigner_text_color' => 1,
        'nbdesigner_text_background' => 1,
        'nbdesigner_text_shadow' => 1,
        'nbdesigner_text_line_height' => 1,
        'nbdesigner_text_font_size' => 1,
        'nbdesigner_text_opacity' => 1,
        'nbdesigner_text_outline' => 1,
        'nbdesigner_text_proportion' => 1,
        'nbdesigner_text_rotate' => 1,
        'nbdesigner_clipart_change_path_color' => 1,           
        'nbdesigner_clipart_rotate' => 1,           
        'nbdesigner_clipart_opacity' => 1,           
        'nbdesigner_image_unlock_proportion' => 1,           
        'nbdesigner_image_shadow' => 1,           
        'nbdesigner_image_opacity' => 1,           
        'nbdesigner_image_grayscale' => 1,           
        'nbdesigner_image_invert' => 1,           
        'nbdesigner_image_sepia' => 1,           
        'nbdesigner_image_sepia2' => 1,           
        'nbdesigner_image_remove_white' => 1,      
        'nbdesigner_image_transparency' => 1,           
        'nbdesigner_image_tint' => 1,           
        'nbdesigner_image_blend' => 1,           
        'nbdesigner_image_brightness' => 1,           
        'nbdesigner_image_noise' => 1,         
        'nbdesigner_image_pixelate' => 1,         
        'nbdesigner_image_multiply' => 1,     
        'nbdesigner_image_blur' => 1,           
        'nbdesigner_image_sharpen' => 1,         
        'nbdesigner_image_emboss' => 1,         
        'nbdesigner_image_edge_enhance' => 1,          
        'nbdesigner_image_rotate' => 1,          
        'nbdesigner_image_crop' => 1,          
        'nbdesigner_image_shapecrop' => 1,          
        'nbdesigner_draw_brush' => 1,          
        'nbdesigner_draw_shape' => 1          
    );
    return $default;
}

}

if (!function_exists('nbdesigner_get_default_setting')) {

function nbdesigner_get_default_setting($key = false){
	$config = Mage::helper('onlinedesign/config');
	$thumb_size = $config->getThumbSize();
    $frontend = default_frontend_setting();
	$str_color = "";
	if(strpos($key, "nbdesigner_hex_names") !== false) {
		$collection = Mage::getModel('onlinedesign/color')->getCollection();
		foreach($collection as $c) {
			$str_color .= "#".$c->getHex().":".$c->getColorName().",";
		}
	}
	
    $nbd_setting = array_merge(array(
        'nbdesigner_button_label' => $config->getDesignLabel(),
        'nbdesigner_position_button_in_catalog' => 1,
        'nbdesigner_position_button_product_detail' => 1,
        'nbdesigner_thumbnail_width' => $thumb_size[0],
        'nbdesigner_thumbnail_height' => $thumb_size[1],
        'nbdesigner_thumbnail_quality' => $config->getThumbQuality(),
        'nbdesigner_default_dpi' => $config->getDefaultDPI(),
        'nbdesigner_show_in_cart' => 'yes',
        'nbdesigner_show_in_order' => 'yes',
        'nbdesigner_dimensions_unit' => $config->getUnit(),
        'nbdesigner_disable_on_smartphones' => $config->getHideOnMobile() ? "yes" : "no",
        'nbdesigner_upload_designs_php_logged_in' => $config->getLoginRequire(),
        'nbdesigner_notifications' => 'yes',
        'nbdesigner_notifications_recurrence' => 'hourly',
        'nbdesigner_notifications_emails' => '',
        'nbdesigner_facebook_app_id' => $config->getFApiKey(),
        'nbdesigner_enable_text' => $config->EnableAddText() ? "yes" : "no",
        'nbdesigner_default_text' => $config->getDefaultText(),
        'nbdesigner_enable_curvedtext' => 'yes',
        'nbdesigner_enable_textpattern' => 'yes',
        'nbdesigner_enable_clipart' => $config->EnableClipArt() ? "yes" : "no",
        'nbdesigner_enable_image' => $config->EnableAddImage() ? "yes" : "no",
        'nbdesigner_enable_upload_image' => $config->EnableUploadImage() ? "yes" : "no",
        'nbdesigner_enable_image_webcam' => $config->EnableInsertImageWebcame() ? "yes" : "no",
        'nbdesigner_enable_facebook_photo' => $config->EnableInsertImageFacebook() ? "yes" : "no",
        'nbdesigner_upload_show_term' => $config->EnableInsertImageTerm() ? "yes" : "no",
        'nbdesigner_enable_image_url' => $config->EnableInsertImageUrl() ? "yes" : "no",
        'nbdesigner_upload_term' => $config->getImageTextTerm(),
        'nbdesigner_enable_draw' =>  $config->EnableFreedraw() ? "yes" : "no",
        'nbdesigner_enable_qrcode' => $config->EnableQRCode() ? "yes" : "no",
        'nbdesigner_default_qrcode' => $config->getQRText(),
        'nbdesigner_show_all_color' => $config->getShowAllColor() ? "yes" : "no",
        'nbdesigner_maxsize_upload' => $config->getUploadMaxSize(),
        'nbdesigner_minsize_upload' => $config->getUploadMinSize(),
        'nbdesigner_default_color' => $config->getDefaultColor(),
        'nbdesigner_hex_names' => $str_color,
        'nbdesigner_instagram_app_id' => '',
        'nbdesigner_printful_key' => ''
    ), $frontend);
    if(!$key) return $nbd_setting;
    return $nbd_setting[$key];
}

}

function nbd_get_product_info($user_id, $product_id, $variation_id = 0, $task = '', $reference_product = '', $template_folder = '', $order_id = '', $order_item_folder = '' ){
    $path = '';
    $data = array();
	
	$data['product'] = unserialize(get_post_meta($product_id, '_designer_setting', true));
	$data['dpi'] = (get_post_meta($product_id, '_nbdesigner_dpi', true) != '') ?  get_post_meta($product_id, '_nbdesigner_dpi', true) : 96;
	if($variation_id > 0){
		$variation_enable = get_post_meta($variation_id, '_nbdesigner_enable'.$variation_id, true);
		if($variation_enable){
			$data['product'] = unserialize(get_post_meta($variation_id, '_designer_setting'.$variation_id, true));
		}
	}
	if($task == 'redesign') {
		/* fix code */
		$path = getDesignPath($order_id, $product_id);
		//$path = NBDESIGNER_CUSTOMER_DIR . '/' .$user_id. '/' .$order_id. '/' .$order_item_folder ;
	
	} elseif ($task == 'create_template' || $task == 'edit_template'){
		if($template_folder != ''){
			$path = NBDESIGNER_ADMINDESIGN_DIR . '/' . $product_id . '/' . $template_folder;
		}    
	}else {
		if($reference_product != ''){
			mage::log("admin design 1");
			$path = NBDESIGNER_CUSTOMER_DIR. '/' .$user_id. '/nb_order/' .$reference_product;
			$data['ref'] = unserialize(get_post_meta($reference_product, '_designer_setting', true));
		}else{
			mage::log("admin design 2");
			$option = unserialize(get_post_meta($product_id, '_nbdesigner_option', true));
			if($option['admindesign']){
				mage::log("admin design 3");
				if($template_folder != ''){
					mage::log("admin design 4");
					$path = NBDESIGNER_ADMINDESIGN_DIR . '/' . $product_id . '/' . $template_folder;
				}else {
					mage::log("admin design 5");
					$path = NBDESIGNER_ADMINDESIGN_DIR . '/' . $product_id . '/primary';
				}              
			}
		}
	}  
	mage::log("admin design");
	mage::log($path);
	$data['design'] = nbd_get_data_from_json($path . '/design.json');
	$data['fonts'] = nbd_get_data_from_json($path . '/used_font.json');
	$data['config'] = nbd_get_data_from_json($path . '/config.json');

	return $data;
}

function nbd_get_data_from_json($path = ''){
    if ($path != '' && file_exists($path)) {
        return json_decode(file_get_contents($path));           
    }    
    return '';
}

function hex_code_to_rgb($code){        
    list($r, $g, $b) = sscanf($code, "#%02x%02x%02x");
    $rgb = array($r, $g, $b);
    return $rgb;
}

function getDesignPath($oid, $pid){
	$order = Mage::getModel('sales/order')->load($oid);
	$order->getAllVisibleItems();
	$orderItems = $order->getItemsCollection()->addAttributeToSelect('*')->load();
	foreach($orderItems as $item){
		if($item->getParentItemId() == null || $item->getParentItemId() == "") {
			if($item->getId() == $_GET["order_item_id"]) {
				$data_design = $item->getNbdesignerJson();
				$folder = Mage::helper('onlinedesign/data')->getSessionFolderFromPath($data_design);
			}
		}
	}
	
	$path = NBDESIGNER_CUSTOMER_DIR . '/' .$folder. '/nb_order/' .$pid.'/' ;
	return $path;
}

?>
<?php /* =========== Nbdesigner_IO  ============= */ ?>
<?php
class Nbdesigner_IO {
    public function __construct() {
        //TODO
    }
    /**
     * Get all images in folder by level
     * 
     * @param string $path path folder
     * @param int $level level scan dir
     * @return array Array path images in folder
     */
    public static function get_list_thumbs($path, $level = 100){
        $list = array();
        $_list = self::get_list_files($path, $level);
        $list = preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $_list);
        return $list;        
    }
    public static function get_list_files($folder = '', $levels = 100) {
        if (empty($folder))
            return false;
        if (!$levels)
            return false;
        $files = array();
        if ($dir = @opendir($folder)) {
            while (($file = readdir($dir) ) !== false) {
                if (in_array($file, array('.', '..')))
                    continue;
                if (is_dir($folder . '/' . $file)) {
                    $files2 = self::get_list_files($folder . '/' . $file, $levels - 1);
                    if ($files2)
                        $files = array_merge($files, $files2);
                    else
                        $files[] = $folder . '/' . $file . '/';
                } else {
                    $files[] = $folder . '/' . $file;
                }
            }
        }
        @closedir($dir);
        return $files;
    }
    public static function delete_folder($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                self::delete_folder(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    } 
    public static function copy_dir($src, $dst) {
        if (file_exists($dst)) self::delete_folder($dst);
        if (is_dir($src)) {
            wp_mkdir_p($dst);
            $files = scandir($src);
            foreach ($files as $file){
                if ($file != "." && $file != "..") self::copy_dir("$src/$file", "$dst/$file");
            }
        } else if (file_exists($src)) copy($src, $dst);
    }        
    public static function create_image_path($upload_path, $filename, $ext=''){
	$date_path = '';
        if (!file_exists($upload_path))
            mkdir($upload_path);
        $year = @date() === false ? gmdate('Y') : date('Y');
        $date_path .= '/' . $year . '/';
        if (!file_exists($upload_path . $date_path))
            mkdir($upload_path . $date_path);
        $month = @date() === false ? gmdate('m') : date('m');
        $date_path .= $month . '/';
        if (!file_exists($upload_path . $date_path))
            mkdir($upload_path . $date_path);
        $day = @date() === false ? gmdate('d') : date('d');
        $date_path .= $day . '/';
        if (!file_exists($upload_path . $date_path))
            mkdir($upload_path . $date_path);
        $file_path = $upload_path . $date_path . $filename;
        $file_counter = 1;
        $real_filename = $filename;
        while (file_exists($file_path . '.' . $ext)) {
            $real_filename = $file_counter . '-' . $filename;
            $file_path = $upload_path . $date_path . $real_filename;
            $file_counter++;
        }
        return array(
            'full_path' => $file_path,
            'date_path' => $date_path . $real_filename
        );
    }   
    public static function secret_image_url($file_path){
        $type = pathinfo($file_path, PATHINFO_EXTENSION);
        $data = file_get_contents($file_path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);   
        return $base64;        
    }   
    public static function convert_path_to_url($path){
        $upload_dir = wp_upload_dir();
        $basedir = $upload_dir['basedir'];
        $arr = explode('/', $basedir);
        $upload = $arr[count($arr) - 1];
        if(is_multisite()) $upload = $arr[count($arr) - 3].'/'.$arr[count($arr) - 2].'/'.$arr[count($arr) - 1];
        $str_path = substr($path, strrpos($path, '/' . $upload . '/nbdesigner'));
		$path_dir = explode('media', $str_path);
		return NBDESIGNER_BASE_URL.'/media/'.$path_dir[1];
		/* return content_url( substr($path, strrpos($path, '/' . $upload . '/nbdesigner')) ); */
    }
    public static function convert_url_to_path($url){
        /* $upload_dir = wp_upload_dir();
        $basedir = $upload_dir['basedir']; */
		$basedir = NBDESIGNER_DATA_DIR;
        $arr = explode('/', $basedir);
        $upload = $arr[count($arr) - 1];
        if(is_multisite()) $upload = $arr[count($arr) - 3].'/'.$arr[count($arr) - 2].'/'.$arr[count($arr) - 1];
        $arr_url = explode('/'.$upload, $url);
        return $basedir.$arr_url[1];
    }
    public static function save_data_to_file($path, $data){
        if (!$fp = fopen($path, 'w')) {
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $data);
        flock($fp, LOCK_UN);
        fclose($fp);
        return TRUE;        
    }
    public static function checkFileType($file_name, $arr_mime) {
        $check = false;
        $filetype = explode('.', $file_name);
        $file_exten = $filetype[count($filetype) - 1];
        if (in_array(strtolower($file_exten), $arr_mime)) $check = true;
        return $check;
    }    
}
?>
<?php /* =========== End Nbdesigner_IO  ============= */ ?>