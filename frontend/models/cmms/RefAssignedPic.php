<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_assigned_pic".
 *
 * @property int $id
 * @property int|null $work_order_master_id
 * @property string|null $name
 * @property int|null $active_sts
 *
 * @property CmmsCorrectiveWorkOrderMaster $workOrderMaster
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
            [['work_order_master_id', 'active_sts', 'staff_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['work_order_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsCorrectiveWorkOrderMaster::className(), 'targetAttribute' => ['work_order_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_order_master_id' => 'Work Order Master ID',
            'name' => 'Name',
            'staff_id' => 'Staff ID',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[WorkOrderMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkOrderMaster()
    {
        return $this->hasOne(CmmsCorrectiveWorkOrderMaster::className(), ['id' => 'work_order_master_id']);
    }
}
