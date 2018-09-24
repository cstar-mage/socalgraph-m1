<?php
	/* get default value for design */
	$current_theme = Mage::getSingleton('core/design_package')->getPackageName();
	$img_skin_path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/'.$current_theme.'/default/cmsmart/onlinedesign/images/default.png';
	$overlay = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/'.$current_theme.'/default/cmsmart/onlinedesign/images/overlay.png';

	$product_id     = $this->getRequest()->getParam('id');
	$path_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/nbdesigner/productimages/'.$product_id;
	$onlinedesignIds = Mage::getModel('onlinedesign/onlinedesign')->_getOnlineDesignByProduct($product_id);
	$data = "";
	if(sizeof($onlinedesignIds)) {
		foreach ($onlinedesignIds as $od) {
			$data = $od->getContentDesign();
			$dpi = $od->getDpi();
			$enable = $od->getStatus();
			$option = unserialize($od->getNbdesignerOption());
			break;
		}
		$designer_setting = unserialize($data);
	} else {
		$designer_setting = array(
            array(
                'orientation_name' => 'Side 1',
                'img_src' => $img_skin_path,
                'img_overlay' => $overlay,
                'real_width' => 8,
                'real_height' => 6,
                'real_left' => 1,
                'real_top' => 1,
                'area_design_top' => 100,
                'area_design_left' => 50,
                'area_design_width' => 400,
                'area_design_height' => 300,
                'img_src_top' => 50,
                'img_src_left' => 0,
                'img_src_width' => 500,
                'img_src_height' => 400,
                'product_width' => 10,    
                'product_height' => 8,
                'bg_type'   => 'image',
                'bg_color_value' => "#ffffff",
                'show_overlay' => 0,
                'version' => ""
            )
        );
	}
	
	if($dpi == 0 || $dpi == "")$dpi = nbdesigner_get_option('nbdesigner_default_dpi');
	
	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$table_name = $resource->getTableName('onlinedesign/templates');
	$admin_template = $readConnection->fetchAll('SELECT folder, priority FROM ' . $table_name . ' WHERE product_id = '.$product_id.'');
	
	$priority = 0;
	for($i = 0; $i < count($admin_template); $i++) {
		if($admin_template[$i]["folder"] == "primary" && $admin_template[$i]["priority"] == "1") {
			$priority = 1;
			break;
		}
	}
	
	$product = Mage::getModel('catalog/product')->load($product_id);	
	$link_admindesign = $product->getProductUrl()."?product_id=".$product->getId();
?>

<!-- The Modal Image Product-->
<div id="myModal" class="modal" title="">
	<!-- Modal content -->
	<div class="modal-content">
		<div class="modal-title">
			<span class="close">&times;</span>
			<p><strong><?php echo $this->__('Please select product image from list') ?></strong></p>
		</div>
		<div id="img_list_dsg">
		<?php foreach ($product->getMediaGalleryImages() as $image) : ?>
			<img width="100" height="100" src="<?php echo $image->getUrl(); ?>" alt="<?php echo $product->getName()?>" />
		<?php endforeach; ?>
		</div>
		
		<!-- upload image file -->
		<div class="rCol">
			<p><strong><?php echo $this->__('The images you have uploaded') ?></strong></p>
			<div id ="prv">
			<?php
			$path = Mage::getBaseDir("media").'/nbdesigner/productimages/'.$product_id;
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					$files[] = $entry;
				}
				$images = $files;
				foreach($images as $image){
					$image_link = $path_url.'/'.$image;
					if (@getimagesize($image_link)) {
			?>
					<img width="100" height="100" src="<?php echo $image_link; ?>">
					<!-- <a href="#" id="rmv_'+fcnt+'" onclick="return removeit('+fcnt+')" class="close-classic"></a></div><input type="hidden" id="name_'+fcnt+'" value="'+data+'">' -->
			<?php
					}
				}
				closedir($handle);
			}
			?>
			</div>
			<br />
			<label><?php echo $this->__('Upload Photo:') ?></label> 
			<input type="file" id="file" name='file' onChange=" return submitForm();">
			<input type="hidden" id="filecount" value='0'>
			<p><?php echo $this->__("Allow file type: .jpg, .png"); ?></p>
		</div>
	</div>
</div>

