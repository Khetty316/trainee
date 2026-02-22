<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">

    <?= $this->render('__ClaimNavBar', ['module' => 'personal_claims', 'pageKey' => '2']) ?>
    <?php $this->params['breadcrumbs'][] = $this->title; ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
//            'layout' => '{items}{pager}', 
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'claims_id',
                'label' => 'Claim ID',
                'format' => 'raw',
                'value' => function($data) {
                    $file = "";
                    if ($data->claimsMi != "") {
                        $file = " " . Html::a("<i class='far fa-file-alt' ></i>", "/working/mi/get-file?filename=" . urlencode($data->claimsMi->filename), ['target' => "_blank", 'class' => 'm-1', 'title' => "Click to view file"]);
                    }
                    return Html::a($data->claims_id, '/working/claim/view-claimmaster-detail?claimsMasterId=' . $data->claims_master_id, ['title' => 'Click to view detail']) . "&nbsp;&nbsp;" . $file;
                },
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'claim_type',
                'label' => 'Claim Type',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimType->claim_name;
                },
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Submission Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                },
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'total_amount',
                'label' => 'Total Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->total_amount) . '</p>';
                },
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'claims_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimsStatus->status_name;
                },
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'claims_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    $claimType = $model->claimType->claim_name;
                    $claimId = Html::a($claimType . " (" . $model->claims_id . ") ", '/working/claim/view-claimmaster-detail?claimsMasterId=' . $model->claims_master_id);
                    $file = $model->claimsMi ? (" " . Html::a("<i class='far fa-file-alt' ></i>", "/working/mi/get-file?filename=" . urlencode($model->claimsMi->filename), ['target' => "_blank", 'class' => 'm-1', 'title' => "Click to view file"])) : "";
                    $submissionDate = "<p class='m-0 p-0'>Submitted @ <b>" . MyFormatter::asDateTime_ReaddmYHi($model->created_at) . "</b></p>";
                    $amt = "<p class='m-0 p-0'>Amt: <b>RM " . MyFormatter::asDecimal2($model->total_amount) . "</b></p>";
                    $status = "<p class='m-0 p-0'>Status: <b>" . $model->claimsStatus->status_name . "</b></p>";
                    return '<b>' . $claimId . $file . '</b><br/>' . $submissionDate . $amt . $status;
                },
                'contentOptions' => ['class' => 'd-md-none'],
                'headerOptions' => ['class' => 'd-none']
            ],
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

    function disableSubmit() {
        alert("One or more items in this category is expired. Please remove the item and try again.");
    }
</script>