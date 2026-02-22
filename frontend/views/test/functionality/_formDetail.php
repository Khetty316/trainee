<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
?>
<div>

    <?php
    $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>
    <div class="col-12">
        <?= $form->field($detail, 'pot')->dropdownList($potList, []) ?>
    </div>
    <div class="col-12">
        <?= $form->field($detail, 'pot_val')->textInput(['placeholder' => 'Leave empty if not applicable']) ?>
    </div>

    <div class="text-right">
        <?= Html::submitButton('Save Detail', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end();
    ?>
</div>