<?php

namespace frontend\controllers\production;

use Yii;
use frontend\models\projectproduction\ProjectProductionPanelProcDispatchMasterSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\ProjectProductionPanelSearch;
use frontend\models\ProjectProduction\ProjectProductionPanelProcDispatchMaster;
use common\models\myTools\FlashHandler;

//use ProjectProductionPanel
class ProcurementController extends Controller {

    CONST mainViewPath = "/projectproduction/procurement/";

    public function behaviors() {
        return [
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
        ];
    }

    public function actionIndexPendingOrderList() {
        $searchModel = new ProjectProductionPanelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingOrderList');

        return $this->render(self::mainViewPath . 'indexPendingOrderList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    // View pending order detail, and dispatch
    public function actionViewPendingOrder($panelId) {
        $model = new ProjectProductionPanelProcDispatchMaster();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->dispatch() && $model->saveDispatchItems($post)) {
                $panel = ProjectProductionPanels::findOne($panelId);
                $panel->updateItemDispatchStatus();
                $transaction->commit();
                FlashHandler::success("Dispatched");
                return $this->redirect(['view-pending-order', 'panelId' => $panelId]);
            } else {
                $transaction->rollBack();
                FlashHandler::err_getITHelp();
                return $this->redirect(['view-pending-order', 'panelId' => $panelId]);
            }
        }


        $panel = ProjectProductionPanels::findOne($panelId);
        $model->proj_prod_panel_id = $panelId;
        return $this->render(self::mainViewPath . 'viewPendingOrder', [
                    'model' => $model,
                    'panel' => $panel
        ]);
    }

    // View dispatched list detail
    public function actionAjaxViewProcDispatch($dispatchId) {
        $model = ProjectProductionPanelProcDispatchMaster::findOne($dispatchId);
        return $this->renderAjax(self::mainViewPath . '_ajaxViewProcDispatch', [
                    'model' => $model,
        ]);
    }

    public function actionIndexProcDispatchedList() {
        $searchModel = new ProjectProductionPanelProcDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render(self::mainViewPath . 'indexProcDispatchedList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewProcDispatched($dispatchId) {
        $model = ProjectProductionPanelProcDispatchMaster::findOne($dispatchId);
        return $this->render(self::mainViewPath . 'viewProcDispatched', [
                    'model' => $model
        ]);
    }

}
