<?php

//$leaveHistory = new frontend\models\working\leavemgmt\VMasterLeaveBreakdown();
use \common\models\myTools\MyFormatter;
use yii\helpers\Html;
use frontend\models\office\leave\LeaveMaster;

$tableLGDisplay = "";
$tableSMDisplay = "";
foreach ($reliefHistory as $leave) {
    $totalDays = '';
    $status = '';
    $start = MyFormatter::asDate_Read($leave->start_date);
    $end = MyFormatter::asDate_Read($leave->end_date);

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
    }

    $tableLGDisplay .= "<tr>"
            . Html::tag('td', $leave->requestor)
            . Html::tag('td', $start)
            . Html::tag('td', $end)
            . Html::tag('td', $totalDays)
            . "</tr>";

    $tableSMDisplay .= "<tr><td>"
            . Html::tag('span', "<b>" . $leave->requestor . "</b><br/>")
            . Html::tag('span', $start . " - ")
            . Html::tag('span', $end . "<br/>")
            . Html::tag('span', $totalDays . "<br/>") . "</td>"
            . "</tr>";
}
?>
<div class="d-none d-sm-block">
    <table class="table table-sm table-bordered w-100 table-striped">
        <tr style="background-color: #eeeeee" >
            <th class="text-center">Relief For</th>
            <th class="text-center">From</th>
            <th class="text-center">To</th>
            <th class="text-center">Days</th>
        </tr>
        <?= $tableLGDisplay ?>
    </table>
</div>
<div class="d-sm-none">
    <table class="table table-sm table-bordered w-100">
        <tr style="background-color: #eeeeee" >
            <th class="text-center">Relief For</th>
        </tr>
        <?= $tableSMDisplay ?>
    </table>
</div>
