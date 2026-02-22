<?php

namespace frontend\models\bom;

use Yii;
use common\models\User;

/**
 * This is the model class for table "stock_dispatch_trial".
 *
 * @property int $id
 * @property int|null $stock_outbound_details_id
 * @property int|null $stock_dispatch_master_id
 * @property int|null $dispatch_qty
 * @property string|null $remark
 * @property string $status
 * @property string $created_at
 * @property int|null $created_by
 * @property int|null $current_sts 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $status_updated_at
 *
 * @property StockOutboundDetails $stockOutboundDetails
 * @property User $createdBy
 * @property StockDispatchMaster $stockDispatchMaster
 */
class StockDispatchTrial extends \yii\db\ActiveRecord {

    const DISPATCH_STATUS = 'dispatch';
    const ADJUST_STATUS = 'adjust';
    const RETURN_STATUS = 'return';
    const DEACTIVEITEM_STATUS = 'deactiveItem';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'stock_dispatch_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['stock_outbound_details_id', 'stock_dispatch_master_id', 'dispatch_qty', 'created_by', 'current_sts'], 'integer'],
            [['created_at', 'status_updated_at'], 'safe'],
            [['remark', 'status'], 'string', 'max' => 255],
            [['stock_outbound_details_id'], 'exist', 'skipOnError' => true, 'targetClass' => StockOutboundDetails::className(), 'targetAttribute' => ['stock_outbound_details_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['stock_dispatch_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => StockDispatchMaster::className(), 'targetAttribute' => ['stock_dispatch_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'stock_outbound_details_id' => 'Stock Outbound Details ID',
            'stock_dispatch_master_id' => 'Stock Dispatch Master ID',
            'dispatch_qty' => 'Dispatch Qty',
            'remark' => 'Remark',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'current_sts' => 'Current Sts',
            'status_updated_at' => 'Status Updated At',
        ];
    }

    /**
     * Gets query for [[StockOutboundDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundDetails() {
        return $this->hasOne(StockOutboundDetails::className(), ['id' => 'stock_outbound_details_id']);
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
     * Gets query for [[StockDispatchMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockDispatchMaster() {
        return $this->hasOne(StockDispatchMaster::className(), ['id' => 'stock_dispatch_master_id']);
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
}
