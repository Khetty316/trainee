<?php

namespace frontend\models\working\project;

use Yii;
use frontend\models\common\RefArea;
use frontend\models\common\RefProjectType;
use common\models\User;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "prospect_master".
 *
 * @property int $id
 * @property string $proj_code
 * @property string|null $title_short
 * @property string|null $title_long
 * @property string|null $due_date
 * @property int|null $area
 * @property int|null $staff_pic
 * @property string|null $other_pic
 * @property string|null $project_type
 * @property int $push_to_project
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property ProspectDetail[] $prospectDetails
 * @property RefArea $area0
 * @property User $createdBy
 * @property User $updatedBy
 * @property RefProjectType $projectType
 * @property User $staffPic
 * @property ProspectMasterScope[] $prospectMasterScopes
 */
class ProspectMaster extends \yii\db\ActiveRecord {

    public $locationDropdown;
    public $staffPicDropdown;
    public $scannedFile;
    public $files = [];  // For display purpose

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prospect_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
//            [['proj_code'], 'required'],
            [['title_short'], 'required'],
            [['due_date', 'created_at', 'updated_at'], 'safe'],
            [['area', 'staff_pic', 'push_to_project', 'created_by', 'updated_by'], 'integer'],
            [['proj_code'], 'string', 'max' => 100],
            [['title_short', 'title_long', 'other_pic', 'locationDropdown'], 'string', 'max' => 255],
            [['project_type'], 'string', 'max' => 10],
            [['area'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['area' => 'area_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['project_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectType::className(), 'targetAttribute' => ['project_type' => 'code']],
            [['staff_pic'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['staff_pic' => 'id']],
            ['scannedFile', 'file', 'maxFiles' => 0, 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'skipOnEmpty' => true],
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
            'due_date' => 'Due Date',
            'area' => 'Location',
            'staff_pic' => 'NPL P.I.C',
            'other_pic' => 'Other P.I.C',
            'project_type' => 'Project Type',
            'push_to_project' => 'Push To Project',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[ProspectDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetails() {
        return $this->hasMany(ProspectDetail::className(), ['prospect_master' => 'id']);
    }

    /**
     * Gets query for [[Area0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea0() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'area']);
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
     * Gets query for [[ProjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectType() {
        return $this->hasOne(RefProjectType::className(), ['code' => 'project_type']);
    }

    /**
     * Gets query for [[StaffPic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStaffPic() {
        return $this->hasOne(User::className(), ['id' => 'staff_pic']);
    }

    /**
     * Gets query for [[ProspectMasterScopes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectMasterScopes() {
        return $this->hasMany(ProspectMasterScope::className(), ['master_prospect' => 'id']);
    }

    /**
     * Save Function
     */
    public function processAndSave() {

        $this->proj_code = ($this->proj_code == "" ? "" : $this->proj_code);
        $this->due_date = ($this->due_date == "") ? "" : \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->due_date);
        $this->save();


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

}
