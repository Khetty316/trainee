<?php

use yii\helpers\Html;
use frontend\models\asset\AssetService;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */

$this->title = $model->asset_idx_no;
if ($pendingTracking && $pendingTracking->receive_user == Yii::$app->user->id) {
    $this->params['breadcrumbs'][] = ['label' => 'Pending (Receive)', 'url' => ['/asset/asset-pending-receive']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Asset On Hand', 'url' => ['/asset/asset-on-hand']];
}


$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


$transferToYou = ($pendingTracking['receive_user'] == Yii::$app->user->id) ? true : false;
$transferFromYou = ($pendingTracking['from_user'] == Yii::$app->user->id) ? true : false;
$isCurrentHolder = ($currentTracking['receive_user'] == Yii::$app->user->id) ? true : false;
$isThirdPerson = ($currentTracking['receive_user'] != Yii::$app->user->id && $pendingTracking['receive_user'] != Yii::$app->user->id) ? true : false;
$alreadyRequest = false;
$isUnderTransfer = ($pendingTracking['receive_user']) ? true : false;
?>
<div class="asset-master-view">
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Asset Detail</legend>
        <?php
        echo '<p class="text-primary m-0">Current person-in-charge: <b>' . $currentTracking->receiveUser->fullname . '</b>. &nbsp</p>';
        if ($requestTransfer) {
            echo '<p class="m-0">Request by:</p>';
            foreach ($requestTransfer as $key => $request) {
                $reqString = '<p class="m-1"><b>&nbsp;&nbsp;&nbsp; -' . $request->requestor0->fullname
                        . '</b>&nbsp@&nbsp<b>' . MyFormatter::asDateTime_ReaddmYHi($request->created_at) . '</b>';
                if ($request->requestor == Yii::$app->user->id && !$transferToYou) {
                    $alreadyRequest = true;
                    $reqString .= Html::a(' Cancel <i class="fas fa-times fa-lg"></i>', ['/asset/personal-cancel-request-asset', 'id' => $request->id],
                                    [
                                        'data-confirm' => 'Cancel request?',
                                        'data-method' => 'post',
                                        'class' => 'btn btn-danger btn-sm ml-2'
                    ]);
                }
                if ($request->remark) {
                    $reqString .= '<button type="button" class="btn btn-info btn-sm ml-2" data-toggle="collapse" data-target="#req_' . $request->id . '" title="Remark"><i class="fas fa-info-circle"></i></button>';
                }
                if ($isCurrentHolder && !$isUnderTransfer) {
                    $reqString .= Html::a('<i class="far fa-share-square"></i>',
                                    'javsacript:',
                                    [
                                        'class' => 'btn btn-success btn-sm ml-2',
                                        'onclick' => 'makeTransfer("' . $request->requestor . '")'
                                    ]
                    );
                }
                $reqString .= '</p>';
                $reqString .= '<div class="text-wrap collapse container ml-5 m-0 p-0" id="req_' . $request->id . '"><fieldset class="form-group border p-3">' . Html::encode($request->remark) . "</fieldset></div>";

                echo $reqString;
            }
        }
        if ($transferToYou) {
            echo '<p class="text-success m-0">Item is under transfer to YOU. &nbsp</p>';
            echo Html::a('Receive <i class="far fa-check-circle fa-lg"></i>',
                    "javsacript:",
                    [
                        'title' => 'Receive',
                        'data-toggle' => 'modal',
                        'data-target' => '#workingModel_receive',
                        'class' => 'btn btn-success'
                    ]
            );

            echo Html::a('Reject <i class="fas fa-times-circle fa-lg"></i>',
                    ["/asset/personal-transfer-reject", 'id' => $pendingTracking->id],
                    [
                        'class' => 'btn btn-danger ml-2',
                        'data-confirm' => 'Are you sure to reject the transfer?',
                        'data-method' => 'post'
                    ]
            );
        } else if ($transferFromYou) {
            echo '<p class="text-warning m-0">Item is under transfer to: <b>' . $pendingTracking->receiveUser->fullname . '</b>. &nbsp</p>';
            echo Html::a('Cancel <i class="fas fa-times fa-lg"></i>',
                    ["/asset/personal-transfer-cancel", 'id' => $pendingTracking->id],
                    [
                        'class' => 'btn btn-danger',
                        'data-confirm' => 'Are you sure to cancel the transfer?',
                        'data-method' => 'post'
                    ]
            );
        } else if ($isCurrentHolder) {
            echo Html::a('Transfer <i class="far fa-share-square fa-lg"></i>',
                    "javsacript:",
                    [
                        'title' => 'Transfer',
//                        'data-toggle' => 'modal',
                        'onclick' => 'makeTransfer("")',
                        'class' => 'btn btn-success'
                    ]
            );
        } else if ($isThirdPerson && !$alreadyRequest) {
            echo Html::a('Make Request <i class="fas fa-question fa-lg"></i>',
                    "javsacript:",
                    [
                        'title' => 'Request',
                        'data-toggle' => 'modal',
                        'data-target' => '#workingModel_request',
                        'class' => 'btn btn-success'
                    ]
            );
        }
        ?>

        <div class="pt-2">
            <?=
            $this->render('_viewAssetDetailView', [
                'model' => $model,
            ])
            ?>
        </div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Service Record</legend>
        <?php
//        $transferToYou
//        $transferFromYou
//        $isCurrentHolder
//        $isThirdPerson

        if ($isCurrentHolder) {
            echo Html::a('Add Record <i class="fas fa-plus"></i>',
                    "javsacript:",
                    [
                        'title' => 'Transfer',
                        'data-toggle' => 'modal',
                        'data-target' => '#workingModel_service',
                        'class' => 'btn btn-success'
                    ]
            );
        }
        ?>
        <div class="pt-2">
            <?=
            $this->render('_viewAssetServiceRecord', [
                'model' => $assetService,
            ])
            ?>
        </div>
    </fieldset>


    <!--            ~~~~~~~~~~~~~~~~~~~ Modal ~~~~~~~~~~~~~~~~~~~             -->
    <?php if ($isCurrentHolder) { ?>
        <div class="modal fade" id="workingModel_transfer" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content p-3">
                    <?=
                    $this->render('_assetTransfer', [
                        'modelTracking' => new frontend\models\asset\AssetTracking(),
                        'model' => $model,
                        'currentTracking' => $currentTracking,
                        'userType' => 'normalUser',
                        'formAction' => '/asset/personal-transfer'
                    ])
                    ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="workingModel_service" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content p-3">
                    <?=
                    $this->render('_modalAssetService', [
                        'model' => new AssetService(),
                        'assetId' => $model->id,
                        'userType' => 'normalUser',
                        'formAction' => '/asset/personal-service'
                    ])
                    ?>
                </div>
            </div>
        </div>


        <?php
    }


    if ($transferToYou) {
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
                        'formAction' => '/asset/personal-receive'
                    ])
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    if ($isThirdPerson) {
        ?>
        <div class="modal fade" id="workingModel_request" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content p-3">
                    <?=
                    $this->render('_assetRequest', [
                        'model' => $model,
                        'currentTracking' => $currentTracking,
                    ])
                    ?>
                </div>
            </div>
        </div>
    <?php }
    ?>


</div>

<script>
    $(function () {

    });


    function makeTransfer(userId) {
        $("#workingModel_transfer").modal('show');
        $("#assettracking-receive_user").val(userId);
    }
</script>