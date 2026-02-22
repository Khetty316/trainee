<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use frontend\models\office\employeeHandbook\EmployeeHandbookMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyFormatter;
use frontend\models\office\employeeHandbook\RefEmployeeHandbookContent;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * EmployeeHandbookController implements the CRUD actions for EmployeeHandbookMaster model.
 */
class EmployeeHandbookController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/employee-handbook/');
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
                        'actions' => ['view-employee-handbook', 'view-content'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'user-manual-super-user'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Eh_Super],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new EmployeeHandbookMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $contentList = RefEmployeeHandbookContent::find()->where(['is_active' => 1])->orderBy(['order' => SORT_ASC])->all();
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'contentList' => $contentList,
                    'superUser' => true
        ]);
    }

    public function actionViewEmployeeHandbook() {
        $user = \common\models\User::findOne(Yii::$app->user->id); 

        if($user->grade === \frontend\models\RefStaffGrade::NONEXEC_CODE){
            $contentList = RefEmployeeHandbookContent::find()->where(['is_active' => 1, 'grade' => 0])->orderBy(['order' => SORT_ASC])->all();
        }else{
            $contentList = RefEmployeeHandbookContent::find()->where(['is_active' => 1])->orderBy(['order' => SORT_ASC])->all();
        }
        
        $model = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();
        return $this->render('view', [
                    'model' => $model,
                    'contentList' => $contentList,
                    'superUser' => false
        ]);
    }

    public function actionCreate() {
        $model = new EmployeeHandbookMaster();

        if ($model->load(Yii::$app->request->post())) {
            $model->edition_date = MyFormatter::fromDateRead_toDateSQL($model->edition_date);

            if ($model->save()) {
                FlashHandler::success("Successfully created a new Employee Handbook!");
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                FlashHandler::err("Creation failed. Please try again.");
                return $this->redirect(['index']);
            }
        }

        return $this->renderAjax('create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->edition_date = MyFormatter::fromDateRead_toDateSQL($model->edition_date);
            if ($model->save()) {
                FlashHandler::success("The details were updated successfully!");
            } else {
                FlashHandler::err("Update failed. Please try again.");
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = EmployeeHandbookMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewContent($id, $contentTypeCode, $superUser) {
        $model = $this->findModel($id);

        $redirectAction = $model->getRedirectAction($id, $contentTypeCode, $superUser);
        if ($redirectAction) {
            return $this->redirect([$redirectAction]);
        }

        FlashHandler::err("Content type not found.");
        return $this->redirect(['index']);
    }
    
    public function actionUserManualSuperUser() {
        $this->layout = false;
        $fileName = EmployeeHandbookMaster::SUPERUSER_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();
        
        return $this->render('/user-manual', [
            'fileUrl' => $fileUrl,
        ]);
    }
}
