<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\leave\LeaveMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
//use frontend\models\notification\NotificationMaster;
use common\models\myTools\FlashHandler;
use frontend\models\office\leave\RefLeaveType;
use frontend\models\office\leave\RefLeaveSection;
use frontend\models\office\leave\LeaveStatus;
use frontend\models\cron\CronEmail;

/**
 * LeaveController implements the CRUD actions for LeaveMaster model.
 */
class LeaveController extends Controller {

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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all LeaveMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeaveMaster model.
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
     * Updates an existing LeaveMaster model.
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
     * Deletes an existing LeaveMaster model.
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
     * Finds the LeaveMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LeaveMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = LeaveMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * *********** Render ***************************
     */
    public function actionPersonalLeave() {

        $calendar = new \frontend\models\common\fullcalendar\GetEvents();
        $data = $calendar->getCompleteCalendar();
        $leaveStatus = LeaveStatus::getPersonalLeaveStatus(Yii::$app->user->identity->id, date("Y"));
        $leaveHistory = \frontend\models\working\leavemgmt\VMasterLeaveBreakdown::find()->where(['requestor_id' => Yii::$app->user->identity->id])
                ->andWhere(['!=', 'leave_type_code', RefLeaveType::codeTravel])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        $reliefHistory = \frontend\models\working\leavemgmt\VMasterLeaveBreakdown::find()->where(['relief_user_id' => Yii::$app->user->identity->id, 'leave_status' => LeaveMaster::STATUS_Approved])
                ->orderBy(['id' => SORT_DESC])
                ->all();

        return $this->render('personalLeave', [
                    'data' => $data,
                    'leaveStatus' => $leaveStatus,
                    'leaveHistory' => $leaveHistory,
                    'reliefHistory' => $reliefHistory
        ]);
    }

    public function actionWorkTravelReq() {

        $calendar = new \frontend\models\common\fullcalendar\GetEvents();
        $data = $calendar->getCompleteCalendar();
        $leaveHistory = \frontend\models\working\leavemgmt\VMasterLeaveBreakdown::find()->where(['requestor_id' => Yii::$app->user->identity->id, 'leave_type_code' => RefLeaveType::codeTravel])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        $reliefHistory = \frontend\models\working\leavemgmt\VMasterLeaveBreakdown::find()->where(['relief_user_id' => Yii::$app->user->identity->id, 'leave_status' => LeaveMaster::STATUS_Approved, 'leave_type_code' => "travel"])
                ->orderBy(['id' => SORT_DESC])
                ->all();

        $formType = RefLeaveType::codeTravel;
        return $this->render('workTravelReq', [
                    'data' => $data,
                    'leaveHistory' => $leaveHistory,
                    'reliefHistory' => $reliefHistory,
                    'formType' => $formType
        ]);
    }

    /**
     * 
     */
    public function actionGetLeaveCalendar() {
        $calendar = new \frontend\models\common\fullcalendar\GetEvents();
        $data = $calendar->getCompleteCalendar();
        return $this->render('_leaveCalendar', [
                    'data' => $data
        ]);
    }

    public function actionShowLeaveWorklist() {
        $id = Yii::$app->request->get('id');
        $model = \frontend\models\office\leave\VMasterLeave::find()->where('id=' . $id)->one();
        if ($model->requestor_id != Yii::$app->user->id) {
            return 'Access denied!';
        }

//        $delegateList = \frontend\models\working\leavemgmt\LeaveDelegateList::find()->where(['leave_id' => $id])->all();
        $superior = \common\models\User::findOne($model->superior_id);

        return $this->renderAjax('_leaveWorklist', [
                    'leave' => $model,
//                    'delegateList' => $delegateList,
                    'superior' => $superior
        ]);
    }

