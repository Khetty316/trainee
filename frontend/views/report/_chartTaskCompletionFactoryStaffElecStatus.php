<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h6>Electrical Task Deadlines Summary by <?= $worker->fullname ?><br>
            Generated on <?= date('d/m/Y') ?></h6>
        <canvas id='elecTaskWorkerChart'></canvas>
    </div>
    <script>
        var dataElecCtx = document.getElementById('elecTaskWorkerChart').getContext('2d');
        var dataElec = <?= $dataElec ?>;
        var totalTaskOverdueWorkerElec = dataElec.datasets[0].data[0];
        var totalTaskWorkerElec = <?= $totalTaskWorkerElec ?>;

        var dataElecChart = new Chart(dataElecCtx, {
            type: 'pie',
            data: {
                labels: dataElec.labels,
                datasets: [{
                        data: dataElec.datasets[0].data,
                        backgroundColor: dataElec.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (valueElec, dataElecCtx) => {
                            let percentageElec = 0;

                            if (dataElecCtx.dataIndex === 0) {
                                percentageElec = (totalTaskOverdueWorkerElec / totalTaskWorkerElec) * 100;
                            } else if (totalTaskWorkerElec !== 0) {
                                percentageElec = ((totalTaskWorkerElec - totalTaskOverdueWorkerElec) / totalTaskWorkerElec) * 100;
                            }

                            return valueElec + (valueElec === 1 ? " Task, " : " Tasks, ") + percentageElec.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
