<?php

namespace frontend\models\working\documentreminder;

use Yii;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "document_reminder_master".
 *
 * @property int $id
 * @property string $category
 * @property int $active_sts
 * @property string|null $description
 * @property string $filename
 * @property string|null $expiry_date
 * @property int|null $remind_period
 * @property string|null $remind_period_unit
 * @property string|null $remind_date
 * @property string|null $remark
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class DocumentReminderMaster extends \yii\db\ActiveRecord {

    public $scannedFile;

    CONST ALERT_DANGER = 2, ALERT_WARNING = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'document_reminder_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['category', 'filename', 'description'], 'required'],
            [['active_sts', 'remind_period', 'created_by', 'updated_by'], 'integer'],
            [['expiry_date', 'remind_date', 'created_at', 'updated_at'], 'safe'],
            [['remark'], 'string'],
            [['category', 'description', 'filename'], 'string', 'max' => 255],
            [['remind_period_unit'], 'string', 'max' => 10],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
//            ['scannedFile', 'file', 'extensions' => 'png, jpg, pdf, jpeg', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'active_sts' => 'Active Sts',
            'description' => 'Description',
            'filename' => 'Filename',
            'expiry_date' => 'Expiry Date',
            'remind_period' => 'Remind Period',
            'remind_period_unit' => 'Remind Period Unit',
            'remind_date' => 'Remind Date',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        if ($this->expiry_date) {
            $this->expiry_date = MyFormatter::fromDateRead_toDateSQL($this->expiry_date);
        }
        if ($this->remind_date) {
            $this->remind_date = MyFormatter::fromDateRead_toDateSQL($this->remind_date);
        }

        if (!$this->filename) {
            $this->filename = 'temp';
        }

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['companydocument_file_path'];
            MyCommonFunction::mkDirIfNull($filePath);
            $this->filename = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            $this->scannedFile->saveAs($filePath . $this->filename);
        }



        if (!$this->id && $this->save()) {
            FlashHandler::success("Document saved!");
            return true;
        } else if ($this->id && $this->update()) {
            FlashHandler::success("Document updated!");
            return true;
        } else {
            FlashHandler::err("Document saved FAIL!");
            return false;
        }
    }

    public function getDocumentWarningLevel() {
        $today = date("Y-m-d");
        if ($this->expiry_date <= $today) {
            return self::ALERT_DANGER;
        } else if ($this->remind_date < $today) {
            return self::ALERT_WARNING;
        } else {
            return 0;
        }
    }

    public function getDocumentPassReminderDate() {
        $today = date("Y-m-d");
        if ($this->remind_date <= $today) {
            return self::ALERT_WARNING;
        } else {
            return 0;
        }
    }

    public function getDocumentPassExpiryDate() {
        $today = date("Y-m-d");
        if ($this->expiry_date <= $today) {
            return self::ALERT_DANGER;
        } else {
            return 0;
        }
    }

}
