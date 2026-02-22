<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prod-ot-meal-record-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-6">
            <?=
        $form->field($model, 'month')->dropDownList([
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
                ], [
            'class' => 'form-control',
            'prompt' => 'Select Month...',
            'onchange' => 'updatePeriodFromMonthYear()'
        ])->label("Month");
        ?>
        </div>
        <div class="col-6">
    <?=
        $form->field($model, 'year')->dropDownList(
                array_combine(
                        range(2025, date('Y')),
                        range(2025, date('Y'))
                ),
                [
                    'class' => 'form-control',
                    'onchange' => 'updatePeriodFromMonthYear()'
                ]
        )->label("Year");
        ?>
        </div>
        <div class="col-12">
    <label>Selected Period</label>
        <div class="form-control" id="selected-period-display" style="background-color: #f8f9fa;">
            Please select month and year
        </div>
        <?= $form->field($model, 'dateFrom')->hiddenInput(['class' => 'form-control'])->label(false) ?>
        <?= $form->field($model, 'dateTo')->hiddenInput(['class' => 'form-control'])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).ready(function () {
        updatePeriodFromMonthYear();
    });

    function updatePeriodFromMonthYear() {
        var month = document.querySelector('select[name="ProdOtMealRecordMaster[month]"]').value;
        var year = document.querySelector('select[name="ProdOtMealRecordMaster[year]"]').value;

        if (month && year) {
            var monthNum = parseInt(month);
            var yearNum = parseInt(year);

            // Calculate start date (23rd of previous month)
            var startMonth = monthNum - 1;
            var startYear = yearNum;

            if (startMonth === 0) {
                // January: start from December of previous year
                startMonth = 12;
                startYear = yearNum - 1;
            }

            var startDate = new Date(startYear, startMonth - 1, 23);

            // Calculate end date (22nd of selected month)
            var endDate = new Date(yearNum, monthNum - 1, 22);

            // Format dates for display (dd/mm/yyyy)
            var startFormatted = startDate.getDate().toString().padStart(2, '0') + '/' +
                    String(startDate.getMonth() + 1).padStart(2, '0') + '/' +
                    startDate.getFullYear();
            var endFormatted = endDate.getDate().toString().padStart(2, '0') + '/' +
                    String(endDate.getMonth() + 1).padStart(2, '0') + '/' +
                    endDate.getFullYear();

            document.getElementById('selected-period-display').textContent =
                    startFormatted + ' to ' + endFormatted;

            // Set hidden date fields (yyyy-mm-dd format)
            setHiddenDateFields(
                    startDate.getFullYear() + '-' +
                    String(startDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(startDate.getDate()).padStart(2, '0'),
                    endDate.getFullYear() + '-' +
                    String(endDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(endDate.getDate()).padStart(2, '0')
                    );
        }
    }

    function setHiddenDateFields(dateFrom, dateTo) {
        var hiddenDateFrom = document.querySelector('input[name="ProdOtMealRecordMaster[dateFrom]"]');
        var hiddenDateTo = document.querySelector('input[name="ProdOtMealRecordMaster[dateTo]"]');

        if (hiddenDateFrom)
            hiddenDateFrom.value = dateFrom;
        if (hiddenDateTo)
            hiddenDateTo.value = dateTo;
    }
</script>