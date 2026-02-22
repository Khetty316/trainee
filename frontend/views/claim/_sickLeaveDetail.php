<?php

use yii\helpers\Html;
use frontend\models\office\leave\LeaveMaster;

if ($leaveRecord->leave_status == LeaveMaster::STATUS_GetSuperiorApproval) {
    $status = "Getting Superior's approval..";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_GetHrApproval) {
    $status = "Getting HR's approval..";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_GetReliefApproval) {
    $status = "Getting Relief's consent..";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_Approved) {
    $status = "Approved";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_Cancelled || $leave->leave_status == LeaveMaster::STATUS_Rejected) {
    $status = "$leaveRecord->leave_status_name";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_Recalled) {
    $status = "$leaveRecord->leave_remark";
} else if ($leaveRecord->leave_status == LeaveMaster::STATUS_ReliefRejected) {
    $status = "$leaveRecord->leave_remark";
}
?>
<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0">Sick Leave Details</h6>
    </div>
    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Sick Leave Code</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Total Days</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" width="10%"><?= $leaveRecord->leave_code ?></td>
                    <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->start_date))) ?></td>
                    <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->end_date))) ?></td>
                    <td width="30%">
                        <span class="text-left"><?= Html::encode($leaveRecord->reason) ?></span>

                        <?php if (isset($leaveRecord->support_doc) && !empty($leaveRecord->support_doc)): ?>
                            <?php
//                            = Html::a(
//                                "<i class='far fa-file-alt fa-lg float-right'></i>",
//                                ["/working/leavemgmt/get-file", 'filename' => urlencode($leaveRecord->support_doc)],
//                                [
//                                    'title' => "Supporting Document",
//                                    'target' => "_blank",
//                                    'data-pjax' => "0",
//                                ]
//                            ) 
                            ?>
                            <?=
                            Html::a(
                                    "<i class='far fa-file-alt fa-lg float-right'></i>",
                                    "#",
                                    [
                                        'title' => "Supporting Document",
                                        'value' => "/working/leavemgmt/get-file?filename=" . urlencode($leaveRecord->support_doc),
                                        'class' => "docModal"
                                    ]
                            );
                            ?>
                            <?=
                            $this->render('/_docModal')
                            ?>  
                        <?php endif; ?>
                    </td>
                    <td class="text-center" width="10%"><?= Html::encode($leaveRecord->total_days) ?></td>
                    <td>
                        <?= $status ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
