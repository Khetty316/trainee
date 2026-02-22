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
    CONST TKGROUP = ['client_code' => ['T030', 'T031', 'T032']];

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
    public function createProductionPanels($panelIds) {
        foreach ($panelIds as $panelId) {
            $panel = ProjectQPanels::findOne($panelId);
            if ($panel) {
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
}
