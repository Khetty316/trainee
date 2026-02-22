<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\report\ReportingModel;
use common\modules\auth\models\AuthItem;
use frontend\models\report\VQuotationMastersView;
use common\models\myTools\MyFormatter;
use frontend\models\report\VWorkerPerformanceFab;
use frontend\models\report\VWorkerPerformanceElec;
use common\models\User;
use frontend\models\projectproduction\task\TaskAssignment;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\projectproduction\electrical\RefProjProdTaskElec;
use frontend\models\common\RefProjectQTypes;
use common\models\myTools\FlashHandler;
use frontend\models\projectproduction\fabrication\RefTaskWeightFab;
use frontend\models\projectproduction\electrical\RefTaskWeightElec;
use frontend\models\projectquotation\VProjectQuotationMaster;
use frontend\models\projectproduction\electrical\VElecTasksMasterlist;
use frontend\models\projectproduction\fabrication\VFabTasksMasterlist;
use frontend\models\projectproduction\fabrication\ProdFabTaskWeight;
use frontend\models\projectproduction\VStaffCompetedPanelRecordElectrical;
use frontend\models\projectproduction\VStaffCompetedPanelRecordFabrication;

/**
 * AssetController implements the CRUD actions for AssetMaster model.
 */
class ReportingController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ["@"]
                    ],
//                    [
//                        'actions' => ['index-elec-project-list', 'index-elec-in-progress', 'index-elec-all', 'index-elec-project-panels',
//                            'view-assigned-task', 'ajax-action-set-complete'],
//                        'allow' => true,
//                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin]
//                    ],
//                    [
//                        'actions' => [
//                            'ajax-action-set-complete',
//                            'assign-task',
//                            'update-assign-task',
//                            'deactivate-assign-task',
//                            'assign-task-multiple',
//                            'assign-task-multiple-panels',
//                            'confirm-task-assignment',
//                            'checking-tasks-assignment'
//                        ],
//                        'allow' => true,
//                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin]
//                    ],
                ],
            ],
        ];
    }

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/report/');
    }

    public function actionGetQuotationPercentage() {
        $model = new ReportingModel();
        $VQMasterHasRevisions = [];
        $summary = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
            $VQMasterHasRevisions = VQuotationMastersView::find()
                    ->where(['between', 'v_quotation_masters_view.created_at', $model->dateFrom . "  00:00:00", $model->dateTo . "  23:59:59"])
                    ->join('INNER JOIN', 'project_q_revisions', 'project_q_revisions.project_q_type_id = v_quotation_masters_view.type_id')
                    ->distinct()//->asArray()
                    ->all();

            foreach ($VQMasterHasRevisions as $projQType) {
                $projQType->totalPanels = \frontend\models\projectquotation\ProjectQPanels::find()
                        ->where(['revision_id' => $projQType->active_revision_id])
                        ->sum("quantity");

                $type = $projQType->q_type_name;

                if (!isset($summary[$type])) {
                    $summary[$type] = ['totalPanelQty' => 0, 'totalAmount' => 0.0, 'totalConfirmed' => 0, 'totalQuotation' => 0];
                }

                $summary[$type]['totalPanelQty'] += $projQType->totalPanels;
                $summary[$type]['totalAmount'] += ($projQType->is_finalized ? $projQType->active_revision_amount : 0);
                $summary[$type]['totalConfirmed'] += ($projQType->is_finalized ? 1 : 0);
                $summary[$type]['totalQuotation'] += 1;
            }
        }

        return $this->render('getQuotationPercentage', [
                    'model' => $model,
                    'VQMasterHasRevisions' => $VQMasterHasRevisions,
                    'summary' => $summary
        ]);
    }

    public function actionGetIndividualPerformance() {
        $model = new ReportingModel();
        $VQMasterHasRevisions = [];
        $reportDetail = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);

            $worker = \common\models\User::findOne($model->userId);
