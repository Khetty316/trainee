<?php

namespace frontend\models\bom;

use Yii;
use common\models\User;
use frontend\models\bom\StockDispatchTrial;

/**
 * This is the model class for table "stock_dispatch_master".
 *
 * @property int $id
 * @property string|null $dispatch_no
 * @property int|null $production_panel_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property int|null $received_by
 * @property int|null $status 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $status_updated_at
 * @property int|null $trial_status 0 = pending list, 1 = acknowledged list
 *
 * @property User $receivedBy
 * @property User $createdBy
 * @property StockDispatchTrial[] $stockDispatchTrials
 */
class StockDispatchMaster extends \yii\db\ActiveRecord {

    CONST pending_status = [0 => 'To Be Collected', 1 => 'To Be Acknowledged'];
    CONST all_status = [0 => 'To Be Collected', 1 => 'To Be Acknowledged', 2 => 'Has Been Acknowledged'];
    CONST TO_BE_COLLECTED = '0';
    CONST TO_BE_ACKNOWLEDGED = '1';
    CONST HAS_BEEN_ACKNOWLEDGED = '2';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'stock_dispatch_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['production_panel_id', 'created_by', 'received_by', 'status', 'trial_status'], 'integer'],
            [['created_at', 'status_updated_at'], 'safe'],
            [['dispatch_no'], 'string', 'max' => 255],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dispatch_no' => 'Dispatch No',
            'production_panel_id' => 'Production Panel ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'received_by' => 'Received By',
            'status' => 'Status',
            'status_updated_at' => 'Status Updated At',
            'trial_status' => 'Trial Status',
        ];
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
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[StockDispatchTrials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockDispatchTrials() {
        return $this->hasMany(StockDispatchTrial::className(), ['stock_dispatch_master_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function updateDispatchMasterStatus($actionType = "") {
        if (!$this) {
            throw new \Exception("Dispatch Master not found");
        }
        $hasPendingAcknowledgements = VStockDispatchtrial::find()->where(['stock_dispatch_master_id' => $this->id, 'active_sts' => 1, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED]])->exists();
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
