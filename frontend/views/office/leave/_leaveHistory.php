<?php

//$leaveHistory = new frontend\models\working\leavemgmt\VMasterLeaveBreakdown();
use \common\models\myTools\MyFormatter;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use frontend\models\office\leave\LeaveMaster;

$tableLGDisplay = "";
$tableSMDisplay = "";
foreach ($leaveHistory as $leave) {
    $start = '';
    $end = '';
    $totalDays = '';
    $status = '';

    if ($leave->break_id) {
        $start = MyFormatter::asDate_Read($leave->break_start_date);
        $end = MyFormatter::asDate_Read($leave->break_end_date);
//            $totalDays = $leave->break_total_days;
    } else {
        $start = MyFormatter::asDate_Read($leave->start_date);
        $end = MyFormatter::asDate_Read($leave->end_date);
//            $totalDays = $leave->total_days;
    }

    if ($leave->confirm_flag == 1) {
        $hasSth = false;
        if ($leave->days_annual > 0) {
            $totalDays .= $leave->days_annual . ($leave->days_annual > 1 ? " days" : " day") . " (Annual Leave)";
            $hasSth = true;
        }
        if ($leave->days_unpaid > 0) {
            $totalDays .= ($hasSth ? ' + ' : '') . ($leave->days_unpaid > 1 ? " days" : " day") . " (Unpaid Leave)";
            $hasSth = true;
        }
        if ($leave->days_sick > 0) {
            $totalDays .= ($hasSth ? ' + ' : '') . ($leave->days_sick > 1 ? " days" : " day") . " (Sick Leave)";
            $hasSth = true;
        }
        if ($leave->days_others > 0) {
            $totalDays .= ($hasSth ? ' + ' : '') . ($leave->days_others > 1 ? " days" : " day") . " (Others Leave)";
        }
        $status = "Approved";
    } else {
        if ($leave->leave_status == LeaveMaster::STATUS_GetSuperiorApproval) {
            $totalDays = $leave->total_days . ($leave->total_days > 1 ? " days" : " day");
            $status = "Getting Superior's approval..";
        } else if ($leave->leave_status == LeaveMaster::STATUS_GetHrApproval) {
            $totalDays = $leave->total_days . ($leave->total_days > 1 ? " days" : " day");
            $status = "Getting HR's approval..";
        } else if ($leave->leave_status == LeaveMaster::STATUS_GetReliefApproval) {
            $totalDays = $leave->total_days . ($leave->total_days > 1 ? " days" : " day");
            $status = "Getting Relief's consent..";
        } else if ($leave->leave_status == LeaveMaster::STATUS_Approved) {
            $totalDays = $leave->total_days . ($leave->total_days > 1 ? " days" : " day");
            $status = "Approved";
        } else if ($leave->leave_status == LeaveMaster::STATUS_Cancelled || $leave->leave_status == LeaveMaster::STATUS_Rejected) {
            $status = "$leave->leave_status_name";
        } else if ($leave->leave_status == LeaveMaster::STATUS_Recalled) {
            $status = "$leave->leave_remark";
        } else if ($leave->leave_status == LeaveMaster::STATUS_ReliefRejected) {
            $status = "$leave->leave_remark";
        }
    }

    if($leave->compulsory_leave !== null){
        $leave->reason = $leave->reason . ' </br><span class="text-info">(Compulsory Leave)</span>';
    }
    
    $reason = Html::a("<i class='fas fa-info-circle float-right p-1' ></i>", "javascript:",
                    [
                        'title' => "Reason",
                        "value" => "<p class='text-wrap'><b>Reason:</b> \n" . $leave->reason . "</p>",
                        "class" => "modalButton2 m-2"]);
    $doc = $leave->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg float-right mr-1 p-1' ></i>", "javascript:",
                    [
                        'title' => "Supporting Document",
                        "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($leave->support_doc)),
                        "class" => "modalButtonPdf m-2"]);

    $workList = Html::a($status, "javascript:", [
                'title' => "Audit Trail",
                "value" => yii\helpers\Url::to('/office/leave/show-leave-worklist?id=' . $leave->id),
                "class" => "modalButton m-2"]);

