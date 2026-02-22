<?php

namespace frontend\controllers\production;

use frontend\models\projectproduction\fabrication\VFabStaffProduction;
use frontend\models\projectproduction\fabrication\VFabStaffProductionSearch;
use frontend\models\projectproduction\electrical\VElecStaffProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\projectproduction\electrical\TaskAssignElec;
use frontend\models\projectproduction\electrical\TaskAssignElecComplete;
use frontend\models\projectproduction\fabrication\TaskAssignFab;
use frontend\models\projectproduction\fabrication\TaskAssignFabComplete;
use common\models\myTools\FlashHandler;
use frontend\models\projectproduction\VStaffProductionAllSearch;
use common\models\myTools\MyFormatter;
use yii\console\ExitCode;
use frontend\models\projectproduction\fabrication\TaskAssignFabStaff;
use frontend\models\projectproduction\electrical\TaskAssignElecStaff;
use frontend\models\projectproduction\electrical\ProductionElecTasksError;
use frontend\models\projectproduction\fabrication\ProductionFabTasksError;
use frontend\models\projectproduction\electrical\ProductionElecTasksErrorStaff;
use frontend\models\projectproduction\fabrication\ProductionFabTasksErrorStaff;
use common\models\User;

/**
 * PanelTaskStatusController implements the CRUD actions for VFabStaffProduction model.
 */