    /**
     * Creates a new LeaveMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new LeaveMaster();

        $user = \common\models\User::findOne(['id' => Yii::$app->user->id]);
        $model->superior_id = Yii::$app->user->identity->superior_id;
        if (!$model->superior_id) {
            FlashHandler::err("Superior not assigned. Please contact HR immediately.");

            return $this->redirect('personal-leave');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->session->get('insertLeaveRecord') == true && $model->validate()) {
                Yii::$app->session->set('insertLeaveRecord', false);
                $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

                if ($model->processAndSave()) {
                    FlashHandler::success("Leave application request sent!");

                    if ($model->relief_user_id && $model->leave_status == LeaveMaster::STATUS_GetReliefApproval) {
                        CronEmail::bodyLeaveRequestRelief($model->id, 'Relief');
                    }
                    if (!$model->relief_user_id && $model->leave_status == LeaveMaster::STATUS_GetHrApproval) {
                        CronEmail::bodyLeaveRequestHr($model->id, 'HR leave approval');
                    }
                }
                return $this->redirect('personal-leave');
            }
        }
        Yii::$app->session->set('insertLeaveRecord', true);
        if ($user->sex == \frontend\models\common\RefUserSex::CODE_MALE) {
            $leavetypelist = RefLeaveType::getDropDownListMaleWithoutWorkTravel();
        } else {
            $leavetypelist = RefLeaveType::getDropDownListFemaleWithoutWorkTravel();
        }

        return $this->render('create', [
                    'model' => $model,
                    'leaveTypeList' => $leavetypelist,
                    'leaveSectionList' => RefLeaveSection::getDropDownList(),
                    'userList' => \common\models\User::getActiveDropDownListExcludeOne($user->id)
        ]);
    }

    public function actionApplyWorkTravel() {
        $model = new LeaveMaster();
        $user = \common\models\User::findOne(['id' => Yii::$app->user->id]);
        $model->superior_id = Yii::$app->user->identity->superior_id;
        if (!$model->superior_id) {
            FlashHandler::err("Superior not assigned. Please contact HR immediately.");

            return $this->redirect('work-travel-req');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->session->get('insertLeaveRecord') == true && $model->validate()) {
                Yii::$app->session->set('insertLeaveRecord', false);
                $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
                $model->leave_type_code = RefLeaveType::codeTravel;

                if ($model->processAndSave()) {
                    FlashHandler::success("Work travelling application request sent!");

                    if ($model->relief_user_id && $model->leave_status == LeaveMaster::STATUS_GetReliefApproval) {
                        CronEmail::bodyLeaveRequestRelief($model->id, 'Relief');
                    }
                    if (!$model->relief_user_id && $model->leave_status == LeaveMaster::STATUS_GetHrApproval) {
                        CronEmail::bodyLeaveRequestHr($model->id, 'HR work traveling approval');
                    }
                }
                return $this->redirect('work-travel-req');
            }
        }
        Yii::$app->session->set('insertLeaveRecord', true);
        if ($user->sex == \frontend\models\common\RefUserSex::CODE_MALE) {
            $leavetypelist = RefLeaveType::getDropDownListMale();
        } else {
            $leavetypelist = RefLeaveType::getDropDownListFemale();
        }

        $formType = RefLeaveType::codeTravel;
        return $this->render('create', [
                    'model' => $model,
                    'leaveTypeList' => $leavetypelist,
                    'leaveSectionList' => RefLeaveSection::getDropDownList(),
                    'userList' => \common\models\User::getActiveDropDownListExcludeOne($user->id),
                    'formType' => $formType
        ]);
    }

    public function actionCancelLeave() {
        if (Yii::$app->request->post()) {
            $id = Yii::$app->request->post('leaveId');
            $remark = Yii::$app->request->post('remarks');

            $leaveMaster = LeaveMaster::findOne($id);
            if ($leaveMaster->cancelLeave($remark)) {
                FlashHandler::success("Leave cancelled!");
//                NotificationMaster::newNotification($leaveMaster->superior_id,
//                        $leaveMaster->requestor->fullname . " cancelled leave request",
//                        '/office/leave/personal-leave');
            } else {
                FlashHandler::err("Fail to cancel, the leave might has been approved by HR");
            }
        }

        return $this->redirect('personal-leave');
    }

    public function actionViewPersonalRelief($userId) {
        
    }

//    public function actionMigrateGenerateLeaveCode() {
//        set_time_limit(0);
//        ini_set('memory_limit', '10G');
//
//        $batchSize = 100;
//        $offset = 0;
//        $totalProcessed = 0;
//
//        do {
//            $transaction = Yii::$app->db->beginTransaction();
//
//            try {
//                $leaveList = LeaveMaster::find()
//                        ->offset($offset)
//                        ->limit($batchSize)
//                        ->all();
//
//                foreach ($leaveList as $leave) {
//                    $leaveCode = $this->generateLeaveCode($leave);
//
//                    if (!$leaveCode || !$leave->save(false)) {
//                        throw new \Exception("Failed to process leave ID: " . $leave->id);
//                    }
//
//                    $totalProcessed++;
//                }
//
//                $transaction->commit();
//                echo "Processed batch: {$totalProcessed} records\n";
//            } catch (\Exception $e) {
//                $transaction->rollback();
//                echo "Error: " . $e->getMessage() . "\n";
//                break; // Stop on error
//            }
//
//            $offset += $batchSize;
//        } while (count($leaveList) === $batchSize);
//
//        echo "Migration completed. Total processed: {$totalProcessed}\n";
//    }
//
//    private function generateLeaveCode($leave) {
//        // Use the leave's creation year, not current year
//        $leaveRecordYear = date("Y", strtotime($leave->created_at));
//
//        if ($leave->leave_type_code === RefLeaveType::codeTravel) {
//            $initialLeaveCode = LeaveMaster::Prefix_WorkTravelReqCode;
//            $runningNo = LeaveMaster::find()
//                            ->where(['leave_type_code' => RefLeaveType::codeTravel])
//                            ->andWhere(['YEAR(created_at)' => $leaveRecordYear])  // Use leave's year
//                            ->andWhere(['IS NOT', 'leave_code', null])
//                            ->count() + 1;
//        } else {
//            $initialLeaveCode = LeaveMaster::Prefix_LeaveCode;
//            $runningNo = LeaveMaster::find()
//                            ->where(['!=', 'leave_type_code', RefLeaveType::codeTravel])
//                            ->andWhere(['YEAR(created_at)' => $leaveRecordYear])  // Use leave's year
//                            ->andWhere(['IS NOT', 'leave_code', null])
//                            ->count() + 1;
//        }
//
//        if (strlen($runningNo) < LeaveMaster::runningNoLength) {
//            $runningNo = str_repeat("0", LeaveMaster::runningNoLength - strlen($runningNo)) . $runningNo;
//        }
//
//        $leaveYear = date("y", strtotime($leave->created_at));
//        $leaveMonth = date("m", strtotime($leave->created_at));
//        $leaveCode = $initialLeaveCode . $runningNo . "-" . $leaveMonth . $leaveYear;
//        $leave->leave_code = $leaveCode;
//        return $leaveCode;
//    }
    //year only
    public function actionMigrateGenerateLeaveCode() {
        set_time_limit(0);
        ini_set('memory_limit', '10G');

        $batchSize = 100;
        $offset = 0;
        $totalProcessed = 0;

        do {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $leaveList = LeaveMaster::find()
                        ->offset($offset)
                        ->limit($batchSize)
                        ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]) // Important: Process in chronological order
                        ->all();

                foreach ($leaveList as $leave) {
                    // First, temporarily set leave_code to null to recalculate properly
                    $oldLeaveCode = $leave->leave_code;
                    $leave->leave_code = null;

                    // Get the creation year from the leave record
                    $leaveRecordYear = date("Y", strtotime($leave->created_at));

                    if ($leave->leave_type_code === RefLeaveType::codeTravel) {
                        $initialLeaveCode = LeaveMaster::Prefix_WorkTravelReqCode;

                        // Count ALL travel leaves created in the same year (regardless of leave_code)
                        // that were created BEFORE this current leave (chronological order)
                        $runningNo = LeaveMaster::find()
                                        ->where(['leave_type_code' => RefLeaveType::codeTravel])
                                        ->andWhere(['YEAR(created_at)' => $leaveRecordYear])
                                        ->andWhere(['<=', 'created_at', $leave->created_at]) // All leaves up to this point
                                        ->andWhere(['<', 'id', $leave->id]) // Also use ID for tie-breaking
                                        ->count() + 1;
                    } else {
                        $initialLeaveCode = LeaveMaster::Prefix_LeaveCode;

                        // Count ALL non-travel leaves created in the same year (regardless of leave_code)
                        // that were created BEFORE this current leave (chronological order)
                        $runningNo = LeaveMaster::find()
                                        ->where(['!=', 'leave_type_code', RefLeaveType::codeTravel])
                                        ->andWhere(['YEAR(created_at)' => $leaveRecordYear])
                                        ->andWhere(['<=', 'created_at', $leave->created_at]) // All leaves up to this point
                                        ->andWhere(['<', 'id', $leave->id]) // Also use ID for tie-breaking
                                        ->count() + 1;
                    }

                    if (strlen($runningNo) < LeaveMaster::runningNoLength) {
                        $runningNo = str_repeat("0", LeaveMaster::runningNoLength - strlen($runningNo)) . $runningNo;
                    }

                    $leaveYear = date("y", strtotime($leave->created_at));
                    $leaveMonth = date("m", strtotime($leave->created_at));
                    $leaveCode = $initialLeaveCode . $runningNo . "-" . $leaveMonth . $leaveYear;
                    $leave->leave_code = $leaveCode;

                    if (!$leave->save(false)) {
                        throw new \Exception("Failed to process leave ID: " . $leave->id);
                    }

                    $totalProcessed++;
                }

                $transaction->commit();
                echo "Processed batch: {$totalProcessed} records\n";
            } catch (\Exception $e) {
                $transaction->rollback();
                echo "Error: " . $e->getMessage() . "\n";
                break; // Stop on error
            }

            $offset += $batchSize;
        } while (count($leaveList) === $batchSize);

        echo "Migration completed. Total processed: {$totalProcessed}\n";
    }

    //working for continues running number but dont use this affect existing record especially claim module - month and year
//    public function actionMigrateGenerateLeaveCode() {
//        set_time_limit(0);
//        ini_set('memory_limit', '10G');
//
//        $batchSize = 100;
//        $offset = 0;
//        $totalProcessed = 0;
//
//        do {
//            $transaction = Yii::$app->db->beginTransaction();
//
//            try {
//                $leaveList = LeaveMaster::find()
//                        ->offset($offset)
//                        ->limit($batchSize)
//                        ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
//                        ->all();
//
//                foreach ($leaveList as $leave) {
//                    // First, temporarily set leave_code to null to recalculate properly
//                    $oldLeaveCode = $leave->leave_code;
//                    $leave->leave_code = null;
//
//                    // Get the creation year and month from the leave record
//                    $leaveRecordYear = date("Y", strtotime($leave->created_at));
//                    $leaveRecordMonth = date("m", strtotime($leave->created_at));
//
//                    if ($leave->leave_type_code === RefLeaveType::codeTravel) {
//                        $initialLeaveCode = LeaveMaster::Prefix_WorkTravelReqCode;
//
//                        // Count ALL travel leaves created in the same YEAR and MONTH
//                        // that were created BEFORE this current leave
//                        $runningNo = LeaveMaster::find()
//                                        ->where(['leave_type_code' => RefLeaveType::codeTravel])
//                                        ->andWhere(['YEAR(created_at)' => $leaveRecordYear])
//                                        ->andWhere(['MONTH(created_at)' => $leaveRecordMonth])
//                                        ->andWhere(['<=', 'created_at', $leave->created_at])
//                                        ->andWhere(['<', 'id', $leave->id])
//                                        ->count() + 1;
//                    } else {
//                        $initialLeaveCode = LeaveMaster::Prefix_LeaveCode;
//
//                        // Count ALL non-travel leaves created in the same YEAR and MONTH
//                        // that were created BEFORE this current leave
//                        $runningNo = LeaveMaster::find()
//                                        ->where(['!=', 'leave_type_code', RefLeaveType::codeTravel])
//                                        ->andWhere(['YEAR(created_at)' => $leaveRecordYear])
//                                        ->andWhere(['MONTH(created_at)' => $leaveRecordMonth])
//                                        ->andWhere(['<=', 'created_at', $leave->created_at])
//                                        ->andWhere(['<', 'id', $leave->id])
//                                        ->count() + 1;
//                    }
//
//                    if (strlen($runningNo) < LeaveMaster::runningNoLength) {
//                        $runningNo = str_repeat("0", LeaveMaster::runningNoLength - strlen($runningNo)) . $runningNo;
//                    }
//
//                    $leaveYear = date("y", strtotime($leave->created_at));
//                    $leaveMonth = date("m", strtotime($leave->created_at));
//                    $leaveCode = $initialLeaveCode . $runningNo . "-" . $leaveMonth . $leaveYear;
//                    $leave->leave_code = $leaveCode;
//
//                    if (!$leave->save(false)) {
//                        throw new \Exception("Failed to process leave ID: " . $leave->id);
//                    }
//
//                    $totalProcessed++;
//                }
//
//                $transaction->commit();
//                echo "Processed batch: {$totalProcessed} records\n";
//            } catch (\Exception $e) {
//                $transaction->rollback();
//                echo "Error: " . $e->getMessage() . "\n";
//                break;
//            }
//
//            $offset += $batchSize;
//        } while (count($leaveList) === $batchSize);
//
//        echo "Migration completed. Total processed: {$totalProcessed}\n";
//    }
}
