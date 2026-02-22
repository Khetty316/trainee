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

    public function actionDashboard() {
        $model = new ReportingModel();
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y') . '-' . date('m') . '-01';
        $model->dateTo = $dateTo;
        $model->dateFrom = $dateFrom;
        $model->is_internalProject = '';
        $project_coordinator = \common\models\User::findOne(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post())) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
        }

        $countQuotationIndividual = $model->getQuotationDoneByProjectCoordinator($model->dateFrom, $model->dateTo, $project_coordinator->id);
        $countQuotationAllProjectCoordinator = $model->getQuotationDoneAllProjectCoordinator($model->dateFrom, $model->dateTo);

        //Pending Action
        $totalPendingReliefLeave = $this->getTotalPendingReliefApprovalLeave();
        $totalPendingSuperiorLeave = $this->getTotalPendingSuperiorApprovalLeave();
        $totalPendingHrLeave = $this->getTotalPendingHrApprovalLeave();

        $totalPendingSuperiorClaim = $this->getTotalPendingSuperiorApprovalClaim();
        $totalPendingFinanceClaim = $this->getTotalPendingFinanceApprovalClaim();

        $totalPendingSuperiorClaimEntitlement = $this->getTotalPendingSuperiorClaimEntitlement();

        $totalPendingSuperiorPrf = $this->getTotalPendingSuperiorPrf();

        //new public document
        $totalNewPublicDoc = $this->getTotalNewPublicDoc();

        return $this->render('dashboard', [
                    'model' => $model,
                    'dateFrom' => $model->dateFrom,
                    'dateTo' => $model->dateTo,
                    'totalQuotationIndividual' => $countQuotationIndividual ?? null,
                    'totalQuotationAllProjectCoordinator' => $countQuotationAllProjectCoordinator ?? null,
                    'project_coordinator' => $project_coordinator ?? null,
                    'totalPendingReliefLeave' => $totalPendingReliefLeave,
                    'totalPendingSuperiorLeave' => $totalPendingSuperiorLeave,
                    'totalPendingHrLeave' => $totalPendingHrLeave,
                    'totalPendingSuperiorClaim' => $totalPendingSuperiorClaim,
                    'totalPendingFinanceClaim' => $totalPendingFinanceClaim,
                    'totalPendingSuperiorClaimEntitlement' => $totalPendingSuperiorClaimEntitlement,
                    'totalPendingSuperiorPrf' => $totalPendingSuperiorPrf,
                    'totalNewPublicDoc' => $totalNewPublicDoc,
        ]);
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

    public function actionReportSection1() {
        $request = Yii::$app->request;
        $model = new ReportingModel();

        // Manually assign attributes from GET
        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');

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

        // Manually assign attributes from GET
        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');
        $worker = \common\models\User::findOne(Yii::$app->user->id);
        $model->userId = $worker->id;
        if ($model->dateFrom && $model->dateTo) {
            $taskCompletionFabData = $model->initializeChartData();
            $taskCompletionElecData = $model->initializeChartData();
            $worker = \common\models\User::findOne(Yii::$app->user->id);
            $model->userId = $worker->id;
            $totalAmountTaskFab = 0;
            $totalAmountTaskElec = 0;
            $fabPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
            $fabPerformanceList_dept = $this->calculateDepartmentCompleteRecord($model, TaskAssignment::taskTypeFabrication);
            $elecPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);
            $elecPerformanceList_dept = $this->calculateDepartmentCompleteRecord($model, TaskAssignment::taskTypeElectrical);

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

        // Manually assign attributes from GET
        $model->dateFrom = $request->get('dateFrom');
        $model->dateTo = $request->get('dateTo');
        $model->is_internalProject = $request->get('is_internalProject');

        if ($model->dateFrom && $model->dateTo) {
            $topFabricationStaffByTask = $this->getTopPerformanceFactoryStaffByTaskCode($model, TaskAssignment::taskTypeFabrication);
            $topElectricalStaffByTask = $this->getTopPerformanceFactoryStaffByTaskCode($model, TaskAssignment::taskTypeElectrical);
            $topFabStaffOverall = $this->getTopPerformanceFactoryStaffOverall($model, TaskAssignment::taskTypeFabrication);
            $topElecStaffOverall = $this->getTopPerformanceFactoryStaffOverall($model, TaskAssignment::taskTypeElectrical);

            return $this->renderAjax('topPerformanceFabElecStaff', [
                        'model' => $model,
                        'topFabricationStaffByTask' => $topFabricationStaffByTask,
                        'topElectricalStaffByTask' => $topElectricalStaffByTask,
                        'topFabStaffOverall' => $topFabStaffOverall,
                        'topElecStaffOverall' => $topElecStaffOverall
            ]);
        }
    }

    private function getTopPerformanceFactoryStaffOverall($model, $department) {
        $reportDetail = $this->calculateDepartmentCompleteRecord($model, $department);

        $totalSum = array_sum(array_column($reportDetail, 'totalPerformance'));

        usort($reportDetail, function ($a, $b) {
            return floatval($b['totalPerformance']) <=> floatval($a['totalPerformance']);
        });

        $result = array_slice($reportDetail, 0, 3);

        foreach ($result as $index => &$staff) {
            $staff['rank'] = $index + 1;
            $staff['percentage'] = ($totalSum > 0) ? round((floatval($staff['totalPerformance']) / $totalSum) * 100, 2) : 0;
            $staff['formattedValue'] = number_format(floatval($staff['totalPerformance']), 2);
        }

        return $result;
    }

    private function calculateDepartmentCompleteRecord($model, $department) {
        if ($department == TaskAssignment::taskTypeFabrication) {
            if ($model->is_internalProject === "") {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_fabrication.user_id as id',
                                    'v_staff_competed_panel_record_fabrication.staff_id as staffId',
                                    'v_staff_competed_panel_record_fabrication.fullname',
                                    'fab_task_code',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_fabrication')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_fabrication.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['user_id', 'fab_task_code'])
                                ->orderBy(['fab_task_code' => SORT_ASC, 'totalPerformance' => SORT_DESC])
                                ->all();
            } else {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_fabrication.user_id as id',
                                    'v_staff_competed_panel_record_fabrication.staff_id as staffId',
                                    'v_staff_competed_panel_record_fabrication.fullname',
                                    'fab_task_code',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_fabrication')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_fabrication.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['user_id', 'fab_task_code'])
                                ->orderBy(['fab_task_code' => SORT_ASC, 'totalPerformance' => SORT_DESC])
                                ->all();
            }
        }
        if ($department == TaskAssignment::taskTypeElectrical) {
            if ($model->is_internalProject === "") {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_electrical.user_id as id',
                                    'v_staff_competed_panel_record_electrical.staff_id as staffId',
                                    'v_staff_competed_panel_record_electrical.fullname',
                                    'elec_task_code',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_electrical')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_electrical.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['user_id', 'elec_task_code'])
                                ->orderBy(['elec_task_code' => SORT_ASC, 'totalPerformance' => SORT_DESC])
                                ->all();
            } else {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_electrical.user_id as id',
                                    'v_staff_competed_panel_record_electrical.staff_id as staffId',
                                    'v_staff_competed_panel_record_electrical.fullname',
                                    'elec_task_code',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_electrical')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_electrical.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['user_id', 'elec_task_code'])
                                ->orderBy(['elec_task_code' => SORT_ASC, 'totalPerformance' => SORT_DESC])
                                ->all();
            }
        }
    }

    private function getTopPerformanceFactoryStaffByTaskCode($model, $department) {
        $reportDetail = $this->calculateDepartmentCompleteRecord($model, $department);

        if (empty($reportDetail)) {
            return [];
        }

        $taskCodeColumn = $department == TaskAssignment::taskTypeFabrication ? 'fab_task_code' : 'elec_task_code';
        $groupedByTaskCode = [];

        foreach ($reportDetail as $staff) {
            $taskCode = $staff[$taskCodeColumn];
            if (!isset($groupedByTaskCode[$taskCode])) {
                $groupedByTaskCode[$taskCode] = [];
            }
            $groupedByTaskCode[$taskCode][] = $staff;
        }

        $result = [];
        foreach ($groupedByTaskCode as $taskCode => $staffList) {
            $totalPerformanceSum = array_sum(array_column($staffList, 'totalPerformance'));

            usort($staffList, function ($a, $b) {
                return $b['totalPerformance'] <=> $a['totalPerformance'];
            });

            $top3 = array_slice($staffList, 0, 3);

            foreach ($top3 as &$staff) {
                $staff['percentage'] = $totalPerformanceSum > 0 ? round(($staff['totalPerformance'] / $totalPerformanceSum) * 100, 4) : 0;
                $staff['amount'] = $staff['totalPerformance'];
            }

            $result[$taskCode] = $top3;
        }

        return $result;
    }

    private function getTotalNewPublicDoc() {
        $total = \frontend\models\working\hrdoc\HrPublicDocuments::find()
                ->where(['show_alert' => 1])
                ->count();

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
