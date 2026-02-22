<?php

use yii\bootstrap4\Html;
use common\models\myTools\MyFormatter;

//$this->title = 'Quotation Hit Engineer Report';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-quotation-hits">

    <!--<div>-->
        <?php // echo Html::beginForm(['reporting/get-chart-quotation-hit-engineer'], 'get') ?>
<!--        <div class="form-group">
            <div class="col-10">-->
                <?php
//                echo Html::dropDownList('projectId', Yii::$app->request->get('projectId'), $coordinators, [
//                    'id' => 'coordinator',
//                    'class' => 'form-control',
//                    'prompt' => 'Select Project Coordinator'
//                ])
                ?>
<!--            </div>
            <div class="col-2 ml-0 pl-0">-->
                <?php // echo Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
<!--            </div>
        </div>-->
        <?php // echo Html::endForm() ?>
    <!--</div>-->

    <div class="col-12 text-center">
        <h5>Percentage of Amount (RM) of Quotation Hits by Project Coordinator </br> from <?= MyFormatter::asDate_Read($dateFrom) ?> to <?= MyFormatter::asDate_Read($dateTo) ?> </h5>
        <canvas id="quotationChart"></canvas>
    </div>
    <script>
        var ctx = document.getElementById('quotationChart').getContext('2d');
        var chartData = <?= $chartDataJson ?>;
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartData.labels,
                datasets: [{
                        data: chartData.datasets[0].data,
                        backgroundColor: chartData.datasets[0].backgroundColor,
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
                            let percentage = (value * 100 / sum).toFixed(2);
                            return percentage > 0 ? percentage + "%" : '';
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
</div>
