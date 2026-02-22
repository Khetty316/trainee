<?php

namespace frontend\controllers;

use Yii;
use frontend\models\workassignment\WorkAssignmentMasterSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\ProjectProduction\ProjectProductionMasterSearch;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabSearch;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use frontend\models\projectproduction\fabrication\TaskAssignFab;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\task\TaskAssignment;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use common\modules\auth\models\AuthItem;
use frontend\models\projectproduction\task\TaskAssignOngoingSummary;
use frontend\models\projectproduction\fabrication\ProductionFabTasksError;
use frontend\models\projectproduction\fabrication\ProductionFabTasksErrorSearch;
use frontend\models\projectproduction\fabrication\ProdFabTaskWeight;
use frontend\models\projectproduction\fabrication\TaskAssignFabStaffComplete;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabStaffCompleteDelete;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabStaff;
use frontend\models\projectproduction\fabrication\ProductionFabTasksErrorStaff;

/**
 * WorkAssignmentController implements the CRUD actions for WorkAssignmentMaster model.
 */
class FabTaskController extends Controller {

//    CONST mainViewPath = "/workassignment/fab/";
    public function getViewPath() {
        return Yii::getAlias('@frontend/views/workassignment/fab/');
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
                            'index-fab-project-list',
                            'index-fab-in-progress',
                            'index-fab-all',
                            'index-fab-project-panels',
                            'view-assigned-task',
                            'ajax-make-complaint',
                            'ajax-action-set-complete',
                            'delete-complaint',
                            'delete-fab-task',
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
                        'roles' => [AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin]
                    ],
                    [
                        'actions' => ['overall-update', 'migrate-fab-error-staff'],
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
    public function actionIndexFabProjectList() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('indexFabProjectList', [
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
    public function actionIndexFabProjectPanels($id) {
        $model = ProjectProductionMaster::findOne($id);

        return $this->render('indexFabProjectPanels', [
                    'model' => $model,
        ]);
    }

    /**
     * by Khetty, 21/5/2024
     * Updates task weight
     */
    public function actionUpdateTaskWeight($id) {
        $model = new ProductionFabTasks();
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $fabTaskWeight = $model->getFabTaskValue($id);
        $panel = ProjectProductionPanels::findOne($id);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $fabTaskWeightModel = new ProdFabTaskWeight();
            $updateFabTaskWeight = $fabTaskWeightModel->updateFabTaskWeight($postData);
            if (!$updateFabTaskWeight) {
                FlashHandler::err("Update Failed.");
            }

            FlashHandler::success("Update task weight success.");
            return $this->redirect(['index-fab-project-panels', 'id' => $panel->proj_prod_master]);
        }

        return $this->renderAjax('updateTaskWeight', [
                    'refFabTask' => $refFabTask,
                    'fabTaskWeight' => $fabTaskWeight,
                    'panel' => $panel
        ]);
    }

    /*
     * by Khetty, 8/1/2024
     * ****** Delete Fab Task and update the task weight to null ******
     */

    public function actionDeleteFabTask($taskId, $id) {
        $fabTask = ProductionFabTasks::findOne($taskId);
        if ($fabTask && $fabTask->delete()) {
            FlashHandler::success("Task deleted.");
            $fabTaskWeight = new ProdFabTaskWeight();
            $fabTaskWeight->updateFabTaskWeightAfterDeleteTask($fabTask->proj_prod_panel_id, $fabTask->fab_task_code);

            $fab = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $fabTask->proj_prod_panel_id])->all();
            $panel = ProjectProductionPanels::findOne($fabTask->proj_prod_panel_id);
            if (empty($fab)) {
                $panel->fab_work_status = null; //update work status to null if no task found
                $panel->save();
            } else {
                $project = ProjectProductionMaster::findOne($panel->proj_prod_master);
                $project->updateAvgFabProgressPercent();
//                $panel->updateFabProgressPercent();
                $panel->checkPanelFabWorkStatus();
            }
        } else {
            FlashHandler::err("Task delete failed.");
        }

        return $this->redirect(['index-fab-project-panels', 'id' => $id]);
    }

