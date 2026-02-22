<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'errorOptions' => ['class' => 'invalid-feedback-show'],
    ],
        ]);
?>
<div class="row">
    <div class="col-lg-2 col-md-3 col-sm-6">
        <?=
        $form->field($model, 'selectedMonth')->dropDownList([
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

    <div class="col-lg-2 col-md-3 col-sm-6">
        <?=
        $form->field($model, 'selectedYear')->dropDownList(
                array_combine(
                        range(2020, date('Y')),
                        range(2020, date('Y'))
                ),
                [
                    'class' => 'form-control',
                    'onchange' => 'updatePeriodFromMonthYear()'
                ]
        )->label("Year");
        ?>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-12">
        <label>Selected Period</label>
        <div class="form-control" id="selected-period-display" style="background-color: #f8f9fa;">
            Please select month and year
        </div>
        <?= $form->field($model, 'dateFrom')->hiddenInput(['class' => 'form-control'])->label(false) ?>
        <?= $form->field($model, 'dateTo')->hiddenInput(['class' => 'form-control'])->label(false) ?>
    </div>

    <div class="col-lg-2 col-md-12 col-sm-12">
        <?=
        $form->field($model, 'is_internalProject')->dropDownList([
            '' => 'All Projects',
            '1' => 'Internal Projects',
            '0' => 'External Projects'
                ], [
            'class' => 'form-control'
        ])->label("Project Type");
        ?>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="form-group">
            <label>&nbsp;</label> 
            <div>
                <?= Html::a('Reset', '?', ['class' => 'btn btn-secondary mr-1']) ?>
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>                   
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        updatePeriodFromMonthYear();
    });

    function updatePeriodFromMonthYear() {
        var month = document.querySelector('select[name="ReportingModel[selectedMonth]"]').value;
        var year = document.querySelector('select[name="ReportingModel[selectedYear]"]').value;

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
        var hiddenDateFrom = document.querySelector('input[name="ReportingModel[dateFrom]"]');
        var hiddenDateTo = document.querySelector('input[name="ReportingModel[dateTo]"]');

        if (hiddenDateFrom)
            hiddenDateFrom.value = dateFrom;
        if (hiddenDateTo)
            hiddenDateTo.value = dateTo;
    }
</script>
