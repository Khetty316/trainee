<?php

namespace frontend\models\working\project;

use Yii;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "project_subcon_claim".
 *
 * @property int $id
 * @property int $proj_sub_id
 * @property string|null $submit_reference
 * @property string|null $submit_file
 * @property string|null $submit_date
 * @property float|null $submit_amount
 * @property string|null $certified_reference
 * @property string|null $certified_file
 * @property string|null $certified_date
 * @property float|null $certified_amount
 * @property string|null $invoice_file
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectSubcon $projSub
 */
class ProjectSubconClaim extends \yii\db\ActiveRecord {

    public $scannedFileSubmit;
    public $scannedFileCertified;
    public $scannedFileInvoice;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_subcon_claim';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_sub_id'], 'required'],
            [['proj_sub_id', 'created_by'], 'integer'],
            [['submit_date', 'certified_date', 'created_at'], 'safe'],
            [['submit_amount', 'certified_amount'], 'number'],
            [['remarks'], 'string'],
            [['submit_reference', 'submit_file', 'certified_reference', 'certified_file', 'invoice_file'], 'string', 'max' => 250],
            [['proj_sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectSubcon::className(), 'targetAttribute' => ['proj_sub_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_sub_id' => 'Proj Sub ID',
            'submit_reference' => 'Submit Reference',
            'submit_file' => 'Submit File',
            'submit_date' => 'Submit Date',
            'submit_amount' => 'Submit Amount',
            'certified_reference' => 'Certified Reference',
            'certified_file' => 'Certified File',
            'certified_date' => 'Certified Date',
            'certified_amount' => 'Certified Amount',
            'invoice_file' => 'Invoice File',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[ProjSub]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjSub() {
        return $this->hasOne(ProjectSubcon::className(), ['id' => 'proj_sub_id']);
    }



    public function processAndSave() {
        if ($this->submit_date) {
            $this->submit_date = MyFormatter::changeDateFormat_readToDB($this->submit_date);
        }
        if ($this->certified_date) {
            $this->certified_date = MyFormatter::changeDateFormat_readToDB($this->certified_date);
        }

        $projectCode = $this->projSub->project->proj_code;

        if ($this->validate() && $this->scannedFileSubmit) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_subcon_claim_submit'] . '/';
            if ($this->submit_file && file_exists($filePath . $this->submit_file)) {
                unlink($filePath . $this->submit_file);
            }
            $this->submit_file = date('Ymdhis', time()) . '-' . $this->scannedFileSubmit->baseName . '.' . $this->scannedFileSubmit->extension;
            MyCommonFunction::saveFile($this->scannedFileSubmit, $filePath, $this->submit_file);
        }


        if ($this->validate() && $this->scannedFileCertified) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_subcon_claim_cert'] . '/';
            if ($this->certified_file && file_exists($filePath . $this->certified_file)) {
                unlink($filePath . $this->certified_file);
            }
            $this->certified_file = date('Ymdhis', time()) . '-' . $this->scannedFileCertified->baseName . '.' . $this->scannedFileCertified->extension;
            MyCommonFunction::saveFile($this->scannedFileCertified, $filePath, $this->certified_file);
        }
        
        if ($this->validate() && $this->scannedFileInvoice) {
            $filePath = Yii::$app->params['project_file_path'] . '/' . $projectCode . '/' . Yii::$app->params['proj_subcon_claim_inv'] . '/';
            if ($this->invoice_file && file_exists($filePath . $this->invoice_file)) {
                unlink($filePath . $this->invoice_file);
            }
            $this->invoice_file = date('Ymdhis', time()) . '-' . $this->scannedFileInvoice->baseName . '.' . $this->scannedFileInvoice->extension;
            MyCommonFunction::saveFile($this->scannedFileInvoice, $filePath, $this->invoice_file);
        }

        if ($this->id) {
            $status = $this->update(false);
            \common\models\myTools\FlashHandler::success("Progress claim updated!");
        } else {
            $status = $this->save();
            \common\models\myTools\FlashHandler::success("Progress claim created!");
        }

        return $status;
    }

}
