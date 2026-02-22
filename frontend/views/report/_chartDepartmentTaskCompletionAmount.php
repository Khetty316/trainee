<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;
use yii\bootstrap4\ActiveForm;

$this->title = 'Pushed-to-Completed Tracker Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="chart-department-task-completion">

    <?php
    //=
//    $this->render('_dateForm', [
//        'model' => $model
//    ])
    ?>
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
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                        $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Pushed Date From");
                ?>
            </div>

        </div>
    </div>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">

                <?=
                        $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Pushed Date To");
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
                <?= Html::a('Reset', '?', ['class' => 'btn btn-primary float-right mr-1']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div class="row">
        <div class="col-12 text-center mt-5 mb-3">
            <h5>Pushed-to-Completed Value Realization Tracker</h5>
            <h6>Monetary Value of Completed Panels (RM) vs. Total Number of Panel Pushed to Production Over Selected Period </h6>
        </div>
        <div class="col-6 text-center">
            <h5>Electrical</h5>
            <p>Total Task Assigned: RM <?= MyFormatter::asDecimal2($elecTaskAssignedAmount ?? 0) ?></p>
            <canvas id="elecChart"></canvas>
            <p>Total: RM <?= MyFormatter::asDecimal2($totalElec) ?></p>
        </div>
        <div class="col-6 text-center">
            <h5>Fabrication</h5>
            <p>Total Task Assigned: RM <?= MyFormatter::asDecimal2($fabTaskAssignedAmount ?? 0) ?></p>
            <canvas id="fabChart"></canvas>
            <p>Total: RM <?= MyFormatter::asDecimal2($totalFab) ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center mt-5 mb-3">
            <h5>Pushed-to-Completed Volume Realization Tracker</h5>
            <h6>Number of Completed Panels vs. Total Number of Panel Pushed to Production Over Selected Period </h6>
        </div>
        <div class="col-6 text-center">
            <h5>Electrical</h5>
            <canvas id="elecChartPercent"></canvas>
        </div>
        <div class="col-6 text-center">
            <h5>Fabrication</h5>
            <canvas id="fabChartPercent"></canvas>
        </div>
    </div>
    <script>
        var ctx = document.getElementById('elecChart').getContext('2d');
        var elecData = <?= $electricalData ?>;
        var elecChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: elecData.labels,
                datasets: [{
                        data: elecData.datasets[0].data,
                        backgroundColor: elecData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (valueElec) => {
                            return "RM " + parseFloat(valueElec).toLocaleString("en-MY", {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
    <script>
        var ctx = document.getElementById('fabChart').getContext('2d');
        var fabData = <?= $fabricationData ?>;
        var fabChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: fabData.labels,
                datasets: [{
                        data: fabData.datasets[0].data,
                        backgroundColor: fabData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (valueFab) => {
                            return "RM " + parseFloat(valueFab).toLocaleString("en-MY", {minimumFractionDigits: 2, maximumFractionDigits: 2});

                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
    <script>
        var ctx = document.getElementById('elecChartPercent').getContext('2d');
        var elecData = <?= $electricalData2 ?>;
        var elecChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: elecData.labels,
                datasets: [{
                        data: elecData.datasets[0].data,
                        backgroundColor: elecData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.dataset._meta[Object.keys(ctx.dataset._meta)[0]].total;
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
    <script>
        var ctx = document.getElementById('fabChartPercent').getContext('2d');
        var fabData = <?= $fabricationData2 ?>;
        var fabChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: fabData.labels,
                datasets: [{
                        data: fabData.datasets[0].data,
                        backgroundColor: fabData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.dataset._meta[Object.keys(ctx.dataset._meta)[0]].total;
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
</div>
