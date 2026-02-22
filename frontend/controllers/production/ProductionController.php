<?php

namespace frontend\controllers\production;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\ProjectProduction\ProjectProductionMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\ProjectProductionPanelItems;
use frontend\models\ProjectProduction\RefProjectItemUnit;
use frontend\models\projectquotation\ProjectQTypes;
use frontend\models\projectquotation\ProjectQRevisions;
use frontend\models\projectquotation\ProjectQPanels;
use common\models\myTools\MyFormatter;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyCommonFunction;
use frontend\models\projectproduction\ProjectProductionDocuments;
use frontend\models\projectproduction\ProjectProductionPanelsDelete;
use frontend\models\projectproduction\VProjectProductionPanels;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\projectproduction\electrical\RefProjProdTaskElec;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;
use frontend\models\projectproduction\task\WorkerTaskCategories;
use frontend\models\projectproduction\fabrication\TaskAssignFab;
use frontend\models\projectproduction\electrical\ProdElecTaskWeight;
use frontend\models\projectproduction\fabrication\ProdFabTaskWeight;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabStaff;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabStaffComplete;
use frontend\models\projectproduction\fabrication\TaskAssignFabStaffCompleteDelete;

/**
 * ProductionController implements the CRUD actions for ProjectProductionMaster model.
 */
class ProductionController extends Controller {

