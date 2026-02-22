<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\User;
use common\models\myTools\MyFormatter;
?>          
<!--finance's response-->
<?php
$financeApprovalWorklist = $financeApprovalWorklists[$detail->id];
if (!empty($financeApprovalWorklists)) {
    ?>
    <?php
    if (isset($financeApprovalWorklists[$detail->id]) && isset($financeApprovalWorklists[$detail->id]->created_at)) {
        ?>
        <td>
            <div class="decision-result">
                <?php if ($financeApprovalWorklist->claim_status == ClaimMaster::STATUS_APPROVED && $detail->claim_status == ClaimMaster::STATUS_APPROVED): ?>
                    <span class="text-success">Verified</span><br>
                    <?php
                    $responder = User::findOne($financeApprovalWorklist->responsed_by);
                    if ($responder):
                        ?>
                        by <?= Html::encode($responder->fullname) ?>
                    <?php endif; ?>
                    @ <?= MyFormatter::asDateTime_ReaddmYHi($financeApprovalWorklist->created_at) ?>

                <?php elseif ($financeApprovalWorklist->claim_status == ClaimMaster::STATUS_REJECTED && $detail->claim_status == ClaimMaster::STATUS_REJECTED): ?>
                    <span class="text-danger">Rejected</span><br>
                    <?php
                    $responder = User::findOne($financeApprovalWorklist->responsed_by);
                    if ($responder):
                        ?>
                        by <?= Html::encode($responder->fullname) ?>
                    <?php endif; ?>
                    @ <?= MyFormatter::asDateTime_ReaddmYHi($financeApprovalWorklist->created_at) ?>
                    <br>
                    <small class="text-danger">
                        <strong>Reject Reason:</strong><br>
                        <?= Html::encode($financeApprovalWorklist->remark) ?>
                    </small>
                <?php endif; ?>
                <?php
                if ($detail->is_deleted == 1) {
                    $deletedBy = ($detail->deleted_by === null ? $model->deletedBy->fullname : $detail->deletedBy->fullname);
                    $deletedAt = ($detail->deleted_at === null ? $model->deleted_at : $detail->deleted_at);
                    ?>
                    <br><span class="text-danger">Deleted <?= $deletedBy ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($deletedAt) ?></span>
        <?php } ?>
            </div>
        </td>                               
        <?php
    } else {
        if ($detail->claim_status == 0 && $model->claim_status == frontend\models\RefGeneralStatus::STATUS_GetFinanceApproval && $model->claimant_id != Yii::$app->user->id) {
            ?>
            <?=
            $this->render('_superiorFinanceApprovalForm', [
                'detail' => $detail,
                'receiptWorklist' => $financeApprovalWorklist,
                'form' => $form,
                'model' => $model,
            ])
            ?>  
        <?php } else { ?>
            <td></td> 
            <?php
        }
    }
}
?>

<!--superiors response-->
<?php if (!empty($superiorWorklists)) { ?>
    <?php $firstItem = reset($superiorWorklists) ?>
    <?php if ($firstItem->created_at) { ?>
        <?=
        $this->render('_superiorApprovalResult', [
            'model' => $model,
            'detail' => $detail,
            'superiorWorklists' => $superiorWorklists,
        ])
        ?>                                    
    <?php } else { ?>
        <td></td>
    <?php } ?>
<?php } else { ?>
    <td></td>
<?php } ?>

<!--payment finance's response-->
<?php
$financePaymentWorklist = $financePaymentWorklists[$detail->id];
if (!empty($financePaymentWorklists)) {
    ?>
    <?php
    if (isset($financePaymentWorklists[$detail->id]) && isset($financePaymentWorklists[$detail->id]->created_at)) {
        ?>
        <?php if ($detail->is_paid == ClaimMaster::STATUS_PAID && $financePaymentWorklist->claim_status == ClaimMaster::STATUS_APPROVED): ?>
            <td>
                <div class="decision-result">
                    <span class="text-success">Paid</span><br>
                    <?php
                    $responder = User::findOne($financePaymentWorklist->responsed_by);
                    if ($responder):
                        ?>
                        by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
                    @ <?= MyFormatter::asDateTime_ReaddmYHi($financeApprovalWorklist->created_at) ?>
                    <div class="existing-file-controls ms-2">
                        <?=
                        Html::a(
                                "<i class='far fa-file-alt fa-lg'></i>",
                                ["/office/claim/get-file", 'filename' => urlencode($detail->payment_proof_file)],
                                [
                                    'title' => "View Proof",
                                    'target' => "_blank",
                                    'data-pjax' => "0",
                                    'class' => 'btn btn-sm btn-outline-info me-1'
                                ]
                        )
                        ?>
                    </div>
                </div>
            </td>    
        <?php elseif ($detail->is_paid == ClaimMaster::STATUS_HOLD_PAYMENT && $financePaymentWorklist->claim_status == ClaimMaster::STATUS_REJECTED): ?>
            <?=
            $this->render('_financePaymentForm', [
                'detail' => $detail,
                'receiptWorklist' => $financePaymentWorklist,
                'form' => $form,
            ])
            ?>  
        <?php endif; ?>
        <?php
    } else {
        if ($detail->claim_status == 0 && $model->claim_status == frontend\models\RefGeneralStatus::STATUS_WaitingForPayment) {
            ?>
            <?=
            $this->render('_financePaymentForm', [
                'detail' => $detail,
                'receiptWorklist' => $financePaymentWorklist,
                'form' => $form,
            ])
            ?>   
        <?php
        } else if ($detail->is_deleted == 1) {
            $deletedBy = ($detail->deleted_by === null ? $model->deletedBy->fullname : $detail->deletedBy->fullname);
            $deletedAt = ($detail->deleted_at === null ? $model->deleted_at : $detail->deleted_at);
            ?>
            <td><span class="text-danger">Deleted <?= $deletedBy ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($deletedAt) ?></span></td>                                       
        <?php } else { ?>
            <td></td>
            <?php
        }
    }
}
?>
