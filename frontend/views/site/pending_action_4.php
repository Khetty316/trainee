<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<?php
if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Normal])) {
    $pendingCount = $totalPendingPettyCashReqPersonal;
    $desc = 'Awaiting your review';
} else if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Finance])) {
    $pendingCount = ($totalPendingPettyCashReqFinance + $totalPendingReplenishmentFinance);
    $desc = 'Awaiting your review';
} else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
    $pendingCount = $totalPendingReplenishmentDirector;
    $desc = 'Awaiting your approval';
}
?>
<div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#pettyAccordion" aria-expanded="false" aria-controls="pettyAccordion">
    <div class="summary-title">
        <span>
            <i class="fas fa-money-bill-wave me-2 summary-icon text-info"></i>
            Petty Cash 
        </span>
        <span class="summary-count total-pending"><?= $pendingCount ?></span>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="summary-subtext"><?= $desc ?></div>
        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
    </div>
</div>

<?php
if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Normal, AuthItem::ROLE_PC_Finance, AuthItem::ROLE_Director])) {
    ?>
    <div class="collapse mb-2" id="pettyAccordion">
        <div class="accordion-content">
            <?php
            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Normal])) {
                ?>
                <div class="section-header">
                    <span>Personal</span>
                </div>
                <div class="role-summary-list">
                    <a href="/office/petty-cash/personal-pending" class="text-decoration-none">
                        <div class="role-summary-item mb-2">
                            <div class="role-info">
                                <i class="fas fa-receipt me-1 role-icon text-info"></i>
                                <span class="role-name">My Petty Cash Requisition</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="role-count badge bg-info"><?= $totalPendingPettyCashReqPersonal ?></span>
                                <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
            <?php
            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Finance])) {
                ?> 
                <div class="section-header">
                    <span>Finance</span>
                </div>
                <div class="role-summary-list">
                    <a href="/office/petty-cash/finance-approval-pending" class="text-decoration-none">
                        <div class="role-summary-item mb-2">
                            <div class="role-info">
                                <i class="fas fa-receipt me-1 role-icon text-warning"></i>
                                <span class="role-name">Staff Petty Cash Requisition</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="role-count badge bg-warning"><?= $totalPendingPettyCashReqFinance ?></span>
                                <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                            </div>
                        </div>
                    </a>

                    <a href="/office/petty-cash/finance-replenishment" class="text-decoration-none">
                        <div class="role-summary-item mb-2">
                            <div class="role-info">
                                <i class="fas fa-wallet me-1 role-icon text-warning"></i>
                                <span class="role-name">Cash Replenishment Request</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="role-count badge bg-warning"><?= $totalPendingReplenishmentFinance ?></span>
                                <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
            <?php
            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
                ?>
                <div class="section-header">
                    <span>Director</span>
                </div>
                <div class="role-summary-list">
                    <a href="/office/petty-cash/director-approval-pending" class="text-decoration-none">
                        <div class="role-summary-item mb-2">
                            <div class="role-info">
                                <i class="fas fa-wallet me-1 role-icon text-success"></i>
                                <span class="role-name">Replenishment Request Approval</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="role-count badge bg-success"><?= $totalPendingReplenishmentDirector ?></span>
                                <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    </div>
<?php } ?>
