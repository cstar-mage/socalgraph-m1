<?php //if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="nbdesigner-setting-container">
    <?php //wp_nonce_field('nbdesigner_setting_box', 'nbdesigner_setting_box_nonce'); ?>		
    <div class="nbdesigner-left">
        <input type="hidden" value="0" name="_nbdesigner_enable"/>
        <label for="_nbdesigner_enable" class="nbdesigner-setting-box-label"><?php echo $this->__('Enable Design'); ?></label>
        <input type="checkbox" value="1" name="_nbdesigner_enable" id="_nbdesigner_enable" <?php checked($enable); ?> class="short" />
    </div>
    <div class="nbdesigner-right add_more" style="display: none;">
        <a class="button button-primary" onclick="NBDESIGNADMIN.addOrientation('com')"><?php echo __('Add More'); ?></a>
        <a class="button button-primary" onclick="NBDESIGNADMIN.collapseAll('com')"><?php echo __('Collapse All'); ?></a>
    </div>
    <div class="nbdesigner-clearfix"></div>
    <div id="nbdesigner_dpi_con" class="<?php if (!$enable) echo 'nbdesigner-disable'; ?>">
        <label for="nbdesigner_dpi" class="nbdesigner-setting-box-label"><?php echo $this->__('DPI'); ?></label>
        <input name="_nbdesigner_dpi" id="nbdesigner_dpi" value="<?php echo $dpi;?>" type="number"  min="0" max="300" style="width: 60px;" onchange="NBDESIGNADMIN.updateSolutionImage()">&nbsp;<small>(<?php echo $this->__('Dots Per Inch'); ?>)</small>   
    </div>
    <div id="nbdesigner-boxes" class="<?php if (!$enable) echo 'nbdesigner-disable'; ?>">
        <?php $count = 0;
        foreach ($designer_setting as $k => $v): ?>
            <div class="nbdesigner-box-container">
                <div class="nbdesigner-box">
                    <label class="nbdesigner-setting-box-label"><?php echo $this->__('Name'); ?></label>
                    <div class="nbdesigner-setting-box-value">
                        <input name="_designer_setting[<?php echo $k; ?>][orientation_name]" class="short orientation_name" 
                               value="<?php echo $v['orientation_name']; ?>" type="text" required/>
                        <?php if($k ==0): ?>
                        <small class="nbd-helper"><?php echo $this->__('(Click [?]'); ?>  <span class="dashicons dashicons-editor-help"></span><?php echo $this->__('to know how to setting product design)'); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="nbdesigner-right">
                        <a class="button nbdesigner-collapse" onclick="NBDESIGNADMIN.collapseBox(this)"><span class="dashicons dashicons-arrow-up"></span><?php echo $this->__('Less setting'); ?></a>
                        <a class="button nbdesigner-delete delete_orientation" data-index="<?php echo $k; ?>" data-variation="com" onclick="NBDESIGNADMIN.deleteOrientation(this)">&times;</a>
                    </div>
                </div>
                <div class="nbdesigner-box nbdesigner-box-collapse" data-variation="com">
                    <div class="nbdesigner-image-box">
                        <div class="nbdesigner-image-inner">
                            <?php 
                                if($v['product_width'] >= $v['product_height']){
                                    $ratio = 500 / $v['product_width'];
                                    $style_width = 500;
                                    $style_height = round($v['product_height'] * $ratio);
                                    $style_left = 0;
                                    $style_top = round((500 - $style_height) / 2);
                                } else {
                                    $ratio = 500 / $v['product_height'];
                                    $style_height = 500;
                                    $style_width = round($v['product_width'] * $ratio);
                                    $style_top = 0;
                                    $style_left = round((500 - $style_width) / 2);                                    
                                }
                            ?>
                            <div class="nbdesigner-image-original <?php if($v['bg_type'] == 'tran') echo "background-transparent"; ?>"
                                style="width: <?php echo $style_width; ?>px;
                                       height: <?php echo $style_height; ?>px;
                                       left: <?php echo $style_left; ?>px;
                                       top: <?php echo $style_top; ?>px;
                                <?php if($v['bg_type'] == 'color') echo 'background: ' .$v['bg_color_value']?>"       
                            >
                                <img src="<?php echo $v['img_src']; ?>" 
                                    <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>
                                     class="designer_img_src "
                                    />
                            </div>
                            <?php $overlay_style = 'none'; if($v['show_overlay']) $overlay_style = 'block'; ?>
                            <div class="nbdesigner-image-overlay"
                                style="width: <?php echo $v['area_design_width']; ?>px;
                                       height: <?php echo $v['area_design_height']; ?>px;
                                       left: <?php echo $v['area_design_left']; ?>px;
                                       top: <?php echo $v['area_design_top']; ?>px;
                                       display: <?php echo $overlay_style; ?>"                                 
                            >
                                <img src="<?php echo $v['img_overlay']; ?>" class="img_overlay"/>
                            </div>
                            <div class="nbdesigner-area-design" id="nbdesigner-area-design-<?php echo $k; ?>" 
                                 style="width: <?php echo $v['area_design_width'] . 'px'; ?>; 
                                        height: <?php echo $v['area_design_height'] . 'px'; ?>; 
                                        left: <?php echo $v['area_design_left'] . 'px'; ?>; 
                                        top: <?php echo $v['area_design_top'] . 'px'; ?>;"> </div>
                        </div>
                        <input type="hidden" class="hidden_img_src" name="_designer_setting[<?php echo $k; ?>][img_src]" value="<?php echo $v['img_src']; ?>" >
                        <input type="hidden" class="hidden_img_src_top" name="_designer_setting[<?php echo $k; ?>][img_src_top]" value="<?php echo $v['img_src_top']; ?>">
                        <input type="hidden" class="hidden_img_src_left" name="_designer_setting[<?php echo $k; ?>][img_src_left]" value="<?php echo $v['img_src_left']; ?>">
                        <input type="hidden" class="hidden_img_src_width" name="_designer_setting[<?php echo $k; ?>][img_src_width]" value="<?php echo $v['img_src_width']; ?>">
                        <input type="hidden" class="hidden_img_src_height" name="_designer_setting[<?php echo $k; ?>][img_src_height]" value="<?php echo $v['img_src_height']; ?>">
                        <input type="hidden" class="hidden_overlay_src" name="_designer_setting[<?php echo $k; ?>][img_overlay]" value="<?php echo $v['img_overlay']; ?>">
                        <input type="hidden" class="hidden_nbd_version" name="_designer_setting[<?php echo $k; ?>][version]" value="<?php echo $v['version']; ?>">
                        <div>	
                            <a class="button nbdesigner_move nbdesigner_move_left" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'left')">Left</a>
                            <a class="button nbdesigner_move nbdesigner_move_right" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'right')">Right</a>
                            <a class="button nbdesigner_move nbdesigner_move_up" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'up')">Up</a>
                            <a class="button nbdesigner_move nbdesigner_move_down" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'down')">Down</a>
                            <a class="button nbdesigner_move nbdesigner_move_center" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'center')">Center</a>
                            <a class="button nbdesigner_move nbdesigner_move_center" style="padding-left: 7px; padding-right: 7px;" data-index="<?php echo $k; ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'fit')"><i class="mce-ico mce-i-dfw" style="margin: 4px 0px 0px 0px !important; padding: 0 !important;"></i>Fit</a>
                        </div>
                        <div>
                            <p>
                                <label for="nbdesigner_bg_type" class="nbdesigner-setting-box-label"><?php echo $this->__('Background type'); ?></label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo $k; ?>][bg_type]" value="image" 
                                    <?php checked($v['bg_type'], 'image', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php echo $this->__('Image'); ?></label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo $k; ?>][bg_type]" value="color" 
                                    <?php checked($v['bg_type'], 'color', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php echo $this->__('Color'); ?></label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo $k; ?>][bg_type]" value="tran" 
                                    <?php checked($v['bg_type'], 'tran', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php echo $this->__('Transparent'); ?></label>
                            </p>
                        </div> 
                        <div class="nbdesigner_bg_image" <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>>
                            <a class="button nbdesigner-button nbdesigner-add-image" onclick="openImagesPopup(this, 'img-area'); /* NBDESIGNADMIN.loadImage(this) */" data-index="<?php echo $k; ?>"><?php echo $this->__('Set image'); ?></a>     
                        </div>
                        <div class="nbdesigner_bg_color" <?php if($v['bg_type'] != 'color') echo ' style="display: none;"' ?>>
                            <input type="text" name="_designer_setting[<?php echo $k; ?>][bg_color_value]" value="<?php echo $v['bg_color_value'] ?>" class="nbd-color-picker" />
                        </div>
                        <div class="nbdesigner_overlay_box">
                            <label class="nbdesigner-setting-box-label"><?php echo $this->__('Overlay (overlay layer will obove other layers)'); ?></label>
                            <input type="hidden" value="0" name="_designer_setting[<?php echo $k; ?>][show_overlay]" class="show_overlay"/>                   
                            <input type="checkbox" value="1" 
                                name="_designer_setting[<?php echo $k; ?>][show_overlay]" id="_designer_setting[<?php echo $k; ?>][bg_type]" <?php checked($v['show_overlay']); ?> 
                                class="show_overlay" onchange="NBDESIGNADMIN.toggleShowOverlay(this)"/>  
                            <a class="button overlay-toggle" onclick="openImagesPopup(this, 'img-overlay'); /* NBDESIGNADMIN.loadImageOverlay(this) */" style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>">
                                <?php echo __('Set image'); ?>
                            </a>
                            <img style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>"
                                 src="<?php if ($v['img_overlay'] != '') {echo $v['img_overlay'];} else {echo NBDESIGNER_PLUGIN_URL . 'assets/images/overlay.png';} ?>" class="img_overlay"/>                            
                        </div>
                    </div>
                    <div class="nbdesigner-info-box">
                        <?php if($k ==0): ?>
                        <p>
                            <span style="background: #b8dce8; width: 15px; height: 15px; display: inline-block;"></span>&nbsp;<?php echo $this->__('Product area'); ?>&nbsp;
                            <span style="background: #dddacd; width: 15px; height: 15px; display: inline-block;"></span>&nbsp;<?php echo $this->__('Design area'); ?><br />
                            <span style="border:2px solid #f0c6f6; width: 11px; height: 11px; display: inline-block;"></span>&nbsp;<?php echo $this->__('Bounding box'); ?><small> (<?php echo $this->__('product always align vertical/horizontal center bounding box'); ?>)</small>
                        </p>
                        <?php endif; ?>                        
                        <p class="nbd-setting-section-title">
                            <?php echo __('Product size'); ?>
                            <?php if($k ==0): ?>
                            <span class="nbdesign-config-size-tooltip dashicons dashicons-editor-help nbd-helper">[?]
								<span class="tooltiptext">
								<?php
								echo Mage::app()->getLayout()->createBlock('core/template')
											->setTemplate('onlinedesign/help/product_size.phtml')->toHtml(); 
								?>
								</span>
							</span>
                            <?php endif; ?>
                        </p>
                        <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Width'); ?><br /><small>(W<sub>p</sub>)</small></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting[<?php echo $k; ?>][product_width]" 
                                       value="<?php echo $v['product_width']; ?>" class="short product_width" 
                                       onchange="NBDESIGNADMIN.change_dimension_product(this, 'width')"> <?php echo $unit; ?>
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Height'); ?><br /><small>(H<sub>p</sub>)</small></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting[<?php echo $k; ?>][product_height]" 
                                       value="<?php echo $v['product_height']; ?>" class="short product_height"  
                                       onchange="NBDESIGNADMIN.change_dimension_product(this, 'height')"> <?php echo $unit; ?>
                            </div>
                        </div> 
                        <p class="nbd-setting-section-title">
                            <?php echo __('Design area size'); ?>
                            <?php if($k ==0): ?>
                            <span class="nbdesign-config-realsize-tooltip dashicons dashicons-editor-help nbd-helper">[?]
								<span class="tooltiptext">
								<?php
								echo Mage::app()->getLayout()->createBlock('core/template')
											->setTemplate('onlinedesign/help/design_area.phtml')->toHtml(); 
								?>
								</span>
							</span>
                            <?php endif; ?>                              
                        </p>
                        <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Width'); ?><br /><small>(W<sub>d</sub>)</small></label>
                            <div>
                                <input type="number" step="any" name="_designer_setting[<?php echo $k; ?>][real_width]" value="<?php echo $v['real_width']; ?>" class="short real_width" 
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'width')"> <?php echo $unit; ?> 
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Height'); ?><br /><small>(H<sub>d</sub>)</small></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting[<?php echo $k; ?>][real_height]" value="<?php echo $v['real_height']; ?>" class="short real_height"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'height')"> <?php echo $unit; ?> 
                            </div>
                        </div>   
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Top'); ?><br /><small>(T<sub>d</sub>)</small></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting[<?php echo $k; ?>][real_top]" value="<?php echo $v['real_top']; ?>" class="short real_top"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'top')"> <?php echo $unit; ?> 
                            </div>
                        </div> 
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label notice-width nbd-has-notice"><?php echo __('Left'); ?><br /><small>(L<sub>d</sub>)</small></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting[<?php echo $k; ?>][real_left]" value="<?php echo $v['real_left']; ?>" class="short real_left"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'left')"> <?php echo $unit; ?> 
                            </div>
                        </div>                         
                        <p class="nbd-setting-section-title">
                            <?php echo __('Relative position'); ?>&nbsp;
                            <?php if($k == 0): ?> 
                            <span class="nbdesign-config-tooltip dashicons dashicons-editor-help nbd-helper">[?]
								<span class="tooltiptext">
								<?php
								echo Mage::app()->getLayout()->createBlock('core/template')
											->setTemplate('onlinedesign/help/relative_position.phtml')->toHtml(); 
								?>
								</span>
							</span>
                            <?php endif; ?>
                            <span class="dashicons dashicons-update nbdesiger-update-area-design" onclick="NBDESIGNADMIN.updateDesignAreaSize(this)"><?php echo $this->__("[ Update Area Size ]"); ?></span>
                        </p>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Width'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting[<?php echo $k; ?>][area_design_width]" 
                                       value="<?php echo $v['area_design_width']; ?>" class="short area_design_dimension area_design_width" data-index="width" 
                                       onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Height'); ?></label>
                            <div>
                                <input type="number"  step="any" min="0" name="_designer_setting[<?php echo $k; ?>][area_design_height]" value="<?php echo $v['area_design_height']; ?>" class="short area_design_dimension area_design_height" data-index="height" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>	
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Left'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting[<?php echo $k; ?>][area_design_left]" value="<?php echo $v['area_design_left']; ?>" class="short area_design_dimension area_design_left" data-index="left" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>	                        
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php echo __('Top'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting[<?php echo $k; ?>][area_design_top]" value="<?php echo $v['area_design_top']; ?>" class="short area_design_dimension area_design_top" data-index="top" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                                
                            </div>
                        </div>                                                                   
                    </div>	
                </div>
            </div>
    <?php $count++;
endforeach; ?>
        <input type="hidden" value="<?php echo $count; ?>" id="nbdesigner-count-box"/>
    </div>
    <div id="nbdesigner-option" class="nbdesigner-option <?php if (!$enable) echo 'nbdesigner-disable'; ?>">
        <div class="nbdesigner-opt-inner">
            <label for="_nbdesigner_admindesign" class="nbdesigner-setting-box-label"><?php echo $this->__('Admin design template'); ?></label>
            <input type="checkbox" value="1" name="_nbdesigner_option[admindesign]" id="_nbdesigner_admindesign" <?php checked(isset($option['admindesign']) ? $option['admindesign'] : false); ?> class="short"/>
            <?php if($enable && isset($option['admindesign'])): ?>
                <?php if($priority):?>
                <a class="button nbd-admin-tem-link" href="<?php echo $link_admindesign.'&priority=primary&task=edit_template'; ?>" target="_blank">
                    <span class="dashicons dashicons-admin-network"></span> 
                    <?php echo $this->__('Edit Primary Template'); ?>
                </a>
                <a class="button nbd-admin-tem-link" href="<?php echo $link_admindesign.'&priority=extra&task=create_template'; ?>" target="_blank">
                    <span class="dashicons dashicons-plus"></span> 
                    <?php echo $this->__('Add Template'); ?>
                </a>
                <?php else:?>
                <a class="button nbd-admin-tem-link" href="<?php echo $link_admindesign.'&priority=primary&task=create_template'; ?>" target="_blank">
                    <span class="dashicons dashicons-art"></span>
                    <?php echo $this->__('Create Template'); ?>
                </a>
                <?php 
                    endif;
					/* fix code */
                    //$link_manager_template = add_query_arg(array('pid' => $post_id), admin_url('admin.php?page=nbdesigner_admin_template'));
                    $link_manager_template = "";
                ?>
                <a a href="#" onClick="open_manage_template(); return false;" class="button nbd-admin-tem-link">
                    <span class="dashicons dashicons-images-alt"></span>
                    <?php echo $this->__('Manager Templates'); ?>
                </a>
            <?php else: ?>
            <small><?php echo $this->__('After save product, you\'ll see link to start design templates'); ?></small>
            <?php endif; ?>
        </div>  
        <div class="nbdesigner-opt-inner" style="display: none;">
            <label for="_nbdesigner_customprice" class="nbdesigner-setting-box-label"><?php echo $this->__('Custom price'); ?></label>
            <input type="number" step="any" class="short nbdesigner-short-input" id="_nbdesigner_customprice" name="_nbdesigner_option[customprice]" value="<?php if(isset($option['customprice'])) echo $option['customprice']; ?>"/>
        </div>
    </div>    
</div>
<?php
function  add_js_code(){
?><script>
	var $ = jQuery.noConflict();
	jQuery.noConflict();

    jQuery(document).ready( function($) {
		alert("abc");
        var direction = "<?php if(is_rtl()) echo 'right'; else echo 'left'; ?>";
        var options = {
            "content":"<h3>" + "<?php echo $this->__('Notice'); ?>" + "<\/h3>" +
                       "<p>" + "<?php echo $this->__('Bellow values must in range from 0 to 500px'); ?>" + "<\/p>" + 
                       "<p>" + "<?php echo $this->__('There are relative position of design area in bounding box.'); ?>" + "<\/p>" +
                       "<p><img style='max-width: 100%;' src='"+"<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/bounding-box.png'; ?>"+"' /><br /><a href='"+"<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/bounding-box.png'; ?>"+"' target='_blank'>" + "<?php echo $this->__('See detail'); ?>" + "</a></p>",
            "position": {"edge":direction, "align":"center"}
        };
        if ( ! options ) return;
        options = $.extend( options, {
            close: function() {
                //to do
            }
        });
        $('.nbdesign-config-tooltip').first().pointer( options );
        $('.nbdesign-config-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });
        var size_options = {
            "content" : "<h3>" + "<?php echo $this->__('Notice'); ?>" + "<\/h3>" +
                        "<p>"+"<?php echo $this->__('Please upload background image with aspect ratio'); ?>"+": W<sub>p</sub>&timesH<sub>p</sub>.</p>" +
                        "<p>" + "<?php echo $this->__('Make sure setting'); ?>" + " <span style='font-weight: bold; background: #b8dce8;'>" + "<?php echo $this->__('Product size'); ?>" + "</span> " + "<?php echo $this->__('must always be the top priority!'); ?>" + "</p>" +
                        "<p>" + "<?php echo $this->__('You have two order setting options'); ?>" + 
                        ": <br /><strong>1</strong> - <span style='font-weight: bold; background: #b8dce8;'>" + "<?php echo $this->__('Product size'); ?>" + "</span> →"+
                        " <span style='font-weight: bold; background: #dddacd;'>" + "<?php echo $this->__('Design area size'); ?>" + "</span> "+
                        " (<span style='font-weight: bold; background: #f0c6f6;'>" + "<?php echo $this->__('Relative position'); ?>" + "</span> "+"<?php echo $this->__('will automatic update'); ?>"+")" +
                        "<br /><strong>2</strong> - <span style='font-weight: bold; background: #b8dce8;'>" + "<?php echo $this->__('Product size'); ?>" + "</span> →"+
                        " <span style='font-weight: bold; background: #f0c6f6;'>" + "<?php echo $this->__('Relative position'); ?>" + "</span> → "+  
                        "<?php echo $this->__('click'); ?>" + "<span class='dashicons dashicons-update'></span> "+"<?php echo $this->__('to update'); ?>"+" <span style='font-weight: bold; background: #f0c6f6;'>" + "<?php echo $this->__('Design area size'); ?>" + "</span>"+ 
                        "</p>",
            "position": {"edge":direction, "align":"center"}
        };
        $('.nbdesign-config-size-tooltip').first().pointer( size_options );
        $('.nbdesign-config-size-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });
        var da_option = {
            "content" : "<h3>" + "<?php echo $this->__('Notice'); ?>" + "<\/h3>" +
                        "<p>"+"<?php echo $this->__('After change bellow'); ?>"+" <span style='background: #dddacd; font-weight: bold;'>"+"<?php echo $this->__('values'); ?>"+"</span>, "+"<span style='background: #f0c6f6; font-weight: bold;'>"+"<?php echo $this->__('relative position'); ?>"+"</span> "+"<?php echo $this->__('of design area in bounding box will automatic update.'); ?>"+"</p>" +
                        "<p>" + "<?php echo $this->__('Notice'); ?>" + ": W<sub>p</sub> &gt;= W<sub>d</sub> + L<sub>d</sub>" +
                        " | H<sub>p</sub> &gt;= H<sub>d</sub> + T<sub>d</sub>" +
                        "<br />"+"<?php echo $this->__('If color labels change to '); ?>"+"<span style='color: red'>"+"<?php echo $this->__('red'); ?>"+"</span>, "+"<?php echo $this->__('check values again.'); ?>"+"</p>" +                       
                        "<p>"+"<?php echo $this->__('There'); ?>"+" <span style='background: #dddacd; font-weight: bold;'>"+"<?php echo $this->__('values'); ?>"+"</span> "+"<?php echo $this->__('will decide dimensions of output images.'); ?>"+"</p>" +
                        "<p>"+"<?php echo $this->__('If you modify'); ?>"+" <span style='background: #f0c6f6; font-weight: bold;'>"+"<?php echo $this->__('relative position'); ?>"+"</span>, "+"<?php echo $this->__('click button'); ?>"+" <span class='dashicons dashicons-update'></span> "+"<?php echo $this->__('to update'); ?>"+"<span style='background: #dddacd; font-weight: bold;'> "+"<?php echo $this->__('Design area.'); ?>"+"</span>"+"</p>" ,
            "position": {"edge":direction, "align":"center"}            
        };
        $('.nbdesign-config-realsize-tooltip').first().pointer( da_option );
        $('.nbdesign-config-realsize-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });        
    });
</script>
<?php
}
/* fix code */
//add_action("admin_footer", "add_js_code");
?>