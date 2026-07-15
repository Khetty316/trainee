<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

/* @var $model frontend\models\client\ClientDebt */

$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];

$currentYear = date('Y');

$this->registerCss("
    .ui-autocomplete {
        z-index: 9999 !important;
        background: #fff !important;
        border: 1px solid #ccc !important;
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .ui-menu-item-wrapper {
        color: #000 !important;
        padding: 8px 12px;
    }

    .ui-state-active,
    .ui-widget-content .ui-state-active {
        background: #007bff !important;
        border: none !important;
        color: #fff !important;
    }
");

$years = [];
for ($y = 2020; $y <= $currentYear; $y++) {
    $years[$y] = $y;
}
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">

    <!-- Client Code -->
    <div class="col-md-6">

        <div class="form-group">

            <label>Client Code</label>

            <?=
            Html::textInput(
                    'client_code',
                    '',
                    [
                        'class' => 'form-control',
                        'readonly' => true,
                        'id' => 'client-code'
                    ]
            )
            ?>

        </div>

    </div>

    <!-- Company Name -->
    <div class="col-md-6">

        <div class="form-group">

            <label>Company Name</label>

            <?=
            AutoComplete::widget([
                'name' => 'company_name',
                'clientOptions' => [
                    'source' => Url::to(['client-autocomplete']),
                    'minLength' => 1,
                    'select' => new JsExpression("
                    function(event, ui) {

                        $('#client-id').val(ui.item.id);

                        $('#client-code').val(ui.item.client_code);
                    }
                "),
                ],
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Search Company Name',
                    'autocomplete' => 'off',
                ],
            ])
            ?>

            <?=
            Html::hiddenInput('ClientDebt[client_id]', '', [
                'id' => 'client-id'
            ])
            ?>

        </div>

    </div>

    <!-- Company Group -->
    <div class="col-md-6">
        <?=
        $form->field($model, 'tk_group_code')->dropDownList(
                \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3,
                [
                    'prompt' => 'Select Company Group'
                ]
        )
        ?>
    </div>

    <!-- Month -->
    <div class="col-md-3">
        <?=
        $form->field($model, 'month')->dropDownList(
                $months,
                [
                    'prompt' => 'Select Month'
                ]
        )
        ?>
    </div>

    <!-- Year -->
    <div>
        <?php
        $years = [];

        for ($y = date('Y'); $y >= 2020; $y--) {
            $years[$y] = $y;
        }
        ?>

        <?=
        $form->field($model, 'year')->dropDownList(
                $years,
                [
                    'prompt' => 'Select Year',
                    'style' => 'width: 270px',
                ]
        )
        ?>
    </div>

    <!-- Balance -->
    <div class="col-md-6">
        <?= $form->field($model, 'balance')->textInput(['autocomplete' => 'off', 'style' => 'text-align: right;']) ?>
    </div>

</div>

<div class="text-right">
    <?=
    Html::submitButton(
            'Save <i class="fas fa-check"></i>',
            ['class' => 'btn btn-success']
    )
    ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs("

$('#client-id').change(function () {

    var clientId = $(this).val();

    if(clientId != '') {

        $.get(
            '" . \yii\helpers\Url::to(['get-client-code']) . "',
            {id: clientId},
            function(data) {

                $('#client-code').val(data);
            }
        );
    }
});
");
?>