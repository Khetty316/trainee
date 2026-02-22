<?php

use yii\helpers\Html;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;

$ledgerMaster = \frontend\models\office\pettyCash\PettyCashLedgerMaster::findOne(['created_by' => Yii::$app->user->identity->id]) ?? null;
?>
<?php
$form = ActiveForm::begin([
    'action' => ['finance-verify-pre-form', 'id' => $preForm->id],
    'method' => 'post',
    'id' => 'verify-pre-form-' . $preForm->id,
        ]);
?>    

<?php
if ($model->status == frontend\models\RefGeneralStatus::STATUS_GetFinanceApproval) {
    if ($model->created_by != Yii::$app->user->identity->id) {
        ?>
        <td class="text-right">
            <?=
                    $form->field($preForm, "amount_approved")
                    ->input('number', [
                        'class' => 'form-control text-right requested_amount',
                        'step' => 'any',
                        'min' => '0.01',
                        'value' => $preForm->amount_requested,
                        'data-requested' => $preForm->amount_requested,
                        'required' => true,
                    ])
                    ->label(false)
            ?>
            Your current balance (RM): <span class="text-success"> <?= \common\models\myTools\MyFormatter::asDecimal2($ledgerMaster->amount ?? 0.00) ?></span>
            <div class="ledger-container mt-2 w-100"></div>
        </td>
        <td>
            <div class="decision-wrapper" data-index="" data-form-id="verify-pre-form-<?= $preForm->id ?>">
                <div class="d-flex justify-content-center ">
                    <?=
                    Html::activeRadio($preForm, "status", [
                        'label' => false,
                        'value' => PettyCashRequestMaster::STATUS_REJECTED,
                        'uncheck' => null,
                        'class' => 'decision-radio d-none',
                    ])
                    ?>

                    <div class="card m-1 decision-card" data-type="reject" style="width: 100px; cursor: pointer;">
                        <div class="card-body text-center p-1">
                            <label for="reject" class="btn btn-outline-danger btn-sm w-100 mt-1 mb-0">Reject</label>
                        </div>
                    </div>

                    <?=
                    Html::activeRadio($preForm, "status", [
                        'label' => false,
                        'value' => PettyCashRequestMaster::STATUS_APPROVED,
                        'uncheck' => null,
                        'id' => "approve",
                        'class' => 'decision-radio d-none',
                    ])
                    ?>

                    <div class="card m-1 decision-card" data-type="approve" style="cursor: pointer;">
                        <div class="card-body text-center p-1">
                            <?php $greenBtn = "Verify & Cash Release"; ?>
                            <label for="approve" class="btn btn-outline-success btn-sm w-100 mt-1 mb-0"><?= $greenBtn ?></label>
                        </div>
                    </div>
                </div>

                <div class="error-container mt-2 w-100"></div>

                <?=
                $form->field($preForm, "responsed_remark", [
                    'template' => "{input}\n{error}",
                    'options' => ['tag' => false],
                ])->textarea([
                    'class' => 'form-control reject-remark mt-2',
                    'placeholder' => 'Enter reject remark',
                    'style' => 'display: none;',
                ])
                ?>
            </div>
            <hr>
            <div class="form-group">
                <?= Html::button('Save', ['class' => 'btn btn-success float-right mt-2 mb-2 save-btn', 'data-form-id' => 'verify-pre-form-' . $preForm->id]) ?>
            </div>
        </td>
    <?php } else { ?>
        <td></td>
        <td><span class="text-warning">Pending verification</span><br>
            <?php
            $responder = User::findOne($model->finance_id);
            if ($responder):
                ?>
                from <?= Html::encode($responder->fullname) ?>
            <?php endif; ?></td>
        <?php
    }
} else {
    ?>
    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($preForm->amount_approved) ?></td>
    <td>
        <?php if ($preForm->status == PettyCashRequestMaster::STATUS_APPROVED): ?>
            <span class="text-success">Verified</span><br>
            <?php
            $responder = User::findOne($preForm->responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($preForm->responsed_at) ?>

        <?php elseif ($preForm->status == PettyCashRequestMaster::STATUS_REJECTED): ?>
            <span class="text-danger">Rejected</span><br>
            <?php
            $responder = User::findOne($preForm->responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($preForm->responsed_at) ?>
            <br>
            <small class="text-danger">
                <strong>Reject Reason:</strong><br>
                <?= Html::encode($preForm->responsed_remark) ?>
            </small>
        <?php endif; ?>
    </td>
<?php } ?>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        // Reset all radios on load
        $('.decision-radio').prop('checked', false);

        $('.decision-wrapper').each(function () {
            const wrapper = $(this);
            const rejectCard = wrapper.find('[data-type="reject"]');
            const approveCard = wrapper.find('[data-type="approve"]');
            const rejectTextarea = wrapper.find('.reject-remark');
            const cards = wrapper.find('.decision-card');
            const amountApprovedInput = wrapper.closest('tr').find('.requested_amount');
            const errorContainer = wrapper.find('.error-container');
            const ledgerContainer = $('.ledger-container');

            rejectCard.on('click', function () {
                errorContainer.empty();
                ledgerContainer.empty();
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');

                rejectCard.addClass('bg-danger text-white border');
                rejectCard.find('.btn').addClass('text-white fw-bold');

                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_REJECTED ?>"]').prop('checked', true);
                rejectTextarea.show().attr('required', true);

                amountApprovedInput.val('0.00')
                        .removeAttr('required')
                        .removeAttr('min')
                        .prop('required', false);
            });

            approveCard.on('click', function () {
                errorContainer.empty();
                ledgerContainer.empty();
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');

                approveCard.addClass('bg-success text-white border');
                approveCard.find('.btn').addClass('text-white fw-bold');

                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_APPROVED ?>"]').prop('checked', true);
                rejectTextarea.hide().val('').removeAttr('required');

                amountApprovedInput.attr('required', true)
                        .attr('min', '0.01')
                        .prop('required', true);

                const requested = amountApprovedInput.attr('data-requested');
                if (requested && amountApprovedInput.val() == 0)
                    amountApprovedInput.val(requested);
            });
        });

        // Save button click handler
        $('.save-btn').on('click', function () {
            const formId = $(this).data('form-id');
            const form = $('#' + formId);
            let valid = true;
            const ledgerAmount = parseFloat("<?= $ledgerMaster->amount ?? 0.00 ?>") || 0;

            $('.decision-wrapper').each(function () {
                const wrapper = $(this);
                const selected = wrapper.find('.decision-radio:checked');
                const remark = wrapper.find('.reject-remark').val();
                const errorContainer = wrapper.find('.error-container');
                const ledgerContainer = $('.ledger-container');
                const approvedAmount = parseFloat(wrapper.closest('tr').find('.requested_amount').val() || 0);

                // Clear previous messages
                errorContainer.empty();
                ledgerContainer.empty();

                // Must choose Reject or Verify
                if (selected.length === 0) {
                    errorContainer.html('<div class="text-danger text-center small">Please select Reject or Verify</div>');
                    valid = false;
                    return false; // break .each
                }

                // Reject but no remark
                if (selected.val() === '<?= PettyCashRequestMaster::STATUS_REJECTED ?>' && !remark.trim()) {
                    errorContainer.html('<div class="text-danger text-center small">Please provide a reject remark</div>');
                    valid = false;
                    return false; // break .each
                }

                // Verify but not enough balance
                if (selected.val() === '<?= PettyCashRequestMaster::STATUS_APPROVED ?>' && approvedAmount > ledgerAmount) {
                    ledgerContainer.html('<div class="text-danger small text-center">Approved amount cannot exceed your current balance.<br><strong>Please replenish your cash.</strong></div>');
                    valid = false;
                    return false; // break .each
                }
            });

            // Submit only if valid
            if (valid && form.length > 0) {
                console.log('Submitting form...');
                form[0].submit();
            } else {
                console.log('Form not submitted due to validation error.');
            }
        });
    });
</script>

