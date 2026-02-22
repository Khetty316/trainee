<?php

use common\models\myTools\MyFormatter;
?>

<div class="chart-department-task-completion">
    <div class="text-center">
        <h5>Electrical Task Completion from <?= MyFormatter::asDate_Read($dateFrom) ?> to <?= MyFormatter::asDate_Read($dateTo) ?> </h5>
        <canvas id="elecChart"></canvas>
    </div>

    <script>
        var ctxElec = document.getElementById('elecChart').getContext('2d');
        var elecData = <?= $electricalData ?>;
        var elecChart = new Chart(ctxElec, {
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
                        formatter: (value, ctxElec) => {
                            let total = ctxElec.chart.getDatasetMeta(0).total;
                            return ((value / total) * 100).toFixed(2) + '%';
                        },
                        color: '#fff',
                    }
                }
            }
        });
    </script>
</div>
