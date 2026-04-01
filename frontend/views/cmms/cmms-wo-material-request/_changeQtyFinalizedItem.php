<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="payroll-records-create">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
    ]);
    ?>

    <div class="col-lg-12 col-md-12 col-sm-12 d-flex align-items-center mb-2">
        <?=
                    $form->field($model, 'qty')->textInput([
                        'type' => 'number',
                        'step' => '1',
                        'min' => ($model->dispatched_qty + $model->unacknowledged_qty),
                        'placeholder' => 'Enter quantity'
                    ])->label('Quantity')
                    ?>
    </div>
    <?php
        echo Html::submitButton(
                'Save',
                ['class' => 'btn btn-success px-3 float-right mt-2 mr-3']);
        ActiveForm::end();
    ?>
</div>