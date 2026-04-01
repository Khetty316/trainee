<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_assigned_pic".
 *
 * @property int $id
 * @property int|null $corrective_work_order_master_id
 * @property int|null $preventive_work_order_master_id
 * @property string|null $name
 * @property int|null $staff_id
 * @property int|null $active_sts
 *
 * @property CmmsCorrectiveWorkOrderMaster $correctiveWorkOrderMaster
 * @property CmmsPreventiveWorkOrderMaster $preventiveWorkOrderMaster
 */
class RefAssignedPic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_assigned_pic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corrective_work_order_master_id', 'preventive_work_order_master_id', 'staff_id', 'active_sts'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['corrective_work_order_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsCorrectiveWorkOrderMaster::className(), 'targetAttribute' => ['corrective_work_order_master_id' => 'id']],
            [['preventive_work_order_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPreventiveWorkOrderMaster::className(), 'targetAttribute' => ['preventive_work_order_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'corrective_work_order_master_id' => 'Corrective Work Order Master ID',
            'preventive_work_order_master_id' => 'Preventive Work Order Master ID',
            'name' => 'Name',
            'staff_id' => 'Staff ID',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CorrectiveWorkOrderMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrectiveWorkOrderMaster()
    {
        return $this->hasOne(CmmsCorrectiveWorkOrderMaster::className(), ['id' => 'corrective_work_order_master_id']);
    }

    /**
     * Gets query for [[PreventiveWorkOrderMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPreventiveWorkOrderMaster()
    {
        return $this->hasOne(CmmsPreventiveWorkOrderMaster::className(), ['id' => 'preventive_work_order_master_id']);
    }
}
