<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\ProjectProduction\ProjectProductionMasterSearch;
use frontend\models\projectproduction\electrical\TaskAssignElec;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;
use frontend\models\projectproduction\electrical\TaskAssignElecSearch;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\task\TaskAssignment;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use common\modules\auth\models\AuthItem;
use frontend\models\projectproduction\task\TaskAssignOngoingSummary;
use frontend\models\projectproduction\electrical\ProductionElecTasksError;
use frontend\models\projectproduction\electrical\ProductionElecTasksErrorSearch;
use frontend\models\projectproduction\electrical\ProdElecTaskWeight;
use frontend\models\projectproduction\electrical\TaskAssignElecComplete;
use frontend\models\projectproduction\electrical\TaskAssignElecCompleteDelete;
use frontend\models\ProjectProduction\electrical\TaskAssignElecStaffComplete;
use frontend\models\projectproduction\electrical\TaskAssignElecStaff;
use frontend\models\projectproduction\electrical\TaskAssignElecStaffCompleteDelete;
use frontend\models\projectproduction\electrical\ProductionElecTasksErrorStaff;

/**
 * WorkAssignmentController implements the CRUD actions for WorkAssignmentMaster model.
 */
class ElecTaskController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/workassignment/elec/');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index-elec-project-list',
                            'index-elec-in-progress',
                            'index-elec-all',
                            'index-elec-project-panels',
                            'view-assigned-task',
                            'ajax-make-complaint',
                            'ajax-action-set-complete',
                            'delete-complaint',
                            'delete-elec-task',
                        ],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin]
                    ],
                    [
                        'actions' => [
                            'delete-complaint',
                            'ajax-make-complaint',
                            'ajax-action-set-complete',
                            'assign-task',
                            'update-assign-task',
                            'deactivate-assign-task',
                            'assign-task-multiple',
                            'assign-task-multiple-panels',
                            'confirm-task-assignment',
                            'checking-tasks-assignment',
                            'ajax-action-revert-set-complete',
                            'update-target-date',
                            'update-task-weight',
                            'update-task-weight-multiple-panels',
                            'save-task-weight-multiple-panels'
                        ],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin]
                    ],
                    [
                        'actions' => ['overall-update', 'temp-add-busbar', 'migrate-elec-error-staff'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_SystemAdmin]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-complaint' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Index - Showing Project List
     * @return type
     */
    public function actionIndexElecProjectList() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('indexElecProjectList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Index - Showing Panel Lists, 
     * ***** sub page of actionIndexProjectList
     * @param type $id
     * @return type
     */
    public function actionIndexElecProjectPanels($id) {
        $model = ProjectProductionMaster::findOne($id);

        return $this->render('indexElecProjectPanels', [
                    'model' => $model,
        ]);
    }

    /**
     * by Khetty, 21/5/2024
     * Updates task weight
     */
    public function actionUpdateTaskWeight($id) {
        $model = new ProductionElecTasks();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $elecTaskWeight = $model->getElecTaskValue($id);
        $panel = ProjectProductionPanels::findOne($id);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $elecTaskWeightModel = new ProdElecTaskWeight();
            $updateElecTaskWeight = $elecTaskWeightModel->updateElecTaskWeight($postData);
            if (!$updateElecTaskWeight) {
                FlashHandler::err("Update Failed.");
            }

            FlashHandler::success("Update task weight success.");
            return $this->redirect(['index-elec-project-panels', 'id' => $panel->proj_prod_master]);
        }

        return $this->renderAjax('updateTaskWeight', [
                    'refElecTask' => $refElecTask,
                    'elecTaskWeight' => $elecTaskWeight,
                    'panel' => $panel
        ]);
    }

    /*
     * by Khetty, 8/1/2024
     * ****** Delete Elec Task and update the task weight to null ******
     */

    public function actionDeleteElecTask($taskId, $id) {
        $elecTask = ProductionElecTasks::findOne($taskId);

        if ($elecTask && $elecTask->delete()) {
            FlashHandler::success("Task deleted.");
            $elecTaskWeight = new ProdElecTaskWeight();
            $elecTaskWeight->updateElecTaskWeightAfterDeleteTask($elecTask->proj_prod_panel_id, $elecTask->elec_task_code);

            $elec = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $elecTask->proj_prod_panel_id])->all();
            $panel = ProjectProductionPanels::findOne($elecTask->proj_prod_panel_id);
            if (empty($elec)) {
                $panel->elec_work_status = null; //update work status to null if no task found
                $panel->save();
            } else {
                $project = ProjectProductionMaster::findOne($panel->proj_prod_master);
                $project->updateAvgElecProgressPercent();
//                $panel->updateElecProgressPercent();
                $panel->checkPanelElecWorkStatus();
            }
        } else {
            FlashHandler::err("Task delete failed.");
        }

        return $this->redirect(['index-elec-project-panels', 'id' => $id]);
    }

    /**
     * Assigning Task To Staffs
     */
    public function actionAssignTask($id) {
        $task = ProductionElecTasks::findOne($id);
        $model = new TaskAssignElec();
        $model->proj_prod_panel_id = $task->proj_prod_panel_id;
        $model->prod_elec_task_id = $task->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->staffIds = Yii::$app->request->post('selectStaff');
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->processAndSave() && $model->updateTaskCalculation() && $model->updatePanelCalculation()) {
                $targetDateTrialModel = new \frontend\models\projectproduction\electrical\TaskAssignElecTargetDateTrial();
                $targetDateTrialModel->task_assign_elec_id = $model->id;
                $targetDateTrialModel->target_date = $model->current_target_date;
                $targetDateTrialModel->remark = \frontend\models\ProjectProduction\ProjProdTargetDateTrial::REMARK_INITIAL_TARGET_DATE;
                if ($targetDateTrialModel->save()) {
                    $model->current_target_date = $targetDateTrialModel->target_date;
                    $model->update();
                }
                $transaction->commit();
                FlashHandler::success("Task assign success.");
                return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
            } else {
                $transaction->rollBack();
                FlashHandler::err("Task assign failed.");
            }
        } else {
            $model->quantity = $task->qty_total - $task->qty_assigned;
            $model->start_date = (date('Y-m-d'));
        }

