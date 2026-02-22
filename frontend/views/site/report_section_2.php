
<div class="row mt-2 mb-2">
    <?php
    $tasksCompletionElecDataArray = json_decode($tasksCompletionElecData, true);
    $tasksCompletionFabDataArray = json_decode($tasksCompletionFabData, true);

    $hasElecData = !empty($tasksCompletionElecDataArray['datasets'][0]['data']);
    $hasFabData = !empty($tasksCompletionFabDataArray['datasets'][0]['data']);
    ?>

    <?php if ($hasElecData): ?>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartTaskCompletionFactoryStaffElec', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'dataElec' => $tasksCompletionElecData,
                    'totalTaskAmountElec' => $totalAmountTaskElec,
                    'worker' => $worker
                ])
                ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($hasFabData): ?>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartTaskCompletionFactoryStaffFab', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'dataFab' => $tasksCompletionFabData,
                    'totalTaskAmountFab' => $totalAmountTaskFab,
                    'worker' => $worker
                ])
                ?>
            </div>
        </div>                                   
    <?php endif; ?>
</div>
