<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\employeeHandbook\EhExecOtMeal;
use frontend\models\office\employeeHandbook\EhExecOtMealSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;
use frontend\models\office\employeeHandbook\EhExecOtMealDetail;

/**
 * EhExecOtMealController implements the CRUD actions for EhExecOtMeal model.
 */
class EhExecOtMealController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/eh-exec-ot-meal/');
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Eh_Super],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all EhExecOtMeal models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EhExecOtMealSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EhExecOtMeal model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ehId, $superUser) {
        $master = EhExecOtMeal::find()->where(['eh_master_id' => $ehId])->one();
        if ($master === null) {
            $master = new EhExecOtMeal();
        }

        // Fetch or create detail rows
        $execPersonal = EhExecOtMealDetail::find()
                        ->where(['eh_exec_ot_meal_id' => $master->id, 'type' => EhExecOtMeal::EXEC_PERSONAL_OT_MEAL_CODE])
                        ->one() ?? new EhExecOtMealDetail();

        $eh = EmployeeHandbookMaster::findOne($ehId);
        return $this->render('view', [
                    'master' => $master,
                    'execPersonal' => $execPersonal,
                    'eh' => $eh,
                    'superUser' => $superUser
        ]);
    }

    /**
     * Creates a new EhExecOtMeal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new EhExecOtMeal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing EhExecOtMeal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ehId) {
        if (Yii::$app->request->post()) {
            $execPersonal = new EhExecOtMealDetail();

            $execPersonal->load(Yii::$app->request->post(), 'ExecPersonal');

            $master = EhExecOtMeal::find()->where(['eh_master_id' => $ehId])->one();
            if ($master === null) {
                $master = new EhExecOtMeal();
                $master->eh_master_id = $ehId;
                $master->save();
            }

            $execPersonal->eh_exec_ot_meal_id = $master->id;
            $execPersonal->eh_master_id = $ehId;
            $execPersonal->type = EhExecOtMeal::EXEC_PERSONAL_OT_MEAL_CODE;

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($execPersonal->save()) {
                    $transaction->commit();
                    FlashHandler::success("Saved successfully.");
                } else {
                    $transaction->rollBack();
                    FlashHandler::err("Failed to save details.");
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage(), __METHOD__);
                FlashHandler::err("An error occurred while saving: " . $e->getMessage());
            }
        }

        return $this->redirect(['/office/employee-handbook/view', 'id' => $ehId]);
    }

    /**
     * Deletes an existing EhExecOtMeal model.
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
     * Finds the EhExecOtMeal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EhExecOtMeal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EhExecOtMeal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
