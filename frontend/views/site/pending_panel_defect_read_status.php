<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<?php
if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) {
    $pendingCount = $staffPendingReadStatusCount;
    $subText = "Monitor all complaints";
} else {
    $pendingCount = $myPendingReadStatusCount;
    $subText = "My Unread Complaints";
}
?>

<div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#panelDefectAccordion" aria-expanded="false" aria-controls="panelDefectAccordion">
    <div class="summary-title">
        <span>
            <i class="fas fa-calendar-times summary-icon text-info"></i>
            Panel Defect Complaint 
        </span>
        <span class="summary-count total-pending"><?= $pendingCount ?></span>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="summary-subtext"><?= $subText ?></div>
        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
    </div>
</div>

<div class="collapse mb-2" id="panelDefectAccordion">
    <div class="accordion-content">
        <div class="role-summary-list">
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Wkr, AuthItem::ROLE_PrdnFab_Wkr])) { ?>
                <a href="/production/panel-task-status/index-defects" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-user-friends role-icon text-primary"></i>
                            <span class="role-name">Complaints Assigned to You</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-primary"><?= $myPendingReadStatusCount ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>
            <?php } ?>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) { ?>
                <a href="/productiontaskerror/index" class="text-decoration-none">
                    <div class="role-summary-item mb-2">
                        <div class="role-info">
                            <i class="fas fa-user-tie role-icon text-success"></i>
                            <span class="role-name">Unread by Staff</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="role-count badge bg-success"><?= $staffPendingReadStatusCount ?></span>
                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