//        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical); // To be edited, filter by department
        $staffList = User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
                ->join("INNER JOIN", "v_worker_task_categories_elec", "v_worker_task_categories_elec.user_id=user.id")
                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
                ->where(['task_code' => $task->elec_task_code])
                ->asArray()
                ->all();

        return $this->render('assignTask', [
                    'task' => $task,
                    'model' => $model,
                    'staffList' => $staffList
        ]);
    }

    /**
     * Assigning Task To Staffs (Multiple)
     */
//    public function actionAssignTaskMultiple($panelId) {
//        $model = new TaskAssignment();
//
//        if ($model->load(Yii::$app->request->post())) {
//            $model->startDate = MyFormatter::fromDateRead_toDateSQL($model->startDate);
//            $panel = ProjectProductionPanels::findOne($panelId);
//            $project = $panel->projProdMaster;
//            $formList = [];
//            $model->panelId = $panelId;
//            $model->projectId = $project->id;
//            $model->staffIdString = implode(",", $model->staffIds);
//            foreach ($model->taskCode as $key => $task) {
//                $productionElecTasks = ProductionElecTasks::findOne(['proj_prod_panel_id' => $panelId, 'elec_task_code' => $task]);
//                $taskAssign = new TaskAssignElec();
//                $taskAssign->proj_prod_panel_id = $model->panelId;
//                $taskAssign->prod_elec_task_id = $productionElecTasks->id;
//                $taskAssign->quantity = $productionElecTasks->qty_total - $productionElecTasks->qty_assigned;
//                $taskAssign->start_date = $model->startDate;
//                $taskAssign->comments = $model->comments;
//                $taskAssign->tempTaskName = RefProjProdTaskElec::findOne($task)->name;
//                $taskAssign->taskCode = $task;
//                $formList[] = $taskAssign;
//            }
//
////            $staffNameList = User::find()->where(["in", 'id', $model->staffIds])->all();
//            $staffNameList = \frontend\models\projectproduction\task\VWorkerTaskCategoriesElec::find()
//                    ->where(["in", 'user_id', $model->staffIds])
//                    ->andWhere(['in', 'task_code', $model->taskCode])
//                    ->asArray()
//                    ->all();
//
//            return $this->render('confirmTaskAssignment', [
//                        'model' => $model,
//                        'panel' => $panel,
//                        'project' => $project,
//                        'formList' => $formList,
//                        "staffNameList" => $staffNameList
//            ]);
//        } else {
//            $model->startDate = (date('Y-m-d'));
//            $model->panelId = $panelId;
//        }
//
//        $panel = ProjectProductionPanels::findOne($panelId);
//        $project = $panel->projProdMaster;
//        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical); // To be edited, filter by department
//
//        $taskList = ProductionElecTasks::getPanelTasks($panelId);
//        return $this->render('assignTaskMultiple', [
//                    'panel' => $panel,
//                    'project' => $project,
//                    'model' => $model,
//                    'staffList' => $staffList,
//                    'taskList' => $taskList
//        ]);
//    }

    public function actionAssignTaskMultiple($panelId) {
        $model = new TaskAssignment();

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $model->startDate = MyFormatter::fromDateRead_toDateSQL($model->startDate);

            $panel = ProjectProductionPanels::findOne($panelId);
            $project = $panel->projProdMaster;
            $formList = [];
            $model->panelId = $panelId;
            $model->projectId = $project->id;

            // extract posted arrays
            $taskCodes = $post['TaskAssignment']['taskCode'] ?? [];
            $staffIds = $post['TaskAssignment']['staffIds'] ?? [];

            // keep only staffIds that match selected taskCodes
            $validStaffs = [];
            foreach ($staffIds as $taskCode => $staffList) {
                if (in_array($taskCode, $taskCodes, true)) {
                    $validStaffs[$taskCode] = $staffList;
                }
            }

            // build formList per task
            foreach ($taskCodes as $taskCode) {
                $productionElecTasks = ProductionElecTasks::findOne([
                    'proj_prod_panel_id' => $panelId,
                    'elec_task_code' => $taskCode
                ]);

                if (!$productionElecTasks) {
                    continue;
                }

                $taskAssign = new TaskAssignElec();
                $taskAssign->proj_prod_panel_id = $model->panelId;
                $taskAssign->prod_elec_task_id = $productionElecTasks->id;
                $taskAssign->quantity = $productionElecTasks->qty_total - $productionElecTasks->qty_assigned;
                $taskAssign->start_date = $model->startDate;
                $taskAssign->comments = $model->comments;
                $taskAssign->tempTaskName = RefProjProdTaskElec::findOne($taskCode)->name;
                $taskAssign->taskCode = $taskCode;

                // attach staff (only valid ones)
                $taskAssign->staffIds = $validStaffs[$taskCode] ?? [];

                $formList[] = $taskAssign;
            }

            $staffNameList = [];
            foreach ($validStaffs as $taskCode => $staffList) {
                if (!empty($staffList)) {
                    $rows = \frontend\models\projectproduction\task\VWorkerTaskCategoriesElec::find()
                            ->where(['task_code' => $taskCode])
                            ->andWhere(['in', 'user_id', $staffList])
                            ->asArray()
                            ->all();
                    $staffNameList = array_merge($staffNameList, $rows);
                }
            }

            return $this->render('confirmTaskAssignment', [
                        'model' => $model,
                        'panel' => $panel,
                        'project' => $project,
                        'formList' => $formList,
                        'staffNameList' => $staffNameList,
            ]);
        } else {
            $model->startDate = date('Y-m-d');
            $model->panelId = $panelId;
        }

        $panel = ProjectProductionPanels::findOne($panelId);
        $project = $panel->projProdMaster;
        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical);
        $taskList = ProductionElecTasks::getPanelTasks($panelId);

        return $this->render('assignTaskMultiple', [
                    'panel' => $panel,
                    'project' => $project,
                    'model' => $model,
                    'staffList' => $staffList,
                    'taskList' => $taskList,
        ]);
    }

    /**
     * Assigning Task To Staffs with Multiple Panels
     */
    public function actionAssignTaskMultiplePanels($projectId) {
        $model = new TaskAssignment();

        if (Yii::$app->request->post('panelId') != null) {
            $panelIds = implode(",", Yii::$app->request->post('panelId'));
        } else {
            $panelIds = Yii::$app->request->post('panelIds');
        }

        $model->startDate = (date('Y-m-d'));
        $model->panelIds = $panelIds;
        $project = ProjectProductionMaster::findOne($projectId);
        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical); // To be edited, filter by department
        $taskList = RefProjProdTaskElec::getAllActiveSorted();
        $panels = ProjectProductionPanels::find()->where("id IN ($panelIds)")->all();
        return $this->render('assignTaskMultiplePanelElec', [
                    'panels' => $panels,
                    'project' => $project,
                    'model' => $model,
                    'staffList' => $staffList,
                    'taskList' => $taskList
        ]);
    }

    /**
     * Checking and Confirming tasks for multiple panels
     */
