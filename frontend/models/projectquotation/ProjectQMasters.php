<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\projectquotation\ProjectQClients;
use frontend\models\projectquotation\ProjectQTypes;
use common\models\User;
use frontend\models\common\RefCompanyGroupList;
use yii\helpers\ArrayHelper;

//use DateTime;
/**
 * This is the model class for table "project_q_masters".
 *
 * @property int $id
 * @property string|null $quotation_no
 * @property string|null $quotation_display_no
 * @property string|null $project_code Auto-generated
 * @property string|null $project_name
 * @property string|null $company_group_code
 * @property float|null $amount
 * @property int|null $project_coordinator
 * @property string|null $status
 * @property string|null $remark
 * @property int $active
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefCompanyGroupList $companyGroupCode
 * @property User $projectCoordinator
 * @property ProjectProductionMaster[] $projectProductionMasters
 * @property ProjectQClients[] $projectQClients
 * @property ProjectQTypes[] $projectQTypes
 * @property RefProjectQTypes[] $types
 */
class ProjectQMasters extends \yii\db\ActiveRecord {

    CONST runningNoLength = 5;

    public $projCoordinatorFullname;
    public $clients;
    public $similarProjectsInfo;
    public $total_amount;
    public $currency_sign;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_masters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['amount'], 'number'],
            [['project_coordinator', 'active', 'created_by', 'updated_by'], 'integer'],
            [['remark', 'projCoordinatorFullname', 'clients'], 'string'],
            [['projCoordinatorFullname', 'quotation_no', 'company_group_code', 'project_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['quotation_no', 'quotation_display_no', 'project_code'], 'string', 'max' => 255],
            [['project_name'], 'string', 'max' => 500],
            [['company_group_code', 'status'], 'string', 'max' => 10],
            [['quotation_no', 'quotation_display_no'], 'unique', 'targetAttribute' => ['quotation_no', 'quotation_display_no']],
            [['quotation_display_no'], 'unique'],
            [['company_group_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefCompanyGroupList::class, 'targetAttribute' => ['company_group_code' => 'code']],
            [['project_coordinator'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['project_coordinator' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_no' => 'Quotation No.',
            'quotation_display_no' => 'Quotation No',
            'project_code' => 'Project Code',
            'project_name' => 'Project Title',
            'company_group_code' => 'Company',
            'amount' => 'Amount',
            'project_coordinator' => 'Project Coordinator',
            'status' => 'Status',
            'remark' => 'Internal Remark',
            'active' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'clients' => 'Client'
        ];
    }

    /**
     * Gets query for [[ProjectProductionMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMasters() {
        return $this->hasMany(ProjectProductionMaster::className(), ['quotation_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectQClients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQClients() {
        return $this->hasMany(ProjectQClients::className(), ['project_q_master_id' => 'id']);
    }

    /**
     * Gets query for [[CompanyGroupCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyGroupCode() {
        return $this->hasOne(RefCompanyGroupList::className(), ['code' => 'company_group_code']);
    }

    /**
     * Gets query for [[ProjectCoordinator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCoordinator() {
        return $this->hasOne(User::className(), ['id' => 'project_coordinator']);
    }

    /**
     * Gets query for [[ProjectQTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQTypes() {
        return $this->hasMany(ProjectQTypes::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Types]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypes() {
        return $this->hasMany(RefProjectQTypes::className(), ['code' => 'type'])->viaTable('project_q_types', ['project_id' => 'id']);
    }

    /**
     * Gets query for [[WorkAssignmentMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkAssignmentMasters() {
        return $this->hasMany(WorkAssignmentMaster::className(), ['project_q_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function setToInactive() {
        $this->active = 0;
        $this->quotation_no .= "-" . date('d-m-Y.h:i:s');
        return $this->update(false);
    }

    public function proccessAndSave() {
        if (!$this->id) {
            $this->quotation_no = self::getNextQuotationNumber() . "";
        } else {
            $tempProj = ProjectQMasters::findOne($this->id);
            $this->quotation_no = $tempProj->quotation_no;
            $this->project_coordinator = $tempProj->project_coordinator;
        }

        $this->quotation_display_no = $this->generateQuotationDisplayNo();

        if (!$this->validate()) {
            \common\models\myTools\Mydebug::dumpFileA($this->errors);
        }
        return $this->save();
    }

    public static function getNextQuotationNumber() {
        $runningNo = ProjectQMasters::find()->where("quotation_no REGEXP '^[0-9]+$'")
                        ->andWhere("YEAR(created_at) = " . date("Y"))
                        ->max('quotation_no') + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }
        return $runningNo;
    }

    public static function getConfirmedQuotationDropDownList() {
        $projectList = ProjectQMasters::find()
                ->select(['project_q_masters.id', 'concat(quotation_no,"-",project_name) as project_name'])
                ->innerJoin("project_q_types", "project_q_types.project_id=project_q_masters.id AND project_q_types.is_finalized=1")
                ->orderBy(['project_name' => SORT_ASC])
                ->all();
        return ArrayHelper::map($projectList, "id", "project_name");
    }

    public static function getConfirmedQuotationAutocompleteList($term = "") {
        return ProjectQMasters::find()->select(['project_name as value', 'project_q_masters.id as id', 'concat(quotation_no,"-",project_name) as label'])
                        ->innerJoin("project_q_types", "project_q_types.project_id=project_q_masters.id AND project_q_types.is_finalized=1")
                        ->where("concat(quotation_no,'-',project_name) LIKE '%" . addslashes($term) . "%'")
                        ->orderBy(['project_name' => SORT_ASC])
                        ->asArray()
                        ->all();
    }

    private function generateQuotationDisplayNo() {
        return $this->quotation_no . "/" . $this->company_group_code . "/" . (new \DateTime($this->created_at))->format('y');
    }

    /**
     * check if all types exists. If not, then create new
     */
    public function checkAndCreateQTypes() {
        $currentProjectQTypes = ArrayHelper::map($this->projectQTypes, 'type', 'type');
        $projectQTypes = ArrayHelper::map(\frontend\models\common\RefProjectQTypes::find()->all(), 'code', 'code');

        foreach ($projectQTypes as $type) {

            if (empty($currentProjectQTypes[$type])) {
                $projectQType = new ProjectQTypes();
                $projectQType->project_id = $this->id;
                $projectQType->type = $type;
                $projectQType->save();
            }
        }


        return true;
    }

    /**
     * by Khetty 24/11/2023
     * uses Levenshtein distance to find similar project names by comparing input strings against existing records.
     * Adjust the threshold for sensitivity in identifying similarity.
     */
//    public function findSimilarProjects($project_name, $threshold = 80) {
//        $similarProjects = [];
//
//        $existingProjects = ProjectQMasters::find()
//                ->andWhere(['active' => 1])
//                ->all();
//
//        foreach ($existingProjects as $existingProject) {
//            $projectName = str_replace(' ', '', strtolower($project_name));
//            $existingProjectName = str_replace(' ', '', strtolower($existingProject->project_name));
//
//            $distance = levenshtein($projectName, $existingProjectName);
//
//            $maxlength = max(strlen($projectName), strlen($existingProjectName));
//
//            $similarity = round((1 - ($distance / $maxlength)) * 100, 2);
//
//            if ($similarity >= $threshold) {
//                $similarProjects[] = [
//                    'projectName' => $existingProject,
//                    'similarityPercentage' => $similarity,
//                ];
//            }
//        }
//
//        usort($similarProjects, function ($a, $b) {
//            return $b['similarityPercentage'] <=> $a['similarityPercentage'];
//        });
//
//        if (!empty($similarProjects)) {
//            return $similarProjects;
//        }
//    }
    public function findSimilarProjects($project_name, $threshold = 80) {
        $similarProjects = [];
        $existingProjects = ProjectQMasters::find()
                ->andWhere(['active' => 1])
                ->all();

        foreach ($existingProjects as $existingProject) {
            $projectName = str_replace(' ', '', strtolower($project_name));
            $existingProjectName = str_replace(' ', '', strtolower($existingProject->project_name));

            // Check string lengths before using levenshtein
            if (strlen($projectName) > 255 || strlen($existingProjectName) > 255) {
                // Use alternative similarity algorithm for long strings
                $similarity = $this->calculateSimilarityPercentage($projectName, $existingProjectName);
            } else {
                $distance = levenshtein($projectName, $existingProjectName);
                $maxlength = max(strlen($projectName), strlen($existingProjectName));
                $similarity = round((1 - ($distance / $maxlength)) * 100, 2);
            }

            if ($similarity >= $threshold) {
                $similarProjects[] = [
                    'projectName' => $existingProject,
                    'similarityPercentage' => $similarity,
                ];
            }
        }

        usort($similarProjects, function ($a, $b) {
            return $b['similarityPercentage'] <=> $a['similarityPercentage'];
        });

        if (!empty($similarProjects)) {
            return $similarProjects;
        }

        return []; // Always return an array
    }

    /**
     * Alternative similarity calculation for strings longer than 255 characters
     * Uses similar_text function which doesn't have length limitationsprojec
     */
    private function calculateSimilarityPercentage($str1, $str2) {
        $percent = 0;
        similar_text($str1, $str2, $percent);
        return round($percent, 2);
    }
}
