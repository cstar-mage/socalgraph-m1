<?php

/**
 * @method string getId()
 * @method string getDescription()
 * @method int getChargeBasis()
 * @method bool getRevenueProducing()
 * @method float getStandProdUnitsPerH()
 * @method float getHrsPerProdUnit()
 * @method string getMatlQtyPerUnit()
 * @method int getSalesCategory()
 * @method string getMaxQuantity()
 * @method bool getUpdateCurDept()
 * @method string getMatlPrompt()
 * @method string getAskInventory()
 * @method string getAskProdUnit()
 * @method string getAskNonPlannedReason()
 * @method string getAskNotes()
 * @method bool getAddJobTracking()
 * @method int getWipCategory()
 * @method string getCostCenter()
 * @method bool getActive()
 * @method string getAskCounts()
 * @method string getAskPostage()
 * @method string getPlanningIntegration()
 * @method bool getUpdatePlanning()
 * @method bool getExcludeProdUnitsInRollup()
 * @method float getLeadTime()
 * @method float getLagTime()
 * @method bool getAskIfComplete()
 * @method bool getOutsidePurchase()
 * @method string getAskQuantityOfMaterials()
 * @method int getInventoryPrompt()
 * @method bool getIncludeNonInventoryItems()
 * @method bool getCreateActualCosts()
 * @method string getEstimateResultType()
 * @method int getWipDebit()
 * @method int getWipCredit()
 * @method int getCogsDebit()
 * @method int getCogsCredit()
 * @method bool getPaper()
 * @method bool getUseComboSplit()
 * @method int getJobPlanLevel()
 * @method bool getPlanByPass()
 * @method bool getUpdateJobPartLocation()
 * @method int getPlanningTimeCalculation()
 * @method int getPlantManagerDMICategory()
 * @method int getPlantManagerReportCategory()
 * @method int getLaborCostCategory()
 * @method int getLaborOverheadCategory()
 * @method int getMachineCostCategory()
 * @method int getGeneralOACategory()
 * @method int getMarkupCategory()
 * @method int getMaterialOtherCategory()
 * @method int getCostMarkupCategory()
 * @method bool getIncludeInValueAdded()
 * @method bool getIncludeAsCost()
 * @method bool getConsolidateExtras()
 * @method bool getMultipleUpstreamTasks()
 *
 * Class Blackbox_Epace_Model_Epace_Activity_Code
 */
class Blackbox_Epace_Model_Epace_Activity_Code extends Blackbox_Epace_Model_Epace_AbstractObject
{
    protected function _construct()
    {
        $this->_init('ActivityCode', 'id');
    }

    public function getDefinition()
    {
        return [
            'id' => 'string',
            'description' => 'string',
            'chargeBasis' => 'int',
            'revenueProducing' => 'bool',
            'standProdUnitsPerH' => 'float',
            'hrsPerProdUnit' => 'float',
            'matlQtyPerUnit' => 'string',
            'salesCategory' => 'int',
            'maxQuantity' => 'string',
            'updateCurDept' => 'bool',
            'matlPrompt' => 'string',
            'askInventory' => 'string',
            'askProdUnit' => 'string',
            'askNonPlannedReason' => 'string',
            'askNotes' => 'string',
            'addJobTracking' => 'bool',
            'wipCategory' => 'int',
            'costCenter' => 'string',
            'active' => 'bool',
            'askCounts' => 'string',
            'askPostage' => 'string',
            'planningIntegration' => 'string',
            'updatePlanning' => 'bool',
            'excludeProdUnitsInRollup' => 'bool',
            'leadTime' => 'float',
            'lagTime' => 'float',
            'askIfComplete' => 'bool',
            'outsidePurchase' => 'bool',
            'askQuantityOfMaterials' => 'string',
            'inventoryPrompt' => 'int',
            'includeNonInventoryItems' => 'bool',
            'createActualCosts' => 'bool',
            'estimateResultType' => 'string',
            'wipDebit' => 'int',
            'wipCredit' => 'int',
            'cogsDebit' => 'int',
            'cogsCredit' => 'int',
            'paper' => 'bool',
            'useComboSplit' => 'bool',
            'jobPlanLevel' => 'int',
            'planByPass' => 'bool',
            'updateJobPartLocation' => 'bool',
            'planningTimeCalculation' => 'int',
            'plantManagerDMICategory' => 'int',
            'plantManagerReportCategory' => 'int',
            'laborCostCategory' => 'int',
            'laborOverheadCategory' => 'int',
            'machineCostCategory' => 'int',
            'generalOACategory' => 'int',
            'markupCategory' => 'int',
            'materialOtherCategory' => 'int',
            'costMarkupCategory' => 'int',
            'includeInValueAdded' => 'bool',
            'includeAsCost' => 'bool',
            'consolidateExtras' => 'bool',
            'multipleUpstreamTasks' => 'bool',
        ];
    }
}