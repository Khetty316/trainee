<?php

namespace frontend\models\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "cmms_stock_dispatch_trial".
 *
 * @property int $id
 * @property int|null $request_detail_id
 * @property int|null $stock_dispatch_master_id
 * @property int|null $dispatch_qty
 * @property string|null $remark
 * @property string $status
 * @property string $created_at
 * @property int|null $created_by
 * @property int|null $current_sts 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $status_updated_at
 *
 * @property CmmsWoMaterialRequestDetails $requestDetail
 * @property CmmsStockDispatchMaster $stockDispatchMaster
 * @property User $createdBy
 */
class CmmsStockDispatchTrial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_stock_dispatch_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_detail_id', 'stock_dispatch_master_id', 'dispatch_qty', 'created_by', 'current_sts'], 'integer'],
            [['created_at', 'status_updated_at'], 'safe'],
            [['remark', 'status'], 'string', 'max' => 255],
            [['request_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsWoMaterialRequestDetails::className(), 'targetAttribute' => ['request_detail_id' => 'id']],
            [['stock_dispatch_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsStockDispatchMaster::className(), 'targetAttribute' => ['stock_dispatch_master_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_detail_id' => 'Request Detail ID',
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
     * Gets query for [[RequestDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestDetail()
    {
        return $this->hasOne(CmmsWoMaterialRequestDetails::className(), ['id' => 'request_detail_id']);
    }

    /**
     * Gets query for [[StockDispatchMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockDispatchMaster()
    {
        return $this->hasOne(CmmsStockDispatchMaster::className(), ['id' => 'stock_dispatch_master_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }
}
