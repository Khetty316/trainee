<?php

namespace frontend\models\covid\testkit;

use Yii;
use common\models\User;
use common\models\myTools\FlashHandler;

/**
 * This is the model class for table "covid_testkit_record".
 *
 * @property int $id
 * @property int $inventory_id
 * @property int|null $user_id
 * @property string|null $brand
 * @property int $complete_status
 * @property string|null $result_attachment
 * @property string|null $remark
 * @property string|null $updated_at
 * @property string $created_at
 *
 * @property CovidStatusForm[] $covidStatusForms
 * @property CovidTestkitInventory $inventory
 * @property User $user
 */
class CovidTestkitRecord extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'covid_testkit_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_id'], 'required'],
            [['inventory_id', 'user_id', 'complete_status'], 'integer'],
            [['remark'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['brand', 'result_attachment'], 'string', 'max' => 255],
            [['inventory_id'], 'exist', 'skipOnError' => true, 'targetClass' => CovidTestkitInventory::className(), 'targetAttribute' => ['inventory_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_id' => 'Inventory ID',
            'user_id' => 'User ID',
            'brand' => 'Brand',
            'complete_status' => 'Complete Status',
            'result_attachment' => 'Result Attachment',
            'remark' => 'Remark',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[CovidStatusForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCovidStatusForms() {
        return $this->hasMany(CovidStatusForm::className(), ['self_covid_kit_id' => 'id']);
    }

    /**
     * Gets query for [[Inventory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventory() {
        return $this->hasOne(CovidTestkitInventory::className(), ['id' => 'inventory_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function createFromInventory($covidTestkitInventory) {
        $this->brand = $covidTestkitInventory->brand;
        $this->inventory_id = $covidTestkitInventory->id;
        $this->user_id = $covidTestkitInventory->giving_to;
        return $this->save();
    }

    public function updateResult($attachFilename) {
        $this->complete_status = 1;
        $this->result_attachment = $attachFilename;
        return $this->update(false);
    }

    public static function getMyUnusedTestKitDropdownList() {
        $list = CovidTestkitRecord::find()->where(['user_id' => Yii::$app->user->id, 'complete_status' => 0])->orderBy(['id' => SORT_ASC])->all();
        $returnArr = array();
        foreach ($list as $key => $record) {
            $returnArr[$record->id] = $record->brand . ' ' . \common\models\myTools\MyFormatter::asDate_Read($record->inventory->record_date);
        }
        return $returnArr;
    }

    public function personalUpdate($remark) {
        if ($this->user_id != Yii::$app->user->id) {
            FlashHandler::err("This record does not belongs to you. Update fail.");
            return false;
        } else if ($this->complete_status != 0) {
            FlashHandler::err("Someone already updated this record. Update fail.");
            return false;
        } else {


            if ($this->validate() && $this->scannedFile) {
                $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['covid_result_file_path'];
                $this->result_attachment = $this->id . '-manual-update' . '.' . $this->scannedFile->extension;
                \common\models\myTools\MyCommonFunction::mkDirIfNull($filePath);
                $this->scannedFile->saveAs($filePath . $this->result_attachment);
            }

            $this->remark = $remark;
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->complete_status = 1;
            FlashHandler::success("Record updated.");
            return $this->update(false);
        }
    }

}
