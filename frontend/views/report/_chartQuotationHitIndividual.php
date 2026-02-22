<?php

use common\models\myTools\MyFormatter;
?>

<div class="quotation-hit-report">
    <div class="text-center">
        <h5>Quotation Hits by <?= $project_coordinator->fullname ?></br>from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?> </h5>
        <canvas id="qHitsChart"></canvas>
    </div>
    <script>
        var qHitsCtx = document.getElementById('qHitsChart').getContext('2d');
        var qHitsData = <?= $qHitsData ?>;
        var totalQHits = qHitsData.datasets[0].data[0];
        var totalQuotations = <?= $totalQuotationIndividual ?>;

        var qHitsChart = new Chart(qHitsCtx, {
            type: 'pie',
            data: {
                labels: qHitsData.labels,
                datasets: [{
                        data: qHitsData.datasets[0].data,
                        backgroundColor: qHitsData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value, qHitsCtx) => {
                            let percentage = 0;

                            if (qHitsCtx.dataIndex === 0) {
                                percentage = (totalQHits / totalQuotations) * 100;
                            } else {
                                percentage = ((totalQuotations - totalQHits) / totalQuotations) * 100;
                            }

                            return value + " Quotation(s), " + percentage.toFixed(2) + "%";
                            ;
                        },

                        color: '#fff',
                    },

                }
            }
        });

    </script>
</div>
