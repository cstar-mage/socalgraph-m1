<?php

require_once 'abstract.php';

class LimitException extends Exception {}

abstract class Mage_Shell_ImportAbstract extends Mage_Shell_Abstract
{
    public function _construct()
    {
        ini_set("memory_limit","2G");
        set_time_limit(0);
        ini_set('max_execution_time', 0);
    }

    protected $_byLines = false;
    protected $_entityName = 'product';

    protected $_csvDelimiter = ',';
    protected $_csvEnclosure = '"';
    protected $_csvEscape = '\\';
    protected $_offset = 0;

    protected $_skipEmptyRows = true;

    protected function _configure()
    {
        if ($this->getArg('_csv_delimiter')) {
            $this->_csvDelimiter = $this->getArg('csv_delimiter');
        }

        if ($this->getArg('_csv_enclosure')) {
            $this->_csvEnclosure = $this->getArg('csv_enclosure');
        }

        if ($this->getArg('_csv_escape')) {
            $this->_csvEscape = $this->getArg('csv_escape');
        }

        if ($this->getArg('_offset')) {
            $this->_offset = $this->getArg('_offset');
        }
    }

    protected function _processImportAbstractRow(&$csvRow, &$attributesIndexes, $createEntityCallback, $callbackColumns = array())
    {
        $attributes = array();
        foreach ($attributesIndexes as $attribute => $index) {
            if (isset($callbackColumns[$attribute])) {
                $callback = $callbackColumns[$attribute];
                if (is_string($callback) && method_exists($this, $callback)) {
                    $attributes[$attribute] = $this->$callback($csvRow[$index]);
                } else if (is_callable($callback)) {
                    $attributes[$attribute] = $callback($csvRow[$index]);
                } else {
                    throw new Exception('Wrong column callback specified');
                }
            } else {
                $attributes[$attribute] = $csvRow[$index];
            }
        }

        try {
            if (is_string($createEntityCallback) && method_exists($this, $createEntityCallback)) {
                $this->$createEntityCallback($attributes);
            } else if (is_callable($createEntityCallback)) {
                $createEntityCallback($attributes);
            } else {
                throw new Exception('Wrong callback specified');
            }
        } catch (LimitException $e) {
            throw $e;
        } catch (Exception $e) {
            echo "Unable to create $this->_entityName: " . $e->getMessage() . PHP_EOL;
        }
    }

    protected function _prepareAttributes(&$attributeIndexes)
    {

    }

