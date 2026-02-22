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
use frontend\models\projectproduction\fabrication\ProjectProductionFabricationProgressSearch;
use frontend\models\ProjectProduction\fabrication\ProjectProductionFabricationProgress;

/**
 * MaterialBqController implements the CRUD actions for ProjectProductionPanelFabBqMaster model.
 */
class FabricationDeptController extends Controller {

    CONST mainViewPath = "/projectproduction/fabrication/";

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

    public function actionIndexFabricationProjectList() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render($this::mainViewPath . 'indexFabricationProjectList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewProductionFabricationProgress($projId) {
        $model = ProjectProductionMaster::findOne($projId);
        return $this->render($this::mainViewPath . 'viewProductionFabricationProgress', [
                    'model' => $model,
        ]);
    }

    public function actionStartFabricationProcess($projId) {
        $panelIds = Yii::$app->request->post('itemCheckbox');
        foreach ((array) $panelIds as $panelId) {
            ProjectProductionFabricationProgress::initiateProgress($panelId);
        }
        FlashHandler::success("Panel(s) process initiated.");

        return $this->redirect(['view-production-fabrication-progress', 'projId' => $projId]);
    }

    /**
     * Update panel fabrication progress using ajax
     */
    public function actionAjaxUpdateFabricationProgress($id) {
        $model = ProjectProductionFabricationProgress::find()->where(['panel_id' => $id])->one();
        if ($model->load(Yii::$app->request->post())) {
            $model->processProgressChanges();
            return $this->redirect(['view-production-fabrication-progress','projId'=>$model->panel->proj_prod_master]);
        }

        return $this->renderAjax($this::mainViewPath . '_formFabricationProgress', [
                    'model' => $model
        ]);
    }

}
