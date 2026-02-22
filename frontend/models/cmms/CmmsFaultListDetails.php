<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_fault_list_details".
 *
 * @property int $id
 * @property int|null $cmms_asset_or_equipment_list_id
 * @property string|null $fault_area
 * @property string|null $fault_section
 * @property int|null $fault_list_id
 * @property int|null $machine_priority_id
 * @property string|null $additional_remarks
 * @property int|null $is_deleted
 * @property string|null $fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property string|null $maintenance_type
 * @property int|null $machine_breakdown_type_id
 *
 * @property CmmsAssetOrEquipmentList $cmmsAssetOrEquipmentList
 * @property CmmsFaultList $faultList
 * @property RefMachinePriority $machinePriority
 * @property RefMachineBreakdownType $machineBreakdownType
 * @property CmmsMachinePhotos[] $cmmsMachinePhotos
 */
class CmmsFaultListDetails extends \yii\db\ActiveRecord
{
    
    public $photos;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_fault_list_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cmms_asset_list_id', 'fault_list_id', 'machine_priority_id', 'is_deleted', 'machine_breakdown_type_id', 'updated_by'], 'integer'],
            [['fault_asset_code', 'fault_area', 'fault_section', 'additional_remarks', 'fault_type', 'fault_primary_detail', 'fault_secondary_detail'], 'string', 'max' => 255],
            [['cmms_asset_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetList::className(), 'targetAttribute' => ['cmms_asset_list_id' => 'id']],
            [['fault_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsFaultList::className(), 'targetAttribute' => ['fault_list_id' => 'id']],
            [['machine_priority_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMachinePriority::className(), 'targetAttribute' => ['machine_priority_id' => 'id']],
            [['machine_breakdown_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMachineBreakdownType::className(), 'targetAttribute' => ['machine_breakdown_type_id' => 'id']],
            [
                ['photos'],
                'file',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'maxFiles' => 10,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cmms_asset_list_id' => 'Cmms Asset List ID',
            'fault_area' => 'Fault Area',
            'fault_section' => 'Fault Section',
            'fault_list_id' => 'Fault List ID',
            'fault_asset_code' => 'Fault Asset Code',
            'machine_priority_id' => 'Machine Priority ID',
            'additional_remarks' => 'Additional Remarks',
            'is_deleted' => 'Is Deleted',
            'fault_type' => 'Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
            'machine_breakdown_type_id' => 'Machine Breakdown Type ID',
        ];
    }

    /**
     * Gets query for [[CmmsAssetOrEquipmentList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetList()
    {
        return $this->hasOne(CmmsAssetList::className(), ['id' => 'cmms_asset_list_id']);
    }

    /**
     * Gets query for [[FaultList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaultList()
    {
        return $this->hasOne(CmmsFaultList::className(), ['id' => 'fault_list_id']);
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
     * Gets query for [[MachineBreakdownType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMachineBreakdownType()
    {
        return $this->hasOne(RefMachineBreakdownType::className(), ['id' => 'machine_breakdown_type_id']);
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
}