//            $fabPerformanceList = $this->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
//            $elecPerformanceList = $this->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);
            $fabPerformanceList = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
            $elecPerformanceList = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);

            if (!empty($fabPerformanceList)) {
//                $reportDetail["Fabrication"] = $fabPerformanceList;
//                $details = $model->getFabPerformanceAmount($fabPerformanceList);
                $reportDetail["Fabrication"] = $fabPerformanceList;
            }
            if (!empty($elecPerformanceList)) {
//                $reportDetail["Electrical"] = $elecPerformanceList;
//                $details = $model->getElecPerformanceAmount($elecPerformanceList);
                $reportDetail["Electrical"] = $elecPerformanceList;
            }
        }
        return $this->render('getIndividualPerformance', [
                    'model' => $model,
                    'reportDetail' => $reportDetail,
                    'worker' => $worker ?? null
        ]);
    }

    /**
     * To Be Edited
     * @return type
     */
    public function actionGetDepartmentPerformanceDetail() {
        $model = new ReportingModel();
        $reportDetail = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);

            $reportDetail = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, $model->department);
        }
        return $this->render('getDepartmentPerformanceDetail', [
                    'model' => $model,
                    'reportDetail' => json_encode($reportDetail)
        ]);
    }

    /**
     * by Khetty, 15/1/2024
     */
    public function actionReconfigureTaskWeight() {
        $refProjectTypes = RefProjectQTypes::find()->all();
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $refTaskWeightFab = RefTaskWeightFab::find()->all();
        $refTaskWeightElec = RefTaskWeightElec::find()->all();

        return $this->render('reconfigureTaskWeight', [
                    'refProjectTypes' => $refProjectTypes,
                    'refFabTask' => $refFabTask,
                    'refElecTask' => $refElecTask,
                    'refTaskWeightFab' => $refTaskWeightFab,
                    'refTaskWeightElec' => $refTaskWeightElec
        ]);
    }

    /**
     * by Khetty, 15/1/2024
     * **** Update the percentage for both Fabrication and Electrical department ****
     */
    public function actionUpdateProjectQDetail() {
        $refProjectQTypes = RefProjectQTypes::find()->all();

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            $transSuccess = true;

            foreach ($postData['RefProjectQTypes'] as $key => $projectCode) {
                foreach ($projectCode['code'] as $code => $data) {
                    $projectQ = RefProjectQTypes::findOne($code);

                    if (!$projectQ) {
                        continue;
                    }

                    $projectQ->fab_dept_percentage = $data['fab_dept_percentage'];
                    $projectQ->elec_dept_percentage = $data['elec_dept_percentage'];

                    if (!$projectQ->calculateTotalPercentage($data) || !$projectQ->save()) {
                        $transSuccess = false;
                        break 2; // break both inner and outer loops
                    }
                }
            }

            if ($transSuccess) {
                $transaction->commit();
                FlashHandler::success("Updated!");
            } else {
                $transaction->rollBack();
                FlashHandler::err("Update Failed!");
            }
            return $this->redirect(['reconfigure-task-weight']);
        }

        return $this->render('updateProjectQDetail', [
                    'refProjectQTypes' => $refProjectQTypes
        ]);
    }

    /**
     * by Khetty, 15/1/2024
     * **** Update the percentage for task weight ****
     */
    public function actionUpdateTaskWeight($paneltype) {
        $refProjectTypes = RefProjectQTypes::findOne($paneltype);
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $refTaskWeightFab = RefTaskWeightFab::find()->where(['panel_type' => $paneltype])->all();
        $refTaskWeightElec = RefTaskWeightElec::find()->where(['panel_type' => $paneltype])->all();

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            $transSuccess = true;

            if ($this->calculateTotalWeight($postData['RefProjProdTaskFab']) && $this->calculateTotalWeight($postData['RefProjProdTaskElec'])) {
                foreach ($postData['RefProjProdTaskFab'] as $key => $data) {
                    $refTaskWeightFab = new RefTaskWeightFab();
                    if (!$refTaskWeightFab->updateRefTaskWeight($data, $paneltype)) {
                        $transSuccess = false;
                    }
                }
                foreach ($postData['RefProjProdTaskElec'] as $key => $data) {
                    $refTaskWeightElec = new RefTaskWeightElec();
                    if (!$refTaskWeightElec->updateRefTaskWeight($data, $paneltype)) {
                        $transSuccess = false;
                    }
                }
            } else {
                $transSuccess = false;
                FlashHandler::err("Update Failed!");
            }
            if ($transSuccess) {
                $transaction->commit();
                FlashHandler::success("Updated!");
            } else {
                $transaction->rollBack();
                FlashHandler::err("Update Failed!");
            }
            return $this->redirect(['reconfigure-task-weight']);
        }

        return $this->renderAjax('_formTaskWeight', [
                    'refFabTask' => $refFabTask,
                    'refElecTask' => $refElecTask,
                    'refTaskWeightFab' => $refTaskWeightFab,
                    'refTaskWeightElec' => $refTaskWeightElec,
                    'refProjectTypes' => $refProjectTypes
        ]);
    }

