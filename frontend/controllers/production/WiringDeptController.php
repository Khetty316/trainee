<?php

namespace frontend\controllers\production;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionMasterSearch;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqMaster;
use frontend\models\projectproduction\ProjectProductionPanelFabBqMasterSearch;
use yii\web\Controller;
//use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\ProjectProductionPanelFabBqItemsSearch;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqItems;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMasterSearch;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMaster;
use frontend\models\ProjectProduction\RefProdDispatchStatus;
use frontend\models\ProjectProduction\ProjectProductionPanelProcDispatchMaster;
use frontend\models\projectproduction\ProjectProductionPanelProcDispatchMasterSearch;

/**
 * MaterialBqController implements the CRUD actions for ProjectProductionPanelFabBqMaster model.
 */
class WiringDeptController extends Controller {

    CONST mainViewPath = "/projectproduction/wiring/";

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
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

    /**
     * ********************************** Receive Item
     */
    public function actionIndexToReceiveItem() {
        $searchModel = new ProjectProductionPanelProcDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,"indexToReceiveItem");
        return $this->render($this::mainViewPath . 'indexToReceiveItem', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewToReceiveItem($dispatchId) {
        $model = ProjectProductionPanelProcDispatchMaster::findOne($dispatchId);
        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->updateReceiveStatus($post['acceptStatus'])) {
                if ($model->status == RefProdDispatchStatus::STS_Receive) {
                    $model->projProdPanel->checkIfCompleted();
                } else if ($model->status == RefProdDispatchStatus::STS_Reject) {
                    $model->resumeBalance();
                    $model->projProdPanel->updateItemDispatchStatus();
                }
                FlashHandler::success("Updated");
                return $this->redirect(['view-to-receive-item', 'dispatchId' => $dispatchId]);
            }
        }
        return $this->render(self::mainViewPath . 'viewToReceiveItem', [
                    'model' => $model
        ]);
    }

    public function actionIndexAllDispatchedList() {
        $searchModel = new ProjectProductionPanelProcDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(self::mainViewPath . 'indexAllDispatchedList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }


}
