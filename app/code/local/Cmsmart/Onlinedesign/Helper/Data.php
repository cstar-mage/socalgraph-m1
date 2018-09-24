<?php

class Cmsmart_Onlinedesign_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getTilteList(){
		return "Design: ";
	}
	
	public function showButtonDesign(){
		if(Mage::helper('onlinedesign/config')->isEnableModule()){
			return Mage::app()->getLayout()->createBlock('onlinedesign/onlinedesign')
				->setTemplate('onlinedesign/design_it.phtml')->toHtml(); 
		}
	}
	
	public function get_current_user_id() {
		$customer_id = 0;
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			$customer_id = $customerData->getId();
		}
		
		return $customer_id;
	}
	
	public function plugin_path_data(){
		return Mage::getBaseDir("media").DS.'nbdesigner';
	}
	
	public function getMediaPath(){
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."/nbdesigner";
	}
	
	public function nbdesigner_read_json_setting($fullname) {
        if (file_exists($fullname)) {
            $list = json_decode(file_get_contents($fullname), true);           
        } else {
            $list = '';
            file_put_contents($fullname, $list);
        }
        return $list;
    }
	
	public function nbdesigner_save_design_to_image($data, $sid, $pid) {
        $links = array();
        $mes = array();
        $order = 'nb_order';
		
		$path1 = $this->plugin_path_data();
		if(!is_dir($path1)){
			mkdir($path1, 0777);
		}
		
		$path2 = $path1.DS. 'designs';
		if(!is_dir($path2)){
			mkdir($path2, 0777);
		}
		
		$path3 = $path2.DS. $sid;
		if(!is_dir($path3)){
			mkdir($path3, 0777);
		}
		
		$path4 = $path3.DS. $order;
		if(!is_dir($path4)){
			mkdir($path4, 0777);
		}
		
		$path5 = $path4.DS. $pid;
		if(!is_dir($path5)){
			mkdir($path5, 0777);
		}

		$path_thumb = $path5.DS.'thumbs';
		
        if(file_exists($path5)){
            $this->nbdesigner_delete_folder($path5);
        }
		
        if (!file_exists($path5)) {
			
            if (mkdir($path5, 0777)) {
                if (!file_exists($path_thumb))
                    if (!mkdir($path_thumb, 0777)) {
                        $mes[] = $this->__('Your server not allow creat folder');
                    }
            } else {
                $mes[] = $this->__('Your server not allow creat folder');
            }
        }

		$thumb_size = explode("x", $this->getThumbSize());
		$width = $thumb_size[0];
		$height = $thumb_size[1];
		
        foreach ($data as $key => $val) {
            $temp = explode(';base64,', $val);
            $buffer = base64_decode($temp[1]);
            $full_name = $path5 . '/' . $key . '.png';
			//echo $full_name;die;
            if ($this->nbdesigner_save_data_to_image($full_name, $buffer)) {
                /* $image = wp_get_image_editor($full_name); */
                $image = file_get_contents($full_name);
                $_width = $width;
                $_height = $height;
                $_quality = 76;     

				/* fix_code: opt_val in configuration */
				/* $opt_val = get_option('nbdesigner');  
                if(is_array($opt_val)){
                    extract($opt_val);   
                    $_width = $thumbnail_width;
                    $_height = $thumbnail_height;
                    $_quality = $thumbnail_quality;
                } */
				
                if ($image) {
                    $thumb_file = $path_thumb . '/' . $key . '.png';
					/* resize image */
					$src = $this->nbdesigner_resize_imagepng($full_name, $_width, $_height);
					$image = imagecreatetruecolor($_width, $_height);
					imagesavealpha($image, true);
					$color = imagecolorallocatealpha($image, 255, 255, 255, 127);
					imagefill($image, 0, 0, $color);
					imagecopy($image, $src, 0, 0, 0, 0, $_width, $_height);					
					imagepng($image, $thumb_file);
					imagedestroy($src);		

					$links[$key] = $this->nbdesigner_create_secret_image_url($thumb_file);
				}
            } else {
                $mes[] = $this->__('Your server not allow writable file');
            }
        }
        return array('link' => $links, 'mes' => $mes);
    }
	
	public function nbdesigner_resize_imagepng($file, $w, $h){
        list($width, $height) = getimagesize($file);
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagesavealpha($dst, true);
        $color = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $color);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
        imagedestroy($src);
		
        return $dst;        
    }
	
	public function nbdesigner_create_secret_image_url($file_path) {
        $type = pathinfo($file_path, PATHINFO_EXTENSION);
        $data = file_get_contents($file_path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);   
        return $base64;
    }
	
	public function nbdesigner_delete_folder($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $this->nbdesigner_delete_folder(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
	
	public function nbdesigner_save_data_to_image($path, $data) {
        if (!$fp = fopen($path, 'w')) {
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $data);
        flock($fp, LOCK_UN);
        fclose($fp);
        return TRUE;
    }
	
	public function nbdesigner_copy_dir($src, $dst) {
        if (file_exists($dst)) $this->nbdesigner_delete_folder($dst);
        if (is_dir($src)) {
            wp_mkdir_p($dst);
            $files = scandir($src);
            foreach ($files as $file){
                if ($file != "." && $file != "..") $this->nbdesigner_copy_dir("$src/$file", "$dst/$file");
            }
        } else if (file_exists($src)) copy($src, $dst);
    }
	
	public function nbdesigner_list_thumb($path, $level = 2) {
        $list = array();
        $_list = $this->nbdesigner_list_files($path, $level);
        $list = preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $_list);
        return $list;
    }
	
	public function nbdesigner_list_files($folder = '', $levels = 100) {
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
                    $files2 = $this->nbdesigner_list_files($folder . '/' . $file, $levels - 1);
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
	
	public function nbdesigner_delete_json_setting($fullname, $id, $reindex = true) {
        $list = $this->nbdesigner_read_json_setting($fullname);
		$id_found = $this->indexFound($id, $list, "id");
		if (is_array($list)) {
            array_splice($list, $id_found, 1);
			
            /* if ($reindex) {
                $key = 0;
                foreach ($list as $val) {
                    $val->id = (string) $key;
                    $key++;
                }
            } */
        }
        $res = json_encode($list);
        file_put_contents($fullname, $res);
    }
	
	public function nbdesigner_delete_gfont_json_setting_bak($fullname, $id, $reindex = true) {
        $list = $this->nbdesigner_read_json_setting($fullname);
		if (is_array($list)) {
            array_splice($list, $id, 1);
			if ($reindex) {
                $key = 0;
				$tmp = array();
				$tmp2 = array();
                foreach ($list as $val) {
					$tmp["name"] = $val["name"];
					$tmp["id"] = (string) $key;
					$tmp2[] = $tmp;
                    $key++;
                }
            }
        }
        $res = json_encode($tmp2);
        file_put_contents($fullname, $res);
    }
	
	public function nbdesigner_delete_gfont_json_setting($fullname, $name, $reindex = true) {
        $list = $this->nbdesigner_read_json_setting($fullname);
		$new_list =  array();
		if (is_array($list)) {
			$tmp1 = array();
			$tmp11 = array();
			foreach ($list as $val) {
				if($val["name"] != $name) {
					$tmp1["name"] = $val["name"];
					$tmp1["id"] = $val["id"];
					$tmp11[] = $tmp1;
				}
			}
			
			$new_list = $tmp11;
			/* reindex id */
			if ($reindex) {
                $key = 0;
				$tmp = array();
				$tmp2 = array();
                foreach ($new_list as $val) {
					$tmp["name"] = $val["name"];
					$tmp["id"] = (string) $key;
					$tmp2[] = $tmp;
                    $key++;
                }
            }
        }
        $res = json_encode($tmp2);
        file_put_contents($fullname, $res);
    }
	
	public function nbdesigner_update_json_setting_depend($fullname, $id) {
        $list = $this->nbdesigner_read_json_setting($fullname);
        $id_found = $this->indexFound($id, $list, "id");
		if (!is_array($list)) return;
        foreach ($list as $val) {             
            if (!((sizeof($val) > 0))) continue;       
            foreach ($val->cat as $k => $v) {
                if ($v == $id_found) {                   
                    array_splice($val->cat, $k, 1);
                    break;
                }
            }
            foreach ($val->cat as $k => $v) {
                if ($v > $id_found) {
                    $new_v = (string) --$v;
                    unset($val->cat[$k]);
                    array_splice($val->cat, $k, 0, $new_v);									
                }
            }
        }
        $res = json_encode($list);
        file_put_contents($fullname, $res);
    }
	
	public function nbdesigner_update_json_setting($fullname, $data, $id) {
        $list = $this->nbdesigner_read_json_setting($fullname);
        
		$id_found = $this->indexFound($id, $list, "id");
		
		if (is_array($list))
            $list[$id_found] = $data;
        else {
            $list = array();
            $list[] = $data;
        }
        $_list = array();
        foreach ($list as $val) {
            $_list[] = $val;
        }
        $res = json_encode($_list);
        file_put_contents($fullname, $res);
    }
	
	public function indexFound($needle, $haystack, $field) {
		foreach ($haystack as $index => $innerArray) {
			if (isset($innerArray[$field]) && $innerArray[$field] === $needle) {
				return $index;
			}
		}
	}
	
	public function nbdesigner_update_list_arts($art, $id = null) {
        $path = $this->plugin_path_data().DS. 'arts.json';
        if (isset($id)) {
            $this->nbdesigner_update_json_setting($path, $art, $id);
            return;
        }
        $list_art = array();
        $list = $this->nbdesigner_read_json_setting($path);
        if (is_array($list)) {
            $list_art = $list;
            /* $id = sizeOf($list_art); */
            $art['id'] = (string) $id;
        }
        $list_art[] = $art;
        $res = json_encode($list_art);
        file_put_contents($path, $res);
    }
	
	public function nbdesigner_get_list_google_font() {
        $path = $this->plugin_path_data().DS.'data/listgooglefonts.json';
        $data = (array) $this->nbdesigner_read_json_setting($path);
        return json_encode($data);
    }
	
	public function nbdesigner_update_font($font, $id) {
        $path = $this->plugin_path_data(). DS . 'fonts.json';
        $this->nbdesigner_update_json_setting($path, $font, $id);
    }
	
	public function nbdesigner_update_list_fonts($font, $type, $id = null) {
        /* if (isset($id)) {
            $this->nbdesigner_update_font($font, $id);
            return;
        } */
		if ($type == "update") {
            $this->nbdesigner_update_font($font, $id);
            return;
        }
        $list_font = array();
        $path = $this->plugin_path_data(). DS . 'fonts.json';
        $list = $this->nbdesigner_read_json_setting($path);
        if (is_array($list)) {
            $list_font = $list;
            /* $id = sizeOf($list_font);
            $font['id'] = (string) $id; */
        }
        $list_font[] = $font;
        $res = json_encode($list_font);
        file_put_contents($path, $res);
    }
	
	public function nbdesigner_get_extension($file_name) {
        $filetype = explode('.', $file_name);
        $file_exten = $filetype[count($filetype) - 1];
        return $file_exten;
    }
	
	public function checkFileType($file_name, $arr_mime) {
        $check = false;
        $filetype = explode('.', $file_name);
        $file_exten = $filetype[count($filetype) - 1];
        if (in_array(strtolower($file_exten), $arr_mime)) $check = true;
        return $check;
    }
	
	/*
	Return date/time with correct format for each store view
	param $time int
	param $format const Mage_Core_Model_Locale::FORMAT_TYPE_FULL/FORMAT_TYPE_LONG/FORMAT_TYPE_MEDIUM/FORMAT_TYPE_SHORT	
	return string 
	*/
	public function locale_time_format($time,$format,$time_format=null)
    {        
		$format=Mage::app()->getLocale()->getDateFormat($format);
		$date=$this->upppercase_date_string(Mage::app()->getLocale()->date($time)->toString($format));
		if(!empty($time_format))
		$date=$date." ". date($time_format, $time)." ";		
		return $date;		
    } 
	
	/*
	Uppercase for first letter of day/month name for the locale
	param $date_str example: jeudi 14 mars 2013
	return string  exp: Jeudi 14 Mars 2013
	*/
    public function upppercase_date_string($date_str){
		$date=explode(" ",$date_str);$date_arr=array();
		foreach ($date as $d){
		$date_arr[]=ucfirst($d);		
		};
		return implode(" ",$date_arr);
		
	}
	
	public function getLinkCustomer($customer_id,$detail)
	{   
		$url = "adminhtml/customer/edit";
		$result='';
		$result = $this->__("<b><a href=\"%s\" target=\"blank\">%s</a></b>",Mage::helper('adminhtml')->getUrl($url,array('id'=>$customer_id)),$detail);
		return $result;
	}
	
	public function zip_files_and_download($file_names, $archive_file_name, $nameZip){
		if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if(file_exists($archive_file_name)){
                unlink($archive_file_name);
            }
            $dir = dirname($archive_file_name);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
              exit("cannot open <$archive_file_name>\n");
            }
			$fCount = 1;
            foreach($file_names as $file)
            {
                $path_arr = explode('/', $file);
                $name = $path_arr[count($path_arr) - 2].'_'.$path_arr[count($path_arr) - 1];                
                $name = "[".$fCount."]_".$name;
				$zip->addFile($file, $name);
				$fCount++;
            }
            $zip->close();
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$nameZip");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$archive_file_name");
            exit;
        }
    }

	public function getStatusDesign($pid){
		$model = Mage::getModel('onlinedesign/onlinedesign')
				->getCollection()
				->addFieldToFilter('product_id', $pid);
		$status = 0;
		foreach ($model as $m){
			$status = $m->getStatus();
			break;
		}
		return $status;
	}
	
	public function getSessionFolderFromPath($data_design){
		$path_arr_1 = explode("designs", $data_design);
		$path_arr_2 = explode("nb_order", $path_arr_1[1]);
		$folder = trim($path_arr_2[0], '\/');
		return $folder;
	}
}