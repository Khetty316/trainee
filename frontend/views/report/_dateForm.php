<?php

use yii\bootstrap4\Html;
use yii\jui\DatePicker;
use yii\bootstrap4\ActiveForm;

$form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-6\">{input}\n{error}</div>",
                'labelOptions' => ['class' => 'col-sm-6 control-label'],
            ],
            'options' => ['autocomplete' => 'off']
        ]);
?>
<div class="form-group my-0">
    <div class="row">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?=
                    $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
                    ])->label("Quotation Date From");
            ?>
        </div>

    </div>
</div>
<div class="form-group my-0">
    <div class="row">
        <div class="col-lg-4 col-md-12 col-sm-12">

            <?=
                    $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
                    ])->label("Quotation Date To");
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
            <?= Html::a('Reset', '?', ['class' => 'btn btn-primary float-right mr-1']) ?>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>