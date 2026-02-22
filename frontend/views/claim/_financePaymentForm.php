<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\myTools\MyFormatter;
?>
<td>
    <?php
    if ($detail->is_deleted == 0) {
        $receiptIndex = "receipt_{$detail->id}";
        ?>

        <div class="decision-wrapper" data-index="<?= $receiptIndex ?>">
            <div class="d-flex justify-content-center ">
                <?= Html::activeHiddenInput($receiptWorklist, "[{$receiptIndex}]claim_status", ['value' => '']) ?>
                <?= Html::activeHiddenInput($receiptWorklist, "[{$receiptIndex}]claim_detail_id", ['value' => $detail->id]) ?>
                <?=
                Html::activeRadio($receiptWorklist, "[{$receiptIndex}]claim_status", [
                    'label' => false,
                    'value' => ClaimMaster::STATUS_REJECTED,
                    'uncheck' => null,
                    'id' => "reject-{$receiptIndex}",
                    'class' => 'decision-radio d-none',
                ])
                ?>

                <div class="card m-1 decision-card" data-type="reject" style="width: 100px; cursor: pointer;">
                    <div class="card-body text-center p-1">
                        <label for="reject-<?= $receiptIndex ?>" class="btn btn-outline-danger btn-sm w-100 mt-1 mb-0">Hold</label>
                    </div>
                </div>

                <?=
                Html::activeRadio($receiptWorklist, "[{$receiptIndex}]claim_status", [
                    'label' => false,
                    'value' => ClaimMaster::STATUS_APPROVED,
                    'uncheck' => null,
                    'id' => "approve-{$receiptIndex}",
                    'class' => 'decision-radio d-none',
                ])
                ?>

                <!-- Approve Card -->
                <div class="card m-1 decision-card" data-type="approve" style="width: 100px; cursor: pointer;">
                    <div class="card-body text-center p-1">
                        <label for="approve-<?= $receiptIndex ?>" class="btn btn-outline-success btn-sm w-100 mt-1 mb-0">Paid</label>
                    </div>
                </div>
            </div>
            <div class="error-container mt-2 w-100"></div>
        </div>
        <div class="d-flex align-items-center">
            <div class="file-input-container" id="file-input-container-<?= $receiptIndex ?>">
                <?=
                        $form->field($receiptWorklist, "[{$receiptIndex}]scannedFile", ['options' => ['class' => 'mb-0 me-2']])
                        ->fileInput([
                            'class' => 'form-control',
                            'accept' => '.png, .jpg, .jpeg, .pdf',
                            'id' => "file-input-$receiptIndex",
                            'style' => 'display: none;',
                        ])
                        ->label(false)
                ?>
                <small class="text-muted d-block mt-1"></small>
            </div>
        </div>
    <?php }else if ($detail->is_deleted == 1) { ?>
        <br><span class="text-danger">Deleted by <?= $detail->deletedBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($detail->deleted_at) ?></span>
    <?php } ?>
</td>

