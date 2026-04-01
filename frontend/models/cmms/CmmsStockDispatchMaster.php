<?php

namespace frontend\models\cmms;

use Yii;
use common\models\User;
use frontend\models\bom\StockDispatchMaster;
use frontend\models\bom\StockDispatchTrial;

/**
 * This is the model class for table "cmms_stock_dispatch_master".
 *
 * @property int $id
 * @property string|null $dispatch_no
 * @property string|null $wo_type cm, pm
 * @property int|null $wo_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property int|null $received_by
 * @property int|null $status 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $status_updated_at
 * @property int|null $trial_status 0 = pending list, 1 = acknowledged list
 *
 * @property User $createdBy
 * @property User $receivedBy
 * @property CmmsStockDispatchTrial[] $cmmsStockDispatchTrials
 */
class CmmsStockDispatchMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cmms_stock_dispatch_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['wo_id', 'created_by', 'received_by', 'status', 'trial_status'], 'integer'],
            [['created_at', 'status_updated_at'], 'safe'],
            [['dispatch_no'], 'string', 'max' => 255],
            [['wo_type'], 'string', 'max' => 10],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dispatch_no' => 'Dispatch No',
            'wo_type' => 'Work Order Type',
            'wo_id' => 'Work Order ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'received_by' => 'Received By',
            'status' => 'Status',
            'status_updated_at' => 'Status Updated At',
            'trial_status' => 'Trial Status',
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
     * Gets query for [[ReceivedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedBy() {
        return $this->hasOne(User::className(), ['id' => 'received_by']);
    }

    /**
     * Gets query for [[CmmsStockDispatchTrials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsStockDispatchTrials() {
        return $this->hasMany(CmmsStockDispatchTrial::className(), ['stock_dispatch_master_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }
    
    public function updateDispatchMasterStatus($actionType = "") {
        if (!$this) {
            throw new \Exception("Dispatch Master not found");
        }
        $hasPendingAcknowledgements = VCmmsStockDispatchtrial::find()->where(['stock_dispatch_master_id' => $this->id, 'active_sts' => 1, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED]])->exists();
        switch ($actionType) {
            case StockDispatchTrial::DISPATCH_STATUS:
                if ($this->status == StockDispatchMaster::TO_BE_COLLECTED) {
                    $this->status = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
                } elseif ($this->status == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                    $this->status = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
                }
                break;

            case StockDispatchTrial::DEACTIVEITEM_STATUS:
                if ($hasPendingAcknowledgements) {
                    if ($this->status == StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED) {
                        $this->status = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
                    }
                } else {
                    $this->status = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
                }
                break;

            default:
                if ($hasPendingAcknowledgements && $this->status == StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED) {
                    $this->status = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
                } elseif (!$hasPendingAcknowledgements) {
                    if ($this->status == StockDispatchMaster::TO_BE_COLLECTED) {
                        $this->status = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
                    } elseif ($this->status == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                        $this->status = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
                    }
                }
                break;
        }

        $this->trial_status = $hasPendingAcknowledgements ? 0 : 1;
        if (!$this->save()) {
            throw new \Exception("Failed to update dispatch master");
        }
    }
}
