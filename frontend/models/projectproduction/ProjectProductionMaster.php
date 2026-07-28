<?php

namespace frontend\models\ProjectProduction;

use Yii;
use frontend\models\projectquotation\ProjectQRevisions;
use frontend\models\projectquotation\ProjectQMasters;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\client\Clients;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectquotation\ProjectQPanels;
use common\models\myTools\FlashHandler;

/**
 * This is the model class for table "project_production_master".
 *
 * @property int $id
 * @property string|null $project_production_code
 * @property string|null $name
 * @property string|null $remark
 * @property int|null $quotation_id
 * @property int|null $revision_id
 * @property int|null $client_id
 * @property float|null $fab_complete_percent
 * @property float|null $elec_complete_percent
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $internal_project 0 = no, 1 = yes
 * @property string|null $current_target_date
 *
 * @property ProjProdTargetDateTrial[] $projProdTargetDateTrials
 * @property ProjectProductionDocuments[] $projectProductionDocuments
 * @property ProjectProductionDocuments[] $projectProductionDocuments0
 * @property ProjectQMasters $quotation
 * @property User $createdBy
 * @property Clients $client
 * @property ProjectQRevisions $revision
 * @property ProjectProductionPanels[] $projectProductionPanels
 * @property ProjectQTypes[] $projectQTypes
 */
class ProjectProductionMaster extends \yii\db\ActiveRecord {

    public $quotationNo;
    public $quotationName;
    public $clientName;
    public $projectType;
    public $amount;
    public $scannedFile;
    public $component_percentage;
    public $production_fab_complete_percent;
    public $production_elec_complete_percent;

    CONST Prefix = "P";
    CONST runningNoLength = 5;
    CONST Prefix_Internalproject = "I";
    CONST TKGROUP = ['client_code' => ['T056', 'T064', 'T071']];
    //CONST TKGROUP = ['client_code' => ['T06', 'T031', 'T032']];

    public $remark_update_target_date;
    public $new_target_date;
    public $has_fab_tasks;
    public $has_elec_tasks;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['remark'], 'string'],
