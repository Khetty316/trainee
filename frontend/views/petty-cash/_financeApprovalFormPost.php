<?php

use yii\helpers\Html;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

$form = ActiveForm::begin([
    'id' => 'finance-verify-form',
    'action' => ['finance-verify-receipt', 'id' => $postForm->id],
    'method' => 'post',
    'options' => ['class' => 'ajax-form-submit']
        ]);

$amount_requested_approved = $preForm->amount_approved;
?>    

<?php
$approvedAmount = $postForm->receipt_amount ?? 0;
$difference = $approvedAmount - $preForm->amount_approved;
if ($model->status == frontend\models\RefGeneralStatus::STATUS_WaitingForReceiptVerification) {
    ?>
    <td class="text-right">
        <?= Html::encode(MyFormatter::asDecimal2($approvedAmount)) ?>
        <?php
        if ($difference == 0) {
            echo "<span class='text-muted'>&nbsp;(Exact amount)</span>";
        } elseif ($difference > 0) {
            echo "<span class='text-danger'>&nbsp;(" . number_format(abs($difference), 2) . " shortage)</span>";
        } else {
            echo "<span class='text-success'>&nbsp;(" . number_format(abs($difference), 2) . " exceeded)</span>";
        }
        ?>
        <?= Html::activeHiddenInput($postForm, 'amount_approved', ['value' => $approvedAmount]) ?>
    </td>
    <td>
        <?php
        if ($model->finance_id == Yii::$app->user->identity->id) {
            ?>
            <div class="decision-wrapper" data-index="">
                <div class="d-flex justify-content-center">
                    <?=
                    Html::activeRadio($postForm, "status", [
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
                    Html::activeRadio($postForm, "status", [
                        'label' => false,
                        'value' => PettyCashRequestMaster::STATUS_APPROVED,
                        'uncheck' => null,
                        'id' => "approve",
                        'class' => 'decision-radio d-none',
                    ])
                    ?>

                    <div class="card m-1 decision-card" data-type="approve" style="cursor: pointer;">
                        <div class="card-body text-center p-1">
                            <?php $greenBtn = "Verify & Mark as Complete"; ?>
                            <label for="approve" class="btn btn-outline-success btn-sm w-100 mt-1 mb-0"><?= $greenBtn ?></label>
                        </div>
                    </div>
                </div>

                <div class="error-container mt-2 w-100"></div>

                <?=
                $form->field($postForm, "responsed_remark", [
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
                <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right mt-2 mb-2']) ?>
            </div>

        <?php } else { ?>
            <span class="text-warning">Pending verification</span><br>
            <?php
            $responder = User::findOne($model->finance_id);
            if ($responder):
                ?>
                from <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
        <?php }
        ?>
    </td>
<?php } else { ?>
    <td class="text-right">
        <?= Html::encode(MyFormatter::asDecimal2($postForm->amount_approved)) ?>
        <?php
        if ($difference == 0) {
            echo "<span class='text-muted'>&nbsp;(Exact amount)</span>";
        } elseif ($difference > 0) {
            echo "<span class='text-danger'>&nbsp;(-" . number_format(abs($difference), 2) . " shortage)</span>";
        } else {
            echo "<span class='text-success'>&nbsp;(+" . number_format($difference, 2) . " exceed)</span>";
        }
        ?>
    </td>

    <td>
        <?php if ($postForm->status == PettyCashRequestMaster::STATUS_APPROVED): ?>
            <span class="text-success">Verified</span><br>
            <?php
            $responder = User::findOne($postForm->responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($postForm->responsed_at) ?>

        <?php elseif ($postForm->status == PettyCashRequestMaster::STATUS_REJECTED): ?>
            <span class="text-danger">Rejected</span><br>
            <?php
            $responder = User::findOne($postForm->responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($postForm->responsed_at) ?>
            <br>
            <small class="text-danger">
                <strong>Reject Reason:</strong><br>
                <?= Html::encode($postForm->responsed_remark) ?>
            </small>
        <?php endif; ?>
    </td>
<?php } ?>

<?php ActiveForm::end(); ?>

<!-- Modal for Ledger Credit Form -->
<?php
Modal::begin([
    'id' => 'ledger-credit-modal',
    'size' => 'modal-lg',
    'title' => '<h4>Complete Receipt & Update Ledger</h4>',
    'closeButton' => [
        'label' => '×',
        'class' => 'close',
    ],
]);
echo '<div id="modal-content"><div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div></div>';
Modal::end();
?>

<script>
    $(document).ready(function () {
        let isSubmitting = false;
        let isLedgerFormSubmitted = false;

        window.openLedgerCreditModal = function (masterId, postFormId) {
            isLedgerFormSubmitted = false;

            $('#modal-content').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...</div>');

            $('#ledger-credit-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#ledger-credit-modal').modal('show');

            $.ajax({
                url: '<?= Url::to(['finance-confirm-receipt-completed']) ?>',
                type: 'GET',
                data: {
                    id: masterId,
                    postFormId: postFormId  // Pass postFormId
                },
                success: function (data) {
                    $('#modal-content').html(data);
                },
                error: function () {
                    $('#modal-content').html('<div class="alert alert-danger">Failed to load form.</div>');
                }
            });
        };

        // Handle close button - warn user that verification will not be saved
        $(document).on('click', '#ledger-credit-modal .close', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!isLedgerFormSubmitted) {
                if (confirm('WARNING: Closing this window will discard the verification. The receipt will NOT be marked as verified. Are you sure?')) {
                    $('#ledger-credit-modal').modal('hide');
                    // ✅ Just redirect, don't save anything
                    window.location.href = '<?= Url::to(['finance-approval-pending']) ?>';
                }
            } else {
                $('#ledger-credit-modal').modal('hide');
            }
        });

        // Handle ledger form submission
        $(document).on('submit', '#ledger-credit-form', function (e) {
            e.preventDefault();

            if (isSubmitting)
                return false;

            isSubmitting = true;
            isLedgerFormSubmitted = true;

            var submitBtn = $(this).find('button[type=submit]');
            var originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#ledger-credit-modal').modal('hide');

                        if (response.message) {
                            alert(response.message);
                        }

                        setTimeout(function () {
                            window.location.href = response.redirect || '<?= Url::to(['finance-approval-pending']) ?>';
                        }, 500);
                    } else {
                        // On failure, allow retry
                        alert(response.message || 'An error occurred');
                        submitBtn.prop('disabled', false).html(originalText);
                        isSubmitting = false;
                        isLedgerFormSubmitted = false;
                    }
                },
                error: function () {
                    alert('An error occurred while processing.');
                    submitBtn.prop('disabled', false).html(originalText);
                    isSubmitting = false;
                    isLedgerFormSubmitted = false;
                }
            });

            return false;
        });

        // Decision wrapper code
        $('.decision-wrapper').each(function () {
            const wrapper = $(this);
            const rejectCard = wrapper.find('[data-type="reject"]');
            const approveCard = wrapper.find('[data-type="approve"]');
            const rejectTextarea = wrapper.find('.reject-remark');
            const cards = wrapper.find('.decision-card');
            const amountApprovedInput = wrapper.closest('tr').find('.requested_amount');

            rejectCard.on('click', function () {
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');
                rejectCard.addClass('bg-danger text-white border');
                rejectCard.find('.btn').addClass('text-white fw-bold');
                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_REJECTED ?>"]').prop('checked', true);
                rejectTextarea.show().attr('required', true);
                amountApprovedInput.val('0.00').removeAttr('required').removeAttr('min').prop('required', false);
            });

            approveCard.on('click', function () {
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');
                approveCard.addClass('bg-success text-white border');
                approveCard.find('.btn').addClass('text-white fw-bold');
                wrapper.find('[value="<?= PettyCashRequestMaster::STATUS_APPROVED ?>"]').prop('checked', true);
                rejectTextarea.hide().val('').removeAttr('required');
                amountApprovedInput.attr('required', true).attr('min', '0.01').prop('required', true);
                const requested = amountApprovedInput.attr('data-requested');
                if (requested)
                    amountApprovedInput.val(requested);
            });
        });

        const originalApproved = parseFloat('<?= $amount_requested_approved ?>') || 0;
        $('.requested_amount').on('input', function () {
            const current = parseFloat($(this).val()) || 0;
            const difference = (current - originalApproved).toFixed(2);
            const diffCell = $(this).closest('tr').find('.difference-cell span');
            if (difference == 0) {
                diffCell.text('Exact amount').removeClass('text-danger text-success').addClass('text-muted');
            } else if (difference > 0) {
                diffCell.html(`<span class="text-success">+RM ${difference} (Company owes staff)</span>`);
            } else {
                diffCell.html(`<span class="text-danger">RM ${Math.abs(difference)} (Staff to return)</span>`);
            }
        });
        $('.requested_amount').trigger('input');

        // Main form submission
        $('#finance-verify-form').on('submit', function (e) {
            e.preventDefault();

            if (isSubmitting)
                return false;

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
                return false;

            isSubmitting = true;
            var submitBtn = $(this).find('button[type=submit]');
            var originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        if (response.openModal) {
                            isSubmitting = false;
                            submitBtn.prop('disabled', false).html(originalText);
                            // Pass postFormId to modal
                            window.openLedgerCreditModal(response.masterId, response.postFormId);
                        } else if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        alert(response.message || 'An error occurred');
                        submitBtn.prop('disabled', false).html(originalText);
                        isSubmitting = false;
                    }
                },
                error: function (xhr) {
                    alert('An error occurred while processing your request');
                    submitBtn.prop('disabled', false).html(originalText);
                    isSubmitting = false;
                }
            });

            return false;
        });

        window.addEventListener('beforeunload', function (e) {
            if (isSubmitting && !isLedgerFormSubmitted) {
                e.preventDefault();
                e.returnValue = 'Processing is in progress. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    });
</script>