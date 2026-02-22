<?php

namespace frontend\models\working\project;

use Yii;
use common\models\User;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "project_vo".
 *
 * @property int $id
 * @property int $project_id
 * @property string|null $ref_no
 * @property string|null $description
 * @property string|null $file
 * @property string|null $date
 * @property float|null $amount
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property ProjectMaster $project
 */
class ProjectVo extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_vo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id'], 'required'],
            [['project_id', 'created_by'], 'integer'],
            [['date', 'created_at'], 'safe'],
            [['amount'], 'number'],
            [['ref_no', 'description'], 'string', 'max' => 255],
            [['file'], 'string', 'max' => 250],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectMaster::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'ref_no' => 'Ref No',
            'description' => 'Description',
            'file' => 'File',
            'date' => 'Date',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
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
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(ProjectMaster::className(), ['id' => 'project_id']);
    }

    /**
     * 
     * @param type $insert
     * @return type
     */
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        if ($this->date) {
            $this->date = MyFormatter::changeDateFormat_readToDB($this->date);
        }

        $projectCode = $this->project->proj_code;
        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . $projectCode . '/' . Yii::$app->params['proj_vo_folder'] . '/';
            if ($this->file && file_exists($filePath . $this->file)) {
                unlink($filePath . $this->file);
            }
            $this->file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->file);
        }

        // Update vs Save
        if ($this->id) {
            $status = $this->update(false);
            \common\models\myTools\FlashHandler::success("V.O. updated!");
        } else {
            $status = $this->save();
            \common\models\myTools\FlashHandler::success("V.O. created!");
        }

        return true;
    }

}
