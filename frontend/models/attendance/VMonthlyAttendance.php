<?php

namespace frontend\models\attendance;

use Yii;

/**
 * This is the model class for table "v_monthly_attendance".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $staff_id
 * @property string|null $fullname
 * @property int $month
 * @property int $year
 * @property float|null $avail_workday
 * @property float|null $perfect
 * @property float|null $total_days
 * @property float|null $total_present
 * @property float|null $workday_present
 * @property float|null $unpaid_leave_present
 * @property float|null $rest_holiday_present
 * @property float|null $absent
 * @property float|null $leave_taken
 * @property float|null $late_in
 * @property float|null $early_out
 * @property float|null $miss_punch
 * @property float|null $short
 * @property float|null $sche
 * @property float|null $workday
 * @property float|null $workday_ot
 * @property float|null $holiday
 * @property float|null $holiday_ot
 * @property float|null $restday
 * @property float|null $restday_ot
 * @property float|null $unpaid_leave
 * @property float|null $unpaid_leave_ot
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class VMonthlyAttendance extends \yii\db\ActiveRecord {

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_monthly_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['staff_id', 'fullname', 'avail_workday', 'perfect', 'total_days', 'total_present', 'workday_present', 'unpaid_leave_present', 'rest_holiday_present', 'absent', 'leave_taken', 'late_in', 'early_out', 'miss_punch', 'short', 'sche', 'workday', 'workday_ot', 'holiday', 'holiday_ot', 'restday', 'restday_ot', 'unpaid_leave', 'unpaid_leave_ot', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['id'], 'default', 'value' => 0],
            [['id', 'user_id', 'month', 'year', 'created_by', 'updated_by'], 'integer'],
            [['user_id', 'month', 'year'], 'required'],
            [['avail_workday', 'perfect', 'total_days', 'total_present', 'workday_present', 'unpaid_leave_present', 'rest_holiday_present', 'absent', 'leave_taken', 'late_in', 'early_out', 'miss_punch', 'short', 'sche', 'workday', 'workday_ot', 'holiday', 'holiday_ot', 'restday', 'restday_ot', 'unpaid_leave', 'unpaid_leave_ot'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['staff_id'], 'string', 'max' => 10],
            [['fullname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'staff_id' => 'Staff ID',
            'fullname' => 'Fullname',
            'month' => 'Month',
            'year' => 'Year',
            'avail_workday' => 'Avail Workday',
            'perfect' => 'Perfect',
            'total_days' => 'Total Days',
            'total_present' => 'Total Present',
            'workday_present' => 'Workday Present',
            'unpaid_leave_present' => 'Unpaid Leave Present',
            'rest_holiday_present' => 'Rest Holiday Present',
            'absent' => 'Absent',
            'leave_taken' => 'Leave Taken',
            'late_in' => 'Late In',
            'early_out' => 'Early Out',
            'miss_punch' => 'Miss Punch',
            'short' => 'Short',
            'sche' => 'Sche',
            'workday' => 'Workday',
            'workday_ot' => 'Workday Ot',
            'holiday' => 'Holiday',
            'holiday_ot' => 'Holiday Ot',
            'restday' => 'Restday',
            'restday_ot' => 'Restday Ot',
            'unpaid_leave' => 'Unpaid Leave',
            'unpaid_leave_ot' => 'Unpaid Leave Ot',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

}