//    public function actionCheckingTasksAssignment($projectId) {
//        $model = new TaskAssignment();
//        $staffNameList = [];
//        if ($model->load(Yii::$app->request->post())) {
//            $model->startDate = MyFormatter::fromDateRead_toDateSQL($model->startDate);
//
//            $formList = [];
//            $project = ProjectProductionMaster::findOne($projectId);
//
//            $staffNameList = \frontend\models\projectproduction\task\VWorkerTaskCategoriesElec::find()
//                    ->where(["in", 'user_id', $model->staffIds])
//                    ->andWhere(['in', 'task_code', $model->taskCode])
//                    ->asArray()
//                    ->all();
//
//            $panelIds = explode(",", $model->panelIds);
//            foreach ($panelIds as $panelId) {
//                $panel = ProjectProductionPanels::findOne($panelId);
//                $model->projectId = $project->id;
//                $model->staffIdString = implode(",", $model->staffIds);
//                foreach ($model->taskCode as $key => $task) {
//                    $productionElecTasks = ProductionElecTasks::findOne(['proj_prod_panel_id' => $panelId, 'elec_task_code' => $task]);
//                    if (empty($productionElecTasks)) {
//                        continue;
//                    }
//                    $taskAssign = new TaskAssignElec();
//                    $taskAssign->proj_prod_panel_id = $panelId;
//                    $taskAssign->panelCode = $panel->project_production_panel_code;
//                    $taskAssign->prod_elec_task_id = $productionElecTasks->id;
//                    $taskAssign->quantity = $productionElecTasks->qty_total - $productionElecTasks->qty_assigned;
//                    $taskAssign->start_date = $model->startDate;
//                    $taskAssign->comments = $model->comments;
//                    $taskAssign->tempTaskName = RefProjProdTaskElec::findOne($task)->name;
//                    $taskAssign->taskCode = $task;
//                    $formList[] = $taskAssign;
//                }
//            }
//
//            return $this->render('confirmTaskAssignment', [
//                        'model' => $model,
////                        'panel' => $panel,
//                        'project' => $project,
//                        'formList' => $formList,
//                        "staffNameList" => $staffNameList
//            ]);
//        }
//    }

    public function actionCheckingTasksAssignment($projectId) {
        $model = new TaskAssignment();

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $model->startDate = MyFormatter::fromDateRead_toDateSQL($model->startDate);

            $formList = [];
            $project = ProjectProductionMaster::findOne($projectId);

            // extract posted arrays
            $taskCodes = $post['TaskAssignment']['taskCode'] ?? [];
            $staffIds = $post['TaskAssignment']['staffIds'] ?? [];

            // keep only staffIds that match selected taskCodes
            $validStaffs = [];
            foreach ($staffIds as $taskCode => $staffList) {
                if (in_array($taskCode, $taskCodes, true)) {
                    $validStaffs[$taskCode] = $staffList;
                }
            }

            $staffNameList = [];
            foreach ($validStaffs as $taskCode => $staffList) {
                if (!empty($staffList)) {
                    $rows = \frontend\models\projectproduction\task\VWorkerTaskCategoriesElec::find()
                            ->where(['task_code' => $taskCode])
                            ->andWhere(['in', 'user_id', $staffList])
                            ->asArray()
                            ->all();
                    $staffNameList = array_merge($staffNameList, $rows);
                }
            }

            // handle all panels
            $panelIds = explode(",", $model->panelIds);
            foreach ($panelIds as $panelId) {
                $panel = ProjectProductionPanels::findOne($panelId);
                $model->projectId = $project->id;

                foreach ($taskCodes as $taskCode) {
                    $productionElecTasks = ProductionElecTasks::findOne([
                        'proj_prod_panel_id' => $panelId,
                        'elec_task_code' => $taskCode
                    ]);
                    if (!$productionElecTasks) {
                        continue;
                    }

                    $taskAssign = new TaskAssignElec();
                    $taskAssign->proj_prod_panel_id = $panelId;
                    $taskAssign->panelCode = $panel->project_production_panel_code;
                    $taskAssign->prod_elec_task_id = $productionElecTasks->id;
                    $taskAssign->quantity = $productionElecTasks->qty_total - $productionElecTasks->qty_assigned;
                    $taskAssign->start_date = $model->startDate;
                    $taskAssign->comments = $model->comments;
                    $taskAssign->tempTaskName = RefProjProdTaskElec::findOne($taskCode)->name;
                    $taskAssign->taskCode = $taskCode;

                    // attach staff for this task (filtered)
                    $taskAssign->staffIds = $validStaffs[$taskCode] ?? [];

                    $formList[] = $taskAssign;
                }
            }

            return $this->render('confirmTaskAssignment', [
                        'model' => $model,
                        'project' => $project,
                        'formList' => $formList,
                        'staffNameList' => $staffNameList,
            ]);
        }
    }

    /**
     * Page to confirm task assignment
     * @param type $taskAssignment
     * @return type
     */
    public function actionConfirmTaskAssignment() {
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            $taskAssignmentBean = new TaskAssignment();
            $taskAssignmentBean->load($postData);
            $transaction = Yii::$app->db->beginTransaction();
            $tempTaskAssign = null;
            $elecTaskToDoChecking = null; // qty assigned in this table maybe not up to date

            if (!empty($postData['TaskAssignElec'])) {
                foreach ((array) $postData['TaskAssignElec'] as $i => $taskAssignment) {
                    $taskAssignElec = new TaskAssignElec();
                    $taskAssignElec->load($taskAssignment, ''); // Use empty string as second parameter to avoid setting the formName() of the model
                    $taskAssignElec->staffIds = explode(',', $taskAssignElec->staffIdString);
                    if ($taskAssignElec->quantity <= 0) {
                        
                    } else {
                        if (!$taskAssignElec->processAndSave(false) || !$taskAssignElec->updateTaskCalculation()) {
                            $elecTaskToDoChecking = $taskAssignElec->prod_elec_task_id;
                            $transaction->rollBack();
                        }
                    }

                    if ($i == sizeof($postData['TaskAssignElec']) - 1) {
                        $tempTaskAssign = $taskAssignElec;
                    }

                    if (!$taskAssignElec->updatePanelCalculation()) {
                        $transaction->rollBack();
                    }

                    if ($taskAssignElec->processAndSave(false) && $taskAssignElec->updateTaskCalculation() && $taskAssignElec->updatePanelCalculation()) {
                        $targetDateTrialModel = new \frontend\models\projectproduction\electrical\TaskAssignElecTargetDateTrial();
                        $targetDateTrialModel->task_assign_elec_id = $taskAssignElec->id;
                        $targetDateTrialModel->target_date = $taskAssignElec->current_target_date;
                        $targetDateTrialModel->remark = \frontend\models\ProjectProduction\ProjProdTargetDateTrial::REMARK_INITIAL_TARGET_DATE;
                        if (!$targetDateTrialModel->save()) {
                            $transaction->rollBack();
                        }
                    }
                }

                if (!TaskAssignOngoingSummary::updateUserTaskOnHand(explode(',', $taskAssignmentBean->staffIdString))) {
                    $transaction->rollBack();
                }
                if ($transaction->isActive) {
                    FlashHandler::success("Task assigned");
                    $transaction->commit();
                } else {
                    $this->taskAssignmentCorrection($elecTaskToDoChecking);
                    FlashHandler::err("Failed to assign, please retry");
                }
                return $this->redirect(['/elec-task/index-elec-project-panels', 'id' => $taskAssignmentBean->projectId]);
            } else {
                FlashHandler::err("Failed to assign");
            }
            return $this->redirect(['/elec-task/index-elec-project-panels', 'id' => $taskAssignmentBean->projectId]);
        }
    }

    /**
     * If in the to do list, qty_assign is not updated, then do correction
     * @param type $taskToDoId
     * @return boolean
     */
    private function taskAssignmentCorrection($taskToDoId) {
        if (!empty($taskToDoId)) {
            $elecTaskTodo = ProductionElecTasks::findOne($taskToDoId);
            $assignedTasks = TaskAssignElec::findAll(["prod_elec_task_id" => $taskToDoId, "active_sts" => 1]);
            if (empty($assignedTasks)) {
                $elecTaskTodo->qty_assigned = 0;
                $elecTaskTodo->qty_completed = 0;
                $elecTaskTodo->update();
            } else {
                $elecTaskTodo->checkAndUpdateAssignCorrection();
                $assignedTasks[0]->updatePanelCalculation();
            }
        }
        return true;
    }

    /**
     * View assigned task status.
     */
    public function actionViewAssignedTask($taskId) {
        $searchModel = new TaskAssignElecSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'viewAssignedTask', ['taskId' => $taskId]);

        $errorModel = new ProductionElecTasksErrorSearch();
        $errorData = $errorModel->search(Yii::$app->request->queryParams, 'singlePanel', ['taskId' => $taskId]);

        $task = ProductionElecTasks::findOne($taskId);
        return $this->render('viewAssignedTask', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'errorModel' => $errorModel,
                    'errorData' => $errorData,
                    'task' => $task,
        ]);
    }

    public function actionUpdateTargetDate($id, $taskId) {
        $model = TaskAssignElec::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post("TaskAssignElec");
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $targetDate = MyFormatter::fromDateRead_toDateSQL($postData["new_target_date"]);
                $targetDateTrialModel = new \frontend\models\projectproduction\electrical\TaskAssignElecTargetDateTrial;
                $targetDateTrialModel->task_assign_elec_id = $model->id;
                $targetDateTrialModel->target_date = $targetDate;
                $targetDateTrialModel->remark = $postData["remark_update_target_date"];

                if (!$targetDateTrialModel->save()) {
                    throw new \Exception("Failed to save target date trial.");
                }

                $model->current_target_date = $targetDate;
                if (!$model->save()) {
                    throw new \Exception("Failed to update task target date.");
                }

                $transaction->commit();
                FlashHandler::success("Successfully updated the task target completion date!");
            } catch (Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("Failed. Please try again.");
            }

            return $this->redirect(['view-assigned-task', 'taskId' => $taskId]);
        }
        return $this->renderAjax('_updateTargetDate', [
                    'model' => $model,
        ]);
    }

