<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use frontend\models\projectproduction\task\TaskAssignment;
use frontend\models\report\ReportingModel;

$this->title = 'Incentive - Factory Staff';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="app">
    <?= $this->render('_hrNavBarIncentive', ['pageKey' => '1']) ?>

    <div class="work-assignment-master-form mt-3">
        <?php
        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-6\">{input}\n{error}</div>",
                'labelOptions' => ['class' => 'col-sm-6 control-label'],
            ],
            'options' => ['autocomplete' => 'off']
        ]);
        ?>
        <div class="form-group my-0">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <?=
                            $form->field($model, 'department', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                            ->dropdownList([null => 'All', TaskAssignment::taskTypeElectrical => 'Electrical', TaskAssignment::taskTypeFabrication => 'Fabrication'])
                            ->label("Department");
                    ?>
                </div>
            </div>
        </div>
        <?=
        $this->renderAjax('/site/_monthForm', [
            'model' => $model
        ])
        ?>

        <?php ActiveForm::end(); ?>

        <div class="row">
            <div class="col-md-8 col-lg-8">
                <div v-if="models && models.length > 0">
                    <?php
                    $form = ActiveForm::begin([
                    ]);
                    ?>
                    <?php
                    if (!empty($reportDetail)) {
                        ?>
                        <?=
                        Html::a(
                                'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
                                '#',
                                [
                                    'class' => 'btn btn-primary float-right mb-3 mt-4',
                                    'id' => 'exportCsvButton',
                                ]
                        )
                        ?>
                        <?php ActiveForm::end(); ?>
                        <table class="table table-bordered table-striped table-hover mt-2 rounded col-12">
                            <thead>
                                <tr class="text-primary">
                                    <th @click="sortTable('staffId')" class="search-hover col-1">Staff ID</th>
                                    <th @click="sortTable('fullname')" class="search-hover col-2">Fullname</th>
                                    <th @click="sortTable('totalAssignedAmount')" class="search-hover col-1 text-right">Pending Work (RM)</th>
                                    <th @click="sortTable('totalPerformance')" class="search-hover col-1 text-right">Contribution (RM)</th>
                                    <th @click="sortTable('incentiveAmount')" class="search-hover col-1 text-right">Incentive (RM)</th>
                                </tr>
                                <tr>
                                    <th class="p-1"><input class="form-control" v-model="searchCriteria.staffId" type="number"></th>
                                    <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
                                    <th class="p-1 text-right"><input class="form-control" v-model="searchCriteria.totalPendingWorkAmount" type="number"></th>
                                    <th class="p-1 text-right"><input class="form-control" v-model="searchCriteria.totalPerformance" type="number"></th>
                                    <th class="p-1 text-right"><input class="form-control" v-model="searchCriteria.incentiveAmount" type="number"></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="model in filteredModels" :key="model.id">
                                    <td class="p-1">{{ model.staffId }}</td>
                                    <td class="p-1">{{ model.fullname }}</td>
                                    <td class="p-1 text-right">{{ formatDecimalNum(model.totalPendingWorkAmount) }}</td>
                                    <td class="p-1 text-right">{{ formatDecimalNum(model.totalPerformance) }}</td>
                                    <td class="p-1 text-right">{{ formatDecimalNum(model.incentiveAmount) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2"><strong>Total:</strong></td>
                                    <td class="text-right"><strong>MYR <span id="totalPendingWorkAmountSum"></span></strong></td>
                                    <td class="text-right"><strong>MYR <span id="totalPerformanceSum"></span></strong></td>
                                    <td class="text-right"><strong>MYR <span id="totalIncentiveSum"></span></strong></td>
                                </tr>
                            </tbody>
                        </table>                
                        <?php
                    } else {
                        echo "<div class='tdnowrap'>-- No Record --</div>";
                    }
                    ?>
                </div>
                <div v-if="models.length == 0">
                    <div class='tdnowrap mt-5'>-- No Record --</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <table class="table table-bordered table-striped table-hover mt-2 col-12">
                    <thead>
                        <tr>
                            <th colspan="4" class="text-center">Payment Scale</th> <!-- Changed to 4 columns -->
                        </tr>
                        <tr>
                            <th class="text-center">Tier</th>
                            <th class="text-right">Contribution Range (RM)</th>
                            <th class="text-right">Incentive (RM)</th>
                            <th class="text-right">Total Incentive (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tiers = ReportingModel::FACTORY_STAFF_PERFORMANCE_INCENTIVE;
                        $tierNumber = 1;
                        $cumulativeIncentive = 0;

                        foreach ($tiers as $index => $tier):
                            $cumulativeIncentive += $tier['incentive'];

                            // Create range display
                            if ($index === 0) {
                                $range = number_format($tier['threshold']) . ' - ' . number_format($tiers[$index + 1]['threshold'] - 1);
                            } else {
                                $range = number_format($tier['threshold']) . '+';
                            }
                            ?>
                            <tr>
                                <td class="p-1 text-center"><?= $tierNumber ?></td>
                                <td class="p-1 text-right"><?= $range ?></td>
                                <td class="p-1 text-right"><?= number_format($tier['incentive'], 2) ?></td>
                                <td class="p-1 text-right"><?= number_format($cumulativeIncentive, 2) ?></td>
                            </tr>
                            <?php
                            $tierNumber++;
                        endforeach;
                        ?>
                    </tbody>
                </table>

            </div>

        </div>

    </div>
</div>

<script>
    window.models = <?= $reportDetail ?>;

    const totalPendingWorkAmountSum = calculateTotalPendingWorkAmountSum(<?= $reportDetail ?>);
    document.getElementById('totalPendingWorkAmountSum').innerText = totalPendingWorkAmountSum;

    function calculateTotalPendingWorkAmountSum(models) {
        let sum = 0;
        for (let i = 0; i < models.length; i++) {
            sum += parseFloat(models[i].totalPendingWorkAmount);
        }
        return sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    const totalSum = calculateTotalPerformanceSum(<?= $reportDetail ?>);
    document.getElementById('totalPerformanceSum').innerText = totalSum;
    function calculateTotalPerformanceSum(models) {
        let sum = 0;
        for (let i = 0; i < models.length; i++) {
            sum += parseFloat(models[i].totalPerformance);
        }
        return sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    const totalSumIncentive = calculateTotalIncentiveSum(<?= $reportDetail ?>);
    document.getElementById('totalIncentiveSum').innerText = totalSumIncentive;
    function calculateTotalIncentiveSum(models) {
        let sum = 0;
        for (let i = 0; i < models.length; i++) {
            sum += parseFloat(models[i].incentiveAmount);
        }
        return sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    $(document).ready(function () {
        $('#exportCsvButton').on('click', function (e) {
            var reportDetail = <?= json_encode($reportDetail) ?>;
            var totalPendingWorkAmount = totalPendingWorkAmountSum;
            var totalPerformanceAmount = totalSum;
            var totalIncentiveAmount = totalSumIncentive;

            $.ajax({
                url: '/working/hr-employee-incentive/export-to-excel-department-performance-report',
                type: 'POST',
                data: {
                    reportDetail: reportDetail,
                    totalPendingWorkAmount: totalPendingWorkAmount,
                    totalPerformanceAmount: totalPerformanceAmount,
                    totalIncentiveAmount: totalIncentiveAmount,
                    _csrf: yii.getCsrfToken()
                },
                success: function (response) {
                    var department = "<?= $model->department ?>";
                    var dateFrom = "<?= $model->dateFrom ?>";
                    var dateTo = "<?= $model->dateTo ?>";
                    var departmentName = (department !== "" ? (department === 'fab' ? 'Fabrication' : 'Electrical') : 'All');
                    var filename = 'Performance Report - ' + departmentName + ' Department from ' + dateFrom + ' to ' + dateTo + '.xls';
                    var blob = new Blob([response], {type: 'application/vnd.ms-excel'});
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    URL.revokeObjectURL(link.href);
                },
                error: function () {
                    alert('There was an error generating the file.');
                }
            });
        });
    });
</script>
<script src="\js\vueTable.js"></script>