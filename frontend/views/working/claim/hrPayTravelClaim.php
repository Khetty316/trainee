<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
    $this->title = "Travel Claim Details";


    $this->params['breadcrumbs'][] = ['label' => 'HR - Claim'];
    $this->params['breadcrumbs'][] = ['label' => 'HR - Travel Claim (Pending)', 'url' => '/working/claim/hr-travel-claim'];
    $this->params['breadcrumbs'][] = $this->title;

    $dataProvider->sort->sortParam = false;


//            $claimMaster = ClaimsMaster::findOne(1);
    ?>
    <h4 class='mb-3'><?= $claimMaster->claimant->fullname . " - " . $claimMaster->claimType->claim_name . " (" . $claimMaster->claims_id . ")" ?>
        <?= ($claimMaster->claims_status == 4 ? "<span class='text-success'> (PAID)</span>" : "") ?>
        <?= ($claimMaster->claims_status == 9 ? "<span class='text-danger'> (REJECTED)</span>" : "") ?>
    </h4>

    <?php
    $i = 0;
    $days = 0;
    $total = 0;
    $claimDetail = new frontend\models\working\claim\ClaimsDetail();

    echo GridView::widget([
        'dataProvider' => $dataProvider,
//                'rowOptions' => function($model) use (&$okToSubmit) {
//                    if ($model->isExpired()) {
//                        $okToSubmit = false;
//                        return ['class' => 'table-danger'];
//                    }
//                },
        'layout' => '{items}{pager}',
        'options' => ['class' => 'table-sm'],
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'date1',
                'label' => 'Date',
                'format' => 'raw',
                'value' => function($data)use (&$days, &$claimDetail) {
                    $days = ((MyCommonFunction::countDays($data->date1, $data->date2) + 1));
                    $repeat = $claimDetail::checkTravelRepeated($data->claims_detail_id);
                    $dateStr = MyFormatter::asDate_Read($data->date1) . " - " . MyFormatter::asDate_Read($data->date2);
                    if ($repeat != "") {
                        return '<p class="p-0 m-0">' . $dateStr . ' <i class="fas fa-exclamation-circle text-danger" title="Date conflict with in item in: ' . $repeat . '"></i>' . '</p>';
                    } else {
                        return MyFormatter::asDate_Read($data->date1) . " - " . MyFormatter::asDate_Read($data->date2);
                    }
                }
            ],
            [
                'attribute' => 'detail',
                'format' => 'raw',
                'value' => function($data) {
                    return ($data->claim_type == "med" ? "(Medical) - " : "") . $data->detail;
                }
            ],
            [
                'attribute' => 'project_account',
                'format' => 'raw',
            ],
            [
                'attribute' => 'authorized_by',
                'value' => function($data) {
                    return $data->authorizedBy['username'];
                },
            ],
            [
                'attribute' => 'date1',
                'label' => "Days",
                'format' => 'raw',
                'value' => function($data) use (&$days) {
                    return $days;
                }
            ],
            [
                'attribute' => 'amount',
                'label' => "Amount / Day (RM)",
                'format' => 'raw',
                'value' => function($data)use(&$days) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount / $days) . '</p>';
                }
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'label' => 'Amount (RM)',
                'value' => function($data)use(&$total) {
                    $total += $data->amount;
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
                }
            ],
//                    'created_at',
        ],
    ]);
    ?>
    <div>
        <p class='text-right'>
            <?php
            echo yii\helpers\Html::a("Travel Form <i class='far fa-file-pdf'></i>", "/working/claim/print-claim-form?claimsMasterId=" . $claimMaster->claims_master_id, ['target' => '_blank', 'class' => 'btn btn-primary mr-2']);
            if (in_array($claimMaster->claims_status, array(2, 3))) {
                echo Html::a("APPROVE <i class='fas fa-check'></i> ", 'javascript:approvePayment()', ['class' => 'btn btn-success mr-2', 'id' => 'submitBtn']);
                echo Html::a("REJECT <i class='fas fa-times'></i> ", 'javascript:rejectPayment()', ['class' => 'btn btn-danger mr-2']);
            } else if ($claimMaster->claims_status == 5) {
                echo Html::a("PAID <i class='fas fa-check'></i> ", 'javascript:setToPay()', ['class' => 'btn btn-success mr-2', 'id' => 'submitBtn']);
            }
            ?>

        </p>
    </div>
</div>


<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/hr-pay-travel-claim?claimsMasterId=' . $claimMaster->claims_master_id,
                'method' => 'post'
    ]);
    echo '<input type="text" name="setPay" id="setPay" value="pay"/>';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>
    function rejectPayment() {
        var ans = confirm("Confirm to REJECT claim?");
        if (ans) {
            $("#setPay").val("reject");
            $("#submitBtn").html("Processing...").attr('disabled', 'true');
            $("#myForm").submit();
        }
    }
    function approvePayment() {
        var ans = confirm("Confirm to APPROVE claim?");
        if (ans) {
            $("#setPay").val("approve");
            $("#submitBtn").html("Processing...").attr('disabled', 'true');
            $("#myForm").submit();
        }
    }
    function setToPay() {
        var ans = confirm("Confirm to set claim to Paid?\nThe claim will not be calculated in Payroll.");
        if (ans) {
            $("#setPay").val("pay");
            $("#submitBtn").html("Processing...").attr('disabled', 'true');
            $("#myForm").submit();
        }
    }

</script>