//            [['remark_update_target_date', 'new_target_date'], 'required'],
            [['quotation_id', 'revision_id', 'client_id', 'created_by', 'updated_by', 'internal_project'], 'integer'],
            [['fab_complete_percent', 'elec_complete_percent', 'component_percentage'], 'number'],
            [['remark_update_target_date', 'new_target_date', 'created_at', 'updated_at', 'quotationNo', 'clientName'], 'safe'],
            [['project_production_code', 'name', 'remark_update_target_date'], 'string', 'max' => 255],
            [['project_production_code'], 'unique'],
            ['scannedFile', 'file', 'maxFiles' => 0, 'skipOnEmpty' => true],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQMasters::className(), 'targetAttribute' => ['quotation_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['revision_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQRevisions::className(), 'targetAttribute' => ['revision_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_production_code' => 'Project Production Code',
            'name' => 'Project Name',
            'project_production_code' => 'Project Code',
            'remark' => 'Remark',
            'quotationNo' => 'Quotation No.',
            'quotation_id' => 'Quotation',
            'revision_id' => 'Revision',
            'client_id' => 'Client',
            'fab_complete_percent' => 'Fabrication Complete %',
            'elec_complete_percent' => 'Electrical Complete %',
            'clientName' => 'Client Name',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'component_percentage' => 'Component %',
            'internal_project' => 'Internal Project',
            'current_target_date' => 'Target Completion Date',
        ];
    }

    /**
     * Gets query for [[Quotation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation() {
        return $this->hasOne(ProjectQMasters::className(), ['id' => 'quotation_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Revision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevision() {
        return $this->hasOne(ProjectQRevisions::className(), ['id' => 'revision_id']);
    }

    /**
     * Gets query for [[ProjectProductionDocuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionDocuments() {
        return $this->hasMany(ProjectProductionDocuments::class, ['project_production_master_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels() {
        return $this->hasMany(ProjectProductionPanels::className(), ['proj_prod_master' => 'id']);
    }

    /**
     * Gets query for [[ProjectQTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQTypes() {
        return $this->hasMany(ProjectQTypes::className(), ['proj_prod_id' => 'id']);
    }

    public function beforeDelete() {
        if (parent::beforeDelete()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                foreach ($this->projectProductionPanels as $panel) {
                    if (!$panel->delete()) {
                        $transaction->rollBack();
                        return false;
                    }
                }

                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollBack();
                return false;
            }
        }
        return false;
    }

//    public function beforeSave($insert) {
//        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
//        } else {
//            $this->created_at = new \yii\db\Expression('NOW()');
//            $this->created_by = Yii::$app->user->identity->id;
//        }
//        return parent::beforeSave($insert);
//    }

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
        $transaction = Yii::$app->db->beginTransaction();

        if (in_array($this->client->client_code, self::TKGROUP['client_code'])) {
            $this->internal_project = 1;
        }

        // Save record into project 
        if (!$this->generateProjectProductionCode() || !$this->save()) {
            $transaction->rollBack();
            return false;
        }
        // create project panels from quotaiton
//        if (!$this->copyPanelsFromQuotation()) {
//            $transaction->rollBack();
//            return false;
//        }

        $transaction->commit();

        return true;
    }

    /**
     * by Khetty 13/11/2023
     * Retrieves and populates project details from ProjectQType.
     * @param $projType The ProjectQType object from which to retrieve project details.
     *
     * @return boolean
     */
    public function checkAndPopulateProject($projType) {
        $projQMaster = $projType->project;
        $projQRevision = $projType->activeRevision;
        $projQClient = $projType->activeClient;

        if (!empty($projQRevision) && !empty($projQClient)) {
            $this->revision_id = $projQRevision->id;
            $this->quotation_id = $projQMaster->id;
            $this->quotationNo = $projQMaster->quotation_display_no;
            $this->quotationName = $projQMaster->project_name;
            $this->projectType = $projType->type0->project_type_name;
            $this->amount = ($projQRevision->currency->currency_sign ?? "") . " " . MyFormatter::asDecimal2($projQRevision->amount);
            $this->client_id = $projQClient->client_id;
            $this->clientName = $projQClient->client->company_name;
            $this->name = $projQMaster->project_name;
            $this->update();
            return true;
        } else {
            FlashHandler::err("Error: Unable to populate project details.");
            return false;
        }
    }

    /**
     * by Khetty 13/11/2023
     * It retrieves details of each panel from ProjectQPanels and saves them to the database.
     *
     * @param an array of panel IDs
     */
