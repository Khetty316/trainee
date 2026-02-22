<?php

namespace frontend\controllers;

use Yii;
use frontend\models\attendance\MonthlyAttendance;
use frontend\models\attendance\VMonthlyAttendance;
use frontend\models\attendance\BulkProcessMonthlyAttendance;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\myTools\FlashHandler;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyCommonFunction;

/**
 * AttendanceController implements the CRUD actions for MonthlyAttendance model.
 */
class AttendanceController extends Controller {

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Director, AuthItem::ROLE_HR_Senior, AuthItem::ROLE_SystemAdmin,AuthItem::Module_attendance]
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

    public function actionIndex($year = null, $month = null, $err = false) {
        $errorNames = $err ? Yii::$app->session->get('errorNames') : null;
        $yearParam = $year ?? date('y');
        $monthParam = $month ?? date('m');

        $models = VMonthlyAttendance::find()->where(['year' => $yearParam, 'month' => $monthParam])->orderBy('fullname')->asArray()->all();

        return $this->render('index', [
                    'models' => json_encode($models),
                    'year' => $yearParam,
                    'month' => $monthParam,
                    'yearList' => MyCommonFunction::getYearListFromTable('v_monthly_attendance', 'year'),
                    'monthList' => MyCommonFunction::getMonthListArray(),
                    'errorNames' => $errorNames
        ]);
    }

    public function actionView($id) {
        $model = VMonthlyAttendance::findOne($id);
        return $this->renderAjax('view', [
                    'model' => $model,
        ]);
    }

    public function actionCreate($month, $year) {
        $model = new MonthlyAttendance();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'year' => $year, 'month' => $month]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->renderAjax('_form', [
                    'model' => $model,
                    'year' => intval($year),
                    'month' => $month,
                    'userList' => \common\models\User::getActiveDropDownList(),
                    'monthList' => MyCommonFunction::getMonthListArray(),
                    'new' => true
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index', 'year' => $model->year, 'month' => $model->month]);
        }

        return $this->render('update', [
                    'model' => $model,
                    'userList' => \common\models\User::getActiveDropDownList(),
                    'monthList' => MyCommonFunction::getMonthListArray(),
        ]);
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $name = $model->user->fullname;
        $year = $model->year;
        $month = $model->month;
        if ($model->delete()) {
            FlashHandler::success("$name attendance deleted");
        }

        return $this->redirect(['index', 'year' => $year, 'month' => $month]);
    }

    protected function findModel($id) {
        if (($model = MonthlyAttendance::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBulkProcess() {
        $model = new BulkProcessMonthlyAttendance();
        $post = \Yii::$app->request->post();
        $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

        if ($model->scannedFile) {
            $year = $post['BulkProcessMonthlyAttendance']['year'];
            $month = $post['BulkProcessMonthlyAttendance']['month'];
            $result = $model->uploadFile($year, $month);
            if (is_bool($result)) {
                if ($result) {
                    FlashHandler::success("Monthly Attendance for " . MyCommonFunction::numberToMonthFull($month) . " $year is saved.");
                }
                return $this->redirect(['index',
                            'year' => $year,
                            'month' => $month,
                ]);
            } else {
                Yii::$app->session->set('errorNames', $result);
                return $this->redirect(['index',
                            'year' => $year,
                            'month' => $month,
                            'err' => true
                ]);
            }
        }

        return $this->renderAjax('bulkProcess', [
                    'model' => $model,
                    'yearList' => MyCommonFunction::generateYearList(2020, 2025, false),
                    'monthList' => \common\models\myTools\MyCommonFunction::getMonthListArray()
        ]);
    }

    public function actionCheckAttendanceExist() {
        $request = Yii::$app->request;
        $year = $request->post('year');
        $month = $request->post('month');

        $model = VMonthlyAttendance::findOne(['year' => intval($year), 'month' => intval($month)]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['success' => !(bool) $model];
    }

}