    /**
     * Assigning Task To Staffs
     */
    public function actionAssignTask($id) {
        $task = ProductionFabTasks::findOne($id);
        $model = new TaskAssignFab();
        $model->proj_prod_panel_id = $task->proj_prod_panel_id;
        $model->prod_fab_task_id = $task->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->staffIds = Yii::$app->request->post('selectStaff');

            $transaction = Yii::$app->db->beginTransaction();
            if ($model->processAndSave() && $model->updateTaskCalculation() && $model->updatePanelCalculation()) {
                $targetDateTrialModel = new \frontend\models\projectproduction\fabrication\TaskAssignFabTargetDateTrial();
                $targetDateTrialModel->task_assign_fab_id = $model->id;
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

//        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication); // To be edited, filter by department
        $staffList = User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
                ->join("INNER JOIN", "v_worker_task_categories_fab", "v_worker_task_categories_fab.user_id=user.id")
                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
                ->where(['task_code' => $task->fab_task_code, "status" => User::STATUS_ACTIVE])
                ->asArray()
                ->all();
//        
//         User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
//                ->join('INNER JOIN', 'auth_assignment', 'user.id=auth_assignment.user_id')
//                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
//                ->where("status=" . User::STATUS_ACTIVE);
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
//            $model->startDate = MyFormatter::fromDateRead_toDateSQL($model->startDate); \common\models\myTools\Mydebug::dumpFileW(Yii::$app->request->post());
//
//            $panel = ProjectProductionPanels::findOne($panelId);
//            $project = $panel->projProdMaster;
//            $formList = [];
//            $model->panelId = $panelId;
//            $model->projectId = $project->id;
//            $model->staffIdString = implode(",", $model->staffIds);
//            foreach ($model->taskCode as $key => $task) {
//                $productionFabTasks = ProductionFabTasks::findOne(['proj_prod_panel_id' => $panelId, 'fab_task_code' => $task]);
//                $taskAssign = new TaskAssignFab();
//                $taskAssign->proj_prod_panel_id = $model->panelId;
//                $taskAssign->prod_fab_task_id = $productionFabTasks->id;
//                $taskAssign->quantity = $productionFabTasks->qty_total - $productionFabTasks->qty_assigned;
//                $taskAssign->start_date = $model->startDate;
//                $taskAssign->comments = $model->comments;
//                $taskAssign->tempTaskName = RefProjProdTaskFab::findOne($task)->name;
//                $taskAssign->taskCode = $task;
//                $formList[] = $taskAssign;
//            }
//
////            $staffNameList = User::find()->where(["in", 'id', $model->staffIds])->all();
//            $staffNameList = \frontend\models\projectproduction\task\VWorkerTaskCategoriesFab::find()
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
//        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication); // To be edited, filter by department
//
//        $taskList = ProductionFabTasks::getPanelTasks($panelId);
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
                $productionFabTasks = ProductionFabTasks::findOne([
                    'proj_prod_panel_id' => $panelId,
                    'fab_task_code' => $taskCode
                ]);

                if (!$productionFabTasks) {
                    continue;
                }

                $taskAssign = new TaskAssignFab();
                $taskAssign->proj_prod_panel_id = $model->panelId;
                $taskAssign->prod_fab_task_id = $productionFabTasks->id;
                $taskAssign->quantity = $productionFabTasks->qty_total - $productionFabTasks->qty_assigned;
                $taskAssign->start_date = $model->startDate;
                $taskAssign->comments = $model->comments;
                $taskAssign->tempTaskName = RefProjProdTaskFab::findOne($taskCode)->name;
                $taskAssign->taskCode = $taskCode;

                // attach staff (only valid ones)
                $taskAssign->staffIds = $validStaffs[$taskCode] ?? [];

                $formList[] = $taskAssign;
            }

            $staffNameList = [];
            foreach ($validStaffs as $taskCode => $staffList) {
                if (!empty($staffList)) {
                    $rows = \frontend\models\projectproduction\task\VWorkerTaskCategoriesFab::find()
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
        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication);
        $taskList = ProductionFabTasks::getPanelTasks($panelId);

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
        $staffList = User::getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication); // To be edited, filter by department
        $taskList = RefProjProdTaskFab::getAllActiveSorted();
        $panels = ProjectProductionPanels::find()->where("id IN ($panelIds)")->all();
        return $this->render('assignTaskMultiplePanelFab', [
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
//            $staffNameList = \frontend\models\projectproduction\task\VWorkerTaskCategoriesFab::find()
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
//                    $productionFabTasks = ProductionFabTasks::findOne(['proj_prod_panel_id' => $panelId, 'fab_task_code' => $task]);
//                    if (empty($productionFabTasks)) {
//                        continue;
//                    }
//                    $taskAssign = new TaskAssignFab();
//                    $taskAssign->proj_prod_panel_id = $panelId;
//                    $taskAssign->panelCode = $panel->project_production_panel_code;
//                    $taskAssign->prod_fab_task_id = $productionFabTasks->id;
//                    $taskAssign->quantity = $productionFabTasks->qty_total - $productionFabTasks->qty_assigned;
//                    $taskAssign->start_date = $model->startDate;
//                    $taskAssign->comments = $model->comments;
//                    $taskAssign->tempTaskName = RefProjProdTaskFab::findOne($task)->name;
//                    $taskAssign->taskCode = $task;
//                    $formList[] = $taskAssign;
//                }
//            }
//
////            $staffNameList = User::find()->where(["in", 'id', $model->staffIds])->all();
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
                    $rows = \frontend\models\projectproduction\task\VWorkerTaskCategoriesFab::find()
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
                    $productionFabTasks = ProductionFabTasks::findOne([
                        'proj_prod_panel_id' => $panelId,
                        'fab_task_code' => $taskCode
                    ]);
                    if (!$productionFabTasks) {
                        continue;
                    }

                    $taskAssign = new TaskAssignFab();
                    $taskAssign->proj_prod_panel_id = $panelId;
                    $taskAssign->panelCode = $panel->project_production_panel_code;
                    $taskAssign->prod_fab_task_id = $productionFabTasks->id;
                    $taskAssign->quantity = $productionFabTasks->qty_total - $productionFabTasks->qty_assigned;
                    $taskAssign->start_date = $model->startDate;
                    $taskAssign->comments = $model->comments;
                    $taskAssign->tempTaskName = RefProjProdTaskFab::findOne($taskCode)->name;
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
            $fabTaskToDoChecking = null; // qty assigned in this table maybe not up to date

            if (!empty($postData['TaskAssignFab'])) {
                foreach ((array) $postData['TaskAssignFab'] as $i => $taskAssignment) {
                    $taskAssignFab = new TaskAssignFab();
                    $taskAssignFab->load($taskAssignment, ''); // Use empty string as second parameter to avoid setting the formName() of the model
                    $taskAssignFab->staffIds = explode(',', $taskAssignFab->staffIdString);
                    if ($taskAssignFab->quantity <= 0) {
                        
                    } else {
                        if (!$taskAssignFab->processAndSave(false) || !$taskAssignFab->updateTaskCalculation()) {
                            $fabTaskToDoChecking = $taskAssignFab->prod_fab_task_id;
                            $transaction->rollBack();
                        }
                    }

                    if ($i == sizeof($postData['TaskAssignFab']) - 1) {
                        $tempTaskAssign = $taskAssignFab;
                    }

                    if (!$taskAssignFab->updatePanelCalculation()) {
                        $transaction->rollBack();
                    }

                    if ($taskAssignFab->processAndSave(false) && $taskAssignFab->updateTaskCalculation() && $taskAssignFab->updatePanelCalculation()) {
                        $targetDateTrialModel = new \frontend\models\projectproduction\fabrication\TaskAssignFabTargetDateTrial();
                        $targetDateTrialModel->task_assign_fab_id = $taskAssignFab->id;
                        $targetDateTrialModel->target_date = $taskAssignFab->current_target_date;
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
                    $this->taskAssignmentCorrection($fabTaskToDoChecking);
                    FlashHandler::err("Failed to assign, please retry");
                }
                return $this->redirect(['/fab-task/index-fab-project-panels', 'id' => $taskAssignmentBean->projectId]);
            } else {
                FlashHandler::err("Failed to assign");
            }
            return $this->redirect(['/fab-task/index-fab-project-panels', 'id' => $taskAssignmentBean->projectId]);
        }
    }

    /**
     * If in the to do list, qty_assign is not updated, then do correction
     * @param type $taskToDoId
     * @return boolean
     */
    private function taskAssignmentCorrection($taskToDoId) {
        if (!empty($taskToDoId)) {
            $fabTaskTodo = ProductionFabTasks::findOne($taskToDoId);
            $assignedTasks = TaskAssignFab::findAll(["prod_fab_task_id" => $taskToDoId, "active_sts" => 1]);
            if (empty($assignedTasks)) {
                $fabTaskTodo->qty_assigned = 0;
                $fabTaskTodo->qty_completed = 0;
                $fabTaskTodo->update();
            } else {
                $fabTaskTodo->checkAndUpdateAssignCorrection();
                $assignedTasks[0]->updatePanelCalculation();
            }
        }
        return true;
    }

    /**
     * View assigned task status.
     */
    public function actionViewAssignedTask($taskId) {
        $searchModel = new TaskAssignFabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'viewAssignedTask', ['taskId' => $taskId]);

        $errorModel = new ProductionFabTasksErrorSearch();
        $errorData = $errorModel->search(Yii::$app->request->queryParams, 'singlePanel', ['taskId' => $taskId]);

        $task = ProductionFabTasks::findOne($taskId);
        return $this->render('viewAssignedTask', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'errorModel' => $errorModel,
                    'errorData' => $errorData,
                    'task' => $task,
        ]);
    }

