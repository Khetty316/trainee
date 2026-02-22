<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap4\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap4\Modal;
use frontend\models\common\MenuModel;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

AppAsset::register($this);
$session = Yii::$app->session;

$thisUser = Yii::$app->user;
$hr = AuthItem::ROLE_HR_Senior;
$sysadmin = AuthItem::ROLE_SystemAdmin;
$isDirector = AuthItem::ROLE_Director;
$isInvControl = AuthItem::ROLE_InventoryCtrl;
$isFabricating = AuthItem::ROLE_PrdnFab_Executive;
$isFabWorker = AuthItem::ROLE_PrdnFab_Wkr;
$isElectric = AuthItem::ROLE_PrdnElec_Executive;
$isElecWorker = AuthItem::ROLE_PrdnElec_Wkr;
$isCoordinator = AuthItem::ROLE_ProjCoordinator;
$isStaff = AuthItem::ROLE_Staff;
$isProbation = AuthItem::ROLE_Probation;

$this->registerJsFile('@web/js/vue.global.js', ['position' => \yii\web\View::POS_HEAD]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <link rel="stylesheet" type="text/css" href="/css/pikaday.min.css">
        <script type="text/javascript" src="/js/pikaday.js"></script>
        <script type="text/javascript" src="/js/moment.min.js"></script>
        <script type="text/javascript" src="/js/sortable.min.js"></script>

        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="manifest" href="/manifest.json">
        <script src="/index.js" ></script>

        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode(Yii::$app->params['application_name']) ?></title>
        <?php $this->head() ?>

    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="wrap">
            <nav id="w1-navbar" class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top mainmenu p-0 m-0" >
                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="/" style="align-self: center"><b><?= Html::encode(Yii::$app->params['application_name']) ?></b></a>
                <?php
                if (!$thisUser->isGuest) {
//                    if ($this->beginCache($thisUser->id, ['variations' => ['varyBySession' => 0]])) {
                    ?>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">

                            <?php
                            $mainMenuItems = [];

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin,
                                        AuthItem::ROLE_InventoryCtrl, AuthItem::ROLE_FinanceExecutive])) {

                                $menuQuotation = MenuModel::newMenuItems('<i class="fas fa-clipboard-list"></i>', 'Management', '#');
                                $menuQuotation->children[] = MenuModel::newMenuItems('', 'Project', '/projectquotation');
                                $menuQuotation->children[] = MenuModel::newMenuItems('', 'Template', '/projectqtemplate/indexpqrevision');
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin])) {
                                    $menuQuotation->children[] = MenuModel::newMenuItems('', 'Approval', '/projectquotation/director-pending-approval');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin])) {
                                    $menuQuotation->children[] = MenuModel::newMenuItems('', 'Currency Exchange', '/ref-currencies/index');
                                }
                                $mainMenuItems[] = $menuQuotation;

                                $mainMenuItems[] = MenuModel::newMenuItems('<i class="fas fa-address-book"></i>', 'Clients', '/client');
                                ?>

                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;&nbsp;Quotation
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <?php
                                        foreach ($mainMenuItems as $key => $menu) {
                                            echo ' <li class="dropdown dropright pl-0">';
                                            if (empty($menu->children)) {
                                                echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                            } else {
                                                echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                                echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                                echo $menu->title ?? "";
                                                echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                                foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                    echo "<li>";
                                                    if (empty($childrenMenu->children)) {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                    } else {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                                "javascript:void(0)",
                                                                ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                        echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                        foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                            echo "<li>";

                                                            if (empty($grandChildrenMenu->children)) {
                                                                echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                            }
                                                            echo "</li>";
                                                        }echo "</ul>";
                                                    }echo "</li>";
                                                }echo "</ul>";
                                            }echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php
                            $mainMenuProduction = [];

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) {
                                $menuProduction = MenuModel::newMenuItems('<i class="fas fa-hammer"></i>', 'Tracking', '#');
                                $menuProduction->children[] = MenuModel::newMenuItems('', 'Project Production', '/production/production/index-production-main');
                                $menuProduction->children[] = MenuModel::newMenuItems('', 'Panel Defect Complaint', '/productiontaskerror/index');
                                $mainMenuProduction[] = $menuProduction;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
                                $mainMenuTaskWeight = MenuModel::newMenuItems('<i class="fas fa fa-percent"></i>', 'Task Weight', '#');
                                $mainMenuTaskWeight->children[] = MenuModel::newMenuItems('', 'Project List', '/production/production/index-production-project-list');
                                $mainMenuTaskWeight->children[] = MenuModel::newMenuItems('', 'Default', '/reporting/reconfigure-task-weight');
                                $mainMenuProduction[] = $mainMenuTaskWeight;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin])) {
                                $menuTaskAssignment = MenuModel::newMenuItems('<i class="fas fa-tasks"></i>', 'Task Assignment', '#');
                                $menuTaskAssignment->children[] = MenuModel::newMenuItems('', 'Fabrication Department', '/fab-task/index-fab-project-list');
                                $menuTaskAssignment->children[] = MenuModel::newMenuItems('', 'Electrical Department', '/elec-task/index-elec-project-list');
                                $mainMenuProduction[] = $menuTaskAssignment;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Wkr, AuthItem::ROLE_PrdnFab_Wkr])) {
                                $menuPanelTask = MenuModel::newMenuItems('<i class="fas fa-tasks"></i>', 'Panel Task', '#');
                                $menuPanelTask->children[] = MenuModel::newMenuItems('', 'My Task', '/production/panel-task-status/my-active-task');
                                $mainMenuProduction[] = $menuPanelTask;
                            }

                            if (true) {
                                $menuTesting = MenuModel::newMenuItems('<i class="far fa-thumbs-up"></i>', 'Panel Testing', '#');
                                $menuTesting->children[] = MenuModel::newMenuItems('', 'Panel List', '/test/testing/index-project-lists');
                                $menuTesting->children[] = MenuModel::newMenuItems('', 'Test Template', '/test/test-template/index');
                                $mainMenuProduction[] = $menuTesting;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin, AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_PrdnElec_Wkr, AuthItem::ROLE_PrdnFab_Wkr])) {
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-industry"></i>&nbsp;&nbsp;&nbsp;Production
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <?php
                                        foreach ($mainMenuProduction as $key => $menu) {
                                            echo ' <li class="dropdown dropright pl-0">';
                                            if (empty($menu->children)) {
                                                echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                            } else {
                                                echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                                echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                                echo $menu->title ?? "";
                                                echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                                foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                    echo "<li>";
                                                    if (empty($childrenMenu->children)) {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                    } else {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                                "javascript:void(0)",
                                                                ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                        echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                        foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                            echo "<li>";

                                                            if (empty($grandChildrenMenu->children)) {
                                                                echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                            }
                                                            echo "</li>";
                                                        }echo "</ul>";
                                                    }echo "</li>";
                                                }echo "</ul>";
                                            }echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php
                            $mainMenuCmms = [];

                            if (MyCommonFunction::checkRoles([AuthItem::Module_CMMS])) {
                                $menuReportPreventiveMaintenance = MenuModel::newMenuItems('<i class="fas fa-clipboard-list"></i>', 'Asset List', '/cmms/cmms-asset-list');
                                $menuReportFaultList = MenuModel::newMenuItems('<i class="fas fa-solid fa-triangle-exclamation"></i>', 'Fault List', '#');
                                $menuReportFaultList->children[] = MenuModel::newMenuItems('', 'Personal', '/cmms/cmms-fault-list/personal-active');
                                $menuReportFaultList->children[] = MenuModel::newMenuItems('', 'Superior', '/cmms/cmms-fault-list/superior-active');
                                $menuReportCorrectiveWorkOrder = MenuModel::newMenuItems('<i class="fas fa-solid fa-triangle-exclamation"></i>', 'Corrective Work Order', '#');
                                $menuReportCorrectiveWorkOrder->children[] = MenuModel::newMenuItems('', 'Assigned Tasks', '/cmms/cmms-corrective-work-order-master/view-assigned-tasks');
                                $menuReportCorrectiveWorkOrder->children[] = MenuModel::newMenuItems('', 'Superior', '/cmms/cmms-corrective-work-order-master/view-superior');
                                $mainMenuCmms[] = $menuReportPreventiveMaintenance;
                                $mainMenuCmms[] = $menuReportFaultList;
                                $mainMenuCmms[] = $menuReportCorrectiveWorkOrder;
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-tools"></i>&nbsp;&nbsp;&nbsp;CMMS 
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <?php
                                        foreach ($mainMenuCmms as $key => $menu) {
                                            echo ' <li class="dropdown dropright pl-0">';
                                            if (empty($menu->children)) {
                                                echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                            } else {
                                                echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                                echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                                echo $menu->title ?? "";
                                                echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                                foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                    echo "<li>";
                                                    if (empty($childrenMenu->children)) {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                    } else {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                                "javascript:void(0)",
                                                                ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                        echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                        foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                            echo "<li>";

                                                            if (empty($grandChildrenMenu->children)) {
                                                                echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                            }
                                                            echo "</li>";
                                                        }echo "</ul>";
                                                    }echo "</li>";
                                                }echo "</ul>";
                                            }echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php
                            /**
                             * Menu dropdown 2
                             */
                            $mainMenuItems2 = [];

                            $menuLeave = MenuModel::newMenuItems('<i class="fas fa-calendar-times"></i>', 'Leave', '#');
                            $menuLeave->children[] = MenuModel::newMenuItems('', 'Relief', '/working/leavemgmt/relief-leave-approval');
                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Superior])) {
                                $menuLeave->children[] = MenuModel::newMenuItems('', 'Superior', '/working/leavemgmt/superior-leave-approval');
                            }
