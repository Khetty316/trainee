<?php

use frontend\models\test\TestFormVisualpaint;
use frontend\models\test\RefTestStatus;

$formList = ["A (Front)" => "a", "B (Left Side)" => "b", "C (Right Side)" => "c", "D (Top)" => "d", "E (Rear)" => "e", "F (Interior)" => "f"];
?>
<table class="table table-sm table-bordered">
    <tr class="text-center">
        <th></th>
        <th>Severe</br>Scratches</th>
        <th class="vmiddle">Rusts</th>
        <th class="vmiddle">Color</th>
        <th class="vmiddle">Finishing</th>
        <th class="vmiddle">Remark</th>
        <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
            <th class="vmiddle">Result</th>
        <?php } ?>
    </tr>
    <?php
    $value = 1;
    foreach ($formList as $key => $value) {
        ?>
        <tr id="tr_<?= $value ?>">
            <td><?= $key ?></td>
            <td style="padding: 0;">
                <?= $form->field($model, "$value" . "_scratch", ['options' => ['class' => 'm-0']])->dropDownList(['1' => 'Yes', '0' => 'No'], ['class' => 'form-control p-0 m-0 custom-dropdown scratch', 'prompt' => '', 'data-key' => $value])->label(false) ?>
            </td>
            <td style="padding: 0;">
                <?= $form->field($model, "$value" . "_rust", ['options' => ['class' => 'm-0']])->dropDownList(['1' => 'Yes', '0' => 'No'], ['class' => 'form-control p-0 m-0 custom-dropdown rust', 'prompt' => '', 'data-key' => $value])->label(false) ?>
            </td>
            <td style="padding: 0;">
                <?= $form->field($model, "$value" . "_color", ['options' => ['class' => 'm-0']])->dropDownList(TestFormVisualpaint::COLOR_TYPE, ['class' => 'form-control p-0 m-0 custom-dropdown', 'prompt' => ''])->label(false) ?>
            </td>
            <td style="padding: 0;">
                <?= $form->field($model, "$value" . "_finishing", ['options' => ['class' => 'wm-0']])->dropDownList(TestFormVisualpaint::FINISHING_TYPE, ['class' => 'form-control p-0 m-0 custom-dropdown', 'prompt' => ''])->label(false) ?>
            </td>
            <td style="padding: 0;">
                <?= $form->field($model, "$value" . "_remark", ['options' => ['class' => 'wm-0']])->textInput(['maxlenght' => true, 'class' => 'form-control p-0 m-0 custom-dropdown remark'])->label(false) ?>
            </td>
            <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                <td class="text-center vmiddle" style="padding: 0;">
                    <?= $form->field($model, "res_" . "$value", ['options' => ['class' => 'm-0']])->dropDownList([TestFormVisualpaint::RESULT_PASS['value'] => 'Pass', TestFormVisualpaint::RESULT_FAIL['value'] => 'Fail'], ['class' => 'form-control p-0 m-0 custom-dropdown', 'prompt' => '', 'data-key' => $value])->label(false) ?>
                    <span id="result-<?= $value ?>"></span>
                </td>
            <?php } ?>
        </tr>
    <?php }
    ?>
</table>
<script>
    $(document).ready(function () {
        $('.scratch, .rust').trigger('input');
    });

    $('.scratch, .rust').on('input', function () {
        var key = $(this).data('key');
        updateResult(key);
    });

    function updateResult(key) {
        var scratch = parseFloat($(`#tr_${key} .scratch`).val());
        var rust = parseFloat($(`#tr_${key} .rust`).val());
        if (isNaN(scratch) || isNaN(rust)) {
            $('#testformvisualpaint-res_' + key).val(null);
        } else {
            if (scratch === 1 || rust === 1) {
                $('#testformvisualpaint-res_' + key).val(<?= TestFormVisualpaint::RESULT_FAIL['value'] ?>);
            } else {
                $('#testformvisualpaint-res_' + key).val(<?= TestFormVisualpaint::RESULT_PASS['value'] ?>);
            }
        }
    }
</script>