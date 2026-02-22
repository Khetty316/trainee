<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_leave_entitlement".
 *
 * @property string|null $staff_id
 * @property int|null $entitle_id
 * @property int $user_id
 * @property int $staff_active_status
 * @property string|null $fullname
 * @property float|null $annual_bring_forward_days
 * @property float|null $annual_bring_next_year_days
 * @property string $leave_type_code
 * @property string|null $leave_type_name
 * @property int|null $is_pro_rata
 * @property int|null $year
 * @property int|null $month_start
 * @property int|null $month_end
 * @property int|null $entitle_detail_id
 * @property float|null $days
 */
class VLeaveEntitlement extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_leave_entitlement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['entitle_id', 'user_id', 'staff_active_status', 'is_pro_rata', 'year', 'month_start', 'month_end', 'entitle_detail_id'], 'integer'],
            [['annual_bring_forward_days', 'annual_bring_next_year_days', 'days'], 'number'],
            [['leave_type_code'], 'required'],
            [['staff_id', 'leave_type_code'], 'string', 'max' => 10],
            [['fullname', 'leave_type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'staff_id' => 'Staff ID',
            'entitle_id' => 'Entitle ID',
            'user_id' => 'User ID',
            'staff_active_status'=>'Staff Active Status',
            'fullname' => 'Fullname',
            'annual_bring_forward_days' => 'Annual Bring Forward Days',
            'annual_bring_next_year_days' => 'Annual Bring Next Year Days',
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'is_pro_rata' => 'Is Pro Rata',
            'year' => 'Year',
            'month_start' => 'Month Start',
            'month_end' => 'Month End',
            'entitle_detail_id' => 'Entitle Detail ID',
            'days' => 'Days',
        ];
    }

}
