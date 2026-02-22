<?php

namespace frontend\models\working\project;

use frontend\models\common\RefArea;
use frontend\models\working\contact\ContactMaster;
use common\models\User;
use Yii;
use frontend\models\working\project\ProspectDetailRevision;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "project_master".
 *
 * @property int $id
 * @property string $proj_code
 * @property string|null $title_short
 * @property string|null $title_long
 * @property string|null $project_status Ongoing, Completed, Superceded
 * @property int|null $location
 * @property int|null $client_id
 * @property string|null $service
 * @property float|null $contract_sum
 * @property string|null $client_pic_name
 * @property string|null $client_pic_contact
 * @property string|null $award_date
 * @property string|null $commencement_date
 * @property string|null $eot_date
 * @property string|null $handover_date
 * @property string|null $dlp_expiry_date
 * @property int|null $proj_director
 * @property int|null $proj_manager
 * @property int|null $site_manager
 * @property int|null $proj_coordinator
 * @property int|null $project_engineer
 * @property int|null $site_engineer
 * @property int|null $site_supervisor
 * @property int|null $project_qs
 * @property string|null $remarks
 * @property int|null $show_in_resume
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property ProjectClosing[] $projectClosings
 * @property ProjectLetters[] $projectLetters
 * @property ContactMaster $client
 * @property RefArea $location0
 * @property User $projCoordinator
 * @property User $projDirector
 * @property User $projectEngineer
 * @property User $projManager
 * @property User $projectQs
 * @property User $siteEngineer
 * @property User $siteManager
 * @property User $siteSupervisor
 * @property User $createdBy
 * @property User $updatedBy
 * @property ProjectProgressClaim[] $projectProgressClaims
 * @property ProjectSubcon[] $projectSubcons
 * @property ProjectVo[] $projectVos
 */
class ProjectMaster extends \yii\db\ActiveRecord {

