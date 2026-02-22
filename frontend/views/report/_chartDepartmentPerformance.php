<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;

$this->title = 'Department Performance Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="department-performance-report">

    <div class="col-4 text-center">
        <h5>Department Performance from <?= MyFormatter::asDate_Read($dateFrom) ?> to <?= MyFormatter::asDate_Read($dateTo) ?> </h5>
        <canvas id="departmentPerformanceChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('departmentPerformanceChart').getContext('2d');
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