//    public function createProductionPanels($panelIds) {
//        foreach ($panelIds as $panelId) {
//            $panel = ProjectQPanels::findOne($panelId);
//            if ($panel) {
//                $projProdPanel = new ProjectProductionPanels();
//                $projProdPanel->proj_prod_master = $this->id;
//                $projProdPanel->panel_id = $panel->id;
//                $projProdPanel->project_production_panel_code = $projProdPanel->generatePanelCode();
//                $projProdPanel->panel_description = $panel->panel_description;
//                $projProdPanel->panel_type = $panel->panel_type;
//                $projProdPanel->remark = $panel->remark;
////                $projProdPanel->amount = $panel->amount;
//                $projProdPanel->amount = $this->calculateConvertedPanelAmount($panel->amount);
//                $projProdPanel->quantity = $panel->quantity;
//                $projProdPanel->unit_code = $panel->unit_code;
//
//                // Get the latest sort value
//                $latestSortNum = ProjectProductionPanels::find()
//                        ->select('MAX(sort)')
//                        ->scalar();
//
//                // Increment the latestSortNum by 1
//                $sortNum = $latestSortNum + 1;
//                $projProdPanel->sort = $sortNum;
//
//                $projProdPanel->save();
//            }
//        }
//    }
    //for PLC
    public function createProductionPanels($panelIds, $activeRevision) {
        foreach ($panelIds as $panelId) {
            $panel = ProjectQPanels::findOne($panelId);
            if ($panel) {
                $panel->enquiry_date = $activeRevision->projectQType->project->enquiry_date;
                $panel->enquiry_by = $activeRevision->projectQType->project->created_by;
                $panel->quotation_by = $activeRevision->quotation_by;
                $panel->quotation_date = $activeRevision->quotation_date;
                $panel->confirmed_by = $activeRevision->confirmed_by;
                $panel->confirmed_date = $activeRevision->confirmed_date;
                $panel->shop_drawings_submitted_by = $activeRevision->shop_drawings_submitted_by;
                $panel->shop_drawings_submission_date = $activeRevision->shop_drawings_submission_date;
                $panel->pushed_by = Yii::$app->user->identity->id;
                $panel->pushed_date = new \yii\db\Expression('NOW()');
                $panel->save(false);

                $projProdPanel = new ProjectProductionPanels();
                $projProdPanel->proj_prod_master = $this->id;
                $projProdPanel->panel_id = $panel->id;
                $projProdPanel->project_production_panel_code = $projProdPanel->generatePanelCode();
                $projProdPanel->panel_description = $panel->panel_description;
                $projProdPanel->panel_type = $panel->panel_type;
                $projProdPanel->remark = $panel->remark;
//                $projProdPanel->amount = $panel->amount;
                $projProdPanel->amount = $this->calculateConvertedPanelAmount($panel->amount);
                $projProdPanel->quantity = $panel->quantity;
                $projProdPanel->unit_code = $panel->unit_code;
                $projProdPanel->enquiry_date = $panel->enquiry_date;
                $projProdPanel->enquiry_by = $panel->enquiry_by;
                $projProdPanel->quotation_by = $panel->quotation_by;
                $projProdPanel->quotation_date = $panel->quotation_date;
                $projProdPanel->confirmed_by = $panel->confirmed_by;
                $projProdPanel->confirmed_date = $panel->confirmed_date;
                $projProdPanel->shop_drawings_submitted_by = $panel->shop_drawings_submitted_by;
                $projProdPanel->shop_drawings_submission_date = $panel->shop_drawings_submission_date;
                $projProdPanel->pushed_by = $panel->pushed_by;
                $projProdPanel->pushed_date = $panel->pushed_date;

                // Get the latest sort value
                $latestSortNum = ProjectProductionPanels::find()
                        ->select('MAX(sort)')
                        ->scalar();

                // Increment the latestSortNum by 1
                $sortNum = $latestSortNum + 1;
                $projProdPanel->sort = $sortNum;

                $projProdPanel->save();
            }
        }
    }

    private function calculateConvertedPanelAmount($amount) {
        $rate = $this->revision->currency->exchange_rate ?? 1;
        return round($amount * $rate, 2);
    }

    /**
     *  ************************** Copy produciton panels from Quotation
     * @return boolean
     */
    public function copyPanelsFromQuotation() {
        $panelList = $this->revision->projectQPanels;
        if (empty($panelList)) {
            return true;
        }
        array_multisort(array_column($panelList, "sort"), SORT_ASC, $panelList);
        // Copy panels from quotation
        foreach ($panelList as $key => $panel) {
            $projProdPanel = new ProjectProductionPanels();
            $projProdPanel->proj_prod_master = $this->id;
            $projProdPanel->project_production_panel_code = $projProdPanel->generatePanelCode();
            $projProdPanel->panel_id = $panel->id;
            $projProdPanel->panel_description = $panel->panel_description;
            $projProdPanel->remark = $panel->remark;
            $projProdPanel->quantity = $panel->quantity;
            $projProdPanel->unit_code = $panel->unit_code;
            $projProdPanel->sort = $key + 1;
            $projProdPanel->save();
        }

        return true;
    }

    // *************************** GENERAL FUNCTIONS
    // comment on 20/2/2026
