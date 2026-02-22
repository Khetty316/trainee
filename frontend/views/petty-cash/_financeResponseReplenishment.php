<?php

use yii\helpers\Html;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;
?>
<?php
//$form = ActiveForm::begin([
//    'action' => ['finance-confirm-replenishment-completed', 'id' => $model->id],
//    'method' => 'post',
//        ]);
?>    
<td>
    <?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_WaitingForCashRelease) { ?>
        <div class="decision-wrapper" data-index="">
            <div class="d-flex justify-content-center ">
                <?=
                Html::activeRadio($model, "finance_responsed_status", [
                    'label' => false,
                    'value' => PettyCashRequestMaster::STATUS_APPROVED,
                    'uncheck' => null,
                    'id' => "approve",
                    'class' => 'decision-radio d-none',
                ])
                ?>
                <?=
                Html::a("Confirm Cash Release",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['finance-confirm-replenishment-completed', 'id' => $model->id]),
                            "class" => "modalButtonMedium btn btn-outline-success",
                            'data-modaltitle' => "Ledger Debit Detail"
                        ]
                )
                ?>
            </div>

        </div>
    <?php } else if ($model->status == frontend\models\RefGeneralStatus::STATUS_Completed) { ?>
        <?php if ($model->finance_responsed_status == PettyCashRequestMaster::STATUS_APPROVED   && $model->finance_responsed_by !== null): ?>
            <span class="text-success">Replenishment Completed</span><br>
            <?php
            $responder = User::findOne($model->finance_responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($model->finance_responsed_at) ?>

        <?php endif; ?>

    <?php } ?>
</td>
<?php // ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $('.decision-wrapper').each(function () {
            const wrapper = $(this);
            const rejectCard = wrapper.find('[data-type="reject"]');
            const approveCard = wrapper.find('[data-type="approve"]');
            const rejectTextarea = wrapper.find('.reject-remark');
            const cards = wrapper.find('.decision-card');
            const amountApprovedInput = wrapper.closest('tr').find('.requested_amount'); // find the amount_approved input in same row

            rejectCard.on('click', function () {
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');

                rejectCard.addClass('bg-danger text-white border');
                rejectCard.find('.btn').addClass('text-white fw-bold');

                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_REJECTED ?>"]').prop('checked', true);
                rejectTextarea.show().attr('required', true);

                // ✅ Remove required AND min validation
                amountApprovedInput.val('0.00')
                        .removeAttr('required')
                        .removeAttr('min')           // Remove min attribute
                        .prop('required', false);
            });

            approveCard.on('click', function () {
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');

                approveCard.addClass('bg-success text-white border');
                approveCard.find('.btn').addClass('text-white fw-bold');

                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_APPROVED ?>"]').prop('checked', true);
                rejectTextarea.hide().val('').removeAttr('required');

                // ✅ Restore both required and min attributes
                amountApprovedInput.attr('required', true)
                        .attr('min', '0.01')        // Restore min validation
                        .prop('required', true);

                const requested = amountApprovedInput.attr('data-requested');
                if (requested)
                    amountApprovedInput.val(requested);
            });
        });

        $('form').on('submit', function (e) {
            let valid = true;

            $('.decision-wrapper').each(function () {
                const wrapper = $(this);
                const selected = wrapper.find('.decision-radio:checked');
                const remark = wrapper.find('.reject-remark').val();
                const errorContainer = wrapper.find('.error-container');

                errorContainer.empty();

                if (selected.length === 0) {
                    errorContainer.html('<div class="text-danger small">Please select Approve or Reject</div>');
                    valid = false;
                } else if (selected.val() === '<?= PettyCashRequestMaster::STATUS_REJECTED ?>' && !remark.trim()) {
                    errorContainer.html('<div class="text-danger small">Please provide a reject remark</div>');
                    valid = false;
                }
            });

            if (!valid)
                e.preventDefault();
        });
    });
</script>