class PanelTaskStatusController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/projectproduction/paneltaskstatus');
    }

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@']
                            ],
                        ],
                    ],
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    public function actionIndexDefects() {
        $userId = Yii::$app->user->id;
        $defectLists = [];

        // Helper function to process task errors
        $processTaskErrors = function ($taskErrors, $taskAssignClass, $taskErrorStaffClass, $taskType, $taskRelationName) use ($userId) {
            $result = [];
            foreach ($taskErrors as $taskError) {
                $taskAssign = $taskAssignClass::findOne([
                    $taskType === 'elec' ? 'prod_elec_task_id' : 'prod_fab_task_id' =>
                    $taskError->{$taskType === 'elec' ? 'production_elec_task_id' : 'production_fab_task_id'},
                    'deactivated_by' => null,
                    'deactivated_at' => null
                ]);

                if (!$taskAssign) {
                    continue;
                }

                $taskErrorStaff = $taskErrorStaffClass::findOne([
                    $taskType === 'elec' ? 'production_elec_tasks_error_id' : 'production_fab_tasks_error_id' => $taskError->id,
                    'staff_id' => $userId
                ]);

                if (!$taskErrorStaff) {
                    continue;
                }

                $createdBy = User::findOne($taskError->created_by);

                $result[] = [
                    'id' => $taskError->id,
                    'task_assign_id' => $taskAssign->id,
                    'task_type' => $taskType,
                    'panel_code' => $taskAssign->projProdPanel->project_production_panel_code ?? '',
                    'task_name' => $taskError->{$taskRelationName}->{$taskType === 'elec' ? 'elecTaskCode' : 'fabTaskCode'}->name ?? '',
                    'description' => $taskError->errorCode->description ?? '',
                    'remark' => $taskError->remark,
                    'created_by' => $createdBy ? $createdBy->fullname : '',
                    'created_at_raw' => $taskError->created_at, // for sorting
                    'created_at' => MyFormatter::asDateTime_ReaddmYHi($taskError->created_at), // formatted
                    'is_read' => $taskErrorStaff->is_read,
                    'read_at' => $taskErrorStaff->read_at ? MyFormatter::asDateTime_ReaddmYHi($taskErrorStaff->read_at) : '',
                    'production_task_id' => $taskType === 'elec' ? $taskError->production_elec_task_id : $taskError->production_fab_task_id,
                ];
            }
            return $result;
        };

        // Process Electrical defects
        $panelTaskErrorElec = ProductionElecTasksError::find()->all();
        $defectLists = array_merge($defectLists, $processTaskErrors(
                        $panelTaskErrorElec,
                        TaskAssignElec::class,
                        ProductionElecTasksErrorStaff::class,
                        'elec',
                        'productionElecTask'
                ));

        // Process Fabrication defects
        $panelTaskErrorFab = ProductionFabTasksError::find()->all();
        $defectLists = array_merge($defectLists, $processTaskErrors(
                        $panelTaskErrorFab,
                        TaskAssignFab::class,
                        ProductionFabTasksErrorStaff::class,
                        'fab',
                        'productionFabTask'
                ));

        // Sort by created_at ascending
        usort($defectLists, function ($a, $b) {
            $timeA = isset($a['created_at_raw']) ? strtotime($a['created_at_raw']) : PHP_INT_MAX;
            $timeB = isset($b['created_at_raw']) ? strtotime($b['created_at_raw']) : PHP_INT_MAX;
            return $timeB <=> $timeA;
        });

        return $this->render('indexDefects', [
                    'defectLists' => json_encode($defectLists),
        ]);
    }

    public function actionAjaxTaskElecDefectDetail($id, $complaintId) {
        $taskAssign = TaskAssignElec::findOne($id);
        if (!$taskAssign) {
            throw new NotFoundHttpException('Task assignment not found');
        }

        $panel = $taskAssign->projProdPanel;
        $project = $panel->projProdMaster;
        $staffNameList = $taskAssign->taskAssignElecStaff;

        $complaint = ProductionElecTasksError::findOne($complaintId);
        if (!$complaint) {
            throw new NotFoundHttpException('Complaint not found');
        }

        $task = $complaint->productionElecTask;
        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $panelTaskErrorElecStaff = ProductionElecTasksErrorStaff::findOne([
            'production_elec_tasks_error_id' => $complaintId,
            'staff_id' => Yii::$app->user->id
        ]);

        if (Yii::$app->request->isPost) {
            if ($panelTaskErrorElecStaff && $panelTaskErrorElecStaff->is_read == 1) {
                $panelTaskErrorElecStaff->is_read = 2;
                $panelTaskErrorElecStaff->read_at = new \yii\db\Expression('NOW()');

                if ($panelTaskErrorElecStaff->save()) {
                    // Check if all staff have read this complaint
                    $unreadCount = ProductionElecTasksErrorStaff::find()
                            ->where([
                                'production_elec_tasks_error_id' => $complaintId,
                                'is_read' => 1
                            ])
                            ->count();

                    // If no unread records, mark complaint as read
                    if ($unreadCount == 0) {
                        $complaint->is_read = 2;
                        $complaint->save();
                    }
                }
            }

            return $this->redirect(['index-defects']);
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR: Invalid request type";
        }

        return $this->renderAjax('_ajaxTaskDefectDetail', [
                    'taskAssign' => $taskAssign,
                    'panel' => $panel,
                    'project' => $project,
                    'staffNameList' => $staffNameList,
                    'complaint' => $complaint,
                    'task' => $task,
                    'panelTaskErrorStaff' => $panelTaskErrorElecStaff,
                    'department' => "elec"
        ]);
    }

    public function actionAjaxTaskFabDefectDetail($id, $complaintId) {
        $taskAssign = TaskAssignFab::findOne($id);
        if (!$taskAssign) {
            throw new NotFoundHttpException('Task assignment not found');
        }

        $panel = $taskAssign->projProdPanel;
        $project = $panel->projProdMaster;
        $staffNameList = $taskAssign->taskAssignFabStaff;

        $complaint = ProductionFabTasksError::findOne($complaintId);
        if (!$complaint) {
            throw new NotFoundHttpException('Complaint not found');
        }

        $task = $complaint->productionFabTask;
        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $panelTaskErrorFabStaff = ProductionFabTasksErrorStaff::findOne([
            'production_fab_tasks_error_id' => $complaintId,
            'staff_id' => Yii::$app->user->id
        ]);

        if (Yii::$app->request->isPost) {
            if ($panelTaskErrorFabStaff && $panelTaskErrorFabStaff->is_read == 1) {
                $panelTaskErrorFabStaff->is_read = 2;
                $panelTaskErrorFabStaff->read_at = new \yii\db\Expression('NOW()');

                if ($panelTaskErrorFabStaff->save()) {
                    // Check if all staff have read this complaint
                    $unreadCount = ProductionFabTasksErrorStaff::find()
                            ->where([
                                'production_fab_tasks_error_id' => $complaintId,
                                'is_read' => 1
                            ])
                            ->count();

                    // If no unread records, mark complaint as read
                    if ($unreadCount == 0) {
                        $complaint->is_read = 2;
                        $complaint->save();
                    }
                }
            }

            return $this->redirect(['index-defects']);
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR: Invalid request type";
        }

        return $this->renderAjax('_ajaxTaskDefectDetail', [
                    'taskAssign' => $taskAssign,
                    'panel' => $panel,
                    'project' => $project,
                    'staffNameList' => $staffNameList,
                    'complaint' => $complaint,
                    'task' => $task,
                    'panelTaskErrorStaff' => $panelTaskErrorFabStaff,
                    'department' => "fab"
        ]);
    }

    public function actionIndex() {
        $searchModel = new VFabStaffProductionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Index Tables for Ongoing Task
     * @return type
     */
    public function actionMyActiveTask($date = null) {
        $searchModelFab = new VFabStaffProductionSearch();
        $dataProviderFab = $searchModelFab->search($this->request->queryParams, 'myActiveTask', $date);
        $searchModelElec = new VElecStaffProductionSearch();
        $dataProviderElec = $searchModelElec->search($this->request->queryParams, 'myActiveTask', $date);

        return $this->render('myActiveTask', [
                    'searchModelFab' => $searchModelFab,
                    'dataProviderFab' => $dataProviderFab,
                    'searchModelElec' => $searchModelElec,
                    'dataProviderElec' => $dataProviderElec,
        ]);
    }

    /**
     * Index Tables for All Task
     * @return type
     */
    public function actionMyAllTask() {
        $searchModel = new VStaffProductionAllSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, 'myAllTask');
        return $this->render('myAllTask', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new VFabStaffProduction();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    protected function findModel($id) {
        if (($model = VFabStaffProduction::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Update Fabrication record, and set complete
     */
//    public function actionAjaxFabSetComplete($id) {
//        $taskAssign = TaskAssignFab::findOne($id);
//
//        if (Yii::$app->request->isPost) {
//            if ($taskAssign->addCompletePanelFab(Yii::$app->request->post())) {
//                return $this->redirect(['my-active-task']);
//            }else {
//                return "ERROR: Failed to complete task";
//            }
//        } else if (!Yii::$app->request->isAjax) {
//            return "ERROR: Invalid request type";
//        } else {
//            $taskAssign->complete_date = date('Y-m-d');
//            $text = $taskAssign->comments;
//            $completeTasks = $taskAssign->taskAssignFabCompletes;
//            foreach ($completeTasks as $completeTask) {
//                $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
//                        . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
//            }
//            $taskAssign->comments = $text;
//        }
//
//        return $this->renderAjax('/workassignment/fab/_ajaxFormSetComplete', [
//                    'model' => $taskAssign,
//                    'allowDateChange' => false,
//                    'limit' => intval($taskAssign->quantity - $taskAssign->complete_qty),
//        ]);
//    }

    public function actionAjaxFabSetComplete($id) {
        $taskAssign = TaskAssignFab::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($taskAssign->addCompletePanelFab(Yii::$app->request->post())) {
                return $this->redirect(['my-active-task']);
            } else {
                return "ERROR: Failed to complete task";
            }
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR: Invalid request type";
        } else {
            $taskAssign->complete_date = date('Y-m-d');
            $text = $taskAssign->comments;
            $completeTasks = $taskAssign->taskAssignFabCompletes;
            foreach ($completeTasks as $completeTask) {
                $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                        . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
            }
            $taskAssign->comments = $text;
        }
        return $this->renderAjax('/workassignment/fab/_ajaxFormSetComplete', [
                    'model' => $taskAssign,
                    'allowDateChange' => false,
                    'limit' => ($taskAssign->quantity - $taskAssign->complete_qty),
                    'updateAllStaff' => false
        ]);
    }

    /**
     * Update Electrical record, and set complete
     */
    public function actionAjaxElecSetComplete($id) {
        $taskAssign = TaskAssignElec::findOne($id);

        if (Yii::$app->request->isPost) {
            if ($taskAssign->addCompletePanelElec(Yii::$app->request->post())) {
                return $this->redirect(['my-active-task']);
            } else {
                return "ERROR: Failed to complete task";
            }
        } else if (!Yii::$app->request->isAjax) {
            return "ERROR: Invalid request type";
        } else {
            $taskAssign->complete_date = date('Y-m-d');
            $text = $taskAssign->comments;
            $completeTasks = $taskAssign->taskAssignElecCompletes;
            foreach ($completeTasks as $completeTask) {
                $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                        . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
            }
            $taskAssign->comments = $text;
        }

        return $this->renderAjax('/workassignment/elec/_ajaxFormSetComplete', [
                    'model' => $taskAssign,
                    'allowDateChange' => false,
                    'limit' => ($taskAssign->quantity - $taskAssign->complete_qty),
                    'updateAllStaff' => false
        ]);
    }

    public function actionMigrateCompletedPastPanels($assignModel, $completeModel) {
        $taskAssigns = $assignModel::find()->where(['not', ['complete_date' => null]])->all();
        $fail = [];

        foreach ($taskAssigns as $taskAssign) {
            $taskAssignComplete = new $completeModel();
            $taskAssignComplete->{$assignModel::tablename() . "_id"} = $taskAssign->id;
            $taskAssignComplete->quantity = $taskAssign->quantity;
            $taskAssignComplete->created_by = $taskAssign->complete_by;
            $taskAssignComplete->created_at = $taskAssign->complete_date;

            if (!$taskAssignComplete->save()) {
                $fail[] = $taskAssign->id;
            }
        }

        if ($fail) {
            return $fail;
        }

        return 'Data migrated';
    }

    public function actionMigrateCompletedTask() {
        $transaction = Yii::$app->db->beginTransaction();

        if (!$this->actionMigrateCompletedPastPanels(TaskAssignFab::class, TaskAssignFabComplete::class)) {
            $transaction->rollBack();
            return false;
        }

        if (!$this->actionMigrateCompletedPastPanels(TaskAssignElec::class, TaskAssignElecComplete::class)) {
            $transaction->rollBack();
            return false;
        }

        return $transaction->commit();
    }

    /**
     * Migrate task assignment data from old schema to new schema
     */
    public function actionMigrateTaskDataFab() {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Step 1: Update task_assign_fab_staff
//            \common\models\myTools\Mydebug::dumpFileW("Step 1: Updating task_assign_fab_staff...\n");
//            $updateQuery = "
//                UPDATE task_assign_fab_staff staff
//                INNER JOIN task_assign_fab fab 
//                    ON staff.task_assign_fab_id = fab.id
//                SET 
//                    staff.complete_qty = fab.complete_qty,
//                    staff.complete_date = fab.complete_date
//            ";
//            $updateResult = Yii::$app->db->createCommand($updateQuery)->execute();
//            \common\models\myTools\Mydebug::dumpFileW("Updated {$updateResult} records in task_assign_fab_staff\n");
            // Step 2: Insert into task_assign_fab_staff_complete
            $insertCompleteQuery = "
                INSERT INTO task_assign_fab_staff_complete (
                    task_assign_fab_staff_id, quantity, complete_date, 
                    comment, created_at, created_by
                )
                SELECT 
                    staff.id, comp.quantity, comp.complete_date,
                    comp.comment, comp.created_at, comp.created_by
                FROM task_assign_fab_complete comp
                INNER JOIN task_assign_fab_staff staff 
                    ON comp.task_assign_fab_id = staff.task_assign_fab_id
            ";
            $insertCompleteResult = Yii::$app->db->createCommand($insertCompleteQuery)->execute();
            \common\models\myTools\Mydebug::dumpFileW("Inserted {$insertCompleteResult} records into task_assign_fab_staff_complete\n");

            // Step 2: Insert into task_assign_fab_staff_complete_delete
            $insertDeleteQuery = "
                INSERT INTO task_assign_fab_staff_complete_delete (
                    task_assign_fab_staff_id,
                    quantity, complete_date, complete_comment, revert_comment,
                    complete_created_at, complete_created_by, deleted_at, deleted_by
                )
                SELECT 
                    staff.id,
                    del.quantity, del.complete_date, del.complete_comment, del.revert_comment,
                    del.complete_created_at, del.complete_created_by, del.deleted_at, del.deleted_by
                FROM task_assign_fab_complete_delete del
                INNER JOIN task_assign_fab_staff staff 
                    ON del.task_assign_fab_id = staff.task_assign_fab_id
            ";
            $insertDeleteResult = Yii::$app->db->createCommand($insertDeleteQuery)->execute();
            \common\models\myTools\Mydebug::dumpFileW("Inserted {$insertDeleteResult} records into task_assign_fab_staff_complete_delete\n");

            $transaction->commit();
            \common\models\myTools\Mydebug::dumpFileW("Migration completed successfully!\n");
            return ExitCode::OK;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \common\models\myTools\Mydebug::dumpFileW("Migration failed: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionMigrateUpdateCompleteQtyTaskFab() {
        set_time_limit(0);
        ini_set('memory_limit', '10G'); // Increase memory limit

        $batchSize = 100; // Process 100 records at a time
        $processedIds = [];
        $errorCount = 0;
        $totalProcessed = 0;

        // Get total count
        $totalCount = TaskAssignFab::find()->count();
        echo "Total records to process: $totalCount\n";

        try {
            for ($offset = 0; $offset < $totalCount; $offset += $batchSize) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    echo "Processing batch: " . ($offset + 1) . " to " . min($offset + $batchSize, $totalCount) . "\n";

                    // Load only current batch
                    $taskAssigns = TaskAssignFab::find()
                            ->offset($offset)
                            ->limit($batchSize)
                            ->all();

                    $batchProcessedIds = [];
                    $batchProcessedCount = 0;

                    foreach ($taskAssigns as $taskAssignFab) {
                        $taskAssignFabStaff = TaskAssignFabStaff::find()
                                ->select(['id'])
                                ->where(['task_assign_fab_id' => $taskAssignFab->id])
                                ->column();

                        if (empty($taskAssignFabStaff)) {
                            continue;
                        }

                        if (!$taskAssignFab->migrateUpdateFabStaffQty($taskAssignFab->id, $taskAssignFabStaff)) {
                            throw new \Exception('Failed updateFabStaffQty for ID: ' . $taskAssignFab->id);
                        }

                        if (!$taskAssignFab->updateCompleteQtyInAssignment()) {
                            throw new \Exception('Failed updateCompleteQtyInAssignment for ID: ' . $taskAssignFab->id);
                        }

                        if (!$taskAssignFab->updateTaskCalculation()) {
                            throw new \Exception('Failed updateTaskCalculation for ID: ' . $taskAssignFab->id);
                        }

                        if (!$taskAssignFab->bulkUpdateThisUserTaskOnHand()) {
                            throw new \Exception('Failed bulkUpdateThisUserTaskOnHand for ID: ' . $taskAssignFab->id);
                        }

                        if (!$taskAssignFab->updateTaskAndPanelCalculation()) {
                            throw new \Exception('Failed updateTaskAndPanelCalculation for ID: ' . $taskAssignFab->id);
                        }

                        $batchProcessedIds[] = $taskAssignFab->id;
                        $batchProcessedCount++;
                    }

                    // If we reach here, all records in the batch were processed successfully
                    $transaction->commit();

                    // Add batch results to totals only after successful commit
                    $processedIds = array_merge($processedIds, $batchProcessedIds);
                    $totalProcessed += $batchProcessedCount;

                    // Clear memory after each batch
                    unset($taskAssigns);
                    gc_collect_cycles();

                    echo "Batch completed successfully. Processed $batchProcessedCount records in this batch.\n";
                    echo "Total processed so far: $totalProcessed\n";
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $errorCount++;
                    echo "Batch failed and rolled back: " . $e->getMessage() . "\n";
                    echo "STOPPING entire process due to batch error.\n";

                    // Re-throw the exception to stop the entire process
                    throw new \Exception("Process stopped due to batch error at offset $offset: " . $e->getMessage());
                }
            }

            echo "All batches completed successfully!\n";
            return "Successfully processed $totalProcessed records with no errors.";
        } catch (\Exception $e) {
            \common\models\myTools\FlashHandler::err($e->getMessage());
            echo "Process terminated. Total processed before error: $totalProcessed\n";
            return 'Error: ' . $e->getMessage();
        }
    }

    public function actionMigrateTaskDataElec() {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Step 1: Update task_assign_elec_staff
//            $updateQuery = "
//                UPDATE task_assign_elec_staff staff
//                INNER JOIN task_assign_elec elec 
//                    ON staff.task_assign_elec_id = elec.id
//                SET 
//                    staff.complete_qty = elec.complete_qty,
//                    staff.complete_date = elec.complete_date
//            ";
//            $updateResult = Yii::$app->db->createCommand($updateQuery)->execute();
//            \common\models\myTools\Mydebug::dumpFileW("Updated {$updateResult} records in task_assign_elec_staff\n");
            // Step 2: Insert into task_assign_elec_staff_complete
            $insertCompleteQuery = "
                INSERT INTO task_assign_elec_staff_complete (
                    task_assign_elec_staff_id, quantity, complete_date, 
                    comment, created_at, created_by
                )
                SELECT 
                    staff.id, comp.quantity, comp.complete_date,
                    comp.comment, comp.created_at, comp.created_by
                FROM task_assign_elec_complete comp
                INNER JOIN task_assign_elec_staff staff 
                    ON comp.task_assign_elec_id = staff.task_assign_elec_id
            ";
            $insertCompleteResult = Yii::$app->db->createCommand($insertCompleteQuery)->execute();
            \common\models\myTools\Mydebug::dumpFileW("Inserted {$insertCompleteResult} records into task_assign_elec_staff_complete\n");

            // Step 3: Insert into task_assign_elec_staff_complete_delete
            $insertDeleteQuery = "
                INSERT INTO task_assign_elec_staff_complete_delete (
                    task_assign_elec_staff_id,
                    quantity, complete_date, complete_comment, revert_comment,
                    complete_created_at, complete_created_by, deleted_at, deleted_by
                )
                SELECT 
                    staff.id,
                    del.quantity, del.complete_date, del.complete_comment, del.revert_comment,
                    del.complete_created_at, del.complete_created_by, del.deleted_at, del.deleted_by
                FROM task_assign_elec_complete_delete del
                INNER JOIN task_assign_elec_staff staff 
                    ON del.task_assign_elec_id = staff.task_assign_elec_id
            ";
            $insertDeleteResult = Yii::$app->db->createCommand($insertDeleteQuery)->execute();
            \common\models\myTools\Mydebug::dumpFileW("Inserted {$insertDeleteResult} records into task_assign_elec_staff_complete_delete\n");

            $transaction->commit();
            \common\models\myTools\Mydebug::dumpFileW("Migration completed successfully!\n");
            return ExitCode::OK;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \common\models\myTools\Mydebug::dumpFileW("Migration failed: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionMigrateUpdateCompleteQtyTaskElec() {
        set_time_limit(0);
        ini_set('memory_limit', '10G'); // Increase memory limit

        $batchSize = 100; // Process 100 records at a time
        $processedIds = [];
        $errorCount = 0;
        $totalProcessed = 0;

        // Get total count
        $totalCount = TaskAssignElec::find()->count();
        echo "Total records to process: $totalCount\n";

        try {
            for ($offset = 0; $offset < $totalCount; $offset += $batchSize) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    echo "Processing batch: " . ($offset + 1) . " to " . min($offset + $batchSize, $totalCount) . "\n";

                    // Load only current batch
                    $taskAssigns = TaskAssignElec::find()
                            ->offset($offset)
                            ->limit($batchSize)
                            ->all();

                    $batchProcessedIds = [];
                    $batchProcessedCount = 0;

                    foreach ($taskAssigns as $taskAssignElec) {
                        // Remove the try-catch here - let exceptions bubble up to rollback the entire batch
                        $taskAssignElecStaff = TaskAssignElecStaff::find()
                                ->select(['id'])
                                ->where(['task_assign_elec_id' => $taskAssignElec->id])
                                ->column();

                        if (empty($taskAssignElecStaff)) {
                            continue;
                        }

                        if (!$taskAssignElec->migrateUpdateElecStaffQty($taskAssignElec->id, $taskAssignElecStaff)) {
                            throw new \Exception('Failed updateElecStaffQty for ID: ' . $taskAssignElec->id);
                        }

                        if (!$taskAssignElec->updateCompleteQtyInAssignment()) {
                            throw new \Exception('Failed updateCompleteQtyInAssignment for ID: ' . $taskAssignElec->id);
                        }

                        if (!$taskAssignElec->updateTaskCalculation()) {
                            throw new \Exception('Failed updateTaskCalculation for ID: ' . $taskAssignElec->id);
                        }

                        if (!$taskAssignElec->bulkUpdateThisUserTaskOnHand()) {
                            throw new \Exception('Failed bulkUpdateThisUserTaskOnHand for ID: ' . $taskAssignElec->id);
                        }

                        if (!$taskAssignElec->updateTaskAndPanelCalculation()) {
                            throw new \Exception('Failed updateTaskAndPanelCalculation for ID: ' . $taskAssignElec->id);
                        }

                        $batchProcessedIds[] = $taskAssignElec->id;
                        $batchProcessedCount++;
                    }

                    // If we reach here, all records in the batch were processed successfully
                    $transaction->commit();

                    // Add batch results to totals only after successful commit
                    $processedIds = array_merge($processedIds, $batchProcessedIds);
                    $totalProcessed += $batchProcessedCount;

                    // Clear memory after each batch
                    unset($taskAssigns);
                    gc_collect_cycles();

                    echo "Batch completed successfully. Processed $batchProcessedCount records in this batch.\n";
                    echo "Total processed so far: $totalProcessed\n";
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $errorCount++;
                    echo "Batch failed and rolled back: " . $e->getMessage() . "\n";
                    echo "STOPPING entire process due to batch error.\n";

                    // Re-throw the exception to stop the entire process
                    throw new \Exception("Process stopped due to batch error at offset $offset: " . $e->getMessage());
                }
            }

            echo "All batches completed successfully!\n";
            return "Successfully processed $totalProcessed records with no errors.";
        } catch (\Exception $e) {
            \common\models\myTools\FlashHandler::err($e->getMessage());
            echo "Process terminated. Total processed before error: $totalProcessed\n";
            return 'Error: ' . $e->getMessage();
        }
    }
}
