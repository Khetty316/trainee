<?php

use yii\helpers\Html;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;
?>
<?php
$form = ActiveForm::begin([
    'action' => ['director-approval-replenishment-request', 'id' => $model->id],
    'method' => 'post',
        ]);
?>    

<?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_GetDirectorApproval) { ?>
    <td class="text-right">
        <?=
                $form->field($model, "amount_approved")
                ->input('number', [
                    'class' => 'form-control text-right requested_amount',
                    'step' => 'any',
                    'min' => '0.01',
                    'value' => $model->amount_requested,
                    'data-requested' => $model->amount_requested,
                    'required' => true,
                ])
                ->label(false)
        ?>
    </td>
    <td>
        <div class="decision-wrapper" data-index="">
            <div class="d-flex justify-content-center ">
                <?=
                Html::activeRadio($model, "director_responsed_status", [
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
                Html::activeRadio($model, "director_responsed_status", [
                    'label' => false,
                    'value' => PettyCashRequestMaster::STATUS_APPROVED,
                    'uncheck' => null,
                    'id' => "approve",
                    'class' => 'decision-radio d-none',
                ])
                ?>

                <div class="card m-1 decision-card" data-type="approve" style="width: 100px; cursor: pointer;">
                    <div class="card-body text-center p-1">
                        <?php $greenBtn = "Approve"; ?>
                        <label for="approve" class="btn btn-outline-success btn-sm w-100 mt-1 mb-0"><?= $greenBtn ?></label>
                    </div>
                </div>
            </div>

            <div class="error-container mt-2 w-100"></div>

            <?=
            $form->field($model, "director_responsed_remark", [
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
    </td>
    <td></td>
    <?php // } else if ($model->status == frontend\models\RefGeneralStatus::STATUS_WaitingForCashRelease || $model->status == frontend\models\RefGeneralStatus::STATUS_DirectorRejected) { ?>
<?php } else { ?>
    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->amount_approved) ?></td>
    <td>
        <?php if ($model->director_responsed_status == PettyCashRequestMaster::STATUS_APPROVED && $model->director_responsed_by !== null): ?>
            <span class="text-success">Approved</span><br>
            <?php
            $responder = User::findOne($model->director_responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($model->director_responsed_at) ?>

        <?php elseif ($model->status == PettyCashRequestMaster::STATUS_REJECTED && $model->director_responsed_by !== null): ?>
            <span class="text-danger">Rejected</span><br>
            <?php
            $responder = User::findOne($model->director_responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($model->director_responsed_at) ?>
            <br>
            <small class="text-danger">
                <strong>Reject Reason:</strong><br>
                <?= Html::encode($model->director_responsed_remark) ?>
            </small>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_WaitingForCashRelease) { ?>
            <?= $model->finance_responsed_status == 0 ? '<span class="text-warning">Pending</span>' : '<span class="text-success">Completed (Replenished) </span><br>' ?><?php
            $responder = User::findOne($model->finance_responsed_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($model->finance_responsed_at) ?>
            <?php endif; ?>
        <?php } else if ($model->status == frontend\models\RefGeneralStatus::STATUS_Completed) { ?>
            <?php if ($model->finance_responsed_status == PettyCashRequestMaster::STATUS_APPROVED && $model->finance_responsed_by !== null) { ?>
                <span class="text-success">Replenishment Completed</span><br>
                <?php
                $responder = User::findOne($model->finance_responsed_by);
                if ($responder):
                    ?>
                    by <?= Html::encode($responder->fullname) ?>
                <?php endif; ?>
                @ <?= MyFormatter::asDateTime_ReaddmYHi($model->finance_responsed_at && $model->finance_responsed_by !== null) ?>

            <?php } ?>
            <?php
        }
        ?>
    </td>
<?php } ?>

<?php ActiveForm::end(); ?>
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
                        .removeAttr('min') // Remove min attribute
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
                if (requested && amountApprovedInput.val() == 0)
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
