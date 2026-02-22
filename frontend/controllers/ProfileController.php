<?php

namespace frontend\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\working\hrdoc\HrEmployeeDocuments;
use common\models\User;

/**
 * Site controller
 */
class ProfileController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['update'],
                        'allow' => false,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionUpdate($id) {
        $model = User::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->processAndSave()) {
                return $this->redirect(['view-profile', 'id' => $model->id]);
            }
        }
        $areaList = \yii\helpers\ArrayHelper::map(\frontend\models\common\RefArea::find()->all(), "area_id", "area_name");

        return $this->render('updateProfile', [
                    'model' => $model,
                    'areaList' => $areaList
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword() {

        $model = new \frontend\models\profile\ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->redirect('view-profile');
        }

        return $this->render('changePassword', [
                    'model' => $model,
        ]);
    }

    public function actionViewProfile() {
        $id = Yii::$app->user->identity->id;
        return $this->render('viewProfile', [
                    'model' => User::findOne($id),
        ]);
    }

    /*     * ************************************************************************************************************************  Personal Document * */

    public function actionViewUserHrDocuments() {
        $model = HrEmployeeDocuments::find()->where('employee_id=' . Yii::$app->user->identity->id)->andWhere('active_sts=1')->all();
        return $this->render('viewUserHRDocuments', [
                    'model' => $model
        ]);
    }

    public function actionSetHrDocRead() {
        $id = Yii::$app->request->get('doc_id');
        if (HrEmployeeDocuments::setToRead($id)) {
            return \yii\helpers\Json::encode(['data' => ['success' => true, 'message' => 'Success']]);
        } else {
            return \yii\helpers\Json::encode(['data' => ['success' => false, 'message' => 'Fail']]);
        }
    }

    public function actionGetDoc($docId) {
        $model = HrEmployeeDocuments::findOne($docId);
        if (Yii::$app->user->identity->id !== $model->employee_id) {
            return "YOU ARE NOT AUTHORIZED TO VIEW THIS DOCUMENT";
        }

        $doc = HrEmployeeDocuments::findOne($docId);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['personaldocument_file_path'] . "$doc->employee_id/" . $doc->filename;
        return Yii::$app->response->sendFile($completePath, substr($doc->filename, 7), ['inline' => true]);
    }

    /*     * **************************************************************************************************************************  Public Document * */

    public function actionViewUserPublicDocuments($status = null) {
        $searchModel = new \frontend\models\working\hrdoc\HrPublicDocumentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, $status);

        return $this->render('viewUserPublicDocuments', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetPublicFile($id) {
        $doc = \frontend\models\working\hrdoc\HrPublicDocuments::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['publicdocument_file_path'] . $doc->filename;

        // Update read status
        $employeeId = Yii::$app->user->identity->id;
        $readModel = \frontend\models\working\hrdoc\HrPublicDocumentsRead::find()
                ->where([
                    'employee_id' => $employeeId,
                    'hr_public_doc_id' => $doc->id,
                ])
                ->one();

        if ($readModel) {
            $readModel->is_read = 1;
            $readModel->read_at = new \yii\db\Expression('NOW()');
            $readModel->save(false);
        }

        return Yii::$app->response->sendFile($completePath, substr($doc->filename, 15), ['inline' => true]);
    }
}
