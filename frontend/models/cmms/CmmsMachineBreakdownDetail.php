<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_machine_breakdown_detail".
 *
 * @property int $id
 * @property int|null $machine_breakdown_type_id
 * @property int|null $fault_list_id
 * @property int|null $machine_priority_id
 * @property string|null $additional_remarks
 * @property string|null $date_reported
 * @property int|null $is_deleted
 *
 * @property CmmsFaultList $faultList
 * @property RefMachinePriority $machinePriority
 * @property RefMachineBreakdownType $machineBreakdownType
 * @property CmmsMachinePhotos[] $cmmsMachinePhotos
 */
class CmmsMachineBreakdownDetail extends \yii\db\ActiveRecord
{
    public $photos;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_machine_breakdown_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['machine_breakdown_type_id', 'fault_list_id', 'machine_priority_id', 'is_deleted'], 'integer'],
            [['date_reported'], 'safe'],
            [['additional_remarks'], 'string', 'max' => 255],
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
            'machine_breakdown_type_id' => 'Machine Breakdown Type ID',
            'fault_list_id' => 'Fault List ID',
            'machine_priority_id' => 'Machine Priority ID',
            'additional_remarks' => 'Additional Remarks',
            'date_reported' => 'Date Reported',
            'is_deleted' => 'Is Deleted',
            'updated_by' => 'Updated By',
        ];
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
        return $this->hasMany(CmmsMachinePhotos::className(), ['cmms_machine_breakdown_detail_id' => 'id']);
    }
}