    public function actionUpdateTargetDate($id, $taskId) {
        $model = TaskAssignFab::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post("TaskAssignFab");
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $targetDate = MyFormatter::fromDateRead_toDateSQL($postData["new_target_date"]);
                $targetDateTrialModel = new \frontend\models\projectproduction\fabrication\TaskAssignFabTargetDateTrial;
                $targetDateTrialModel->task_assign_fab_id = $model->id;
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
//        $model = $taskId ? new ProductionFabTasksError() : ProductionFabTasksError::findOne($complaintId);
//        $task = $model->isNewRecord ? ProductionFabTasks::findOne($taskId) : $model->productionFabTask;
//
//        $post = Yii::$app->request->post('ProductionFabTasksError');
//
//        if (Yii::$app->request->isPost) {
//            if ($model->isNewRecord) {
//                $model->production_fab_task_id = $taskId;
//            }
//            $model->remark = $post['remark'];
//            $model->error_code = $post['error_code'];
//            if ($model->validate() && $model->save()) {
//                FlashHandler::success('Complaint sent');
//                return $this->redirect(['view-assigned-task', 'taskId' => $taskId ?? $model->production_fab_task_id]);
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

        // Load model: new or existing complaint
        $model = $complaintId ? ProductionFabTasksError::findOne($complaintId) : new ProductionFabTasksError();
        if (!$model) {
            throw new NotFoundHttpException('Complaint not found');
        }

        // Load task
        $task = $model->isNewRecord ? ProductionFabTasks::findOne($taskId) : $model->productionFabTask;
        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->isNewRecord) {
                $model->production_fab_task_id = $taskId;
            }

