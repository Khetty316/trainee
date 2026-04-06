<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\ContactForm;
use common\models\myTools\MyFormatter;
use frontend\models\report\ReportingModel;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\projectproduction\task\TaskAssignment;
use frontend\models\projectproduction\VStaffCompetedPanelRecordFabrication;
use frontend\models\projectproduction\VStaffCompetedPanelRecordElectrical;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use frontend\models\office\pettyCash\PettyCashReplenishment;
use frontend\models\projectproduction\electrical\ProductionElecTasksError;
use frontend\models\projectproduction\fabrication\ProductionFabTasksError;
use frontend\models\projectproduction\electrical\ProductionElecTasksErrorStaff;
use frontend\models\projectproduction\fabrication\ProductionFabTasksErrorStaff;

include_once(Yii::getAlias('@webroot') . "/library/phpqrcode/qrlib.php");

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'dashboard'],
                'rules' => [
//                    [
//                        'actions' => ['login','request-password-reset','actionResendVerificationEmail','actionResetPassword','actionVerifyEmail'],
//                        'allow' => true,
//                        'roles' => ['?'],
//                    ],

                    [
                        'actions' => ['logout', 'index', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {

        if (Yii::$app->user->isGuest) {
            return $this->render('index');
        } else {
            return $this->redirect('/site/dashboard');
        }
    }

//    public function actionDashboard() {
////        $announcements = \frontend\models\working\announcement\AnnouncementMaster::find()->where("active=1")->orderBy(['id' => SORT_DESC])->all();
//
//        return $this->render('dashboard', [
////                    "announcements" => $announcements
//        ]);
//    }
//    public function actionDashboard() {
//        $model = new ReportingModel();
//        $model->selectedMonth = date('m');
//        $model->selectedYear = date('Y');
//        $dateFrom = date('Y') . '-' . date('m', strtotime('-1 month')) . '-23';
//        $dateTo = date('Y') . '-' . date('m') . '-22';
//        $model->dateTo = $dateTo;
//        $model->dateFrom = MyFormatter::changeDateFormat_readToDB($dateFrom);
//        $model->is_internalProject = '';
//        $project_coordinator = \common\models\User::findOne(Yii::$app->user->id);
//
//        if ($model->load(Yii::$app->request->post())) {
//            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
//            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
//        }
//
//        //Pending Action
//        $totalPendingReliefLeave = $this->getTotalPendingReliefApprovalLeave();
//        $totalPendingSuperiorLeave = $this->getTotalPendingSuperiorApprovalLeave();
//        $totalPendingHrLeave = $this->getTotalPendingHrApprovalLeave();
//
//        $totalPendingSuperiorClaim = $this->getTotalPendingSuperiorApprovalClaim();
//        $totalPendingFinanceClaim = $this->getTotalPendingFinanceApprovalClaim();
//
//        $totalPendingSuperiorClaimEntitlement = $this->getTotalPendingSuperiorClaimEntitlement();
//
//        $totalPendingSuperiorPrf = $this->getTotalPendingSuperiorPrf();
//
//        //new public document
//        $totalNewPublicDoc = $this->getTotalNewPublicDoc();
//
//        return $this->render('dashboard', [
//                    'model' => $model,
//                    'project_coordinator' => $project_coordinator ?? null,
//                    'totalPendingReliefLeave' => $totalPendingReliefLeave,
//                    'totalPendingSuperiorLeave' => $totalPendingSuperiorLeave,
//                    'totalPendingHrLeave' => $totalPendingHrLeave,
//                    'totalPendingSuperiorClaim' => $totalPendingSuperiorClaim,
//                    'totalPendingFinanceClaim' => $totalPendingFinanceClaim,
//                    'totalPendingSuperiorClaimEntitlement' => $totalPendingSuperiorClaimEntitlement,
//                    'totalPendingSuperiorPrf' => $totalPendingSuperiorPrf,
//                    'totalNewPublicDoc' => $totalNewPublicDoc,
//        ]);
//    }

    public function actionDashboard() {
        $model = new ReportingModel();
        $model->selectedMonth = date('m');
        $model->selectedYear = date('Y');

        /**
         * Always calculate using a real base date
         * 1) Start from first day of this month
         * 2) Go back 1 month
         * 3) Set day to 23
         */
        $lastMonth = strtotime('first day of this month -1 month');
        $dateFrom = date('Y-m-23', $lastMonth);

        /**
         * Date to = 22nd of current month
         */
        $currentMonth = strtotime('first day of this month');
        $dateTo = date('Y-m-22', $currentMonth);

        $model->dateFrom = MyFormatter::changeDateFormat_readToDB($dateFrom);
        $model->dateTo = $dateTo;
        $model->is_internalProject = '';

        $project_coordinator = \common\models\User::findOne(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post())) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
        }

        //Pending Action
        $totalPendingReliefLeave = $this->getTotalPendingReliefApprovalLeave();
        $totalPendingSuperiorLeave = $this->getTotalPendingSuperiorApprovalLeave();
        $totalPendingHrLeave = $this->getTotalPendingHrApprovalLeave();
        $totalPendingDirectorLeave = $this->getTotalPendingDirectorApprovalLeave();

        $totalPendingSuperiorClaim = $this->getTotalPendingSuperiorApprovalClaim();
        $totalPendingFinanceClaim = $this->getTotalPendingFinanceApprovalClaim();

        $totalPendingSuperiorClaimEntitlement = $this->getTotalPendingSuperiorClaimEntitlement();

        $totalPendingSuperiorPrf = $this->getTotalPendingSuperiorPrf();

        //new public document
        $totalNewPublicDoc = $this->getTotalNewPublicDoc();

        return $this->render('dashboard', [
                    'model' => $model,
                    'project_coordinator' => $project_coordinator ?? null,
                    'totalPendingReliefLeave' => $totalPendingReliefLeave,
                    'totalPendingSuperiorLeave' => $totalPendingSuperiorLeave,
                    'totalPendingHrLeave' => $totalPendingHrLeave,
                    'totalPendingSuperiorClaim' => $totalPendingSuperiorClaim,
                    'totalPendingFinanceClaim' => $totalPendingFinanceClaim,
                    'totalPendingSuperiorClaimEntitlement' => $totalPendingSuperiorClaimEntitlement,
                    'totalPendingSuperiorPrf' => $totalPendingSuperiorPrf,
                    'totalNewPublicDoc' => $totalNewPublicDoc,
                    'totalPendingDirectorLeave' => $totalPendingDirectorLeave,
        ]);
    }

//    public function actionPendingActionInventory() {
//        $newItemApprovedForPurchase = \frontend\models\office\preReqForm\PrereqFormMaster::find()
//                ->where(['source_module' => 2])
//                ->andWhere(['is_deleted' => 0])
//                ->andWhere(['status' => \frontend\models\RefGeneralStatus::STATUS_Approved])
//                ->andWhere(['inventory_flag' => 1])
//                ->count();
//
//        $newItemReadyForPo = \frontend\models\inventory\InventoryPurchaseRequest::find()
//                ->where(['source_type' => 1])
//                ->andWhere(['po_status' => 1])
//                ->count();
//
//        $reorderItemReadyForPo = \frontend\models\inventory\InventoryPurchaseRequest::find()
//                ->where(['source_type' => 2])
//                ->andWhere(['po_status' => 1])
//                ->count();
//
//        return $this->renderAjax('pending_action_inventory', [
//                    'newItemApprovedForPurchase' => $newItemApprovedForPurchase,
//                    'newItemReadyForPo' => $newItemReadyForPo,
//                    'reorderItemReadyForPo' => $reorderItemReadyForPo,
//        ]);
//    }

    public function actionPendingPanelDefectReadStatus() {
        $staffId = Yii::$app->user->id;
        $pendingReadPanelDefectStaffElec = ProductionElecTasksError::find()
                ->alias('e')
                ->leftJoin(
                        ProductionElecTasksErrorStaff::tableName() . ' s',
                        's.production_elec_tasks_error_id = e.id'
                )
                ->where(['e.is_read' => 1, 's.staff_id' => $staffId, 's.is_read' => 1, 's.read_at' => null])
                ->count();

        $pendingReadPanelDefectStaffFab = ProductionFabTasksError::find()
                ->alias('f')
                ->leftJoin(
                        ProductionFabTasksErrorStaff::tableName() . ' s',
                        's.production_fab_tasks_error_id = f.id'
                )
                ->where(['f.is_read' => 1, 's.staff_id' => $staffId, 's.is_read' => 1, 's.read_at' => null])
                ->count();

        $myPendingReadStatusCount = $pendingReadPanelDefectStaffElec + $pendingReadPanelDefectStaffFab;
        $staffPendingReadStatusCount = ProductionElecTasksError::find()->where(['is_read' => 1])->count() + ProductionFabTasksError::find()->where(['is_read' => 1])->count();

        return $this->renderAjax('pending_panel_defect_read_status', [
                    'myPendingReadStatusCount' => $myPendingReadStatusCount,
                    'staffPendingReadStatusCount' => $staffPendingReadStatusCount,
        ]);
    }

    public function actionPerformanceFactoryStaffBanner() {
        $model = new ReportingModel();
        $worker = \common\models\User::findOne(Yii::$app->user->id);
        $model->userId = $worker->id;
        $dateFrom = date('Y') . '-' . date('m', strtotime('-1 month')) . '-23';
        $dateTo = date('Y') . '-' . date('m') . '-22';

        $model->dateTo = $dateTo;
        $model->dateFrom = MyFormatter::changeDateFormat_readToDB($dateFrom);
        $model->is_internalProject = '';

        $fabPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
        $elecPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);

        $individualAmtFab = 0;
        $individualAmtElec = 0;
        if (!empty($fabPerformanceList_individual)) {
            $individualAmtFab = round(array_sum(array_map('floatval', array_column($fabPerformanceList_individual, 'performanceAmount'))), 2);
        }
        if (!empty($elecPerformanceList_individual)) {
            $individualAmtElec = round(array_sum(array_map('floatval', array_column($elecPerformanceList_individual, 'performanceAmount'))), 2);
        }

        $contributionAmt = $individualAmtFab + $individualAmtElec;
        $staffIncentiveAmount = $model->calculateIncentiveFactoryStaff($contributionAmt);

        // Determine which banner to show
        $bannerData = $this->determineBannerToShow($contributionAmt);
        return $this->renderAjax('performanceFactoryStaffBanner', [
                    'staffIncentiveAmount' => $staffIncentiveAmount,
                    'contributionAmt' => $contributionAmt,
                    'bannerData' => $bannerData,
        ]);
    }

    /**
     * Determine which banner to show (only one banner at a time)
     * Banner 1: Encouragement/Achievement messages (has priority)
     * Banner 2: Warning banner (only if Banner 1 condition not met)
     * @param float $contributionAmt
     * @return array
     */
    private function determineBannerToShow($contributionAmt) {
        $incentiveTiers = ReportingModel::FACTORY_STAFF_PERFORMANCE_INCENTIVE;

        // Sort tiers by threshold to ensure proper order
        usort($incentiveTiers, function ($a, $b) {
            return $a['threshold'] <=> $b['threshold'];
        });

        $tier1 = $incentiveTiers[0]; // ['threshold' => 20000, 'incentive' => 100]
        $tier2 = $incentiveTiers[1]; // ['threshold' => 40000, 'incentive' => 150]
        // Calculate cumulative incentives
        $tier1TotalIncentive = $tier1['incentive']; // RM100
        $tier2TotalIncentive = $tier1['incentive'] + $tier2['incentive']; // RM100 + RM150 = RM250

        $currentDay = 16;

        $data = [
            'showBanner' => false,
            'bannerType' => '', // 'encouragement', 'congratulations', or 'warning'
            'message' => '',
            'tier' => 0,
            'incentiveAmount' => 0,
        ];

        if ($contributionAmt >= $tier1['threshold']) {
            $data['showBanner'] = true;

            if ($contributionAmt >= ($tier2['threshold'] - 5000) && ($contributionAmt >= $tier2['threshold'] - 1)) {
                $remaining_amount = $tier2['threshold'] - $contributionAmt;
                $data['tier'] = 2;
                $data['message'] = "You are nearly RM {$remaining_amount} short to receive Tier 2 incentive (RM{$tier2TotalIncentive}). Keep it up!";
                $data['incentiveAmount'] = $tier2TotalIncentive;
            } else if ($contributionAmt >= $tier2['threshold']) {
                // Achieved Tier 2 (highest tier)
                $data['bannerType'] = 'congratulations';
                $data['tier'] = 2;
                $data['message'] = "Congratulations on Tier 2 achievement (RM{$tier2TotalIncentive})!";
                $data['incentiveAmount'] = $tier2TotalIncentive;
            } else {
                // Achieved Tier 1, aiming for Tier 2
                $data['bannerType'] = 'congratulations';
                $data['tier'] = 1;
                $data['message'] = "Congratulations on Tier 1 achievement (RM{$tier1TotalIncentive})! Please try your best to aim for Tier 2 incentive (RM{$tier2TotalIncentive}).";
                $data['incentiveAmount'] = $tier1TotalIncentive;
                $data['nextTierIncentive'] = $tier2TotalIncentive;
            }
        } else {
            if ($currentDay >= 15 && $currentDay <= 22) {
                $remaining = $tier1['threshold'] - $contributionAmt;
                $daysLeft = 22 - $currentDay;

                $data['showBanner'] = true;
                $data['bannerType'] = 'warning';
                $data['tier'] = 0;
                $data['message'] = "Alert! You are RM " . number_format($remaining, 2) . " short of Tier 1. Only {$daysLeft} day(s) left until cutoff (22nd)!";
                $data['remainingAmount'] = $remaining;
                $data['daysLeft'] = $daysLeft;
                $data['nextTierIncentive'] = $tier1TotalIncentive;
            } else {
                $data['showBanner'] = true;
                $remaining = $tier1['threshold'] - $contributionAmt;
                if ($contributionAmt >= ($tier1['threshold'] - 5000) && ($contributionAmt >= $tier1['threshold'] - 1)) {
                    $data['tier'] = 1;
                    $data['message'] = "You are nearly RM " . number_format($remaining, 2) . " short to receive Tier 1 incentive (RM{$tier1TotalIncentive}). Keep it up!";
                    $data['incentiveAmount'] = $tier1TotalIncentive;
                } else {
                    $data['tier'] = 1;
                    $data['message'] = "You are RM " . number_format($remaining, 2) . " short to receive Tier 1 incentive (RM{$tier1TotalIncentive}).";
                    $data['incentiveAmount'] = $tier1TotalIncentive;
                    $data['nextTierIncentive'] = $tier1TotalIncentive;
                }
            }
        }

        return $data;
    }

    public function actionPendingAction1() {
        $totalPendingApprovalQuotaion = $this->getTotalPendingQuotationApproval();

        return $this->renderAjax('pending_action_1', [
                    'totalPendingApprovalQuotaion' => $totalPendingApprovalQuotaion,
        ]);
    }

    public function actionPendingAction2() {
        $totalProjectOverdue = $this->getTotalProjectOverdue();
        $totalProjectNeardue = $this->getTotalProjectNeardue();

        return $this->renderAjax('pending_action_2', [
                    'totalProjectOverdue' => $totalProjectOverdue,
                    'totalProjectNeardue' => $totalProjectNeardue,
        ]);
    }

    public function actionPendingAction3() {
        $totalTaskOverdueWorker = $this->getTotalTaskOverdueWorker();
        $totalTaskNeardueWorker = $this->getTotalTaskNeardueWorker();

        $totalTaskOverdueFabSuper = $this->getTotalTaskOverdueFabSuper();
        $totalTaskNeardueFabSuper = $this->getTotalTaskNeardueFabSuper();
        $totalTaskOverdueElecSuper = $this->getTotalTaskOverdueElecSuper();
        $totalTaskNeardueElecSuper = $this->getTotalTaskNeardueElecSuper();

        return $this->renderAjax('pending_action_3', [
                    'totalTaskOverdueWorker' => $totalTaskOverdueWorker,
                    'totalTaskNeardueWorker' => $totalTaskNeardueWorker,
                    'totalTaskOverdueFabSuper' => $totalTaskOverdueFabSuper,
                    'totalTaskNeardueFabSuper' => $totalTaskNeardueFabSuper,
                    'totalTaskOverdueElecSuper' => $totalTaskOverdueElecSuper,
                    'totalTaskNeardueElecSuper' => $totalTaskNeardueElecSuper,
        ]);
    }

    public function actionPendingAction4() {
        $totalPendingPettyCashReqPersonal = $this->getTotalPendingPettyCash('pendingRequestPersonal');
        $totalPendingPettyCashReqFinance = $this->getTotalPendingPettyCash('pendingRequestFinance');
        $totalPendingReplenishmentFinance = $this->getTotalPendingPettyCash('pendingReplenishmentFinance');
        $totalPendingReplenishmentDirector = $this->getTotalPendingPettyCash('pendingReplenishmentDirector');

        return $this->renderAjax('pending_action_4', [
                    'totalPendingPettyCashReqPersonal' => $totalPendingPettyCashReqPersonal,
                    'totalPendingPettyCashReqFinance' => $totalPendingPettyCashReqFinance,
                    'totalPendingReplenishmentFinance' => $totalPendingReplenishmentFinance,
                    'totalPendingReplenishmentDirector' => $totalPendingReplenishmentDirector,
        ]);
    }

    public function actionReportSection1() {
        $request = Yii::$app->request;
        $model = new ReportingModel();

        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');

        if ($model->dateFrom === '' || $model->dateTo === '') {
            $dateTo = date('Y-m-23');
            $dateFrom = date('Y') . '-' . date('m') . '-22';
            $model->dateTo = $dateTo;
            $model->dateFrom = $dateFrom;
            $model->is_internalProject = '';
        }

        if ($model->dateFrom && $model->dateTo) {
            $project_coordinator = \common\models\User::findOne(Yii::$app->user->id);

            $countQuotationIndividual = $model->getQuotationDoneByProjectCoordinator(
                    $model->dateFrom,
                    $model->dateTo,
                    $project_coordinator->id
            );

            $countQuotationAllProjectCoordinator = $model->getQuotationDoneAllProjectCoordinator(
                    $model->dateFrom,
                    $model->dateTo
            );

            $totalQuotationOthers = $countQuotationAllProjectCoordinator - $countQuotationIndividual;
            $qDoneData = $model->preparePieChartData(
                    'By ' . $project_coordinator->fullname,
                    'By Other Project Coordinators',
                    $countQuotationIndividual,
                    $totalQuotationOthers,
                    '#633EBB',
                    '#BE61CA'
            );

            $totalQuotationIndividualConfirmed = $model->getQuotationHitsByProjectCoordinator(
                    $model->dateFrom,
                    $model->dateTo,
                    $project_coordinator->id
            );
            $totalQuotationIndividualPending = $countQuotationIndividual - $totalQuotationIndividualConfirmed;
            $qHitsData = $model->preparePieChartData(
                    'Confirmed',
                    'Pending',
                    $totalQuotationIndividualConfirmed,
                    $totalQuotationIndividualPending,
                    '#007CC3',
                    '#FFAB05'
            );

            $dataCompletion = $model->getTasksCompletionByProjectCoordinator(
                    $model->dateFrom,
                    $model->dateTo,
                    $project_coordinator->id
            );
            $tasksIncomplete = $dataCompletion['rowCount'] > 0 ? 100 - $dataCompletion['combinedAverage'] : 0;
            $tasksCompletionData = $model->preparePieChartData(
                    'Complete',
                    'Incomplete',
                    $dataCompletion['combinedAverage'],
                    $tasksIncomplete,
                    '#52D726',
                    '#F13C59'
            );

            return $this->renderAjax('report_section_1', [
                        'model' => $model,
                        'dateFrom' => $model->dateFrom,
                        'dateTo' => $model->dateTo,
                        'qDoneData' => json_encode($qDoneData),
                        'qHitsData' => json_encode($qHitsData),
                        'tasksCompletionData' => json_encode($tasksCompletionData),
                        'totalQuotationIndividual' => $countQuotationIndividual ?? null,
                        'totalQuotationAllProjectCoordinator' => $countQuotationAllProjectCoordinator ?? null,
                        'project_coordinator' => $project_coordinator ?? null,
            ]);
        }
    }

    public function actionReportSection2() {
        $request = Yii::$app->request;
        $model = new ReportingModel();

        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');
        $worker = \common\models\User::findOne(Yii::$app->user->id);
        $model->userId = $worker->id;
        if ($model->dateFrom === '' || $model->dateTo === '') {
            $dateTo = date('Y-m-23');
            $dateFrom = date('Y') . '-' . date('m') . '-22';
            $model->dateTo = $dateTo;
            $model->dateFrom = $dateFrom;
            $model->is_internalProject = '';
        }

        if ($model->dateFrom && $model->dateTo) {
            $taskCompletionFabData = $model->initializeChartData();
            $taskCompletionElecData = $model->initializeChartData();
            $worker = \common\models\User::findOne(Yii::$app->user->id);
            $model->userId = $worker->id;
            $totalAmountTaskFab = 0;
            $totalAmountTaskElec = 0;
            $fabPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
            $fabPerformanceList_dept = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, TaskAssignment::taskTypeFabrication);
            $elecPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);
            $elecPerformanceList_dept = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, TaskAssignment::taskTypeElectrical);

            if (!empty($fabPerformanceList_individual)) {
                $individualAmt = round(array_sum(array_map('floatval', array_column($fabPerformanceList_individual, 'performanceAmount'))), 2);
                $totalAmountTaskFab = round(array_sum(array_map('floatval', array_column($fabPerformanceList_dept, 'totalPerformance'))), 2);
                $byOthersFab = $totalAmountTaskFab - $individualAmt;
                $taskCompletionFabData = $model->preparePieChartData('By ' . $worker->fullname, 'By Other Staffs', $individualAmt, $byOthersFab, '#4B0082', '#87CEEB');
            }

            if (!empty($elecPerformanceList_individual)) {
                $individualAmt = round(array_sum(array_map('floatval', array_column($elecPerformanceList_individual, 'performanceAmount'))), 2);
                $totalAmountTaskElec = round(array_sum(array_map('floatval', array_column($elecPerformanceList_dept, 'totalPerformance'))), 2);
                $byOthersElec = $totalAmountTaskElec - $individualAmt;
                $taskCompletionElecData = $model->preparePieChartData('By ' . $worker->fullname, 'By Other Staffs', $individualAmt, $byOthersElec, '#4B0082', '#87CEEB');
            }
        }
        return $this->renderAjax('report_section_2', [
                    'model' => $model,
                    'dateFrom' => $model->dateFrom,
                    'dateTo' => $model->dateTo,
                    'tasksCompletionFabData' => json_encode($taskCompletionFabData),
                    'tasksCompletionElecData' => json_encode($taskCompletionElecData),
                    'totalAmountTaskFab' => $totalAmountTaskFab ?? null,
                    'totalAmountTaskElec' => $totalAmountTaskElec ?? null,
                    'worker' => $worker
        ]);
    }

    public function actionReportSection3() {
        $request = Yii::$app->request;
        $model = new ReportingModel();

        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');

        if ($model->dateFrom === '' || $model->dateTo === '') {
            $dateTo = date('Y-m-23');
            $dateFrom = date('Y') . '-' . date('m') . '-22';
            $model->dateTo = $dateTo;
            $model->dateFrom = $dateFrom;
            $model->is_internalProject = '';
        }

        if ($model->dateFrom && $model->dateTo) {
            $cachedData = $this->getFactoryStaffPerformanceReportData($model);

            return $this->renderAjax('topPerformanceFabElecStaff', [
                        'model' => $model,
                        'topFabricationStaffByTask' => $cachedData['topFabricationStaffByTask'] ?? null,
                        'topElectricalStaffByTask' => $cachedData['topElectricalStaffByTask'] ?? null,
                        'topFabStaffOverall' => $cachedData['topFabStaffOverall'] ?? null,
                        'topElecStaffOverall' => $cachedData['topElecStaffOverall'] ?? null,
                        'cached' => true,
                        'generated_at' => $cachedData['generated_at'] ?? null
            ]);
        }
    }

    public function actionReportSection4() {
        $request = Yii::$app->request;
        $model = new ReportingModel();

        // Set initial dates
        $model->dateFrom = $request->get('dateFrom') ?: date('Y-m') . '-22';
        $model->dateTo = $request->get('dateTo') ?: date('Y-m-23');
        $model->is_internalProject = $request->get('is_internalProject', '');

        $dateFrom = $model->dateFrom . " 00:00:00";
        $dateTo = $model->dateTo . " 23:59:59";

        // Reusable logic
        $chartData = $model->getDepartmentTaskCompletion($model, $dateFrom, $dateTo);
        return $this->renderAjax('report_section_4', [
                    'model' => $model,
                    'electricalData' => $chartData['electricalData'],
                    'fabricationData' => $chartData['fabricationData'],
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
        ]);
    }

    private function getTotalPendingPettyCash($type) {
        switch ($type) {
            case "pendingRequestPersonal":
                $query = PettyCashRequestMaster::find()
                        ->where(['IN', 'status', \frontend\models\RefGeneralStatus::STATUS_Pending])
                        ->andWhere(['created_by' => \Yii::$app->user->identity->id])
                        ->andWhere(['deleted_by' => null]);
                break;

            case "pendingRequestFinance":
                $query = PettyCashRequestMaster::find()
                        ->where(['IN', 'status', \frontend\models\RefGeneralStatus::STATUS_Pending_Finance])
                        ->andWhere([
                            'or',
                            ['finance_id' => \Yii::$app->user->identity->id],
                            ['finance_id' => null]
                        ])
                        ->andWhere(['deleted_by' => null]);
                break;

            case "pendingReplenishmentFinance":
                $query = PettyCashReplenishment::find()
                        ->where(['IN', 'status', \frontend\models\RefGeneralStatus::STATUS_Pending_Finance])
                        ->andWhere(['created_by' => \Yii::$app->user->identity->id])
                        ->andWhere(['deleted_by' => null]);
                break;

            case "pendingReplenishmentDirector":
                $query = PettyCashReplenishment::find()
                        ->where(['IN', 'status', \frontend\models\RefGeneralStatus::STATUS_GetDirectorApproval])
                        ->andWhere(['deleted_by' => null]);
                break;
        }

        return $query->count();
    }

    private function getFactoryStaffPerformanceReportData($model) {
        // Try to find if user's date range falls within any cached period
        $cachedReport = \common\models\FactoryStaffPerformanceReports::find()->where(['<=', 'date_from', $model->dateFrom])->andWhere(['>=', 'date_to', $model->dateTo])->andWhere(['is_internal_project' => $model->is_internalProject])->orderBy(['updated_at' => SORT_DESC])->one();

        if ($cachedReport) {
            return $cachedReport->getDecodedReportData();
        }

        // Fallback to 'all' projects if specific project type not found
        if ($model->is_internalProject !== '') {
            $cachedReport = \common\models\FactoryStaffPerformanceReports::find()->where(['<=', 'date_from', $model->dateFrom])->andWhere(['>=', 'date_to', $model->dateTo])->andWhere(['is_internal_project' => ''])->orderBy(['updated_at' => SORT_DESC])->one();

            if ($cachedReport) {
                return $cachedReport->getDecodedReportData();
            }
        }

        return null;
    }

    private function getTotalNewPublicDoc() {
        $total = \frontend\models\working\hrdoc\HrPublicDocumentsRead::find()
                ->alias('r')
                ->innerJoin('hr_public_documents d', 'd.id = r.hr_public_doc_id')
                ->where([
                    'r.employee_id' => \Yii::$app->user->identity->id,
                    'r.is_read' => 0
                ])
                ->count();

//        $total = \frontend\models\working\hrdoc\HrPublicDocumentsRead::find()
//                ->where(['employee_id' => \Yii::$app->user->identity->id, 'is_read' => 0])
//                ->count();

        return $total;
    }

    private function getTotalPendingQuotationApproval() {
        $totalPending = \frontend\models\projectquotation\QuotationPdfMasters::find()
                ->where(['md_approval_status' => \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL])
                ->count();

        return $totalPending;
    }

    private function getTotalTaskOverdueWorker() {
        $today = new \DateTime();
        $totalPendingFab = $this->getTotalTaskOverdueWorkerFab($today->format('Y-m-d'));
        $totalPendingElec = $this->getTotalTaskOverdueWorkerElec($today->format('Y-m-d'));

        $totalPending = ($totalPendingFab + $totalPendingElec);
        return $totalPending;
    }

    private function getTotalTaskOverdueWorkerFab($date = null) {
        $totalPendingFab = \frontend\models\projectproduction\fabrication\VFabStaffProduction::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(assigned_current_target_date, :today) < 0"
                ))->addParams([':today' => $date])
                ->andWhere(['assigned_complete_date' => null])
                ->andWhere(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['assigned_active_status' => 1])
                ->count();
        return $totalPendingFab;
    }

    private function getTotalTaskOverdueWorkerElec($date = null) {
        $totalPendingElec = \frontend\models\projectproduction\electrical\VElecStaffProduction::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(assigned_current_target_date, :today) < 0"
                ))->addParams([':today' => $date])
                ->andWhere(['assigned_complete_date' => null])
                ->andWhere(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['assigned_active_status' => 1])
                ->count();
        return $totalPendingElec;
    }

    private function getTotalTaskNeardueWorker() {
        $today = new \DateTime();
        $totalPendingFab = $this->getTotalTaskNeardueWorkerFab($today->format('Y-m-d'));
        $totalPendingElec = $this->getTotalTaskNeardueWorkerElec($today->format('Y-m-d'));

        $totalPending = ($totalPendingFab + $totalPendingElec);
        return $totalPending;
    }

    private function getTotalTaskNeardueWorkerFab($date = null) {
        $totalPendingFab = \frontend\models\projectproduction\fabrication\VFabStaffProduction::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(assigned_current_target_date, :today) BETWEEN 0 AND 5"
                ))->addParams([':today' => $date])
                ->andWhere(['assigned_complete_date' => null])
                ->andWhere(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['assigned_active_status' => 1])
                ->count();

        return $totalPendingFab;
    }

    private function getTotalTaskNeardueWorkerElec($date = null) {
        $totalPendingElec = \frontend\models\projectproduction\electrical\VElecStaffProduction::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(assigned_current_target_date, :today) BETWEEN 0 AND 5"
                ))->addParams([':today' => $date])
                ->andWhere(['assigned_complete_date' => null])
                ->andWhere(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['assigned_active_status' => 1])
                ->count();

        return $totalPendingElec;
    }

    private function getTotalTaskOverdueFabSuper() {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');
        $totalPending = \frontend\models\projectproduction\fabrication\TaskAssignFab::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(task_assign_fab.current_target_date, :today) < 0"
                ))->addParams([':today' => $todayStr])
                ->andWhere(['task_assign_fab.complete_date' => null])
                ->andWhere(['task_assign_fab.active_sts' => 1])
                ->count();

        return $totalPending;
    }

    private function getTotalTaskOverdueElecSuper() {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');
        $totalPending = \frontend\models\projectproduction\electrical\TaskAssignElec::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(task_assign_elec.current_target_date, :today) < 0"
                ))->addParams([':today' => $todayStr])
                ->andWhere(['task_assign_elec.complete_date' => null])
                ->andWhere(['task_assign_elec.active_sts' => 1])
                ->count();

        return $totalPending;
    }

    private function getTotalTaskNeardueFabSuper() {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');
        $totalPending = \frontend\models\projectproduction\fabrication\TaskAssignFab::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(task_assign_fab.current_target_date, :today) BETWEEN 0 AND 5"
                ))->addParams([':today' => $todayStr])
                ->andWhere(['task_assign_fab.complete_date' => null])
                ->andWhere(['task_assign_fab.active_sts' => 1])
                ->count();

        return $totalPending;
    }

    private function getTotalTaskNeardueElecSuper() {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');
        $totalPending = \frontend\models\projectproduction\electrical\TaskAssignElec::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(task_assign_elec.current_target_date, :today) BETWEEN 0 AND 5"
                ))->addParams([':today' => $todayStr])
                ->andWhere(['task_assign_elec.complete_date' => null])
                ->andWhere(['task_assign_elec.active_sts' => 1])
                ->count();

        return $totalPending;
    }

    private function getTotalProjectOverdue() {
        $today = new \DateTime();
        $totalPending = \frontend\models\ProjectProduction\ProjectProductionMaster::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(project_production_master.current_target_date, :today) < 0"
                ))
                ->addParams([':today' => $today->format('Y-m-d')])
                ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
                ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
                ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id])
                ->count();

        return $totalPending;
    }

    private function getTotalProjectNeardue() {
        $today = new \DateTime();
        $totalPending = \frontend\models\ProjectProduction\ProjectProductionMaster::find()
                ->where(new \yii\db\Expression(
                                "DATEDIFF(project_production_master.current_target_date, :today) BETWEEN 0 AND 5"
                ))
                ->addParams([':today' => $today->format('Y-m-d')])
                ->andWhere(['!=', 'project_production_master.fab_complete_percent', 100])
                ->andWhere(['!=', 'project_production_master.elec_complete_percent', 100])
                ->andWhere(['project_production_master.created_by' => \Yii::$app->user->identity->id])
                ->count();

        return $totalPending;
    }

    private function getTotalPendingDirectorApprovalLeave() {
        $totalPending = \frontend\models\office\leave\LeaveCompulsoryMaster::find()->where(['status' => LeaveMaster::STATUS_GetDirectorApproval])->count();
        return $totalPending;
    }

    private function getTotalPendingReliefApprovalLeave() {
        $totalPending = LeaveMaster::find()->where(['leave_status' => 1, 'relief_user_id' => Yii::$app->user->identity->id])->count();
        return $totalPending;
    }

    private function getTotalPendingSuperiorApprovalLeave() {
        $totalPending = LeaveMaster::find()->where(['leave_status' => 2, 'superior_id' => Yii::$app->user->identity->id])->count();
        return $totalPending;
    }

    private function getTotalPendingHrApprovalLeave() {
        $totalPending = LeaveMaster::find()->where(['leave_status' => 3])->count();
        return $totalPending;
    }

    private function getTotalPendingSuperiorApprovalClaim() {
        $totalPending = \frontend\models\office\claim\ClaimMaster::find()->where(['claim_status' => \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval])
                ->andWhere(['superior_id' => \Yii::$app->user->identity->id])
                ->andWhere(['is_deleted' => 0])
                ->count();

        return $totalPending;
    }

    private function getTotalPendingFinanceApprovalClaim() {
        $totalPending = \frontend\models\office\claim\ClaimMaster::find()
                        ->where(['IN', 'claim_status', \frontend\models\RefGeneralStatus::STATUS_Pending_Finance])
                        ->andWhere(['is_deleted' => 0])->count();

        return $totalPending;
    }

    private function getTotalPendingSuperiorClaimEntitlement() {
        $totalPending = \frontend\models\office\claim\ClaimEntitlement::find()
                ->where(['status' => \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval])
                ->andWhere(['superior_id' => \Yii::$app->user->identity->id])
                ->count();

        return $totalPending;
    }

    private function getTotalPendingSuperiorPrf() {
        $totalPending = \frontend\models\office\preReqForm\PrereqFormMaster::find()
                ->where(['status' => \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval])
                ->andWhere(['superior_id' => \Yii::$app->user->identity->id])
                ->andWhere(['is_deleted' => 0])
                ->count();

        return $totalPending;
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        Yii::$app->session->destroy();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout() {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
//    public function actionSignup() {
//        $model = new SignupForm();
//
//        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
//            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
//            return $this->goHome();
//        }
//
//        return $this->render('signup', [
//                    'model' => $model,
//        ]);
//    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token) {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                Yii::$app->session->setFlash('warning', 'PLEASE RESET YOUR PASSWORD FOR THE FIRST TIME LOGIN!');
//                return $this->goHome();
                return $this->redirect('/profile/reset-password');
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail() {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
                    'model' => $model
        ]);
    }

    // ********************** FOR DEVELOPMENT PURPOSES
    public function actionSw() {

        $id = Yii::$app->request->post("id");
        if ($id) {
            $model = new LoginForm();
            $user = \common\models\User::findOne($id);

            $model->username = $user->username;
            $model->password = "password";
            $model->rememberMe = true;
            $model->login();
        }
        $userList = new \common\models\User();
        return $this->render('sw', [
                    'userList' => $userList->getAllDropDownList()
        ]);
    }
}
