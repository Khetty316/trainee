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
use frontend\models\ProjectProduction\fabrication\TaskAssignFab;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\task\TaskAssignment;

/**
 * WorkAssignmentController implements the CRUD actions for WorkAssignmentMaster model.
 */
class TaskAssignmentController extends Controller {

//    CONST mainViewPath = "/workassignment/fab/";
    public function getViewPath() {
        return Yii::getAlias('@frontend/views/workassignment/');
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
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Assigning Task To Staffs - A better interface for Supervisor to assign
     */
    public function actionAssignTask($panelId, $taskType) {
        $task = ProductionFabTasks::findOne($panelId);
        $panel = ProjectProductionPanels::findOne($panelId);
        $project = $panel->projProdMaster;
        $model = new TaskAssignment();
//        $model = new TaskAssignFab();
//        $model->proj_prod_panel_id = $task->proj_prod_panel_id;
//        $model->prod_fab_task_id = $task->id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Task assign success.");
                return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
            } else {
                FlashHandler::err("Task assign failed.");
            }
        } else {
            $model->projectId = $project->id;
            $model->panelId = $panelId;
        }

        $staffList = \common\models\User::getStaffList_productionAssignee($taskType); // To be edited, filter by department

        return $this->render('assignTask', [
                    'panel' => $panel,
                    'project' => $project,
                    'taskType' => $taskType,
                    'model' => $model,
                    'staffList' => $staffList
        ]);
    }

    /**
     * View assigned task status.
     */
    public function actionViewAssignedTask($taskId) {
        $searchModel = new TaskAssignFabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'viewAssignedTask', ['taskId' => $taskId]);

        $task = ProductionFabTasks::findOne($taskId);
        return $this->render('viewAssignedTask', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'task' => $task
        ]);
    }

    /**
     * Update record, and set complete
     */
    public function actionAjaxActionSetComplete($id, $toIndex = "") {
        $taskAssign = TaskAssignFab::findOne($id);

        if ($taskAssign->load(Yii::$app->request->post())) {
            if ($taskAssign->updateCompleteDate()) {
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
        ]);
    }

    /**
     * Update Task Assigned To Staffs
     */
    public function actionUpdateAssignTask($taskAssignId, $toIndex = "") {
        $model = TaskAssignFab::findOne($taskAssignId);
        $task = $model->prodFabTask;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Task update success.");
                if ($toIndex == "inProgress") {
                    return $this->redirect(['index-fab-in-progress']);
                } else if ($toIndex == "all") {
                    return $this->redirect(['index-fab-all']);
                } else {
                    return $this->redirect(['view-assigned-task', 'taskId' => $task->id]);
                }
            } else {
                FlashHandler::err("Task assign failed.");
            }
        }

        $staffList = \common\models\User::getActiveStaffList(); // To be edited, filter by department

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
    public function actionIndexFabInProgress() {
        $searchModel = new TaskAssignFabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexFabInProgress');
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

    //////////////////////////////////////////////////////////////////////////////// OLD CODES ///////////////////////////////////////////////

    /**
     * Index - Showing ALL work assignment
     * @return type
     */
    /*
      public function actionIndexAll() {
      $searchModel = new WorkAssignmentMasterSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexWorkAssignmentAll');
      return $this->render(self::mainViewPath . 'indexWorkAssignmentAll', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      ]);
      }
     */
    /**
     * Index - Showing Completed work assignment
     * @return type
     */
    /*  public function actionIndexComplete() {
      $searchModel = new WorkAssignmentMasterSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexWorkAssignmentComplete');
      return $this->render(self::mainViewPath . 'indexWorkAssignmentComplete', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      ]);
      }
     */
}
