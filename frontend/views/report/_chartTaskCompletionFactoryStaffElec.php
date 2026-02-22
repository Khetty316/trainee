<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h5>Electrical Task Completion (RM) by <?= $worker->fullname ?></br>from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?> </h5>
        <canvas id='elecChart'></canvas>
    </div>
    <script>
        var dataElecCtx = document.getElementById('elecChart').getContext('2d');
        var dataElec = <?= $dataElec ?>;
        var completedTaskAmountElec = dataElec.datasets[0].data[0];
        var totalTaskAmountElec = <?= $totalTaskAmountElec ?>;

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
                                percentageElec = (completedTaskAmountElec / totalTaskAmountElec) * 100;
                            } else if (totalTaskAmountElec !== 0) {
                                percentageElec = ((totalTaskAmountElec - completedTaskAmountElec) / totalTaskAmountElec) * 100;
                            }

                            return "RM " + valueElec.toFixed(2) + ", " + percentageElec.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
