<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "v_cmms_stock_dispatch_trial".
 *
 * @property int $trial_id
 * @property int|null $request_detail_id
 * @property int|null $stock_dispatch_master_id
 * @property int|null $dispatch_qty
 * @property string|null $remark
 * @property string $status
 * @property string $trial_created_at
 * @property int|null $trial_created_by
 * @property int $request_master_id
 * @property string|null $model_type
 * @property string|null $brand
 * @property string|null $descriptions
 * @property float|null $qty
 * @property string|null $superior_remark
 * @property float|null $dispatched_qty
 * @property float|null $unacknowledged_qty
 * @property int $active_sts
 * @property int $fully_dispatch_status
 * @property string|null $dispatch_no
 * @property int|null $received_by
 * @property int|null $current_sts 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $status_updated_at
 */
class VCmmsStockDispatchTrial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_cmms_stock_dispatch_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trial_id', 'request_detail_id', 'stock_dispatch_master_id', 'dispatch_qty', 'trial_created_by', 'request_master_id', 'active_sts', 'fully_dispatch_status', 'received_by', 'current_sts'], 'integer'],
            [['trial_created_at', 'status_updated_at'], 'safe'],
            [['request_master_id'], 'required'],
            [['qty', 'dispatched_qty', 'unacknowledged_qty'], 'number'],
            [['remark', 'status', 'dispatch_no'], 'string', 'max' => 255],
            [['model_type', 'brand', 'descriptions', 'superior_remark'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'trial_id' => 'Trial ID',
            'request_detail_id' => 'Request Detail ID',
            'stock_dispatch_master_id' => 'Stock Dispatch Master ID',
            'dispatch_qty' => 'Dispatch Qty',
            'remark' => 'Remark',
            'status' => 'Status',
            'trial_created_at' => 'Trial Created At',
            'trial_created_by' => 'Trial Created By',
            'request_master_id' => 'Request Master ID',
            'model_type' => 'Model Type',
            'brand' => 'Brand',
            'descriptions' => 'Descriptions',
            'qty' => 'Qty',
            'superior_remark' => 'Superior Remark',
            'dispatched_qty' => 'Dispatched Qty',
            'unacknowledged_qty' => 'Unacknowledged Qty',
            'active_sts' => 'Active Sts',
            'fully_dispatch_status' => 'Fully Dispatch Status',
            'dispatch_no' => 'Dispatch No',
            'received_by' => 'Received By',
            'current_sts' => 'Current Sts',
            'status_updated_at' => 'Status Updated At',
        ];
    }
}
