<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<?php $pendingCount = ($totalProjectOverdue + $totalProjectNeardue); ?>
<div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#projectAccordion" aria-expanded="false" aria-controls="projectAccordion">
    <div class="summary-title">
        <span>
            <i class="fas fa-tasks me-2 summary-icon text-info"></i>
            Project Deadlines 
        </span>
        <span class="summary-count total-pending"><?= $pendingCount ?></span>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="summary-subtext">Your project deadline summary</div>
        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
    </div>
</div>

<div class="collapse mb-2" id="projectAccordion">
    <div class="accordion-content">
        <div class="role-summary-list">
            <a href="/production/production/index-production-main?type=overdue" class="text-decoration-none">
                <div class="role-summary-item mb-2">
                    <div class="role-info">
                        <i class="fas fa-exclamation-circle me-1 role-icon text-danger"></i>
                        <span class="role-name">Overdue</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="role-count badge bg-danger"><?= $totalProjectOverdue ?></span>
                        <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                    </div>
                </div>
            </a>

            <a href="/production/production/index-production-main?type=neardue" class="text-decoration-none">
                <div class="role-summary-item mb-2">
                    <div class="role-info">
                        <i class="fas fa-hourglass-half me-1 role-icon text-warning"></i>
                        <span class="role-name">Due Soon</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="role-count badge bg-warning"><?= $totalProjectNeardue ?></span>
                        <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
