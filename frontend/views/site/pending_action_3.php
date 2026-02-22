<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<?php
if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) {
    $totalTaskOverdueSuper = ($totalTaskOverdueFabSuper + $totalTaskOverdueElecSuper);
    $totalTaskNeardueSuper = ($totalTaskNeardueFabSuper + $totalTaskNeardueElecSuper);
    $pendingCount = ($totalTaskOverdueSuper + $totalTaskNeardueSuper);
    $desc = 'Task deadline summary';
    ?>
    <?php
} else if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Wkr, AuthItem::ROLE_PrdnFab_Wkr])) {
    $pendingCount = ($totalTaskOverdueWorker + $totalTaskNeardueWorker);
    $desc = 'Your task deadline summary';
}
?>
<div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#taskAccordion" aria-expanded="false" aria-controls="taskAccordion">
    <div class="summary-title">
        <span>
            <i class="fas fa-tasks me-2 summary-icon text-info"></i>
            Task Deadlines 
        </span>
        <span class="summary-count total-pending"><?= $pendingCount ?></span>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="summary-subtext"><?= $desc ?></div>
        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
    </div>
</div>

<?php
if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) {
    ?>
    <div class="collapse mb-2" id="taskAccordion">
        <div class="accordion-content">
            <div class="section-header">
                <span>Fabrication</span>
            </div>
            <div class="role-summary-list">
                <a href="/fab-task/index-fab-in-progress?date=overdue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-exclamation-circle me-1 role-icon text-danger"></i>
                            <span class="role-name">Overdue</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-danger"><?= $totalTaskOverdueFabSuper ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>

                <a href="/fab-task/index-fab-in-progress?date=neardue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-hourglass-half me-1 role-icon text-warning"></i>
                            <span class="role-name">Due Soon</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-warning"><?= $totalTaskNeardueFabSuper ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="section-header">
                <span>Electrical</span>
            </div>
            <div class="role-summary-list">
                <a href="/elec-task/index-elec-in-progress?date=overdue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-exclamation-circle me-1 role-icon text-danger"></i>
                            <span class="role-name">Overdue</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-danger"><?= $totalTaskOverdueElecSuper ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>

                <a href="/elec-task/index-elec-in-progress?date=neardue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-hourglass-half me-1 role-icon text-warning"></i>
                            <span class="role-name">Due Soon</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-warning"><?= $totalTaskNeardueElecSuper ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
<?php } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Wkr, AuthItem::ROLE_PrdnFab_Wkr])) { ?>
    <div class="collapse mb-2" id="taskAccordion">
        <div class="accordion-content">
            <div class="role-summary-list">
                <a href="/production/panel-task-status/my-active-task?date=overdue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-exclamation-circle me-1 role-icon text-danger"></i>
                            <span class="role-name">Overdue</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-danger"><?= $totalTaskOverdueWorker ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>

                <a href="/production/panel-task-status/my-active-task?date=neardue" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-hourglass-half me-1 role-icon text-warning"></i>
                            <span class="role-name">Due Soon</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-warning"><?= $totalTaskNeardueWorker ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