//    public function actionUpdateTaskWeight() {
//        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
//        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
//
//        if (Yii::$app->request->post()) {
//            $postData = Yii::$app->request->post();
//            $transaction = Yii::$app->db->beginTransaction();
//            $transSuccess = true;
//
//            $fabTotalWeight = $this->calculateTotalWeight($postData['RefProjProdTaskFab']);
//            $elecTotalWeight = $this->calculateTotalWeight($postData['RefProjProdTaskElec']);
//
//            if ($fabTotalWeight !== false && $elecTotalWeight !== false) {
//                foreach ($postData['RefProjProdTaskFab'] as $key => $task) {
//                    foreach ($task['code'] as $taskCode => $data) {
//                        $fabTask = RefProjProdTaskFab::findOne($taskCode);
//                        if (!$fabTask) {
//                            continue;
//                        }
//                        $fabTask->weight = $data['weight'];
//                        if (!$fabTask->save()) {
//                            $transSuccess = false;
//                            break 2;
//                        }
//                    }
//                }
//
//                foreach ($postData['RefProjProdTaskElec'] as $key => $task) {
//                    foreach ($task['code'] as $taskCode => $data) {
//                        $elecTask = RefProjProdTaskElec::findOne($taskCode);
//                        if (!$elecTask) {
//                            continue;
//                        }
//                        $elecTask->weight = $data['weight'];
//                        if (!$elecTask->save()) {
//                            $transSuccess = false;
//                            break 2;
//                        }
//                    }
//                }
//            } else {
//                $transSuccess = false;
//                FlashHandler::err("Update Failed!");
//            }
//            if ($transSuccess) {
//                $transaction->commit();
//                FlashHandler::success("Updated!");
//            } else {
//                $transaction->rollBack();
//                FlashHandler::err("Update Failed!");
//            }
//            return $this->redirect(['reconfigure-task-weight']);
//        }
//
//        return $this->render('updateTaskWeight', [
//                    'refFabTask' => $refFabTask,
//                    'refElecTask' => $refElecTask
//        ]);
//    }

    /**
     * by Khetty, 15/1/2024
     * **** Calculate the total task weight ****
     */
    private function calculateTotalWeight($data) {
        $totalWeight = 0;
        foreach ($data as $key => $taskCode) {
            foreach ($taskCode as $value) {
                $totalWeight += (float) $value['weight'];
            }
        }

        if ($totalWeight > 100) {
            return false;
        }

        return true;
    }

    /*     * *********************************************** Charting Functions ************************************* */

    public function actionGetChartQuotationHit() {
        $model = new ReportingModel();
        $VQMasterHasRevisions = [];
        $summary = [];
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y-m-d', strtotime('-4 months'));
        $model->dateTo = $dateTo;
        $model->dateFrom = $dateFrom;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
        }

        $chartDataCoord = $this->actionGetChartQuotationHitEngineer($dateFrom, $dateTo, $model->userId);

        $chartDataType = $this->actionGetChartQuotationHitType($dateFrom, $dateTo);

        return $this->render('_chartQuotationHit', [
                    'chartDataCoord' => $chartDataCoord,
                    'chartDataType' => $chartDataType,
                    'model' => $model,
                    'VQMasterHasRevisions' => $VQMasterHasRevisions,
                    'summary' => $summary
        ]);
    }

    public function actionGetChartQuotationHitEngineer($dateFrom, $dateTo, $projectId = null) {
//        $username = $projectId ? VQuotationMastersView::findOne($projectId)->project_coordinator_fullname : null;
        $username = $projectId ? VQuotationMastersView::find()->where(['project_coordinator_fullname' => $projectId])->one() : null;
        $data = $this->getQuotationData($dateFrom, $dateTo, $username);
        $coordinators = VQuotationMastersView::getDropDownListCoordinatorPeriod($dateFrom, $dateTo);

        return [
            'coordinators' => $coordinators,
            'chartDataJson' => json_encode($data),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];

//        return $this->render('_chartQuotationHitEngineer', [
//                    'model' => new VQuotationMastersView(),
//                    'coordinators' => $coordinators,
//                    'chartDataJson' => json_encode($data),
//                    'dateFrom' => $dateFrom,
//                    'dateTo' => $dateTo
//        ]);
    }

    private function getQuotationData($dateFrom, $dateTo, $projectCoordinatorFullname = null) {
        $labels = [];
        $confirmedQuotations = [];
        $backgroundColor = [];

        $data = (new \yii\db\Query())
                ->select([
                    'project_coordinator_fullname',
                    'SUM(CASE WHEN is_finalized = 1 THEN active_revision_amount ELSE 0 END) as confirmed_quotations_amount',
                    'SUM(active_revision_amount) as quoted_quotations_amount'
                ])
                ->from('v_quotation_masters_view')
                ->where(['between', 'created_at', $dateFrom, $dateTo])
                ->groupBy('project_coordinator_fullname')
                ->all();

        if ($projectCoordinatorFullname) {
            $specificConfirmed = 0;
            $othersConfirmed = 0;

            foreach ($data as $row) {
                if ($row['project_coordinator_fullname'] === $projectCoordinatorFullname) {
                    $specificConfirmed += (int) $row['confirmed_quotations_amount'];
                } else {
                    $othersConfirmed += (int) $row['quoted_quotations_amount'];
                }
            }

            $labels = [$projectCoordinatorFullname, 'Others'];
            $confirmedQuotations = [$specificConfirmed, $othersConfirmed];
            $backgroundColor = ['#FF6384', '#36A2EB'];
        } else {
            foreach ($data as $row) {
                $labels[] = $row['project_coordinator_fullname'];
                $confirmedQuotations[] = $row['confirmed_quotations_amount'];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $confirmedQuotations,
                    'backgroundColor' => ['#e03c31', '#ff7f41', '#2dc84d', '#147bd1', '#753bbd', '#f7ea48'],
                ]
            ]
        ];
    }

    public function actionGetChartQuotationHitType($dateFrom, $dateTo) {
        $labels = [];
        $data = [];

        $summary = [];
        $VQMasterHasRevisions = VQuotationMastersView::find()
                ->where(['between', 'created_at', "$dateFrom 00:00:00", "$dateTo 23:59:59"])
                ->all();

        foreach ($VQMasterHasRevisions as $projQType) {
            $type = $projQType->q_type_name;
            if (!isset($summary[$type])) {
                $summary[$type] = ['totalConfirmed' => 0, 'totalQuotation' => 0];
            }
            $summary[$type]['totalConfirmed'] += ($projQType->is_finalized ? $projQType->active_revision_amount : 0);
            $summary[$type]['totalQuotation'] += $projQType->active_revision_amount;
        }

        foreach ($summary as $type => $stats) {
            $labels[] = $type;
            $data[] = $stats['totalConfirmed'];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#e03c31', '#ff7f41', '#2dc84d', '#147bd1', '#753bbd', '#f7ea48'],
                ]
            ]
        ];

        return [
            'chartDataJson' => json_encode($chartData),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];
    }

    public function actionGetChartDepartmentTaskCompletion() {
        $model = new ReportingModel();
        $model->dateFrom = date('Y') . '-01-01';
        $model->dateTo = date('Y-m-d');
        $model->is_internalProject = '';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
        }

        $dateFrom = $model->dateFrom . " 00:00:00";
        $dateTo = $model->dateTo . " 23:59:59";

        $chartData = $model->getDepartmentTaskCompletion($model, $dateFrom, $dateTo);

        return $this->render('_chartDepartmentTaskCompletion', [
                    'model' => $model,
                    'electricalData' => $chartData['electricalData'],
                    'fabricationData' => $chartData['fabricationData'],
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
        ]);
    }

    public function actionGetChartDepartmentTaskCompletionAmount() {
        $model = new ReportingModel();
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y') . '-01-01';
        $model->dateTo = $dateTo;
        $model->dateFrom = $dateFrom;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
        }

        $dateFrom = $dateFrom . " 00:00:00";
        $dateTo = $dateTo . " 23:59:59";
        $totalFab = $completedFab = $totalElec = $completedElec = $fabTaskAssignedAmount = $elecTaskAssignedAmount = 0;
        $this->calculateDepartmentTaskCompletionAmount_fab($dateFrom, $dateTo, $totalFab, $completedFab, $fabTaskAssignedAmount);
        $this->calculateDepartmentTaskCompletionAmount_elec($dateFrom, $dateTo, $totalElec, $completedElec, $elecTaskAssignedAmount);

        $fabricationData = [
            'labels' => ['Complete', 'In Progress'],
            'datasets' => [
                [
                    'data' => [MyFormatter::asDecimal2NoSeparator($completedFab), MyFormatter::asDecimal2NoSeparator($totalFab - $completedFab)],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]
            ]
        ];
        $electricalData = [
            'labels' => ['Complete', 'In Progress'],
            'datasets' => [
                [
                    'data' => [MyFormatter::asDecimal2NoSeparator($completedElec), MyFormatter::asDecimal2NoSeparator($totalElec - $completedElec)],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]
            ]
        ];

        $tasksElecCreated = (VElecTasksMasterlist::find()
                        ->select(['project_production_panel_code'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->count()) * 100;

        $tasksElecComplete = VElecTasksMasterlist::find()
                        ->select(['project_production_panel_code', 'elec_complete_percent'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->sum('elec_complete_percent') * 1;

        if ($tasksElecCreated == 0) {
            $incompleteElec = 0;
            $completeElec = 0;
        } else {
            $incompleteElec = $tasksElecCreated - $tasksElecComplete;
            $completeElec = $tasksElecComplete;
        }

        $electricalData2 = [
            'labels' => ['Complete', 'In Progress'],
            'datasets' => [
                [
                    'data' => [$completeElec, $incompleteElec],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]
            ]
        ];

        $tasksFabCreated = (VFabTasksMasterlist::find()
                        ->select(['project_production_panel_code'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->count()) * 100;

        $tasksFabComplete = VFabTasksMasterlist::find()
                        ->select(['project_production_panel_code', 'fab_complete_percent'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->sum('fab_complete_percent') * 1;

        if ($tasksFabCreated == 0) {
            $incompleteFab = 0;
            $completeFab = 0;
        } else {
            $incompleteFab = $tasksFabCreated - $tasksFabComplete; // ($tasksFabCreated - $tasksFabComplete) / $tasksFabCreated * 100;
            $completeFab = $tasksFabComplete;
        }

        $fabricationData2 = [
            'labels' => ['Complete', 'In Progress'],
            'datasets' => [
                [
                    'data' => [$completeFab, $incompleteFab],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]
            ]
        ];

        return $this->render('_chartDepartmentTaskCompletionAmount', [
                    'model' => $model,
                    'electricalData' => json_encode($electricalData),
                    'fabricationData' => json_encode($fabricationData),
                    'totalFab' => $totalFab,
                    'totalElec' => $totalElec,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'fabTaskAssignedAmount' => $fabTaskAssignedAmount,
                    'elecTaskAssignedAmount' => $elecTaskAssignedAmount,
                    'electricalData2' => json_encode($electricalData2),
                    'fabricationData2' => json_encode($fabricationData2)
        ]);
    }

    /**
     * Sub Fuction of actionGetChartDepartmentTaskCompletionAmount
     * @param type $dateFrom
     * @param type $dateTo
     * @param type $denominator
     * @param type $numerator
     */
    private function calculateDepartmentTaskCompletionAmount_fab($dateFrom, $dateTo, &$denominator, &$numerator, &$fabTaskAssignedAmount) {
        $denominatorSql = "SELECT SUM(IFNULL(amount, 0) * IFNULL(panel_type_weight, 0) * (IFNULL(assemble, 0) + IFNULL(bend, 0) + IFNULL(cutnpunch, 0) + IFNULL(powcoat, 0) + IFNULL(weldngrind, 0)) / 10000) AS task_total "
                . "FROM prod_fab_task_weight INNER JOIN project_production_panels ON proj_prod_panel_id = project_production_panels.id "
                . "WHERE prod_fab_task_weight.created_at BETWEEN :dateFrom AND :dateTo ";

        $denominator = Yii::$app->db->createCommand($denominatorSql)->bindValue(':dateFrom', $dateFrom)->bindValue(':dateTo', $dateTo)->queryScalar();

        $numeratorSql = "SELECT a.proj_prod_panel_id, IFNULL(b.amount, 0) AS panel_amount, 
        IFNULL(a.panel_type_weight, 0) / 100 AS type_weight,
        IFNULL(a.assemble, 0) / 100 AS assemble_perc,
        IFNULL(a.bend, 0) / 100 AS bend_perc,
        IFNULL(a.cutnpunch, 0) / 100 AS cutnpunch_perc,
        IFNULL(a.powcoat, 0) / 100 AS powcoat_perc,
        IFNULL(a.weldngrind, 0) / 100 AS weldngrind_perc,
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_fab_tasks WHERE proj_prod_panel_id = b.id 
            AND fab_task_code = 'assemble'), 0) AS assemble,  
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_fab_tasks WHERE proj_prod_panel_id = b.id 
            AND fab_task_code = 'bend'), 0) AS bend,  
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_fab_tasks WHERE proj_prod_panel_id = b.id 
            AND fab_task_code = 'cutnpunch'), 0) AS cutnpunch,
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_fab_tasks WHERE proj_prod_panel_id = b.id 
            AND fab_task_code = 'powcoat'), 0) AS powcoat,
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_fab_tasks WHERE proj_prod_panel_id = b.id 
            AND fab_task_code = 'weldngrind'), 0) AS weldngrind
    FROM prod_fab_task_weight AS a JOIN project_production_panels AS b 
        ON a.proj_prod_panel_id = b.id WHERE a.created_at BETWEEN :dateFrom AND :dateTo";

        $numeratorResult = Yii::$app->db->createCommand($numeratorSql)->bindValue(':dateFrom', $dateFrom)->bindValue(':dateTo', $dateTo)->queryAll();

        $numerator = 0;

        foreach ($numeratorResult as $row) {
            $calculation = $row['panel_amount'] * $row['type_weight'] * (
                    $row['assemble_perc'] * $row['assemble'] +
                    $row['bend_perc'] * $row['bend'] +
                    $row['cutnpunch_perc'] * $row['cutnpunch'] +
                    $row['powcoat_perc'] * $row['powcoat'] +
                    $row['weldngrind_perc'] * $row['weldngrind']
            );
            $numerator += $calculation;
        }
        $fabTaskAssignedAmountSql = "SELECT 
            SUM(
                IFNULL(task_assign_fab.quantity, 0) * 
                IFNULL(project_production_panels.amount, 0) *  
                IFNULL(prod_fab_task_weight.panel_type_weight, 0) * 
                (
                    CASE 
                        WHEN production_fab_tasks.fab_task_code = 'assemble' THEN IFNULL(prod_fab_task_weight.assemble, 0)
                        WHEN production_fab_tasks.fab_task_code = 'bend' THEN IFNULL(prod_fab_task_weight.bend, 0)
                        WHEN production_fab_tasks.fab_task_code = 'cutnpunch' THEN IFNULL(prod_fab_task_weight.cutnpunch, 0)
                        WHEN production_fab_tasks.fab_task_code = 'powcoat' THEN IFNULL(prod_fab_task_weight.powcoat, 0)
                        WHEN production_fab_tasks.fab_task_code = 'weldngrind' THEN IFNULL(prod_fab_task_weight.weldngrind, 0)
                        ELSE 0
                    END
                ) / 10000
            ) AS task_assigned_total
        FROM task_assign_fab
        INNER JOIN production_fab_tasks 
            ON task_assign_fab.prod_fab_task_id = production_fab_tasks.id
        INNER JOIN project_production_panels 
            ON production_fab_tasks.proj_prod_panel_id = project_production_panels.id
        INNER JOIN prod_fab_task_weight 
            ON production_fab_tasks.proj_prod_panel_id = prod_fab_task_weight.proj_prod_panel_id
        WHERE task_assign_fab.prod_fab_task_id IS NOT NULL 
        AND task_assign_fab.created_at BETWEEN :dateFrom AND :dateTo;";

        $fabTaskAssignedAmount = Yii::$app->db->createCommand($fabTaskAssignedAmountSql)
                ->bindValue(':dateFrom', $dateFrom)
                ->bindValue(':dateTo', $dateTo)
                ->queryScalar();
    }

    /**
     * Sub Fuction of actionGetChartDepartmentTaskCompletionAmount
     * @param type $dateFrom
     * @param type $dateTo
     * @param type $denominator
     * @param type $numerator
     */
    private function calculateDepartmentTaskCompletionAmount_elec($dateFrom, $dateTo, &$denominator, &$numerator, &$elecTaskAssignedAmount) {
        $denominatorSql = "SELECT SUM(IFNULL(amount, 0) * IFNULL(panel_type_weight, 0) * (IFNULL(busbar, 0) + IFNULL(mount, 0) + IFNULL(test, 0) + IFNULL(wire, 0)) / 10000) AS task_total "
                . "FROM prod_elec_task_weight INNER JOIN project_production_panels ON proj_prod_panel_id = project_production_panels.id "
                . "WHERE prod_elec_task_weight.created_at BETWEEN :dateFrom AND :dateTo ";

        $denominator = Yii::$app->db->createCommand($denominatorSql)->bindValue(':dateFrom', $dateFrom)->bindValue(':dateTo', $dateTo)->queryScalar();

        $numeratorSql = "SELECT a.proj_prod_panel_id, IFNULL(b.amount, 0) AS panel_amount, 
        IFNULL(a.panel_type_weight, 0) / 100 AS type_weight,
        IFNULL(a.busbar, 0) / 100 AS busbar_perc,
        IFNULL(a.mount, 0) / 100 AS mount_perc,
        IFNULL(a.test, 0) / 100 AS test_perc,
        IFNULL(a.wire, 0) / 100 AS wire_perc,
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_elec_tasks WHERE proj_prod_panel_id = b.id 
            AND elec_task_code = 'busbar'), 0) AS busbar,  
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_elec_tasks WHERE proj_prod_panel_id = b.id 
            AND elec_task_code = 'mount'), 0) AS mount,  
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_elec_tasks WHERE proj_prod_panel_id = b.id 
            AND elec_task_code = 'test'), 0) AS test,
        IFNULL((SELECT qty_completed / qty_total AS completed_percentage 
            FROM production_elec_tasks WHERE proj_prod_panel_id = b.id 
            AND elec_task_code = 'wire'), 0) AS wire
    FROM prod_elec_task_weight AS a JOIN project_production_panels AS b 
        ON a.proj_prod_panel_id = b.id WHERE a.created_at BETWEEN :dateFrom AND :dateTo ";

        $numeratorResult = Yii::$app->db->createCommand($numeratorSql)->bindValue(':dateFrom', $dateFrom)->bindValue(':dateTo', $dateTo)->queryAll();

        $numerator = 0;

        foreach ($numeratorResult as $row) {
            $calculation = $row['panel_amount'] * $row['type_weight'] * (
                    $row['busbar_perc'] * $row['busbar'] +
                    $row['mount_perc'] * $row['mount'] +
                    $row['test_perc'] * $row['test'] +
                    $row['wire_perc'] * $row['wire']
            );
            $numerator += $calculation;
        }

        $elecTaskAssignedAmountSql = "SELECT 
            SUM(
                IFNULL(task_assign_elec.quantity, 0) * 
                IFNULL(project_production_panels.amount, 0) *  
                IFNULL(prod_elec_task_weight.panel_type_weight, 0) * 
                (
                    CASE 
                        WHEN production_elec_tasks.elec_task_code = 'busbar' THEN IFNULL(prod_elec_task_weight.busbar, 0)
                        WHEN production_elec_tasks.elec_task_code = 'mount' THEN IFNULL(prod_elec_task_weight.mount, 0)
                        WHEN production_elec_tasks.elec_task_code = 'test' THEN IFNULL(prod_elec_task_weight.test, 0)
                        WHEN production_elec_tasks.elec_task_code = 'wire' THEN IFNULL(prod_elec_task_weight.wire, 0)
                        ELSE 0
                    END
                ) / 10000
            ) AS task_assigned_total
        FROM task_assign_elec
        INNER JOIN production_elec_tasks 
            ON task_assign_elec.prod_elec_task_id = production_elec_tasks.id
        INNER JOIN project_production_panels 
            ON production_elec_tasks.proj_prod_panel_id = project_production_panels.id
        INNER JOIN prod_elec_task_weight 
            ON production_elec_tasks.proj_prod_panel_id = prod_elec_task_weight.proj_prod_panel_id
        WHERE task_assign_elec.prod_elec_task_id IS NOT NULL 
        AND task_assign_elec.created_at BETWEEN :dateFrom AND :dateTo;";

        $elecTaskAssignedAmount = Yii::$app->db->createCommand($elecTaskAssignedAmountSql)
                ->bindValue(':dateFrom', $dateFrom)
                ->bindValue(':dateTo', $dateTo)
                ->queryScalar();
    }

    public function actionGetChartQuotationHitIndividual() {
        $model = new ReportingModel();
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y') . '-01-01';
        $model->dateTo = $dateTo;
        $model->dateFrom = $dateFrom;
        $qDoneData = $model->initializeChartData();
        $qHitsData = $model->initializeChartData();
        $tasksCompletionData = $model->initializeChartData();
        $countQuotationIndividual = 0;
        $countQuotationAllProjectCoordinator = 0;
        $project_coordinator = null;
        $defaultUser = User::getProjectCoordinatorList()[0] ?? null;

        if (!$model->load(Yii::$app->request->post())) {
            $model->userId = $defaultUser['id'] ?? null;
        }

        if ($model->userId !== null) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
            $project_coordinator = \common\models\User::findOne($model->userId);

            $countQuotationIndividual = $model->getQuotationDoneByProjectCoordinator($model->dateFrom, $model->dateTo, $project_coordinator->id);
            $countQuotationAllProjectCoordinator = $model->getQuotationDoneAllProjectCoordinator($model->dateFrom, $model->dateTo);

            $totalQuotationOthers = $countQuotationAllProjectCoordinator - $countQuotationIndividual;
            $qDoneData = $model->preparePieChartData('By ' . $project_coordinator->fullname, 'By Other Project Coordinators', $countQuotationIndividual, $totalQuotationOthers, '#633EBB', '#BE61CA');

            $totalQuotationIndividualConfirmed = $model->getQuotationHitsByProjectCoordinator($model->dateFrom, $model->dateTo, $project_coordinator->id);
            $totalQuotationIndividualPending = $countQuotationIndividual - $totalQuotationIndividualConfirmed;
            $qHitsData = $model->preparePieChartData('Confirmed', 'Pending', $totalQuotationIndividualConfirmed, $totalQuotationIndividualPending, '#007CC3', '#FFAB05');

            $dataCompletion = $model->getTasksCompletionByProjectCoordinator($model->dateFrom, $model->dateTo, $project_coordinator->id);
            $tasksIncomplete = $dataCompletion['rowCount'] > 0 ? 100 - $dataCompletion['combinedAverage'] : 0;
            $tasksCompletionData = $model->preparePieChartData('Complete', 'Incomplete', $dataCompletion['combinedAverage'], $tasksIncomplete, '#52D726', '#F13C59');
        }

        return $this->render('_chartQuotationIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'qDoneData' => json_encode($qDoneData),
                    'qHitsData' => json_encode($qHitsData),
                    'tasksCompletionData' => json_encode($tasksCompletionData),
                    'totalQuotationIndividual' => $countQuotationIndividual ?? null,
                    'totalQuotationAllProjectCoordinator' => $countQuotationAllProjectCoordinator ?? null,
                    'project_coordinator' => $project_coordinator ?? null
        ]);
    }

    public function actionGetChartFactoryStaff() {
        $model = new ReportingModel();
        $reportDetail = [];
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y') . '-01-01';
        $model->dateTo = $dateTo;
        $model->dateFrom = $dateFrom;
        $model->is_internalProject = '';
        $taskCompletionFabData = $model->initializeChartData();
        $taskCompletionElecData = $model->initializeChartData();
        $worker = null;
        $totalAmountTaskFab = 0;
        $totalAmountTaskElec = 0;
        $defaultUser = User::getStaffList_productionAssignee()[0] ?? null;

        if (!$model->load(Yii::$app->request->post())) {
            $model->userId = $defaultUser['id'] ?? null;
        }

        if ($model->userId !== null) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
            $worker = \common\models\User::findOne($model->userId);
//            $fabPerformanceList_individual = $this->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
//            $fabPerformanceList_dept = $this->calculateDepartmentCompleteRecord($model, TaskAssignment::taskTypeFabrication);
//            $elecPerformanceList_individual = $this->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);
//            $elecPerformanceList_dept = $this->calculateDepartmentCompleteRecord($model, TaskAssignment::taskTypeElectrical);
            $fabPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeFabrication);
            $fabPerformanceList_dept = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, TaskAssignment::taskTypeFabrication);
            $elecPerformanceList_individual = $model->calculatePersonalCompleteRecord($model, TaskAssignment::taskTypeElectrical);
            $elecPerformanceList_dept = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, TaskAssignment::taskTypeElectrical);

