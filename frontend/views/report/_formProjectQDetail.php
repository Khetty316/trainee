<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectQTypes;
?>
<style>
    .table thead th {
        text-align: center;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }
</style>
<div class="project-q-type-form">

    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
    ]);
    ?>

    <table class="table table-sm table-striped table-bordered col-lg-8 col-md-12 col-sm-12">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Project Type</th>
                <th>Fabrication (%)</th>
                <th>Electrical (%)</th>
                <th>Total (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($refProjectQTypes as $key => $projectQTypes) { ?>
                <tr id="tr_<?= $key ?>">
                    <td><?= $key + 1 ?></td>
                    <td><?= $projectQTypes['project_type_name'] ?></td>
                    <td class="text-right">
                        <?=
                                $form->field($projectQTypes, "[$key][code][$projectQTypes->code]fab_dept_percentage", ['options' => ['style' => 'margin: 0px;']])
                                ->input('number', ['step' => 'any', 'class' => 'form-control text-right fabDept', 'value' => number_format($projectQTypes->fab_dept_percentage, 2), 'data-key' => $key,
                                    'data-project-code' => $projectQTypes->code, 'min' => '0', 'oninput' => "validity.valid||(value='" . number_format(0, 2) . "')", 'type' => 'number'])
                                ->label(false)
                        ?>
                    </td>
                    <td class="text-right">
                        <?=
                                $form->field($projectQTypes, "[$key][code][$projectQTypes->code]elec_dept_percentage", ['options' => ['style' => 'margin: 0px;']])
                                ->input('number', ['step' => 'any', 'class' => 'form-control text-right elecDept', 'value' => number_format($projectQTypes->elec_dept_percentage, 2), 'data-key' => $key,
                                    'data-project-code' => $projectQTypes->code, 'min' => '0', 'oninput' => "validity.valid||(value='" . number_format(0, 2) . "')", 'type' => 'number'])
                                ->label(false)
                        ?>
                    </td>
                    <td class="text-right total">
                        <div class="totalPanelWeight">
                            <?php $totalpanelWeight = $projectQTypes->fab_dept_percentage + $projectQTypes->elec_dept_percentage; ?>
                            <?= number_format($totalpanelWeight, 2) ?>
                        </div>

                        <span class="panel-error-message"></span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="form-group col-lg-8 col-md-12 col-sm-12">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right px-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $('tr[id^="tr_"]').each(function () {
            var key = $(this).attr('id').split('_')[1];
            var type = $(this).find('.fabDept, .elecDept').data('project-code');
            updateTotal(key, type);
        });

        $(document).on('input', '.fabDept, .elecDept', function () {
            var key = $(this).data('key');
            var type = $(this).data('project-code');
            updateTotal(key, type);
        });
    });


    function updateTotal(key, type) {
        var fabDept = parseFloat($(`#tr_${key} .fabDept`).val()) || 0;
        var elecDept = parseFloat($(`#tr_${key} .elecDept`).val()) || 0;

        var total = fabDept + elecDept;
        $(`#tr_${key} .totalPanelWeight`).text(total.toFixed(2));

        var totalTd = $(`#tr_${key} .totalPanelWeight`).closest('td');
        totalTd.removeClass('isYellow');
        $(`#tr_${key} .panel-error-message`).removeClass('text-danger');
        if (total > 100) {
            $(`#tr_${key} .panel-error-message`).addClass('text-danger');
            $(`#tr_${key} .panel-error-message`).text("Total weight cannot exceed 100%");
        } else if (total < 100) {
            totalTd.addClass('isYellow');
            $(`#tr_${key} .panel-error-message`).text("Total weight is less than 100%");
        } else {
            $(`#tr_${key} .panel-error-message`).text("");
        }
    }

    $('#myForm').on('submit', function (e) {
        var totalPanelWeightErrors = $('.panel-error-message:contains("Total weight cannot exceed 100%")');

        if (totalPanelWeightErrors.length > 0) {
            e.preventDefault();
        }
    });
</script>
