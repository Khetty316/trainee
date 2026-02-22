<?php

namespace frontend\controllers;

use Yii;
use frontend\models\covid\testkit\CovidTestkitInventory;
use frontend\models\covid\testkit\CovidTestkitInventorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\covid\testkit\CovidTestkitRecordSearch;
use common\models\myTools\FlashHandler;
use frontend\models\covid\testkit\CovidTestkitRecord;

/**
 * CovidtestkitController implements the CRUD actions for CovidTestkitInventory model.
 */
class CovidtestkitController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CovidTestkitInventory models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CovidTestkitInventorySearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "summary");

        return $this->render('/covid/covidtestkit/indexCovidTestkitInventory', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexDetail() {
        $searchModel = new CovidTestkitInventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "detail");

        return $this->render('/covid/covidtestkit/indexCovidTestkitInventoryDetail', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexCovidTestkitTransferredDetail() {
//        $searchModel = new CovidTestkitInventorySearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "detail");


        $searchModel = new CovidTestkitRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('/covid/covidtestkit/indexCovidTestkitTransferredDetail', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CovidTestkitInventory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('/covid/covidtestkit/viewCovidTestkitInventory', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CovidTestkitInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CovidTestkitInventory();

        if ($model->load(Yii::$app->request->post())) {

            $model->record_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($model->record_date);
            $model->brand = trim($model->brand);
            $model->confirm_status = 1;
            $model->created_by = Yii::$app->user->id;
            if ($model->save(false)) {
                FlashHandler::success("Stock in success!");

                return $this->redirect(['index', 'id' => $model->id]);
            }
        }
        return $this->render('/covid/covidtestkit/createCovidTestkitInventory', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new CovidTestkitInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionStockout() {
        $model = new CovidTestkitInventory();

        if ($model->load(Yii::$app->request->post())) {
            $model->record_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($model->record_date);
            $model->brand = trim($model->brand);
            $model->total_movement = $model->total_movement * -1;
            $model->created_by = Yii::$app->user->id;

            if ($model->save()) {
                FlashHandler::success("Stock out, waiting for acceptance.");
                return $this->redirect(['index', 'id' => $model->id]);
            }
        }
        return $this->render('/covid/covidtestkit/stockoutCovidTestkitInventory', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing CovidTestkitInventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('/covid/covidtestkit/updateCovidTestkitInventory', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CovidTestkitInventory model.
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
     * Finds the CovidTestkitInventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CovidTestkitInventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CovidTestkitInventory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPersonalReceiveTestkit() {
        $searchModel = new CovidTestkitInventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "personalReceiveTestkit");

        $searchModel2 = new CovidTestkitRecordSearch();
        $dataProvider2 = $searchModel2->search(Yii::$app->request->queryParams, "personalReceiveTestkit");

        return $this->render('/covid/covidtestkit/personalReceiveTestkit', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'searchModel2' => $searchModel2,
                    'dataProvider2' => $dataProvider2,
        ]);
    }

//    receive-testkit?invId=' . $model->id;
    public function actionReceiveTestkit($invId) {
        if (Yii::$app->request->post()) {
            // confirm inventory record
            $model = CovidTestkitInventory::find()->where(["id" => $invId, 'confirm_status' => 0])->one();
            if ($model) {
                $model->confirm_status = 1;
                if ($model->update(false)) {
                    // create record in covid testkit record
                    $size = $model->total_movement * -1;
                    for ($x = 0; $x < $size; $x++) {
                        $record = new \frontend\models\covid\testkit\CovidTestkitRecord();
                        $record->createFromInventory($model);
                    }
                    FlashHandler::success("Test-Kit Received");
                }
            } else {
                FlashHandler::err("Record outdated");
            }
        }

        return $this->redirect('personal-receive-testkit');
    }

    public function actionPersonalUpdate() {
        if (Yii::$app->request->post()) {
            $id = Yii::$app->request->post('testkitid');
            $remark = Yii::$app->request->post('remark');
            $model = CovidTestkitRecord::findOne($id);
//            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            $model->scannedFile = \yii\web\UploadedFile::getInstanceByName('CovidTestkitRecord[scannedFile]');
            $model->personalUpdate($remark);
        } else {
            FlashHandler::err_getHelp();
        }

        return $this->redirect('personal-receive-testkit');
    }

}
