<?php

use yii\bootstrap4\Html;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$totalOfClaim = 0;
?>
<style>
    th{
        text-align: center;
    }
    td p{
        margin:0px;
        padding:0px;
    }
</style>

<div class="claims-detail-index">

    <?php
    $this->title = $model->claims_id;
    $this->params['breadcrumbs'][] = ['label' => 'Personal Claims - Submitted', 'url' => ['/working/claim/personal-submitted-claim']];
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <h4> <?= $model->claimType->claim_name . " (" . $model->claims_id . ") - " . $model->claimsStatus->status_name ?></h4>
    <div class="col-12 d-none d-md-block">
        <?php
        $isTravelClaim = $model->claim_type == "tra" ? true : false;
        ?>
        <table class='table table-hover table-sm table-bordered' width="100%">
            <thead>
                <tr class='table-primary'>
                    <th>Attachment</th>
                    <th>Date</th>
                    <th>Detail</th>
                    <?= $isTravelClaim ? '' : '<th>Company Name</th>' ?>
                    <?= $isTravelClaim ? '' : '<th>Receipt No.</th>' ?>
                    <th>Project Code</th>
                    <th>Authorized By</th>
                    <th>Auth. Status</th>
                    <?= $isTravelClaim ? '<th>Days</th>' : '' ?>
                    <?= $isTravelClaim ? '<th>Amt / Day</th>' : '' ?>
                    <th>Total Amount(RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasLostReceipt = 0;
                foreach ($model->claimsDetails as $key => $claimsDetail) {
                    $days = $isTravelClaim ? (MyCommonFunction::countDays($claimsDetail->date1, $claimsDetail->date2) + 1) : 0;
                    $recordDate = MyFormatter::asDate_Read($claimsDetail->date1) . ($isTravelClaim ? " - " . MyFormatter::asDate_Read($claimsDetail->date2) : '');
                    $authorizeSts = "";
                    if ($claimsDetail->authorize_status == 1) {
                        
                    }

                    switch ($claimsDetail->authorize_status) {
                        case 1:
                            $authorizeSts = "Waiting for authorization";
                            break;
                        case 2:
                            $authorizeSts = "Authorized";
                            break;
                        case 3:
                            $authorizeSts = "Rejected";
                            break;
                    }
                    ?>
                    <tr <?= $claimsDetail->authorize_status == 3 ? "class='table-danger'" : ($key % 2 == 0 ? "class='table-info'" : "") ?>>
                        <td style='text-align: center'><?= $claimsDetail->filename == "" ? "" : Html::a("<i class='far fa-file-alt'></i>", "/working/claim/get-file?filename=" . urlencode($claimsDetail->filename), ['target' => '_blank']) ?></td>
                        <td style="text-align: center"><?= $recordDate ?></td>
                        <td><?= $claimsDetail->showDetail() ?></td>
                        <?= $isTravelClaim ? '' : '<td>' . $claimsDetail->company_name . '</td>' ?>
                        <?= $isTravelClaim ? '' : '<td>' . $claimsDetail->receipt_no . ($claimsDetail->receipt_lost == 1 ? ' <i class="fas fa-exclamation-triangle fa-sm text-danger" title="Receipt Lost"></i>' : '') . '</td>' ?>
                        <td><?= $claimsDetail->project_account ?></td>
                        <td><?= $claimsDetail->authorizedBy['fullname'] ?></td>
                        <td><?= $authorizeSts ?></td>
                        <?= $isTravelClaim ? '<td style="text-align: right">' . $days . '</td>' : '' ?>
                        <?= $isTravelClaim ? '<td style="text-align: right">' . MyFormatter::asDecimal2($claimsDetail->amount / $days) . '</td>' : '' ?>
                        <td style="text-align: right"><?= MyFormatter::asDecimal2($claimsDetail->amount) ?></td>
                    </tr>
                    <?php
                    $totalOfClaim += $claimsDetail->amount;
                    $hasLostReceipt += $claimsDetail->receipt_lost;
                }
                ?>
            </tbody>
            <tfoot>
                <tr class='table-primary'>
                    <td colspan="8" style="text-align:right;padding: 5px">
                        <b>Total Of Claim</b>
                    </td>
                    <td style="text-align: right;padding: 5px"><b><?= MyFormatter::asDecimal2($totalOfClaim) ?></b></td>
                </tr>
            </tfoot>
        </table>


    </div>




    <div class="d-md-none">
        <table class='table table-hover table-sm table-bordered' width="100%">
            <thead>
                <tr class='table-primary'>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($model->claimsDetails as $key => $claimsDetail) {
                    $days = $isTravelClaim ? (MyCommonFunction::countDays($claimsDetail->date1, $claimsDetail->date2) + 1) : 0;
                    $recordDate = MyFormatter::asDate_Read($claimsDetail->date1) . ($isTravelClaim ? " - " . MyFormatter::asDate_Read($claimsDetail->date2) : '');
                    $authorizeSts = "";
                    switch ($claimsDetail->authorize_status) {
                        case 1:
                            $authorizeSts = "Waiting for authorization";
                            break;
                        case 2:
                            $authorizeSts = "Authorized";
                            break;
                        case 3:
                            $authorizeSts = "Rejected";
                            break;
                    }
                    ?>
                    <tr <?= $claimsDetail->authorize_status == 3 ? "class='table-danger'" : ($key % 2 == 0 ? "class='table-info'" : "") ?>>
                        <td>
                            <p>Date: <?= $recordDate ?> &nbsp; &nbsp; <?= $claimsDetail->filename == "" ? "" : Html::a("<i class='far fa-file-alt'></i>", "/working/claim/get-file?filename=" 
                                    . urlencode($claimsDetail->filename), ['target' => '_blank']) ?></p>
                            <p>Detail: <?= $claimsDetail->detail ?></p>
                            <?= $isTravelClaim ? '' : '<p>' . $claimsDetail->company_name . '</p>' ?>
                            <?= $isTravelClaim ? '' : '<p>' . $claimsDetail->receipt_no . ($claimsDetail->receipt_lost == 1 ? ' <i class="fas fa-exclamation-triangle fa-sm text-danger" title="Receipt Lost"></i>' : '') . '</p>' ?>
                            <p><?= $claimsDetail->project_account ?></p>
                            <p><?= $claimsDetail->authorizedBy['fullname'] ?></p>
                            <p><?= $authorizeSts ?></p>
                            <?= $isTravelClaim ? '<p style="text-align: right">' . $days . '</p>' : '' ?>
                            <?= $isTravelClaim ? '<p style="text-align: right">' . MyFormatter::asDecimal2($claimsDetail->amount / $days) . '</p>' : '' ?>
                            <p>Amount: <?= MyFormatter::asDecimal2($claimsDetail->amount) ?></p>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr class='table-primary'>
                    <td style="text-align: right;padding: 5px"><b>Total Of Claim <?= MyFormatter::asDecimal2($totalOfClaim) ?></b></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div>
        <?php
        if ($model->claims_status > 1 && $model->claims_status <= 4 && $model->claim_type != 'tra') {
            echo yii\helpers\Html::a("Claim Form <i class='far fa-file-pdf'></i>", "/working/claim/print-claim-form?claimsMasterId=" . $model->claims_master_id, ['target' => '_blank', 'class' => 'btn btn-primary']);
            echo "&nbsp;";
            if ($hasLostReceipt > 0) {
                echo yii\helpers\Html::a("Lost Receipt Form(s) <i class='far fa-file-pdf'></i>", "/working/claim/print-receipt-lost-form?claimsMasterId=" . $model->claims_master_id, ['target' => '_blank', 'class' => 'btn btn-primary']);
            }
        } else if (in_array($model->claims_status, array(7, 9))) { //rejected or cancelled
            echo Html::button('Copy claim items <i class="far fa-copy"></i>', ['class' => 'btn btn-primary', 'onclick' => 'copyItems()']);
        }


        if ($model->claim_type == 'tra') {
            echo '<p>** No Claim Form needed for Travel Claim.</p>';
        }
        ?>
    </div>

</div>
<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/copy-claim-items',
                'method' => 'post'
    ]);
    echo yii\helpers\Html::textInput('claimsMasterId', $model->claims_master_id, ['id' => 'claimsMasterId']);
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>

<script>
    function copyItems() {
        var ans = confirm("Copy claim items into Pending List?");
        if (ans) {
            $("#myForm").submit();
        }
    }
</script>
