<?php

class Blackbox_Import_Helper_File extends Mage_Downloadable_Helper_File
{
    /**
     * Checking file for copying and copy it
     *
     * @param string $baseOrigPath
     * @param string $basePath
     * @param array $file
     * @return string
     */
    public function copyFile($baseOrigPath, $basePath, $file)
    {
        if (isset($file[0])) {
            $fileName = $file[0]['file'];
            if ($file[0]['status'] == 'new') {
                try {
                    $fileName = $this->_copyFile(
                        $baseOrigPath, $basePath, $file[0]['file']
                    );
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                }
            }
            return $fileName;
        }
        return '';
    }

    /**
     * Copy file from tmp path to base path
     *
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string $file
     * @return string
     */
    protected function _copyFile($baseOrigPath, $basePath, $file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->getFilePath($basePath, $file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
            . Mage_Core_Model_File_Uploader::getNewFileName($this->getFilePath($basePath, $file));

        /*Mage::helper('core/file_storage_database')->copyFile(
            $this->getFilePath($baseOrigPath, $file),
            $this->getFilePath($basePath, $destFile)
        );*/
        Mage::helper('core/file_storage_database')->saveFile($this->getFilePath($basePath, $destFile));

        $result = $ioObject->cp(
            $this->getFilePath($baseOrigPath, $file),
            $this->getFilePath($basePath, $destFile)
        );
        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    static protected function _addDirSeparator($dir)
    {
        if (substr($dir,-1) != DIRECTORY_SEPARATOR) {
            $dir.= DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
}