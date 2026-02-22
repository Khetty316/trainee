<?php

use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectQPanelUnit;

if ($model->isNewRecord) {
    $model->unit_code = RefProjectQPanelUnit::DEFAULT_Code;
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
            <?= $form->field($model, 'id') ?>
            <?= $form->field($model, 'proj_prod_master')->textInput() ?>
        </div>
        <div>
            <div class="form-row">
                <div class="col-sm-12 col-lg-6">
                    <?= $form->field($model, 'panel_description')->textInput()->label("Panel Name") ?>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <?php
                    echo $form->field($model, 'panel_type')->dropdownList(\frontend\models\common\RefProjectQTypes::getDropDownList());
                    ?>
                </div>
                <div class="col-sm-12 col-lg-1">
                    <?php
                    echo $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'class' => 'text-right form-control']);
                    ?>
                </div>
                <div class="col-sm-12 col-lg-2">
                    <?= $form->field($model, 'unit_code')->dropDownList(RefProjectQPanelUnit::getDropDownList()) ?>
                </div>
            </div>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success submitButton"><?= $btnText ?></button>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
