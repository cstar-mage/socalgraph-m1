<?php

class Blackbox_Barcode_Model_Barcode
{
    protected $product;
    protected $filetype;
    protected $type;

    protected $widthFactor = 2;
    protected $totalHeight = 30;
    protected $color = 'black';

    protected $defaultDataSource = 'inventory_number';

    protected static $colors = [
        'black' => [0, 0, 0],
        'red' => [255, 0, 0],
        'green' => [0, 255, 0],
        'blue' => [0, 0, 255],
        'cyan' => [0, 255, 255],
        'yellow' => [255, 255, 0],
        'magenta' => [255, 0, 255],
        'orange' => [255, 102, 0],
        'white' => [255, 255, 255]
    ];

    public function __construct(Mage_Catalog_Model_Product $product, $filetype, $type, $defaultDataSource)
    {
        $this->product = $product;
        $this->filetype = $filetype;
        $this->type = $type;
        $this->defaultDataSource = $defaultDataSource;
    }

    public function getFileSystemPath()
    {
        return Mage::getBaseDir('media') . '/barcode/' . $this->product->getId() . '.' . $this->filetype;
    }

    public function getUrl($create = false)
    {
        if ($create) {
            $this->generate();
        }
        return Mage::getBaseUrl('media') . '/barcode/' . $this->product->getId() . '.' . $this->filetype;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getFiletype()
    {
        return $this->filetype;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getWidthFactor()
    {
        return $this->widthFactor;
    }

    public function getTotalHeight()
    {
        return $this->totalHeight;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getText()
    {
        return $this->product->getData($this->getDataSource());
    }

    /**
     * @param int $widthFactor
     * @return $this
     */
    public function setWidthFactor($widthFactor)
    {
        $this->widthFactor = $widthFactor;

        return $this;
    }

    /**
     * @param int $totalHeight
     * @return $this
     */
    public function setTotalHeight($totalHeight)
    {
        $this->totalHeight = $totalHeight;

        return $this;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    public function generate()
    {
        $path = $this->getFileSystemPath();
        if (is_file($path)) {
            return true;
        }
        return $this->_generate();
    }

    public function regenerate()
    {
        $this->delete();
        return $this->_generate();
    }

    public function delete()
    {
        $path = $this->getFileSystemPath();
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function __toString()
    {
        try {
            return $this->getUrl(true);
        } catch (Exception $e) {
            return '';
        }
    }

    protected function _generate()
    {
        if (!$this->getText()) {
            Mage::throwException('Text empty.');
        }
        $path = $this->getFileSystemPath();
        $generator = $this->getGenerator();
        $barcode = $generator->getBarcode($this->getText(), $this->type, $this->getWidthFactor(), $this->getTotalHeight(), $this->_getColor());
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }
        $file = fopen($path, 'w+');
        fwrite($file, $barcode);
        fclose($file);

        return true;
    }

    protected function _getColor()
    {
        $color = strtolower(trim($this->getColor()));
        switch ($this->filetype) {
            case 'jpg':
            case 'png':
                if ($color) {
                    if (isset(self::$colors[$color])) {
                        return self::$colors[$color];
                    }
                    if (preg_match('/^#?([0-9A-Fa-f]{1-6})&/', $color, $match)) {
                        $number = $match[1];
                        if (strlen($number) < 6) {
                            $number = str_pad($number, 6, '0', STR_PAD_LEFT);
                        }
                        preg_match('/^(..)(..)(..)&/', $number, $match);
                        return [
                            hexdec($match[1]),
                            hexdec($match[2]),
                            hexdec($match[3])
                        ];
                    } else if (preg_match('/(\d+).*(\d+).*(\d+)/', $color, $match)) {
                        return [
                            (int)$match[1],
                            (int)$match[2],
                            (int)$match[3]
                        ];
                    }
                }
                return [0, 0, 0];
            case 'svg':
            case 'html':
                return $color;
            default:
                Mage::throwException('Wrong barcode filetype');
        }
    }

    /**
     * @return \Picqer\Barcode\BarcodeGeneratorHTML|\Picqer\Barcode\BarcodeGeneratorJPG|\Picqer\Barcode\BarcodeGeneratorPNG|\Picqer\Barcode\BarcodeGeneratorSVG
     */
    protected function getGenerator()
    {
        switch ($this->filetype) {
            case 'jpg':
                return new \Picqer\Barcode\BarcodeGeneratorJPG();
            case 'png':
                return new \Picqer\Barcode\BarcodeGeneratorPNG();
            case 'svg':
                return new \Picqer\Barcode\BarcodeGeneratorSVG();
            case 'html':
                return new \Picqer\Barcode\BarcodeGeneratorHTML();
            default:
                Mage::throwException('Wrong barcode filetype');
        }
    }

    protected function getDataSource()
    {
        return $this->product->getBarcodeDataSource() ?: $this->defaultDataSource;
    }
}