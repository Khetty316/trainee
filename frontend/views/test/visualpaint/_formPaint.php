<?php

use frontend\models\test\TestFormVisualpaint;
use frontend\models\test\RefTestStatus;
?>

<table class="table table-sm table-bordered text-center">
    <tr>
        <th rowspan="2" style="width:18%;"></th>
        <th colspan="6">UNIT(μm)</th>
    </tr>
    <tr>
        <?php foreach (range('A', 'F') as $char) : ?>
            <th><?= $char ?></th>
        <?php endforeach; ?>
    </tr>
    <?php for ($i = 1; $i <= 3; $i++) : ?>
        <tr>
            <td>Measurement <?= $i ?></td>
            <?php foreach (range('a', 'f') as $char) : ?>
                <td style="padding: 0;">
                    <?= $form->field($model, "{$char}_measure{$i}", ['options' => ['class' => 'wm-0']])->input('number', ['step' => 'any', 'maxlength' => true, 'class' => "form-control p-0 m-0 custom-dropdown measurement"])->label(false) ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endfor; ?>
    <tr>
        <td>Average</td>
        <?php foreach (range('a', 'f') as $char) : ?>
            <td style="padding: 0;">
                <?= $form->field($model, "{$char}_average", ['options' => ['class' => 'm-0']])->input('number', ['step' => 'any', 'maxlength' => true, 'class' => 'form-control p-0 m-0 custom-dropdown avg'])->label(false) ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
        <tr>
            <td>Result</td>
            <?php foreach (range('a', 'f') as $char) : ?>
                <td style="padding: 0;">
                    <?= $form->field($model, "res_ave_{$char}", ['options' => ['class' => 'm-0']])->dropDownList(['1' => 'Pass', '0' => 'Fail'], ['class' => 'form-control p-0 m-0 custom-dropdown', 'prompt' => '', 'data-key' => "res_ave_{$char}"])->label(false) ?>
                    <span id="resAve<?= $char ?>"></span>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php } ?>
</table>

<script>
    $(document).ready(function () {
        $('.measurement').on('input', function () {
            calculateAverage($(this));
        });
    });

    function calculateAverage(input) {
        var id = input.attr('id');
        var position = id.substring(20, id.length - 9);
        var total = 0;
        var allFilled = true;
        var threshold = <?= $model->treshold_a ?>;

        for (var i = 1; i <= 3; i++) {
            var measureInput = $('#testformvisualpaint-' + position + '_measure' + i);
            var value = parseFloat(measureInput.val());

            if (isNaN(value)) {
                allFilled = false;
                break;
            }

            total += value;
        }

        if (allFilled) {
            var average = total / 3;
            $('#testformvisualpaint-' + position + '_average').val(average.toFixed(2));
            if (average >= threshold) {
                $('#testformvisualpaint-res_ave_' + position).val(<?= TestFormVisualpaint::RESULT_PASS['value'] ?>);
            } else {
                $('#testformvisualpaint-res_ave_' + position).val(<?= TestFormVisualpaint::RESULT_FAIL['value'] ?>);
            }
        } else {
            $('#testformvisualpaint-' + position + '_average').val(null);
            $('#testformvisualpaint-res_ave_' + position).val(null);
        }
    }
</script>
