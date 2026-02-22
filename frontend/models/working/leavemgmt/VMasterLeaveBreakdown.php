<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_master_leave_breakdown".
 *
 * @property int|null $break_id
 * @property int $id
 * @property int $requestor_id
 * @property string|null $requestor
 * @property string $requestor_email
 * @property string|null $leave_type_code
 * @property string|null $leave_type_name
 * @property int|null $superior_id
 * @property int|null $relief_user_id
 * @property string|null $relief
 * @property string|null $superior
 * @property string|null $superior_email
 * @property string $reason
 * @property string $start_date
 * @property int $start_section
 * @property string|null $start_sec_name
 * @property string $end_date
 * @property int $end_section
 * @property string|null $end_sec_name
 * @property float|null $total_days
 * @property int|null $leave_status
 * @property string|null $leave_status_name
 * @property string|null $leave_remark
 * @property string|null $support_doc
 * @property string $created_at
 * @property int|null $sup_response
 * @property string|null $sup_response_by
 * @property string|null $sup_response_at
 * @property string|null $sup_remarks
 * @property int|null $hr_response
 * @property string|null $hr_response_by
 * @property string|null $hr_response_at
 * @property string|null $hr_remarks
 * @property int|null $rep_response
 * @property string|null $rep_response_by
 * @property string|null $rep_response_at
 * @property string|null $rep_remarks
 * @property string|null $break_start_date
 * @property int|null $break_start_section
 * @property string|null $break_start_section_name
 * @property string|null $break_end_date
 * @property int|null $break_end_section
 * @property string|null $break_end_section_name
 * @property float|null $break_total_days
 * @property float|null $days_annual
 * @property float|null $days_unpaid
 * @property float|null $days_sick
 * @property float|null $days_others
 * @property string|null $leave_confirm_month
 * @property int|null $leave_confirm_year
 * @property int|null $confirm_flag
 * @property int|null $is_recorded
 */
class VMasterLeaveBreakdown extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_master_leave_breakdown';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['break_id', 'id', 'requestor_id', 'superior_id', 'relief_user_id', 'start_section', 'end_section', 'leave_status', 'sup_response', 'hr_response', 'rep_response', 'break_start_section', 'break_end_section', 'leave_confirm_year', 'confirm_flag', 'is_recorded'], 'integer'],
            [['requestor_id', 'requestor_email', 'reason', 'start_date', 'start_section', 'end_date', 'end_section'], 'required'],
            [['reason', 'leave_remark', 'sup_remarks', 'hr_remarks', 'rep_remarks'], 'string'],
            [['start_date', 'end_date', 'created_at', 'sup_response_at', 'hr_response_at', 'rep_response_at', 'break_start_date', 'break_end_date'], 'safe'],
            [['total_days', 'break_total_days', 'days_annual', 'days_unpaid', 'days_sick', 'days_others'], 'number'],
            [['requestor', 'requestor_email', 'leave_type_name', 'relief', 'superior', 'superior_email', 'support_doc', 'sup_response_by', 'hr_response_by', 'rep_response_by'], 'string', 'max' => 255],
            [['leave_type_code'], 'string', 'max' => 10],
            [['start_sec_name', 'end_sec_name', 'leave_status_name', 'break_start_section_name', 'break_end_section_name'], 'string', 'max' => 100],
            [['leave_confirm_month'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'break_id' => 'Break ID',
            'id' => 'ID',
            'requestor_id' => 'Requestor ID',
            'requestor' => 'Requestor',
            'requestor_email' => 'Requestor Email',
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'superior_id' => 'Superior ID',
            'relief_user_id' => 'Relief User ID',
            'relief' => 'Relief',
            'superior' => 'Superior',
            'superior_email' => 'Superior Email',
            'reason' => 'Reason',
            'start_date' => 'Start Date',
            'start_section' => 'Start Section',
            'start_sec_name' => 'Start Sec Name',
            'end_date' => 'End Date',
            'end_section' => 'End Section',
            'end_sec_name' => 'End Sec Name',
            'total_days' => 'Total Days',
            'leave_status' => 'Leave Status',
            'leave_status_name' => 'Leave Status Name',
            'leave_remark' => 'Leave Remark',
            'support_doc' => 'Support Doc',
            'created_at' => 'Created At',
            'sup_response' => 'Sup Response',
            'sup_response_by' => 'Sup Response By',
            'sup_response_at' => 'Sup Response At',
            'sup_remarks' => 'Sup Remarks',
            'hr_response' => 'Hr Response',
            'hr_response_by' => 'Hr Response By',
            'hr_response_at' => 'Hr Response At',
            'hr_remarks' => 'Hr Remarks',
            'rep_response' => 'Rep Response',
            'rep_response_by' => 'Rep Response By',
            'rep_response_at' => 'Rep Response At',
            'rep_remarks' => 'Rep Remarks',
            'break_start_date' => 'Break Start Date',
            'break_start_section' => 'Break Start Section',
            'break_start_section_name' => 'Break Start Section Name',
            'break_end_date' => 'Break End Date',
            'break_end_section' => 'Break End Section',
            'break_end_section_name' => 'Break End Section Name',
            'break_total_days' => 'Break Total Days',
            'days_annual' => 'Days Annual',
            'days_unpaid' => 'Days Unpaid',
            'days_sick' => 'Days Sick',
            'days_others' => 'Days Others',
            'leave_confirm_month' => 'Leave Confirm Month',
            'leave_confirm_year' => 'Leave Confirm Year',
            'confirm_flag' => 'Confirm Flag',
            'is_recorded' => 'Is Recorded',
        ];
    }

}
