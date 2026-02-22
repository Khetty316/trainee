<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\office\pettyCash\PettyCashRequestMaster;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\pettyCash\PettyCashRequestMaster */
$action = ($module === 'finance' ? ['label' => 'Petty Cash Replenishment Request - Finance', 'url' => ['finance-replenishment']] : ['label' => 'Petty Cash Replenishment Request Approval - Director', 'url' => ['director-approval-pending']]);
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
                        <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($model->voucher_no) ?></span>
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
            <h6 class="mb-0">Replenishment Request Details</h6>
        </div>
        <div class="col-md-12 mt-3 ml-2">
            <?php
            if ($module === 'finance' && $model->status == frontend\models\RefGeneralStatus::STATUS_GetDirectorApproval && $model->deleted_by === null && $model->created_by == \Yii::$app->user->identity->id) {
                echo Html::a("Update",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['update-replenishment-request', 'id' => $model->id]),
                            "class" => "modalButtonMedium btn btn-primary",
                            'data-modaltitle' => "Update Petty Cash Replenishment Request Form"
                        ]
                );

                echo Html::a('Cancel', ['cancel-replenishment-request', 'id' => $model->id], [
                    'class' => 'btn btn-danger mx-1',
                    'data' => [
                        'confirm' => 'Are you sure you want to cancel this request?',
                        'method' => 'post',
                    ],
                ]);
            }
            ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" width="6%">Date Requested</th>
                            <th rowspan="2" class="text-right" width="12%">Amount Requested (RM)</th>
                            <th rowspan="2" width="16%">Purpose</th>
                            <th rowspan="2" width="16%">Ledger Records</th>
                            <th colspan="2" class="text-center" width="35%">Director's Response</th>
                            <th class="text-center" width="35%">Finance's Response</th>
                        </tr>
                        <tr>
                            <th class="text-right" width="12%">Amount Approved (RM)</th>
                            <th class="text-left" width="14%">Remark</th>
                            <th class="text-left" width="14%">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                    <td><?= MyFormatter::asDateTime_ReaddmYHi($model->created_at) ?></td>
                    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->amount_requested) ?></td>
                    <td><?= $model->purpose ?></td>
                    <td class="text-center" width="10%">
                        <?=
                        Html::a("View",
                                "javascript:",
                                [
                                    "onclick" => "event.preventDefault();",
                                    "value" => \yii\helpers\Url::to(['../office/petty-cash/ajax-view-ledger-detail-list', 'id' => $model->id]),
                                    "class" => "modalButton btn btn-primary",
                                ]
                        )
                        ?>
                    </td>
                    <?php if ($module === 'finance') { ?>
                        <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->amount_approved) ?></td>
                        <td>
                            <?php if ($model->status == frontend\models\RefGeneralStatus::STATUS_GetDirectorApproval) { ?>
                                <?= $model->director_responsed_status == 0 ? '<span class="text-warning">Pending</span>' : '<span class="text-success">Approved</span><br>' ?><?php
                                $responder = User::findOne($model->director_responsed_by);
                                if ($responder):
                                    ?>
                                    by <?= Html::encode($responder->fullname) ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($model->director_responsed_at) ?>
                                <?php endif; ?>
                            <?php } else { ?>
                                <?php if ($model->director_responsed_status == PettyCashRequestMaster::STATUS_APPROVED && $model->deleted_by === null) { ?>
                                    <span class="text-success">Approved</span><br>
                                    <?php
                                    $responder = User::findOne($model->director_responsed_by);
                                    if ($responder):
                                        ?>
                                        by <?= Html::encode($responder->fullname) ?>
                                    <?php endif; ?>
                                    @ <?= MyFormatter::asDateTime_ReaddmYHi($model->director_responsed_at) ?>

                                <?php } else if ($model->director_responsed_status == PettyCashRequestMaster::STATUS_REJECTED) { ?>
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
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <?=
                        $this->render('_financeResponseReplenishment', [
                            'model' => $model
                        ])
                        ?>
                    <?php } else if ($module === 'director') { ?>
                        <?=
                        $this->render('_directorApprovalFormReplenishment', [
                            'model' => $model
                        ])
                        ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
