<?php

class Cmsmart_Onlinedesign_Block_Adminhtml_Art_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('art_form', array('legend'=>Mage::helper('onlinedesign')->__('Art information')));
     
		$helper = Mage::helper('onlinedesign/data');
		$result = "";
		$list = $helper->nbdesigner_read_json_setting($helper->plugin_path_data().DS. 'arts.json');
		$art_id = $this->getRequest()->getParam('id');
		$art_url = "";
		$art_index_found = $helper->indexFound($art_id, $list, "id");
		if (isset($list[$art_index_found])) {
			$art_data = $list[$art_index_found];
			$art_url  =  $art_data["url"];
			$result = '<img src="'. Mage::getBaseUrl('media') . 'nbdesigner/' . $art_data['path'].'" width="100" />';
		}
		
	  $fieldset->addField('note', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__(''),
          'name'      => 'note',
		  'text'     => '<b>Choose file upload</b><br />
						Supported file: .svg<br />
						Ex: https://www.svgimages.com
						',
      ));
	  
	  $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('onlinedesign')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
	  $fieldset->addField('image', 'note', array(
          'label'     => Mage::helper('onlinedesign')->__(''),
          'name'      => 'image',
		  'text'     => $result,
      ));

	  $cates = array();
      $cates[''] = '-- Please select cateogry --';
	  $collection = Mage::getModel('onlinedesign/catart')->getCollection();
	  foreach ($collection as $cat) {
		 	$cates[$cat->getId()] = $cat->getTitle();
	  }
      $fieldset->addField('category', 'select', array(
          'label'     => Mage::helper('onlinedesign')->__('Cateogry'),
          'name'      => 'category',
      	  'class'     => 'required-entry',
          'required'  => true,
          'values'    => $cates
      ));
     
	  $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('onlinedesign')->__('Title'),
          'required'  => false,
          'name'      => 'title',
      ));
	  
      if ( Mage::getSingleton('adminhtml/session')->getArtData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getArtData());
          Mage::getSingleton('adminhtml/session')->setArtData(null);
      } elseif ( Mage::registry('art_data') ) {
          $form->setValues(Mage::registry('art_data')->getData());
		  if($art_url != null){
			//$form->getElement('filename')->setValue($art_url);
		  } else {
			  $form->getElement('filename')->setValue("");
		  }
      }
	  
      return parent::_prepareForm();
  }
}