            $isNewRecord = $model->isNewRecord;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $success = $model->save() && ($isNewRecord ? $model->saveProductionFabTasksErrorStaff() : $model->updateProductionFabTasksErrorStaff());

                if ($success) {
                    $transaction->commit();

                    if (Yii::$app->request->isAjax) {
                        return $this->asJson(['success' => true]);
                    }

                    return $this->redirect(['view-assigned-task', 'taskId' => $taskId ?? $model->production_fab_task_id]);
                } else {
                    $transaction->rollBack();

                    if (Yii::$app->request->isAjax) {
                        return $this->asJson([
                                    'success' => false,
                                    'errors' => $model->errors,
                                    'message' => 'Failed to save complaint'
                        ]);
                    }

                    Yii::$app->session->setFlash('error', 'Failed to save complaint');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->__toString());

                if (Yii::$app->request->isAjax) {
                    return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
                } else {
                    throw $e;
                }
            }
        }

        // Render form for AJAX
        return $this->renderAjax('_ajaxFormMakeComplaint', [
                    'model' => $model,
                    'task' => $task,
        ]);
    }

    public function actionMigrateFabErrorStaff() {
        $errors = ProductionFabTasksError::find()->all();

        foreach ($errors as $error) {
            $taskAssign = TaskAssignFab::findOne([
                'prod_fab_task_id' => $error->production_fab_task_id,
                'deactivated_by' => null,
                'deactivated_at' => null
            ]);

            if (!$taskAssign) {
                Yii::warning("No task assign found for fab task {$error->production_fab_task_id}");
                continue;
            }

            $taskAssignedStaffs = $taskAssign->taskAssignFabStaff;

            foreach ($taskAssignedStaffs as $staff) {

                $exists = ProductionFabTasksErrorStaff::find()
                        ->where([
                            'production_fab_tasks_error_id' => $error->id,
                            'staff_id' => $staff->user_id
                        ])
                        ->exists();

                if ($exists) {
                    continue;
                }

                $taskErrorStaff = new ProductionFabTasksErrorStaff();
                $taskErrorStaff->production_fab_tasks_error_id = $error->id;
                $taskErrorStaff->staff_id = $staff->user_id;
                $taskErrorStaff->is_read = 1;
                $taskErrorStaff->read_at = null;

                if (!$taskErrorStaff->save()) {
                    Yii::error([
                        'error_id' => $error->id,
                        'staff_id' => $staff->user_id,
                        'errors' => $taskErrorStaff->errors
                            ], 'FAB_MIGRATION_ERROR');
                }
            }
        }

        return 'FAB migration completed';
    }

    public function actionDeleteComplaint($id) {
        $model = ProductionFabTasksError::findOne($id);
        $task = $model->productionFabTask;

        if ($model->delete()) {
            FlashHandler::success('Complaint Deleted');
            return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
        }
    }

    /**
     * Update record, and set complete
     */
    public function actionAjaxActionSetComplete($id, $toIndex = "") {
        $taskAssign = TaskAssignFab::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($taskAssign->addCompletePanelFab(Yii::$app->request->post())) {
                FlashHandler::success("Updated.");
            } else {
                FlashHandler::err("Task assign failed.");
            }

            if ($toIndex == "inProgress") {
                return $this->redirect(['index-fab-in-progress']);
            } else if ($toIndex == "all") {
                return $this->redirect(['index-fab-all']);
            } else {
                return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_fab_task_id]);
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
//        $taskAssignCompleteDetail = TaskAssignFabComplete::findOne($id);
//        $taskAssign = TaskAssignFab::findOne($taskId);
//        $model = new TaskAssignFabCompleteDelete();
//        
//        if (Yii::$app->request->isPost) {
//            $taskAssignCompleteDelete = new TaskAssignFabCompleteDelete();
//            if (!$taskAssignCompleteDelete->copyTaskAssignCompleteDetail($taskAssignCompleteDetail, $_POST['TaskAssignFabCompleteDelete']['revert_comment']) || !$taskAssignCompleteDetail->delete() || !$taskAssign->updateCompleteQtyInAssignment()) {
//                FlashHandler::err("Revert completed panel amounts failed.");
//            } else {
////                if (!$taskAssign->addCompletePanelFab(Yii::$app->request->post())) {
//                if (!$taskAssign->updateTaskCalculation() || !$taskAssign->bulkUpdateThisUserTaskOnHand() || !$taskAssign->updateTaskAndPanelCalculation() || !$taskAssign->revertCompleteDate()) {
//                    FlashHandler::err("Revert process failed.");
//                } else {
//                    FlashHandler::success("Updated.");
//                }
//            }
//            return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_fab_task_id]);
//        }else if (!Yii::$app->request->isAjax) {
//            return "ERROR";
//        }
//
//        return $this->renderAjax('_ajaxFormRevertSetComplete', [
////                    'model' => $taskAssign,
////                    'limit' => $taskAssignCompleteDetail->quantity,
//                    'model' => $model
//        ]);
//    }

    public function actionAjaxActionRevertSetComplete($id, $taskId) {
        $taskAssignCompleteDetail = TaskAssignFabStaffComplete::findAll($id);
        $taskAssignFabStaff = TaskAssignFabStaff::findOne($taskId);
        $taskAssign = TaskAssignFab::findOne($taskAssignFabStaff->task_assign_fab_id);
        $model = new TaskAssignFabStaffCompleteDelete();

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $taskAssignCompleteDelete = new \frontend\models\projectproduction\fabrication\TaskAssignFabStaffCompleteDelete();
                $revertComment = $_POST['TaskAssignFabStaffCompleteDelete']['revert_comment'];

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

                    if (!$taskAssign->updateCompleteQtyAfterRevert($taskAssignFabStaff, false)) {
                        throw new \Exception("Failed to update complete quantities.");
                    }
                }

                $transaction->commit();
                FlashHandler::success("Updated successfully.");
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("Revert process failed: " . $e->getMessage());
            }

            return $this->redirect(['view-assigned-task', 'taskId' => $taskAssign->prod_fab_task_id]);
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
    //before alert complaint
