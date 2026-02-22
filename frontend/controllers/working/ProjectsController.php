<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\project\MasterProjects;
use frontend\models\working\project\MasterProjectsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectsController implements the CRUD actions for MasterProjects model.
 */
class ProjectsController extends Controller {

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
            ],
        ];
    }

    /**
     * Lists all MasterProjects models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new MasterProjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MasterProjects model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MasterProjects model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($getAjax = false) {
        $model = new MasterProjects();
        if ($model->load(Yii::$app->request->post())) {
            $model->project_code = str_replace(' ', '', strtoupper($model->project_code));
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->project_code]);
            }
        }

        $userList = \common\models\User::getActiveDropDownList();
        if ($getAjax) {
            return $this->renderAjax('create', [
                        'model' => $model,
                        'getAjax' => $getAjax,
                        'userList' => $userList
            ]);
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'getAjax' => $getAjax,
                        'userList' => $userList
            ]);
        }
    }

    public function actionCreateByAjax($getAjax = false) {
        $model = new MasterProjects();

        $return = array('status' => 'success', 'msg' => '');
        if ($model->load(Yii::$app->request->post())) {
            $model->project_code = str_replace(' ', '', strtoupper($model->project_code));

            if ($model->save()) {
                $return['msg'] = 'Created!';
            } else {
                $return['status'] = 'fail';
                if ($model->hasErrors()) {
                    $return['msg'] = ($model->getErrorSummary(false)[0]);
                } else {
                    $return['msg'] = 'error!';
                }
            }
            return \yii\helpers\Json::encode($return);
        }
        $userList = \common\models\User::getActiveDropDownList();
        return $this->renderAjax('create', [
                    'model' => $model,
                    'getAjax' => $getAjax,
                    'userList' => $userList
        ]);
    }

    /**
     * Updates an existing MasterProjects model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $userList = \common\models\User::getActiveDropDownList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->project_code]);
        }

        return $this->render('update', [
                    'model' => $model,
                    'userList' => $userList
        ]);
    }

    /**
     * Deletes an existing MasterProjects model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MasterProjects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MasterProjects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = MasterProjects::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
