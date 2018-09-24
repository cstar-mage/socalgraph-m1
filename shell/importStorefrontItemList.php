<?php

require_once 'importAbstract.php';

class Mage_Shell_ImportStorefrontItemList extends Mage_Shell_ImportAbstract
{
    protected $_mediaDir;
    protected $_state = [];
    /** @var Mage_Catalog_Model_Resource_Eav_Attribute */
    protected $_colorAttribute;
    protected $attributesCache = array();

    protected $_defaultAttributeSetId = 4;
    protected $_configurableAttributeSetName = 'Configurable Color';
    protected $_configurableAttributeSetId;

    protected $_skus = [];

    protected $_images = [];

    protected $_modes = [
        'default' => [
            'method' => '_importRow',
            'columns_callbacks' => [
                'category' => '_categoryCallback',
                'sub_category' => '_subCategoryCallback',
                'sub_sub_cat' => '_subSubCategoryCallback',
                'name' => 'trim',
                'color' => '_colorCallback',
                'price' => '_priceCallback'
            ]
        ],
        'qty_on_site' => [
            'method' => '_importQtyOnSite',
            'columns_callbacks' => [
                'name' => 'trim'
            ]
        ],
        'images' => [
            'method' => '_importImages',
            'init' => '_importImagesInit',
            'columns_callbacks' => [
                'category' => '_categoryCallback',
                'sub_category' => '_subCategoryCallback',
                'sub_sub_cat' => '_subSubCategoryCallback',
                'name' => 'trim'
            ]
        ]
    ];

    public function _construct()
    {
        $this->_mediaDir = Mage::getBaseDir('media');
        $this->_entityName = 'row';
    }

    public function run()
    {
        $file = $this->getArg('file');
        if (!$file) {
            echo 'Please, specify file --file.' . PHP_EOL;
            return;
        }

        if ($this->getArg('byLines')) {
            $this->_byLines = true;
        }

        $mode = $this->getArg('mode');
        if (!$mode) {
            $mode = 'default';
        } else {
            if (!array_key_exists($mode, $this->_modes)) {
                echo 'Wrong mode';
                return;
            }
        }

        if ($this->getArg('images')) {
            $this->getFilesRecursively($this->getArg('images'), [ 'jpg', 'png', 'jpeg' ], $this->_images);
        }

        $attributes = [
            'category' => 'Category',
            'category_image' => 'Image ID',
            'sub_category' => 'Sub Category',
            'sub_category_image' => 'Image ID',
            'sub_sub_cat' => 'Sub Sub Cat.',
            'sub_sub_cat_image' => 'Image ID',
            'name' => 'Product List',
            'product_image' => 'Image ID',
            'color' => 'Colors',
            'description' => 'Product Description',
            'accounting_code' => 'Accounting Code',
            'qty_on_site' => 'QTY\'s on site',
            'min_qty' => 'Min Qty',
            'max_qty' => 'Max Qty',
            'price' => 'Price/Pack'
        ];

        $this->_colorAttribute = $this->_getAttribute('color');
        $this->_prepareConfigurableAttribute($this->_colorAttribute);
        $this->_configurableAttributeSetId = $this->_createAttributeSet($this->_configurableAttributeSetName, array($this->_colorAttribute->getId() => 'General'));

        $this->_configure();

        if (isset($this->_modes[$mode]['init'])) {
            $init = $this->_modes[$mode]['init'];
            try {
                $this->$init();
            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                return;
            }
        }

        $this->_importAbstract($file, $attributes, $this->_modes[$mode]['method'], $this->_modes[$mode]['columns_callbacks']);
    }

    protected function _importRow($attributes)
    {
        if (!$attributes['name'])
            return;

        if ($attributes['color']) {
            //$this->_createConfigurableProduct($attributes);
        } else {
            $this->_createSimpleProduct($attributes);
        }
    }

    protected function _importQtyOnSite($attributes)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');

        $sku = $this->getSku($attributes['name']);

        $id = $product->getIdBySku($sku);

        if (!$id) {
            return;
        }

        $product->load($id);

        if (!$product->getId()) {
            return;
        }

