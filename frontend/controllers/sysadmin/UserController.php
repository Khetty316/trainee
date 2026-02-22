<?php

namespace frontend\controllers\sysadmin;

use Yii;
use common\models\User;
use frontend\models\sysadmin\user\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\modules\auth\models\AuthItem;
use common\modules\auth\models\AuthAssignment;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

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
                        'roles' => [AuthItem::ROLE_SystemAdmin, AuthItem::ROLE_HR_Senior],
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

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $authAssign = \common\modules\auth\models\AuthAssignment::find()->where('user_id=' . $id)->all();

        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'authAssign' => $authAssign
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            //Create auth assignment for superior
            if ($model->superior_id) {
                $this->assignAuthToSuperior($model->superior_id);
            }

            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $tempModel = clone $model;

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($tempModel->superior_id != $model->superior_id) {
//                $this->actionDeleteUserAuth(AuthItem::ROLE_Superior, $tempModel->superior_id);
                $this->assignAuthToSuperior($model->superior_id);
            }
            if ($model->processAndSave()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

//        $areaList = \yii\helpers\ArrayHelper::map(\frontend\models\common\RefArea::find()->all(), "area_id", "area_name");
//        $religionList = ArrayHelper::map(\frontend\models\common\RefUserReligion::find()->orderBy([])->all())
        return $this->render('update', [
                    'model' => $model,
//                    'areaList' => $areaList
        ]);
    }

    private function assignAuthToSuperior($superiorId) {
        $authModel = AuthAssignment::findOne(['item_name' => AuthItem::ROLE_Superior, 'user_id' => $superiorId]);
        if (!$authModel) {
            $authModel = new AuthAssignment();
            $authModel->item_name = AuthItem::ROLE_Superior;
            $authModel->user_id = $superiorId;
            if ($authModel->save()) {
                return true;
            }
        }
        return false;
    }

    public function actionUpdateUserAuth($id) {

        $auth = new AuthAssignment();
        $model = $this->findModel($id);

        if ($auth->load(Yii::$app->request->post())) {
            $auth->save();
            \common\models\myTools\FlashHandler::success("Role added!");
            return $this->redirect(['update-user-auth',
                        'id' => $model->id
            ]);
        }

        $authAssign = AuthAssignment::find()->where('user_id=' . $id)->all();
        $authList = \yii\helpers\ArrayHelper::map(AuthItem::find()->all(), 'name', 'auth_fullname');
        return $this->render('updateUserAuth', [
                    'model' => $model,
                    'authAssign' => $authAssign,
                    'authList' => $authList
        ]);
    }

    public function actionDeleteUserAuth($item_name, $user_id) {
        AuthAssignment::deleteAll(['item_name' => $item_name, 'user_id' => $user_id]);
        return $this->redirect(['update-user-auth',
                    'id' => $user_id
        ]);
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup() {
        $model = new \frontend\models\SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            $this->redirect("index");
        }
        $companyList = \frontend\models\common\RefCompanyGroupList::getDropDownList();
        $employmentTypeList = \frontend\models\common\RefUserEmploymentType::getDropDownList();
        return $this->render('signup', [
                    'model' => $model,
                    'companyList' => $companyList,
                    'employmentTypeList' => $employmentTypeList
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new \frontend\models\PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new \frontend\models\ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token) {
        try {
            $model = new \frontend\models\VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail() {
        $model = new \frontend\models\ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
                    'model' => $model
        ]);
    }

    public function actionActivateuser() {

        $user = User::findOne(Yii::$app->request->get('id'));
        if ($user->activateUser()) {
            \common\models\myTools\FlashHandler::success("User account " . $user->username . " activated.");
        }

        $this->redirect('index');
    }

    public function actionDeactivateuser() {

        $user = User::findOne(Yii::$app->request->get('id'));
        if ($user->deactivateUser()) {
            \common\models\myTools\FlashHandler::success("User account " . $user->username . " deactivated.");
        }

        $this->redirect('index');
    }

    public function actionDeleteuser($id) {
        $user = User::findOne($id);
        if ($user->deleteUser()) {
            \common\models\myTools\FlashHandler::success("User account " . $user->username . " deleted.");
        }

        $this->redirect('index');
    }

}