//                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
//                                $menuLeave->children[] = MenuModel::newMenuItems('', 'HR', '/working/leavemgmt/hr-leave-approval');
//                            }
                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
                                $menuLeave->children[] = MenuModel::newMenuItems('', 'HR', '/working/leavemgmt/hr-leave-approval');
                            } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_FinanceExecutive])) {
                                $menuLeave->children[] = MenuModel::newMenuItems('', 'HR', '/working/leavemgmt/hr-all-leave');
                            }
                            $mainMenuItems2[] = $menuLeave;

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
                                $menuHrDocs = MenuModel::newMenuItems('<i class="fas fa-folder-open"></i>', 'HR', '#');
                                $menuHrDocs->children[] = MenuModel::newMenuItems('', 'Documents', '/working/hr-employee-document/index');
                                $menuHrDocs->children[] = MenuModel::newMenuItems('', 'Incentive', '/working/hr-employee-incentive/factory-staff-performance-detail');
                                $mainMenuItems2[] = $menuHrDocs;
                            }

                            $menuAppraisal = MenuModel::newMenuItems('<i class="far fa-id-badge"></i>', 'Staff Appraisal', '#');
                            $menuAppraisal->children[] = MenuModel::newMenuItems('', 'Staff Appraisal', '/appraisalgnrl/index-rating');
                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Superior])) {
                                $menuAppraisal->children[] = MenuModel::newMenuItems('', 'Superior Appraisal', '/appraisalgnrl/index-main');
                            }
                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
                                $menuAppraisal->children[] = MenuModel::newMenuItems('', 'HR Appraisal', '/appraisal/index');
                            }
                            $mainMenuItems2[] = $menuAppraisal;

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_HR_Senior, AuthItem::ROLE_SystemAdmin, AuthItem::Module_attendance])) {
                                $menuAttendance = MenuModel::newMenuItems('<i class="fas fa-list-ul"></i>', 'Staff Attendance', '/attendance');
                                $mainMenuItems2[] = $menuAttendance;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Normal, AuthItem::ROLE_Director, AuthItem::ROLE_PC_Finance])) {
                                $menuPetty = MenuModel::newMenuItems('<i class="fas fa-money-bill-wave"></i>', 'Petty Cash', '#');
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Normal])) {
                                    $menuPetty->children[] = MenuModel::newMenuItems('', 'Personal', '/office/petty-cash/personal-pending');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_PC_Finance])) {
                                    $menuPetty->children[] = MenuModel::newMenuItems('', 'Finance', '/office/petty-cash/finance-approval-pending');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director])) {
                                    $menuPetty->children[] = MenuModel::newMenuItems('', 'Director', '/office/petty-cash/director-approval-pending');
                                }

                                $mainMenuItems2[] = $menuPetty;
                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_Normal, AuthItem::ROLE_PRF_Superior])) {
                                $menuPrereqForm = MenuModel::newMenuItems('<i class="fas fa-file-alt"></i>', 'Pre-Requisition Form', '#');
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_Normal])) {
                                    $menuPrereqForm->children[] = MenuModel::newMenuItems('', 'Personal', '/office/prereq-form-master/personal-pending-approval');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_Superior])) {
                                    $menuPrereqForm->children[] = MenuModel::newMenuItems('', 'Superior', '/office/prereq-form-master/superior-pending-approval');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_PRF_SuperUser])) {
                                    $menuPrereqForm->children[] = MenuModel::newMenuItems('', 'Super User', '/office/prereq-form-master/superuser-pending-approval');
                                }
                                $mainMenuItems2[] = $menuPrereqForm;
                            }

                            if (MyCommonFunction::checkRoles([
                                        AuthItem::ROLE_CM_Normal,
                                        AuthItem::ROLE_CM_Superior,
                                        AuthItem::ROLE_CM_Finance,
                                        AuthItem::ROLE_CE_Superior,
                                        AuthItem::ROLE_CE_HR
                                    ])) {
                                // Main Claim menu
                                $menuClaim = MenuModel::newMenuItems('<i class="fas fa-receipt"></i>', 'Claim', '#');

                                // ---- Claim (Normal / Superior / Finance) ----
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Normal])) {
                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Personal', '/office/claim/personal-claim-pending');
                                }

                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Superior])) {
                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Superior', '/office/claim/superior-approval-pending');
                                }

                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) {
                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Finance', '/office/claim/finance-approval-pending');
                                }

                                // ---- Claim Entitlement (as a submenu under Claim) ----
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior, AuthItem::ROLE_CE_HR])) {
                                    $menuClaimEntitlement = MenuModel::newMenuItems('', 'Claim Entitlement', '#');

                                    if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior])) {
                                        $menuClaimEntitlement->children[] = MenuModel::newMenuItems('', 'Superior', '/office/claim-entitlement/superior-pending-approval');
                                    }

                                    if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_HR])) {
                                        $menuClaimEntitlement->children[] = MenuModel::newMenuItems('', 'HR', '/office/claim-entitlement/pending-approval');
                                    }

                                    // Add Claim Entitlement as sub-item of Claim
                                    $menuClaim->children[] = $menuClaimEntitlement;
                                }

                                $mainMenuItems2[] = $menuClaim;
                            }

