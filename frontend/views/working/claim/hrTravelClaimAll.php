<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
//    $this->title = "HR Claim List";
//    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    echo $this->render('__ClaimNavBar', ['module' => 'hr_claims', 'pageKey' => '2']);
    $this->params['breadcrumbs'][] = ['label' => 'HR - Claim'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'claims_id',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->claims_id, '/working/claim/hr-pay-travel-claim?claimsMasterId=' . $data->claims_master_id);
                }
            ],
            [
                'attribute' => 'claimant_id',
                'label' => 'Claimant',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimant->fullname;
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Submission Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                }
            ],
            [
                'attribute' => 'claims_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimsStatus->status_name;
                }
            ],
            [
                'attribute' => 'total_amount',
                'label' => 'Total Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->total_amount) . '</p>';
                }
            ],
//                [
//                    'attribute' => 'company_name',
//                    'enableSorting' => false,
//                ],
//                [
//                    'attribute' => 'receipt_no',
//                    'enableSorting' => false,
//                ],
//                [
//                    'attribute' => 'detail',
//                    'format' => 'raw',
//                    'enableSorting' => false,
//                    'value' => function($data) {
//                        return ($data->claim_type == "med" ? "(Medical) - " : "") . $data->detail;
//                    }
//                ],
//                [
//                    'attribute' => 'project_account',
//                    'enableSorting' => false,
//                ],
//                [
//                    'attribute' => 'amount',
//                    'format' => 'raw',
//                    'enableSorting' => false,
//                    'value' => function($data)use(&$total) {
//                        $total += $data->amount;
//                        return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
//                    }
//                ],
//                    'created_at',
        ],
    ]);
    ?>

</div>


<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/submit-claim',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claimIds" id="claimIds"/> ';
    echo '<input type="text" name="claimFamily" id="claimFamily"/>';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>

    function submitClaim(claimName, recordIds, claimFamily) {

        var Ids = $("#" + recordIds).val();

        var answer = confirm("Submit your " + claimName + "?");
        if (answer) {
            $("#claimIds").val(Ids);
            $("#claimFamily").val(claimFamily);
            $("#myForm").submit();
        }


    }

</script>