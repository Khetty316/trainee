<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\claim\RefClaimType;
use common\models\myTools\MyFormatter;

$this->title = $model->claim_code;
$this->params['breadcrumbs'][] = ['label' => 'My Claims - Personal', 'url' => ['personal-claim-pending']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
//$disable = ($model->status_flag == 1);
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
        <div class="col-md-4 text-right">
            <?php
            if ($model->is_deleted == 0) {
                echo Html::a('Update', ['claimant-update-claim', 'id' => $model->id], ['class' => 'btn btn-primary mr-1']);
            }
            ?>

            <?php
            if ($model->has_payment == 0 && $model->is_deleted == 0) {
                echo Html::a("Cancel",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['ajax-claimant-cancel-claim', 'id' => $model->id]),
                            "class" => "modalButtonMedium btn btn-danger mx-1",
                            'data-modaltitle' => "Cancel Claim"
                        ]
                );
            }
            ?>
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
                            <th>Superior's Response</th>
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
                            $this->render('_financeApprovalResult', [
                                'model' => $model,
                                'detail' => $travelAllowance,
                                'financeApprovalWorklists' => $financeApprovalWorklists,
                            ])
                            ?>                                    
                            <?=
                            $this->render('_superiorApprovalResult', [
                                'model' => $model,
                                'detail' => $travelAllowance,
                                'superiorWorklists' => $superiorWorklists,
                            ])
                            ?>
                            <?=
                            $this->render('_financePaymentResult', [
                                'model' => $model,
                                'detail' => $travelAllowance,
                                'financePaymentWorklists' => $financePaymentWorklists,
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
    }else if ($model->claim_type === RefClaimType::codeProdOTMeal) {
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
                                    $this->render('_financeApprovalResult', [
                                        'model' => $model,
                                        'detail' => $detail,
                                        'financeApprovalWorklists' => $financeApprovalWorklists,
                                    ])
                                    ?>                                    
                                    <?=
                                    $this->render('_superiorApprovalResult', [
                                        'model' => $model,
                                        'detail' => $detail,
                                        'superiorWorklists' => $superiorWorklists,
                                    ])
                                    ?>
                                    <?=
                                    $this->render('_financePaymentResult', [
                                        'model' => $model,
                                        'detail' => $detail,
                                        'financePaymentWorklists' => $financePaymentWorklists,
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
    <?=
    $this->render('/_docModal')
    ?>  
    <?php ActiveForm::end(); ?>
</div>
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
