<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_corrective_work_request".
 *
 * @property int $id
 * @property int|null $submitted_by
 * @property int|null $machine_breakdown_type_id
 * @property int|null $reviewed_by
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 * @property RefCorrectiveMachineBreakdownType $machineBreakdownType
 */
class CmmsCorrectiveWorkRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_corrective_work_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'submitted_by', 'machine_breakdown_type_id', 'reviewed_by'], 'integer'],
            [['id'], 'unique'],
            [['machine_breakdown_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCorrectiveMachineBreakdownType::className(), 'targetAttribute' => ['machine_breakdown_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'submitted_by' => 'Submitted By',
            'machine_breakdown_type_id' => 'Machine Breakdown Type ID',
            'reviewed_by' => 'Reviewed By',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['work_request_id' => 'id']);
    }

    /**
     * Gets query for [[MachineBreakdownType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMachineBreakdownType()
    {
        return $this->hasOne(RefCorrectiveMachineBreakdownType::className(), ['id' => 'machine_breakdown_type_id']);
    }
}
