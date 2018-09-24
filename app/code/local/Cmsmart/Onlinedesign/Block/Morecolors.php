<?php
class Cmsmart_Onlinedesign_Block_Morecolors 
extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('hex', array(
            'label' => $this->__('Hexa Color'),
            'style' => 'width:120px',
        	'default'   =>   'default',
        ));
		
		
		$this->addColumn('name', array(
            'label' => $this->__('Color Name'),
            'style' => 'width:200px',
			'default'   =>    'Default'
        ));	
						 
        $this->_addAfter = false;
        $this->_addButtonLabel = $this->__('Add More Color');

    }
}