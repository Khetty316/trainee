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
    echo $this->render('__ClaimNavBar', ['module' => 'account_claims', 'pageKey' => '1']);
    $this->params['breadcrumbs'][] = ['label' => 'Account - Claim'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <button class="btn btn-success mb-2 " onclick="bulkPaid()">Paid <i class="far fa-money-bill-alt"></i></button>
    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->claims_master_id, 'style' => 'text-align:center'];
                }
            ],
            [
                'attribute' => 'claims_id',
                'format' => 'raw',
                'value' => function($data) {
                    $url = '/working/claim/viewonly?claimsMasterId=' . $data->claims_master_id;
                    return Html::a($data->claims_id, "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);
                }
            ],
            [
                'attribute' => 'claim_type',
                'label' => 'Claim Type',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimType->claim_name;
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
                    return MyFormatter::asDate_Read($data->created_at);
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
        ],
    ]);
    ?>

</div>


<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myFormPayClaim',
                'action' => '/working/claim/account-claim-pending',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claimIds" id="claimIds"/> ';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>

    function bulkPaid() {
        var checkedList = [];
        $('tbody input:checked').each(function () {
            checkedList.push(this.value);
        });
        if (checkedList.length === 0) {
            alert("No record selected");
            return;
        }
        $("#claimIds").val(checkedList);

        var ans = confirm("Set status to Paid?");
        if (ans) {
            $("#myFormPayClaim").submit();
        }
    }

</script>