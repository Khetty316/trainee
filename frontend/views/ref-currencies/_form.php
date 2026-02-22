<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\common\RefCurrencies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ref-currencies-form">

    <?php $form = ActiveForm::begin([
    'id' => 'ref-currency-form',
    'enableAjaxValidation' => true,
    'validationUrl' => $model->isNewRecord
        ? ['create']
        : ['update', 'id' => $model->currency_id],
]); ?>


    <div class="form-row">
        <div class="col-sm-12 col-lg-12 pb-3">
            <?= $form->field($model, 'currency_name')->textInput(['maxlength' => true]) ?>       
        </div>
        
        <div class="col-sm-12 col-lg-12 pb-3">   
            <?= $form->field($model, 'currency_code')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-lg-12 pb-3">
            <?= $form->field($model, 'currency_sign')->textInput(['maxlength' => true]) ?>
        </div>
        
        <div class="col-sm-12 col-lg-12 pb-3">   
            <?=
                    $form->field($model, 'exchange_rate')
                    ->input('number', [
                        'step' => '0.0001',
                        'min' => 0
                    ])
            ?>
        </div>

        <div class="col-sm-12 col-lg-12 pb-3">
            <?=
            $form->field($model, 'active')->dropDownList([
                '1' => 'Yes',
                '0' => 'No',
            ])
            ?>        
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
