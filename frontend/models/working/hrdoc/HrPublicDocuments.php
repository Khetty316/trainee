<?php

namespace frontend\models\working\hrdoc;

use Yii;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "hr_public_documents".
 *
 * @property int $id
 * @property string $category
 * @property int $active_sts
 * @property string|null $description
 * @property string $filename
 * @property string|null $remark
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class HrPublicDocuments extends \yii\db\ActiveRecord {

    public $scannedFile;

    CONST ALERT_DANGER = 2, ALERT_WARNING = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_public_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['category', 'filename', 'description'], 'required'],
            [['active_sts', 'created_by', 'updated_by', 'show_alert'], 'integer'],
            [['remark'], 'string'],
            [['created_at', 'updated_at', 'file_date'], 'safe'],
            [['category', 'description', 'filename'], 'string', 'max' => 255],
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
            'filename' => 'Document',
            'remark' => 'Remark',
            'file_date' => 'File Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'show_alert' => 'Show Alert?',
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
        if (!$this->filename) {
            $this->filename = 'temp';
        }

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['publicdocument_file_path'];
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

//
//    public function getDocumentWarningLevel() {
//        $today = date("Y-m-d");
//        if ($this->expiry_date <= $today) {
//            return self::ALERT_DANGER;
//        } else if ($this->remind_date < $today) {
//            return self::ALERT_WARNING;
//        } else {
//            return 0;
//        }
//    }

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