//                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Normal, AuthItem::ROLE_CM_Superior, AuthItem::ROLE_CM_Finance])) {
//                                $menuClaim = MenuModel::newMenuItems('<i class="fas fa-receipt"></i>', 'Claim', '#');
//                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Normal])) {
//                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Personal', '/office/claim/personal-claim-pending');
//                                }
//                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Superior])) {
//                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Superior', '/office/claim/superior-approval-pending');
//                                }
//                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) {
//                                    $menuClaim->children[] = MenuModel::newMenuItems('', 'Finance', '/office/claim/finance-approval-pending');
//                                }
//                                $mainMenuItems2[] = $menuClaim;
//                            }
//
//                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior, AuthItem::ROLE_CE_HR])) {
//                                $menuClaimEntitlement = MenuModel::newMenuItems('<i class="fas fa-clipboard-check"></i>', 'Claim Entitlement', '#');
//                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_Superior])) {
//                                    $menuClaimEntitlement->children[] = MenuModel::newMenuItems('', 'Superior', '/office/claim-entitlement/superior-pending-approval');
//                                }
//                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_HR])) {
//                                    $menuClaimEntitlement->children[] = MenuModel::newMenuItems('', 'HR', '/office/claim-entitlement/pending-approval');
//                                }
//                                $mainMenuItems2[] = $menuClaimEntitlement;
//                            }

                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal, AuthItem::ROLE_Eh_Super])) {
                                $menuEh = MenuModel::newMenuItems('<i class="fa fa-book"></i>', 'Employee Handbook', '#');
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal])) {
                                    $menuEh->children[] = MenuModel::newMenuItems('', 'Personal', '/office/employee-handbook/view-employee-handbook');
                                }
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super])) {
                                    $menuEh->children[] = MenuModel::newMenuItems('', 'Super User', '/office/employee-handbook/index');
                                }
                                $mainMenuItems2[] = $menuEh;
                            }
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Office
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <?php
                                    foreach ($mainMenuItems2 as $key => $menu) {
                                        echo ' <li class="dropdown dropright pl-0">';
                                        if (empty($menu->children)) {
                                            echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                        } else {
                                            echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                            echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                            echo $menu->title ?? "";
                                            echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                            foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                echo "<li>";
                                                if (empty($childrenMenu->children)) {
                                                    echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                } else {
                                                    echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                            "javascript:void(0)",
                                                            ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                    foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                        echo "<li>";

                                                        if (empty($grandChildrenMenu->children)) {
                                                            echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                        }
                                                        echo "</li>";
                                                    }echo "</ul>";
                                                }echo "</li>";
                                            }echo "</ul>";
                                        }echo "</li>";
                                    }
                                    ?>
                                </ul>
                            </li>

                            <?php
                            /**
                             * Menu dropdown 3
                             */
