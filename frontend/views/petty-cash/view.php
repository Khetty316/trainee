<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\office\pettyCash\PettyCashRequestMaster;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\pettyCash\PettyCashRequestMaster */
$action = ($module === 'personal' ? ['label' => 'Petty Cash Request - Personal', 'url' => ['personal-pending']] : ['label' => 'Petty Cash Request - Finance', 'url' => ['finance-approval-pending']]);
$this->title = $model->ref_code;
$this->params['breadcrumbs'][] = $action;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="petty-cash-request-master-view">

    <div class="row mb-1">
        <div class="col-md-8">
            <h5>Reference Code: <?= Html::encode($this->title) ?></h5>
        </div>

    </div>

    <div class="alert alert-info border-left-primary mb-4">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <strong class="text-muted">Name:</strong>
                        <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($model->createdBy->fullname) ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Voucher No.:</strong>
                        <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($model->voucher_no ) ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Status:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?php
                            if ($model->deleted_by !== null) {
                                $status = ($model->status0->status_name . ' by ' . $model->deletedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->deleted_at));
                            } else {
                            $status = ($model->status0->status_name);
                            }
                            ?>
                            <?= $status ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Advance Request Details</h6>
        </div>
        <div class="col-md-12 mt-3 ml-2">
            <?php
            if ($module === 'personal' && $model->status == frontend\models\RefGeneralStatus::STATUS_GetFinanceApproval && $model->deleted_by === null) {
                echo Html::a("Update",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['update', 'id' => $model->id]),
                            "class" => "modalButtonMedium btn btn-primary",
                            'data-modaltitle' => "Update Petty Cash Request Form"
                        ]
                );

                echo Html::a("Cancel",
                        ['cancel-form-pre', 'id' => $model->id, 'module' => 'personal'],
                        [
                            'class' => 'btn btn-danger ml-1',
                            'data-confirm' => 'Are you sure you want to cancel this request?',
                            'data-method' => 'post',
                        ]
                );
            }
            
            if ($module === 'finance' && $model->status == frontend\models\RefGeneralStatus::STATUS_PendingSupportedDocument && $model->deleted_by === null && $model->finance_id == Yii::$app->user->identity->id) {
                echo Html::a("Cancel",
                        ['cancel-form-pre', 'id' => $model->id, 'module' => 'finance'],
                        [
                            'class' => 'btn btn-danger',
                            'data-confirm' => 'Are you sure you want to cancel this request?',
                            'data-method' => 'post',
                        ]
                );
            }
            ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" width="6%">Date Requested</th>
                            <th rowspan="2" class="text-right" width="10%">Amount Requested (RM)</th>
                            <th rowspan="2" width="18%">Purpose</th>
                            <th colspan="2" class="text-center" width="35%">Finance's Response</th>
                        </tr>
                        <tr>
                            <th class="text-right" width="10%">Amount Approved (RM)</th>
                            <th class="text-left" width="14%">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                    <td><?= MyFormatter::asDateTime_ReaddmYHi($preForm->created_at) ?></td>
                    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($preForm->amount_requested) ?></td>
                    <td><?= $preForm->purpose_of_advance ?></td>
                    <?php if ($module === 'personal') { ?>
                        <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($preForm->amount_approved) ?></td>
                        <td>
                            <?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_GetFinanceApproval) { ?>
                                <?= $preForm->status == 0 ? '<span class="text-warning">Pending</span>' : '<span class="text-success">Verified</span><br>' ?><?php
                                $responder = User::findOne($preForm->responsed_by);
                                if ($responder):
                                    ?>
                                    by <?= Html::encode($responder->fullname) ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($preForm->responsed_at) ?>
                                <?php endif; ?>
                            <?php } else { ?>
                                <?php if ($preForm->status == PettyCashRequestMaster::STATUS_APPROVED) { ?>
                                    <span class="text-success">Verified</span><br>
                                    <?php
                                    $responder = User::findOne($preForm->responsed_by);
                                    if ($responder):
                                        ?>
                                        by <?= Html::encode($responder->fullname) ?>
                                    <?php endif; ?>
                                    @ <?= MyFormatter::asDateTime_ReaddmYHi($preForm->responsed_at) ?>

                                <?php } else if ($preForm->status == PettyCashRequestMaster::STATUS_REJECTED) { ?>
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
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    <?php } else if ($module === 'finance') { ?>
                        <?=
                        $this->render('_financeApprovalFormPre', [
                            'preForm' => $preForm,
                            'model' => $model
                        ])
                        ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($preForm->status == PettyCashRequestMaster::STATUS_APPROVED && $preForm->amount_approved != 0.00) { ?>
        <div class="card mt-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Receipt Details</h6>
            </div>
            <div class="col-md-12 mt-3 ml-2">
                <?php
                if ($module === 'personal' && empty($postForm) && $model->status != frontend\models\RefGeneralStatus::STATUS_ClaimantCancelClaim) {
                    echo Html::a("Add Receipt",
                            "javascript:",
                            [
                                "onclick" => "event.preventDefault();",
                                "value" => \yii\helpers\Url::to(['return-receipt', 'id' => $model->id]),
                                "class" => "modalButton btn btn-primary",
                                'data-modaltitle' => "Return Receipt"
                            ]
                    );
                }
                ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2" width="6%">Submitted by</th>
                                <th rowspan="2" width="6%">Receipt</th>
                                <th rowspan="2" class="text-right" width="10%">Receipt Amount (RM)</th>
                                <th colspan="2" class="text-center" width="35%">Finance's Response</th>
                            </tr>
                            <tr>
                                <th class="text-right" width="10%">Amount Approved (RM)</th>
                                <th class="text-left" width="16%">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($postForm)) {
                                ?>
                            <td><?= Html::encode($postForm->createdBy->fullname) ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($postForm->created_at) ?></td>
                            <td class="text-center">
                                <?=
                                Html::a("View",
                                        "javascript:",
                                        [
                                            "onclick" => "event.preventDefault();",
                                            "value" => \yii\helpers\Url::to(['view-receipt', 'id' => $postForm->id, 'module' => $module]),
                                            "class" => "modalButton btn btn-sm btn-primary",
                                            'data-modaltitle' => "Receipt detail"
                                        ]
                                )
                                ?>
                            </td>
                            <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($postForm->receipt_amount) ?></td>
                            <?php if ($module === 'personal') { ?>
                                <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($postForm->amount_approved) ?></td>
                                <td>
                                    <?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_WaitingForReceiptVerification) { ?>
                                        <?= $postForm->status == 0 ? '<span class="text-warning">Pending</span>' : '<span class="text-success">Verified</span><br>' ?><?php
                                        $responder = User::findOne($postForm->responsed_by);
                                        if ($responder):
                                            ?>
                                            by <?= Html::encode($responder->fullname) ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($postForm->responsed_at) ?>
                                        <?php endif; ?>
                                    <?php } else { ?>
                                        <?php if ($postForm->status == PettyCashRequestMaster::STATUS_APPROVED) { ?>
                                            <span class="text-success">Verified</span><br>
                                            <?php
                                            $responder = User::findOne($postForm->responsed_by);
                                            if ($responder):
                                                ?>
                                                by <?= Html::encode($responder->fullname) ?>
                                            <?php endif; ?>
                                            @ <?= MyFormatter::asDateTime_ReaddmYHi($postForm->responsed_at) ?>

                                        <?php } else if ($postForm->status == PettyCashRequestMaster::STATUS_REJECTED) { ?>
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
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            <?php } else if ($module === 'finance') { ?>
                                <?=
                                $this->render('_financeApprovalFormPost', [
                                    'postForm' => $postForm,
                                    'model' => $model,
                                    'preForm' => $preForm
                                ])
                                ?>
                                <?php
                            }
//                            }
                            ?>
                        <?php } else { ?>
                            <td colspan="5">No result found.</td>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

</div>
