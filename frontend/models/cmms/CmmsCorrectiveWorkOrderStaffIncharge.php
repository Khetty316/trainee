<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_corrective_work_order_staff_incharge".
 *
 * @property int $id
 * @property int|null $work_order_detail_id
 * @property int|null $staff_id
 * @property int|null $labour_start_at
 * @property int|null $labour_end_at
 * @property string|null $repair_duration
 * @property string|null $remark
 * @property int|null $work_status
 * @property int|null $active_sts
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 */
class CmmsCorrectiveWorkOrderStaffIncharge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_corrective_work_order_staff_incharge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'work_order_detail_id', 'staff_id', 'labour_start_at', 'labour_end_at', 'work_status', 'active_sts'], 'integer'],
            [['repair_duration'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_order_detail_id' => 'Work Order Detail ID',
            'staff_id' => 'Staff ID',
            'labour_start_at' => 'Labour Start At',
            'labour_end_at' => 'Labour End At',
            'repair_duration' => 'Repair Duration',
            'remark' => 'Remark',
            'work_status' => 'Work Status',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['supervisor_id' => 'id']);
    }
}
