<?php

use frontend\models\projectproduction\electrical\RefProjProdTaskElec;
?>
<style>
    .borderTopBottom {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
</style>
<div>
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
                            <input type="number" name="ProdElecTaskWeight[<?= $panel->id ?>][<?= $taskCode ?>][weight]" class="form-control text-right elecDept" value="<?= number_format($value, 2) ?>" data-key="<?= $taskCode ?>" step="any" min="0" oninput="validity.valid||(value='<?= number_format(0, 2) ?>')">
                        </td>
                    </tr>
                    <?php
                    $key++;
                endforeach;
                ?>
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
            <?php } else { ?>
                <tr>
                    <td colspan="3"><i>No task found</i></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>       
</div>
<script>
    $(document).ready(function () {
        updateTotalTaskWeight('elec');

        $(document).on('input', '.elecDept', function () {
            updateTotalTaskWeight('elec');
        });
    });

    function updateTotalTaskWeight(type) {
        let totalWeight = 0;

        $('.' + type + 'Dept').each(function () {
            let value = parseFloat($(this).val()) || 0;
            totalWeight += value;
        });

        $('#' + type + 'TotalWeight').text(totalWeight.toFixed(2));

        var totalTd = $('#' + type + 'TotalWeight').closest('td');
        totalTd.removeClass('bg-warning');
        $('.' + type + '-error-message').removeClass('text-danger');
        if (totalWeight > 100) {
            $('.' + type + '-error-message').addClass('text-danger');
            $('.' + type + '-error-message').text("Total weight cannot exceed 100%");
        } else if (totalWeight < 100) {
            totalTd.addClass('bg-warning');
            $('.' + type + '-error-message').text("Total weight is less than 100%");
        } else {
            $('.' + type + '-error-message').text("");
        }
    }
</script>