        $product->setQtyOnSite($attributes['qty_on_site'])->save();
    }

    protected function _importImages($attributes)
    {
        if ($attributes['name']) {
            $this->_importProductImage($attributes);
        }

        if ($attributes['category'] && $attributes['category_image']) {
            $category = $this->_getCategoryRecursively([
                $attributes['category']
            ]);
            $this->_importCategoryImage($category, $attributes['category_image']);
        }
        if ($attributes['sub_category'] && $attributes['sub_category_image']) {
            $subCategory = $this->_getCategoryRecursively([
                $this->_state['category'],
                $attributes['sub_category']
            ]);
            $this->_importCategoryImage($subCategory, $attributes['sub_category_image']);
        }
        if ($attributes['sub_sub_cat'] && $attributes['sub_sub_cat_image']) {
            $subSubCategory = $this->_getCategoryRecursively([
                $this->_state['category'],
                $this->_state['sub_category'],
                $attributes['sub_sub_cat']
            ]);
            $this->_importCategoryImage($subSubCategory, $attributes['sub_sub_cat_image']);
        }
    }

    protected function _importProductImage($attributes)
    {
        if ($attributes['product_image'] == 'N/A_placeholders') {
            return;
        }
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');

        $sku = $this->getSku($attributes['name']);
        $id = $product->getIdBySku($sku);

        if (!$id) {
            echo "Cannot find product with name {$attributes['name']} by sku $sku\n";
            return;
        }

        $product->load($id);
        if (!$product->getId()) {
            echo "Cannot find product with name {$attributes['name']} by id $id\n";
            return;
        }

        if ($attributes['product_image']) {
            $imageName = pathinfo(urldecode(parse_url($attributes['product_image'], PHP_URL_PATH)))['basename'];
            $image = $this->findImage($imageName);
            if ($image) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                $product->addImageToMediaGallery($image, ['image', 'small_image', 'thumbnail'], false, false);
                $product->save();
                echo "Image imported for product {$attributes['name']}\n";
            } else {
                echo "Cannot find image \"$imageName\" for product {$attributes['name']}\n";
                if ($this->_state['output']) {
                    fputcsv($this->_state['output'], [
                        $attributes['name'],
                        $attributes['product_image']
                    ]);
                }
            }
        } else {
            echo "Product with name \"{$attributes['name']}\" has not image\n";
        }
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param string $imageString
     */
    protected function _importCategoryImage(Mage_Catalog_Model_Category $category, $imageString)
    {
        $imageName = pathinfo(urldecode(parse_url($imageString, PHP_URL_PATH)))['basename'];
        $image = $this->findImage($imageName);
        if ($image) {
            $importedImage = $this->importFile($image, Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category');
            $category->setImage($importedImage);
            $category->save();
            echo "Image imported for category {$category->getName()}\n";
        } else {
            if ($this->_state['output']) {
                fputcsv($this->_state['output'], [
                    $category->getName(),
                    $imageString
                ]);
            }
            echo "Cannot find image \"$imageName\" for category {$category->getName()}\n";
        }
    }

    protected function _importImagesInit()
    {
        if (!$this->getArg('images')) {
            throw new Exception('Argument --images required');
        }
        if (empty($this->_images)) {
            throw new Exception('No images found');
        }
        $outputFile = $this->getArg('output');
        if ($outputFile) {
            $this->_state['output'] = fopen($outputFile, 'w');
            if (!$this->_state['output']) {
                throw new Exception("Cannot open file $outputFile\n");
            }
        }
    }

    protected function _createSimpleProduct($attributes)
    {
        $product = $this->_createProduct('simple', $attributes, $this->_defaultAttributeSetId);
        $product->save();
        return $product;
    }

    /**
     * @param string $type
     * @param array $attributes
     * @param int $attributeSetId
     * @param Mage_Catalog_Model_Product|null $parent
     * @return Mage_Catalog_Model_Product
     */
    protected function _createProduct($type, $attributes, $attributeSetId, $parent = null)
    {
        $product = Mage::getModel('catalog/product')
            ->setTypeId($type)
            ->setStoreId(1)
            ->setWebsiteIds([1])
            ->setAttributeSetId($attributeSetId)
            ->setCreatedAt(strtotime('now'))
            ->setStatus(1)
            ->setTaxClassId(4)
            ->setWeight(1);
        $product->setName($attributes['name']);
        if ($parent) {
            $sku = $parent->getSku();
        } else {
            $sku = $this->getSku($attributes['name']);
        }
        $product->setSku($sku);

        if (!$product->getIdBySku($sku)) {
            $product->setStockData([
                'qty' => $attributes['qty'],
                'use_config_manage_stock' => 0,
                'manage_stock' => 0,
                'is_in_stock' => 1,
                'min_sale_qty' => $attributes['min_qty'],
                'use_config_min_sale_qty' => 0,
                'max_sale_qty' => $attributes['max_qty'],
                'use_config_max_sale_qty' => 0
            ]);
        }

        $product->setDescription($attributes['description']);
        $product->setShortDescription($attributes['description']);
        $product->setPrice($attributes['price']);
        $product->setQtyOnSite($attributes['qty_on_site']);

        if (!$parent) {
            $categories = [
                $this->_state['category'],
                $this->_state['sub_category'],
                $this->_state['sub_sub_cat']
            ];
            $resultCategories = [];
            foreach ($categories as $category) {
                if ($category) {
                    $resultCategories[] = $category;
                } else {
                    break;
                }
            }
            if (!empty($resultCategories)) {
                $product->setCategoryIds([$this->_getCategoryRecursively($resultCategories)->getId()]);
            }
        }

        if ($attributes['product_image']) {
            $imageName = pathinfo(urldecode(parse_url($attributes['product_image'], PHP_URL_PATH)))['basename'];
            $image = $this->findImage($imageName);
            if ($image) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                $product->addImageToMediaGallery($image, [ 'image', 'small_image', 'thumbnail' ], false, false);
            }
        }

        return $product;
    }

    protected function _createConfigurableProduct($attributes)
    {
        $product = $this->_createProduct('configurable', $attributes, $this->_configurableAttributeSetId);

        $configurableAttributesData = array(
            0 => array(
                'id' => NULL,
                'label' => 'Color',
                'position' => NULL,
                'attribute_id' => $this->_colorAttribute->getId(),
                'attribute_code' => 'color',
                'frontend_label' => 'Color'
            ),
        );

        $configurableUsedProductIds = [];
        foreach ($attributes['color'] as $color) {
            if ($color === '' || $color === null) {
                continue;
            }
            if (!$this->isOptionExists($this->_colorAttribute, $color)) {
                $this->addAttributeOptions($this->_colorAttribute, $color);
            }
            $value = $this->getAttributeValue($this->_colorAttribute, $color);
            $child = $this->_createProduct('simple', $attributes, $this->_configurableAttributeSetId, $product);
            $child->setColor($value);
            $child->setSku($child->getSku() . '_' . $color);
            $child->save();

            $colorAttributeData = array(
                'label' => $product->getAttributeText('color'),
                'attribute_id' => $this->_colorAttribute->getId(),
                'value_index' => (int)$product->getColor(),
                'is_percent' => 0,
                'pricing_value' => $product->getPrice()
            );

            $configurableAttributesData[0]['values'][] = $colorAttributeData;
            $configurableUsedProductIds[] = $child->getId();
        }

        $product->setCanSaveConfigurableAttributes(true);
        $product->setConfigurableAttributesData($configurableAttributesData);
        $product->save();

        Mage::getResourceSingleton('catalog/product_type_configurable')
            ->saveProducts($product, $configurableUsedProductIds);

        return $product;
    }

    protected function _categoryCallback($category)
    {
        $category = trim($category);
        if ($category) {
            $this->_state['category'] = $category;
            $this->_state['sub_category'] = null;
            $this->_state['sub_sub_category'] = null;
        }
        return $category;
    }

    protected function _subCategoryCallback($category)
    {
        $category = trim($category);
        if ($category) {
            $this->_state['sub_category'] = $category;
            $this->_state['sub_sub_category'] = null;
        }
        return $category;
    }

    protected function _subSubCategoryCallback($category)
    {
        $category = trim($category);
        if ($category) {
            $this->_state['sub_sub_cat'] = $category;
        }
        return $category;
    }

    protected function _colorCallback($color)
    {
        if ($color) {
           return array_filter(explode(',', str_replace('&',',',$color)), 'trim');
        }
        return $color;
    }

    protected function _priceCallback($price)
    {
        if ($price && preg_match('/\$\s*(\d*)[,\.](\d*)\s*/', $price, $matches)) {
            return $matches[1] . '.' . $matches[2];
        }
        return 0;
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
                    unset($csv[0][$key]);
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
                unset($csv[0][$key]);
            }
        }

        return $result;
    }

    protected function _prepareConfigurableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        $save = false;
        if (!$attribute->getData('is_configurable')) {
            $attribute->setData('is_configurable', '1');
            $save = true;
        }
        if ($attribute->getData('frontend_input') != 'select')
            throw new Exception("Attribute {$attribute->getAttributeCode()} is not select");
        $applyTo = explode(',',$attribute->getData('apply_to'));
        if (!in_array('configurable', $applyTo)) {
            $applyTo[] = 'configurable';
            $attribute->setData('apply_to', implode(',', $applyTo));
            $save = true;
        }
        if ($save) {
            $attribute->save();
        }
        $this->attributesCache['color'] = $attribute->getSource()->getAllOptions(true, true);
    }

    /**
     * @param string $code
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     * @throws Exception
     */
    protected function _getAttribute($code)
    {
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
        if (!$attribute->getId())
            throw new Exception('Cant find attribute ' . $code);
        return $attribute;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string $option
     * @return bool
     */
    protected function isOptionExists(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $option)
    {
        foreach ($this->attributesCache[$attribute->getAttributeCode()] as $value) {
            if ($value['label'] == $option) {
                return true;
            }
        }
        return false;
    }

    protected function addAttributeOptions(Mage_Catalog_Model_Resource_Eav_Attribute &$attribute, $option)
    {
        $value['option'] = array($option, $option);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        $attribute->save();

        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute->getAttributeCode());
        $this->attributesCache[$attribute->getAttributeCode()] = $attribute->getSource()->getAllOptions(true, true);
    }

    protected function getAttributeValue(Mage_Eav_Model_Entity_Attribute $attribute, $label)
    {
        if (!key_exists($attribute->getAttributeCode(), $this->attributesCache) == true) {
            $this->attributesCache[$attribute->getAttributeCode()] = $attribute->getSource()->getAllOptions(true, true);
        }
        foreach ($this->attributesCache[$attribute->getAttributeCode()] as $value) {
            if ($value['label'] == $label) {
                return $value['value'];
            }
        }
        return null;
    }

    protected function _createAttributeSet($name, $newAttributes, $parentId = 4)
    {
        $attibuteSet = Mage::getModel("eav/entity_attribute_set")->load($name, 'attribute_set_name');
        if ($attibuteSet->getId()) {
            return $attibuteSet->getId();
        }
        echo 'Creating attribute set ' . $name . '...' . PHP_EOL;

        try {
            /** @var Mage_Catalog_Model_Product_Attribute_Set_Api */
            $id = Mage::getModel('catalog/product_attribute_set_api')
                ->create($name, $parentId);
        } catch (Exception $e) {
            throw new Exception('Unable to create attribute set "' . $name . '" : ' . $e->getMessage());
        }

        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

        foreach ($newAttributes as $attributeId => $groupName) {
            $attributeGroupId = $setup->getAttributeGroupId('catalog_product', $id, $groupName);
            $setup->addAttributeToSet('catalog_product', $id, $attributeGroupId, $attributeId);
        }

        return $id;
    }

    /**
     * @param array $path
     * @param Mage_Catalog_Model_Category|null $parent
     * @return Mage_Catalog_Model_Category|null
     */
    protected function _getCategoryRecursively($path, $parent = null)
    {
        $current = array_shift($path);
        if (!$parent) {
            $parentId = 2;
        } else {
            $parentId = $parent->getId();
        }
        $current = ucwords(strtolower($current));
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToFilter('name', $current)
            ->addAttributeToFilter('parent_id', $parentId)
            ->load();

        if ($collection->count() > 0) {
            $category = $collection->getFirstItem();
        } else {
            $category = Mage::getModel('catalog/category');
            $category->setName($current);
            $category->setUrlKey($current);
            //$category->setImage($image);
            $category->setIsActive(1);
            $category->setDisplayMode('PRODUCTS');
            $category->setIsAnchor(0);
            $category->setStoreId(1);
            if (!$parent) {
                $parent = Mage::getModel('catalog/category')->load($parentId);
            }
            $category->setPath($parent->getPath());
            $category->save();
        }

        if (empty($path)) {
            return $category;
        } else {
            return $this->_getCategoryRecursively($path, $category);
        }
    }

    protected function getFilesRecursively($directory, $ext, &$files = array())
    {
        $dirContents = scandir($directory);
        foreach ($dirContents as $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }
            $fullName = $directory . DS . $name;
            if (is_dir($fullName)) {
                $this->getFilesRecursively($fullName, $ext, $files);
            } else {
                $curExt = strtolower(pathinfo($name)['extension']);
                if (is_array($ext) && in_array($curExt, $ext) || $curExt == $ext) {
                    $files[] = $fullName;
                }
            }
        }
    }

    protected function findImage($name)
    {
        foreach ($this->_images as $image) {
            if (pathinfo($image)['basename'] == $name) {
                return $image;
            }
        }
        return false;
    }

    protected function importFile($file, $destPath)
    {
        $pathInfo = pathinfo($file);

        $fileInfo[] = array(
            'file' => $pathInfo['basename'],
            'name' => $pathInfo['basename'],
            'size' => filesize($file),
            'status' => 'new'
        );

        $dispertionPath = Mage_Core_Model_File_Uploader::getDispretionPath($pathInfo['basename']);

        $resultFileName = Mage::helper('blackbox_import/file')->copyFile(
            $pathInfo['dirname'],
            $destPath . $dispertionPath,
            $fileInfo
        );

        if (substr($resultFileName, 0, 1) == '.') {
            $resultFileName = substr($resultFileName, 1);
        }

        return $dispertionPath . $resultFileName;
    }

    protected function getSku($name)
    {
        $sku = $name;
        if ($this->_skus[$sku] > 0) {
            $sku .= ' ' . $this->_skus[$sku];
        }
        $this->_skus[$sku]++;
        return $sku;
    }

    public function __destruct()
    {
        if ($this->_state['output']) {
            fclose($this->_state['output']);
        }
    }
}

$shell = new Mage_Shell_ImportStorefrontItemList();
$shell->run();