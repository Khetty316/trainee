<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use frontend\models\asset\AssetService;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */

$this->title = $model->asset_idx_no;
$this->params['breadcrumbs'][] = ['label' => 'Asset Management', 'url' => ['/asset/index-asset-super']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="asset-master-view">

    <p>
        <?= Html::a('Edit Asset Detail <i class="fas fa-pencil-alt"></i>', ['super-edit-asset', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Asset Detail</legend>
        <div class="pt-2">
            <?=
            $this->render('_viewAssetDetailView', [
                'model' => $model,
            ])
            ?>
        </div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Transfer History</legend>
        <?php
        if ($pendingTracking) {
            echo '<p class="text-success m-0">Item is under transfer to ' . $pendingTracking['receiveUser']['fullname'] . '. &nbsp</p>';

            echo Html::a('Cancel On Behalf <i class="fas fa-times fa-lg"></i>',
                    ["/asset/super-transfer-cancel", 'id' => $pendingTracking->id],
                    [
                        'class' => 'btn btn-danger mr-2',
                        'data-confirm' => 'Are you sure to cancel the transfer?',
                        'data-method' => 'post',
                    ]
            );
        } else {
            echo Html::a('Transfer <i class="far fa-share-square"></i>',
                    "javsacript:",
                    [
                        'title' => 'Transfer',
                        'data-toggle' => 'modal',
                        'data-target' => '#workingModel_transfer',
                        'class' => 'btn btn-success'
                    ]
            );
        }
        ?>
        <div class="pt-2">
            <table class="table table-sm table-striped table-bordered" id="transferHistory">
                <thead class="thead-light text-center">
                    <tr>
                        <th colspan="2">Transfer</th>
                        <th colspan="6">Receive</th>
                        <th rowspan="2" class="align-middle">Status</th>
                        <th rowspan="2" class="align-middle">Initiate By</th>
                    </tr>
                    <tr>
                        <th>From</th><th>Date</th>
                        <th>To</th>
                        <th>Date</th>
                        <th>Project Code</th>
                        <th>Area</th>
                        <th>Address</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($assetTrackings as $key => $tracking) {
                        ?>
                        <tr class="<?= $tracking->active_status ? "bg-primary" : "" ?>">
                            <td><?= $tracking['fromUser']['fullname'] ?></td>
                            <td><?= MyFormatter::asDate_Read($tracking['deliver_date']) ?></td>
                            <!--<td class="text-wrap"><?= $tracking['deliver_remark'] ?></td>-->
                            <td><?= $tracking['receiveUser']['fullname'] ?></td>
                            <td><?= MyFormatter::asDate_Read($tracking['receive_date']) ?></td>
                            <td><?= $tracking['receive_proj_code'] ?></td>
                            <td><?= $tracking['receiveArea']['area_name'] ?></td>
                            <td><?= $tracking['receiveAddress']['address_name'] ?></td>
                            <td><?= $tracking['receiveCondition']['description'] ?></td>
                            <!--<td class="text-wrap"><?= $tracking['receive_remark'] ?></td>-->
                            <td class="<?php
                            switch ($tracking['request_status']) {
                                case 'reject':
                                case 'cancel':
                                    echo 'bg-danger';
                                    break;
                                case 'accept':
                                    echo 'bg-primary';
                                    break;
                                case 'pending':
                                    echo 'bg-success';
                                    break;
                            }
                            ?>"><?= $tracking['requestStatus']['description'] ?></td>
                            <td><?= $tracking['createdBy']['fullname'] ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Service Record</legend>
        <?php
        echo Html::a('Add Record <i class="fas fa-plus"></i>',
                "javsacript:",
                [
                    'title' => 'Transfer',
                    'data-toggle' => 'modal',
                    'data-target' => '#workingModel_service',
                    'class' => 'btn btn-success'
                ]
        );
        ?>
        <div class="pt-2">
            <?=
            $this->render('_viewAssetServiceRecord', [
                'model' => $assetService,
            ])
            ?>
        </div>
    </fieldset>

    <?php if ($currentTracking) { ?>
        <div class="modal fade" id="workingModel_transfer" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content p-3">
                    <?=
                    $this->render('_assetTransfer', [
                        'modelTracking' => new frontend\models\asset\AssetTracking(),
                        'model' => $model,
                        'currentTracking' => $currentTracking,
                        'userType' => 'normalUser',
                        'formAction' => '/asset/super-transfer'
                    ])
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    if ($pendingTracking) {
        ?>
        <div class="modal fade" id="workingModel_receive" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content p-3">
                    <?=
                    $this->render('_assetReceive', [
                        'modelTracking' => $pendingTracking,
                        'model' => $model,
                        'currentTracking' => $currentTracking,
                        'userType' => 'normalUser',
                        'formAction' => '/asset/super-receive'
                    ])
                    ?>
                </div>
            </div>
        </div>
    <?php }
    ?>

    <div class="modal fade" id="workingModel_service" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-3">
                <?=
                $this->render('_modalAssetService', [
                    'model' => new AssetService(),
                    'assetId' => $model->id,
                    'userType' => 'normalUser',
                    'formAction' => '/asset/super-service'
                ])
                ?>
            </div>
        </div>
    </div>

</div>
