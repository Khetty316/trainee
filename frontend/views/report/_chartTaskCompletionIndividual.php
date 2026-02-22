<?php

use common\models\myTools\MyFormatter;
?>
<div class="quotation-done-report">
    <div class="text-center">
        <h5>Project Completion % by <?= $project_coordinator->fullname ?></br>from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?> </h5>
        <canvas id="taskCompletionChart"></canvas>
    </div>
    <script>
        var taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
        var taskCompletionData = <?= $tasksCompletionData ?>;

        var taskCompletionChart = new Chart(taskCompletionCtx, {
            type: 'pie',
            data: {
                labels: taskCompletionData.labels,
                datasets: [{
                        data: taskCompletionData.datasets[0].data,
                        backgroundColor: taskCompletionData.datasets[0].backgroundColor,
                    }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value) => {

                            return value.toFixed(2) + "%";
                        },

                        color: '#fff',
                    },
                }
            }
        });
    </script>
</div>