//    public function updateAvgFabProgressPercent() {
//        $percents = ProjectProductionPanels::find()
//                        ->where(['proj_prod_master' => $this->id])->all();
//        $totalQty = ProjectProductionPanels::find()
//                        ->where(['proj_prod_master' => $this->id])->sum("quantity");
//        $completion = 0;
//        foreach ($percents as $percent) {
//            $temp = $percent->fab_complete_percent * $percent->quantity / $totalQty;
//            $completion += $temp;
//        }
//
//        $this->fab_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($completion);
//        if (!empty($this->getDirtyAttributes())) {
//            return $this->update();
//        } else {
//            return true;
//        }
//    }
//
//    public function updateAvgElecProgressPercent() {
//        $percents = ProjectProductionPanels::find()
//                        ->where(['proj_prod_master' => $this->id])->all();
//        $totalQty = ProjectProductionPanels::find()
//                        ->where(['proj_prod_master' => $this->id])->sum("quantity");
//        $completion = 0;
//        foreach ($percents as $percent) {
//            $temp = $percent->elec_complete_percent * $percent->quantity / $totalQty;
//            $completion += $temp;
//        }
//
//        $this->elec_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($completion);
//        if (!empty($this->getDirtyAttributes())) {
//            return $this->update();
//        } else {
//            return true;
//        }
//    }

    public function updateAvgFabProgressPercent() {
        $panels = ProjectProductionPanels::find()
                ->where(['proj_prod_master' => $this->id])
                ->all();
        $totalQty = 0;
        $completion = 0;

        foreach ($panels as $panel) {
            $hasFabTasks = fabrication\ProductionFabTasks::find()
                    ->where(['proj_prod_panel_id' => $panel->id])
                    ->exists();
            if ($hasFabTasks) {
                $panel->updateFabProgressPercent(); // force recalculate first
                $totalQty += $panel->quantity;
            }
        }

        if ($totalQty == 0) {
            $this->fab_complete_percent = 0;
        } else {
            foreach ($panels as $panel) {
                $hasFabTasks = fabrication\ProductionFabTasks::find()
                        ->where(['proj_prod_panel_id' => $panel->id])
                        ->exists();
                if ($hasFabTasks) {
                    $temp = $panel->fab_complete_percent * $panel->quantity / $totalQty;
                    $completion += $temp;
                }
            }
            $this->fab_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($completion);
        }

        if (!empty($this->getDirtyAttributes())) {
            return $this->update();
        } else {
            return true;
        }
    }

    public function updateAvgElecProgressPercent() {
        $panels = ProjectProductionPanels::find()
                ->where(['proj_prod_master' => $this->id])
                ->all();
        $totalQty = 0;
        $completion = 0;

        foreach ($panels as $panel) {
            $hasElecTasks = electrical\ProductionElecTasks::find()
                    ->where(['proj_prod_panel_id' => $panel->id])
                    ->exists();
            if ($hasElecTasks) {
                $panel->updateElecProgressPercent(); // force recalculate first
                $totalQty += $panel->quantity;
            }
        }

        if ($totalQty == 0) {
            $this->elec_complete_percent = 0;
        } else {
            foreach ($panels as $panel) {
                $hasElecTasks = electrical\ProductionElecTasks::find()
                        ->where(['proj_prod_panel_id' => $panel->id])
                        ->exists();
                if ($hasElecTasks) {
                    $temp = $panel->elec_complete_percent * $panel->quantity / $totalQty;
                    $completion += $temp;
                }
            }
            $this->elec_complete_percent = (float) MyFormatter::asDecimal2NoSeparator($completion);
        }

        if (!empty($this->getDirtyAttributes())) {
            return $this->update();
        } else {
            return true;
        }
    }

    public function generateProjectProductionCode() {
        $runningNo = ProjectProductionMaster::find()
                        ->where("YEAR(created_at) = " . date("Y"))
                        ->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        if (in_array($this->client->client_code, self::TKGROUP['client_code'])) {
            $initialProjectCode = self::Prefix_Internalproject;
        } else {
            $initialProjectCode = self::Prefix;
        }
        return $this->project_production_code = $initialProjectCode . ($this->quotation->quotation_no ?? "") . "-" . $runningNo . "-" . date("my");
    }

    public function uploadAttachments() {
        if ($this->record_date) {
            $this->record_date = MyFormatter::changeDateFormat_readToDB($this->record_date);
        }
        $status = '';
        $projectCode = $this->project->proj_code;
        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_main_claim_folder'] . '/';
            if ($this->submit_approve_file && file_exists($filePath . $this->submit_approve_file . '-approved')) {
                unlink($filePath . $this->submit_approve_file . '-approved');
            }
            $this->submit_approve_file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '-approved' . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->submit_approve_file);
        }

        $this->approval_status = RefGeneralProgressStatus::APPROVE;

        if ($this->id) {
            $status = $this->update(false);
        } else {
            $status = $this->save(false);
        }
        FlashHandler::success("Progress claim saved!");

        return $status;
    }

    public function updateProjectDeliveryStatus() {
        // Count panels by delivery status
        $totalPanels = ProjectProductionPanels::find()->where(['proj_prod_master' => $this->id])->count();
        $nonDeliveredCount = ProjectProductionPanels::find()->where(['proj_prod_master' => $this->id, 'delivery_status' => 1])->count();
        $partialDeliveredCount = ProjectProductionPanels::find()->where(['proj_prod_master' => $this->id, 'delivery_status' => 2])->count();
        $fullyDeliveredCount = ProjectProductionPanels::find()->where(['proj_prod_master' => $this->id, 'delivery_status' => 3])->count();
        $latestDeliveredDate = ProjectProductionPanels::find()->where(['proj_prod_master' => $this->id, 'delivery_status' => 3])->max('delivered_at');

        // Determine project delivery status
        if ($fullyDeliveredCount == $totalPanels && $totalPanels > 0) {
            // All panels are fully delivered
            $this->delivery_status = 3;
            $this->delivered_at = $latestDeliveredDate;
            // Keep the delivered_at date
        } else if ($nonDeliveredCount == $totalPanels) {
            // All panels have no delivery
            $this->delivery_status = 1;
            $this->delivered_at = null;
        } else {
            // Some panels are delivered (partially or fully), but not all are fully delivered
            $this->delivery_status = 2;
            $this->delivered_at = null;
        }

        return $this->save(false);
    }

    /*     * ************************************** TASK ASSIGNMENT ******************************************************* */

    // FOR FUTURE USE - RELATED TO PLC
    public static function getAssignableQty($department, $panelId, $currentTaskCode) {
        // Special handling for electrical parallel tasks
        if ($department === 'elec') {
            return self::getElecAssignableQty($panelId, $currentTaskCode);
        }

        // Original logic for fabrication
        $sequence = self::getActiveTaskSequence($department, $panelId);
        $currentPosition = array_search($currentTaskCode, $sequence);
        if ($currentPosition === false) {
            return 0;
        }

        $assignedQty = self::getAssignedQtyByTask($department, $panelId, $currentTaskCode);
        $panel = self::findOne($panelId);
        $totalQty = $panel->total_qty ?? 0;

        if ($currentPosition === 0) {
            $inputQty = $totalQty;
        } else {
            $previousTask = $sequence[$currentPosition - 1];
            $inputQty = self::getCompletedQtyByTask($department, $panelId, $previousTask);
        }

        return max(0, $inputQty - $assignedQty);
    }

