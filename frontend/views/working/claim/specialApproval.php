<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */

$this->title = 'Request for special approval';
$this->params['breadcrumbs'][] = ['label' => 'Personal Claims', 'url' => ['/working/claim/personal-claim']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container p-0">

    <h3><?= Html::encode($this->title) ?></h3>



    <div class=" justify-content-center">
        <?php
        $form = ActiveForm::begin([
                    'id' => 'myForm',
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "<div class=\"col-sm-12\">{input}</div>\n",
                    ],
                    'options' => ['autocomplete' => 'off']
        ]);
        echo $form->field($model, 'special_request_remark')->textarea(['rows' => '6', 'placeholder' => 'Reason...']);
        echo Html::a('Submit', "#", ['class' => 'btn btn-primary', 'onclick' => "validateInputs()"]);

        ActiveForm::end();
        ?>

        <h4 class="pt-4">Claims Detail: </h4>
        <?=
        yii\widgets\DetailView::widget([
            'model' => $model,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
            'attributes' => [
                [
                    'attribute' => 'claim_type',
                    'value' => function($data) {
                        return $data->claimType->claim_name;
                    }
                ],
                [
                    'attribute' => 'filename',
                    'format' => 'raw',
                    'value' => function($data) {
                        $str = $data->filename == "" ? "" : Html::a("<i class='far fa-file-alt fa-lg' ></i>",
                                        "/working/claim/get-file?filename=" . urlencode($data->filename),
                                        ['target' => "_blank"]);



                        return $str;
                    }
                ],
                [
                    'attribute' => 'date1',
                    'label' => 'Date',
                    'value' => function($data) {

                        return MyFormatter::asDate_Read($data->date1) . ($data->date2 == "" ? "" : " - " . MyFormatter::asDate_Read($data->date2));
                    }
                ],
                'company_name',
                'receipt_no',
                [
                    'attribute' => 'detail',
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'project_account',
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'amount',
                    'value' => function($data) {
                        return MyFormatter::asCurrency($data->amount);
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Record Time',
                    'value' => function($data) {
                        return MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                    }
                ],
//            'is_submitted',
//            'is_deleted',
//            'receipt_lost',
            ],
        ])
        ?>
    </div>
</div>
<script>
    function validateInputs() {
        if ($("#claimsdetail-special_request_remark").val() === "") {
            alert("Field cannot be empty");
            $("#claimsdetail-special_request_remark").focus();
            return;
        }

        $("#myForm").submit();
    }
</script>