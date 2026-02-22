<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\office\claim\ClaimMaster;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\claim\RefClaimType;
use common\models\myTools\MyFormatter;

$this->title = $model->claim_code;
$this->params['breadcrumbs'][] = ['label' => 'Claim Approval - Superior', 'url' => ['superior-approval-pending']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    .deleted-row {
        text-decoration: line-through;
        opacity: 0.6;
        color: #6c757d;
    }
</style>
<div class="claim-master-view">
    <?php
    $form = ActiveForm::begin();
    ?>
    <div class="row mb-1">
        <div class="col-md-8">
            <h5><?= Html::encode($this->title) ?></h5>
            <p class="text-muted">
                Submitted by <?= Html::encode($model->claimant->fullname) ?> 
                on <?= MyFormatter::asDateTime_ReaddmYHi($model->created_at) ?>
            </p>
        </div>
    </div>

    <div class="alert alert-info border-left-primary mb-4">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <strong class="text-muted">Claim Type:</strong>
                        <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($model->claimType->claim_name) ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Superior:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?php
                            $superiorName = ($model->superior_id !== null ? $model->superior->fullname : '-');
                            ?>
                            <?= $superiorName ?>
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Status:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?php
                            if ($model->is_deleted == 1) {
                                $status = ($model->claimStatus->status_name . ' by ' . $model->deletedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->deleted_at) . ' Remark: ' . $model->delete_remark);
                            } else {
                                $status = ($model->claimStatus->status_name);
                            }
                            ?>
                            <?= $status ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Travel Allowance Section (if applicable) -->
    <?php
    $leaveRecord = LeaveMaster::findOne(['leave_code' => $model->ref_code]);
    if ($travelAllowance) {
        ?>
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Work Traveling Requisition Details</h6>
            </div>
            <div class="card-body p-2 table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">WTR Code</th>
                            <th class="text-center">Start Date</th>
                            <th class="text-center">End Date</th>
                            <th>Reason</th>
                            <th>Total Days</th>
                            <th>Location</th>
                            <th class="text-right">Allowance Per Day (RM)</th>
                            <th class="text-right">Total Allowance (RM)</th>
                            <th width="20%">Finance's Response</th>
                            <th width="20%">Superior's Response</th> 
                            <th width="20%">Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" width="10%"><?= $leaveRecord->leave_code ?></td>
                            <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->start_date))) ?></td>
                            <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->end_date))) ?></td>
                            <td width="23%">
                                <span class="text-left"><?= Html::encode($leaveRecord->reason) ?></span>

                                <?php if (isset($leaveRecord->support_doc) && !empty($leaveRecord->support_doc)): ?>
                                    <?=
                                    Html::a("<i class='far fa-file-alt fa-lg float-right'></i>", "#",
                                            [
                                                'title' => "Supporting Document",
                                                "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($leaveRecord->support_doc)),
                                                "class" => "modalButtonPdf"]);
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= Html::encode($leaveRecord->total_days) ?></td>
                            <td>
                                <?php
                                $travelLocation = \frontend\models\RefTravelLocation::findOne(['code' => $travelAllowance->travel_location_code]);
                                echo $travelLocation->name;
                                ?>
                            </td>
                            <td class="text-right">
                                <?= \common\models\myTools\MyFormatter::asDecimal2($travelAllowancePerDay) ?>
                            </td>
                            <td class="text-right">
                                <?= \common\models\myTools\MyFormatter::asDecimal2($travelAllowance->amount_to_be_paid) ?>
                            </td>
                            <?=
                            $this->render('_superiorResponse', [
                                'model' => $model,
                                'detail' => $travelAllowance,
                                'superiorWorklists' => $superiorWorklists,
                                'financeApprovalWorklists' => $financeApprovalWorklists,
                                'financePaymentWorklists' => $financePaymentWorklists,
                                'form' => $form
                            ])
                            ?> 
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right font-weight-bold">Total Allowance To Be Paid (RM):</th>
                            <td class="text-right">
                                <?php
                                $totalToBePaid = 0;
                                $isDeleted = ($travelAllowance->is_deleted == 1);
                                $isRejected = ($travelAllowance->claim_status == 1);
                                $totalToBePaid += ((!$isDeleted && !$isRejected) ? $travelAllowance->amount_to_be_paid : 0.00);
                                ?>
                                <?= \common\models\myTools\MyFormatter::asDecimal2($totalToBePaid) ?>
                            </td>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Message container for user feedback -->
                <div id="allowance-message" class="mt-2" style="display: none;">
                    <div class="alert" role="alert">
                        <span id="allowance-message-text"></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } else if ($model->claim_type === RefClaimType::codeMedical && $model->ref_code_sts == 1) { ?>
        <?=
        $this->render('_sickLeaveDetail', [
            'leaveRecord' => $leaveRecord
        ])
        ?>
        <?php
    } else if ($model->claim_type === RefClaimType::codeMaterial || $model->claim_type === RefClaimType::codeRepair) {
        $prfMaster = \frontend\models\office\preReqForm\PrereqFormMaster::findOne(['prf_no' => $model->ref_code]);
        if ($prfMaster !== null) {
            ?>
            <?=
            $this->render('_prfDetail', [
                'prfMaster' => $prfMaster
            ])
            ?>
            <?php
        }
    } else if ($model->claim_type === RefClaimType::codeProdOTMeal) {
        $record = frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::findOne(['ref_code' => $model->ref_code]);
        if ($record !== null) {
            ?>
            <?=
            $this->render('_prodotmealDetail', [
                'record' => $record
            ])
            ?>
            <?php
        }
    }
    ?>
    <!-- Receipt Details Section -->
    <div class="card mt-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Receipt Detail</h6>
            <span class="badge badge-secondary"><?= count($claimDetail) ?> item(s)</span>
        </div>
        <div class="card-body">
            <?php if (empty($claimDetail)): ?>
                <div class="text-center p-4">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No receipt details found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="3%">#</th>
                                <th width="5%">Receipt</th>
                                <th width="5%">Date</th>
                                <th width="10%">Description</th>
                                <th width="10%" class="text-right">Receipt Amount (RM)</th>
                                <th width="10%" class="text-right">To be Paid (RM)</th>
                                <th width="20%">Finance's Response</th>
                                <th width="20%">Superior's Response</th>
                                <th width="20%">Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            $totalToBePaid = 0;
                            ?>
                            <?php foreach ($claimDetail as $index => $detail): ?>
                                <?php
                                $isDeleted = ($detail->is_deleted == 1);
                                $isRejected = ($detail->claim_status == 1);
                                $totalAmount += $detail->receipt_amount;
                                $totalToBePaid += ((!$isDeleted && !$isRejected) ? $detail->amount_to_be_paid : 0.00);
                                ?>
                                <tr <?= $isDeleted ? 'class="deleted-row"' : '' ?>>                                 
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?php if ($detail->receipt_file && !$isDeleted): ?>
                                            <?=
                                            Html::a("<i class='far fa-file-alt fa-lg'></i>", "#",
                                                    [
                                                        'title' => "View Current Receipt",
                                                        "value" => ("/office/claim/get-file?filename=" . urlencode($detail->receipt_file)),
                                                        "class" => "docModal"]);
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?=
                                        $detail->receipt_date ?
                                                Yii::$app->formatter->asDate($detail->receipt_date, 'php:d/m/Y') :
                                                '<span class="text-muted">-</span>'
                                        ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="<?= Html::encode($detail->detail) ?>">
                                            <?= Html::encode($detail->detail ?: 'No description') ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <strong><?= \common\models\myTools\MyFormatter::asDecimal2($detail->receipt_amount) ?></strong>
                                    </td>
                                    <td class="text-right">
                                        <strong class="text-success"><?= \common\models\myTools\MyFormatter::asDecimal2($detail->amount_to_be_paid) ?></strong>
                                    </td>
                                    <?=
                                    $this->render('_superiorResponse', [
                                        'model' => $model,
                                        'detail' => $detail,
                                        'superiorWorklists' => $superiorWorklists,
                                        'financeApprovalWorklists' => $financeApprovalWorklists,
                                        'financePaymentWorklists' => $financePaymentWorklists,
                                        'form' => $form
                                    ])
                                    ?> 
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Totals (RM):</th>
                                <th class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($totalAmount) ?></th>
                                <th class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($totalToBePaid) ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group mt-3 text-right">
        <?php
        if ($model->claim_status === \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval) {
            echo Html::submitButton('Save', ['class' => 'btn btn-success px-3']);
        }
        ?>
    </div>
    <?=
    $this->render('/_docModal')
    ?>  
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('.decision-wrapper').each(function () {
            const wrapper = $(this);
            const index = wrapper.data('index');

            // Scope all selectors to this specific wrapper
            const radios = wrapper.find('.decision-radio');
            const cards = wrapper.find('.decision-card');
            const rejectCard = wrapper.find('[data-type="reject"]');
            const approveCard = wrapper.find('[data-type="approve"]');
            const rejectTextarea = $(`[name="ClaimApprovalWorklist[${index}][remark]"]`);

            // Click on card triggers radio selection (scoped to this wrapper)
            rejectCard.on('click', function () {
                wrapper.find(`#reject-${index}`).prop('checked', true).trigger('change');
            });

            approveCard.on('click', function () {
                wrapper.find(`#approve-${index}`).prop('checked', true).trigger('change');
            });

            // Handle radio change (scoped to this wrapper only)
            radios.on('change', function () {
                const value = $(this).val();
                wrapper.removeClass('has-error');
                wrapper.find('.error-container').empty();

                // Reset styles for THIS wrapper only
                cards.removeClass('bg-danger bg-success text-white border');
                cards.find('.btn').removeClass('text-white fw-bold');

                if (value === '<?= ClaimMaster::STATUS_REJECTED ?>') {
                    rejectCard.addClass('bg-danger text-white border');
                    rejectCard.find('.btn').addClass('text-white fw-bold');
                    rejectTextarea.show().attr('required', true);
                } else if (value === '<?= ClaimMaster::STATUS_APPROVED ?>') {
                    approveCard.addClass('bg-success text-white border');
                    approveCard.find('.btn').addClass('text-white fw-bold');
                    rejectTextarea.hide().val('').removeAttr('required');
                }
            });
        });

        // Validation before submit
        $('form').off('submit').on('submit', function (e) {
            let valid = true;

            // Clear previous error states
            $('.decision-wrapper .error-container').empty();
            $('.decision-wrapper').removeClass('has-error');

            $('.decision-wrapper').each(function () {
                const wrapper = $(this);
                const index = wrapper.data('index');
                const selectedRadio = wrapper.find('.decision-radio:checked');
                const remark = $(`[name="ClaimApprovalWorklist[${index}][remark]"]`).val();
                const errorContainer = wrapper.find('.error-container');

                if (selectedRadio.length === 0) {
                    wrapper.addClass('has-error');
                    errorContainer.html('<div class="text-danger small">Please select Approve or Reject</div>');
                    valid = false;
                } else if (selectedRadio.val() === '<?= ClaimMaster::STATUS_REJECTED ?>' && !remark.trim()) {
                    errorContainer.html('<div class="text-danger small">Please provide a reject remark</div>');
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>

<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 768px) {
        .card-body .row .col-md-3,
        .card-body .row .col-md-6 {
            margin-bottom: 1rem;
        }

        .btn-group .btn {
            margin-bottom: 0.25rem;
        }
    }
</style>
