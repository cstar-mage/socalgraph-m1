<?php

class Blackbox_RolesPermissions_Block_Cms_Block extends Mage_Cms_Block_Block
{
    public function __construct(array $args = array())
    {
        parent::__construct($args);
    }

    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);

            Mage::dispatchEvent('cms_block_tohtml', array('block' => $block));

            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $helper = Mage::helper('cms');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
                $this->addModelTags($block);
            }
        }
        return $html;
    }
}