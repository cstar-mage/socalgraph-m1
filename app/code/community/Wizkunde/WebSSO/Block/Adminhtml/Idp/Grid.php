<?php

/**
 * Grid class
 *
 * Class Wizkunde_WebSSO_Block_Adminhtml_Idp_Grid
 */
class Wizkunde_WebSSO_Block_Adminhtml_Idp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Setup the grid basis
     */
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('websso_idp_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return string
     */
    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'websso/idp_collection';
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('identifier',
            array(
                'header'=> $this->__('Identifier'),
                'index' => 'identifier'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}