<?php

namespace frontend\models\attendance;

use Yii;
use common\models\User;

/**
 * This is the model class for table "monthly_attendance".
 *
 * @property int $id
 * @property int $user_id
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
 *
 * @property User $user
 */
class MonthlyAttendance extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'monthly_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required', 'message' => 'Staff cannot be blank.'],
            [['avail_workday', 'perfect', 'total_days', 'total_present', 'workday_present', 'unpaid_leave_present', 'rest_holiday_present', 'absent', 'leave_taken', 'late_in', 'early_out', 'miss_punch', 'short', 'sche', 'workday', 'workday_ot', 'holiday', 'holiday_ot', 'restday', 'restday_ot', 'unpaid_leave', 'unpaid_leave_ot', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['user_id', 'month', 'year'], 'required'],
            [['user_id', 'month', 'year', 'created_by', 'updated_by'], 'integer'],
            [['avail_workday', 'perfect', 'total_days', 'total_present', 'workday_present', 'unpaid_leave_present', 'rest_holiday_present', 'absent', 'leave_taken', 'late_in', 'early_out', 'miss_punch', 'short', 'sche', 'workday', 'workday_ot', 'holiday', 'holiday_ot', 'restday', 'restday_ot', 'unpaid_leave', 'unpaid_leave_ot'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'month', 'year'], 'unique', 'targetAttribute' => ['user_id', 'month', 'year']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Staff',
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->id;
        }
        $absent = $this->absent;
        $leaveTaken = $this->leave_taken;
        $totalPresent = $this->total_present;
        $this->avail_workday = $absent + $leaveTaken + $totalPresent;

        return parent::beforeSave($insert);
    }

}
