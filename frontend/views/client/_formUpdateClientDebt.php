<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
?>

<div class="client-debt-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-6">
            <label>Client Code</label>
            <input type="text"
                   class="form-control"
                   value="<?= $model->client ? $model->client->client_code : '-' ?>"
                   readonly>
        </div>

        <div class="col-md-6">
            <label>Company Name</label>
            <input type="text"
                   class="form-control"
                   value="<?= $model->client ? $model->client->company_name : '-' ?>"
                   readonly>
        </div>

    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            <?=
                    $form->field($model, 'tk_group_code')
                    ->dropDownList(
                            \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3,
                            [
                                'prompt' => 'Select Group'
                            ]
                    )
            ?>
        </div>

        <div class="col-md-3">
            <?=
                    $form->field($model, 'month')
                    ->dropDownList(
                            $months,
                            [
                                'prompt' => 'Select Month'
                            ]
                    )
            ?>
        </div>

        <div class="col-md-3">
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
                    ]
            )
            ?>
        </div>

    </div>

    <br>

    <div class="col-md-6">
        <?=
                $form->field($model, 'balance')
                ->textInput([
                    'class' => 'form-control text-right',
                    'type' => 'number',
                    'step' => '0.01',
                    'autocomplete' => 'off'
                ])
        ?>
    </div>

    <div class="form-group">
        <?=
        Html::submitButton(
                'Save',
                ['class' => 'btn btn-success float-right']
        )
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
