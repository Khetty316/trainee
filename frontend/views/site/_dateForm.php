<?php
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Html;

//$form = ActiveForm::begin([
//    'layout' => 'horizontal',
//    'fieldConfig' => [
//        'template' => "{label}\n<div class=\"col-sm-8\">{input}\n{error}</div>",
//        'labelOptions' => ['class' => 'col-sm-4 col-form-label fw-semibold'],
//    ],
//    'options' => ['autocomplete' => 'off', 'class' => 'report-form']
//        ]);
$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'errorOptions' => ['class' => 'invalid-feedback-show'],
    ],
]);
?>
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <?= $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])->widget(DatePicker::className(), ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'], 'dateFormat' => 'dd/MM/yyyy', 'clientOptions' => ['showButtonPanel' => true, 'closeText' => 'Close']])->label("Date From"); ?>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <?= $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])->widget(DatePicker::className(), ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'], 'dateFormat' => 'dd/MM/yyyy', 'clientOptions' => ['showButtonPanel' => true, 'closeText' => 'Close']])->label("Date To"); ?>
    </div>
    <div class="col-lg-3 col-md-8 col-sm-8">
        <?= $form->field($model, 'is_internalProject')->dropDownList(\frontend\models\report\ReportingModel::PROJECT_TYPE_OPTIONS, ['options' => ['class' => 'form-control']])->label('Project Type'); ?>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="form-group">
            <label>&nbsp;</label> 
            <div>
                <?= Html::a('Reset', '?', ['class' => 'btn btn-secondary mr-1']) ?>
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>                   
<?php ActiveForm::end(); ?>