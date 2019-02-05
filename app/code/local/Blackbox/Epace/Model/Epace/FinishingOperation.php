<?php

class Blackbox_Epace_Model_Epace_FinishingOperation extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('FinishingOperation', 'id');
    }

    /**
     * @return Blackbox_Epace_Model_Epace_FinishingOperation_Speed[]
     */
    public function getSpeeds()
    {
        /** @var Blackbox_Epace_Model_Resource_Epace_FinishingOperation_Speed_Collection $collection */
        $collection = Mage::getResourceModel('efi/finishingOperation_speed_collection');
        $collection->addFilter('finishingOperation', $this->getId());
        $items = $collection->getItems();
        foreach ($items as $item) {
            $item->setFinishingOperation($this);
        }
        return $items;
    }

    public function getDefinition()
    {
        return [
            'id' => 'int',
            'description' => '',
            'quantityCalcMethod' => '',
            'materialsActivityCode' => '',
            'makeReadyActivityCode' => '',
            'runActivityCode' => '',
            'helperActivityCode' => '',
            'quantityMultiplier' => '',
            'finishingGroup' => '',
            'unitLabel' => '',
            'category' => '',
            'rotateStack' => 'bool',
            'active' => 'bool',
            'useMachineActivityCodes' => 'bool',
            'customerViewable' => 'bool',
            'nonComboQuantity' => 'bool',
            'useUptoSize' => 'bool',
            'removeIfNotRequired' => 'bool',
            'materialsIncludedInAdditionalPieceWeight' => 'bool',
        ];
    }
}