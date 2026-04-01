<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$this->registerCssFile('@web/css/dashboardStyle.css');
?>
<div class="container-fluid dashboard-container">

    <div class="dashboard-header">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <h1 class="greeting">Hi, <?= Yii::$app->user->identity->fullname ?>!</h1>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?php
                $pendingCount = ($totalNewPublicDoc);
                ?>
                <a href="/profile/view-user-public-documents" style="text-decoration: none; color: inherit;">
                    <div class="summary-title float-right" style="font-size: 20px; position: relative; display: inline-block;">
                        <span class="badge bg-warning text-dark p-2 rounded-pill">
                            <i class="far fa-bell fa-lg pr-1 text-white <?= $pendingCount != 0 ? 'shake' : '' ?>"></i>
                            New Announcements 
                        </span>
                        <span class="summary-count total-pending text-white rounded-circle position-absolute" style="top: -12px; right: -12px; min-width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                            <?= $pendingCount ?>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!--<div class="col-lg-12 col-md-12 col-sm-12">-->
        <?php
//            $performanceFactoryStaffBanner = MyCommonFunction::checkRoles([
//                AuthItem::ROLE_PrdnElec_Wkr,
//                AuthItem::ROLE_PrdnFab_Wkr,
//            ]);
        ?>
        <?php // if ($performanceFactoryStaffBanner) { ?>
        <!--                <div id="performance-factory-staff-banner">
                            <div class="loader text-center p-5">
                                <i class="fas fa-spinner fa-spin"></i> Calculating...
                            </div>
                        </div>-->
        <?php // } ?>
        <!--</div>-->
        <div class="col-lg-3 col-md-12 col-sm-12 summary-section">
            <div class="summary-header-card">
                <i class="fas fa-tasks"></i>
                <span>Pending Actions</span>
            </div>
            <?php
            $hasPendingAction1Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_Director,
                AuthItem::ROLE_SystemAdmin
            ]);

            $hasPendingAction2Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_Director,
                AuthItem::ROLE_ProjCoordinator,
                AuthItem::ROLE_SystemAdmin
            ]);

            $hasPendingAction3Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_Director,
                AuthItem::ROLE_ProjCoordinator,
                AuthItem::ROLE_SystemAdmin,
                AuthItem::ROLE_PrdnElec_Executive,
                AuthItem::ROLE_PrdnFab_Executive,
                AuthItem::ROLE_PrdnElec_Wkr,
                AuthItem::ROLE_PrdnFab_Wkr,
            ]);

            $hasPendingAction4Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_Director,
                AuthItem::ROLE_PC_Normal,
                AuthItem::ROLE_PC_Finance,
            ]);

            $hasPendingPanelDefectReadStatusAccess = MyCommonFunction::checkRoles([
                AuthItem::ROLE_Director,
                AuthItem::ROLE_ProjCoordinator,
                AuthItem::ROLE_SystemAdmin,
                AuthItem::ROLE_PrdnElec_Executive,
                AuthItem::ROLE_PrdnFab_Executive,
                AuthItem::ROLE_PrdnElec_Wkr,
                AuthItem::ROLE_PrdnFab_Wkr,
            ]);
            ?>

            <?php if ($hasPendingAction1Access) { ?>
                <div id="pending-action-1">
                    <div class="loader text-center p-5">
                        <i class="fas fa-spinner fa-spin"></i> Calculating...
                    </div>
                </div>
            <?php } ?>

            <?php if ($hasPendingAction2Access) { ?>
                <div id="pending-action-2" class="clickable-card" data-bs-toggle="collapse" data-bs-target="#projectAccordion">
                    <div class="loader text-center p-5">
                        <i class="fas fa-spinner fa-spin"></i> Calculating...
                    </div>
                </div>
            <?php } ?>

            <?php if ($hasPendingAction3Access) { ?>
                <div id="pending-action-3" class="clickable-card" data-bs-toggle="collapse" data-bs-target="#taskAccordion">
                    <div class="loader text-center p-5">
                        <i class="fas fa-spinner fa-spin"></i> Calculating...
                    </div>
                </div>
            <?php } ?>

            <?php
            if ($hasPendingPanelDefectReadStatusAccess) {
                ?>
                <div id="pending-panel-defect-read-status" class="clickable-card" data-bs-toggle="collapse" data-bs-target="#panelDefectAccordion">
                    <div class="loader text-center p-5">
                        <i class="fas fa-spinner fa-spin"></i> Calculating...
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
//            if ($hasPendingInventoryAccess) {
            ?>
<!--            <div id="pending-action-inventory" class="clickable-card" data-bs-toggle="collapse" data-bs-target="#inventoryAccordion">
                <div class="summary-card">
                    <div class="summary-title">
                        <span><i class="fas fa-boxes summary-icon text-info"></i> Inventory</span>
                        <span class="summary-count">...</span>
                    </div>
                    <div class="loader text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>-->
            <?php
//            }
            ?>
            <?php
            if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior]) && !MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
                $pendingCount = ($totalPendingReliefLeave + $totalPendingSuperiorLeave + $totalPendingHrLeave);
            } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
                $pendingCount = ($totalPendingReliefLeave + $totalPendingSuperiorLeave + $totalPendingDirectorLeave);
            } else {
                $pendingCount = ($totalPendingReliefLeave + $totalPendingSuperiorLeave);
            }
            ?>
            <div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#leaveAccordion" aria-expanded="false" aria-controls="leaveAccordion">
                <div class="summary-title">
                    <span>
                        <i class="fas fa-calendar-times summary-icon text-info"></i>
                        Leave 
                    </span>
                    <span class="summary-count total-pending"><?= $pendingCount ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="summary-subtext">Awaiting your approval</div>
                    <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
                </div>
            </div>

            <div class="collapse mb-2" id="leaveAccordion">
                <div class="accordion-content">
                    <div class="role-summary-list">
                        <a href="/working/leavemgmt/relief-leave-approval" class="text-decoration-none">
                            <div class="role-summary-item mb-2">
                                <div class="role-info">
                                    <i class="fas fa-user-friends role-icon text-primary"></i>
                                    <span class="role-name">Relief</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="role-count badge bg-primary"><?= $totalPendingReliefLeave ?></span>
                                    <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                </div>
                            </div>
                        </a>

                        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Superior])) { ?>
                            <a href="/working/leavemgmt/superior-leave-approval" class="text-decoration-none">
                                <div class="role-summary-item mb-2">
                                    <div class="role-info">
                                        <i class="fas fa-user-check role-icon text-success"></i>
                                        <span class="role-name">Superior</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="role-count badge bg-success"><?= $totalPendingSuperiorLeave ?></span>
                                        <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>

                        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior]) && !MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) { ?>
                            <a href="/working/leavemgmt/hr-leave-approval" class="text-decoration-none">
                                <div class="role-summary-item mb-2">
                                    <div class="role-info">
                                        <i class="fas fa-users role-icon text-info"></i>
                                        <span class="role-name">HR</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="role-count badge bg-info"><?= $totalPendingHrLeave ?></span>
                                        <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>

                        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) { ?>
                            <a href="/working/leavemgmt/director-compulsory-leave" class="text-decoration-none">
                                <div class="role-summary-item mb-2">
                                    <div class="role-info">
                                        <i class="fas fa-user-tie role-icon text-dark"></i>
                                        <span class="role-name">Director</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="role-count badge bg-dark"><?= $totalPendingDirectorLeave ?></span>
                                        <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if ($hasPendingAction4Access) { ?>
                <div id="pending-action-4" class="clickable-card" data-bs-toggle="collapse" data-bs-target="#pettyAccordion">
                    <div class="loader text-center p-5">
                        <i class="fas fa-spinner fa-spin"></i> Calculating...
                    </div>
                </div>
            <?php } ?>

            <?php
            if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Superior])) {
                $pendingCount = $totalPendingSuperiorClaim;
            } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) {
                $pendingCount = $totalPendingFinanceClaim;
            }
            if ((MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Superior]) || MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) && !MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
                ?>
                <div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#claimAccordion" aria-expanded="false" aria-controls="claimAccordion">
                    <div class="summary-title">
                        <span>
                            <i class="fas fa-receipt summary-icon text-info"></i>
                            Claim
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="summary-count total-pending"><?= $pendingCount ?></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="summary-subtext">Pending review and approval</div>
                        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
                    </div>
                </div>

                <div class="collapse mb-2" id="claimAccordion">
                    <div class="accordion-content">
                        <div class="role-summary-list">
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Superior])) { ?>
                                <a href="/office/claim/superior-approval-pending" class="text-decoration-none">
                                    <div class="role-summary-item mb-2">
                                        <div class="role-info">
                                            <i class="fas fa-user-tie role-icon text-success"></i>
                                            <span class="role-name">Superior</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="role-count badge bg-success"><?= $totalPendingSuperiorClaim ?></span>
                                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>

                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) { ?>
                                <a href="/office/claim/finance-approval-pending" class="text-decoration-none">
                                    <div class="role-summary-item mb-2">
                                        <div class="role-info">
                                            <i class="fas fa-calculator role-icon text-warning"></i>
                                            <span class="role-name">Finance</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="role-count badge bg-warning"><?= $totalPendingFinanceClaim ?></span>
                                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_Superior])) { ?>
                <?php $pendingCount = ($totalPendingSuperiorPrf); ?>
                <div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#prfAccordion" aria-expanded="false" aria-controls="prfAccordion">
                    <div class="summary-title">
                        <span>
                            <i class="fas fa-file-alt summary-icon text-info"></i>
                            Pre-Requisition Form
                        </span>
                        <span class="summary-count total-pending"><?= $pendingCount ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="summary-subtext">Awaiting your approval</div>
                        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
                    </div>
                </div>

                <div class="collapse mb-2" id="prfAccordion">
                    <div class="accordion-content">
                        <div class="role-summary-list">
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_Superior])) { ?>
                                <a href="/office/prereq-form-master/superior-pending-approval" class="text-decoration-none">
                                    <div class="role-summary-item mb-2">
                                        <div class="role-info">
                                            <i class="fas fa-user-tie role-icon text-success"></i>
                                            <span class="role-name">Superior</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="role-count badge bg-success"><?= $totalPendingSuperiorPrf ?></span>
                                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior])) { ?>
                <?php $pendingCount = $totalPendingSuperiorClaimEntitlement; ?>
                <div class="summary-card clickable-card" data-bs-toggle="collapse" data-bs-target="#ceAccordion" aria-expanded="false" aria-controls="ceAccordion">
                    <div class="summary-title">
                        <span>
                            <i class="fas fa-clipboard-check summary-icon text-info"></i>
                            Claim Entitlement
                        </span>
                        <span class="summary-count total-pending"><?= $pendingCount ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="summary-subtext">Awaiting your approval</div>
                        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
                    </div>
                </div>

                <div class="collapse mb-2" id="ceAccordion">
                    <div class="accordion-content">
                        <div class="role-summary-list">
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior])) { ?>
                                <a href="/office/claim-entitlement/superior-pending-approval" class="text-decoration-none">
                                    <div class="role-summary-item mb-2">
                                        <div class="role-info">
                                            <i class="fas fa-user-tie role-icon text-success"></i>
                                            <span class="role-name">Superior</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="role-count badge bg-success"><?= $totalPendingSuperiorClaimEntitlement ?></span>
                                            <i class="fas fa-chevron-right accordion-arrow ml-3"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div> 
        <div class="col-lg-9 col-md-12 col-sm-12">
            <?php
            $hasSection1Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_PrdnElec_Executive,
                AuthItem::ROLE_PrdnFab_Executive,
                AuthItem::ROLE_Director,
                AuthItem::ROLE_ProjCoordinator,
                AuthItem::ROLE_SystemAdmin
            ]);

            $hasSection2Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_PrdnElec_Wkr,
                AuthItem::ROLE_PrdnFab_Wkr,
                AuthItem::ROLE_SystemAdmin
            ]);

            $hasSection3Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_PrdnElec_Executive,
                AuthItem::ROLE_PrdnFab_Executive,
                AuthItem::ROLE_PrdnElec_Wkr,
                AuthItem::ROLE_PrdnFab_Wkr,
                AuthItem::ROLE_Director,
                AuthItem::ROLE_SystemAdmin
            ]);

            $hasSection4Access = MyCommonFunction::checkRoles([
                AuthItem::ROLE_PrdnElec_Executive,
                AuthItem::ROLE_PrdnFab_Executive,
                AuthItem::ROLE_SystemAdmin
            ]);
            ?>

            <?php if ($hasSection1Access || $hasSection2Access || $hasSection3Access || $hasSection4Access) { ?>
                <div class="report-card">
                    <div class="date-form-section">
                        <?=
                        $this->renderAjax('/site/_monthForm', [
                            'model' => $model
                        ])
                        ?>
                    </div>

                    <?php if ($hasSection1Access) { ?>
                        <div id="report-section-1">
                            <div class="loader text-center p-5">
                                <i class="fas fa-spinner fa-spin"></i> Loading reports...
                            </div> 
                        </div>
                    <?php } ?>

                    <?php if ($hasSection2Access) { ?>    
                        <div id="report-section-2">
                            <div class="loader text-center p-5">
                                <i class="fas fa-spinner fa-spin"></i> Loading reports...
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($hasSection4Access) { ?>
                        <div id="report-section-4">
                            <div class="loader text-center p-5">
                                <i class="fas fa-spinner fa-spin"></i> Loading reports...
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($hasSection3Access) { ?>
                        <div id="report-section-3">
                            <div class="loader text-center p-5">
                                <i class="fas fa-spinner fa-spin"></i> Loading reports...
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.clickable-sub', function () {
        var target = $(this).attr('data-target');
        $(target).collapse('toggle');
    });
    $(document).ready(function () {
<?php // if ($performanceFactoryStaffBanner) {        ?>
//            $.ajax({
//                url: '/site/performance-factory-staff-banner',
//                type: 'GET',
//                success: function (data) {
//                    $('#performance-factory-staff-banner').html(data);
//                },
//                error: function (xhr) {
//                    $('#performance-factory-staff-banner').html(
//                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
//                            );
//                }
//            });
<?php // }        ?>

<?php if ($hasPendingAction1Access) { ?>
            $.ajax({
                url: '/site/pending-action-1',
                type: 'GET',
                success: function (data) {
                    $('#pending-action-1').html(data);
                },
                error: function (xhr) {
                    $('#pending-action-1').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasPendingAction2Access) { ?>
            $.ajax({
                url: '/site/pending-action-2',
                type: 'GET',
                success: function (data) {
                    $('#pending-action-2').html(data);
                },
                error: function (xhr) {
                    $('#pending-action-2').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasPendingAction3Access) { ?>
            $.ajax({
                url: '/site/pending-action-3',
                type: 'GET',
                success: function (data) {
                    $('#pending-action-3').html(data);
                },
                error: function (xhr) {
                    $('#pending-action-3').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasPendingAction4Access) { ?>
            $.ajax({
                url: '/site/pending-action-4',
                type: 'GET',
                success: function (data) {
                    $('#pending-action-4').html(data);
                },
                error: function (xhr) {
                    $('#pending-action-4').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasPendingPanelDefectReadStatusAccess) { ?>
            $.ajax({
                url: '/site/pending-panel-defect-read-status',
                type: 'GET',
                success: function (data) {
                    $('#pending-panel-defect-read-status').html(data);
                },
                error: function (xhr) {
                    $('#pending-panel-defect-read-status').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php // if ($hasPendingPanelDefectReadStatusAccess) {   ?>
        $.ajax({
            url: '/site/pending-action-inventory',
            type: 'GET',
            success: function (data) {
                $('#pending-action-inventory').html(data);

                // Re-initialize Bootstrap Collapses for the newly injected HTML
                var collapseElementList = [].slice.call(document.querySelectorAll('#inventoryAccordion .collapse'));
                var collapseList = collapseElementList.map(function (collapseEl) {
                    return new bootstrap.Collapse(collapseEl, {toggle: false});
                });
            },
            error: function (xhr) {
                $('#pending-action-inventory').html("<div class='alert alert-danger'>Error: " + xhr.responseText + "</div>");
            }
        });
<?php // }   ?>

<?php if ($hasSection1Access || $hasSection2Access || $hasSection3Access || $hasSection4Access) { ?>
            var dateFrom = document.getElementById('reportingmodel-datefrom').value;
            var dateTo = document.getElementById('reportingmodel-dateto').value;
<?php } ?>

<?php if ($hasSection1Access) { ?>
            $.ajax({
                url: '/site/report-section1',
                type: 'GET',
                data: {
                    dateFrom: "<?= $model->dateFrom ?>",
                    dateTo: "<?= $model->dateTo ?>",
                    is_internalProject: "<?= $model->is_internalProject ?>"
                },
                success: function (data) {
                    $('#report-section-1').html(data);
                },
                error: function (xhr) {
                    $('#report-section-1').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasSection2Access) { ?>
            $.ajax({
                url: '/site/report-section2',
                type: 'GET',
                data: {
                    dateFrom: "<?= $model->dateFrom ?>",
                    dateTo: "<?= $model->dateTo ?>",
                    is_internalProject: "<?= $model->is_internalProject ?>"
                },
                success: function (data) {
                    $('#report-section-2').html(data);
                },
                error: function (xhr) {
                    $('#report-section-2').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasSection3Access) { ?>
            $.ajax({
                url: '/site/report-section3',
                type: 'GET',
                data: {
                    dateFrom: dateFrom,
                    dateTo: dateTo,
                    is_internalProject: "<?= $model->is_internalProject ?>"
                },
                success: function (data) {
                    $('#report-section-3').html(data);
                },
                error: function (xhr) {
                    $('#report-section-3').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>

<?php if ($hasSection4Access) { ?>
            $.ajax({
                url: '/site/report-section4',
                type: 'GET',
                data: {
                    dateFrom: dateFrom,
                    dateTo: dateTo,
                    is_internalProject: "<?= $model->is_internalProject ?>"
                },
                success: function (data) {
                    $('#report-section-4').html(data);
                },
                error: function (xhr) {
                    $('#report-section-4').html(
                            "<div class='alert alert-danger'>Failed to load reports. " + xhr.responseText + "</div>"
                            );
                }
            });
<?php } ?>
    });

    document.addEventListener('DOMContentLoaded', function () {
        const clickableCards = document.querySelectorAll('.clickable-card');
        clickableCards.forEach(card => {
            card.addEventListener('click', function () {
                const target = this.getAttribute('data-bs-target');
                const accordion = document.querySelector(target);
                const arrow = this.querySelector('.accordion-arrow');
                if (accordion) {
                    if (accordion.classList.contains('show')) {
                        accordion.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                        arrow.style.transform = 'rotate(0deg)';
                    } else {
                        // Close other accordions
                        document.querySelectorAll('.collapse.show').forEach(openAccordion => {
                            if (openAccordion !== accordion) {
                                openAccordion.classList.remove('show');
                            }
                        });
                        // Reset other arrows
                        document.querySelectorAll('.accordion-arrow').forEach(otherArrow => {
                            if (otherArrow !== arrow) {
                                otherArrow.style.transform = 'rotate(0deg)';
                            }
                        });
                        // Reset other cards
                        document.querySelectorAll('.clickable-card').forEach(otherCard => {
                            if (otherCard !== this) {
                                otherCard.setAttribute('aria-expanded', 'false');
                            }
                        });
                        // Open this accordion
                        accordion.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        arrow.style.transform = 'rotate(180deg)';
                    }
                }
            });
        });
    });
</script>