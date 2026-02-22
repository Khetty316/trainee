<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\appraisal\ShortAppraisalMaster;
use frontend\models\working\appraisal\ShortAppraisalMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShortappraisalController implements the CRUD actions for ShortAppraisalMaster model.
 */
class ShortappraisalController extends Controller {

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
     * Lists all ShortAppraisalMaster models.
     * @return mixed
     */
//    public function actionIndex() {
//        $searchModel = new ShortAppraisalMasterSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('indexShortappraisal', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Displays a single ShortAppraisalMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('viewShortappraisal', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing ShortAppraisalMaster model.
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

        return $this->render('updateShortappraisal', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShortAppraisalMaster model.
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
     * Finds the ShortAppraisalMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShortAppraisalMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ShortAppraisalMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * ************************************ PERSONAL START **********************************
     * 
     * 
     */
    public function actionPersonal() {
        $searchModel = new ShortAppraisalMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'personal');

        return $this->render('indexShortappraisalPersonal', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new ShortAppraisalMaster();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                return $this->redirect(['personal']);
            }
        }

        return $this->render('createShortappraisal', [
                    'model' => $model,
        ]);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ PERSONAL ENDS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * ************************************ ADMIN START **********************************
     * 
     * 
     */
    public function actionAdmin() {
        $searchModel = new ShortAppraisalMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexShortappraisal', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ADMIN ENDS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
}
