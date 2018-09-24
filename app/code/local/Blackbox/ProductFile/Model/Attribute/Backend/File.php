<?php

class Blackbox_ProductFile_Model_Attribute_Backend_File extends Jvs_FileAttribute_Model_Attribute_Backend_File
{
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            // START EXTENSION
            unlink(Mage::getBaseDir('media') . '/catalog/product' . $value['value']);
            // END EXTENSION
            return;
        }

        try {
            $uploadedFile = new Varien_Object();
            $uploadedFile->setData('name', $this->getAttribute()->getName());
            $uploadedFile->setData(
                'allowed_extensions',
                array(
                    'jpg',
                    'jpeg',
                    'gif',
                    'png',
                    'tif',
                    'tiff',
                    'mpg',
                    'mpeg',
                    'mp3',
                    'wav',
                    'pdf',
                    'txt',
                )
            );

            Mage::dispatchEvent(
                'jvs_fileattribute_allowed_extensions',
                array('file' => $uploadedFile)
            );

            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions($uploadedFile->getData('allowed_extensions'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $uploader->save(Mage::getBaseDir('media') . '/catalog/product');
        } catch (Exception $e) {
            return $this;
        }

        $fileName = $uploader->getUploadedFileName();

        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            // START EXTENSION
            unlink(Mage::getBaseDir('media') . '/catalog/product' . $value['value']);
            // END EXTENSION
        }

        return $this;
    }
}