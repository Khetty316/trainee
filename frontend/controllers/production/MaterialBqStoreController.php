<?php

namespace frontend\controllers\production;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMaster;
use frontend\models\ProjectProduction\ProjectProductionPanelStoreDispatchMasterSearch;
use frontend\models\projectproduction\ProjectProductionPanelFabBqMasterSearch;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\RefProjProdBqStatus;

/**
 * MaterialBqStoreController implements the CRUD actions for ProjectProductionPanelStoreDispatchMaster model.
 */
class MaterialBqStoreController extends Controller {

    CONST mainViewPath = "/projectproduction/materialbq/store/";

    /**
     * {@inheritdoc}
     */
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

    public function actionIndex() {
        $searchModel = new ProjectProductionPanelStoreDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(self::mainViewPath . 'index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render(self::mainViewPath . 'view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new ProjectProductionPanelStoreDispatchMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(self::mainViewPath . 'create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(self::mainViewPath . 'update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProjectProductionPanelStoreDispatchMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectProductionPanelStoreDispatchMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectProductionPanelStoreDispatchMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectProductionPanelStoreDispatchMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionIndexToDispatch() {
        $searchModel = new ProjectProductionPanelFabBqMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "awaitingDispatch");

        return $this->render(self::mainViewPath . 'indexToDispatch', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexStoreDispatchedList() {
        $searchModel = new ProjectProductionPanelStoreDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(self::mainViewPath . 'indexStoreDispatchedList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexAllBq() {
        $searchModel = new ProjectProductionPanelFabBqMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(self::mainViewPath . 'indexAllBq', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDispatchItems($bqId) {
        $model = new ProjectProductionPanelStoreDispatchMaster();
        $model->fab_bq_master_id = $bqId;
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->dispatch() && $model->saveDispatchItems($post)) {
                $bqMaster = $model->fabBqMaster;
                $bqMaster->updateStatus();
                $transaction->commit();
                FlashHandler::success("Dispatched");
                return $this->redirect(['view-store-dispatch', 'dispatchId' => $model->id]);
            } else {
                $transaction->rollBack();
                FlashHandler::err_getITHelp();
                return $this->redirect(['dispatch-items', 'bqId' => $bqId]);
            }
        }
        $bqMaster = ProjectProductionPanelFabBqMaster::findOne($bqId);

        return $this->render(self::mainViewPath . 'dispatchItems', [
                    'bqMaster' => $bqMaster,
                    'model' => $model
        ]);
    }

    public function actionResponseSetComplete($bqId) {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['/']);
        }

        $bqMaster = ProjectProductionPanelFabBqMaster::findOne($bqId);
        $bqMaster->bq_status = RefProjProdBqStatus::STS_Done;
        $bqMaster->update();

//        $bqId = Yii::$app->request->post('');

        return $this->redirect(['dispatch-items', 'bqId' => $bqId]);
    }

    public function actionViewStoreDispatch($dispatchId) {
        return $this->render(self::mainViewPath . 'viewStoreDispatch', [
                    'model' => $this->findModel($dispatchId),
        ]);
    }

    public function actionAjaxViewStoreDispatch($dispatchId) {
        return $this->renderAjax(self::mainViewPath . '_ajaxViewStoreDispatch', [
                    'model' => $this->findModel($dispatchId),
        ]);
    }

    /* public function actionReceiveItems($dispatchId) {
      $model = ProjectProductionPanelStoreDispatchMaster::findOne($dispatchId);
      $post = Yii::$app->request->post();
      if ($model->load($post)) {
      $transaction = Yii::$app->db->beginTransaction();
      if ($model->dispatch() && $model->saveDispatchItems($post)) {
      $transaction->commit();
      FlashHandler::success("Dispatched");
      return $this->redirect(['view', 'id' => $model->id]);
      } else {
      $transaction->rollBack();
      FlashHandler::err_getITHelp();
      return $this->redirect(['dispatch-items', 'bqId' => $bqId]);
      }
      }
      $bqMaster = ProjectProductionPanelFabBqMaster::findOne($bqId);

      return $this->render(self::mainViewPath . 'dispatchItems', [
      'bqMaster' => $bqMaster,
      'model' => $model
      ]);
      }
     */
}
