<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_leave_annual_summary".
 *
 * @property int $user_id
 * @property string|null $user_fullname
 * @property string $username
 * @property int $year_of_leave
 * @property string|null $leave_type_code
 * @property string|null $leave_type_name
 * @property float|null $total_approved
 * @property float|null $total_pending
 */
class VLeaveAnnualSummary extends \yii\db\ActiveRecord {

    public $cur_entitle;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_leave_annual_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'year_of_leave'], 'integer'],
            [['username', 'year_of_leave'], 'required'],
            [['total_approved', 'total_pending'], 'number'],
            [['user_fullname', 'username', 'leave_type_name'], 'string', 'max' => 255],
            [['leave_type_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_id' => 'User ID',
            'user_fullname' => 'User Fullname',
            'username' => 'Username',
            'year_of_leave' => 'Year Of Leave',
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'total_approved' => 'Total Approved',
            'total_pending' => 'Total Pending',
        ];
    }

}
