<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormAttendance;
use frontend\models\test\TestFormAttendanceSearch;
use frontend\models\test\TestDetailAttendance;
use frontend\models\test\TestItemAttendees;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * AttendanceController implements the CRUD actions for TestFormAttendance model.
 */
class AttendanceformController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/attendance');
    }

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    /**
     * Lists all TestFormAttendance models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormAttendance::findOne($id);
        $master = $model->testMaster;
        $searchModel = new \frontend\models\test\TestDetailAttendanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, 'singleAttendance', $model->id);

        return $this->render('index', [
                    'model' => $model,
                    'master' => $master,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEditAttendanceList($id) {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $attendances = $req->post('testDetailAttendance');
            foreach ($attendances as $attendance) {
                if ($attendance['attendeeId']) {
                    $attend = TestDetailAttendance::find()->where(['id' => $attendance['attendeeId'], 'form_attendance_id' => $id])->one();
                } else {
                    $attend = new TestDetailAttendance();
                }
                if ($attendance['toDelete'] == 1) {
                    $attend->delete();
                } else {
                    $attend->form_attendance_id = $id;
                    $attend->name = ucwords(strtolower($attendance['attendeeName']));
                    $attend->org = ucwords(strtolower($attendance['attendeeOrg']));
                    $attend->designation = ucwords(strtolower($attendance['attendeeDesign']));
                    $attend->role = ucwords(strtolower($attendance['attendeeRole']));
                    $attend->signature = $attendance['attendeeSign'];
                    if ($attend->isNewRecord) {
                        $attend->save();
                        $this->actionProcessAttendees($attend);
                    } else {
                        $attend->update(false);
                    }
                }
            }
            \common\models\myTools\FlashHandler::success("Saved.");
            return $this->redirect(["index", 'id' => $id]);
        }
        $models = TestDetailAttendance::find()->where(['form_attendance_id' => $id])->orderBy(['name' => SORT_ASC])->all();
        $testForm = TestFormAttendance::findOne($id);
        $master = $testForm->testMaster;
        return $this->render('editAttendanceList', [
                    'attendanceList' => $models,
                    'testForm' => $testForm,
                    'master' => $master,
                    'userList' => TestItemAttendees::getAutoCompleteList(),
        ]);
    }

    private function actionProcessAttendees($data) {
        $attendee = TestItemAttendees::find()->where(['name' => $data->name, 'org' => $data->org, 'designation' => $data->designation])->one();

        if (!$attendee) {
            $model = new TestItemAttendees();
            $model->name = $data->name;
            $model->org = $data->org;
            $model->designation = $data->designation;
            $model->role = $data->role;
            return $model->save();
        }
    }

    public function actionAjaxAddAttendanceItem($key) {
        $attendance = new TestDetailAttendance();
        return $this->renderPartial('_formAttendanceItem', [
                    'attendance' => $attendance,
                    'key' => $key,
                    'userList' => TestDetailAttendance::getAutoCompleteList(),
        ]);
    }

    public function actionAttendanceStatus($id, $sts) {
        $model = TestFormAttendance::findOne($id);
        $master = $model->testMaster;

        $model->status = $sts;

        if ($model->update(false)) {

            if ($model->status == \frontend\models\test\RefTestStatus::STS_IN_TESTING) {
                \common\models\myTools\FlashHandler::success('Insulation and Hipot Test Start');
                return $this->redirect(['index', 'id' => $id]);
            } else {
                \common\models\myTools\FlashHandler::success('Attendance List Status Updated');
                return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
            }
        } else {
            \common\models\myTools\FlashHandler::err('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionDeleteForm($id) {
        $model = TestFormAttendance::findOne($id);
        $master = $model->testMaster;
        $details = $model->testDetailAttendances;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($details as $detail) {
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            if (!$model->delete()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            FlashHandler::success('Attendance form deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
        }
        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

}
