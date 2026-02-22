<?php

use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectQTypes;
?>
<style>
    .borderTopBottom {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
</style>
<div class="task-weight-form">
    <h5>Panel Weight</h5>
    <table class="table table-sm table-striped table-bordered">
        <thead class="thead-light text-center">
            <tr>
                <th>Project Type</th>
                <th>Fabrication (%)</th>
                <th>Electrical (%)</th>
                <th>Total (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr id="tr_<?= $panel->id ?>">
                <td class="text-center">
                    <?= $panel->panelType->project_type_name ?>
                </td>
                <td class="text-right">
                    <input type="number" name="ProdFabPanelWeight[<?= $panel->id ?>][panel_type_weight][weight]" class="form-control text-right fabPanel" value="<?= number_format($fabPanelWeight, 2) ?>" data-key="<?= $panel->id ?>" data-project-code="<?= $panel->panel_type ?>" step="any" min="0" oninput="validity.valid||(value='<?= number_format(0, 2) ?>')">
                    <span class="fabPanel-error-message text-danger"></span>
                </td>
                <td class="text-right">
                    <input type="number" name="ProdElecPanelWeight[<?= $panel->id ?>][panel_type_weight][weight]" class="form-control text-right elecPanel" value="<?= number_format($elecPanelWeight, 2) ?>" data-key="<?= $panel->id ?>" data-project-code="<?= $panel->panel_type ?>" step="any" min="0" oninput="validity.valid||(value='<?= number_format(0, 2) ?>')">
                    <span class="elecPanel-error-message text-danger"></span>
                </td>
                <td class="text-right">
                    <div class="totalPanelWeight">
                        <?php $totalpanelWeight = $fabPanelWeight + $elecPanelWeight; ?>
                        <?= number_format($totalpanelWeight, 2) ?>
                    </div>
                    <span class="panel-error-message"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('tr[id^="tr_"]').each(function () {
            var key = $(this).attr('id').split('_')[1];
            var type = $(this).find('.fabPanel, .elecPanel').data('project-code');
            validatePanelWeight(key, type);
        });

        $(document).on('input', '.fabPanel, .elecPanel', function () {
            var key = $(this).data('key');
            var type = $(this).data('project-code');
            validatePanelWeight(key, type);
        });

    });

    function validatePanelWeight(key, type) {
        var fabDept = parseFloat($(`#tr_${key} .fabPanel`).val()) || 0;
        var elecDept = parseFloat($(`#tr_${key} .elecPanel`).val()) || 0;

        var total = fabDept + elecDept;
        $(`#tr_${key} .totalPanelWeight`).text(total.toFixed(2));

        var totalTd = $(`#tr_${key} .totalPanelWeight`).closest('td');
        totalTd.removeClass('bg-warning');
        $(`#tr_${key} .panel-error-message`).removeClass('text-danger');
        if (total > 100) {
            $(`#tr_${key} .panel-error-message`).addClass('text-danger');
            $(`#tr_${key} .panel-error-message`).text("Total weight cannot exceed 100%");
        } else if (total < 100 && (type !== '<?= RefProjectQTypes::CODE_SERV ?>' && type !== '<?= RefProjectQTypes::CODE_TRADE ?>')) {
            totalTd.addClass('bg-warning');
            $(`#tr_${key} .panel-error-message`).text("Total weight is less than 100%");
        } else {
            $(`#tr_${key} .panel-error-message`).text("");
        }
    }
</script>
