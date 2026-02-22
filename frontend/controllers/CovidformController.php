<?php

namespace frontend\controllers;

use Yii;
use frontend\models\covid\form\CovidStatusForm;
use frontend\models\covid\form\CovidStatusFormSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;

/**
 * CovidformController implements the CRUD actions for CovidStatusForm model.
 */
class CovidformController extends Controller {

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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all CovidStatusForm models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CovidStatusFormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/covid/covidform/indexCovidForm', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CovidStatusForm model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('/covid/covidform/viewCovidForm', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CovidStatusForm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateCovidformPersonal() {

        $model = new CovidStatusForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->processAndSave()) {
                if ($model->to_take_action > 2) {
                    FlashHandler::err($model->toTakeAction->description);
                } else {
                    FlashHandler::success("Form submitted. You are safe to enter the workplace");
                }

                if ($model->spo2 >= 96) {
                    
                } else if ($model->spo2 >= 95) {
                    FlashHandler::errAddon('Your SPO2 level is a bit low. Kindly stay at home and monitor');
                } else if ($model->spo2 >= 93) {
                    FlashHandler::errAddon('Your SPO2 level is low. Kindly seek advice from doctor');
                } else if ($model->spo2 <= 92) {
                    FlashHandler::errAddon('Your SPO2 level is extremely low. Need immediate medical advice!! - Call 999');
                }
//                errAddon
            }
            return $this->redirect(['/']);
        }

        $previousModel = null;

        $max = CovidStatusForm::find()->where(['user_id' => Yii::$app->user->id])->max('id');

        if ($max) {
            $previousModel = CovidStatusForm::findOne($max);
        }

        return $this->render('/covid/covidform/createCovidformPersonal', [
                    'model' => $model,
                    'previousModel' => $previousModel
        ]);
    }

    /**
     * Updates an existing CovidStatusForm model.
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
     * Deletes an existing CovidStatusForm model.
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
     * Finds the CovidStatusForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CovidStatusForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CovidStatusForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['covid_result_file_path'] . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

}
