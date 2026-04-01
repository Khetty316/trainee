<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "v_cmms_stock_dispatch_master".
 *
 * @property int $dispatch_id
 * @property string|null $dispatch_no
 * @property int|null $wo_id
 * @property string|null $wo_type cm, pm
 * @property string|null $dispatch_created_at
 * @property int|null $dispatch_created_by
 * @property string|null $received_by
 * @property int|null $master_status 0 = to be collected, 1 = to be acknowledged, 2 = has been acknowledged
 * @property string|null $master_status_updated_at
 * @property int|null $master_trial_status 0 = pending list, 1 = acknowledged list
 * @property int|null $request_detail_id
 * @property float|null $total_trial_dispatch_qty
 * @property int|null $request_master_id
 * @property string|null $model_type
 * @property string|null $brand
 * @property string|null $descriptions
 * @property float|null $detail_qty
 * @property string|null $superior_remark
 * @property float|null $dispatched_qty
 * @property float|null $unacknowledged_qty
 * @property int|null $active_sts
 * @property int|null $fully_dispatch_status
 */
class VCmmsStockDispatchMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_cmms_stock_dispatch_master';
    }

    public static function primaryKey() {
        return ["dispatch_id"];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dispatch_id', 'wo_id', 'dispatch_created_by', 'master_status', 'master_trial_status', 'request_detail_id', 'request_master_id', 'active_sts', 'fully_dispatch_status'], 'integer'],
            [['dispatch_created_at', 'master_status_updated_at'], 'safe'],
            [['total_trial_dispatch_qty', 'detail_qty', 'dispatched_qty', 'unacknowledged_qty'], 'number'],
            [['dispatch_no', 'received_by'], 'string', 'max' => 255],
            [['wo_type'], 'string', 'max' => 10],
            [['model_type', 'brand', 'descriptions', 'superior_remark'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dispatch_id' => 'Dispatch ID',
            'dispatch_no' => 'Dispatch No',
            'wo_id' => 'Wo ID',
            'wo_type' => 'Wo Type',
            'dispatch_created_at' => 'Dispatch Created At',
            'dispatch_created_by' => 'Dispatch Created By',
            'received_by' => 'Received By',
            'master_status' => 'Master Status',
            'master_status_updated_at' => 'Master Status Updated At',
            'master_trial_status' => 'Master Trial Status',
            'request_detail_id' => 'Request Detail ID',
            'total_trial_dispatch_qty' => 'Total Trial Dispatch Qty',
            'request_master_id' => 'Request Master ID',
            'model_type' => 'Model Type',
            'brand' => 'Brand',
            'descriptions' => 'Descriptions',
            'detail_qty' => 'Detail Qty',
            'superior_remark' => 'Superior Remark',
            'dispatched_qty' => 'Dispatched Qty',
            'unacknowledged_qty' => 'Unacknowledged Qty',
            'active_sts' => 'Active Sts',
            'fully_dispatch_status' => 'Fully Dispatch Status',
        ];
    }
}
