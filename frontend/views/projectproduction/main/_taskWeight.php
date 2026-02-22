<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\projectproduction\electrical\RefProjProdTaskElec;
?>
<style>
    .borderTopBottom {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
</style>
<div class="task-weight-form">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myFinalizeForm',
    ]);
    ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0 ">Panel Weight:</legend>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 order-md-1 mt-2">
                <?php
                foreach ($panels as $panel) {
                    $panelWeight = frontend\models\common\RefProjectQTypes::getPanelWeight($panel->panel_type);
                }
                ?>

                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Fabrication (%)</th>
                            <th>Electrical (%)</th>
                            <th>Total (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="allDept">
                            <td class="text-right">
                                <input type="number" name="ProdFabPanelWeight[panel_type_weight][weight]" class="form-control text-right fabPanel" value="<?= number_format($panelWeight->fab_dept_percentage, 2) ?>" data-key="panelFab">
                            </td>
                            <td class="text-right">
                                <input type="number" name="ProdElecPanelWeight[panel_type_weight][weight]" class="form-control text-right elecPanel" value="<?= number_format($panelWeight->elec_dept_percentage, 2) ?>" data-key="panelElec">
                            </td>
                            <td class="text-right">
                                <div id="totalPanelWeight">
                                    <?php $totalpanelWeight = $panelWeight->fab_dept_percentage + $panelWeight->elec_dept_percentage; ?>
                                    <?= number_format($totalpanelWeight, 2) ?>
                                </div>
                                <!--<span class="panel-error-message text-danger"></span>-->
                                <span class="panel-error-message text-danger"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </fieldset>


    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0 ">Task Weight:</legend>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 order-md-1 mt-2">
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th class="text-center col-1" colspan="3">Fabrication</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Task</th>
                            <th>Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($fabTaskWeight !== null) {
                            $fabTotalWeight = 0;
                            $key = 0;
                            foreach ($fabTaskWeight as $taskCode => $value):
                                $fabTotalWeight += $value;
                                ?>
                                <?php $taskName = RefProjProdTaskFab::getTaskName($taskCode); ?>
                                <tr>
                                    <td class="text-center"><?= $key + 1 ?></td>
                                    <td><?= $taskName ?></td>
                                    <td class="text-right">
                                        <input type="number" name="ProdFabTaskWeight[<?= $taskCode ?>][weight]" class="form-control text-right fabDept" value="<?= number_format($value, 2) ?>" data-key="<?= $taskCode ?>">
                                    </td>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                            ?>
                            <tr class="spacer-row" style="height: 1px"></tr>
                            <tr>
                                <th colspan="2" class="text-right borderTopBottom">Total Weight (%):</th>
                                <th class="text-right borderTopBottom pr-3">
                                    <div id="fabTotalWeight">
                                        <?= number_format($fabTotalWeight, 2) ?>
                                    </div>
                                    <span class="fab-error-message text-danger"></span>
                                </th>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3"><i>No task found</i></td>                       
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 order-md-1 mt-2">
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light text-center">
                        <tr><th class="text-center col-1" colspan="3">Electrical</th></tr>
                        <tr>
                            <th>#</th>
                            <th>Task</th>
                            <th>Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($elecTaskWeight !== null) {
                            $elecTotalWeight = 0;
                            $key = 0;
                            foreach ($elecTaskWeight as $taskCode => $value):
                                $elecTotalWeight += $value;
                                ?>
                                <?php $taskName = RefProjProdTaskElec::getTaskName($taskCode); ?>
                                <tr>
                                    <td class="text-center"><?= $key + 1 ?></td>
                                    <td><?= $taskName ?></td>
                                    <td class="text-right">
                                        <input type="number" name="ProdElecTaskWeight[<?= $taskCode ?>][weight]" class="form-control text-right elecDept" value="<?= number_format($value, 2) ?>" data-key="<?= $taskCode ?>">
                                    </td>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                            ?>
                            <tr class="spacer-row" style="height: 1px"></tr>
                            <tr>
                                <th colspan="2" class="text-right borderTopBottom">Total Weight (%):</th>
                                <th class="text-right borderTopBottom pr-3">
                                    <div id="elecTotalWeight">
                                        <?= number_format($elecTotalWeight, 2) ?>
                                    </div>
                                    <span class="elec-error-message text-danger"></span>
                                </th>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3"><i>No task found</i></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>

    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).on('input', '.fabPanel, .elecPanel', function () {
        updateTotalPanelWeight();
    });

    function updateTotalPanelWeight() {
        var fabDept = parseFloat($(`.fabPanel`).val()) || 0;
        var elecDept = parseFloat($(`.elecPanel`).val()) || 0;

        var total = fabDept + elecDept;

        var errorMessageElement = $(`.panel-error-message`);

        if (total > 100) {
            $(`#totalPanelWeight`).text(total.toFixed(2));
            errorMessageElement.text("Total cannot exceed 100%");
        } else {
            errorMessageElement.text("");
            $(`#totalPanelWeight`).text(total.toFixed(2));
        }
    }

    $(document).on('input', '.fabDept', function () {
        updateTotalTaskWeight('fab');
    });

    $(document).on('input', '.elecDept', function () {
        updateTotalTaskWeight('elec');
    });

    function updateTotalTaskWeight(type) {
        let totalWeight = 0;

        $('.' + type + 'Dept').each(function () {
            let value = parseFloat($(this).val()) || 0;
            totalWeight += value;
        });

        if (totalWeight > 100) {
            $('#' + type + 'TotalWeight').text(totalWeight.toFixed(2));
            $('.' + type + '-error-message').text("Total weight cannot exceed 100%");
        } else {
            $('#' + type + 'TotalWeight').text(totalWeight.toFixed(2));
            $('.' + type + '-error-message').text("");
        }
    }
</script>