// New method specifically for electrical task assignable quantity
    private static function getElecAssignableQty($panelId, $currentTaskCode) {
        $assignedQty = self::getAssignedQtyByTask('elec', $panelId, $currentTaskCode);

        // Get fabrication completion quantity (either assembly or last completed)
        $fabCompletionQty = self::getFabCompletionForElec($panelId);

        // Check which tasks exist in this panel
        $hasMountTask = self::taskExistsInPanel('elec', $panelId, 'mount');
        $hasBusbarTask = self::taskExistsInPanel('elec', $panelId, 'busbar');
        $hasWireTask = self::taskExistsInPanel('elec', $panelId, 'wire');

        switch ($currentTaskCode) {
            case 'busbar':
                // Busbar depends on fabrication completion
                return max(0, $fabCompletionQty - $assignedQty);

            case 'mount':
                // Mount depends on fabrication completion
                return max(0, $fabCompletionQty - $assignedQty);

            case 'wire':
                // Wire depends on mount if mount exists, otherwise depends on fabrication
                if ($hasMountTask) {
                    $mountCompleted = self::getCompletedQtyByTask('elec', $panelId, 'mount');
                    return max(0, $mountCompleted - $assignedQty);
                } else {
                    // No mount task, wire depends on fabrication completion
                    return max(0, $fabCompletionQty - $assignedQty);
                }

            case 'test':
                // Test depends on wire completion
                if ($hasWireTask) {
                    $wireCompleted = self::getCompletedQtyByTask('elec', $panelId, 'wire');
                    return max(0, $wireCompleted - $assignedQty);
                } else {
                    // No wire task (unlikely), but fallback to mount or fabrication
                    if ($hasMountTask) {
                        $mountCompleted = self::getCompletedQtyByTask('elec', $panelId, 'mount');
                        return max(0, $mountCompleted - $assignedQty);
                    } else {
                        return max(0, $fabCompletionQty - $assignedQty);
                    }
                }

            default:
                return 0;
        }
    }