//    public function actionAjaxMakeComplaint($taskId = null, $complaintId = null) {
//        $model = $taskId ? new ProductionElecTasksError() : ProductionElecTasksError::findOne($complaintId);
//        $task = $model->isNewRecord ? ProductionElecTasks::findOne($taskId) : $model->productionElecTask;
//
//        $post = Yii::$app->request->post('ProductionElecTasksError');
//
//        if (Yii::$app->request->isPost) {
//            if ($model->isNewRecord) {
//                $model->production_elec_task_id = $taskId;
//            }
//            $model->remark = $post['remark'];
//            $model->error_code = $post['error_code'];          
//            if ($model->validate() && $model->save()) {
//                return $this->redirect(['view-assigned-task', 'taskId' => $taskId ?? $model->production_elec_task_id]);
//            } else {
//                return "ERROR";
//            }
//        } else if (!Yii::$app->request->isAjax) {
//            return "ERROR";
//        }
//        return $this->renderAjax('_ajaxFormMakeComplaint', [
//                    'model' => $model,
//                    'task' => $task
//        ]);
//    }

    public function actionAjaxMakeComplaint($taskId = null, $complaintId = null) {
        if (!$taskId && !$complaintId) {
            throw new BadRequestHttpException('Task ID or Complaint ID is required.');
        }

        $model = $complaintId ? ProductionElecTasksError::findOne($complaintId) : new ProductionElecTasksError();
        if (!$model) {
            throw new NotFoundHttpException('Complaint not found');
        }

        $task = $model->isNewRecord ? ProductionElecTasks::findOne($taskId) : $model->productionElecTask;
        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->isNewRecord) {
                $model->production_elec_task_id = $taskId;
            }

            $isNewRecord = $model->isNewRecord;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $success = $model->save() && ($isNewRecord ? $model->saveProductionElecTasksErrorStaff() : $model->updateProductionElecTasksErrorStaff());

                if ($success) {
                    $transaction->commit();
                    return Yii::$app->request->isAjax ? $this->asJson(['success' => true]) : $this->redirect(['view-assigned-task', 'taskId' => $taskId ?? $model->production_elec_task_id]);
                } else {
                    $transaction->rollBack();
                    return Yii::$app->request->isAjax ? $this->asJson(['success' => false, 'errors' => $model->errors]) : Yii::$app->session->setFlash('error', 'Failed to save complaint');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage());
                if (Yii::$app->request->isAjax) {
                    return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
                } else {
                    throw $e;
                }
            }
        }

        // render form for AJAX
        return $this->renderAjax('_ajaxFormMakeComplaint', [
                    'model' => $model,
                    'task' => $task,
        ]);
    }

    public function actionMigrateElecErrorStaff() {
        $errors = ProductionElecTasksError::find()->all();

        foreach ($errors as $error) {
            $taskAssign = TaskAssignElec::findOne([
                'prod_elec_task_id' => $error->production_elec_task_id,
                'deactivated_by' => null,
                'deactivated_at' => null
            ]);

            if (!$taskAssign) {
                Yii::warning("No task assign found for fab task {$error->production_elec_task_id}");
                continue;
            }

            $taskAssignedStaffs = $taskAssign->taskAssignElecStaff;

            foreach ($taskAssignedStaffs as $staff) {
                $exists = ProductionElecTasksErrorStaff::find()
                        ->where([
                            'production_elec_tasks_error_id' => $error->id,
                            'staff_id' => $staff->user_id
                        ])
                        ->exists();

                if ($exists) {
                    continue;
                }

                $taskErrorStaff = new ProductionElecTasksErrorStaff();
                $taskErrorStaff->production_elec_tasks_error_id = $error->id;
                $taskErrorStaff->staff_id = $staff->user_id;
                $taskErrorStaff->is_read = 1;
                $taskErrorStaff->read_at = null;

                if (!$taskErrorStaff->save()) {
                    Yii::error([
                        'error_id' => $error->id,
                        'staff_id' => $staff->user_id,
                        'errors' => $taskErrorStaff->errors
                            ], 'ELEC_MIGRATION_ERROR');
                }
            }
        }

        return 'ELEC migration completed';
    }
    
    public function actionDeleteComplaint($id) {
        $model = ProductionElecTasksError::findOne($id);
        $task = $model->productionElecTask;

        if ($model->delete()) {
            FlashHandler::success('Complaint Deleted');
            return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
        }
    }

    /**
     * Update record, and set complete
     */
    public function actionAjaxActionSetComplete($id, $toIndex = "") {
        $taskAssign = TaskAssignElec::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($taskAssign->addCompletePanelElec(Yii::$app->request->post())) {
                FlashHandler::success("Updated.");
            } else {
                FlashHandler::err("Task assign failed.");
            }

            if ($toIndex == "inProgress") {
                return $this->redirect(['index-elec-in-progress']);
            } else if ($toIndex == "all") {
                return $this->redirect(['index-elec-all']);
            } else {
                return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_elec_task_id]);
            }
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR";
        } else {
            $taskAssign->complete_date = date('Y-m-d');
        }


        return $this->renderAjax('_ajaxFormSetComplete', [
                    'model' => $taskAssign,
                    'allowDateChange' => true,
                    'limit' => $taskAssign->quantity - $taskAssign->complete_qty,
                    'updateAllStaff' => true
        ]);
    }

    /*
     * by Khetty, 12/6/2024
     * Revert completed panel amounts
     * 
     */

