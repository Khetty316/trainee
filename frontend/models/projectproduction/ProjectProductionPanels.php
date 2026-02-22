<?php

namespace frontend\models\ProjectProduction;

use Yii;
use frontend\models\projectquotation\ProjectQPanels;
use frontend\models\common\RefProjectQPanelUnit;
use common\models\User;
//use frontend\models\ProjectProduction\RefProjProdBqStatus;
use frontend\models\ProjectProduction\electrical\TaskAssignElec;
use frontend\models\ProjectProduction\fabrication\TaskAssignFab;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefProjectQTypes;
use frontend\models\test\TestMain;
use frontend\models\projectproduction\electrical\ProdElecTaskWeight;
use frontend\models\projectproduction\fabrication\ProdFabTaskWeight;
use frontend\models\bom\BomMaster;

/**
 * This is the model class for table "project_production_panels".
 *
 * @property int $id
 * @property string|null $project_production_panel_code
 * @property int $proj_prod_master
 * @property int|null $panel_id
 * @property string|null $panel_description
 * @property string|null $panel_type
 * @property string|null $remark
 * @property float|null $amount
 * @property int $sort
 * @property int|null $quantity
 * @property string|null $unit_code
 * @property string|null $finalized_at
 * @property int|null $finalized_by
 * @property string|null $item_dispatch_status
 * @property string|null $design_completed_at
 * @property int|null $design_completed_by
 * @property string|null $material_completed_at
 * @property int|null $material_completed_by
 * @property float|null $fab_assign_percent
 * @property float|null $fab_complete_percent
 * @property string|null $fab_completed_at
 * @property int|null $fab_completed_by
 * @property string|null $fab_work_status
 * @property int|null $fab_dispatch_wire_quantity
 * @property float|null $elec_assign_percent
 * @property float|null $elec_complete_percent
 * @property string|null $elec_completed_at
 * @property int|null $elec_completed_by
 * @property string|null $elec_work_status
 * @property string|null $filename
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property BomMaster[] $bomMasters
 * @property ProdElecTaskWeight[] $prodElecTaskWeights
 * @property ProdFabTaskWeight[] $prodFabTaskWeights
 * @property ProductionElecTasks[] $productionElecTasks
 * @property RefProjProdTaskElec[] $elecTaskCodes
 * @property ProductionFabTasks[] $productionFabTasks
 * @property RefProjProdTaskFab[] $fabTaskCodes
 * @property ProjectProductionMaster $projProdMaster
 * @property RefProjectQTypes $panelType
 * @property ProjectQPanels $panel
 * @property RefProjectQPanelUnit $unitCode
 * @property User $designCompletedBy
 * @property User $materialCompletedBy
 * @property User $fabCompletedBy
 * @property User $elecCompletedBy
 * @property RefProjProdPanelWorkStatus $fabWorkStatus
 * @property RefProjProdPanelWorkStatus $elecWorkStatus
 * @property StockOutboundMaster[] $stockOutboundMasters
 * @property TaskAssignElec[] $taskAssignElecs
 * @property TaskAssignFab[] $taskAssignFabs
 * @property TestMain[] $testMains
 */
class ProjectProductionPanels extends \yii\db\ActiveRecord {

    public $isSubmission;
    public $scannedFile;

    CONST runningNoLength = 3;

