<?php //if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="config_draw" class="shadow od_tab nbdesigner_config" ng-style="{'display': pop.draw}">
    <ul class="config_list" id="draw_config_list">
        <li><a href="#free_draw" ng-click="changeDrawMode()"><span class="fa fa-paint-brush" aria-hidden="true"></span></a></li>   
        <li><a href="#draw_shape" ng-click="disableDrawMode()"><span class="fa fa-star" aria-hidden="true"></span></a></li>                       
    </ul>
    <div class="list-indicator"></div>
    <div id="draw_shape" class="nbdesigner_config_content content" ng-show="settings['nbdesigner_draw_shape'] == 1">
        <div class="nb-col-2">
            <p class="label-config"><?php echo $this->__("Geometrical") ?></p>
            <div class="btn-group dropup">
                <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown"><?php echo $this->__("Shape") ?>&nbsp;<span class="caret"></span></button>
                <ul class="dropdown-menu dropup  shadow hover-shadow">
                    <li><a href="javascript:void(0);" ng-click="addRect()"><?php echo $this->__("Rectangle") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="addCircle()"><?php echo $this->__("Circle") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="addTriangle()"><?php echo $this->__("Triangle") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="addLine()"><?php echo $this->__("Line") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="addPolygon()"><?php echo $this->__("Polygon") ?></a></li>                
                </ul>                
            </div>
        </div>
        <div class="nb-col-30" style="padding-left: 15px;">
            <p class="label-config"><?php echo $this->__("Color") ?></p>
            <?php  if($enableColor == 'yes'): ?>
            <input readonly="true" disabled class="jscolor shadow hover-shadow" value="{{colorShape}}" ng-model="colorShape" ng-change="setShapeColor(colorShape)">
            <?php else: ?>
            <spectrum-colorpicker
                ng-model="colorShape" 
                ng-change="setShapeColor(colorShape)" 
                options="{
                    showPaletteOnly: true, 
                    togglePaletteOnly: false, 
                    hideAfterPaletteSelect:true,
                    palette: colorPalette}">
            </spectrum-colorpicker>       
            <?php endif; ?>           
        </div>  
        <div class="nb-col-2" ng-show="shapeMode">
            <p class="label-config label-rotate"><?php echo $this->__("Rotate") ?></p>
            <div class="rotation-text"><input type="text" id="rotation-shape" data-min="0" data-max="359"></div>
        </div>  
        <div class="nb-col-30" ng-show="shapeMode">
            <p class="label-config"><?php echo $this->__("Opacity") ?></p>
            <div class="container-dg-slider"><div class="dg-slider" id="opacity_shape"></div></div>					
        </div>         
    </div>
    <div id="free_draw" class="nbdesigner_config_content content" ng-show="settings['nbdesigner_draw_brush'] == 1">
        <div class="nb-col-2 has-popover-option">
            <p class="label-config"><?php echo $this->__("Mode") ?></p>
            <div class="btn-group dropup">
                <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown"><?php echo $this->__("Brush") ?>&nbsp;<span class="caret"></span></button>
                <ul class="dropdown-menu dropup  shadow hover-shadow">
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('Pencil')"><?php echo $this->__("Pencil") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('Circle')"><?php echo $this->__("Circle") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('Spray')"><?php echo $this->__("Spray") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('Pattern')"><?php echo $this->__("Pattern") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('hline')"><?php echo $this->__("Horizontal line") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('vline')"><?php echo $this->__("Vertical line") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('square')"><?php echo $this->__("Square") ?></a></li>
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('diamond')"><?php echo $this->__("Diamond") ?></a></li>                    
                    <li><a href="javascript:void(0);" ng-click="setDrawingMode('texture')"><?php echo $this->__("Texture") ?></a></li>                    
                </ul>
            </div> 
            <span class="toggle-popup-option fa fa-chevron-down hover-shadow e-shadow c-option" style="display: none;" id="show_popover_option_draw"></span>
        </div>
        <div class="nb-col-2">
            <p class="label-config"><?php echo $this->__("Color") ?></p>
            <?php  if($enableColor == 'yes'): ?>
            <input readonly="true" disabled class="jscolor shadow hover-shadow" value="{{colorBrush}}" ng-model="colorBrush" ng-change="setDrawingLineColor(colorBrush)">
            <?php else: ?>
            <spectrum-colorpicker
                ng-model="colorBrush" 
                ng-change="setDrawingLineColor(colorBrush)" 
                options="{
                    showPaletteOnly: true, 
                    togglePaletteOnly: false, 
                    hideAfterPaletteSelect:true,
                    palette: colorPalette}">
            </spectrum-colorpicker>       
            <?php endif; ?>      
        </div> 
        <div class="nb-col-30">
            <p class="label-config"><?php echo $this->__("Brush width") ?></p>
            <div class="container-dg-slider"><div class="dg-slider" id="brush_width"></div></div>					
        </div>
        <div class="nb-col-30">
            <p class="label-config"><?php echo $this->__("Shadow width") ?></p>
            <div class="container-dg-slider"><div class="dg-slider" id="brush_shadow_width"></div></div>					
        </div>        
    </div>  
</div>