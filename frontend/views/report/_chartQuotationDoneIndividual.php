<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h5>Quotation Done by <?= $project_coordinator->fullname ?></br>from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?> </h5>
        <canvas id="qDoneChart"></canvas>
    </div>
    <script>
        var qDoneCtx = document.getElementById('qDoneChart').getContext('2d');
        var qDoneData = <?= $qDoneData ?>;
        var totalQDoneIndividual = qDoneData.datasets[0].data[0];
        var totalQuotationAllStaffs = <?= $totalQuotationAllProjectCoordinator ?>;

        var qDoneChart = new Chart(qDoneCtx, {
            type: 'pie',
            data: {
                labels: qDoneData.labels,
                datasets: [{
                        data: qDoneData.datasets[0].data,
                        backgroundColor: qDoneData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value, qDoneCtx) => {
                            let percentage = 0;

                            if (qDoneCtx.dataIndex === 0) {
                                percentage = (totalQDoneIndividual / totalQuotationAllStaffs) * 100;
                            } else {
                                percentage = ((totalQuotationAllStaffs - totalQDoneIndividual) / totalQuotationAllStaffs) * 100;
                            }

                            return value + " Quotation(s), " + percentage.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
