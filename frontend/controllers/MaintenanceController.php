<?php

namespace frontend\controllers;

use Yii;
use frontend\models\PreventiveMaintenanceMaster;
use frontend\models\PreventiveMaintenanceMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;
use common\modules\auth\models\AuthItem;

/**
 * MaintenanceController implements the CRUD actions for PreventiveMaintenanceMaster model.
 */
class MaintenanceController extends Controller {

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
                        'roles' => [AuthItem::Module_CMMS]
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

    public function actionIndex() {
        $models = PreventiveMaintenanceMaster::find()->orderBy('id DESC')->asArray()->all();

        return $this->render('index', [
                    'models' => $models,
                    'modelsArray' => json_encode($models)
        ]);
    }

    public function actionView($id) {
        $model = PreventiveMaintenanceMaster::findOne($id);

        return $this->renderAjax('_viewEquipment', [
                    'model' => $model
        ]);
    }

    public function actionAddEquipment() {
        $model = new PreventiveMaintenanceMaster();

        if ($model->load(Yii::$app->request->post())) {
            $model->next_service_date = MyFormatter::fromDateRead_toDateSQL($model->next_service_date);
            if ($model->save()) {
                FlashHandler::success('Saved');
                $this->redirect(['index']);
            }
        }
        return $this->renderAjax('_formEquipment', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = PreventiveMaintenanceMaster::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->next_service_date = MyFormatter::fromDateRead_toDateSQL($model->next_service_date);
            if ($model->save()) {
                FlashHandler::success('Saved');
                $this->redirect(['index']);
            }
        }
        return $this->renderAjax('_formEquipment', [
                    'model' => $model,
        ]);
    }

    public function actionDuplicate($id) {
        $refModel = PreventiveMaintenanceMaster::findOne($id);
        $model = new PreventiveMaintenanceMaster();

        if ($model->load(Yii::$app->request->post())) {
            $model->next_service_date = MyFormatter::fromDateRead_toDateSQL($model->next_service_date);
            if ($model->save()) {
                FlashHandler::success('Saved');
                $this->redirect(['index']);
            }
        }
        $model->attributes = $refModel->attributes;
        return $this->renderAjax('_formEquipment', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $model = PreventiveMaintenanceMaster::findOne($id);

        if ($model->delete()) {
            FlashHandler::success('Equipment Deleted');
        }
        return $this->redirect(['index']);
    }

}
