<?php

use common\models\myTools\MyFormatter;

//$this->title = 'Quotation Hit Type Report';
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="quotation-hit-type-report">
    <div class="col-12 text-center">
        <h5>Percentage of Amount (RM) of Quotation Hits by Type </br> from <?= MyFormatter::asDate_Read($dateFrom) ?> to <?= MyFormatter::asDate_Read($dateTo) ?> </h5>
        <canvas id="quotationHitTypeChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('quotationHitTypeChart').getContext('2d');
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
//                    legend: {
//                        onHover: handleHover,
//                        onLeave: handleLeave
//                    },
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
