<?php

namespace frontend\controllers;

use Yii;
use frontend\models\bom\VStockDispatchMaster;
use frontend\models\bom\VStockDispatchMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VStockDispatchMasterController implements the CRUD actions for VStockDispatchMaster model.
 */
class VStockDispatchMasterController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

//    /**
//     * Lists all VStockDispatchMaster models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $searchModel = new VStockDispatchMasterSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single VStockDispatchMaster model.
//     * @param integer $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//    /**
//     * Creates a new VStockDispatchMaster model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new VStockDispatchMaster();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->dispatch_id]);
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Updates an existing VStockDispatchMaster model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->dispatch_id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Deletes an existing VStockDispatchMaster model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the VStockDispatchMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VStockDispatchMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VStockDispatchMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
