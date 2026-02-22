<?php 
use common\models\myTools\MyFormatter;
?><script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<div class="row mb-2">
    <div class="col-lg-6 col-md-12 col-sm-12 mb-2">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartDepartmentTaskCompletionFab', [
                    'model' => $model,
                    'fabricationData' => $fabricationData,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                ])
                ?>
            </div>
        </div>
    <div class="col-lg-6 col-md-12 col-sm-12 mb-2">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartDepartmentTaskCompletionElec', [
                    'model' => $model,
                    'electricalData' => $electricalData,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                ])
                ?>
            </div>
        </div>
</div>
