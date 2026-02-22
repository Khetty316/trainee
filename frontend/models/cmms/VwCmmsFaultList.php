<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "vw_cmms_fault_list".
 *
 * @property int $fault_list_id
 * @property int|null $reported_by
 * @property int|null $reviewed_by
 * @property string|null $fault_area
 * @property string|null $fault_section
 * @property string|null $fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property int|null $fault_list_detail_id
 * @property int|null $machine_breakdown_type_id
 * @property int|null $machine_priority_id
 * @property string|null $additional_remarks
 * @property int|null $photo_id
 * @property string|null $file_name
 */
class VwCmmsFaultList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vw_cmms_fault_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fault_list_id', 'reported_by', 'reviewed_by', 'fault_list_detail_id', 'machine_breakdown_type_id', 'machine_priority_id', 'photo_id'], 'integer'],
            [['fault_area', 'fault_section', 'fault_type', 'fault_primary_detail', 'fault_secondary_detail', 'additional_remarks', 'file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fault_list_id' => 'Fault List ID',
            'reported_by' => 'Reported By',
            'reviewed_by' => 'Reviewed By',
            'fault_area' => 'Fault Area',
            'fault_section' => 'Fault Section',
            'fault_type' => 'Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
            'fault_list_detail_id' => 'Fault List Detail ID',
            'machine_breakdown_type_id' => 'Machine Breakdown Type ID',
            'machine_priority_id' => 'Machine Priority ID',
            'additional_remarks' => 'Additional Remarks',
            'photo_id' => 'Photo ID',
            'file_name' => 'File Name',
        ];
    }
}
