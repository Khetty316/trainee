<?php

use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectQPanelUnit;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefProjectQTypes;
if ($model->isNewRecord) {
    $model->unit_code = RefProjectQPanelUnit::DEFAULT_Code; // Default to "Unit" 3/3/2022
}
$model->amount = MyFormatter::asDecimal2NoSeparator($model->amount);
$form2 = ActiveForm::begin([
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
            'id' => 'testForm',
            'options' => ['autocomplete' => 'off'],
        ]);
?>
<div class="modal-body">
    <?= $form2->field($model, 'revision_template_id', ['options' => ['class' => 'hidden']])->hiddenInput()->label(false) ?>
    <?= $form2->field($model, 'panel_type')->dropdownList(RefProjectQTypes::getDropDownList(), ['prompt' => 'Select...']) ?>
    <?= $form2->field($model, 'panel_description')->textInput(['required' => true])->label("Panel name") ?>
    <?= $form2->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'required' => true]) ?>
    <?= $form2->field($model, 'unit_code')->dropDownList(RefProjectQPanelUnit::getDropDownList()) ?>
    <?= $form2->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'text-right form-control', 'required' => true]) ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-success"><?= $submitBtnText ?></button>
</div>
<?php ActiveForm::end(); ?>
