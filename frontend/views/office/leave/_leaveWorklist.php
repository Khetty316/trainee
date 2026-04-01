
<?php

use common\models\myTools\MyFormatter;
use yii\helpers\Html;

$leaveMaster = new frontend\models\office\leave\LeaveMaster();
?>
<div class="">
    <table class="table table-sm table-bordered w-100 table-striped">

        <?php
//        foreach ($model as $leave) {
        ?>

        <tr >
            <td>
                <?= MyFormatter::asDateTime_ReaddmYHi($leave->created_at) ?> :-<br/> - Leave request submission.
            </td>
        </tr>

        <?php
        if ($leave->compulsory_leave !== null) {
            $leaveCompulsoryDetail = \frontend\models\office\leave\LeaveCompulsoryDetail::findOne($leave->compulsory_leave);
            $leaveCompulsoryMaster = $leaveCompulsoryDetail->compulsoryMaster;
            ?>
            <tr>
                <td>
                    <?= MyFormatter::asDateTime_ReaddmYHi($leaveCompulsoryMaster->approved_at) ?> :-<br/>
                    - Respond by : <?= $leaveCompulsoryMaster->approvalBy->fullname ?> (Director)<br/>
                    - Status:  <?= $leaveCompulsoryMaster->status === \frontend\models\office\leave\RefLeaveStatus::STS_APPROVED ? "Approved" : "Rejected" ?>
                    <?php if ($leaveCompulsoryMaster->approval_remark) { ?>
                        <br/> - Remarks:<div class='text-wrap'><?= Html::encode($leaveCompulsoryMaster->approval_remark) ?></div>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>

        <?php
        if ($leave->rep_response_by) {
            ?>
            <tr>
                <td>
                    <?= MyFormatter::asDateTime_ReaddmYHi($leave['rep_response_at']) ?> :-<br/>
                    - Respond by : <?= $leave->rep_response_by ?> (Relief)<br/>
                    - Status:  <?= $leave->rep_response == 1 ? "Accepted" : "Decline" ?>
                    <?php if ($leave->rep_remarks) { ?>
                        <br/> - Remarks:<div class='text-wrap'><?= Html::encode($leave->rep_remarks) ?></div>

                    <?php } ?>
                </td>
            </tr>
            <?php
        } else if ($leave->relief_user_id && $leave->leave_status == $leaveMaster::STATUS_GetReliefApproval) {
            ?>
            <tr><td>Waiting for relief's (<?= $leave->relief ?>) consent...</td></tr>
            <?php
        }
        if ($leave->sup_response_by) {
            ?>
            <tr>
                <td>
                    <?= MyFormatter::asDateTime_ReaddmYHi($leave['sup_response_at']) ?> :-<br/>
                    - Respond by : <?= $leave->sup_response_by ?> (Superior)<br/>    
                    - Status:  <?= $leave->sup_response ? "Approve" : "Decline" ?>
                    <?php if ($leave->sup_remarks) { ?>
                        <br/>   - Remarks: <div class='text-wrap'><?= Html::encode($leave->sup_remarks) ?></div>
                    <?php } ?>
                </td>
            </tr>
            <?php
        } else if ($leave->superior_id && $leave->leave_status == $leaveMaster::STATUS_GetSuperiorApproval) {
            ?>
            <tr><td>Waiting for superior's (<?= $superior->fullname ?>) approval...</td></tr>
            <?php
        }
        if ($leave->hr_response_by) {
            ?>

            <tr>
                <td>
                    <?= MyFormatter::asDateTime_ReaddmYHi($leave['hr_response_at']) ?> :-<br/>
                    - Respond by : <?= $leave->hr_response_by ?> (HR)<br/>
                    - Status:  <?= $leave->hr_response ? "Approve" : "Decline" ?>
                    <?php if ($leave->hr_remarks) { ?>
                        <br/>     - Remarks:<div class='text-wrap'><?= Html::encode($leave->hr_remarks) ?></div>
                    <?php } ?>

                </td>
            </tr>
            <?php
        } else if ($leave->leave_status == $leaveMaster::STATUS_GetHrApproval) {
            ?>
            <tr><td>Waiting for HR's approval...</td></tr>
            <?php
        }

        if ($leave->leave_status == $leaveMaster::STATUS_Cancelled) {
            $leaveWorklist = \frontend\models\working\leavemgmt\LeaveWorklist::find()->where(['leave_id' => $leave->id, 'leave_status' => $leaveMaster::STATUS_Cancelled])->one();
            if ($leaveWorklist) {
                ?> 
                <tr>
                    <td>
                        <?= MyFormatter::asDateTime_ReaddmYHi($leaveWorklist->created_at) ?> :-<br/>
                        - Cancelled by : <?= $leaveWorklist->responsedBy->fullname ?><br/>
                        <?php if ($leaveWorklist->remarks) { ?>
                            - Remarks:<div class='text-wrap pl-3'><?= Html::encode($leaveWorklist->remarks) ?></div>
                        <?php } ?>
                    </td>
                </tr>
                <?php
            }
        }

        if ($leave->hr_recall) {
            ?> 
            <tr>
                <td>
                    <?= MyFormatter::asDateTime_ReaddmYHi($leave->hr_recall_at) ?> :-<br/>
                    - Recalled by : <?= $leave->hr_recall_by ?><br/>
                    <?php if ($leave->hr_recall_remarks) { ?>
                        - Remarks:<div class='text-wrap pl-3'><?= Html::encode($leave->hr_recall_remarks) ?></div>
                    <?php } ?>
                </td>
            </tr>
            <?php
        }
//        }
        ?>
    </table>
</div>