<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use common\models\User;
/**
 * This is the model class for table "production_elec_tasks_error_staff".
 *
 * @property int $id
 * @property int|null $production_elec_tasks_error_id
 * @property int|null $staff_id
 * @property int|null $is_read 0 = no, 1 = yes
 * @property string|null $read_at
 *
 * @property User $staff
 */
class ProductionElecTasksErrorStaff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_elec_tasks_error_staff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['production_elec_tasks_error_id', 'staff_id', 'is_read'], 'integer'],
            [['read_at'], 'safe'],
            [['staff_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['staff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'production_elec_tasks_error_id' => 'Production Elec Tasks Error ID',
            'staff_id' => 'Staff ID',
            'is_read' => 'Is Read',
            'read_at' => 'Read At',
        ];
    }

    /**
     * Gets query for [[Staff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(User::className(), ['id' => 'staff_id']);
    }
}