    CONST mainViewPath = "/projectproduction/main/";
    CONST panelItemViewPath = "/projectproduction/panelitem/";
    CONST taskWeightViewPath = "/projectproduction/taskweight/";

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
//                        'actions' => ['view-production-main'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_SystemAdmin, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator],
                    ],
                    [
                        'actions' => ['get-panel-file-by-panel-id', 'get-file-by-id'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['delete-production-main', 'index-production-project-list', 'view-production-project-panels', 'update-task-weight'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Director]
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

    public function actionUpdateProjectTargetDate($id) {
        $model = ProjectProductionMaster::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $targetDate = MyFormatter::fromDateRead_toDateSQL($model->new_target_date);
                $targetDateTrialModel = new \frontend\models\ProjectProduction\ProjProdTargetDateTrial();
                $targetDateTrialModel->proj_prod_master_id = $model->id;
                $targetDateTrialModel->target_date = $targetDate;
                $targetDateTrialModel->remark = $model->remark_update_target_date;

                if (!$targetDateTrialModel->save()) {
                    throw new \Exception("Failed to save target date trial.");
                }

                $model->current_target_date = $targetDateTrialModel->target_date;
                if (!$model->save()) {
                    throw new \Exception("Failed to update project target date.");
                }

                $transaction->commit();
                FlashHandler::success("Successfully updated the project target completion date!");
            } catch (Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("Failed. Please try again.");
            }

            return $this->redirect(['view-production-main', 'id' => $model->id]);
        }
        return $this->renderAjax($this::mainViewPath . '_updateTargetDate', [
                    'model' => $model,
        ]);
    }

    public function actionIndexProductionMain($type = null) {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render($this::mainViewPath . 'indexProductionMain', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * by Khetty 
     * 2/11/2023
     */

    public function actionViewProductionMain($id) {
        $model = ProjectProductionMaster::findOne($id);
        $state = true;

// Query the VProjectProductionPanels table to retrieve data.
        $query = VProjectProductionPanels::find()->where(['proj_prod_master' => $id]);
        $result = $query->all();
        foreach ($result as $row) {
            if ($row->finalized_by) {
                $state = false;
                break;
            } else {
                
            }
        }
        return $this->render($this::mainViewPath . 'viewProductionMain', [
                    'model' => $model,
                    'state' => $state,
                    'panelLists' => $result,
        ]);
    }

    /**
     * @param type $id
     * @return type Return by Ajax, just to show project's detail
     */
    public function actionAjaxViewProjectDetail($id) {
        return $this->renderAjax($this::mainViewPath . '_detailviewProjectProduction', [
                    'model' => $this->findModel($id),
        ]);
    }

    /** Initiate Project from Quotation
     * by Khetty
     * 6/11/2023
     * @return type
     */
    public function actionCreateProductionMain($id) {

        $model = new ProjectProductionMaster();
        $projType = ProjectQTypes::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {

            //save target date
            $targetDate = Yii::$app->request->post("ProjectProductionMaster")["current_target_date"];
            $targetDateTrialModel = new \frontend\models\ProjectProduction\ProjProdTargetDateTrial();
            $targetDateTrialModel->proj_prod_master_id = $model->id;
            $targetDateTrialModel->target_date = $targetDate;
            $targetDateTrialModel->remark = \frontend\models\ProjectProduction\ProjProdTargetDateTrial::REMARK_INITIAL_TARGET_DATE;
            if ($targetDateTrialModel->save()) {
                $model->current_target_date = $targetDateTrialModel->target_date;
                $model->update();
            }

            // If saved fail, most probably due to the revision id duplicated.
            if ($model->isNewRecord) {
                $model = ProjectProductionMaster::findOne(['revision_id' => $projType->active_revision_id]);
            }

            $panelIds = Yii::$app->request->post('finalizeBox');
            if ($panelIds) {
                $model->createProductionPanels($panelIds);
            }

            $projType->proj_prod_id = $model->id;
            $projType->update();

            FlashHandler::success("Project created.");
            return $this->redirect(['view-production-main', 'id' => $model->id]);
        }

        if (!empty($projType) && $model->checkAndPopulateProject($projType)) {
            return $this->render($this::mainViewPath . 'createProductionMain', [
                        'model' => $model,
            ]);
        }

        return $this->redirect("/production/production/index-production-main");
    }

    /**
     * by Khetty 13/11/2023
     * Load Repush Production Main page
     * @param int $id the project type ID
     * @return string the rendered view
     */
    public function actionRepushProductionMain($id) {

        $projType = ProjectQTypes::findOne($id);

        $panelIds = ProjectProductionPanels::find()
                ->select('panel_id')
                ->column();

        $masterId = ProjectProductionMaster::find()
                ->select('id')
                ->where(['revision_id' => ProjectQRevisions::find()->select('id')->where(['project_q_type_id' => $id])])
                ->scalar();

        if (empty($projType->proj_prod_id)) {
            $projType->proj_prod_id = $masterId;
            $projType->update();
        }
        // Load existing model if updating
        $model = ProjectProductionMaster::findOne($masterId);

        if (!empty($projType) && $model->checkAndPopulateProject($projType)) {
            return $this->render($this::mainViewPath . 'rePushProductionMain', [
                        'model' => $model,
                        'panelIds' => $panelIds,
            ]);
        }
    }

    /**
     * by Khetty 14/11/2023
     * ****** Repush Production ******
     * @param int $id the project production master ID
     * @return string the rendered view
     */
    public function actionRepushProduction($id) {
        $model = ProjectProductionMaster::findOne($id);
        $panelIds = Yii::$app->request->post('finalizeBox');

        if ($panelIds) {
            $model->createProductionPanels($panelIds);
            $model->updateAvgFabProgressPercent();
        }
        FlashHandler::success("Success.");
        return $this->redirect(['view-production-main', 'id' => $model->id]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-production-main', 'id' => $model->id]);
        }

        return $this->render($this::mainViewPath . 'updateProductionMain', [
                    'model' => $model,
        ]);
    }

    public function actionDeleteProductionMain($id) {
        $model = $this->findModel($id);
        $projTypes = ProjectQTypes::findAll(['proj_prod_id' => $id]);

        \frontend\models\ProjectProduction\ProjProdTargetDateTrial::deleteAll(['proj_prod_master_id' => $model->id]);
        $return = $model->delete();

        if ($return) {
            foreach ($projTypes as $projType) {
                $projType->proj_prod_id = null;
                $projType->update(false);
            }
            FlashHandler::success("Project retracted.");
        } else {
            FlashHandler::err("Unable to retract as already pushed to task.");
        }

        return $this->redirect('index-production-main');
    }

    protected function findModel($id) {
        if (($model = ProjectProductionMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Add Panel in Project Production. Temporarily stop
     * @param type $projProdMasterId
     * @return type
     */
    public function actionAjaxAddPanels($projProdMasterId) {
        $model = new ProjectProductionPanels();

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            FlashHandler::success("Panel added");
            return $this->redirect(['view-production-main', 'id' => $model->proj_prod_master]);
        }
        $model->proj_prod_master = $projProdMasterId;
        return $this->renderAjax($this::mainViewPath . '__ajaxFormAddPanel', [
                    'model' => $model,
                    'title' => 'New Panel',
                    'btnText' => 'Save'
        ]);
    }

    public function actionAjaxEditPanels($panelId) {
        $model = ProjectProductionPanels::findOne($panelId);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Panel updated");
            } else {
                FlashHandler::err("Unable to edit");
            }
            return $this->redirect(['view-production-main', 'id' => $model->proj_prod_master]);
        }

        return $this->renderAjax($this::mainViewPath . '__ajaxFormAddPanel', [
                    'model' => $model,
                    'title' => 'Update Panel',
                    'btnText' => 'Update'
        ]);
    }

    public function actionRemoveProjectPanel($panelId) {
        $model = ProjectProductionPanels::findOne($panelId);
        try {
            $model->delete();
            FlashHandler::success("Panel removed");
        } catch (\Exception $e) {
            FlashHandler::err("Unable to remove. The panel already in proccess");
        }
        return $this->redirect(['view-production-main', 'id' => $model->proj_prod_master]);
    }

    public function actionIndexProductionProjectList() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render($this::taskWeightViewPath . 'indexProductionProjectList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewProductionProjectPanels($id) {
        $model = ProjectProductionMaster::findOne($id);

        return $this->render($this::taskWeightViewPath . 'viewProductionProjectPanels', [
                    'model' => $model,
        ]);
    }

    public function actionUpdateTaskWeight($id) {
        $panel = ProjectProductionPanels::findOne($id);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $fabTaskWeightModel = new ProdFabTaskWeight();
            $updateFabTaskWeight = $fabTaskWeightModel->updateFabTaskWeight($postData);
            $elecTaskWeightModel = new ProdElecTaskWeight();
            $updateElecTaskWeight = $elecTaskWeightModel->updateElecTaskWeight($postData);
            if (!$updateFabTaskWeight || !$updateElecTaskWeight) {
                FlashHandler::err("Update Failed.");
            }

            FlashHandler::success("Update task weight success.");
            return $this->redirect(['view-production-project-panels', 'id' => $panel->proj_prod_master]);
        }

        return $this->renderAjax($this::taskWeightViewPath . 'updateTaskWeight', [
                    'panel' => $panel
        ]);
    }

    /**
     * ********* FINALIZE PANEL
     * ** Need to create task link automatically
     * @param type $id
     * @return type
     */
//    public function actionFinalizePanel($id) {
//        $panelIds = Yii::$app->request->post('finalizeBox');
//        foreach ((array) $panelIds as $panelId) {
//            $panel = ProjectProductionPanels::findOne($panelId);
//            $panel->finalizePanelDetail();
//            $panel->checkAndGetFabTask(); // Generate fabrication tasks
//            $panel->checkPanelFabWorkStatus(); // Update panel status
//            $panel->checkAndGetElecTask();
//            $panel->checkPanelElecWorkStatus();
//        }
//        FlashHandler::success("Panel(s) finalized.");
//        return $this->redirect(['view-production-main', 'id' => $id]);
//    }

    /*
     * updated by Khetty, 21/2/2024
     * ****** Finalize panel and get the Task Lists ******
     */
    public function actionFinalizePanel($id) {
        $model = ProjectProductionMaster::findOne($id);
        $panelIds = Yii::$app->request->post('finalizeBox');
        $panels = ProjectProductionPanels::find()->where(['id' => $panelIds, 'proj_prod_master' => $id])->all();
        $prodFabTasks = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $panelIds])->all();
        $prodElecTasks = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $panelIds])->all();
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $finalize = true;
        return $this->render($this::mainViewPath . 'taskList', [
                    'model' => $model,
                    'panels' => $panels,
                    'prodFabTasks' => $prodFabTasks,
                    'prodElecTasks' => $prodElecTasks,
                    'refFabTask' => $refFabTask,
                    'refElecTask' => $refElecTask,
                    'finalize' => $finalize
        ]);
    }

    /**
     * Created by Paul @ 24/01/2025
     * Allow to revert the finalization of Panels in Production, if the task is not assigned yet
     */
    public function actionRevertFinalize($panelId) {

        $panel = ProjectProductionPanels::find()->where(['id' => $panelId])->one();

        if ($panel->taskAssignElecs || $panel->taskAssignFabs) {
            return "Unable to revert, task assigned!";
        }

        ProdElecTaskWeight::deleteAll(['proj_prod_panel_id' => $panelId]);
        ProdFabTaskWeight::deleteAll(['proj_prod_panel_id' => $panelId]);
        ProductionElecTasks::deleteAll(['proj_prod_panel_id' => $panelId]);
        ProductionFabTasks::deleteAll(['proj_prod_panel_id' => $panelId]);
        \frontend\models\bom\BomMaster::deleteAll(['production_panel_id' => $panelId]);

        $panel->finalized_at = null;
        $panel->finalized_by = null;

        if ($panel->update()) {
            FlashHandler::success("Panel " . $panel->project_production_panel_code . " Reverted");
        } else {
            FlashHandler::err("Revert Fail. Please contact IT Support");
        }

        return $this->redirect(['view-production-main', 'id' => $panel->proj_prod_master]);
    }

    /*
     * by Khetty, 20/2/2024
     * ****** View Tasks ******
     */

    public function actionViewTasks($id, $panelId) {
        $model = ProjectProductionMaster::findOne($id);
        $panels = ProjectProductionPanels::find()->where(['id' => $panelId, 'proj_prod_master' => $id])->all();
        $prodFabTasks = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
        $prodElecTasks = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $fabPanelWeight = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        $elecPanelWeight = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        $finalize = false;
        return $this->render($this::mainViewPath . 'taskList', [
                    'model' => $model,
                    'panels' => $panels,
                    'prodFabTasks' => $prodFabTasks,
                    'prodElecTasks' => $prodElecTasks,
                    'refFabTask' => $refFabTask,
                    'refElecTask' => $refElecTask,
                    'panelId' => $panelId,
                    'fabPanelWeight' => $fabPanelWeight,
                    'elecPanelWeight' => $elecPanelWeight,
                    'finalize' => $finalize
        ]);
    }

    /*
     * by Khetty 
     * updated on 26/6/2024
     * ****** Update Task List ******
     */

    public function actionUpdateTasks($id, $pushPanel) {
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            $successFab = true;
            $successElec = true;
            foreach ($postData['tasks'] as $panelId => $dept) {
                $panel = ProjectProductionPanels::findOne($panelId);
                if (!$panel) {
                    continue;
                }
                if ($pushPanel) {
                    $panel->finalizePanelDetail();
                }

                $project = ProjectProductionMaster::findOne($panel->proj_prod_master);

                if (isset($dept['fab'])) {
                    foreach ($dept['fab'] as $taskCode => $fabTaskData) {
                        if (!$this->updateFabTask($panelId, $panel, $fabTaskData)) {
                            $successFab = false;
                        }
                    }

                    $project->updateAvgFabProgressPercent();
                }
//                else {
//                    $successFab = false;
//                    FlashHandler::err("No fabrication tasks have been selected.");
//                }

                if (isset($dept['elec'])) {
                    foreach ($dept['elec'] as $taskCode => $elecTaskData) {
                        if (!$this->updateElecTask($panelId, $panel, $elecTaskData)) {
                            $successElec = false;
                        }
                    }

                    $project->updateAvgElecProgressPercent();
                }
//                else {
//                    $successElec = false;
//                    FlashHandler::err("No electrical tasks have been selected.");
//                }
            }

            if ($successFab && $successElec) {
                $transaction->commit();
                $this->updatePanelWeight($postData);
                FlashHandler::success("Saved.");
            } else {
                $transaction->rollBack();
            }
            return $this->redirect(['view-production-main', 'id' => $id]);
        }
    }

    /*     * ************* Updates both fabrication and electrical tasks for a specific panel. ******************

     * by Khetty, 21/5/2024
     * Finalizes the fabrication and electrical tasks and updates relevant progress and status.
     * @param array $fabTaskData and $elecTaskData Data of the task to be updated.
     * @return bool True if the task was successfully updated or deleted, false otherwise.
     */

    private function updateFabTask($panelId, $panel, $fabTaskData) {
        $fabTaskWeight = new ProdFabTaskWeight();
        if (isset($fabTaskData['id']) && $fabTaskData['id'] !== "") {
            if ($panel->finalizeFabTask($fabTaskData)) {
                $fabTaskWeight->saveDefaultFabPanelTaskWeight($fabTaskData['id'], $fabTaskData['code']);
            }

            if ($panel->updateFabProgressPercent()) {
                $panel->checkPanelFabWorkStatus();
            }
        } else {
            $fabTask = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $panelId, 'fab_task_code' => $fabTaskData['code']])->one();
            if ($fabTask && $fabTask->delete()) {
                if (!$fabTaskWeight->updateFabTaskWeightAfterDeleteTask($panelId, $fabTaskData['code']) || !$panel->updateFabWorkStatusAfterDeleteTask($panelId)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function updateElecTask($panelId, $panel, $elecTaskData) {
        $elecTaskWeight = new ProdElecTaskWeight();
        if (isset($elecTaskData['id']) && $elecTaskData['id'] !== "") {
            if ($panel->finalizeElecTask($elecTaskData)) {
                $elecTaskWeight->saveDefaultElecPanelTaskWeight($elecTaskData['id'], $elecTaskData['code']);
            }

            if ($panel->updateElecProgressPercent()) {
                $panel->checkPanelElecWorkStatus();
            }
        } else {
            $elecTask = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $panelId, 'elec_task_code' => $elecTaskData['code']])->one();
            if ($elecTask && $elecTask->delete()) {
                if (!$elecTaskWeight->updateElecTaskWeightAfterDeleteTask($panelId, $elecTaskData['code']) || !$panel->updateElecWorkStatusAfterDeleteTask($panelId)) {
                    return false;
                }
            }
        }
        return true;
    }

    /*
     * by Khetty 
     * updated at 20/5/2024
     * Update Panel Weight for Pushed Panel
     */

    private function updatePanelWeight($postData) {
        $fabTaskWeightModel = new ProdFabTaskWeight();
        $elecTaskWeightModel = new ProdElecTaskWeight();

        $updateFabTaskWeight = $fabTaskWeightModel->updateFabPanelWeight($postData);
        $updateElecTaskWeight = $elecTaskWeightModel->updateElecPanelWeight($postData);
        if (!$updateFabTaskWeight || !$updateElecTaskWeight) {
            FlashHandler::err("Update Panel Weight Failed.");
        }
    }

    /*
     * by Khetty 
     * on 30/10/2023
     * ****** Delete Selected Panels ******
     */

    public function actionDeletePanelsAjax($id) {
        $panelIds = Yii::$app->request->post('panelIds');

        foreach ($panelIds as $panelId) {
            $panel = ProjectProductionPanels::findOne($panelId);
            if ($panel) {
                $panelDelete = new ProjectProductionPanelsDelete();
                $panelDelete->attributes = $panel->attributes; // Copy attributes
                // Set the differing column
                $panelDelete->project_production_panels_id = $panel->id;
                $panelDelete->save();
//                 Delete all related task and BOM records before deleting the panel
                ProdElecTaskWeight::deleteAll(['proj_prod_panel_id' => $panel->id]);
                ProdFabTaskWeight::deleteAll(['proj_prod_panel_id' => $panel->id]);
                ProductionElecTasks::deleteAll(['proj_prod_panel_id' => $panel->id]);
                ProductionFabTasks::deleteAll(['proj_prod_panel_id' => $panel->id]);
                \frontend\models\bom\BomMaster::deleteAll(['production_panel_id' => $panel->id]);
                $panel->delete();
            }
        }

        FlashHandler::success("Panel(s) deleted.");
        return $this->redirect(['view-production-main', 'id' => $id]);
    }

