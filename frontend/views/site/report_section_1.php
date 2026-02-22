<?php if ($project_coordinator && $qDoneData): ?>
    <div class="row mt-3 mb-2">
        <div class="col-lg-4 col-md-12 col-sm-12 mb-2">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartQuotationDoneIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'qDoneData' => $qDoneData,
                    'totalQuotationAllProjectCoordinator' => $totalQuotationAllProjectCoordinator,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
        </div>

        <div class="col-lg-4 col-md-12 col-sm-12 mb-2">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartQuotationHitIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'qHitsData' => $qHitsData,
                    'totalQuotationIndividual' => $totalQuotationIndividual,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="chart-card">
                <?=
                $this->renderAjax('/report/_chartTaskCompletionIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'tasksCompletionData' => $tasksCompletionData,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
        </div>                   
    </div>
<?php else: ?>
    <div class="no-data-state">
        <div class="no-data-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3 class="no-data-title">No Data Available</h3>
        <p class="no-data-text">
            Select a date range above to view your performance analytics and reports.
        </p>
    </div>
<?php endif; ?>
