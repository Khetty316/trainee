<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>

<div class="form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-12">
            <?=
            $form->field($model, 'new_target_date')->widget(yii\jui\DatePicker::className(),
                    ['options' => ['class' => 'form-control', 'required' => true, 'placeholder' => 'dd/mm/yyyy']
                        , 'dateFormat' => 'dd/MM/yyyy'])->label('New Target Completion Date <span class="text-danger">*</span>', ['encode' => false])
            ?>        
        </div>
    </div>

    <?= $form->field($model, 'remark_update_target_date')->textarea(['maxlength' => true, 'required' => true])->label('Remark <span class="text-danger">*</span>', ['encode' => false]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
