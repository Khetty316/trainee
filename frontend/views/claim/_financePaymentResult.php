<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\User;
use common\models\myTools\MyFormatter;
?>
<?php
if (isset($financePaymentWorklists[$detail->id]) && isset($financePaymentWorklists[$detail->id]->created_at)) {
    $receiptWorklist = $financePaymentWorklists[$detail->id];
    ?>
    <td>                                                
        <div class="decision-result">
            <?php if ($receiptWorklist->claim_status == ClaimMaster::STATUS_APPROVED && $detail->is_paid == ClaimMaster::STATUS_PAID) { ?>
                <!-- Approved Display -->
                <span class="text-success">Paid</span><br>
                <?php
                $responder = User::findOne($receiptWorklist->responsed_by);
                if ($responder):
                    ?>
                    by <?= Html::encode($responder->fullname) ?>
                <?php endif; ?>
                @ <?= MyFormatter::asDateTime_ReaddmYHi($receiptWorklist->created_at) ?>
                <div class="existing-file-controls ms-2">
                    <?php
//                    =
//                    Html::a(
//                            "<i class='far fa-file-alt fa-lg'></i>",
//                            ["/office/claim/get-file", 'filename' => urlencode($detail->payment_proof_file)],
//                            [
//                                'title' => "View Proof",
//                                'target' => "_blank",
//                                'data-pjax' => "0",
//                                'class' => 'btn btn-sm btn-outline-info me-1'
//                            ]
//                    )
                    ?>
                    <?=
                    Html::a(
                            "<i class='far fa-file-alt fa-lg'></i>",
                            "#",
                            [
                                'title' => "View Proof",
                                'value' => "/office/claim/get-file?filename=" . urlencode($detail->payment_proof_file),
                                'class' => "btn btn-sm btn-outline-info me-1 docModal"
                            ]
                    );
                    ?>
                    <?=
                    $this->render('/_docModal')
                    ?>  
                </div>
            <?php } else if ($receiptWorklist->claim_status != ClaimMaster::STATUS_APPROVED && $detail->is_paid == ClaimMaster::STATUS_HOLD_PAYMENT) { ?>
                <span class="text-warning"><?= $model->claimStatus->status_name ?></span>                                        
            <?php } ?>
        </div>
    </td>
    <?php
} else {
    if ($detail->is_deleted == 0 && $detail->claim_status == 0 && $model->claim_status == frontend\models\RefGeneralStatus::STATUS_WaitingForPayment) {
        ?>
        <td><span class="text-warning"><?= $model->claimStatus->status_name ?></span></td>
    <?php } else if ($detail->is_deleted == 1) { ?>
        <td><br><span class="text-danger">Deleted by <?= $detail->deletedBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($detail->deleted_at) ?></span></td>
    <?php } else { ?>
        <td></td>
    <?php
    }
}
?>




