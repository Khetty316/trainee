<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-production-master-form">

    <?php
    $form = ActiveForm::begin([
        'id'=>'quotationSelectForm'
    ]);
    $model = new \frontend\models\ProjectProduction\ProjectProductionMaster();
    ?>
    <h3>Import from quotation:</h3>
    <div class="hidden">
        <?= $form->field($model, 'quotation_id')->textInput() ?>
    </div>
    <?=
    $form->field($model, 'quotationNo')->widget(\yii\jui\AutoComplete::className(), [
        'clientOptions' => [
            'source' => \yii\helpers\Url::to(['/projectquotation/get-autocomplete-project-quotation-list']),
            'minLength' => '1',
            'autoFill' => true,
            'change' => new \yii\web\JsExpression("function( event, ui ) { 
			      $(this).val((ui.item ? ui.item.value : ''));
			     }"),
            'delay' => 200,
            'appendTo' => '#quotationSelectForm',
        ],
        'options' => [
            'class' => 'form-control',
            'placeholder' => 'Type to search'
        ]
    ])
    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