    public $drop_location, $drop_client;
    public $user_proj_director;
    public $user_proj_manager;
    public $user_site_manager;
    public $user_proj_coordinator;
    public $user_project_engineer;
    public $user_site_engineer;
    public $user_site_supervisor;
    public $user_project_qs;
    public $scannedFile;
    public $files = [];  // For display purpose

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_code'], 'required'],
            [['location', 'client_id', 'proj_director', 'proj_manager', 'site_manager', 'proj_coordinator', 'project_engineer', 'site_engineer', 'site_supervisor', 'project_qs', 'show_in_resume', 'created_by', 'updated_by'], 'integer'],
            [['contract_sum'], 'number'],
            [['award_date', 'commencement_date', 'eot_date', 'handover_date', 'dlp_expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['remarks'], 'string'],
            [['proj_code'], 'string', 'max' => 100],
            [['title_short', 'title_long', 'service', 'client_pic_name', 'client_pic_contact'], 'string', 'max' => 255],
            [['project_status'], 'string', 'max' => 20],
            [['proj_code'], 'unique'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContactMaster::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['location'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['location' => 'area_id']],
            [['proj_coordinator'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['proj_coordinator' => 'id']],
            [['proj_director'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['proj_director' => 'id']],
            [['project_engineer'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['project_engineer' => 'id']],
            [['proj_manager'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['proj_manager' => 'id']],
            [['project_qs'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['project_qs' => 'id']],
            [['site_engineer'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['site_engineer' => 'id']],
            [['site_manager'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['site_manager' => 'id']],
            [['site_supervisor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['site_supervisor' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_code' => 'Project Code',
            'title_short' => 'Title (Short)',
            'title_long' => 'Title (Long)',
            'project_status' => 'Project Status',
            'location' => 'Location',
            'client_id' => 'Client',
            'service' => 'Service',
            'contract_sum' => 'Contract Sum (RM)',
            'client_pic_name' => 'Client P.I.C. Name',
            'client_pic_contact' => 'Client P.I.C. Contact',
            'award_date' => 'Award Date',
            'commencement_date' => 'Commencement Date',
            'eot_date' => 'EOT Date',
            'handover_date' => 'Handover Date',
            'dlp_expiry_date' => 'DLP Expiry Date',
            'proj_director' => 'Project Director',
            'proj_manager' => 'Project Manager',
            'site_manager' => 'Site Manager',
            'proj_coordinator' => 'Project Coordinator',
            'project_engineer' => 'Project Engineer',
            'site_engineer' => 'Site Engineer',
            'site_supervisor' => 'Site Supervisor',
            'project_qs' => 'QS Executive',
            'remarks' => 'Remarks',
            'show_in_resume' => 'Show In Resume',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ProjectClosings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectClosings() {
        return $this->hasMany(ProjectClosing::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectLetters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectLetters() {
        return $this->hasMany(ProjectLetters::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(ContactMaster::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Location0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation0() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'location']);
    }

    /**
     * Gets query for [[ProjCoordinator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjCoordinator() {
        return $this->hasOne(User::className(), ['id' => 'proj_coordinator']);
    }

    /**
     * Gets query for [[ProjDirector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjDirector() {
        return $this->hasOne(User::className(), ['id' => 'proj_director']);
    }

    /**
     * Gets query for [[ProjectEngineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectEngineer() {
        return $this->hasOne(User::className(), ['id' => 'project_engineer']);
    }

    /**
     * Gets query for [[ProjManager]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjManager() {
        return $this->hasOne(User::className(), ['id' => 'proj_manager']);
    }

    /**
     * Gets query for [[ProjectQs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQs() {
        return $this->hasOne(User::className(), ['id' => 'project_qs']);
    }

    /**
     * Gets query for [[SiteEngineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSiteEngineer() {
        return $this->hasOne(User::className(), ['id' => 'site_engineer']);
    }

    /**
     * Gets query for [[SiteManager]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSiteManager() {
        return $this->hasOne(User::className(), ['id' => 'site_manager']);
    }

    /**
     * Gets query for [[SiteSupervisor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSiteSupervisor() {
        return $this->hasOne(User::className(), ['id' => 'site_supervisor']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ProjectProgressClaims]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProgressClaims() {
        return $this->hasMany(ProjectProgressClaim::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectSubcons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSubcons() {
        return $this->hasMany(ProjectSubcon::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectVos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectVos() {
        return $this->hasMany(ProjectVo::className(), ['project_id' => 'id']);
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

    public function createProjectFromProspect($revisionId) {
        $revision = ProspectDetailRevision::findOne($revisionId);
        $prospectDetail = $revision->prospectDetail;
        $prospectMaster = $prospectDetail->prospectMaster;


        $this->proj_code = $prospectMaster->proj_code;
        $this->title_short = $prospectMaster->title_short;
        $this->title_long = $prospectMaster->title_long;
        $this->project_status = "Ongoing";
        $this->location = $prospectMaster->area;
        $this->client_id = $prospectDetail->client_id;
        $this->service = $prospectDetail->service;
        $this->contract_sum = $revision->amount;
        $this->client_pic_name = $prospectDetail->pic_name;
        $this->client_pic_contact = $prospectDetail->pic_contact;
        $this->save();
        $prospectMaster->push_to_project = 1;
        $prospectMaster->update(false);

        $projectList = MasterProjects::copyFromProjectMaster($this);

        return true;
    }

    public function processAndSave() {

        $this->scannedFile = \yii\web\UploadedFile::getInstances($this, 'scannedFile');
        $this->award_date = MyCommonFunction::DBSaveDate($this->award_date);
        $this->commencement_date = MyCommonFunction::DBSaveDate($this->commencement_date);
        $this->eot_date = MyCommonFunction::DBSaveDate($this->eot_date);
        $this->handover_date = MyCommonFunction::DBSaveDate($this->handover_date);
        $this->dlp_expiry_date = MyCommonFunction::DBSaveDate($this->dlp_expiry_date);
        $this->update(false);

        if ($this->proj_code == "") {
            $this->proj_code = "NPL" . $this->id;
            $this->update(false);
        }

        if ($this->scannedFile) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . '/' . $this->proj_code . '/' . Yii::$app->params['tender_doc_folder'];
            MyCommonFunction::mkDirIfNull($filePath);
            foreach ($this->scannedFile as $file) {
                $file->saveAs($filePath . "/" . $file->baseName . "." . $file->extension);
            }
        }

        return true;
    }

    public function getFilesFromFolder() {
        $filePath = Yii::$app->params['project_file_path'] . '/' . $this->proj_code . '/' . Yii::$app->params['tender_doc_folder'];
        if (file_exists($filePath)) {
            $this->files = \yii\helpers\FileHelper::findFiles($filePath . "/", ['recursive' => false]);

            foreach ($this->files as $key => $thefile) {
                $thefile = str_replace("\\", "", substr($thefile, strripos($thefile, "/") + 1));
                $this->files[$key] = $thefile;
            }
        }
    }

    public function getVoTotal() {
        $total = 0;
        $vos = $this->projectVos;
        foreach ($vos as $key => $vo) {
            $total += $vo->amount;
        }
        return $total;
    }

    public function getTotalCertifiedClaim() {
        $total = 0;
        $progresClaims = $this->projectProgressClaims;
        foreach ($progresClaims as $key => $progresClaim) {
            $total += $progresClaim->certified_amount;
        }
        return $total;
    }

    public function updateDates($dateType, $date) {
        $sqldate = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($date);
        switch ($dateType) {
            case 'award':
                $this->award_date = $sqldate;
                break;
            case 'eot':
                $this->eot_date = $sqldate;
                break;
            case 'dlp':
                $this->dlp_expiry_date = $sqldate;
                break;
            case 'hand':
                $this->handover_date = $sqldate;
                break;
            case 'comm':
                $this->commencement_date = $sqldate;
                break;
        }
        return $this->update(false);
    }

}
