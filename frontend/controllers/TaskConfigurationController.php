<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectproduction\task\WorkerTaskCategories;
use frontend\models\projectproduction\task\WorkerTaskCategoriesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\projectproduction\task\TaskAssignment;
use common\models\User;

/**
 * TaskConfigurationController implements the CRUD actions for WorkerTaskCategories model.
 */
class TaskConfigurationController extends Controller {

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/taskconfiguration/');
    }

    public function actionIndexFab() {
        $taskType = TaskAssignment::taskTypeFabrication;

        $workerList = \common\models\User::getStaffList_productionAssignee($taskType);
        $workTasks = \frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab::find()->where(['active_sts' => 1])
                        ->orderBy(['sort' => SORT_ASC])->asArray()->all();
        $workerAvailableTasks = WorkerTaskCategories::find()->where(['task_type' => $taskType])->asArray()->all();

        $mainList = [];
        foreach ($workerAvailableTasks as $assignableTask) {
            $mainList[$assignableTask['user_id']][$assignableTask['task_code']] = 1;
        }
        return $this->render('indexFab', [
                    'workerList' => $workerList,
                    'workTasks' => $workTasks,
                    'mainList' => $mainList
        ]);
    }

    public function actionUpdateFab() {
        $taskType = TaskAssignment::taskTypeFabrication;

        $workerList = \common\models\User::getStaffList_productionAssignee($taskType);
        $workTasks = \frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab::find()->where(['active_sts' => 1])
                        ->orderBy(['sort' => SORT_ASC])->asArray()->all();
        $workerAvailableTasks = WorkerTaskCategories::find()->where(['task_type' => $taskType])->asArray()->all();

        $mainList = [];

        foreach ($workerAvailableTasks as $assignableTask) {
            $mainList[$assignableTask['user_id']][$assignableTask['task_code']] = 1;
        }


        if ($this->request->isPost) {
            $selections = $this->request->post('taskAllow', []);
            foreach ($workerList as $worker) {
                foreach ($workTasks as $tasks) {
                    $userId = $worker['id'];
                    $taskCode = $tasks['code'];

                    $isSelected = isset($selections[$userId][$taskCode]);

                    $model = WorkerTaskCategories::findOne(['user_id' => $userId, 'task_code' => $taskCode, 'task_type' => $taskType]);

                    if ($isSelected) {
                        // If the checkbox is selected and the record doesn't exist, create it.
                        if (!$model) {
                            $model = new WorkerTaskCategories();
                            $model->user_id = $userId;
                            $model->task_code = $taskCode;
                            $model->task_type = $taskType;
                            $model->save();
                        }
                    } else {
                        // If the checkbox is not selected and the record exists, delete it.
                        if ($model) {
                            $model->delete();
                        }
                    }
                }
            }



            return $this->redirect(['index-fab']);
        }

        return $this->render('updateFab', [
                    'workerList' => $workerList,
                    'workTasks' => $workTasks,
                    'mainList' => $mainList
        ]);
    }

    public function actionIndexElec() {
        $taskType = TaskAssignment::taskTypeElectrical;

        $workerList = \common\models\User::getStaffList_productionAssignee($taskType);
        $workTasks = \frontend\models\ProjectProduction\electrical\RefProjProdTaskElec::find()->where(['active_sts' => 1])
                        ->orderBy(['sort' => SORT_ASC])->asArray()->all();
        $workerAvailableTasks = WorkerTaskCategories::find()->where(['task_type' => $taskType])->asArray()->all();

        $mainList = [];
        foreach ($workerAvailableTasks as $assignableTask) {
            $mainList[$assignableTask['user_id']][$assignableTask['task_code']] = 1;
        }
        return $this->render('indexElec', [
                    'workerList' => $workerList,
                    'workTasks' => $workTasks,
                    'mainList' => $mainList
        ]);
    }

    public function actionUpdateElec() {
        $taskType = TaskAssignment::taskTypeElectrical;

        $workerList = \common\models\User::getStaffList_productionAssignee($taskType);
        $workTasks = \frontend\models\ProjectProduction\electrical\RefProjProdTaskElec::find()->where(['active_sts' => 1])
                        ->orderBy(['sort' => SORT_ASC])->asArray()->all();
        $workerAvailableTasks = WorkerTaskCategories::find()->where(['task_type' => $taskType])->asArray()->all();

        $mainList = [];

        foreach ($workerAvailableTasks as $assignableTask) {
            $mainList[$assignableTask['user_id']][$assignableTask['task_code']] = 1;
        }


        if ($this->request->isPost) {
            $selections = $this->request->post('taskAllow', []);
            foreach ($workerList as $worker) {
                foreach ($workTasks as $tasks) {
                    $userId = $worker['id'];
                    $taskCode = $tasks['code'];

                    $isSelected = isset($selections[$userId][$taskCode]);

                    $model = WorkerTaskCategories::findOne(['user_id' => $userId, 'task_code' => $taskCode, 'task_type' => $taskType]);

                    if ($isSelected) {
                        // If the checkbox is selected and the record doesn't exist, create it.
                        if (!$model) {
                            $model = new WorkerTaskCategories();
                            $model->user_id = $userId;
                            $model->task_code = $taskCode;
                            $model->task_type = $taskType;
                            $model->save();
                        }
                    } else {
                        // If the checkbox is not selected and the record exists, delete it.
                        if ($model) {
                            $model->delete();
                        }
                    }
                }
            }



            return $this->redirect(['index-elec']);
        }

        return $this->render('updateElec', [
                    'workerList' => $workerList,
                    'workTasks' => $workTasks,
                    'mainList' => $mainList
        ]);
    }

}