// Helper to get fabrication completion quantity for electrical tasks
    public static function getFabCompletionForElec($panelId) {
        // Check if assemble task exists
        $hasAssembleTask = self::taskExistsInPanel('fab', $panelId, 'assemble');

        if ($hasAssembleTask) {
            // Use assembly completion
            return self::getCompletedQtyByTask('fab', $panelId, 'assemble');
        } else {
            // Use last completed fabrication task
            return self::getLastCompletedTaskQty('fab', $panelId);
        }
    }

// Helper method to check if a task exists
    public static function taskExistsInPanel($department, $panelId, $taskCode) {
        $taskTable = 'production_' . $department . '_tasks';
        $taskCodeField = $department . '_task_code';

        return (new \yii\db\Query())
                        ->from($taskTable)
                        ->where([
                            'proj_prod_panel_id' => $panelId,
                            $taskCodeField => $taskCode,
                        ])
                        ->exists();
    }

    public static function getActiveTaskSequence($department, $panelId) {
        $master = [
            'fab' => ['cutnpunch', 'bend', 'weld', 'grind', 'powcoat', 'assemble'],
            'elec' => [
                'stage1' => ['busbar', 'mount'], // Parallel tasks
                'stage2' => ['wire'],
                'stage3' => ['test'],
            ],
        ];

        if ($department === 'fab') {
            $sequence = $master['fab'];
            $dbTasks = fabrication\ProductionFabTasks::find()
                    ->select('fab_task_code')
                    ->where(['proj_prod_panel_id' => $panelId])
                    ->column();
            return array_values(array_intersect($sequence, $dbTasks));
        } else {
            // For electrical, we need a flat sequence but handle dependencies differently
            $allElecTasks = electrical\ProductionElecTasks::find()
                    ->select('elec_task_code')
                    ->where(['proj_prod_panel_id' => $panelId])
                    ->column();

            // Return in logical order but busbar/mount are considered stage1
            $orderedTasks = [];
            if (in_array('busbar', $allElecTasks))
                $orderedTasks[] = 'busbar';
            if (in_array('mount', $allElecTasks))
                $orderedTasks[] = 'mount';
            if (in_array('wire', $allElecTasks))
                $orderedTasks[] = 'wire';
            if (in_array('test', $allElecTasks))
                $orderedTasks[] = 'test';

            return $orderedTasks;
        }
    }

    public static function getLastCompletedTaskQty($department, $panelId) {
        $taskSequences = [
            'fab' => [
                'assemble',
                'powcoat',
                'grind',
                'weld',
                'bend',
                'cutnpunch',
            ],
            'elec' => [
                'test',
                'wire',
                'mount',
                'busbar',
            ],
        ];

        $sequence = $taskSequences[$department] ?? [];

        // Look through tasks from last to first
        foreach ($sequence as $taskCode) {
            $completedQty = self::getCompletedQtyByTask($department, $panelId, $taskCode);
            if ($completedQty > 0) {
                return $completedQty;
            }
        }

        return 0;
    }

    public static function getCompletedQtyByTask($department, $panelId, $taskCode) {
        $taskTable = 'production_' . $department . '_tasks';
        $taskCodeField = $department . '_task_code';

        $completedQty = (new \yii\db\Query())
                ->select(['SUM(qty_completed)'])
                ->from($taskTable)
                ->where([
                    'proj_prod_panel_id' => $panelId,
                    $taskCodeField => $taskCode,
                ])
                ->scalar();

        return (int) $completedQty;
    }

    public static function getAssignedQtyByTask($department, $panelId, $taskCode) {
        $taskTable = 'production_' . $department . '_tasks';
        $taskCodeField = $department . '_task_code';

        $assignedQty = (new \yii\db\Query())
                ->select(['SUM(qty_assigned)'])
                ->from($taskTable)
                ->where([
                    'proj_prod_panel_id' => $panelId,
                    $taskCodeField => $taskCode,
                ])
                ->scalar();

        return (int) $assignedQty;
    }

    /*     * ************************************** TASK COMPLETION ******************************************************* */

    public static function getCompletableQty($department, $panelId, $currentTaskCode) {
        if ($department === 'elec') {
            return self::getElecCompletableQty($panelId, $currentTaskCode);
        }

        // For fabrication
        $sequence = self::getActiveTaskSequence($department, $panelId);
        $currentPosition = array_search($currentTaskCode, $sequence);
        if ($currentPosition === false) {
            return 0;
        }

        $completedQty = self::getCompletedQtyByTask($department, $panelId, $currentTaskCode);
        $panel = ProjectProductionPanels::findOne($panelId);
        $totalQty = $panel->total_qty ?? 0;

        // FIRST TASK
        if ($currentPosition === 0) {
            $inputQty = $totalQty;
        } else {
            $previousTask = $sequence[$currentPosition - 1];
            $inputQty = self::getCompletedQtyByTask($department, $panelId, $previousTask);
        }

        // For completion: available = what's available from input - what's already completed
        return max(0, $inputQty - $completedQty);
    }

