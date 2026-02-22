<?php

namespace frontend\models\working\project;

use frontend\models\working\contact\ContactMaster;
use Yii;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "project_subcon".
 *
 * @property int $id
 * @property int $project_id
 * @property int|null $vendor_id
 * @property string|null $description
 * @property string|null $file
 * @property string|null $date
 * @property float|null $amount
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectMaster $project
 * @property ContactMaster $vendor
 * @property ProjectSubconClaim[] $projectSubconClaims
 */
class ProjectSubcon extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_subcon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id'], 'required'],
            [['project_id', 'vendor_id', 'created_by'], 'integer'],
            [['file'], 'string'],
            [['date', 'created_at'], 'safe'],
            [['amount'], 'number'],
            [['description'], 'string', 'max' => 255],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectMaster::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContactMaster::className(), 'targetAttribute' => ['vendor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'vendor_id' => 'Vendor ID',
            'description' => 'Description',
            'file' => 'File',
            'date' => 'Date',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
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
     * Gets query for [[Vendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVendor() {
        return $this->hasOne(ContactMaster::className(), ['id' => 'vendor_id']);
    }

    /**
     * Gets query for [[ProjectSubconClaims]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSubconClaims() {
        return $this->hasMany(ProjectSubconClaim::className(), ['proj_sub_id' => 'id']);
    }

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
            $filePath = Yii::$app->params['project_file_path'] . $projectCode . '/' . Yii::$app->params['proj_subcon_folder'] . '/';
            if ($this->file && file_exists($filePath . $this->file)) {
                unlink($filePath . $this->file);
            }
            $this->file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->file);
        }

        // Update vs Save
        if ($this->id) {
            $status = $this->update(false);
            \common\models\myTools\FlashHandler::success("Sub-con. updated!");
        } else {
            $status = $this->save();
            \common\models\myTools\FlashHandler::success("Sub-con. created!");
        }

        return true;
    }

}
