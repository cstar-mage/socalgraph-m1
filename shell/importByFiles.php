<?php
require_once 'abstract.php';

class Mage_Shell_ImportByFiles extends Mage_Shell_Abstract
{
    protected $allowedExtensions = [
        'jpg',
        'jpeg',
        'png'
    ];

    public function run()
    {
        $dir = $this->getArg('dir');
        if (!$dir) {
            $this->writeln('Please, specify directory with --dir parameter.');
            return;
        }

        try {
            $tree = $this->buildTree($dir);
            $this->import($tree);
            $this->writeln('Success.');
        } catch (Exception $e) {
            $this->writeln($e->getMessage());
        }
    }

    protected function buildTree($directory)
    {
        if (!is_dir($directory)) {
            throw new Exception($directory . ' is not directory.');
        }
        $result = [];
        $dirContents = scandir($directory);
        foreach ($dirContents as $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }
            $fullName = $directory . DS . $name;
            if (is_dir($fullName)) {
                $result[] = [
                    'type' => 'category',
                    'name' => $name,
                    'children' => $this->buildTree($fullName)
                ];
            } else if (in_array(strtolower(pathinfo($name)['extension']), $this->allowedExtensions)) {
                $result[] = [
                    'type' => 'product',
                    'name' => pathinfo($name)['filename'],
                    'file' => $fullName
                ];
            }
        }

        return $result;
    }

    protected function import($tree)
    {
        $category = Mage::getModel('catalog/category')->load(2);
        $this->importCategoryChildren($tree, $category);
    }

    /**
     * @param array $node
     * @param Mage_Catalog_Model_Category $category
     */
    protected function importCategoryChildren($node, $category)
    {
        foreach ($node as $item)
        {
            switch ($item['type']) {
                case 'category':
                    $this->importCategory($item, $category);
                    break;
                case 'product':
                    $this->importProduct($item, $category);
                    break;
            }
        }
    }

    /**
     * @param array $node
     * @param Mage_Catalog_Model_Category $category
     */
    protected function importProduct($node, $category)
    {
        $name = $this->getName($node['name']);

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setStoreId(1)
            ->setWebsiteIds([1])
            ->setAttributeSetId(Mage::getModel('catalog/product')->getDefaultAttributeSetId())
            ->setCreatedAt(strtotime('now'))
            ->setStatus(1)
            ->setTaxClassId(4)
            ->setWeight(1)
            ->setName($name)
            ->setSku('temp-sku-' . $node['name']);

        $product->setStockData([
            'qty' => 100,
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'is_in_stock' => 1,
            'min_sale_qty' => 0,
            'use_config_min_sale_qty' => 0,
            'max_sale_qty' => 0,
            'use_config_max_sale_qty' => 0
        ]);

        $product->setDescription($name);
        $product->setShortDescription($name);
        $product->setPrice(0);

        $product->setCategoryIds([$category->getId()]);

        $product->setMediaGallery(array('images' => array(), 'values' => array()));
        $product->addImageToMediaGallery($node['file'], [ 'image', 'small_image', 'thumbnail' ], false, false);

        $product->save();

        $product->setSku('TG-' . str_pad($product->getId(), 3, '0', STR_PAD_LEFT));
        $product->getResource()->getWriteConnection()->update($product->getResource()->getEntityTable(), ['sku' => $product->getSku()], 'entity_id = ' . $product->getId());

        $this->writeln('Imported product ' . $name);
    }

    /**
     * @param array $node
     * @param Mage_Catalog_Model_Category $parent
     */
    protected function importCategory($node, $parent)
    {
        $name = $this->getName($node['name']);
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToFilter('name', $name)
            ->addAttributeToFilter('parent_id', $parent->getId());

        if ($collection->count() > 0) {
            $category = $collection->getFirstItem();
        } else {
            $category = Mage::getModel('catalog/category');
            $category->setName($name);
            $category->setUrlKey($node['name']);
            $category->setIsActive(1);
            $category->setDisplayMode('PRODUCTS');
            $category->setIsAnchor(0);
            $category->setStoreId(1);
            $category->setPath($parent->getPath());
            $category->save();
            $this->writeln('Created category ' . $name);
        }

        $this->importCategoryChildren($node['children'], $category);
    }

    protected function getName($name)
    {
        return ucwords(strtolower(str_replace('-', ' ', $name)));
    }

    protected function writeln($message)
    {
        echo $message . PHP_EOL;
    }
}

$shell = new Mage_Shell_ImportByFiles();
$shell->run();