//    public function actionDeletePanelsAjax($id) {
//        $panelIds = Yii::$app->request->post('panelIds');
//
//        $deletedCount = 0;
//        $cannotDeletePanels = [];
//
//        foreach ($panelIds as $panelId) {
//            $panel = ProjectProductionPanels::findOne($panelId);
//            if ($panel) {
//                $panelDelete = new ProjectProductionPanelsDelete();
//                $panelDelete->attributes = $panel->attributes; // Copy attributes
//                // Set the differing column
//                $panelDelete->project_production_panels_id = $panel->id;
//                $panelDelete->save();
//
//                $hasBomMasterRecord = \frontend\models\bom\BomMaster::find()->where(['production_panel_id' => $panel->id])->exists();
//                if (!$hasBomMasterRecord) {
//                    $panel->delete();
//                    $deletedCount++;
//                } else {
//                    // Add to the list of panels that cannot be deleted
//                    $cannotDeletePanels[] = $panel->project_production_panel_code;
//                }
//            }
//        }
//
//        if (count($cannotDeletePanels) > 0) {
//            $undeletablePanels = implode('</br>', $cannotDeletePanels);
//            FlashHandler::err("The following panel(s) cannot be deleted because B.O.M has been created: </br>$undeletablePanels");
//        }
//
//        if ($deletedCount > 0) {
//            FlashHandler::success("$deletedCount panel(s) successfully deleted.");
//        }
//
//        return $this->redirect(['view-production-main', 'id' => $id]);
//    }

    /*
     * ******************************* Project Production Panel ITEMS
     */

    public function actionViewProjectPanelItems($panelId) {
        $model = ProjectProductionPanels::findOne($panelId);
        $req = Yii::$app->request;
        if ($req->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->saveItems($req->post())) {
                FlashHandler::success("Saved");
            } else {
                $transaction->rollBack();
            }
            $transaction->commit();
            return $this->redirect(["view-project-panel-items", "panelId" => $panelId]);
        }

        if (empty($model->finalized_at)) {
            return $this->render($this::panelItemViewPath . 'updateProjectPanelItems', [
                        'model' => $model,
            ]);
        } else {
            return $this->render($this::panelItemViewPath . 'viewProjectPanelItems', [
                        'model' => $model,
            ]);
        }
    }

    /*
     * ********************************** General Functions **********************************
     */

    public function actionAjaxInsertItems($itemId = "") {
        $item = $itemId ? ProjectProductionPanelItems::findOne($itemId) : (new ProjectProductionPanelItems());
        $unitList = RefProjectItemUnit::getDropDownList();
        return $this->renderAjax($this::panelItemViewPath . '__ajaxInsertItems', [
                    'item' => $item,
                    'unitList' => $unitList
        ]);
    }

    public function actionAjaxGetItemHistory($term = "") {
        $data = ProjectProductionPanelItems::find()
                        ->select(['item_description as label', 'item_description as value', 'item_description as id'])
                        ->where("item_description LIKE '%" . addslashes($term) . "%'")
                        ->orderBy(['item_description' => SORT_ASC])
                        ->distinct()->asArray()->all();
        return \yii\helpers\Json::encode($data);
    }

    /**
     *  For production documents
     * @param type $id
     * @return type
     */
    public function actionUploadAttachments($id) {
        if (Yii::$app->request->isPost) {
            $model = $this->findModel($id);
            $model->scannedFile = \yii\web\UploadedFile::getInstances($model, 'scannedFile');

            if ($model->validate() && $model->scannedFile) {

                $filePath = Yii::$app->params['project_file_path'] . $model->id . '/references/';
                MyCommonFunction::mkDirIfNull($filePath);
                foreach ($model->scannedFile as $file) {
                    $file->saveAs($filePath . $file->name);
                    $document = new \frontend\models\projectproduction\ProjectProductionDocuments();
                    $document->filename = $file->name;
                    $document->project_production_master_id = $model->id;
                    $document->save();
                }
                FlashHandler::success("Uploaded!");
            }
        }

        return $this->redirect(["view-production-main", "id" => $id]);
    }

    /**
     *  For production documents
     * @param type $id
     * @return type
     */
    public function actionGetFileById($id) {
        $doc = ProjectProductionDocuments::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $doc->project_production_master_id . "/references/" . $doc->filename;
        return Yii::$app->response->sendFile($completePath, $doc->filename, ['inline' => true]);
    }

    public function actionDeleteProductionFile($id) {
        $doc = ProjectProductionDocuments::findOne($id);
        if (Yii::$app->request->isPost) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $doc->project_production_master_id . "/references/" . $doc->filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $doc->delete();
            FlashHandler::success("Project file removed");
        }
        return $this->redirect(["view-production-main", "id" => $doc->project_production_master_id]);
    }

    /**
     *  For PANELS' documents
     * @param type $panelId
     * @return type
     */
    public function actionPanelUploadAttachment($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);

        if (Yii::$app->request->isPost) {
            $panel->scannedFile = \yii\web\UploadedFile::getInstance($panel, 'scannedFile');

            if ($panel->scannedFile) {
                $file = $panel->scannedFile;
                $filePath = Yii::$app->params['project_file_path'] . $panel->proj_prod_master . '/panels/' . $panel->id . "/";
                MyCommonFunction::mkDirIfNull($filePath);
                $file->saveAs($filePath . $file->name);

                $panel->filename = $file->name;
                if ($panel->update()) {
                    FlashHandler::success("Uploaded!");
                }
            }
            return $this->redirect(["view-production-main", "id" => $panel->proj_prod_master]);
        }

        return $this->renderPartial("/projectproduction/main/_formUploadPanelFile", ["model" => $panel]);
    }

    /**
     *  For Panel document
     * @param type $panelId
     * @return type
     */
    //work better
    public function actionGetPanelFileByPanelId($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);

        if (!$panel) {
            throw new \yii\web\NotFoundHttpException("Panel not found.");
        }

        $completePath = Yii::getAlias('@webroot') . '/'
                . Yii::$app->params['project_file_path']
                . $panel->proj_prod_master . '/panels/'
                . $panel->id . "/" . $panel->filename;

        if (!file_exists($completePath)) {
            throw new \yii\web\NotFoundHttpException("File not found.");
        }

        if (!is_readable($completePath)) {
            throw new \yii\web\ServerErrorHttpException("File is not readable.");
        }

        // Disable Apache's output compression
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        // Disable all PHP output buffering and compression
        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', '0');
        @ini_set('implicit_flush', '1');

        // Clear all output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Read entire file into memory first (ensures complete load)
        $content = file_get_contents($completePath);
        $fileSize = strlen($content);

        // Send headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($panel->filename) . '"');
        header('Content-Length: ' . $fileSize);
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: public, max-age=3600');
        header('Accept-Ranges: bytes');
        header('Connection: close');

        // Output entire content at once
        echo $content;
        flush();

        exit();
    }