// For electrical with parallel tasks
    private static function getElecCompletableQty($panelId, $currentTaskCode) {
        $completedQty = self::getCompletedQtyByTask('elec', $panelId, $currentTaskCode);
        $fabCompletionQty = self::getFabCompletionForElec($panelId);

        $hasMountTask = self::taskExistsInPanel('elec', $panelId, 'mount');
        $hasWireTask = self::taskExistsInPanel('elec', $panelId, 'wire');

        switch ($currentTaskCode) {
            case 'busbar':
                // Can complete up to what fabrication has completed
                return max(0, $fabCompletionQty - $completedQty);

            case 'mount':
                // Can complete up to what fabrication has completed
                return max(0, $fabCompletionQty - $completedQty);

            case 'wire':
                if ($hasMountTask) {
                    $mountCompleted = self::getCompletedQtyByTask('elec', $panelId, 'mount');
                    return max(0, $mountCompleted - $completedQty);
                } else {
                    return max(0, $fabCompletionQty - $completedQty);
                }

            case 'test':
                if ($hasWireTask) {
                    $wireCompleted = self::getCompletedQtyByTask('elec', $panelId, 'wire');
                    return max(0, $wireCompleted - $completedQty);
                } else if ($hasMountTask) {
                    $mountCompleted = self::getCompletedQtyByTask('elec', $panelId, 'mount');
                    return max(0, $mountCompleted - $completedQty);
                } else {
                    return max(0, $fabCompletionQty - $completedQty);
                }

            default:
                return 0;
        }
    }

    public static function getPreviousTaskDetails($department, $panelId, $currentTaskCode) {
        $sequence = self::getActiveTaskSequence($department, $panelId);
        $currentPosition = array_search($currentTaskCode, $sequence);

        if ($currentPosition === false || $currentPosition === 0) {
            return null;
        }

        $previousTaskCode = $sequence[$currentPosition - 1];

        // Get previous task completion details
        $completedQty = self::getCompletedQtyByTask($department, $panelId, $previousTaskCode);
        $assignedQty = self::getAssignedQtyByTask($department, $panelId, $previousTaskCode);
        $totalQty = ProjectProductionPanels::findOne($panelId)->total_qty ?? 0;

        // Get staff assigned to previous task
        $staffAssigned = self::getStaffAssignedToTask($department, $panelId, $previousTaskCode);

        // Get task name/label
        $taskLabels = [
            'fab' => [
                'cutnpunch' => 'Cutting & Punching',
                'bend' => 'Bending',
                'weld' => 'Welding',
                'grind' => 'Grinding',
                'powcoat' => 'Powder Coating',
                'assemble' => 'Assembling',
            ],
            'elec' => [
                'busbar' => 'Busbar Work',
                'mount' => 'Components Mounting',
                'wire' => 'Wiring',
                'test' => 'Testing',
            ],
        ];

        $taskName = $taskLabels[$department][$previousTaskCode] ?? ucfirst($previousTaskCode);

        return [
            'taskCode' => $previousTaskCode,
            'taskName' => $taskName,
            'completedQty' => $completedQty,
            'assignedQty' => $assignedQty,
            'totalQty' => $totalQty,
            'remainingToComplete' => $assignedQty - $completedQty,
            'staffAssigned' => $staffAssigned,
            'message' => "Previous task '{$taskName}' has no completed units yet. " .
            "Completed: {$completedQty} / Assigned: {$assignedQty}"
        ];
    }

