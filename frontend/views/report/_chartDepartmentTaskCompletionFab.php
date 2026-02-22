<?php

use common\models\myTools\MyFormatter;
?>

<div class="chart-department-task-completion">
    <div class="text-center">
        <h5>Fabrication Task Completion from <?= MyFormatter::asDate_Read($dateFrom) ?> to <?= MyFormatter::asDate_Read($dateTo) ?> </h5>
        <canvas id="fabChart"></canvas>
    </div>
    <script>
        var ctxFab = document.getElementById('fabChart').getContext('2d');
        var fabData = <?= $fabricationData ?>;
        var fabChart = new Chart(ctxFab, {
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
                        formatter: (value, ctxFab) => {
                            let total = ctxFab.chart.getDatasetMeta(0).total;
                            return ((value / total) * 100).toFixed(2) + '%';
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
</div>
