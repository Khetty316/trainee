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

/**
 * MaterialBqController implements the CRUD actions for ProjectProductionPanelFabBqMaster model.
 */
class MaterialBqController extends Controller {

    CONST mainViewPath = "/projectproduction/materialbq/";

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

    public function actionIndexMaterialBq() {
        $searchModel = new ProjectProductionPanelFabBqMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render($this::mainViewPath . 'indexMaterialBq', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexMaterialBqByProjects() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render($this::mainViewPath . 'indexMaterialBqByProjects', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewMaterialBq($id) {
        $bqMaster = new ProjectProductionPanelFabBqMaster();
        $model = ProjectProductionMaster::findOne($id);
        return $this->render($this::mainViewPath . 'viewMaterialBq', [
                    'model' => $model,
                    'bqMaster' => $bqMaster
        ]);
    }

    public function actionCreateMaterialBq($panelId) {
        $model = new ProjectProductionPanelFabBqMaster();
        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            $model->saveBQItems(Yii::$app->request->post());
            FlashHandler::success($model->bqStatus->status_name);
            return $this->redirect(['update-material-bq', 'bqMasterId' => $model->id]);
        }
        $model->proj_prod_panel_id = $panelId;
        $panel = $model->projProdPanel;
        return $this->render($this::mainViewPath . 'createMaterialBq', [
                    'model' => $model,
                    'panel' => $panel
        ]);
    }

    public function actionUpdateMaterialBq($bqMasterId, $fromMain = false) {
        $model = ProjectProductionPanelFabBqMaster::findOne($bqMasterId);

        if (!empty($model->submitted_at)) {
            return $this->redirect(['view-material-bq-items', 'bqId' => $bqMasterId, 'fromMain' => $fromMain]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            $model->saveBQItems(Yii::$app->request->post());
            FlashHandler::success($model->bqStatus->status_name);
            return $this->redirect(['update-material-bq', 'bqMasterId' => $model->id, 'fromMain' => $fromMain]);
        }

        $panel = $model->projProdPanel;
        $viewFile = $fromMain ? "updateMaterialBqFromMain" : "updateMaterialBq";
        return $this->render($this::mainViewPath . $viewFile, [
                    'model' => $model,
                    'panel' => $panel
        ]);
    }

    public function actionAjaxInsertBqItems($itemId = "") {
        $item = $itemId ? ProjectProductionPanelFabBqItems::findOne($itemId) : (new ProjectProductionPanelFabBqItems());
        $unitList = \frontend\models\ProjectProduction\RefProjectItemUnit::getDropDownList();
        return $this->renderAjax($this::mainViewPath . '__ajaxInsertBqItems', [
                    'item' => $item,
                    'unitList' => $unitList
        ]);
    }

    public function actionViewMaterialBqPanel($panelId) {
        $panel = ProjectProductionPanels::findOne($panelId);

        $searchModel = new ProjectProductionPanelFabBqMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'byPanel', array('panelId' => $panelId));
        return $this->render($this::mainViewPath . 'viewMaterialBqPanel', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'panel' => $panel
        ]);
    }

    public function actionViewMaterialBqItems($bqId, $fromMain = false) {
        $bqMaster = ProjectProductionPanelFabBqMaster::findOne($bqId);

        $searchModel = new ProjectProductionPanelFabBqItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'byBq', array('bqId' => $bqId));

        $items = $bqMaster->projectProductionPanelFabBqItems;
        $viewFile = $fromMain ? "viewMaterialBqItemsFromMain" : "viewMaterialBqItems";
        return $this->render($this::mainViewPath . $viewFile, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bqMaster' => $bqMaster,
                    'items' => $items
        ]);
    }

    /**
     * ********************************** Receive Item
     * 
     */
    public function actionIndexToReceiveMaterial() {

        $searchModel = new ProjectProductionPanelStoreDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => RefProdDispatchStatus::STS_Dispatched]);
        return $this->render($this::mainViewPath . 'indexToReceiveMaterial', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionToReceiveMaterial($dispatchId) {
        $model = ProjectProductionPanelStoreDispatchMaster::findOne($dispatchId);
        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->updateReceiveStatus($post['acceptStatus'])) {
                if ($model->status == RefProdDispatchStatus::STS_Receive) {
                    $model->fabBqMaster->checkIfCompleted();
                } else if ($model->status == RefProdDispatchStatus::STS_Reject) {
                    $model->resumeBalance();
                    $model->fabBqMaster->updateStatus();
                }
                FlashHandler::success("Updated");
                return $this->redirect(['to-receive-material', 'dispatchId' => $dispatchId]);
            }
        }
        $bqMaster = $model->fabBqMaster;
        return $this->render(self::mainViewPath . 'toReceiveMaterial', [
                    'bqMaster' => $bqMaster,
                    'model' => $model
        ]);
    }

    public function actionIndexAllDispatchedList() {
        $searchModel = new ProjectProductionPanelStoreDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(self::mainViewPath . 'indexAllDispatchedList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * ********************************** General Functions **********************************
     */

    public function actionAjaxGetBqItemHistory($term = "") {
        $data = ProjectProductionPanelFabBqItems::find()
                        ->select(['item_description as label', 'item_description as value', 'item_description as id'])
                        ->where("item_description LIKE '%" . addslashes(trim($term)) . "%'")
                        ->orderBy(['item_description' => SORT_ASC])
                        ->distinct()->asArray()->all();
        return \yii\helpers\Json::encode($data);
    }

}