//            $fabPerformanceList = VWorkerPerformanceFab::findCustomSummarize($model->userId, $model->dateFrom, $model->dateTo);
            if (!empty($fabPerformanceList_individual)) {
                $individualAmt = round(array_sum(array_map('floatval', array_column($fabPerformanceList_individual, 'performanceAmount'))), 2);
                $totalAmountTaskFab = round(array_sum(array_map('floatval', array_column($fabPerformanceList_dept, 'totalPerformance'))), 2);
                $byOthersFab = $totalAmountTaskFab - $individualAmt;
                $taskCompletionFabData = $model->preparePieChartData('By ' . $worker->fullname, 'By Other Staffs', $individualAmt, $byOthersFab, '#4B0082', '#87CEEB');
            }
//            }
            if (!empty($elecPerformanceList_individual)) {
                $individualAmt = round(array_sum(array_map('floatval', array_column($elecPerformanceList_individual, 'performanceAmount'))), 2);
                $totalAmountTaskElec = round(array_sum(array_map('floatval', array_column($elecPerformanceList_dept, 'totalPerformance'))), 2);
                $byOthersElec = $totalAmountTaskElec - $individualAmt;
                $taskCompletionElecData = $model->preparePieChartData('By ' . $worker->fullname, 'By Other Staffs', $individualAmt, $byOthersElec, '#4B0082', '#87CEEB');
            }
        }

        return $this->render('_chartFactoryStaff', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'tasksCompletionFabData' => json_encode($taskCompletionFabData),
                    'tasksCompletionElecData' => json_encode($taskCompletionElecData),
                    'totalAmountTaskFab' => $totalAmountTaskFab ?? null,
                    'totalAmountTaskElec' => $totalAmountTaskElec ?? null,
                    'worker' => $worker ?? null
        ]);
    }

    public function actionExportToExcelDepartmentPerformanceReport() {
        $reportDetail = json_decode(Yii::$app->request->post('reportDetail'));
        $totalPerformanceAmount = Yii::$app->request->post('totalPerformanceAmount');
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;

        return $this->renderPartial('_departmentPerformanceCSV', [
                    'reportDetail' => $reportDetail,
                    'totalPerformanceAmount' => $totalPerformanceAmount
        ]);
    }

    private function calculatePersonalCompleteRecord($model, $department) {
        if ($department == TaskAssignment::taskTypeFabrication) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    '*',
                                    'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user_id' => $model->userId])
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    '*',
                                    'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user_id' => $model->userId])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->asArray()
                                ->all();
            }
        }

        if ($department == TaskAssignment::taskTypeElectrical) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    '*',
                                    'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user_id' => $model->userId])
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    '*',
                                    'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user_id' => $model->userId])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->asArray()
                                ->all();
            }
        }
    }

    private function calculateDepartmentCompleteRecordIncludeInactiveStaff($model, $department) {
        if ($department == TaskAssignment::taskTypeFabrication) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            }
        }

        if ($department == TaskAssignment::taskTypeElectrical) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            }
        }
    }

    private function calculateDepartmentCompleteRecord($model, $department) {
        if ($department == TaskAssignment::taskTypeFabrication) {
            if ($model->is_internalProject === "") {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_fabrication.user_id as id',
                                    'v_staff_competed_panel_record_fabrication.staff_id as staffId',
                                    'v_staff_competed_panel_record_fabrication.fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_fabrication')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_fabrication.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['v_staff_competed_panel_record_fabrication.user_id'])
                                ->orderBy(['user.fullname' => SORT_ASC])
                                ->all();
            } else {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_fabrication.user_id as id',
                                    'v_staff_competed_panel_record_fabrication.staff_id as staffId',
                                    'v_staff_competed_panel_record_fabrication.fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_fabrication')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_fabrication.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['v_staff_competed_panel_record_fabrication.user_id'])
                                ->orderBy(['user.fullname' => SORT_ASC])
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
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_electrical')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_electrical.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['v_staff_competed_panel_record_electrical.user_id'])
                                ->orderBy(['user.fullname' => SORT_ASC])
                                ->all();
            } else {
                return (new \yii\db\Query())
                                ->select([
                                    'v_staff_competed_panel_record_electrical.user_id as id',
                                    'v_staff_competed_panel_record_electrical.staff_id as staffId',
                                    'v_staff_competed_panel_record_electrical.fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->from('v_staff_competed_panel_record_electrical')
                                ->join('INNER JOIN', 'user', 'user.id = v_staff_competed_panel_record_electrical.user_id')
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                                ->groupBy(['v_staff_competed_panel_record_electrical.user_id'])
                                ->orderBy(['user.fullname' => SORT_ASC])
                                ->all();
            }
        }
    }

    private function calculateDepartmentCompleteRecordOld($model, $department) {
        if ($department == TaskAssignment::taskTypeFabrication) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordFabrication::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            }
        }

        if ($department == TaskAssignment::taskTypeElectrical) {
            if ($model->is_internalProject === "") {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            } else {
                return VStaffCompetedPanelRecordElectrical::find()
                                ->select([
                                    'user_id as id',
                                    'staff_id as staffId',
                                    'fullname',
                                    'ROUND(SUM(amount / totalStaff * qty_completed_panel / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS totalPerformance'
                                ])
                                ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
                                ->andWhere(['internal_project' => $model->is_internalProject])
                                ->groupBy('user_id')
                                ->asArray()
                                ->all();
            }
        }
    }
}