//    public function actionUpdateAssignTask($taskAssignId, $toIndex = "") {
//        $model = TaskAssignFab::findOne($taskAssignId);
//        $task = $model->prodFabTask;
//        if ($model->load(Yii::$app->request->post())) {
//            $model->staffIds = Yii::$app->request->post('selectStaff');
////            if ($model->processAndSave()) {
//            if ($model->processAndSave() && $model->updateCompleteQtyInAssignment() && $model->updateTaskCalculation() && $model->updatePanelCalculation()) {
//                $this->taskAssignmentCorrection($model->prod_fab_task_id);
//                FlashHandler::success("Task update success.");
//                if ($toIndex == "inProgress") {
//                    return $this->redirect(['index-fab-in-progress']);
//                } else if ($toIndex == "all") {
//                    return $this->redirect(['index-fab-all']);
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
//                ->join("INNER JOIN", "v_worker_task_categories_fab", "v_worker_task_categories_fab.user_id=user.id")
//                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
//                ->where(['task_code' => $task->fab_task_code, "status" => User::STATUS_ACTIVE])
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
        $model = TaskAssignFab::findOne($taskAssignId);

        if (!$model) {
            throw new NotFoundHttpException('Task assignment not found');
        }

        $task = $model->prodFabTask;

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
                $this->taskAssignmentCorrection($model->prod_fab_task_id);
                FlashHandler::success("Task update success.");

                if ($toIndex == "inProgress") {
                    return $this->redirect(['index-fab-in-progress']);
                } else if ($toIndex == "all") {
                    return $this->redirect(['index-fab-all']);
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
        $staffList = User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
                ->join("INNER JOIN", "v_worker_task_categories_fab", "v_worker_task_categories_fab.user_id=user.id")
                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
                ->where(['task_code' => $task->fab_task_code, "status" => User::STATUS_ACTIVE])
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
        $model = TaskAssignFab::findOne($taskAssignId);

        if ($model && Yii::$app->request->isPost) {
            if ($model->deactivateTask()) {
                FlashHandler::success("Task deactivated.");
            } else {
                FlashHandler::err("Task deactivation failed.");
            }

            if ($toIndex == "inProgress") {
                return $this->redirect(['index-fab-in-progress']);
            } else if ($toIndex == "all") {
                return $this->redirect(['index-fab-all']);
            } else {
                return $this->redirect(['view-assigned-task', 'taskId' => $model->prodFabTask->id]);
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
    public function actionIndexFabInProgress($date = null) {
        $searchModel = new TaskAssignFabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexFabInProgress', null, $date);
        return $this->render('indexFabInProgress', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexFabAll() {
        $searchModel = new TaskAssignFabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexFabAll');
        return $this->render('indexFabAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateTaskWeightMultiplePanels($projectId) {
        $panelIds = Yii::$app->request->post('panelId');

        if (empty($panelIds)) {
            Yii::$app->session->setFlash('error', 'No panels selected.');
            return $this->redirect(['index-fab-project-panels', 'id' => $projectId]);
        }

        $project = ProjectProductionMaster::findOne($projectId);
        if (!$project) {
            throw new NotFoundHttpException('Project not found.');
        }

        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $prodFabTasks = ProdFabTaskWeight::find()
                ->where(['proj_prod_panel_id' => $panelIds])
                ->all();

        return $this->render('_editTaskWeightMultiplePanelFab', [
                    'project' => $project,
                    'refFabTask' => $refFabTask,
                    'prodFabTasks' => $prodFabTasks,
                    'panelIds' => $panelIds,
        ]);
    }

//    public function actionSaveTaskWeightMultiplePanels($projectId) {
//        $data = Yii::$app->request->post('TaskWeight');
//
//        if (empty($data)) {
//            Yii::$app->session->setFlash('error', 'No data to save.');
//            return $this->redirect(['index-fab-project-panels', 'id' => $projectId]);
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
//                    $model = ProdFabTaskWeight::findOne($recordId);
//                } else {
//                    $model = ProdFabTaskWeight::findOne(['proj_prod_panel_id' => $panelId]);
//                    if (!$model) {
//                        $model = new ProdFabTaskWeight();
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
//        return $this->redirect(['index-fab-project-panels', 'id' => $projectId]);
//    }

    public function actionSaveTaskWeightMultiplePanels($projectId) {
        $weights = Yii::$app->request->post('TaskWeight');
        $selectedPanels = Yii::$app->request->post('SelectedPanels');

        if (empty($weights) || empty($selectedPanels)) {
            Yii::$app->session->setFlash('error', 'No data or panels selected.');
            return $this->redirect(['index-fab-project-panels', 'id' => $projectId]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Validate total
            $totalWeight = array_sum(array_map('floatval', $weights));
            if ($totalWeight > 100) {
                throw new \Exception("Total weight exceeds 100% ({$totalWeight}%)");
            }

            foreach ($selectedPanels as $panelId) {
                $model = ProdFabTaskWeight::findOne(['proj_prod_panel_id' => $panelId]);
                if (!$model) {
                    $model = new ProdFabTaskWeight();
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

        return $this->redirect(['index-fab-project-panels', 'id' => $projectId]);
    }

    /**
     * A hidden function use to update all the status, percentage of production master and panels
     */
    public function actionOverallUpdate() {
        ini_set('pcre.backtrack_limit', '100000000'); // 100M
        ini_set('pcre.recursion_limit', '10000000');  // 10M
        ini_set('memory_limit', '4096M');
        set_time_limit(0);

        $query = TaskAssignFab::find()->where(['active_sts' => 1]);
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
}