//                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_StockOutbound])) {
                            if (true) {
                                $mainMenuItems = [];
                                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_Stock_Ob_View])) {
                                    $menuInventory = MenuModel::newMenuItems('<i class="fas fa-clipboard-list"></i>', 'Stock Outbound', '#');
                                    $menuInventory->children[] = MenuModel::newMenuItems('', 'Stock Outbound Main', '/stockoutbound');
                                    $menuInventory->children[] = MenuModel::newMenuItems('', 'Stock Dispatch Master', '/stock-dispatch-master/index');
//                                $menuInventory->children[] = MenuModel::newMenuItems('', 'Quotation Template', '/projectqtemplate/indexpqrevision');
//                                $menuInventory->children[] = MenuModel::newMenuItems('', 'Report', '/projectqtemplate/indexpqrevision');
                                    $mainMenuItems[] = $menuInventory;
                                }
                                $menuInventory2 = MenuModel::newMenuItems('<i class="fas fa-list"></i>', 'My Acknowledgement List', '/stock-dispatch-master/my-pending-acknowledgements');
                                $mainMenuItems[] = $menuInventory2;

                                //stock
                                $menuInventory3 = MenuModel::newMenuItems('<i class="fas fa-clipboard-list"></i>', 'Stock', '/inventory/inventory/item-list');
                                $mainMenuItems[] = $menuInventory3;

                                //purchasing
                                $menuInventory4 = MenuModel::newMenuItems('<i class="fas fa-clipboard-list"></i>', 'Purchasing', '#');
                                $menuInventory4Exec = MenuModel::newMenuItems('', 'Executive', '/inventory/inventory/executive-pre-requisition-pending-approval');
