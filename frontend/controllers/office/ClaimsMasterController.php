<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\claim\ClaimsMaster;
use frontend\models\office\claim\ClaimsMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\claim\RefClaimType;
use frontend\models\office\claim\VClaimMasterDetails;

/**
 * ClaimsMasterController implements the CRUD actions for ClaimsMaster model.
 */
class ClaimsMasterController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/claims-master/');
    }

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
     * Lists all ClaimsMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClaimsMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ClaimsMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new VClaimMasterDetails();
        $user = \common\models\User::findOne(['id' => Yii::$app->user->id]);
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                             \common\models\myTools\Mydebug::dumpFileW(Yii::$app->request->post());

            }

//            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
//            if ($model->processAndSave()) {
//                FlashHandler::success("Record Saved!");
//            } else {
//                FlashHandler::err_getITHelp();
//            }
//            return $this->redirect(['index', 'id' => $model->id]);
        }

        $claimTypeList = RefClaimType::getDropDownList();
        $superior = Yii::$app->user->identity->superior;
        return $this->render('create', [
                    'model' => $model,
                    'claimTypeList' => $claimTypeList,
                    'superior' => $superior,
                    'userList' => \common\models\User::getActiveDropDownListExcludeOne($user->id)
        ]);
    }

    /**
     * Updates an existing ClaimsMaster model.
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

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClaimsMaster model.
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
     * Finds the ClaimsMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClaimsMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ClaimsMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
