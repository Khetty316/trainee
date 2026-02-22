<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h5>Fabrication Task Completion (RM) by <?= $worker->fullname ?></br>from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?> </h5>
        <canvas id='fabChart'></canvas>
    </div>
    <script>
        var dataFabCtx = document.getElementById('fabChart').getContext('2d');
        var dataFab = <?= $dataFab ?>;
        var completedTaskAmountFab = dataFab.datasets[0].data[0];
        var totalTaskAmountFab = <?= $totalTaskAmountFab ?>;
        
        var dataFabChart = new Chart(dataFabCtx, {
            type: 'pie',
            data: {
                labels: dataFab.labels,
                datasets: [{
                        data: dataFab.datasets[0].data,
                        backgroundColor: dataFab.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (valueFab, dataFabCtx) => {
                            let percentageFab = 0;

                            if (dataFabCtx.dataIndex === 0) {
                                percentageFab = (completedTaskAmountFab / totalTaskAmountFab) * 100;
                            } else if (totalTaskAmountFab !== 0) {
                                percentageFab = ((totalTaskAmountFab - completedTaskAmountFab) / totalTaskAmountFab) * 100;
                            }

                            return "RM " + valueFab.toFixed(2) + ", " + percentageFab.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
