<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\User;
use common\models\myTools\MyFormatter;
?>
<?php if (!empty($financeApprovalWorklists)) { ?>
    <?php $firstItem = reset($financeApprovalWorklists) ?>
    <?php if ($firstItem->created_at) { ?>
        <?=
        $this->render('_financeApprovalResult', [
            'model' => $model,
            'detail' => $detail,
            'financeApprovalWorklists' => $financeApprovalWorklists,
        ])
        ?>                                    
    <?php } else { ?>
        <td></td>
    <?php } ?>
<?php } else { ?>
    <td></td>
<?php } ?>

<?php
$receiptWorklist = $superiorWorklists[$detail->id];
if (isset($superiorWorklists[$detail->id]) && isset($superiorWorklists[$detail->id]->created_at)) {
    ?>
    <td>
        <?php
        $receiptWorklist = $superiorWorklists[$detail->id];
        ?>
        <div class="decision-result">
            <?php if ($receiptWorklist->claim_status == ClaimMaster::STATUS_APPROVED): ?>
                <span class="text-success">Approved</span><br>
                <?php
                $responder = User::findOne($receiptWorklist->responsed_by);
                if ($responder):
                    ?>
                    by <?= Html::encode($responder->fullname) ?>
                <?php endif; ?>
                @ <?= MyFormatter::asDateTime_ReaddmYHi($receiptWorklist->created_at) ?>

            <?php elseif ($receiptWorklist->claim_status == ClaimMaster::STATUS_REJECTED): ?>
                <span class="text-danger">Rejected</span><br>
                <?php
                $responder = User::findOne($receiptWorklist->responsed_by);
                if ($responder):
                    ?>
                    by <?= Html::encode($responder->fullname) ?>
                <?php endif; ?>
                @ <?= MyFormatter::asDateTime_ReaddmYHi($receiptWorklist->created_at) ?>
                <br>
                <small class="text-danger">
                    <strong>Reject Reason:</strong><br>
                    <?= Html::encode($receiptWorklist->remark) ?>
                </small>
            <?php endif; ?>
            <?php
            if ($detail->is_deleted == 1) {
                $deletedBy = ($detail->deleted_by === null ? $model->deletedBy->fullname : $detail->deletedBy->fullname);
                $deletedAt = ($detail->deleted_at === null ? $model->deleted_at : $detail->deleted_at);
                ?>
                <br><span class="text-danger">Deleted by <?= $deletedBy ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($deletedAt) ?></span>
            <?php } ?>
        </div>
    </td>
    <?php
} else {
    if ($detail->claim_status == 0 && $model->claim_status == frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval) {
        ?>
        <?=
        $this->render('_superiorFinanceApprovalForm', [
            'detail' => $detail,
            'receiptWorklist' => $receiptWorklist,
            'form' => $form,
            'model' => $model,
        ])
        ?>  
    <?php } else { ?>
        <td></td> 
        <?php
    }
    ?>  
<?php } ?>

<?php if (!empty($financePaymentWorklists)) { ?>
    <?php $firstItem = reset($financePaymentWorklists) ?>
    <?php if ($firstItem->created_at) { ?>
        <?=
        $this->render('_financePaymentResult', [
            'model' => $model,
            'detail' => $detail,
            'financePaymentWorklists' => $financePaymentWorklists,
        ])
        ?>
    <?php } else { ?>
        <td></td>
    <?php } ?>
<?php } else { ?>
    <td></td>
<?php } ?>