//    public function actionAjaxActionRevertSetComplete($id, $taskId) {
//        $taskAssignCompleteDetail = TaskAssignElecComplete::findOne($id);
//        $taskAssign = TaskAssignElec::findOne($taskId);
//        $model = new TaskAssignElecCompleteDelete();
//
//        if (Yii::$app->request->isPost) {
//            $taskAssignCompleteDelete = new TaskAssignElecCompleteDelete();
//            if (!$taskAssignCompleteDelete->copyTaskAssignCompleteDetail($taskAssignCompleteDetail, $_POST['TaskAssignElecCompleteDelete']['revert_comment']) ||!$taskAssignCompleteDetail->delete() ||!$taskAssign->updateCompleteQtyInAssignment()) {
//                FlashHandler::err("Revert completed panel amounts failed.");
//            } else {
////                if (!$taskAssign->addCompletePanelElec(Yii::$app->request->post())) {
//                if (!$taskAssign->updateTaskCalculation() || !$taskAssign->bulkUpdateThisUserTaskOnHand() || !$taskAssign->updateTaskAndPanelCalculation() || !$taskAssign->revertCompleteDate()) {
//                    FlashHandler::err("Revert process failed.");
//                } else {
//                    FlashHandler::success("Updated.");
//                }
//            }
//            return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_elec_task_id]);
//        } else if (!Yii::$app->request->isAjax) {
//            return "ERROR";
//        }
//
//        return $this->renderAjax('_ajaxFormRevertSetComplete', [
////                    'model' => $taskAssign,
////                    'limit' => $taskAssignCompleteDetail->quantity
//                    'model' => $model
//        ]);
//    }

    public function actionAjaxActionRevertSetComplete($id, $taskId) {
        $taskAssignCompleteDetail = TaskAssignElecStaffComplete::findAll($id);
        $taskAssignElecStaff = TaskAssignElecStaff::findOne($taskId);
        $taskAssign = TaskAssignElec::findOne($taskAssignElecStaff->task_assign_elec_id);
        $model = new TaskAssignElecStaffCompleteDelete();

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $taskAssignCompleteDelete = new \frontend\models\projectproduction\electrical\TaskAssignElecStaffCompleteDelete();
                $revertComment = $_POST['TaskAssignElecStaffCompleteDelete']['revert_comment'];

                // Copy the details first
                if (!$taskAssignCompleteDelete->copyTaskAssignCompleteDetail($taskAssignCompleteDetail, $revertComment)) {
                    throw new \Exception("Failed to copy task assign complete details.");
                } else {
                    // Delete all details
                    $allDeleted = true;
                    foreach ($taskAssignCompleteDetail as $detail) {
                        if (!$detail->delete()) {
                            $allDeleted = false;
                            break;
                        }
                    }

                    if (!$allDeleted) {
                        throw new \Exception("Failed to delete some task assign complete details.");
                    }

                    if (!$taskAssign->updateCompleteQtyAfterRevert($taskAssignElecStaff, false)) {
                        throw new \Exception("Failed to update complete quantities.");
                    }
                }

                $transaction->commit();
                FlashHandler::success("Updated successfully.");
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("Revert process failed: " . $e->getMessage());
            }

            return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_elec_task_id]);
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR";
        }

        return $this->renderAjax('_ajaxFormRevertSetComplete', [
                    'model' => $model
        ]);
    }

    /**
     * Update Task Assigned To Staffs
     */