//    public function actionGetPanelFileByPanelId($panelId) {
//        $panel = ProjectProductionPanels::findOne($panelId);
//        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $panel->proj_prod_master . '/panels/' . $panel->id . "/" . $panel->filename;
//        return Yii::$app->response->sendFile($completePath, $panel->filename, ['inline' => true]);
//    }

    public function actionDeletePanelFile($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);
        if (Yii::$app->request->isPost) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $panel->proj_prod_master . '/panels/' . $panel->id . "/" . $panel->filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $panel->filename = null;
            $panel->update();
            FlashHandler::success("Panel file removed");
        }
        return $this->redirect(["view-production-main", "id" => $panel->proj_prod_master]);
    }

    /**
     * Delete the panels' in Project Production, rollback.
     * password = delete
     */
    public function actionSecretDeleteProductionPanel($panelId, $password = '') {
        if ($password != "delete") {
            return "ERROR!";
        }
        $panel = ProjectProductionPanels::findOne($panelId);
        foreach ($panel->productionElecTasks as $sub) {
            foreach ($sub->taskAssignElecs as $sub2) {
                foreach ($sub2->taskAssignElecCompletes as $complete) {
                    $complete->delete();
                }
                foreach ($sub2->taskAssignElecStaff as $staff) {
                    $staff->delete();
                }

                $sub2->delete();
            }
            $sub->delete();
        }
        foreach ($panel->productionFabTasks as $sub) {
            foreach ($sub->taskAssignFabs as $sub2) {
                foreach ($sub2->taskAssignFabCompletes as $complete) {
                    $complete->delete();
                }
                foreach ($sub2->taskAssignFabStaff as $staff) {
                    $staff->delete();
                }

                $sub2->delete();
            }
            $sub->delete();
        }
        foreach ($panel->testMains as $testMain) {
            foreach ($testMain->testMasters as $testMaster) {
                Yii::$app->db->createCommand("SET foreign_key_checks = 0;")->execute();
                $testMaster->delete();
                Yii::$app->db->createCommand("SET foreign_key_checks = 1;")->execute();
            }
            $testMain->delete();
        }
        if (!empty($panel->prodElecTaskWeights)) {
            foreach ($panel->prodElecTaskWeights as $elecWeight) {
                $elecWeight->delete();
            }
        }
        if (!empty($panel->prodFabTaskWeights)) {
            foreach ($panel->prodFabTaskWeights as $fabWeight) {
                $fabWeight->delete();
            }
        }
        if (!empty($panel->bomMasters)) {
            foreach ($panel->bomMasters as $bomMaster) {
                foreach ($bomMaster->bomDetails as $bomDetail) {
                    $bomDetail->delete();
                }
                $bomMaster->delete();
            }
        }
        $panel->finalized_at = null;
        $panel->finalized_by = null;
        return $panel->update();
//        return $this->redirect(["view-production-main", "id" => $panel->proj_prod_master]);
    }

    /**
     * Create individual task weight for existing production panel
     */
    public function actionSecretadddefaultweight() {
        $panels = ProjectProductionPanels::find()->all();
        foreach ($panels as $panel) {
            $fab = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panel->id])->all();
            if (empty($fab)) {
                $fabTasks = $panel->productionFabTasks;
                foreach ($fabTasks as $task) {
                    $newWeight = new ProdFabTaskWeight();
                    $newWeight->saveDefaultFabPanelTaskWeight($panel->id, $task->fab_task_code);
                }
            }
            $elec = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panel->id])->all();
            if (empty($elec)) {
                $elecTasks = $panel->productionElecTasks;
                foreach ($elecTasks as $task) {
                    $newWeight = new ProdElecTaskWeight();
                    $newWeight->saveDefaultElecPanelTaskWeight($panel->id, $task->elec_task_code);
                }
            }
            // $panelId, $taskCode
        }
        return "done";
    }

    public function actionInsertWeldGrindValue() {
        ini_set('memory_limit', '10G'); // optional – increase if still heavy
        ini_set('max_execution_time', '0');

        $batchSize = 50;
        $query = ProductionFabTasks::find()
                ->where(['fab_task_code' => 'weldngrind']);

        foreach ($query->batch($batchSize) as $taskBatch) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                foreach ($taskBatch as $prodWeldnGrindTask) {

                    foreach (['weld', 'grind'] as $newCode) {
                        // ✅ Skip if already cloned
                        $alreadyCloned = ProductionFabTasks::find()
                                ->where([
                                    'parent_id' => $prodWeldnGrindTask->id,
                                    'fab_task_code' => $newCode,
                                ])
                                ->exists();

                        if ($alreadyCloned) {
                            echo "Skipping {$newCode} for parent ID {$prodWeldnGrindTask->id} - already exists\n";
                            continue;
                        }

                        // 🔹 Step 1: Clone weldngrind → weld or grind
                        $newTask = new ProductionFabTasks();
                        $newTask->setAttributes($prodWeldnGrindTask->getAttributes(null, ['id']), false);
                        $newTask->fab_task_code = $newCode;
                        $newTask->parent_id = $prodWeldnGrindTask->id;

                        if (!$newTask->save(false)) {
                            throw new \Exception("Failed to save {$newCode} task for ID {$prodWeldnGrindTask->id}: " . json_encode($newTask->errors));
                        }

                        // 🔹 Step 2: Clone TaskAssignFab
                        foreach (TaskAssignFab::find()->where(['prod_fab_task_id' => $prodWeldnGrindTask->id])->batch(20) as $assignBatch) {
                            foreach ($assignBatch as $taskAssignWeldnGrind) {
                                $newTaskAssign = new TaskAssignFab();
                                $newTaskAssign->setAttributes($taskAssignWeldnGrind->getAttributes(null, ['id']), false);
                                $newTaskAssign->prod_fab_task_id = $newTask->id;

                                if (!$newTaskAssign->save(false)) {
                                    throw new \Exception("Failed to save TaskAssignFab for ID {$taskAssignWeldnGrind->id} ({$newCode}): " . json_encode($newTaskAssign->errors));
                                }

                                // 🔹 Step 3: Clone TaskAssignFabStaff
                                foreach (TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $taskAssignWeldnGrind->id])->batch(20) as $staffBatch) {
                                    foreach ($staffBatch as $taskAssignStaff) {
                                        $newStaff = new TaskAssignFabStaff();
                                        $newStaff->setAttributes($taskAssignStaff->getAttributes(null, ['id']), false);
                                        $newStaff->task_assign_fab_id = $newTaskAssign->id;

                                        if (!$newStaff->save(false)) {
                                            throw new \Exception("Failed to save TaskAssignFabStaff for ID {$taskAssignStaff->id} ({$newCode}): " . json_encode($newStaff->errors));
                                        }

                                        // 🔹 Step 4: Clone TaskAssignFabStaffComplete
                                        foreach (TaskAssignFabStaffComplete::find()->where(['task_assign_fab_staff_id' => $taskAssignStaff->id])->batch(20) as $completeBatch) {
                                            foreach ($completeBatch as $staffComplete) {
                                                $newComplete = new TaskAssignFabStaffComplete();
                                                $newComplete->setAttributes($staffComplete->getAttributes(null, ['id']), false);
                                                $newComplete->task_assign_fab_staff_id = $newStaff->id;

                                                if (!$newComplete->save(false)) {
                                                    throw new \Exception("Failed to save TaskAssignFabStaffComplete for ID {$staffComplete->id} ({$newCode}): " . json_encode($newComplete->errors));
                                                }

                                                // 🔹 Step 5: Clone TaskAssignFabStaffCompleteDelete
                                                foreach (TaskAssignFabStaffCompleteDelete::find()
                                                        ->where([
                                                            'task_assign_fab_staff_complete_id' => $staffComplete->id,
                                                            'task_assign_fab_staff_id' => $taskAssignStaff->id,
                                                        ])->batch(20) as $deleteBatch) {

                                                    foreach ($deleteBatch as $completeDelete) {
                                                        $newDelete = new TaskAssignFabStaffCompleteDelete();
                                                        $newDelete->setAttributes($completeDelete->getAttributes(null, ['id']), false);
                                                        $newDelete->task_assign_fab_staff_complete_id = $newComplete->id;
                                                        $newDelete->task_assign_fab_staff_id = $newStaff->id;

                                                        if (!$newDelete->save(false)) {
                                                            throw new \Exception("Failed to save TaskAssignFabStaffCompleteDelete for ID {$completeDelete->id} ({$newCode}): " . json_encode($newDelete->errors));
                                                        }

                                                        unset($newDelete);
                                                    }
                                                }

                                                unset($newComplete);
                                            }
                                        }

                                        unset($newStaff);
                                    }
                                }

                                unset($newTaskAssign);
                            }
                        }

                        unset($newTask);
                    }

                    // Free up memory per parent
                    unset($prodWeldnGrindTask);
                    gc_collect_cycles();
                }

                $transaction->commit();
                echo "Batch committed successfully.\n";
            } catch (\Throwable $e) {
                $transaction->rollBack();
                echo "Error: " . $e->getMessage() . "\n";
                Yii::error($e->getMessage(), __METHOD__);
            }

            gc_collect_cycles(); // extra cleanup per batch
        }

        echo "All weldngrind tasks successfully cloned to weld & grind.\n";
    }
}
