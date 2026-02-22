<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use common\models\myTools\MyFormatter;
use yii\helpers\ArrayHelper;
use common\models\User;
use frontend\models\projectproduction\task\TaskAssignment;

$this->title = 'Report - Department Performance';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="app">
    <h3><?= Html::encode($this->title) ?></h3>
    <div class="work-assignment-master-form">
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
                            ->dropdownList([TaskAssignment::taskTypeElectrical => 'Electrical', TaskAssignment::taskTypeFabrication => 'Fabrication'])
                            ->label("Department");
                    ?>
                </div>

            </div>
        </div>
        <div class="form-group my-0">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <?=
                            $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                            ->widget(DatePicker::className(), [
                                'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                                'dateFormat' => 'dd/MM/yyyy',
                                'clientOptions' => [
                                    'showButtonPanel' => true,
                                    'closeText' => 'Close',
                                ],
                            ])->label("Complete Date From");
                    ?>
                </div>

            </div>
        </div>

        <div class="form-group my-0">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">

                    <?=
                            $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                            ->widget(DatePicker::className(), [
                                'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                                'dateFormat' => 'dd/MM/yyyy',
                                'clientOptions' => [
                                    'showButtonPanel' => true,
                                    'closeText' => 'Close',
                                ],
                            ])->label("Complete Date To");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group my-0">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <?=
                            $form->field($model, 'is_internalProject')
                            ->dropDownList(\frontend\models\report\ReportingModel::PROJECT_TYPE_OPTIONS)
                            ->label("Project Type");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div v-if="models && models.length > 0">
            <div class="col-md-8">
                <?php
                $form = ActiveForm::begin([
                ]);
                ?>
                <?php if (!empty($reportDetail)) { ?>
                    <?=
                    Html::a(
                            'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
                            '#',
                            [
                                'class' => 'btn btn-primary float-right mb-3',
                                'id' => 'exportCsvButton',
                            ]
                    )
                    ?>
                    <?php ActiveForm::end(); ?>
                    <table class="table table-bordered table-striped table-hover mt-2 col-12 rounded">
                        <thead>
                            <tr class="text-primary">
                                <th @click="sortTable('staffId')" class="search-hover col-1">Staff ID</th>
                                <th @click="sortTable('fullname')" class="search-hover col-2">Fullname</th>
                                <th @click="sortTable('totalPerformance')" class="search-hover col-1">Amount (RM)</th>
                            </tr>
                            <tr>
                                <th class="p-1"><input class="form-control" v-model="searchCriteria.staffId" type="number"></th>
                                <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
                                <th class="p-1"><input class="form-control" v-model="searchCriteria.totalPerformance" type="number"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="model in filteredModels" :key="model.id">
                                <td class="p-1">{{ model.staffId }}</td>
                                <td class="p-1">{{ model.fullname }}</td>
                                <td class="p-1 text-right">
                                    <?php
                                    $form = ActiveForm::begin([
                                                'id' => 'redirectForm',
                                                'action' => ['/reporting/get-individual-performance'],
                                                'method' => 'post',
                                                'options' => ['class' => 'p-0 m-0', 'target' => 'blank']
                                    ]);
                                    ?>
                                    <?= $form->field($model, 'userId', ['options' => ['class' => 'm-0']])->hiddenInput([':value' => "model.id"])->label(false) ?>
                                    <?= $form->field($model, 'dateFrom', ['options' => ['class' => 'm-0']])->hiddenInput()->label(false) ?>
                                    <?= $form->field($model, 'dateTo', ['options' => ['class' => 'm-0']])->hiddenInput()->label(false) ?>
                                    <?= $form->field($model, 'is_internalProject', ['options' => ['class' => 'm-0']])->hiddenInput()->label(false) ?>

                                    <div class="form-group p-0 m-0">
                                        <?= Html::submitButton('', ['class' => 'btn btn-link p-0', 'v-text' => 'formatDecimalNum(model.totalPerformance)']) ?>
                                    </div>
                                    <?php ActiveForm::end(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="2"><strong>Total:</strong></td>
                                <td class="text-right"><strong>MYR <span id="totalPerformanceSum"></span></strong></td>
                            </tr>
                        </tbody>
                    </table>                
                    <?php
                } else {
                    echo "<div class='tdnowrap'>-- No Record --</div>";
                }
                ?>
            </div>
        </div>

    </div>
</div>

<script>
    window.models = <?= $reportDetail ?>;
    const totalSum = calculateTotalPerformanceSum(<?= $reportDetail ?>);
    document.getElementById('totalPerformanceSum').innerText = totalSum;

    function calculateTotalPerformanceSum(models) {
        let sum = 0;
        for (let i = 0; i < models.length; i++) {
            sum += parseFloat(models[i].totalPerformance);
        }
        return sum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    $(document).ready(function () {
        $('#exportCsvButton').on('click', function (e) {
            var reportDetail = <?= json_encode($reportDetail) ?>;
            var totalPerformanceAmount = totalSum;

            $.ajax({
                url: '/reporting/export-to-excel-department-performance-report',
                type: 'POST',
                data: {
                    reportDetail: reportDetail,
                    totalPerformanceAmount: totalPerformanceAmount,
                    _csrf: yii.getCsrfToken()
                },
                success: function (response) {
                    var department = "<?= $model->department ?>";
                    var dateFrom = "<?= $model->dateFrom ?>";
                    var dateTo = "<?= $model->dateTo ?>";
                    var departmentName = (department === 'fab' ? 'Fabrication' : 'Electrical');
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