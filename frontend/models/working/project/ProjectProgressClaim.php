<?php

namespace frontend\models\working\project;

use Yii;
use common\models\myTools\MyCommonFunction;
use common\models\myTools\MyFormatter;

/**
 * This is the model class for table "project_progress_claim".
 *
 * @property int $id
 * @property int $project_id
 * @property string|null $submit_reference
 * @property string|null $submit_file
 * @property string|null $submit_date
 * @property float|null $submit_amount
 * @property string|null $certified_reference
 * @property string|null $certified_file
 * @property string|null $certified_date
 * @property float|null $certified_amount
 * @property string|null $invoice_file
 * @property string|null $invoice_date
 * @property int|null $invoice_by
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectMaster $project
 * @property ProjectProgressClaimPay[] $projectProgressClaimPays
 */
class ProjectProgressClaim extends \yii\db\ActiveRecord {

//    public $scannedDocSubmit = '';
//    public $scannedDocCertified = '';
    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_progress_claim';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id'], 'required'],
            [['project_id', 'invoice_by', 'created_by'], 'integer'],
            [['submit_date', 'certified_date', 'invoice_date', 'created_at'], 'safe'],
            [['submit_amount', 'certified_amount'], 'number'],
            [['remarks'], 'string'],
            [['submit_reference', 'submit_file', 'certified_reference', 'certified_file', 'invoice_file'], 'string', 'max' => 250],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectMaster::className(), 'targetAttribute' => ['project_id' => 'id']],
//            ['scannedFile', 'file', 'maxFiles' => 1, 'skipOnEmpty' => true],
//            ['scannedFile', 'file', 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
//            
            [['scannedFile'], 'file', 'maxFiles' => 1, 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf, jpeg'],
            ['scannedFile', 'file', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
//            ['scannedFile', 'file', 'maxFiles' => 1, 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'submit_reference' => 'Submit Reference',
            'submit_file' => 'Submit File',
            'submit_date' => 'Submit Date',
            'submit_amount' => 'Submit Amount',
            'certified_reference' => 'Certified Reference',
            'certified_file' => 'Certified File',
            'certified_date' => 'Certified Date',
            'certified_amount' => 'Certified Amount',
            'invoice_file' => 'Invoice File',
            'invoice_date' => 'Invoice Date',
            'invoice_by' => 'Invoice By',
            'remarks' => 'Remarks',
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
     * Gets query for [[ProjectProgressClaimPays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProgressClaimPays() {
        return $this->hasMany(ProjectProgressClaimPay::className(), ['progress_claim_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
//        else {
//            $this->updated_at = new Expression('NOW()');
//        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        if ($this->submit_date) {
            $this->submit_date = MyFormatter::changeDateFormat_readToDB($this->submit_date);
        }

        $projectCode = $this->project->proj_code;
        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_main_claim_folder'] . '/';
            if ($this->submit_file && file_exists($filePath . $this->submit_file)) {
                unlink($filePath . $this->submit_file);
            }
            $this->submit_file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->submit_file);
        }
        if ($this->id) {
            $status = $this->update(false);
            \common\models\myTools\FlashHandler::success("Progress claim updated!");
        } else {
            $status = $this->save();
            \common\models\myTools\FlashHandler::success("Progress claim created!");
        }

        return true;
    }

    public function issueInvoice() {
        if ($this->invoice_date) {
            $this->invoice_date = MyFormatter::changeDateFormat_readToDB($this->invoice_date);
        }

        $projectCode = $this->project->proj_code;
        if ($this->validate() && $this->scannedFile) {

            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_main_claim_folder'] . '/';
            if ($this->invoice_file && file_exists($filePath . $this->invoice_file)) {
                unlink($filePath . $this->invoice_file);
            }
            $this->invoice_file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->invoice_file);
        }

        $this->invoice_by = Yii::$app->user->id;
        return $this->update(false);
//        
//        if ($this->id) {
//            $status = $this->update(false);
//            \common\models\myTools\FlashHandler::success("Progress claim updated!");
//        } else {
//            $status = $this->save();
//            \common\models\myTools\FlashHandler::success("Progress claim created!");
//        }
    }

    public function processAndSaveCertified() {
        if ($this->certified_date) {
            $this->certified_date = MyFormatter::changeDateFormat_readToDB($this->certified_date);
        }
        $projectCode = $this->project->proj_code;

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_main_claim_folder'] . '/';
            if ($this->certified_file && file_exists($filePath . $this->certified_file)) {
                unlink($filePath . $this->certified_file);
            }
            $this->certified_file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->certified_file);
        }
        $this->update();
        return true;
    }

}
