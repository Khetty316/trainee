<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_leave_monthly_summary_group".
 *
 * @property int $user_id
 * @property string|null $user_fullname
 * @property string $username
 * @property int|null $leave_confirm_year
 * @property string|null $leave_confirm_month
 * @property string|null $leave_type_code
 * @property string|null $leave_type_name
 * @property float|null $days
 */
class VLeaveMonthlySummaryGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_leave_monthly_summary_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'leave_confirm_year'], 'integer'],
            [['days'], 'number'],
            [['user_fullname', 'username', 'leave_type_name'], 'string', 'max' => 255],
            [['leave_confirm_month'], 'string', 'max' => 2],
            [['leave_type_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_fullname' => 'User Fullname',
            'username' => 'Username',
            'leave_confirm_year' => 'Leave Confirm Year',
            'leave_confirm_month' => 'Leave Confirm Month',
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'days' => 'Days',
        ];
    }
}