//                                $menuInventory4Exec->children[] = MenuModel::newMenuItems('', 'New Item', '/inventory/inventory/executive-pre-requisition-pending-approval');
//                                $menuInventory4Exec->children[] = MenuModel::newMenuItems('', 'Reorder Item', '/inventory/inventory/items-to-reorder');
                                $menuInventory4->children[] = $menuInventory4Exec;

                                $menuInventory4Asist = MenuModel::newMenuItems('', 'Assistant', '/inventory/inventory/assistant-pre-requisition-pending-approval');
//                                $menuInventory4Asist->children[] = MenuModel::newMenuItems('', 'New Item', '/inventory/inventory/executive-pre-requisition-pending-approval');
//                                $menuInventory4Asist->children[] = MenuModel::newMenuItems('', 'Reorder Item', '/inventory/inventory/pre-requisition-all-application');
                                $menuInventory4->children[] = $menuInventory4Asist;

                                $menuInventory4Proj = MenuModel::newMenuItems('', 'Project Coordinator', '/inventory/inventory/projcoor-pre-requisition-pending-approval');
                                $menuInventory4->children[] = $menuInventory4Proj;
                                
                                $menuInventory4Cmms = MenuModel::newMenuItems('', 'Head of Maintenance', '/inventory/inventory/cmmsprerequisition');
                                $menuInventory4->children[] = $menuInventory4Cmms;

                                $mainMenuItems[] = $menuInventory4;

                                //receiving
                                $menuInventory5 = MenuModel::newMenuItems('<i class="fas fa-clipboard-check"></i>', 'Receiving', '#');
                                $menuInventory5->children[] = MenuModel::newMenuItems('', 'Executive', '/inventory/inventory/po?type=execPendingReceiving');
                                $mainMenuItems[] = $menuInventory5;
                                ?>

                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Inventory Control
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <?php
                                        foreach ($mainMenuItems as $key => $menu) {
                                            echo ' <li class="dropdown dropright pl-0">';
                                            if (empty($menu->children)) {
                                                echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                            } else {
                                                echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                                echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                                echo $menu->title ?? "";
                                                echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                                foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                    echo "<li>";
                                                    if (empty($childrenMenu->children)) {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                    } else {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                                "javascript:void(0)",
                                                                ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                        echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                        foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                            echo "<li>";

                                                            if (empty($grandChildrenMenu->children)) {
                                                                echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                            }
                                                            echo "</li>";
                                                        }echo "</ul>";
                                                    }echo "</li>";
                                                }echo "</ul>";
                                            }echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            }

                            /**
                             * Menu dropdown 4
                             */
                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin])) {
                                $mainMenuReporting = [];

                                $menuReportOverall = MenuModel::newMenuItems('<i class="fas fa-clipboard"></i>', 'Overall', '#');
                                $menuReportOverall->children[] = MenuModel::newMenuItems('', 'Quotation Hit', '/reporting/get-quotation-percentage');

                                $menuReportPerformance = MenuModel::newMenuItems('<i class="fas fa-clipboard"></i>', 'Performance', '#');
                                $menuReportPerformance->children[] = MenuModel::newMenuItems('', 'Individual', '/reporting/get-individual-performance');
                                $menuReportPerformance->children[] = MenuModel::newMenuItems('', 'Department', '/reporting/get-department-performance-detail');
//                                $menuReportPerformance->children[] = MenuModel::newMenuItems('', 'Reconfigure Task Weight', '/reporting/reconfigure-task-weight');

                                $menuReportChart = MenuModel::newMenuItems('<i class="fas fa-clipboard"></i>', 'Report Charts', '#');
                                $menuReportChart->children[] = MenuModel::newMenuItems('', 'Individual', '/reporting/get-chart-factory-staff');
                                $menuReportChart->children[] = MenuModel::newMenuItems('', 'Project Coordinator', '/reporting/get-chart-quotation-hit-individual');
                                $menuReportChart->children[] = MenuModel::newMenuItems('', 'Quotation Hit', '/reporting/get-chart-quotation-hit');
                                $menuReportChart->children[] = MenuModel::newMenuItems('', 'Department Task Completion', '/reporting/get-chart-department-task-completion');
                                $menuReportChart->children[] = MenuModel::newMenuItems('', 'Pushed-to-Completed Tracker Report', '/reporting/get-chart-department-task-completion-amount');

                                $mainMenuReporting[] = $menuReportOverall;
                                $mainMenuReporting[] = $menuReportPerformance;
                                $mainMenuReporting[] = $menuReportChart;
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Report
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <?php
                                        foreach ($mainMenuReporting as $key => $menu) {
                                            echo ' <li class="dropdown dropright pl-0">';
                                            if (empty($menu->children)) {
                                                echo Html::a($menu->icon . "&nbsp;&nbsp;&nbsp;" . $menu->title, $menu->link, ['class' => 'pl-4']);
                                            } else {
                                                echo '<a class="dropdown-toggle pl-4" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" style="min-width:250px">';
                                                echo (empty($menu->icon) ? "" : ($menu->icon . "&nbsp;&nbsp;&nbsp;"));
                                                echo $menu->title ?? "";
                                                echo '</a><ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                                                foreach ($menu->children as $childrenKey => $childrenMenu) {
                                                    echo "<li>";
                                                    if (empty($childrenMenu->children)) {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""), $childrenMenu->link);
                                                    } else {
                                                        echo Html::a((empty($childrenMenu->icon) ? "" : ($childrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($childrenMenu->title ?? ""),
                                                                "javascript:void(0)",
                                                                ["class" => "dropdown-toggle", "id" => "navbarDropdown_$childrenKey", "role" => "button", "data-toggle" => "dropdown", "style" => "min-width:250px"]);

                                                        echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown_$childrenKey' . $childrenKey . '">';
                                                        foreach ($childrenMenu->children as $grandChildrenMenu) {
                                                            echo "<li>";

                                                            if (empty($grandChildrenMenu->children)) {
                                                                echo Html::a((empty($grandChildrenMenu->icon) ? "" : ($grandChildrenMenu->icon . "&nbsp;&nbsp;&nbsp;")) . ($grandChildrenMenu->title ?? ""), $grandChildrenMenu->link);
                                                            }
                                                            echo "</li>";
                                                        }echo "</ul>";
                                                    }echo "</li>";
                                                }echo "</ul>";
                                            }echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                        <ul class="navbar-nav mr-1">
                            <?php
                            if ($thisUser->can($sysadmin) || $thisUser->can($hr)) {
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-users-cog"></i>&nbsp;&nbsp;&nbsp;System Administrator
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <li class="dropdown dropleft pl-0">
                                            <a class="dropdown-toggle pl-4" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-users-cog"></i>&nbsp;&nbsp;&nbsp;Users
                                            </a>
                                            <ul class="dropdown-menu ">
                                                <?php
                                                echo Html::tag('li',
                                                        Html::a('<i class="fas fa-users"></i>&nbsp;&nbsp;&nbsp;Manage Users', '/sysadmin/user/index', ['class' => 'pl-4'])
                                                        , ['class' => 'pl-0']);
                                                echo Html::tag('li',
                                                        Html::a('<i class="fas fa-user-tag"></i>&nbsp;&nbsp;&nbsp;Config - Position', '/sysadmin/user-position/index', ['class' => 'pl-4'])
                                                        , ['class' => 'pl-0']);
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-user-circle"></i>&nbsp;&nbsp;&nbsp;<?= $thisUser->identity->username ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <?php
                                    echo Html::tag('li',
                                            Html::a('<i class="far fa-id-card"></i>&nbsp;&nbsp;&nbsp;My Space', '/profile/view-profile', ['class' => 'pl-4'])
                                            , ['class' => 'pl-0']);
                                    ?>
                                    <li><div class="dropdown-divider p-0 m-0"></div></li>
                                    <li>
                                        <?=
                                        Html::beginForm(['/site/logout'], 'post')
                                        . Html::submitButton(
                                                '<i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;&nbsp;Logout ',
//                                                 '<i class="fas fa-running"></i>&nbsp;&nbsp;&nbsp;"Good Bye" ',
                                                ['class' => 'btn btn-block logout pl-4 text-left']
                                        )
                                        . Html::endForm()
                                        ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <?php
//                        $this->endCache();
//                    }
                } else {
                    ?>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto nav"></ul>
                        <ul class="navbar-nav nav">
                            <?php
                            echo Html::tag('li',
                                    Html::a('Login <i class="fas fa-sign-in-alt"></i>', '/site/login', ['class' => 'pl-4'])
                                    , ['class' => 'pl-0']);
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </nav>
            <div class="mainContainer p-0" style='margin-top: 70px'>
                <?php
                Modal::begin([
                    'id' => 'myModal',
                    'title' => '<p class="modal-title"></p>',
                    'size' => 'modal-xl',
                    'centerVertical' => true,
                    'options' => ['style' => 'padding:0px;margin:0px;']
                ]);
                echo "<div id='myModalContent' style='padding:0px;margin:0px;white-space: normal;'></div>";
                Modal::end();

                Modal::begin([
                    'id' => 'myModalMedium',
                    'title' => '<p class="modal-title"></p>',
                    'size' => 'modal-md',
                    'centerVertical' => true,
                    'options' => ['style' => 'padding:0px;margin:0px;']
                ]);

                echo "<div id='myModalContentMedium' style='padding:0px;margin:0px;'></div>";
                Modal::end();

                Modal::begin([
                    'id' => 'myModalSmall',
                    'title' => '<p class="modal-title"></p>',
                    'size' => 'modal-sm',
                    'centerVertical' => true,
                    'options' => ['style' => 'padding:0px;margin:0px;']
                ]);

                echo "<div id='myModalContentSmall' style='padding:0px;margin:0px;'></div>";
                Modal::end();
                ?>


                <div class="modal fade" tabindex="-1" role="dialog" id="spinnerModal">
                    <div class="modal-dialog modal-dialog-centered text-center" role="document">
                        <span class="fa fa-spinner fa-spin fa-3x w-100"></span>
                    </div>
                </div>

                <!--display:none;--> 
                <div id="loading-icon" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.5); z-index:1000;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <img src="/images/loading-gif.gif" alt="Loading..." height="40px">
                        <p class="text-white">Loading...</p>
                    </div>
                </div>
                <?php
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'options' => [],
                ])
                ?>
                <?= Alert::widget() ?>
                <div class="container-fluid">
                    <?= $content ?>
                </div>
                <?php
                Modal::begin([
                    'id' => 'alertModal',
                    'title' => '<i class="fas fa-exclamation-triangle text-red"></i>',
                    'centerVertical' => true,
                    'headerOptions' => [
                        'class' => 'm-0 py-1'
                    ]
                ]);
                echo "<div id='alertModalContent'></div>";
                Modal::end();
                ?>

                <?php
                $modalFooter = yii\bootstrap4\Html::button('Close', ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary'])
                        . yii\bootstrap4\Html::submitButton('Submit', ['class' => 'btn btn-success']);

                Modal::begin([
                    'id' => 'confirmModal',
                    'title' => 'Confirm',
                    'centerVertical' => true,
                    'footer' => $modalFooter
                ]);
                echo "<div id='confirmModalContent'></div>";
                Modal::end();
                ?>
            </div>
        </div>
        <footer class="footer">
            <div class="col-12">
                <span class="">&copy; <?= Html::encode(Yii::$app->params['application_name']) ?> </span>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>

<?php

function checkAuthToDisplay($authsList, $specialAuthList) {
    if (!$authsList && !$specialAuthList) {
        return true;
    }

    $auths = explode(',', $authsList);
    $specialAuth = explode(',', $specialAuthList);

    $thisUser = Yii::$app->user;

    foreach ($auths as $auth) {
        if ($thisUser->can($auth)) {
            return true;
        }
    }

    foreach ($specialAuth as $auth) {
        if (checkSpecialFunction($auth)) {
            return true;
        }
    }
    return false;
}
?>