//    public function actionUpdateAssignTask($taskAssignId, $toIndex = "") {
//        $model = TaskAssignElec::findOne($taskAssignId);
//        $task = $model->prodElecTask;
//        if ($model->load(Yii::$app->request->post())) {
//            $model->staffIds = Yii::$app->request->post('selectStaff');
////            if ($model->processAndSave()) {
//            if ($model->processAndSave() && $model->updateCompleteQtyInAssignment() && $model->updateTaskCalculation() && $model->updatePanelCalculation()) {
//                $this->taskAssignmentCorrection($model->prod_elec_task_id);
//                FlashHandler::success("Task update success.");
//                if ($toIndex == "inProgress") {
//                    return $this->redirect(['index-elec-in-progress']);
//                } else if ($toIndex == "all") {
//                    return $this->redirect(['index-elec-all']);
//                } else {
//                    return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
//                }
//            } else {
//                FlashHandler::err("Task assign failed.");
//            }
//        }
//
////        $staffList = User::getActiveStaffList(); // To be edited, filter by department
//        $staffList = User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
//                ->join("INNER JOIN", "v_worker_task_categories_elec", "v_worker_task_categories_elec.user_id=user.id")
//                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
//                ->where(['task_code' => $task->elec_task_code])
//                ->asArray()
//                ->all();
//
//        return $this->render('updateAssignTask', [
//                    'task' => $task,
//                    'model' => $model,
//                    'staffList' => $staffList
//        ]);
//    }

    public function actionUpdateAssignTask($taskAssignId, $toIndex = "") {
        $model = TaskAssignElec::findOne($taskAssignId);

        if (!$model) {
            throw new NotFoundHttpException('Task assignment not found');
        }

        $task = $model->prodElecTask;

        if ($model->load(Yii::$app->request->post())) {
            $model->staffIds = Yii::$app->request->post('selectStaff');

            // Process each step and collect errors
            $errors = [];

            if (!$model->processAndSave()) {
                $errors[] = 'Failed to process and save task';
            }

            if (!$model->updateCompleteQtyInAssignment()) {
                $errors[] = 'Failed to update complete quantity';
            }

            if (!$model->updateTaskCalculation()) {
                $errors[] = 'Failed to update task calculation';
            }

            if (!$model->updatePanelCalculation()) {
                $errors[] = 'Failed to update panel calculation';
            }

            if (!$model->updatePanelDefectStaff()) {
                $errors[] = 'Failed to update panel defect staff';
            }

            if (empty($errors)) {
                $this->taskAssignmentCorrection($model->prod_elec_task_id);
                FlashHandler::success("Task update success.");

                if ($toIndex == "inProgress") {
                    return $this->redirect(['index-elec-in-progress']);
                } else if ($toIndex == "all") {
                    return $this->redirect(['index-elec-all']);
                } else {
                    return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
                }
            } else {
                // Show specific errors
                $errorMessage = implode(', ', $errors);
                FlashHandler::err("Task assign failed: " . $errorMessage);
                Yii::error("Task update failed for task $taskAssignId: " . $errorMessage);
            }
        }

        // Get staff list
        $staffList = User::find()
                ->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
                ->join("INNER JOIN", "v_worker_task_categories_elec", "v_worker_task_categories_elec.user_id=user.id")
                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
                ->where(['task_code' => $task->elec_task_code])
                ->asArray()
                ->all();

        return $this->render('updateAssignTask', [
                    'task' => $task,
                    'model' => $model,
                    'staffList' => $staffList
        ]);
    }

    /**
     * Deactivate Task Assigned To Staffs
     */
    public function actionDeactivateAssignTask($taskAssignId, $toIndex = "") {
        $model = TaskAssignElec::findOne($taskAssignId);
        if ($model && Yii::$app->request->isPost) {
            if ($model->deactivateTask()) {
                FlashHandler::success("Task deactivated.");
            } else {
                FlashHandler::err("Task deactivation failed.");
            }
            if ($toIndex == "inProgress") {
                return $this->redirect(['index-elec-in-progress']);
            } else if ($toIndex == "all") {
                return $this->redirect(['index-elec-all']);
            } else {
                return $this->redirect(['view-assigned-task', 'taskId' => $model->prodElecTask->id]);
            }
        } else {
            FlashHandler::err("ERROR!");
            return $this->redirect(['/']);
        }
    }

    /**
     * Index - Showing In Progress List
     * @return type
     */
    public function actionIndexElecInProgress($date = null) {
        $searchModel = new TaskAssignElecSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexElecInProgress', null, $date);
        return $this->render('indexElecInProgress', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexElecAll() {
        $searchModel = new TaskAssignElecSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexElecAll');
        return $this->render('indexElecAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateTaskWeightMultiplePanels($projectId) {
        $panelIds = Yii::$app->request->post('panelId');

        if (empty($panelIds)) {
            Yii::$app->session->setFlash('error', 'No panels selected.');
            return $this->redirect(['index-elec-project-panels', 'id' => $projectId]);
        }

        $project = ProjectProductionMaster::findOne($projectId);
        if (!$project) {
            throw new NotFoundHttpException('Project not found.');
        }

        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $prodElecTasks = ProdElecTaskWeight::find()
                ->where(['proj_prod_panel_id' => $panelIds])
                ->all();

        return $this->render('_editTaskWeightMultiplePanelElec', [
                    'project' => $project,
                    'refElecTask' => $refElecTask,
                    'prodElecTasks' => $prodElecTasks,
                    'panelIds' => $panelIds,
        ]);
    }

//    public function actionSaveTaskWeightMultiplePanels($projectId) {
//        $data = Yii::$app->request->post('TaskWeight');
//
//        if (empty($data)) {
//            Yii::$app->session->setFlash('error', 'No data to save.');
//            return $this->redirect(['index-elec-project-panels', 'id' => $projectId]);
//        }
//
//        $transaction = Yii::$app->db->beginTransaction();
//
//        try {
//            foreach ($data as $panelId => $taskWeights) {
//                $recordId = $taskWeights['record_id'] ?? null;
//
//                unset($taskWeights['record_id']);
//
//                $totalWeight = 0;
//                foreach ($taskWeights as $taskCode => $weight) {
//                    $totalWeight += (float) $weight;
//                }
//
//                // Validate total weight
//                if ($totalWeight > 100) {
//                    throw new \Exception("Total weight for panel ID {$panelId} exceeds 100% ({$totalWeight}%)");
//                }
//
//                // Find or create record
//                if ($recordId) {
//                    $model = ProdElecTaskWeight::findOne($recordId);
//                } else {
//                    $model = ProdElecTaskWeight::findOne(['proj_prod_panel_id' => $panelId]);
//                    if (!$model) {
//                        $model = new ProdElecTaskWeight();
//                        $model->proj_prod_panel_id = $panelId;
//                    }
//                }
//
//                if ($model) {
//                    // Update all task weight columns
//                    foreach ($taskWeights as $taskCode => $weight) {
//                        $model->{$taskCode} = (float) $weight;
//                    }
//
//                    // Update timestamps
//                    $model->updated_at = date('Y-m-d H:i:s');
//                    $model->updated_by = Yii::$app->user->id;
//
//                    if ($model->isNewRecord) {
//                        $model->created_at = date('Y-m-d H:i:s');
//                        $model->created_by = Yii::$app->user->id;
//                    }
//
//                    if (!$model->save()) {
//                        throw new \Exception('Failed to save task weights: ' . json_encode($model->errors));
//                    }
//                }
//            }
//
//            $transaction->commit();
//            Yii::$app->session->setFlash('success', 'Task weights updated successfully.');
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
//        }
//
//        return $this->redirect(['index-elec-project-panels', 'id' => $projectId]);
//    }

    public function actionSaveTaskWeightMultiplePanels($projectId) {
        $weights = Yii::$app->request->post('TaskWeight');
        $selectedPanels = Yii::$app->request->post('SelectedPanels');

        if (empty($weights) || empty($selectedPanels)) {
            Yii::$app->session->setFlash('error', 'No data or panels selected.');
            return $this->redirect(['index-elec-project-panels', 'id' => $projectId]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Validate total
            $totalWeight = array_sum(array_map('floatval', $weights));
            if ($totalWeight > 100) {
                throw new \Exception("Total weight exceeds 100% ({$totalWeight}%)");
            }

            foreach ($selectedPanels as $panelId) {
                $model = ProdElecTaskWeight::findOne(['proj_prod_panel_id' => $panelId]);
                if (!$model) {
                    $model = new ProdElecTaskWeight();
                    $model->proj_prod_panel_id = $panelId;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->created_by = Yii::$app->user->id;
                }

                foreach ($weights as $taskCode => $value) {
                    $model->{$taskCode} = (float) $value;
                }

                $model->updated_at = date('Y-m-d H:i:s');
                $model->updated_by = Yii::$app->user->id;

                if (!$model->save()) {
                    throw new \Exception('Failed to save weights for panel ID ' . $panelId);
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Task weights updated successfully for selected panels.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }

        return $this->redirect(['index-elec-project-panels', 'id' => $projectId]);
    }

    /**
     * A hidden function use to update all the status, percentage of production master and panels
     */
    public function actionOverallUpdate() {
        ini_set('pcre.backtrack_limit', '100000000'); // 100M
        ini_set('pcre.recursion_limit', '10000000');  // 10M
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $query = TaskAssignElec::find()->where(['active_sts' => 1]);
        foreach ($query->batch(50) as $taskAssignBatch) {
            foreach ($taskAssignBatch as $taskAssign) {
                $taskAssign->updateTaskCalculation();
                $taskAssign->updatePanelCalculation();

                // Free memory after each iteration
                unset($taskAssign);
            }
            Yii::$app->db->close(); // close & reopen DB to prevent connection cache bloat
            Yii::$app->db->open();
            gc_collect_cycles(); // force garbage collection
        }
        return "DONE";
    }

    public function actionTempAddBusbar() {

// Step 1: Select panel IDs that don't have a 'busbar' task
        $sql = "SELECT a.id, a.quantity
        FROM project_production_panels a
        LEFT JOIN production_elec_tasks b ON a.id = b.proj_prod_panel_id AND b.elec_task_code = 'busbar'
        WHERE b.id IS NULL";
        $panels = Yii::$app->db->createCommand($sql)->queryAll();

// Step 2: Insert new records into production_elec_tasks
        foreach ($panels as $panel) {
            $newTask = new ProductionElecTasks();
            $newTask->proj_prod_panel_id = $panel['id'];
            $newTask->elec_task_code = 'busbar';
            $newTask->qty_total = $panel['quantity'];
            // Set other necessary fields if required
            // e.g., $newTask->created_at = date('Y-m-d H:i:s');
            // e.g., $newTask->created_by = Yii::$app->user->id;

            if (!$newTask->save()) {
                // Handle error
                Yii::error("Error saving new task for panel ID: " . $panel['id']);
            }
        }
    }
}
