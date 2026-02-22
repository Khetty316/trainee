<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h6>Fabrication Task Deadlines Summary by <?= $worker->fullname ?><br>
            Generated on <?= date('d/m/Y') ?></h6>
        <canvas id='FabTaskWorkerChart'></canvas>
    </div>
    <script>
        var dataFabCtx = document.getElementById('FabTaskWorkerChart').getContext('2d');
        var dataFab = <?= $dataFab ?>;
        var totalTaskOverdueWorkerFab = dataFab.datasets[0].data[0];
        var totalTaskWorkerFab = <?= $totalTaskWorkerFab ?>;

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
                                percentageFab = (totalTaskOverdueWorkerFab / totalTaskWorkerFab) * 100;
                            } else if (totalTaskWorkerFab !== 0) {
                                percentageFab = ((totalTaskWorkerFab - totalTaskOverdueWorkerFab) / totalTaskWorkerFab) * 100;
                            }

                            return valueFab + (valueFab === 1 ? " Task, " : " Tasks, ") + percentageFab.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
