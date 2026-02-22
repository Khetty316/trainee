<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\User;
use common\models\myTools\MyFormatter;
?>
<?php
if (isset($superiorWorklists[$detail->id]) && isset($superiorWorklists[$detail->id]->created_at)) {
    ?>
    <?php
    $receiptWorklist = $superiorWorklists[$detail->id];
    ?>
    <td>
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
            <?php if ($detail->is_deleted == 1) { ?>
                <br><span class="text-danger">Deleted by <?= $detail->deletedBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($detail->deleted_at) ?></span>
            <?php } ?>
        </div>
    </td>
    <?php
} else {
    if ($detail->is_deleted == 0 && $detail->claim_status == 0 && $model->claim_status == frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval) {
        ?>
        <td><span class="text-warning">Pending</span></td>
    <?php } else if ($detail->is_deleted == 1) {
        ?>
        <td><span class="text-danger">Deleted by <?= $detail->deletedBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($detail->deleted_at) ?></span></td>                                       
    <?php } else { ?>
        <td></td>
    <?php }
}
?>
