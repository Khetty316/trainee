<?php

namespace frontend\models\office\leave;

use \common\models\myTools\MyFormatter;
use Yii;

/**
 * This is the model class for table "v_master_leave".
 *
 * @property int $id
 * @property int $requestor_id
 * @property string|null $requestor
 * @property string $requestor_email
 * @property string|null $leave_type_code
 * @property string|null $leave_type_name
 * @property int|null $relief_user_id
 * @property string|null $relief
 * @property int|null $superior_id
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
 * @property int|null $hr_recall
 * @property string|null $hr_recall_by
 * @property string|null $hr_recall_at
 * @property string|null $hr_recall_remarks
 * @property int|null $rep_response
 * @property string|null $rep_response_by
 * @property string|null $rep_response_at
 * @property string|null $rep_remarks
 */
class VMasterLeave extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_master_leave';
    }

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'requestor_id', 'relief_user_id', 'superior_id', 'start_section', 'end_section', 'leave_status', 'sup_response', 'hr_response', 'hr_recall', 'rep_response'], 'integer'],
            [['requestor_id', 'requestor_email', 'reason', 'start_date', 'start_section', 'end_date', 'end_section'], 'required'],
            [['reason', 'leave_remark', 'sup_remarks', 'hr_remarks', 'hr_recall_remarks', 'rep_remarks'], 'string'],
            [['start_date', 'end_date', 'created_at', 'sup_response_at', 'hr_response_at', 'hr_recall_at', 'rep_response_at'], 'safe'],
            [['total_days'], 'number'],
            [['requestor', 'requestor_email', 'leave_type_name', 'relief', 'superior', 'superior_email', 'support_doc', 'sup_response_by', 'hr_response_by', 'hr_recall_by', 'rep_response_by'], 'string', 'max' => 255],
            [['leave_type_code'], 'string', 'max' => 10],
            [['start_sec_name', 'end_sec_name', 'leave_status_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requestor_id' => 'Requestor ID',
            'requestor' => 'Requestor',
            'requestor_email' => 'Requestor Email',
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'relief_user_id' => 'Relief User ID',
            'relief' => 'Relief',
            'superior_id' => 'Superior ID',
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
            'hr_recall' => 'Hr Recall',
            'hr_recall_by' => 'Hr Recall By',
            'hr_recall_at' => 'Hr Recall At',
            'hr_recall_remarks' => 'Hr Recall Remarks',
            'rep_response' => 'Rep Response',
            'rep_response_by' => 'Rep Response By',
            'rep_response_at' => 'Rep Response At',
            'rep_remarks' => 'Rep Remarks',
        ];
    }

    public function getLeaveForCalendar_Json() {
        $leaveList = VMasterLeave::find()->where('leave_status=4')->all();
        $returnList = [];
        foreach ($leaveList as $leave) {
//            $title = $leave->requestor . " (" . $leave->leave_type_name . ") " . MyFormatter::asDate_Read_dm($leave->start_date) . " " . $leave->start_sec_name . " - " . MyFormatter::asDate_Read_dm($leave->end_date) . " " . $leave->end_sec_name;
            $title = $leave->requestor . " (" . $leave->leave_type_name . ") " . MyFormatter::asDate_Read_dm($leave->start_date) . " - " . MyFormatter::asDate_Read_dm($leave->end_date);
            $color = '';
            switch ($leave->leave_type_code) {
                case 4:
                    $color = 'green';
                    break;
                case 2:
                    $color = 'red';
                    break;
                case 5:
                    $color = '#ffbbbb';
                    break;
                default :
                    $color = "#0056b3";
                    break;
            }

            // Debug on 07/02/2021 - Date to displayed wrongly in calendar
            if ($leave->end_date != $leave->start_date) {
                $leave->end_date = date('Y-m-d', strtotime($leave->end_date . ' + 1 days'));
            }

            $tempArr = array(
                "title" => "$title",
                "allDay" => "true", // always set to all day
                "start" => "$leave->start_date",
                "end" => "$leave->end_date",
                'color' => $color
            );

            array_push($returnList, $tempArr);
        }
        return json_encode($returnList);
    }
}
