<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>


<div class="add-component-form">
    <?php
    $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off']
    ]);
    ?>

    <?= $form->field($model, 'comp_type')->dropdownList($compList, ['id' => 'comp_type', 'prompt' => 'Select Component']) ?>

    <?= $form->field($model, 'comp_name', ['options' => ['id' => 'compName', 'class' => 'mb-3']])->textInput(['placeholder' => 'Component name']) ?>

    <?= $form->field($model, 'pou', ['options' => ['class' => 'mb-0']])->textInput(['id' => 'pou_label', 'disabled' => true]) ?>

    <?= $form->field($model, 'pou', ['options' => ['class' => 'm-0']])->dropdownList($pointList1, ['id' => 'pou_list1', 'disabled' => true])->label(false) ?>

    <?= $form->field($model, 'pou')->dropdownList($pointList2, ['id' => 'pou_list2', 'disabled' => true])->label(false) ?>

    <?= $form->field($model, 'pou_val')->textInput(['id' => 'pou_val', 'placeholder' => 'Leave empty if not applicable']) ?>

    <div class="form-group col text-right pr-0">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#pou_list1, #pou_list2, #compName').hide();

        $('#comp_type').change(function () {
            var selectedValue = $(this).val();

            if (selectedValue) {
                if (selectedValue === '<?= \frontend\models\test\RefTestCompType::TYPE_OTHER ?>') {
                    $('#compName').show().prop('disabled', false);
                } else {
                    $('#compName').hide().prop('disabled', true);
                }
                if (selectedValue === '<?= \frontend\models\test\RefTestCompType::TYPE_BUSBAR ?>') {
                    $('#pou_list2').show().prop('disabled', false);
                    $('#pou_list1').hide().prop('disabled', true);
                } else {
                    $('#pou_list1').show().prop('disabled', false);
                    $('#pou_list2').hide().prop('disabled', true);
                }
                $('#pou_label').hide().prop('disabled', true);
            } else {
                $('#pou_list1').hide().prop('disabled', true);
                $('#pou_list2').hide().prop('disabled', true);
                $('#pou_label').show().prop('disabled', true);
            }

        });
    });
</script>

