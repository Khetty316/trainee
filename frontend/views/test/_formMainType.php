<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
?>

<div class="select-main-type">


    <?php $form = ActiveForm::begin(); ?>

    <div class="col">
        <?= $form->field($model, 'testType')->dropdownList(frontend\models\test\TestMain::getDropDownListType($main->id)); ?>
        <?=
                $form->field($model, 'date', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                ->widget(\yii\jui\DatePicker::className(), [
                    'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                    'clientOptions' => [
                        'dateFormat' => 'dd/mm/yy',
                        'showButtonPanel' => true,
                        'closeText' => 'Close',
                        'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                    ],
        ]);
        ?>
        <?=
        $form->field($model, 'tested_by')->widget(AutoComplete::className(), [
            'clientOptions' => [
                'source' => $userList,
            ],
            'options' => ['class' => 'form-control', 'value' => $model['testedBy']['fullname'] ?? null],
            'clientEvents' => [
                'open' => new JsExpression("function(event, ui) {
                                // Set z-index of autocomplete dropdown explicitly
                                $('.ui-autocomplete').css('z-index', 1051);
                                }"),
            ],
        ]);
        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'float-right btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<?php $this->registerJs("$(document).ready(function() { $('form').attr('autocomplete', 'off'); });"); ?>