    public static function tableName() {
        return 'project_production_panels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            [['proj_prod_master', 'quantity', 'unit_code', 'panel_description', 'panel_type'], 'required'],
            [['proj_prod_master', 'panel_id', 'sort', 'quantity', 'finalized_by', 'design_completed_by', 'material_completed_by', 'fab_completed_by', 'fab_dispatch_wire_quantity', 'elec_completed_by', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['amount', 'fab_assign_percent', 'fab_complete_percent', 'elec_assign_percent', 'elec_complete_percent'], 'number'],
            [['finalized_at', 'design_completed_at', 'material_completed_at', 'fab_completed_at', 'elec_completed_at', 'created_at', 'updated_at'], 'safe'],
            [['project_production_panel_code', 'panel_description', 'filename'], 'string', 'max' => 255],
            [['panel_type', 'unit_code', 'item_dispatch_status', 'fab_work_status', 'elec_work_status'], 'string', 'max' => 10],
            [['proj_prod_master'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionMaster::class, 'targetAttribute' => ['proj_prod_master' => 'id']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::class, 'targetAttribute' => ['panel_type' => 'code']],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQPanels::class, 'targetAttribute' => ['panel_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQPanelUnit::class, 'targetAttribute' => ['unit_code' => 'code']],
            [['design_completed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['design_completed_by' => 'id']],
            [['material_completed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['material_completed_by' => 'id']],
            [['fab_completed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['fab_completed_by' => 'id']],
            [['elec_completed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['elec_completed_by' => 'id']],
            [['fab_work_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdPanelWorkStatus::class, 'targetAttribute' => ['fab_work_status' => 'code']],
            [['elec_work_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdPanelWorkStatus::class, 'targetAttribute' => ['elec_work_status' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_production_panel_code' => 'Panel Code',
            'proj_prod_master' => 'Proj Prod Master',
            'panel_id' => 'Panel ID',
            'panel_description' => "Panel's Name",
            'remark' => 'Remark',
            'amount' => 'Amount',
            'sort' => 'Sort',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'finalized_at' => 'Finalized At',
            'finalized_by' => 'Finalized By',
            'item_dispatch_status' => 'Item Dispatch Status',
            'design_completed_at' => 'Design Completed At',
            'design_completed_by' => 'Design Completed By',
            'material_completed_at' => 'Material Completed At',
            'material_completed_by' => 'Material Completed By',
            'fab_assign_percent' => 'Fabrication Assigned Percent',
            'fab_complete_percent' => 'Fabrication Complete Percent',
            'fab_completed_at' => 'Fabrication Completed At',
            'fab_completed_by' => 'Fabrication Completed By',
            'fab_work_status' => 'Fab Work Status',
            'fab_dispatch_wire_quantity' => 'Fab Dispatch Wire Quantity',
            'elec_assign_percent' => 'Electrical Assign Percent',
            'elec_complete_percent' => 'Electrical Complete Percent',
            'elec_completed_at' => 'Electrical Completed At',
            'elec_completed_by' => 'Electrical Completed By',
            'elec_work_status' => 'Elec Work Status',
            'filename' => 'Filename',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getBomMasters() {
        return $this->hasMany(BomMaster::className(), ['production_panel_id' => 'id']);
    }

    /**
     * Gets query for [[ProdElecTaskWeights]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdElecTaskWeights() {
        return $this->hasMany(ProdElecTaskWeight::className(), ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[ProdFabTaskWeights]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdFabTaskWeights() {
        return $this->hasMany(ProdFabTaskWeight::className(), ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[DesignCompletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDesignCompletedBy() {
        return $this->hasOne(User::class, ['id' => 'design_completed_by']);
    }

    /**
     * Gets query for [[ElecCompletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecCompletedBy() {
        return $this->hasOne(User::class, ['id' => 'elec_completed_by']);
    }

    /**
     * Gets query for [[ElecTaskCodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecTaskCodes() {
        return $this->hasMany(RefProjProdTaskElec::class, ['code' => 'elec_task_code'])->viaTable('production_elec_tasks', ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[ElecWorkStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecWorkStatus() {
        return $this->hasOne(RefProjProdPanelWorkStatus::class, ['code' => 'elec_work_status']);
    }

    /**
     * Gets query for [[FabCompletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabCompletedBy() {
        return $this->hasOne(User::class, ['id' => 'fab_completed_by']);
    }

    /**
     * Gets query for [[FabTaskCodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabTaskCodes() {
        return $this->hasMany(RefProjProdTaskFab::class, ['code' => 'fab_task_code'])->viaTable('production_fab_tasks', ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[FabWorkStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabWorkStatus() {
        return $this->hasOne(RefProjProdPanelWorkStatus::class, ['code' => 'fab_work_status']);
    }

    /**
     * Gets query for [[MaterialCompletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialCompletedBy() {
        return $this->hasOne(User::class, ['id' => 'material_completed_by']);
    }

    /**
     * Gets query for [[Panel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanel() {
        return $this->hasOne(ProjectQPanels::class, ['id' => 'panel_id']);
    }

    /**
     * Gets query for [[PanelType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanelType() {
        return $this->hasOne(RefProjectQTypes::class, ['code' => 'panel_type']);
    }

    /**
     * Gets query for [[ProductionElecTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTasks() {
        return $this->hasMany(ProductionElecTasks::class, ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[ProductionFabTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionFabTasks() {
        return $this->hasMany(ProductionFabTasks::class, ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[ProjProdMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdMaster() {
        return $this->hasOne(ProjectProductionMaster::class, ['id' => 'proj_prod_master']);
    }

    /**
     * Gets query for [[TaskAssignElecs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecs() {
        return $this->hasMany(TaskAssignElec::class, ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignFabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabs() {
        return $this->hasMany(TaskAssignFab::class, ['proj_prod_panel_id' => 'id']);
    }

    /**
     * Gets query for [[TestMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMains() {
        return $this->hasMany(TestMain::class, ['panel_id' => 'id']);
    }

    /**
     * Gets query for [[StockOutboundMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundMasters() {
        return $this->hasMany(\frontend\models\bom\StockOutboundMaster::className(), ['production_panel_id' => 'id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectQPanelUnit::class, ['code' => 'unit_code']);
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $isWeb = Yii::$app instanceof \yii\web\Application;

            if ($insert) {
                $this->created_by = $isWeb ? Yii::$app->user->identity->id : null;
                $this->created_at = new \yii\db\Expression('NOW()');
            }
            $this->updated_by = $isWeb ? Yii::$app->user->identity->id : null;
            $this->updated_at = new \yii\db\Expression('NOW()');

            return true;
        }
        return false;
    }

    public function processAndSave() {
        if (!empty($this->finalized_at)) {
            return false;
        }
        if (empty($this->project_production_panel_code)) {
            $this->project_production_panel_code = $this->generatePanelCode();
        }
        if (empty($this->sort)) {
            $this->sort = (ProjectProductionPanels::find()->where(['proj_prod_master' => $this->proj_prod_master])->max('sort')) + 1;
        }
        return $this->save();
    }

    // Save Items
    public function saveItems($post) {
        if (empty($post['itemDescription'])) {
            return true;
        }

        $itemDescriptions = $post['itemDescription'];
        $itemQuantitys = $post['quantity'];
        $itemUnits = $post['unitCode'];
        $itemIds = $post['itemId'];
        foreach ((array) $itemDescriptions as $key => $itemDescription) {
            $itemDescription = trim($itemDescription);
            if (!empty($itemDescription)) {
                if (empty($itemIds[$key])) { // if new
                    $item = new ProjectProductionPanelItems();
                    $item->item_description = $itemDescription;
                    $item->quantity = $itemQuantitys[$key];
                    $item->balance = $itemQuantitys[$key] * $this->quantity;
                    $item->unit_code = $itemUnits[$key];
                    $item->proj_prod_panel_id = $this->id;
                } else { // if old, update
                    $item = ProjectProductionPanelItems::findOne($itemIds[$key]);
                    $item->item_description = $itemDescription;
                    $item->quantity = $itemQuantitys[$key];
                    $item->balance = $itemQuantitys[$key] * $this->quantity;
                    $item->unit_code = $itemUnits[$key];
                    $item->update();
                }
            } else {
                // Delete if has id
                if (!empty($itemIds[$key])) {
                    $item = ProjectProductionPanelItems::findOne($itemIds[$key]);
                    $item->delete();
                }
            }
        }

        return true;
    }

    // *************************** GENERAL FUNCTIONS

    /**
     * Generate Panel Code, auto running
     * @return type
     */
    public function generatePanelCode() {
        $runningNo = VProjectProductionPanels::find()->where(['proj_prod_master' => $this->proj_prod_master])->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        return $this->projProdMaster->project_production_code . "-" . $runningNo;
    }

    /**
     *  Finalize Panels Item and quantity
     */
    public function finalizePanelDetail() {
        $this->finalized_at = new \yii\db\Expression("NOW()");
        $this->finalized_by = Yii::$app->user->id;

        /*
         * // Comment this for second phase
          if (empty($this->projectProductionPanelItems)) {
          $this->item_dispatch_status = RefProjProdBqStatus::STS_Done;
          } else if (empty($this->item_dispatch_status)) {
          $this->item_dispatch_status = RefProjProdBqStatus::STS_Submitted;
          } */
        return $this->update();
    }

    /**
     * Finalize Panels Design
     */
    public function finalizeDesign() {
        $this->design_completed_at = new \yii\db\Expression("NOW()");
        $this->design_completed_by = Yii::$app->user->id;
        return $this->update();
    }

    /**
     * by Khetty, 4/1/2024
     * ****** Save Selected Fab Task ******
     */
    public function finalizeFabTask($data) {
        $fabTask = new ProductionFabTasks();
        $fabTask->proj_prod_panel_id = $data['id'];
        $fabTask->fab_task_code = $data['code'];
        $fabTask->qty_total = $data['quantity'];
        $fabTask->qty_assigned = 0;
        $fabTask->qty_completed = 0;
        $fabTask->save();
        return true;
    }

    /**
     * by Khetty, 4/1/2024
     * ****** Save Selected Elec Task ******
     */
    public function finalizeElecTask($data) {
        $elecTask = new ProductionElecTasks();
        $elecTask->proj_prod_panel_id = $data['id'];
        $elecTask->elec_task_code = $data['code'];
        $elecTask->qty_total = $data['quantity'];
        $elecTask->qty_assigned = 0;
        $elecTask->qty_completed = 0;

        $elecTask->save();
        return true;
    }

    /**
     * by Khetty, 19/1/2024
     * ****** Update Fabrication Department Work Status After Deleting Tasks  ******
     */
    public function updateFabWorkStatusAfterDeleteTask($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);
        $fabTask = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
        if (empty($fabTask)) {
            $panel->fab_work_status = null; //update work status to null if no task found
            $panel->save();
            return true;
        } else {
            $panel->updateFabProgressPercent();
            $panel->checkPanelFabWorkStatus();
            return true;
        }
    }

    /**
     * by Khetty, 19/1/2024
     * ****** Update Electrical Department Work Status After Deleting Tasks ******
     */
    public function updateElecWorkStatusAfterDeleteTask($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);
        $elecTask = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
        if (empty($elecTask)) {
            $panel->elec_work_status = null; //update work status to null if no task found
            $panel->save();
            return true;
        } else {
            $panel->updateElecProgressPercent();
            $panel->checkPanelElecWorkStatus();
            return true;
        }
    }

    /**
     * Check if Fabrication Tasks are created. 
     * Create if not 
     * @return boolean
     */
    public function checkAndGetFabTask() {
        if (!empty($this->finalized_at)) { // if not yet finalized
            if (empty($this->productionFabTasks)) { // if no task, then create
                if ($this->createFabTask()) {
                    return $this->checkPanelFabWorkStatus();
                }
            }
            return true;
        }
    }

    /**
     * PRIVATE FUNCTION
     * To create fabrication tasks from ref_proj_prod_task_fab
     * @return boolean
     */
    private function createFabTask() {
        $toDos = RefProjProdTaskFab::getAllActiveSorted();
        $transaction = Yii::$app->db->beginTransaction();
        $transSuccess = true;
        foreach ((array) $toDos as $key => $toDo) {
            $task = new ProductionFabTasks();
            $task->proj_prod_panel_id = $this->id;
            $task->fab_task_code = $toDo->code;
            $task->qty_total = $this->quantity;
            $task->qty_assigned = 0;
            $task->qty_completed = 0;
            if (!$task->save()) {
                $transSuccess = false;
            }
        }

        if ($transSuccess) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $transSuccess;
    }

    /**
     * A function to generate the Table in IndexWAProjectPanels
     */
    public function getFabTaskProgressStatus() {
        $returnArr = [];
        $taskTodo = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $this->id])->all();

        foreach ((array) $taskTodo as $taskAssign) {
            $tempArr['id'] = $taskAssign->id;
            $tempArr['progress'] = ($taskAssign->qty_completed ?? 0) . ' / ' . ($taskAssign->qty_assigned ?? 0) . ' / ' . ($taskAssign->qty_total ?? 0);
            $tempArr['hasRecord'] = $taskAssign->qty_assigned > 0 ? true : false;
            $tempArr['allAssigned'] = $taskAssign->qty_assigned >= $taskAssign->qty_total ? true : false;
            $returnArr[$taskAssign->fab_task_code] = $tempArr;
        }

        return $returnArr;
    }

    /**
     * by Khetty - 17/1/2024
     * A function to generate the Table in IndexWAProjectPanels
     */
    public function checkTaskAssignFab() {
//        $fabTasks = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $this->id])->all();
        $fabTasks = ProductionFabTasks::find()
                ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = production_fab_tasks.fab_task_code') // include join condition if needed
                ->where(['proj_prod_panel_id' => $this->id])
                ->andWhere(['ref.active_sts' => 1])
                ->all();

        $fabTaskIds = [];

        foreach ($fabTasks as $fabTask) {
            $fabTaskIds[] = $fabTask->id;
        }

        return TaskAssignFab::find()->andWhere(['prod_fab_task_id' => $fabTaskIds])->all();
    }

    /**
     * by Khetty - 17/1/2024
     * A function to generate the Table in IndexWAProjectPanels
     */
    public function checkTaskAssignElec() {
        $elecTasks = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $this->id])->all();
        $elecTaskIds = [];

        foreach ($elecTasks as $elecTask) {
            $elecTaskIds[] = $elecTask->id;
        }

        return TaskAssignElec::find()->andWhere(['prod_elec_task_id' => $elecTaskIds])->all();
    }

    /**
     * Update panel's Fabrication working status
     * @return boolean
     */
    public function checkPanelFabWorkStatus() {
        if ($this->fab_work_status == RefProjProdPanelWorkStatus::STS_5_Cancel) {
            return true;
        } else if ($this->fab_assign_percent == 0) {
            $this->fab_work_status = RefProjProdPanelWorkStatus::STS_1_Pending;
        } else if ($this->fab_complete_percent >= 100) {
            $this->fab_work_status = RefProjProdPanelWorkStatus::STS_4_Complete;
        } else if ($this->fab_assign_percent >= 100) {
            $this->fab_work_status = RefProjProdPanelWorkStatus::STS_3_Fully;
        } else {
            $this->fab_work_status = RefProjProdPanelWorkStatus::STS_2_Partial;
        }
        if (!empty($this->dirtyAttributes)) {
            return $this->update(false);
        } else {
            return true;
        }
    }

    /**
     * Update % of Fabrication Progress of Panels
     * @return type
     */
    //comment on 20/2/2026
//    public function updateFabProgressPercent() {
//        $qtys = $this->getFabQtys();
//        $this->fab_assign_percent = (float) MyFormatter::asDecimal2NoSeparator($qtys->qty_assigned / $qtys->qty_total * 100.00);
//        $this->fab_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($qtys->qty_completed / $qtys->qty_total * 100.00);
//        if (!empty($this->getDirtyAttributes())) {
//            return $this->update();
//        } else {
//            return true;
//        }
//    }
    public function updateFabProgressPercent() {
        $qtys = $this->getFabQtys();

        if ($qtys === null || $qtys->qty_total == 0) {
            $this->fab_assign_percent = 0;
            $this->fab_complete_percent = 0;
        } else {
            $this->fab_assign_percent = (float) MyFormatter::asDecimal2NoSeparator(
                            $qtys->qty_assigned / $qtys->qty_total * 100.00
                    );
            $this->fab_complete_percent = (float) MyFormatter::asDecimal2NoSeparator(
                            $qtys->qty_completed / $qtys->qty_total * 100.00
                    );
        }

        if (!empty($this->getDirtyAttributes())) {
            return $this->update();
        } else {
            return true;
        }
    }

    private function getFabQtys() {
//        return ProductionFabTasks::find()
//                        ->select(['SUM(qty_assigned) as qty_assigned', 'SUM(qty_completed) as qty_completed', 'SUM(qty_total) as qty_total'])
//                        ->where(['proj_prod_panel_id' => $this->id])->groupBy(['proj_prod_panel_id'])->one();
        return ProductionFabTasks::find()
                        ->select(['SUM(qty_assigned) as qty_assigned', 'SUM(qty_completed) as qty_completed', 'SUM(qty_total) as qty_total'])
                        ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = production_fab_tasks.fab_task_code')
                        ->where(['proj_prod_panel_id' => $this->id])
                        ->andWhere(['ref.active_sts' => 1])
                        ->groupBy(['proj_prod_panel_id'])
                        ->one();
    }

    /**
     * Check if Electrical Tasks are created. 
     * Create if not 
     * @return boolean
     */
    public function checkAndGetElecTask() {
        if (!empty($this->finalized_at)) { // if not yet finalized
            if (empty($this->productionElecTasks)) { // if no task, then create
                if ($this->createElecTask()) {
                    return $this->checkPanelElecWorkStatus();
                }
            }
            return true;
        }
    }

    /**
     * PRIVATE FUNCTION
     * To create electrical tasks from ref_proj_prod_task_fab
     * @return boolean
     */
    private function createElecTask() {
        $toDos = RefProjProdTaskElec::getAllActiveSorted();
        $transaction = Yii::$app->db->beginTransaction();
        $transSuccess = true;
        foreach ((array) $toDos as $key => $toDo) {
            $task = new ProductionElecTasks();
            $task->proj_prod_panel_id = $this->id;
            $task->elec_task_code = $toDo->code;
            $task->qty_total = $this->quantity;
            $task->qty_assigned = 0;
            $task->qty_completed = 0;
            if (!$task->save()) {
                $transSuccess = false;
            }
        }

        if ($transSuccess) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $transSuccess;
    }

    /**
     * A function to generate the Table in IndexWAProjectPanels
     */
    public function getElecTaskProgressStatus() {
        $returnArr = [];
        $taskTodo = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $this->id])->all();
        foreach ((array) $taskTodo as $taskAssign) {
            $tempArr['id'] = $taskAssign->id;
            $tempArr['progress'] = ($taskAssign->qty_completed ?? 0) . ' / ' . ($taskAssign->qty_assigned ?? 0) . ' / ' . ($taskAssign->qty_total ?? 0);
            $tempArr['hasRecord'] = $taskAssign->qty_assigned > 0 ? true : false;
            $tempArr['allAssigned'] = $taskAssign->qty_assigned >= $taskAssign->qty_total ? true : false;
            $returnArr[$taskAssign->elec_task_code] = $tempArr;
        }

        return $returnArr;
    }

    /**
     * Update panel's Fabrication working status
     * @return boolean
     */
    public function checkPanelElecWorkStatus() {
        if ($this->elec_work_status == RefProjProdPanelWorkStatus::STS_5_Cancel) {
            return true;
        } else if ($this->elec_assign_percent == 0) {
            $this->elec_work_status = RefProjProdPanelWorkStatus::STS_1_Pending;
        } else if ($this->elec_complete_percent >= 100) {
            $this->elec_work_status = RefProjProdPanelWorkStatus::STS_4_Complete;
        } else if ($this->elec_assign_percent >= 100) {
            $this->elec_work_status = RefProjProdPanelWorkStatus::STS_3_Fully;
        } else {
            $this->elec_work_status = RefProjProdPanelWorkStatus::STS_2_Partial;
        }
        if (!empty($this->dirtyAttributes)) {
            return $this->update(false);
        } else {
            return true;
        }
    }

    /**
     * Update % of Electrical Progress of Panels 
     * @return type
     */
    //comment on 20/2/2026
//    public function updateElecProgressPercent() {
//        $qtys = $this->getElecQtys();
//        $this->elec_assign_percent = (float) MyFormatter::asDecimal2NoSeparator($qtys->qty_assigned / $qtys->qty_total * 100.00);
//        $this->elec_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($qtys->qty_completed / $qtys->qty_total * 100.00);
//        if (!empty($this->getDirtyAttributes())) {
//            return $this->update();
//        } else {
//            return true;
//        }
//    }

    public function updateElecProgressPercent() {
        $qtys = $this->getElecQtys();

        if ($qtys === null || $qtys->qty_total == 0) {
            $this->elec_assign_percent = 0;
            $this->elec_complete_percent = 0;
        } else {
            $this->elec_assign_percent = (float) MyFormatter::asDecimal2NoSeparator(
                            $qtys->qty_assigned / $qtys->qty_total * 100.00
                    );
            $this->elec_complete_percent = (float) MyFormatter::asDecimal2NoSeparator(
                            $qtys->qty_completed / $qtys->qty_total * 100.00
                    );
        }

        if (!empty($this->getDirtyAttributes())) {
            return $this->update();
        } else {
            return true;
        }
    }
    
    private function getElecQtys() {
        return ProductionElecTasks::find()
                        ->select(['SUM(qty_assigned) as qty_assigned', 'SUM(qty_completed) as qty_completed', 'SUM(qty_total) as qty_total'])
                        ->where(['proj_prod_panel_id' => $this->id])->groupBy(['proj_prod_panel_id'])->one();
    }

    //*************************************************************** TO BE RELEASED IN PHASE 2 *********************************

    /**
     *  To check if the item is dispatched
     *  Update status to dispatched or fully dispatched
     */
    public function updateItemDispatchStatus() {
        $items = ProjectProductionPanelItems::find()->where(['proj_prod_panel_id' => $this->id])
                        ->andWhere('balance > 0')->all();
        if (!empty($items)) {
            $this->item_dispatch_status = RefProjProdBqStatus::STS_Dispatched;
        } else {
            $this->item_dispatch_status = RefProjProdBqStatus::STS_FullyDispatched;
        }
        return $this->update();
    }

    /**
     *  To check if the Panel item transfer is completed
     */
    public function checkIfCompleted() {
        $balance = Yii::$app->db->createCommand("SELECT items.id,items.quantity-IFNULL(received.total_received,0) AS total_received "
                        . "FROM project_production_panel_items AS items "
                        . "LEFT JOIN (SELECT SUM(b.quantity) AS 'total_received', b.proj_prod_panel_item_id AS itemId "
                        . "FROM project_production_panel_proc_dispatch_master AS a "
                        . "JOIN project_production_panel_proc_dispatch_items AS b ON a.id = b.proc_dispatch_master_id "
                        . "WHERE a.proj_prod_panel_id = " . $this->id . " AND a.status = 'receive' "
                        . "GROUP BY b.id) AS received "
                        . "ON items.id = received.itemId "
                        . "WHERE items.proj_prod_panel_id = " . $this->id
                        . " HAVING total_received > 0")->queryAll();
        if (empty($balance)) {
            $this->item_dispatch_status = RefProjProdBqStatus::STS_Done;
            $this->update();
        }
    }

//    public function checkTotalFabTaskWeight() {
//        $totalWeight = 0;
//        $fabTasks = ProductionFabTasks::find()->select(['fab_task_code'])->where(['proj_prod_panel_id' => $this->id])->asArray()->all();
//        foreach ($fabTasks as $attribute) {
//            $fabTaskWeights = ProdFabTaskWeight::find()->select([$attribute['fab_task_code']])->where(['proj_prod_panel_id' => $this->id])->asArray()->one();
//            foreach ($fabTaskWeights as $taskCode => $weight) {
//                $totalWeight += $weight;
//            }
//        }
//        if ($totalWeight > 100) {
//            return false;
//        }
//        return true;
//    }
    //exclude inactive task
    public function checkTotalFabTaskWeight() {
        $totalWeight = 0;
        $fabTasks = ProductionFabTasks::find()
                ->select(['production_fab_tasks.fab_task_code'])
                ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = production_fab_tasks.fab_task_code')
                ->where([
                    'production_fab_tasks.proj_prod_panel_id' => $this->id,
                    'ref.active_sts' => 1,
                ])
                ->asArray()
                ->all();

        foreach ($fabTasks as $attribute) {
            $fabTaskWeights = ProdFabTaskWeight::find()->select([$attribute['fab_task_code']])->where(['proj_prod_panel_id' => $this->id])->asArray()->one();
            foreach ($fabTaskWeights as $taskCode => $weight) {
                $totalWeight += $weight;
            }
        }
        if ($totalWeight > 100) {
            return false;
        }
        return true;
    }

    public function checkTotalElecTaskWeight() {
        $totalWeight = 0;
        $elecTasks = ProductionElecTasks::find()->select(['elec_task_code'])->where(['proj_prod_panel_id' => $this->id])->asArray()->all();
        foreach ($elecTasks as $attribute) {
            $elecTaskWeights = ProdElecTaskWeight::find()->select([$attribute['elec_task_code']])->where(['proj_prod_panel_id' => $this->id])->asArray()->one();
            foreach ($elecTaskWeights as $taskCode => $weight) {
                $totalWeight += $weight;
            }
        }
        if ($totalWeight > 100) {
            return false;
        }
        return true;
    }
}
