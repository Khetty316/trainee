<?php

namespace frontend\models\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "cmms_wo_material_request_master".
 *
 * @property int $id
 * @property string|null $wo_type cm, pm
 * @property int|null $wo_id
 * @property int|null $finalized_status 0 = no, 1 = fully, 2 = partially
 * @property int $fully_dispatched_status 0 = no, 1 = fully, 2 = partially
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property CmmsWoMaterialRequestDetails[] $cmmsWoMaterialRequestDetails
 * @property User $createdBy
 * @property User $updatedBy
 */
class CmmsWoMaterialRequestMaster extends \yii\db\ActiveRecord {

    CONST WO_TYPE_CM = "cm";
    CONST WO_TYPE_PM = "pm";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cmms_wo_material_request_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['wo_id', 'finalized_status', 'fully_dispatched_status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['wo_type'], 'string', 'max' => 10],
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
            'wo_type' => 'Work Order Type',
            'wo_id' => 'Work Order ID',
            'finalized_status' => 'Finalized Status',
            'fully_dispatched_status' => 'Fully Dispatched Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[CmmsWoMaterialRequestDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsWoMaterialRequestDetails() {
        return $this->hasMany(CmmsWoMaterialRequestDetails::className(), ['request_master_id' => 'id']);
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

    public static function getFinalizedStatusList() {
        return [
            0 => 'Pending',
            2 => 'Partially Finalized',
            1 => 'Fully Finalized',
        ];
    }

    public static function getDispatchedStatusList() {
        return [
            0 => 'Pending',
            2 => 'Partially Dispatched',
            1 => 'Fully Dispatched',
        ];
    }

    public function getFinalizedStatusLabel() {
        switch ($this->finalized_status) {
            case 0: return '<span class="text-warning">Pending</span>';
            case 1: return '<span class="text-success">Fully Finalized</span>';
            case 2: return '<span class="text-info">Partially Finalized</span>';
            default: return '<span class="text-secondary">-</span>';
        }
    }

    public function getDispatchedStatusLabel() {
        switch ($this->fully_dispatched_status) {
            case 0: return '<span class="text-warning">Pending</span>';
            case 1: return '<span class="text-success">Fully Dispatched</span>';
            case 2: return '<span class="text-info">Partially Dispatched</span>';
            default: return '<span class="btext-secondary">-</span>';
        }
    }
}
