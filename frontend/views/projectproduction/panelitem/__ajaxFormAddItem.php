<?php

use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProjectItemUnit;

if ($model->isNewRecord) {
    $model->unit_code = RefProjectItemUnit::DEFAULT_Code;
}
?>

<div id="modalUpdateProjectPanel">
    <div class="" role="">
        <?php
        $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-12',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-6',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                    'options' => ['autocomplete' => 'off'],
        ]);
        ?>
        <h5 class="modal-title" id="modalUpdateProjectQPanelLabel"><?= $title ?? "" ?></h5>
        <div class="hidden">
            <?= $form->field($model, 'id')->label(false) ?>
            <?= $form->field($model, 'proj_prod_panel_id')->label(false) ?>
        </div>
        <div>
            <div class="form-row">
                <div class="col-sm-12 col-lg-6">
                    <?= $form->field($model, 'item_description')->textInput() ?>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <?= $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <?= $form->field($model, 'unit_code')->dropDownList(RefProjectItemUnit::getDropDownList()) ?>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success submitButton">Update <i class="fas fa-check"></i></button>
            <?php ActiveForm::end(); ?>
    </div>
</div>