    protected final function _importAbstract($file, $attributesMap, $createEntityCallback, $callbackColumns = array())
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        if (pathinfo($file)['extension'] == 'xlsx') {
            $this->_importAbstractXlsx($file, $attributesMap, $createEntityCallback, $callbackColumns);
        } else {
            $this->_importAbstractCsv($file, $attributesMap, $createEntityCallback, $callbackColumns);
        }
    }

    private function _importAbstractCsv($file, $attributesMap, $createEntityCallback, $callbackColumns = array())
    {
        try {
            if ($this->_byLines) {
                $fileHandle = fopen($file, "r");

                $csv =& $this->_parseCsv(fgets($fileHandle));
            } else {
                $csv =& $this->_getCsv($file);
            }

            $attributesIndexes = $this->_getAttributesIndexes($attributesMap, $csv);
            if (!$attributesIndexes) {
                throw new \Exception('Invalid csv');
            }
            $this->_prepareAttributes($attributesIndexes);

            Mage::app('admin');
            if (!Mage::registry('isSecureArea')) {
                Mage::register('isSecureArea', 1);
            }
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            if ($this->_byLines) {
                $i = 0;
                while (($line = fgets($fileHandle)) !== false) {
                    echo 'Importing ' . ++$i . " $this->_entityName..." . PHP_EOL;
                    if ($i <= $this->_offset) {
                        continue;
                    }
                    $csv =& $this->_parseCsv($line);
                    if ($this->_skipEmptyRows && $this->_isRowEmpty($csv[0])) {
                        continue;
                    }
                    $this->_processImportAbstractRow($csv[0], $attributesIndexes, $createEntityCallback, $callbackColumns);
                }

            } else {
                $lines = count($csv);
                for ($i = 1; $i < $lines; $i++) {
                    echo 'Importing ' . $i . " $this->_entityName..." . PHP_EOL;
                    if ($i <= $this->_offset) {
                        continue;
                    }
                    if ($this->_skipEmptyRows && $this->_isRowEmpty($csv[$i])) {
                        continue;
                    }
                    $this->_processImportAbstractRow($csv[$i], $attributesIndexes, $createEntityCallback, $callbackColumns);
                }
            }
        } finally {
            if (isset($fileHandle)) {
                fclose($fileHandle);
            }
        }
    }

    private function _importAbstractXlsx($file, $attributesMap, $createEntityCallback, $callbackColumns = array())
    {
        $sheets =& $this->_getXlsx($file);

        Mage::app('admin');
        if (!Mage::registry('isSecureArea')) {
            Mage::register('isSecureArea', 1);
        }
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        foreach ($sheets as $name => $sheet) {
            try {
                echo 'Importing sheet ' . $name . '...' . PHP_EOL;
                $this->_importExcelSheetAbstract($sheet, $attributesMap, $createEntityCallback, $callbackColumns);
            } catch (\Exception $e) {
                echo 'Error while importinh sheet: ' . $name . PHP_EOL;
            }
        }
    }

    protected function _importExcelSheetAbstract($sheet, $attributesMap, $createEntityCallback, $callbackColumns = array())
    {
        $attributesIndexes = $this->_getAttributesIndexes($attributesMap, $sheet);
        if (!$attributesIndexes) {
            throw new \Exception('Invalid xlsx');
        }

        $lines = count($sheet);
        for ($i = 1; $i < $lines; $i++) {
            echo 'Processing ' . $i . " $this->_entityName..." . PHP_EOL;
            if ($this->_skipEmptyRows && $this->_isRowEmpty($sheet[$i])) {
                echo 'Row is empty.' . PHP_EOL;
                continue;
            }
            $this->_processImportAbstractRow($sheet[$i], $attributesIndexes, $createEntityCallback, $callbackColumns);
        }
    }

    protected function _getAttributesIndexes(&$attributesMap, &$csv)
    {
        $result = array();

        foreach ($attributesMap as $attribute => $headers) {
            if (is_array($headers)) {
                foreach ($headers as $header) {
                    $key = $this->_getValueKey($header, $csv[0]);
                    if ($key === false) {
                       continue;
                    }
                    $result[$attribute] = $key;
                    break;
                }
                if (!key_exists($attribute, $result)) {
                    return false;
                }
            } else {
                $header = $headers;
                $key = $this->_getValueKey($header, $csv[0]);
                if ($key === false) {
                    return false;
                }
                if (is_numeric($attribute)) {
                    $attribute = $header;
                }
                $result[$attribute] = $key;
            }
        }

        return $result;
    }

    protected function _getValueKey($search, &$array) {
        foreach ($array as $key => $value) {
            if (trim($value) == $search) {
                return $key;
            }
        }
        return false;
    }

    protected  function  &_getCsv($file)
    {
        $contents = file_get_contents($file);

        if ($contents === false) {
            throw new Exception('File not found.');
        }

        return $this->_parseCsv($contents);
    }

    protected function &_getXlsx($zipFile)
    {
        $za = new \ZipArchive();

        $za->open($zipFile);

        $content = file_get_contents("zip://$zipFile#xl/sharedStrings.xml");
        $xml = simplexml_load_string($content);
        $sharedStringsArr = array();
        foreach ($xml->children() as $item) {
            $sharedStringsArr[] = (string)$item->t;
        }
        $out = array();
        for ($i = 0; $i < $za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            if (dirname($stat['name']) != 'xl/worksheets') {
                continue;
            }
            $file = basename($stat['name']);
            //проходим по всем файлам из директории /xl/worksheets/
            if ($file != "." && $file != ".." && $file != '_rels') {
                $content = file_get_contents("zip://$zipFile#xl/worksheets/$file");
                $xml = simplexml_load_string($content);
                //по каждой строке
                $row = 0;
                foreach ($xml->sheetData->row as $item) {
                    $out[$file][$row] = array();
                    //по каждой ячейке строки
                    //$cell = 0;
                    foreach ($item as $child) {
                        $attr = $child->attributes();
                        $value = isset($child->v)? (string)$child->v:false;
                        $cell = $this->_getColumnIndex((string)$attr->r);
                        $out[$file][$row][$cell] = isset($attr['t']) ? $sharedStringsArr[$value] : $value;
                        //$cell++;
                    }
                    $row++;
                }
            }
        }

        return $out;
    }

    protected function _getColumnIndex($column)
    {
        if (!preg_match_all('/[A-Z]/', $column, $matches)) {
            throw new Exception('Cant parse column name');
        }
        $index = 0;
        $exponent = 0;
        foreach (array_reverse($matches[0]) as $match) {
            $index += $exponent * 27 + ord($match) - 65;
            $exponent++;
        }
        return $index;
    }

    protected function &_parseCsv($csv_string)
    {
        $delimiter = $this->_csvDelimiter;
        $enclosure = $this->_csvEnclosure;
        $escape = $this->_csvEscape;

        $Data = str_getcsv($csv_string, "\n", $enclosure, $escape);
        foreach ($Data as &$Row) {
            $Row = str_getcsv($Row, $delimiter, $enclosure, $escape);
        }
        return $Data;
    }

    protected function _isRowEmpty(array &$row)
    {
        foreach ($row as $field) {
            if ($field) {
                return false;
            }
        }
        return true;
    }
}