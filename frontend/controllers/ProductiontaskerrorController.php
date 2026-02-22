<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\projectproduction\VProductionTasksError;
use frontend\models\projectproduction\VProductionTasksErrorSearch;
use frontend\models\projectproduction\electrical\ProductionElecTasksError;
use frontend\models\projectproduction\electrical\ProductionElecTasksErrorStaff;
use frontend\models\projectproduction\fabrication\ProductionFabTasksError;
use frontend\models\projectproduction\fabrication\ProductionFabTasksErrorStaff;

/**
 * ProductiontaskerrorController implements the CRUD actions for VProductionTasksError model.
 */
class ProductiontaskerrorController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/workassignment/defect');
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
//                    [
//                        'actions' => ['index-fab-project-list', 'index-fab-in-progress', 'index-fab-all', 'index-fab-project-panels',
//                            'view-assigned-task', 'ajax-make-complaint', 'ajax-action-set-complete', 'delete-complaint'],
//                        'allow' => true,
//                        'roles' => [AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_ProjCoordinator, AuthItem::ROLE_SystemAdmin]
//                    ],
//                    [
//                        'actions' => [
//                            'delete-complaint',
//                            'ajax-make-complaint',
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
//                        'roles' => [AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin]
//                    ],
//                    [
//                        'actions' => ['overall-update'],
//                        'allow' => true,
//                        'roles' => [AuthItem::ROLE_SystemAdmin]
//                    ],
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

    public function actionIndex() {
        $searchModel = new VProductionTasksErrorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allErrors');

        return $this->render('indexDefects', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewTaskElecDefectStaff($complaintId) {
        $panelTaskError = ProductionElecTasksError::findOne($complaintId);
        $details = ProductionElecTasksErrorStaff::findAll(['production_elec_tasks_error_id' => $panelTaskError->id]);
        return $this->renderAjax('_taskDefectReadStatus', [
                    'details' => $details,
        ]);
    }
    
    public function actionViewTaskFabDefectStaff($complaintId) {
        $panelTaskError = ProductionFabTasksError::findOne($complaintId);
        $details = ProductionFabTasksErrorStaff::findAll(['production_fab_tasks_error_id' => $panelTaskError->id]);
        return $this->renderAjax('_taskDefectReadStatus', [
                    'details' => $details,
        ]);
    }
}
