<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
?>

<div class="initiate-panel">


    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-12">
            <label>Panel Code: <span><?= $panel->project_production_panel_code ?></span></label>
        </div>
    </div>
    <?= $form->field($model, 'panel_id')->hiddenInput(['value' => $panel->id])->label(false) ?>
    <div class="row">
        <div class="col-4">
            <?php
            if ($model->test_type) {
                echo $form->field($model, 'test_type')->dropdownList(frontend\models\test\TestMain::TEST_LIST, ['disabled' => true]);
            } else {
                echo $form->field($model, 'test_type')->dropdownList(frontend\models\test\TestMain::TEST_LIST);
            }
            ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'doc_ref')->textInput(['disabled' => true, 'value' => 'TK/TC']) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'rev_no')->textInput(['disabled' => true, 'value' => 'Revision 0']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'client')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'elec_consultant')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'elec_contractor')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("$(document).ready(function() { $('form').attr('autocomplete', 'off'); });"); ?>