//    $hasWorked = \frontend\models\working\leavemgmt\LeaveWorklist::find()->where(['leave_id'=>$leave->id])->one();
//    $cancelLeaveBtn = Html::a('Cancel <i class="fas fa-times"></i>', "javascript:", [
//                'title' => "Cancel Leave",
//                "value" => yii\helpers\Url::to('/office/leave/show-leave-worklist?id=' . $leave->id),
//                "class" => "modalButton m-2"]);

    $cancelLeaveBtn = Html::a('Cancel <i class="fas fa-times fa-lg"></i>', "javascript:",
                    [
                        'class' => 'btn btn-danger btn-sm float-right',
                        'title' => "Cancel Leave",
                        'data-toggle' => 'modal',
                        'data-target' => '#workingModel',
                        'data-id' => $leave->id,
                    ]
    );

    if (!in_array($leave->leave_status, array(LeaveMaster::STATUS_Approved, LeaveMaster::STATUS_Cancelled, LeaveMaster::STATUS_Rejected, LeaveMaster::STATUS_Recalled, LeaveMaster::STATUS_ReliefRejected))) {
        $workList .= $cancelLeaveBtn; //Html::a(' Cancel <i class="fas fa-times"></i>', \yii\helpers\Url::to(['/office/leave/cancel-leave', 'id' => $leave->id]), ['class' => 'text-red', 'data-confirm' => 'Cancel Leave?', 'data-method' => 'post']);
    }

    $tableLGDisplay .= "<tr>"
            . yii\helpers\Html::tag('td', $leave->leave_code . " - " . $leave->leave_type_name . " " . $reason . " " . $doc)
            . yii\helpers\Html::tag('td', $start)
            . yii\helpers\Html::tag('td', $end)
            . yii\helpers\Html::tag('td', $totalDays)
            . yii\helpers\Html::tag('td', $workList)
            . "</tr>";

    $tableSMDisplay .= "<tr><td>"
            . yii\helpers\Html::tag('span', "<b>" . $leave->leave_code . " - " . $leave->leave_type_name . " " . $reason . " " . $doc . "</b><br/>")
            . yii\helpers\Html::tag('span', $start . " - ")
            . yii\helpers\Html::tag('span', $end . "<br/>")
            . yii\helpers\Html::tag('span', $totalDays . "<br/>") . "</td>"
            . yii\helpers\Html::tag('td', $workList)
            . "</tr>";
}
?>
<div class="d-none d-sm-block">
    <table class="table table-sm table-bordered w-100 table-striped">
        <tr style="background-color: #eeeeee" >
            <th class="text-center"><?= (isset($formType) && $formType === frontend\models\office\leave\RefLeaveType::codeTravel) ? "Requisition Form" : "Leave type" ?></th>
            <th class="text-center">From</th>
            <th class="text-center">To</th>
            <th class="text-center">Days</th>
            <th class="text-center">Status</th>
        </tr>
        <?= $tableLGDisplay ?>
    </table>
</div>
<div class="d-sm-none">
    <table class="table table-sm table-bordered w-100">
        <tr style="background-color: #eeeeee" >
            <th class="text-center">Leave</th>
            <th class="text-center">Status</th>
        </tr>
        <?= $tableSMDisplay ?>
    </table>
</div>





<?php
$form = ActiveForm::begin([
            'action' => '/office/leave/cancel-leave',
            'method' => 'post',
            'options' => ['autocomplete' => 'off']
        ]);
$modalFooter = yii\bootstrap4\Html::button('Close', ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary'])
        . yii\bootstrap4\Html::submitButton('Submit', ['class' => 'btn btn-success']);

Modal::begin([
    'id' => 'workingModel',
    'title' => 'Cancel Leave..',
    'centerVertical' => true,
    'footer' => $modalFooter
]);

echo '<div class="form-group">' . yii\bootstrap4\Html::hiddenInput('leaveId', '', ['id' => 'leaveId']) . '</div>';

echo '<div class="form-group">'
 . Html::label('Message:', 'remarks', ['class' => 'col-form-label'])
 . Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks'])
 . '</div>';
?>

<?php
Modal::end();
ActiveForm::end();
?>


<script>
    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#leaveId').val(button.data('id'));
        });
    });

</script>