<script>
	/* choose image */
	var img_preview_dsg_show, 
		img_preview_dsg_hide,
		pimage_hidden_img_src_width,
		pimage_hidden_img_src_height,
		ip_image_overlay,
		image_overlay;
	
	// Get the modal
	var modal = document.getElementById('myModal');

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];

	function openImagesPopup(btn, _title) {
		var data_index = jQuery(btn).attr("data-index");
		jQuery('#myModal').attr("title", _title);
		
		if(_title == "img-area") {
			img_preview_dsg_show = jQuery(btn).parents(':eq(4)').find('.nbdesigner-image-inner .designer_img_src')[data_index];
			img_preview_dsg_hide = jQuery(btn).parents(':eq(4)').find('.hidden_img_src')[data_index];	
			pimage_hidden_img_src_width = jQuery(btn).parents(':eq(4)').find('.hidden_img_src_width')[data_index];
			pimage_hidden_img_src_height = jQuery(btn).parents(':eq(4)').find('.hidden_img_src_height')[data_index];
		} else {
			/* console.log(jQuery(btn).parents()); */
			/* for overlay image */
			ip_image_overlay = jQuery(btn).parents(':eq(1)').find('.hidden_overlay_src');
			image_overlay = jQuery(btn).parents(':eq(1)').find('.nbdesigner-image-overlay .img_overlay');
			console.log(image_overlay);
		}
		
		modal.style.display = "block";
		
		return;
	}
	
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
		modal.style.display = "none";
	}

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}
	
	/* image list */
	
	function changeImage(evt){
		// Get the image that was clicked.
		var target = evt.target || evt.srcElement;
		// Then get the src or get the class
		if(target) {
			var targetSrc = target.src;
		} else {
			var targetSrc = jQuery(evt).attr("src");
		}
		/* var targetSrc = target.src; */
		jQuery(img_preview_dsg_show).attr("src", targetSrc);
		
		jQuery.ajax({
			url: "<?php echo $this->getUrl('onlinedesign/uploadimage/upload_exists', array('_current'=>true)); ?>",
			type: 'POST',
			data: {
				filename: targetSrc,
			},
			dataType: 'json',
			success: function(res) {
				if(res.url != "") {
					var _title = jQuery('#myModal').attr("title");
					if(_title == "img-area") {
						jQuery(img_preview_dsg_show).attr("src", res.url);
						jQuery(img_preview_dsg_hide).attr("value", res.url);	
					} else {
						jQuery(ip_image_overlay).val(res.url);
						jQuery(image_overlay).attr("src", res.url);
					}
					/* alert("Update The Image Successfully!"); */ 
				}
			}
		});
		
		modal.style.display = "none";
	}

	var months = document.getElementById("img_list_dsg");
	months.addEventListener("click", changeImage, true);
	
	var months2 = document.getElementById("prv");
	months2.addEventListener("click", changeImage, true);
</script>

<!-- upload image file by ajax -->

<script>
function submitForm() {
	var fcnt = jQuery('#filecount').val();
	var fname = jQuery('#filename').val();
	var imgclean = jQuery('#file');
	if(fcnt <= 5) {
		data = new FormData();
		data.append('file', jQuery('#file')[0].files[0]);

		var imgname  =  jQuery('input[type=file]').val();
		var size  =  jQuery('#file')[0].files[0].size;

		var ext =  imgname.substr( (imgname.lastIndexOf('.') +1) );
		if(ext=='jpg' || ext=='jpeg' || ext=='png' || ext=='gif' || ext=='PNG' || ext=='JPG' || ext=='JPEG') {
			if(size <= 1000000) {
				jQuery.ajax({
					url: "<?php echo $this->getUrl('onlinedesign/uploadimage/upload_myimages', array('_current'=>true)); ?>",
					type: "POST",
					data: data,
					enctype: 'multipart/form-data',
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
				}).done(function(data) {
					if(data!='FILE_SIZE_ERROR' || data!='FILE_TYPE_ERROR' ) {
						fcnt = parseInt(fcnt) + 1;
						jQuery('#filecount').val(fcnt);
						var img = '<img width="100" height="100" onClick = "changeImage(this); return false;" src="<?php echo $path_url; ?>/'+data+'">';
						jQuery('#prv').append(img);
						
						if(fname!=='') {
							fname = fname+','+data;
						} else {
							fname = data;
						}
						jQuery('#filename').val(fname);
						imgclean.replaceWith( imgclean = imgclean.clone( true ) );
					} else {
						imgclean.replaceWith( imgclean = imgclean.clone( true ) );
						alert('SORRY SIZE AND TYPE ISSUE');
					}
				});
				return false;
			} else { //end size
				imgclean.replaceWith( imgclean = imgclean.clone( true ) );//Its for reset the value of file type
				alert('Sorry File size exceeding from 1 Mb');
			}
		} else { //end FILETYPE
			imgclean.replaceWith( imgclean = imgclean.clone( true ) );
			alert('Sorry Only you can uplaod JPEG|JPG|PNG|GIF file type ');
		}
	} else {     //end filecount
		imgclean.replaceWith( imgclean = imgclean.clone( true ) );
		alert('You Can not Upload more than 6 Photos');
	}
}

/* Manage Template */
/* jQuery(".dashicons-images-alt").click(function() {
	jQuery("#onlinedesign_tabs_template_section").trigger("click");
}); */
</script>