// New method to get staff assigned to a specific task
    public static function getStaffAssignedToTask($department, $panelId, $taskCode) {
        $staffList = [];

        try {
            if ($department === 'fab') {
                $taskAssign = fabrication\ProductionFabTasks::find()
                        ->where([
                            'proj_prod_panel_id' => $panelId,
                            'fab_task_code' => $taskCode
                        ])
                        ->one();

                if (!$taskAssign) {
                    return [];
                }

                $assignments = \frontend\models\projectproduction\fabrication\TaskAssignFab::find()
                        ->with(['taskAssignFabStaff', 'taskAssignFabStaff.user'])
                        ->where(['prod_fab_task_id' => $taskAssign->id])
                        ->andWhere(['deactivated_at' => null])
                        ->andWhere(['deactivated_by' => null])
                        ->all();

                // Fix for fabrication section in getStaffAssignedToTask()
                foreach ($assignments as $assignment) {
                    $taskAssignFabStaffs = $assignment->taskAssignFabStaff;
                    foreach ($taskAssignFabStaffs as $taskFabStaff) {
                        $staff = $taskFabStaff->user;
                        if ($staff) {
                            $completedQty = $taskFabStaff->complete_qty ?? 0;
                            $assignedQty = $assignment->quantity ?? 0;
                            $remainingQty = $assignedQty - $completedQty;

                            $staffList[] = [
                                'id' => $staff->id,
                                'name' => $staff->fullname,
                                'assigned_qty' => $assignedQty,
                                'completed_qty' => $completedQty,
                                'remaining_qty' => $remainingQty,
                                'is_completed' => $remainingQty <= 0,
                            ];
                        }
                    }
                }
            } else { // electrical
                $taskAssign = electrical\ProductionElecTasks::find()
                        ->where([
                            'proj_prod_panel_id' => $panelId,
                            'elec_task_code' => $taskCode
                        ])
                        ->one();

                if (!$taskAssign) {
                    return [];
                }

                $assignments = \common\models\TaskAssignElec::find()
                        ->with(['taskAssignElecStaff', 'taskAssignElecStaff.user'])
                        ->where(['prod_elec_tasks_id' => $taskAssign->id])
                        ->all();

                foreach ($assignments as $assignment) {
                    if ($assignment->taskAssignElecStaff && $assignment->taskAssignElecStaff->user) {
                        $staff = $assignment->taskAssignElecStaff->user;
                        $completedQty = $assignment->taskAssignElecStaff->completed_qty ?? 0;
                        $assignedQty = $assignment->quantity ?? 0;
                        $remainingQty = $assignedQty - $completedQty;

                        $staffList[] = [
                            'id' => $staff->id,
                            'name' => $staff->fullname,
                            'assigned_qty' => $assignedQty,
                            'completed_qty' => $completedQty,
                            'remaining_qty' => $remainingQty,
                            'is_completed' => $remainingQty <= 0,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return $staffList;
    }
}
