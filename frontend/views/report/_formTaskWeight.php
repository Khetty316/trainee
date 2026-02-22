<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<style>
    .table thead th {
        text-align: center;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .borderTopBottom {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
</style>
<div class="task-weight-form"><?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
    ]);
    ?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <h5>Project Type: <?= $refProjectTypes->project_type_name ?></h5>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0 ">Fabrication</legend>
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Task</th>
                            <th>Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fabTotalWeight = 0;
                        foreach ($refFabTask as $key => $fab) {
                            ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $fab['name'] ?></td>
                                <td class="text-right">
                                    <?php
                                    foreach ($refTaskWeightFab as $attribute) {
                                        if ($attribute->task_code == $fab->code) {
                                            $fab->weight = $attribute->task_weight;
                                            $fabTotalWeight += $attribute->task_weight;
                                        }
                                        ?>

                                    <?php } ?>
                                    <?=
                                            $form->field($fab, "[$key][$fab->code]weight", ['options' => ['style' => 'margin: 0px;']])
                                            ->input('number', [
                                                'step' => 'any',
                                                'class' => 'form-control text-right fabDept',
                                                'value' => number_format($fab->weight, 2),
                                                'data-key' => $key,
                                                'min' => '0',
                                                'oninput' => "validity.valid||(value='" . number_format(0, 2) . "')",
                                                'type' => 'number'
                                            ])
                                            ->label(false)
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="spacer-row" style="height: 1px"></tr>
                        <tr>
                            <th colspan="2" class="text-right borderTopBottom">Total Weight (%):</th>
                            <td class="text-right borderTopBottom pr-3">
                                <div id="fabTotalWeight">
                                    <?= number_format($fabTotalWeight, 2) ?>
                                </div>
                                <span class="fab-error-message"></span>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </fieldset>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Electrical</legend>
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Task</th>
                            <th>Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $elecTotalWeight = 0;
                        foreach ($refElecTask as $key => $elec) {
                            ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $elec['name'] ?></td>
                                <td class="text-right">
                                    <?php
                                    foreach ($refTaskWeightElec as $attribute) {
                                        if ($attribute->task_code == $elec->code) {
                                            $elec->weight = $attribute->task_weight;
                                            $elecTotalWeight += $attribute->task_weight;
                                        }
                                        ?>

                                    <?php } ?>
                                    <?=
                                            $form->field($elec, "[$key][$elec->code]weight", ['options' => ['style' => 'margin: 0px;']])
                                            ->input('number', [
                                                'step' => 'any',
                                                'class' => 'form-control text-right elecDept',
                                                'value' => number_format($elec->weight, 2),
                                                'data-key' => $key,
                                                'min' => '0',
                                                'oninput' => "validity.valid||(value='" . number_format(0, 2) . "')",
                                                'type' => 'number'
                                            ])
                                            ->label(false)
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="spacer-row" style="height: 1px"></tr>
                        <tr>
                            <th colspan="2" class="text-right borderTopBottom">Total Weight (%):</th>
                            <td class="text-right borderTopBottom pr-3">
                                <div id="elecTotalWeight">
                                    <?= number_format($elecTotalWeight, 2) ?>
                                </div>
                                <span class="elec-error-message"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3 float-right']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        updateTotalWeight('fab');
        updateTotalWeight('elec');
    });

    $(document).on('input', '.fabDept', function () {
        updateTotalWeight('fab');
    });

    $(document).on('input', '.elecDept', function () {
        updateTotalWeight('elec');
    });

    function updateTotalWeight(type) {
        let totalWeight = 0;

        $('.' + type + 'Dept').each(function () {
            let value = parseFloat($(this).val()) || 0;
            totalWeight += value;
        });

        $('#' + type + 'TotalWeight').text(totalWeight.toFixed(2));

        var totalTd = $('#' + type + 'TotalWeight').closest('td');
        totalTd.removeClass('isYellow');
        $('.' + type + '-error-message').removeClass('text-danger');
        if (totalWeight > 100) {
            $('.' + type + '-error-message').addClass('text-danger');
            $('.' + type + '-error-message').text("Total weight cannot exceed 100%");
        } else if (totalWeight < 100) {
            totalTd.addClass('isYellow');
            $('.' + type + '-error-message').text("Total weight is less than 100%");
        } else {
            $('.' + type + '-error-message').text("");
        }
    }

    $('#myForm').on('submit', function (e) {
        var fabErrors = $('.fab-error-message:contains("Total weight cannot exceed 100%")');
        var elecErrors = $('.elec-error-message:contains("Total weight cannot exceed 100%")');

        if (fabErrors.length > 0 || elecErrors.length > 0) {
            e.preventDefault();
        }
    });

</script>
