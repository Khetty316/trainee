<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_fault_list".
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $reported_by
 * @property int|null $reviewed_by
 * @property string|null $reviewed_at
 * @property int|null $cmms_work_order_id
 * @property int|null $status
 * @property int|null $is_deleted
 * @property string|null $reported_at
 * @property string|null $follow_up_required
 * @property int|null $asset_id
 * @property int|null $superior_id
 * @property string|null $maintenance_type
 * @property int|null $active_sts
 * @property int|null $machine_priority_id
 * @property string|null $additional_remarks
 * @property int|null $cmms_asset_list_id
 * @property string|null $fault_area
 * @property string|null $fault_section
 * @property string|null $fault_asset_id
 * @property string|null $fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $last_record
 * @property int|null $frequency
 * @property string|null $remedial_actions
 * @property int|null $part_list_id
 * @property int|null $tool_list_id
 * @property string|null $safety_precautions
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 * @property CmmsAssetList $cmmsAssetList
 * @property RefMachinePriority $machinePriority
 * @property CmmsCorrectiveWorkOrderMaster $cmmsWorkOrder
 * @property RefCmmsStatus $status0
 * @property CmmsPartList $partList
 * @property CmmsToolList $toolList
 * @property CmmsMachinePhotos[] $cmmsMachinePhotos
 */
class CmmsFaultList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_fault_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reported_by', 'reviewed_by', 'cmms_work_order_id', 'status', 'is_deleted', 'asset_id', 'superior_id', 'active_sts', 'machine_priority_id', 'cmms_asset_list_id', 'updated_by', 'frequency', 'part_list_id', 'tool_list_id'], 'integer'],
            [['reviewed_at', 'reported_at', 'updated_at', 'last_record'], 'safe'],
            [['code', 'follow_up_required', 'maintenance_type', 'additional_remarks', 'fault_area', 'fault_section', 'fault_asset_id', 'fault_type', 'fault_primary_detail', 'fault_secondary_detail', 'remedial_actions', 'safety_precautions'], 'string', 'max' => 255],
            [['cmms_asset_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetList::className(), 'targetAttribute' => ['cmms_asset_list_id' => 'id']],
            [['machine_priority_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMachinePriority::className(), 'targetAttribute' => ['machine_priority_id' => 'id']],
            [['cmms_work_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsCorrectiveWorkOrderMaster::className(), 'targetAttribute' => ['cmms_work_order_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefCmmsStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['part_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPartList::className(), 'targetAttribute' => ['part_list_id' => 'id']],
            [['tool_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsToolList::className(), 'targetAttribute' => ['tool_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'reported_by' => 'Reported By',
            'reviewed_by' => 'Reviewed By',
            'reviewed_at' => 'Reviewed At',
            'cmms_work_order_id' => 'Cmms Work Order ID',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'reported_at' => 'Reported At',
            'follow_up_required' => 'Follow Up Required',
            'asset_id' => 'Asset ID',
            'superior_id' => 'Superior ID',
            'maintenance_type' => 'Maintenance Type',
            'active_sts' => 'Active Sts',
            'machine_priority_id' => 'Machine Priority ID',
            'additional_remarks' => 'Additional Remarks',
            'cmms_asset_list_id' => 'Cmms Asset List ID',
            'fault_area' => 'Fault Area',
            'fault_section' => 'Fault Section',
            'fault_asset_id' => 'Fault Asset ID',
            'fault_type' => 'Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'last_record' => 'Last Record',
            'frequency' => 'Frequency',
            'remedial_actions' => 'Remedial Actions',
            'part_list_id' => 'Part List ID',
            'tool_list_id' => 'Tool List ID',
            'safety_precautions' => 'Safety Precautions',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['cmms_fault_list_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsAssetList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetList()
    {
        return $this->hasOne(CmmsAssetList::className(), ['id' => 'cmms_asset_list_id']);
    }

    /**
     * Gets query for [[MachinePriority]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMachinePriority()
    {
        return $this->hasOne(RefMachinePriority::className(), ['id' => 'machine_priority_id']);
    }

    /**
     * Gets query for [[CmmsWorkOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsWorkOrder()
    {
        return $this->hasOne(CmmsCorrectiveWorkOrderMaster::className(), ['id' => 'cmms_work_order_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(RefCmmsStatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[PartList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartList()
    {
        return $this->hasOne(CmmsPartList::className(), ['id' => 'part_list_id']);
    }

    /**
     * Gets query for [[ToolList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToolList()
    {
        return $this->hasOne(CmmsToolList::className(), ['id' => 'tool_list_id']);
    }

    /**
     * Gets query for [[CmmsMachinePhotos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsMachinePhotos()
    {
        return $this->hasMany(CmmsMachinePhotos::className(), ['cmms_fault_list_details_id' => 'id']);
    }
    
    public static function getFrequency($primary_description, $secondary_description)
    {
        $count = CmmsFaultList::find()
                ->where(['fault_primary_detail' => $primary_description])
                ->andWhere(['fault_secondary_detail' => $secondary_description])
                ->andWhere(['is_deleted' => 0])
                ->count();
        
        return $count;
    }
}
