<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\leave\LeaveMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\working\leavemgmt\LeaveMonthlySummary;
use frontend\models\working\leavemgmt\VLeaveMonthlySummaryGroup;
use yii\filters\AccessControl;
use frontend\models\working\leavemgmt\LeaveHolidays;
use common\models\User;
use common\models\myTools\FlashHandler;
use frontend\models\notification\NotificationMaster;
use common\models\myTools\MyFormatter;
use frontend\models\working\leavemgmt\LeaveEntitlement;
use \frontend\models\working\leavemgmt\LeaveEntitlementDetails;
use frontend\models\working\leavemgmt\VLeaveEntitlement;
use frontend\models\office\leave\RefLeaveType;
use frontend\models\office\leave\LeaveStatus;
use frontend\models\working\leavemgmt\LeaveDetailBreakdown;
use frontend\models\common\RefUserDesignation;
use frontend\models\office\leave\LeaveCompulsoryMaster;
use frontend\models\office\leave\LeaveCompulsoryDetail;
use common\modules\auth\models\AuthItem;
use frontend\models\cron\CronEmail;
use yii\base\Exception;

/**
 * LeaveController implements the CRUD actions for LeaveMaster model.
 */
class LeavemgmtController extends Controller {

    const STS_Reject = 0;
    const STS_Approve = 1;
    const STS_Delegate = 2;
    const csv_mimetypes = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
    );
    CONST mainViewPath = "/working/leave/";

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
                        'actions' => ['resp-superior-leave-approval', 'superior-leave-approval', 'get-file', 'relief-leave-approval', 'resp-relief-leave-approval'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['resp-director-leave-approval', 'director-leave-approval'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Director],
                    ],
                    [
                        'actions' => ['resp-director-leave-approval', 'director-leave-approval'],
                        'allow' => false,
                        'roles' => [AuthItem::ROLE_HR_Senior],
                    ],
                    [
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_HR_Senior],
                    ],
                    [
                        'actions' => ['hr-all-leave'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_FinanceExecutive],
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
     * *********** Render - Superior ***************************
     */
    public function actionSuperiorLeaveApproval() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superiorApproval');

        return $this->render($this::mainViewPath . 'superiorApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Render - HR - Approval ***************************
     */
    public function actionHrLeaveApproval() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrApproval');
        return $this->render($this::mainViewPath . 'hrApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Render - HR - Pending ***************************
     */
    public function actionHrLeavePending() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrPending');
        return $this->render($this::mainViewPath . 'hrAllPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Render - HR - All ***************************
     */
    public function actionHrAllLeave() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render($this::mainViewPath . 'hrAllLeave', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Render - HR - Monthly Summary ***************************
     */
    public function actionHrLeaveSummary($year = '') {
        if ($year == '') {
            $year = date_format(date_create(), "Y");
        }
        $yearList = \common\models\myTools\MyCommonFunction::getYearListFromTable('leave_entitlement', 'year');

        $months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        $leaveTypes = ["annual", "unpaid", "sick", "others"];
        $finalArray = [];

        $users = VLeaveMonthlySummaryGroup::find()->orderBy('user_fullname')->groupBy(['username'])->all();

        foreach ($users as $user) {
            $keyId = $user["user_id"];
            $finalArray[$keyId]['fullname'] = ucwords(strtolower($user["user_fullname"]));
            $finalArray[$keyId]['staffid'] = $user["staff_id"];
            // $months is an array of ["01", "02", "03",...]
            foreach ($months as $month) {
                // $leaveTypes is an array of ["annual", "unpaid", "sick", "others"]
                foreach ($leaveTypes as $type) {
                    $finalArray[$keyId][$month][$type] = "";
                }
            }
        }

        $rowsDatas = VLeaveMonthlySummaryGroup::find()->where(['leave_confirm_year' => $year])->all();
        foreach ($rowsDatas as $rowsData) {
            $finalArray[$rowsData["user_id"]][$rowsData["leave_confirm_month"]][$rowsData["leave_type_code"]] = $rowsData["days"];
        }

        return $this->render($this::mainViewPath . 'hrMonthlySummary', [
                    'leaveSummarys' => $finalArray,
                    'yearList' => $yearList,
                    "leaveTypes" => $leaveTypes,
                    'intMonth' => $months
        ]);
    }

    /**
     * *********** Render - HR - Show all to be Recorded ***************************
     */
    public function actionHrLeaveToRecord() {
        $searchModel = new \frontend\models\working\leavemgmt\MasterLeaveBreakdownSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrToRecord');

        if (Yii::$app->request->isPost) {
            $breakIds = Yii::$app->request->post('selection');
            foreach ($breakIds as $breakId) {
                $breakDetail = LeaveDetailBreakdown::find()->where(['id' => $breakId])->one();
                $breakDetail->is_recorded = 1;
                $breakDetail->update(false);
            }

            FlashHandler::success("Row/s recorded.");
            return $this->render($this::mainViewPath . 'hrToRecord', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render($this::mainViewPath . 'hrToRecord', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Action Only - HR - Recall Leave ***************************
     * Leave recall by HR after approval
     * @return type
     */
    public function actionRecallLeave($id) {
        if (!\common\models\myTools\MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
            FlashHandler::err("Fail to recall. You have no authority to do so.");
        } else if (Yii::$app->request->isPost) {
            $leaveMaster = LeaveMaster::findOne($id);
            if ($leaveMaster) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Delete leaveDetailBreakdowns
                    foreach ($leaveMaster->leaveDetailBreakdowns as $leaveDetailBreakdown) {
                        if (!$leaveDetailBreakdown->delete()) {
                            throw new Exception("Failed to delete leave detail breakdown.");
                        }
                    }

                    // Attempt to recall the leave
                    if (!$leaveMaster->hrRecallLeave()) {
                        throw new Exception("Failed to recall leave.");
                    }

                    // If everything is successful, commit the transaction
                    $transaction->commit();
                    FlashHandler::success("Leave recalled!");
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    FlashHandler::err("Failed to recall leave. Please contact ICT for help.");
                }
            } else {
                FlashHandler::err("Leave record not found.");
            }
        }
        return $this->redirect('hr-all-leave');
    }

    /**
     * *********** Render - HR - Show All Staff's leave status ***************************
     */
    public function actionHrFinalLeaveSummary($selectYear = "") {

        $userList = \common\models\User::getActiveStaffList();

        if ($selectYear == "") {
            $selectYear = date("Y");
        }

        if ($selectYear == date("Y")) {
            $selectMonth = date("m");
        } else {
            $selectMonth = 12;
        }

        foreach ($userList as $key => $user) {
            $leaveStatus = LeaveStatus::getPersonalLeaveStatus($user['id'], $selectYear);

            if ($leaveStatus) {
                $userList[$key] = array_merge($user, (array) $leaveStatus);
            } else {
                unset($userList[$key]);
            }
        }

        return $this->render($this::mainViewPath . 'hrLeaveSummary', [
                    'userList' => $userList,
                    'selectYear' => $selectYear,
                    'selectMonth' => $selectMonth
        ]);
    }

    /**
     * *********** Render - HR - Show Holiday List***************************
     */
    public function actionHrHolidayList($year = '') {
        if ($year == '') {
            $year = date_format(date_create(), "Y");
        }
        $holidayList = LeaveHolidays::find()->where('year(holiday_date) = ' . $year)->orderBy(['holiday_date' => SORT_ASC])->all();
        $yearMinMax = LeaveHolidays::getMinMaxYear();
        $minYear = $yearMinMax[0];
        $maxYear = $yearMinMax[1];
        if (date("Y") < $minYear) {
            $minYear = date("Y");
        }
        if ((date("Y") + 1) > $maxYear) {
            $maxYear = date("Y") + 1;
        }
        $yearsList = [];

        for ($i = $maxYear; $i >= $minYear; $i--) {
            $yearsList[$i] = "Year $i";
        }
        return $this->render($this::mainViewPath . 'hrHolidayList', [
                    'holidayList' => $holidayList,
                    'yearMinMax' => $yearMinMax,
                    'selectYear' => $year,
                    'yearsList' => $yearsList
        ]);
    }

    /**
     * *********** Render & Edit - HR - Show Holiday List***************************
     * Added by Paul @ 2022-11-21
     */
    public function actionHrEditHolidayList($year) {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $LeaveHolidays = $req->post('leaveHoliday');
            foreach ($LeaveHolidays as $LeaveHoliday) {
                if ($LeaveHoliday['holidayId']) {
                    $leave = LeaveHolidays::findOne($LeaveHoliday['holidayId']);
                } else {
                    $leave = new LeaveHolidays();
                }
                $leaveDate = MyFormatter::fromDateRead_toDateSQL($LeaveHoliday['holidayDate']);
                if ($LeaveHoliday['toDelete'] == 1) {
                    // Else delete
                    $leave->delete();
                } else {
                    $leave->holiday_date = $leaveDate;
                    $leave->holiday_name = $LeaveHoliday['holidayName'];
                    $leave->save();
                }
                $this->checkAndUpdateLeave($leaveDate);
            }
            FlashHandler::success("Saved.");
            return $this->redirect(["hr-holiday-list", 'year' => $year]);
        }
        $models = LeaveHolidays::find()->where('year(holiday_date) = ' . $year)->orderBy(['holiday_date' => SORT_ASC])->all();
        return $this->render($this::mainViewPath . 'hrEditHolidayList', [
                    'holidayList' => $models,
                    'selectYear' => $year
        ]);
    }

    /**
     * Trigerred only when the have leave update
     */
    private function checkAndUpdateLeave($date) {
        $leaveMaster = LeaveMaster::find()->where("'$date' between start_date AND end_date")->all();
        foreach ($leaveMaster as $leave) {
            $leave->recountDays();
        }

        $leaveDetail = LeaveDetailBreakdown::find()->where("'$date' between start_date AND end_date")->all();
        foreach ($leaveDetail as $leave) {
            $leave->recountDays();
        }
    }

    /**
     * *********** Render & Insert(by Ajax) - HR - Edit Holiday List***************************
     * Added by Paul @ 2022-11-21
     * Add rows in Holiday List
     */
    public function actionAjaxAddHolidayItem($key, $year) {
        $holiday = new LeaveHolidays();
        return $this->renderPartial($this::mainViewPath . '__ajaxHolidayItem', [
                    'holiday' => $holiday,
                    'key' => $key,
                    'selectYear' => $year
        ]);
    }

    /**
     * HR - Show Leave Entitlement 
     * Haziq 9/12/2022 Showing leave entitlement for active user only. 
     * Table in view are editable.
     */
    public function actionHrLeaveEntitlement($selectYear = "") {
        if ($selectYear == "") {
            $selectYear = date("Y");
        }
        $noEntitlementUsers = $this->actionGetNoEntitlementUser($selectYear);
        $countNextYearNoEntitlementUser = count($this->actionGetNoEntitlementUser($selectYear + 1));
        $yearsList = \common\models\myTools\MyCommonFunction::getYearListFromTable('leave_entitlement', 'year');
        $refLeaveType = RefLeaveType::getLeaveTypeWithEntitlement();
        $users = VLeaveEntitlement::find()->where(['year' => $selectYear])->orderBy("staff_id")->groupBy("fullname")->asArray()->all();
        $headers = ["Staff Id", "Fullname", "Annual Rollover"];
        $contentDatas = [];

        //Filling in static header for table in view page
        foreach ($refLeaveType as $key => $value) {
            $headers[] = $refLeaveType[$key]["leave_type_name"];
        }

        //Any additional array header can be filled in here inside the $addHeaders array - Will be at the end of array
        $addHeaders = ["Action"];
        foreach ($addHeaders as $addHeader) {
            $headers[] = $addHeader;
        }

        //Building the frame for array
        foreach ($users as $user) {
            $contentDatas[$user["user_id"]]['user_id'] = $user["user_id"];
            $contentDatas[$user["user_id"]]['staff_active_status'] = $user["staff_active_status"];
            $contentDatas[$user["user_id"]]['staff_id'] = $user["staff_id"];
            $contentDatas[$user["user_id"]]['fullname'] = $user["fullname"];
            $contentDatas[$user["user_id"]]['entitle_id'] = "";
            $contentDatas[$user["user_id"]]['annual_rollover'] = "";
            $contentDatas[$user["user_id"]]['annual_bring_next_year_days'] = "";

            foreach ($refLeaveType as $leave) {
                $contentDatas[$user["user_id"]]['leaveTypes'][$leave["leave_type_code"]]['leaveType'] = "";
                $contentDatas[$user["user_id"]]['leaveTypes'][$leave["leave_type_code"]]['leaveDays'] = "";
                $contentDatas[$user["user_id"]]['leaveTypes'][$leave["leave_type_code"]]['detailId'] = "";
            }
        }

        //Filling in the array by injection
        $insertDatas = $userArrays = VLeaveEntitlement::find()->where(['year' => $selectYear, 'leave_type_code' => [RefLeaveType::codeAnnual, RefLeaveType::codeSick]])->asArray()->all();

        foreach ($insertDatas as $inserData) {
            $contentDatas[$inserData["user_id"]]['entitle_id'] = $inserData["entitle_id"];
            $contentDatas[$inserData["user_id"]]['annual_rollover'] = $inserData["annual_bring_forward_days"];
            $contentDatas[$inserData["user_id"]]['annual_bring_next_year_days'] = $inserData['annual_bring_next_year_days'];
            $contentDatas[$inserData["user_id"]]['leaveTypes'][$inserData["leave_type_code"]]['leaveType'] = $inserData["leave_type_code"];
            $contentDatas[$inserData["user_id"]]['leaveTypes'][$inserData["leave_type_code"]]['leaveDays'] = $inserData["days"];
            $contentDatas[$inserData["user_id"]]['leaveTypes'][$inserData["leave_type_code"]]['detailId'] = $inserData["entitle_detail_id"];

            //if starting month is not on January
            if ($inserData['month_start'] != 1) {
                $tempData = "(" . date('M', mktime(0, 0, 0, $inserData['month_start'])) . "-" . date('M', mktime(0, 0, 0, $inserData['month_end'])) . ") " . number_format($inserData['days'], 1);
                $contentDatas[$inserData["user_id"]]['leaveTypes'][$inserData["leave_type_code"]]['leaveDays'] = $tempData;
            }
        }

        //Check if theres more than 1 entitlement details for 1 type of leave in the same year
        foreach ($users as $user) {

            foreach ($refLeaveType as $leave) {
                $multiDetails = $this->checkEntitlementDetailAdjustment($user, $selectYear, $leave);

                if (is_array($multiDetails)) {
                    $monthAndDays = '';

                    foreach ($multiDetails as $detail) {
                        $monthAndDays .= "(" . date('M', mktime(0, 0, 0, $detail['month_start'])) . "-" . date('M', mktime(0, 0, 0, $detail['month_end'])) . ") " . number_format($detail['days'], 1) . "<br>";
                        $contentDatas[$user["user_id"]]['leaveTypes'][$leave["leave_type_code"]]['leaveDays'] = $monthAndDays;
                    }
                }
            }
        }

        //delete usort to sort by user_id
        usort($contentDatas, function ($a, $b) {
            return strcasecmp($a['fullname'], $b['fullname']);
        });

        return $this->render($this::mainViewPath . 'hrLeaveEntitlement', [
                    'contentDatas' => $contentDatas,
                    'headers' => $headers,
                    'refLeaveType' => $refLeaveType,
                    'selectYear' => $selectYear,
                    'yearsList' => $yearsList,
                    'noEntitlementUsers' => $noEntitlementUsers,
                    'noEntitlementNextYear' => ($countNextYearNoEntitlementUser > 5) ? true : false
        ]);
    }

    /**
     * Haziq 2/12/2022 Get active users with no entitlement in the selected year 
     * Updated by Khetty - 3/1/2025
     */
    public function actionGetNoEntitlementUser($selectYear) {
        $subquery1 = (new \yii\db\Query())->from('leave_entitlement')->where(['year' => $selectYear]);
//        $subquery2 = (new \yii\db\Query())
//                ->from('user')
//                ->where(['and', ['=', 'status', 10], ['is not', 'staff_id', null]])
//                ->andWhere(['<=', 'YEAR(date_of_join)', $selectYear]);
        $subquery2 = (new \yii\db\Query())
                ->from('user')
                ->where(['and',
                    ['=', 'status', 10],
                    ['and',
                        ['is not', 'staff_id', null], // Exclude null values
                        ['regexp', 'staff_id', '^[a-zA-Z0-9]+$'] // Allow only alphanumeric values
                    ]
                ])
                ->andWhere([
            'or',
            ['<=', 'YEAR(date_of_join)', $selectYear],
            ['YEAR(date_of_join)' => null]
        ]);

        $data = (new \yii\db\Query())
                ->from(['a' => $subquery1])
                ->rightJoin(['b' => $subquery2], 'a.user_id = b.id')
                ->where(['is', 'a.id', null])
                ->orderBy(['b.fullname' => SORT_ASC]);
        return $data->all();
    }

    /**
     * Haziq 2/12/2022 Edit number of leave days individually 
     */
    public function actionEditEntitlementCell($entitleId, $leaveTypeCode, $year, $id = "") {
        $leaveType = RefLeaveType::find()->where(["leave_type_code" => $leaveTypeCode])->one();
        if ($id) {
            //VleaveEntitlement is for displaying relevant data 
            $vEntitlement = VLeaveEntitlement::find()->where(['entitle_detail_id' => $id])->one();

            //LeaveEntitlementDetails is for editing data
            $entitleDetail = LeaveEntitlementDetails::findOne($id);
        } else {
            $vEntitlement = VLeaveEntitlement::find()->where(['entitle_id' => $entitleId])->one();
            $entitleDetail = new LeaveEntitlementDetails();
        }

        if (Yii::$app->request->post()) {
            $entitleDetail->load(Yii::$app->request->post());
            if ($entitleDetail->isNewRecord) {
                $entitleDetail->leave_entitle_id = $entitleId;
                $entitleDetail->leave_type_code = $leaveTypeCode;
                $entitleDetail->save();
            } else {
                $entitleDetail->update(false);
            }

            FlashHandler::success("Days/Year updated for " . $vEntitlement["fullname"] . "'s " . $leaveType->leave_type_name);
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax($this::mainViewPath . '_formLeaveCellEditable', [
                    'id' => $id,
                    'vEntitlement' => $vEntitlement,
                    'entitleDetail' => $entitleDetail,
                    'leaveType' => $leaveType,
                    'year' => $year
        ]);
    }

    /**
     * Haziq 2/12/2022 Edit number of annual rollover days individually 
     */
    public function actionEditAnnualRollover($id, $selectYear) {
        //VleaveEntitlement is for getting the user id
        $vEntitlement = VLeaveEntitlement::findOne(['user_id' => $id]);

        //LeaveEntitlementDetails is for editing data
        $leaveEntitle = LeaveEntitlement::find()->where(['user_id' => $id])->andWhere(['year' => $selectYear])->one();

        if (Yii::$app->request->isPost) {
            if ($leaveEntitle->load(Yii::$app->request->post())) {
                $leaveEntitle->update(false);
            }

            FlashHandler::success("Annual rollover updated for " . $vEntitlement["fullname"]);
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax($this::mainViewPath . '_formAnnualCellEditable', [
                    'id' => $vEntitlement->user_id,
                    'vEntitlement' => $vEntitlement,
                    'leaveEntitle' => $leaveEntitle,
                    'selectYear' => $selectYear
        ]);
    }

    /**
     * Haziq 22/12/2022 Check if theres more than 1 entitlement details for 1 type of leave in the same year 
     */
    private function checkEntitlementDetailAdjustment($user, $selectYear, $leave) {
        $LeaveTypes = RefLeaveType::getActiveLeaveType();
        $details = VLeaveEntitlement::find()->where(['user_id' => $user['user_id'], 'year' => $selectYear, 'leave_type_code' => $leave['leave_type_code']])->all();
        if (count($details) > 1) {
            return $details;
        } else {
            return false;
        }
    }

    /**
     * Haziq 5/12/2022 Add adjustment to days on leave entitlement details
     */
    public function actionEntitlementDetailAdjustment($id) {
        $post = Yii::$app->request->post();

        //VleaveEntitlement is for displaying relevant data
        $vEntitlement = VLeaveEntitlement::find()->where(['entitle_detail_id' => $id])->one();

        //LeaveEntitlementDetails is for editing data
        $entitleDetail = LeaveEntitlementDetails::findOne($id);

        $multiData = VLeaveEntitlement::find()
                        ->where(['user_id' => $vEntitlement->user_id])
                        ->andwhere(['leave_type_code' => $entitleDetail->leave_type_code])
                        ->andWhere(['year' => $vEntitlement->year])->all();

        if (Yii::$app->request->isPost) {
            $dataCheck = $this->processEntitlementDetailAdjustment($post['entitleDetail']);
            if (is_array($dataCheck)) {
                $post['entitleDetail'] = $dataCheck;
            } else {
                FlashHandler::err($dataCheck);
                return $this->refresh();
            }

            $leaveEntitlement = LeaveEntitlement::findOne($post['leaveEntitlementId']);
            $leaveDetails = LeaveEntitlementDetails::find()->where(['leave_type_code' => $entitleDetail->leave_type_code, 'leave_entitle_id' => $leaveEntitlement->id])->all();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($leaveDetails as $deleteDetail) {
                    if (!$deleteDetail->delete()) {
                        $transaction->rollBack();
                        FlashHandler::err("Adjustment failed, try again or contact IT department.");
                        return $this->redirect(['hr-leave-entitlement', 'selectYear' => $leaveEntitlement->year]);
                    }
                }

                foreach ($post["entitleDetail"] as $newDetails) {
                    if ($newDetails["days"] == null) {
                        continue;
                    }
                    $modelDetail = new LeaveEntitlementDetails();

                    $modelDetail->month_start = $newDetails["monthStart"];
                    $modelDetail->month_end = $newDetails["monthEnd"];
                    $modelDetail->days = $newDetails["days"];
                    $modelDetail->leave_entitle_id = $leaveEntitlement->id;
                    $modelDetail->leave_type_code = $post["leaveType"];

                    if (!$modelDetail->save()) {
                        $transaction->rollBack();
                        FlashHandler::err("Adjustment failed, try again or contact IT department.");
                        return $this->redirect(['hr-leave-entitlement', 'selectYear' => $leaveEntitlement->year]);
                    }
                }
                $transaction->commit();
                FlashHandler::success("Adjustment made for " . $vEntitlement->fullname);
                return $this->redirect(['hr-leave-entitlement', 'selectYear' => $leaveEntitlement->year]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err("Adjustment failed, try again or contact IT department.");
                return $this->redirect(['hr-leave-entitlement', 'selectYear' => $leaveEntitlement->year]);
            }
        }
        return $this->render($this::mainViewPath . 'hrEditLeaveEntitlementDetails', [
                    'id' => $id,
                    'vEntitlement' => $vEntitlement,
                    'entitleDetail' => $multiData,
        ]);
    }

    /**
     * Haziq 5/12/2022 Add row to _formSingleEntitleDetail
     */
    public function actionAjaxAddLeaveAdjustment($key, $year) {
        $newEntitleDetail = new LeaveEntitlementDetails();

        return $this->renderAjax($this::mainViewPath . '__formSingleEntitleDetailSub', [
                    'singleDetail' => $newEntitleDetail,
                    'key' => $key,
                    'year' => $year
        ]);
    }

    /**
     * Haziq 28/12/2022 Processing and validation of POST data from entitlement details adjustment
     */
    public function processEntitlementDetailAdjustment($processArray) {

        //if single element sent but days not filled
        foreach ($processArray as $singleDetail) {
            if ($singleDetail['days'] == null) {
                return "Please fill in days.";
            }
        }

        //Sort the $array given using monthStart column/key
        usort($processArray, function ($item1, $item2) {
            return $item1['monthEnd'] <=> $item2['monthEnd'];
        });

        //Check the endMonth which must ends in December
        if (end($processArray)['monthEnd'] != 12) {
            return "Adjustment must ends in December.";
        }

        $monthFrame = [];
        foreach ($processArray as $eachElement) {
            foreach (range($eachElement['monthStart'], $eachElement['monthEnd']) as $month) {
                array_push($monthFrame, $month);
            }
        }

        //Check if theres overlaping months from inputs
        if (count(array_unique($monthFrame)) < count($monthFrame)) {
            return "Months are overlapping.";
        }

        //Check if theres a gap between month and enable save when startMonth is not January
        $prev = null;
        foreach ($monthFrame as $value) {
            if ($prev !== null && $value - $prev > 1) {
                return "Adjustment made have a gap/s in between Month.";
            }
            $prev = $value;
        }

        return $processArray;
    }

    /**
     * 
     * *********** EXPORT - HR - Export Leave Entitlement into Excel
     */
    public function actionHrLeaveEntitlementExcel($selectYear = "") {
        $userList = \common\models\User::getActiveStaffList();
        if ($selectYear == "") {
            $selectYear = date("Y");
        }

        foreach ($userList as $key => $user) {
            $entitlement = \frontend\models\working\leavemgmt\LeaveEntitlement::find()->where(["year" => $selectYear, 'user_id' => $user['id']])->asArray()->one();
            $userList[$key] = array_merge($user, (array) $entitlement);
        }
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        return $this->renderPartial($this::mainViewPath . 'hrLeaveEntitlementExcel', [
                    'userList' => $userList,
                    'selectYear' => $selectYear,
        ]);
    }

    /**
     *  *********** Render - HR - Get leave entitlement from excel and display
     */
    public function actionHrBatchUploadLeaveEntitlement() {

        $file = \yii\web\UploadedFile::getInstanceByName('excelFile');

        if (!in_array($file->type, self::csv_mimetypes)) {
            FlashHandler::err("File type not supported!");
            return $this->redirect([
                        'hr-leave-entitlement'
            ]);
        }

        $process = new \frontend\models\working\leavemgmt\LeaveEntitlementExcelModel();
        $excelResult = $process->processExcel($file);
        return $this->render($this::mainViewPath . 'hrEditLeaveEntitlement', [
                    'excelResult' => $excelResult,
        ]);
    }

    /**
     *  *********** Process - HR - Update leave entitlement by batch
     */
    public function actionHrBatchUpdateLeaveEntitlement() {

        $processes = new \frontend\models\working\leavemgmt\LeaveEntitlementExcelModel();
        if ($processes->processUpdates()) {
            FlashHandler::success("Update Success!");
        }
        $theYear = $processes->year;

        return $this->redirect([
                    'hr-leave-entitlement',
                    'selectYear' => $theYear
        ]);
    }

    /**
     * Set leave start month for the new staffs
     */
    public function actionSetLeaveStartMonth() {
        $monthlySummary = new LeaveMonthlySummary();
        if ($monthlySummary->load(Yii::$app->request->post())) {
            if ($monthlySummary->setLeaveStartMonth()) {
                $user = User::findOne($monthlySummary->requestor_id);
                $user->leave_adjusted = 1;
                if ($user->update(false)) {
                    FlashHandler::success("Leave start month set!");
                }
            } else {
                FlashHandler::err("Fail to update");
            }
        }
        return $this->redirect('hr-leave-entitlement');
    }

    /**
     * Add entitlement to user 
     * Haziq 22/11/2022 Process tabular data to save when creating one entitlement
     * Updated by Khetty - 3/1/2025
     */
    public function actionAddEntitlementToUser($id, $selectYear) {
        // Get active leave types and extract leave type codes
        $leaveType = RefLeaveType::getActiveLeaveType();
        $leaveTypeCode = array_column($leaveType, 'leave_type_code');

        // Initialize variables
        $leaveEntitle = new \frontend\models\working\leavemgmt\LeaveEntitlement();
        $user = User::findOne($id);
        $view = Yii::$app->request->isAjax ? 'renderAjax' : 'render';

        // Handle form submission
        if ($leaveEntitle->load(Yii::$app->request->post())) {
            // Set default annual bring forward days if empty
            if (empty($leaveEntitle->annual_bring_forward_days)) {
                $leaveEntitle->annual_bring_forward_days = 0;
            }

            // Load and validate multiple entitlement details
            $entitleDetails = $this->createArray(LeaveEntitlementDetails::className());
            LeaveEntitlementDetails::loadMultiple($entitleDetails, Yii::$app->request->post());

            // Handle AJAX validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                                ActiveForm::validateMultiple($entitleDetails),
                                ActiveForm::validate($leaveEntitle)
                );
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($flag = $leaveEntitle->save()) {

                    // Update previous year's entitlement if exists
                    $previousEntitle = LeaveEntitlement::find()->where(['user_id' => $leaveEntitle->user_id, 'year' => $leaveEntitle->year - 1])->one();
                    if (!empty($previousEntitle)) {
                        $previousEntitle->annual_bring_next_year_days = $leaveEntitle->annual_bring_forward_days;
                        if (!($flag = $previousEntitle->update())) {
                            $transaction->rollBack();
                        }
                    }

                    // Save entitlement details
                    foreach ($entitleDetails as $entitleDetail) {
                        $entitleDetail->leave_entitle_id = $leaveEntitle->id;
                        if (!($flag = $entitleDetail->save())) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    FlashHandler::success("Entitlement added for " . $user->fullname);
                    return $this->redirect(['hr-leave-entitlement', 'selectYear' => $selectYear]);
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
            }
        } else if ($leaveEntitle = LeaveEntitlement::find()->where(['and', ['=', 'user_id', $user->id], ['=', 'year', $selectYear - 1]])->one()) {
            //Case when there is a past data , days will remain as the latest one for any multiple entitlement details with the same type
            $entitleDetails = [];
            foreach ($leaveType as $key => $leave) {
                $entitleDetail = null;
                foreach ($leaveEntitle->leaveEntitlementDetails as $entitle) {
                    if ($entitle['leave_type_code'] == $leave['leave_type_code']) {
                        $entitleDetail = $entitle;
                    }
                }
                if ($leave['is_pro_rata'] == 1) {
                    $entitleDetail->month_start = 1;
                }
                $entitleDetails[$key] = $entitleDetail ?: new LeaveEntitlementDetails();
            }
            $leaveEntitle->annual_bring_forward_days = $this->getBringOverDays($user->id, $selectYear - 1);
            $leaveEntitle->year = $selectYear;
        } else {
            //Case when creating new data but no past data
            foreach ($leaveTypeCode as $key => $leaveCode) {
                $entitleDetails[$key] = new LeaveEntitlementDetails();
                $entitleDetails[$key]->setAttribute('leave_type_code', $leaveCode);
                $days = ($leaveType[$key]['default_days'] === null ? 0 : $leaveType[$key]['default_days']);
                $entitleDetails[$key]->setAttribute('days', $days);
            }
        }

        return $this->$view($this::mainViewPath . '_formEntitlement', [
                    'user' => $user,
                    'selectYear' => $selectYear,
                    'leaveType' => $leaveType,
                    'modelEntitle' => (empty($leaveEntitle)) ? new LeaveEntitlement : $leaveEntitle,
                    'modelDetail' => $entitleDetails
        ]);
    }

    /**
     * Haziq 30/12/2022 Edit current year Leave entitlement and details
     */
    public function actionEditUserEntitlement($id) {
        $leaveEntitlement = LeaveEntitlement::findOne($id);
        $user = User::findOne($leaveEntitlement->user_id);
        $leaveEntitlementDetails = $leaveEntitlement->leaveEntitlementDetails;
        $leaveType = RefLeaveType::getActiveLeaveType();
        $view = Yii::$app->request->isAjax ? 'renderAjax' : 'render';

        if ($leaveEntitlement->load(Yii::$app->request->post())) {
            if (empty($leaveEntitlement->annual_bring_forward_days)) {
                $leaveEntitlement->annual_bring_forward_days = 0;
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($leaveEntitlement->save()) {
                    $previousEntitle = LeaveEntitlement::find()->where(['user_id' => $leaveEntitlement->user_id, 'year' => $leaveEntitlement->year - 1])->one();
                    if (!empty($previousEntitle)) {
                        $previousEntitle->annual_bring_next_year_days = $leaveEntitlement->annual_bring_forward_days;
                        if (!$previousEntitle->update()) {
                            $transaction->rollBack();
                        }
                    }

                    foreach (Yii::$app->request->post('LeaveEntitlementDetails') as $key => $postData) {
                        foreach ($leaveEntitlementDetails as $leaveEntitlementDetail) {
                            if ($leaveEntitlementDetail['leave_type_code'] == $postData['leave_type_code']) {
                                $leaveEntitlementDetail->setAttributes($postData);
                                if (!$leaveEntitlementDetail->save()) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                }
                if ($leaveEntitlement->hasErrors()) {
                    $transaction->rollBack();
                } else {
                    $transaction->commit();
                    FlashHandler::success("Entitlement updated for " . $user->fullname);
                    return $this->redirect(['hr-leave-entitlement', 'selectYear' => $leaveEntitlement->year]);
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
            }
        }

        return $this->$view($this::mainViewPath . '_formEntitlement', [
                    'user' => $user,
                    'selectYear' => $leaveEntitlement->year,
                    'leaveType' => $leaveType,
                    'modelEntitle' => $leaveEntitlement,
                    'modelDetail' => $leaveEntitlementDetails
        ]);
    }

    /**
     * Haziq 15/11/2022 Get default bring over days, and annual leave balance from previous year
     */
    private function getBringOverDays($id, $selectYear) {
        $user = User::findOne($id);
        $sysDefaultBringOver = 0;

        if (!$user->designation0) {
            return 0;
        }
        if ($user->designation0->staff_type == \frontend\models\common\RefUserDesignation::TYPE_Executive) {
            $sysDefaultBringOver = (int) \frontend\models\common\RefSystemConfig::getValue_defaultBringOverExec();
        } else if ($user->designation0->staff_type == \frontend\models\common\RefUserDesignation::TYPE_Office) {
            $sysDefaultBringOver = (int) \frontend\models\common\RefSystemConfig::getValue_defaultBringOverOffice();
        }
        $leaveStatus = LeaveStatus::getPersonalLeaveStatus($id, $selectYear);
        $annualBalance = $leaveStatus->annual_balanceCurrentCanApply;
        if ($annualBalance <= 0) {
            return 0;
        } else {
            return ($annualBalance > $sysDefaultBringOver ? $sysDefaultBringOver : $annualBalance);
        }
    }

    /**
     * Haziq 22/11/2022 Create empty array to load tabular data
     */
    public static function createArray($modelClass) {
        $model = new $modelClass;
        $formName = $model->formName();
        $post = Yii::$app->request->post($formName);
        $models = [];

        if ($post && is_array($post)) {
            foreach ($post as $key => $value) {
                $models[] = new $modelClass;
            }
        }

        return $models;
    }

    /**
     * Calculate days adjustment for each entitlement
     * Haziq 22/11/2022 Using ajax call to fill in days deduct field in form
     */
    public function actionCalculateDaysDeduct($id) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->controller->enableCsrfValidation = false;
            $data = Yii::$app->request->post();
            $leaveDays = $data["leaveDays"];
            $user = User::findOne($id);
            $month = date("m", strtotime($user["date_of_join"]));

            $daysDeduct = (($month - 1) / 12) * floatval($leaveDays);

            if (floatval($daysDeduct) > (intval($daysDeduct) + 0.5)) {
                $deductedDays = intval($daysDeduct) + 1;
            } else if ($daysDeduct < (intval($daysDeduct) + 0.5 )) {
                $deductedDays = intval($daysDeduct) + 0.5;
            } else {
                return 0;
            }

            $number = (explode("-", $data["leaveId"]))[1];

            if (date("Y", strtotime($user["date_of_join"])) == date("Y")) {
                return [
                    "deductedDays" => $deductedDays,
                    "number" => $number
                ];
            } else {
                return [
                    "deductedDays" => 0,
                    "number" => $number
                ];
            }
        }
    }

    /**
     * *********** Render - Relief ***************************
     */
    public function actionReliefLeaveApproval() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'reliefApproval');

        return $this->render($this::mainViewPath . 'reliefApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Render - Director ***************************
     */
    public function actionDirectorLeaveApproval() {
        $searchModel = new LeaveMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'directorApproval');

        return $this->render($this::mainViewPath . 'directorApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *********** Response by Relief***************************
     */
    public function actionRespReliefLeaveApproval() {
        $id = Yii::$app->request->post('leaveId');
        $approval = Yii::$app->request->post('approval');
        $remarks = Yii::$app->request->post('remarks');
        $leave = LeaveMaster::findOne($id);
        $leave->processApproval($approval, $remarks, 1);
        if ($leave->leave_status == LeaveMaster::STATUS_GetSuperiorApproval) {
            CronEmail::bodyLeaveRequestSuperior($id, 'Superior Leave Approval');
        } else if ($leave->leave_status == LeaveMaster::STATUS_GetHrApproval) {
            CronEmail::bodyLeaveRequestHr($id, 'HR Leave Approval');
        } else if ($leave->leave_status == LeaveMaster::STATUS_ReliefRejected) {
            CronEmail::bodyLeaveApplicationResponse($id, 'Relief Rejected', $remarks);
        }
        $this->redirect('relief-leave-approval');
    }

    /**
     * *********** Response by Superior***************************
     */
    public function actionRespSuperiorLeaveApproval() {
        $id = Yii::$app->request->post('leaveId');
        $approval = Yii::$app->request->post('approval');

        $remarks = Yii::$app->request->post('remarks');
        $leave = LeaveMaster::findOne($id);
//        if ($approval == $this::STS_Delegate) {
//            $leave->delegateSuperior($remarks);
//            NotificationMaster::newNotification($leave->requestor_id,
//                    "Leave approval deligated to " . $leave->superior->fullname,
//                    '/office/leave/personal-leave');
//        } else {
        $leave->processApproval($approval, $remarks, 2);
        if (!$approval) {
            CronEmail::bodyLeaveApplicationResponse($id, 'Superior Rejected Leave', $remarks);
        } else if ($approval) {
            CronEmail::bodyLeaveApplicationResponse($id, 'Superior Approved Leave', $remarks);
        }
//        if (!$approval) { // Notify if reject
//            NotificationMaster::newNotification($leave->requestor_id,
//                    "Leave request rejected by " . Yii::$app->user->identity->fullname . ". " . ($remarks ? "Remark: " . $remarks : ""),
//                    '/office/leave/personal-leave');
//        }
//        }
        $this->redirect('superior-leave-approval');
    }

    /**
     * *********** Response by HR Dept***************************
     */
    public function actionRespHrLeaveApproval() {
        $id = Yii::$app->request->post('leaveId');
        $approval = Yii::$app->request->post('approval');
        $remarks = Yii::$app->request->post('remarks');
        $leave = LeaveMaster::findOne($id);
        $leave->processApproval($approval, $remarks, $leave::STATUS_GetHrApproval);
        if (!$approval) {
            CronEmail::bodyLeaveApplicationResponse($id, 'HR Rejected Leave', $remarks);
        } else if ($approval) {
            CronEmail::bodyLeaveApplicationResponse($id, 'HR Approved Leave', $remarks);
        }
//        NotificationMaster::newNotification($leave->requestor_id,
//                "Leave request " . ($approval ? "approved" : "rejected") . " by HR Department. " . ($remarks ? "Remark: " . $remarks : ""),
//                '/office/leave/personal-leave');

        $this->redirect('hr-leave-approval');
    }

    /**
     * *********** Response by Directors***************************
     */
    /*
      public function actionRespDirectorLeaveApproval() {
      $id = Yii::$app->request->post('leaveId');
      $approval = Yii::$app->request->post('approval');
      $remarks = Yii::$app->request->post('remarks');
      $leave = LeaveMaster::findOne($id);
      $leave->processApproval($approval, $remarks, 3);
      $this->redirect('director-leave-approval');
      } */

    /**
     * *********** General Get - Leave Attachment***************************
     */
    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['leave_file_path'] . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    /**
     * Haziq 21/12/2022 Generating leave entitlement in bulk for next year
     */
    public function actionBulkEntitlementGeneration($nextYear, $statusChange = false) {
        $users = $this->actionGetNoEntitlementUser($nextYear);
        if (empty($users)) {
            FlashHandler::success(($nextYear) . "'s leave entitlement were already generated.");
            return $this->redirect(['hr-leave-entitlement', 'selectYear' => $nextYear]);
        }
        $flag = true;
        $leaveType = RefLeaveType::getActiveLeaveType();
        $nameList = '';

        foreach ($users as $user) {
            $entitlement = new LeaveEntitlement();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                //insert data for new leave entitlement
                $entitlement->user_id = $user['id'];
                $entitlement->year = $nextYear;
                $entitlement->annual_bring_forward_days = $this->getBringOverDays($user["id"], $nextYear - 1);
                if ($entitlement->save()) {
                    $flag = true;

                    //past data
                    $previousEntitle = LeaveEntitlement::find()->where(['user_id' => $user['id'], 'year' => ($nextYear - 1)])->one();
                    if (!$previousEntitle) {
                        $transaction->rollBack();
                        $nameList .= $user['fullname'] . "<br>";
                        continue;
                    }
                    $entitlementDetails = LeaveEntitlementDetails::find()->where(['leave_entitle_id' => $previousEntitle['id']])->asArray()->all();

                    //fill leave entitlement details frame
                    if ($entitlementDetails) {
                        foreach ($leaveType as $key => $leave) {
                            $entitleDetails[$key] = new LeaveEntitlementDetails();
                            foreach ($entitlementDetails as $detailsData) {
                                if ($leave["leave_type_code"] == $detailsData['leave_type_code']) {
                                    $entitleDetails[$key]->leave_entitle_id = $entitlement->id;
                                    $entitleDetails[$key]->month_start = 1;
                                    $entitleDetails[$key]->month_end = 12;
                                    $entitleDetails[$key]->leave_type_code = $detailsData['leave_type_code'];
                                    $entitleDetails[$key]->days = $detailsData['days'];

                                    if ($entitleDetails[$key]->save()) {
                                        $flag = true;
                                    } else {
                                        $flag = false;
                                        $transaction->rollBack();
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($previousEntitle)) {
                        $previousEntitle->annual_bring_next_year_days = $entitlement->annual_bring_forward_days;
                        if ($previousEntitle->update()) {
                            $flag = true;
                        } else {
                            $flag = false;
                            $transaction->rollBack();
                        }
                    }
                } else {
                    $flag = false;
                }

                if ($flag) {
                    $transaction->commit();
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err("Entitlement add fail");
                return $this->redirect(['hr-leave-entitlement', 'selectYear' => $nextYear - 1]);
            }
        }

        if ($flag) {
            if ($nameList && $statusChange) {
                FlashHandler::err("These users do not have past records: <br>" . $nameList);
            } else if ($nameList && (!$statusChange)) {
                FlashHandler::success("Leave generation for " . ($nextYear) . " success." . "<br> These users do not have past records: <br>" . $nameList);
            } else {
                FlashHandler::success("Leave generation for " . ($nextYear) . " success.");
            }
            return $this->redirect(['hr-leave-entitlement', 'selectYear' => $nextYear]);
        } else {
            FlashHandler::err("Leave generation for " . ($nextYear) . " failed. Contact IT department.");
            return $this->redirect(['hr-leave-entitlement', 'selectYear' => $nextYear - 1]);
        }
    }

    /**
     * *********** Render - HR - Show all unconfirmed ***************************
     * Commented by Haziq @ 2022-12-2

      public function actionHrLeaveUnconfirm() {
      $searchModel = new \frontend\models\working\leavemgmt\MasterLeaveBreakdownSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrLeaveUnconfirm');

      return $this->render($this::mainViewPath . 'hrLeaveUnconfirm', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      ]);
      }
     */
    /**
     * 
     * *********** EXPORT - HR - Export Holidays into Excel/CSV***************************
     * Commented by Paul @ 2022-11-21
     * No longer using this function
      public function actionHrHolidayListExcel($year = "") {
      if ($year == "") {
      $year = date("Y");
      }

      $response = Yii::$app->response;
      $response->format = \yii\web\Response::FORMAT_RAW;

      $holidayList = LeaveHolidays::find()->where('year(holiday_date) = ' . $year)->orderBy(['holiday_date' => SORT_ASC])->all();
      return $this->renderPartial($this::mainViewPath . 'hrHolidayListExcel', [
      'holidayList' => $holidayList,
      'selectYear' => $year
      ]);
      }
     */
    /**
     *  *********** Render - HR - Get leave entitlement from excel and display************
     * Commented by Paul @ 2022-11-21
      public function actionHrBatchUploadHolidayList() {

      $file = \yii\web\UploadedFile::getInstanceByName('excelFile');

      if (!in_array($file->type, self::csv_mimetypes)) {
      FlashHandler::err("File type not supported!");
      return $this->redirect([
      'hr-holiday-list'
      ]);
      }

      $process = new LeaveHolidays();
      $excelResult = $process->processExcel($file);

      if ($process->year == "") {
      FlashHandler::err("No \"Year\" in the CSV file.");
      return $this->redirect([
      'hr-holiday-list'
      ]);
      }

      return $this->render($this::mainViewPath . 'hrEditHolidayList', [
      'excelResult' => $excelResult,
      'year' => $process->year
      ]);
      }

     */
    /**
     *  *********** Process - HR - Update Holidays******************************
     * Commented by Paul @ 2022-11-21

      public function actionHrBatchUpdateHolidayList() {
      $processes = new LeaveHolidays();
      if ($processes->processUpdates()) {
      FlashHandler::success("Update Success!");
      }
      $theYear = $processes->year;

      return $this->redirect([
      'hr-holiday-list',
      'year' => $theYear
      ]);
      }
     */
    /**
     * *********** Action Only - HR - Confirm Leave*****************************
     * Commented by Haziq @ 5/12/2022

      public function actionRespHrConfirmLeave() {

      $monthYear = Yii::$app->request->post('confirmMonth');
      $month = substr($monthYear, 0, 2);
      $year = substr($monthYear, 2, 4);
      $leaveBreak = new LeaveDetailBreakdown();

      if ($leaveBreak->confirmLeave($month, $year)) {
      $tempDate = date("F", mktime(0, 0, 0, $month, 10));
      FlashHandler::success("Confirmed for " . $tempDate . " " . $year);
      }
      $this->redirect('hr-leave-summary');
      }
     */

    /**     * *************************************************************************************************************** Compulsory Leave ********* */
//    public function actionHrCompulsoryLeave() {
//        $model = LeaveCompulsoryMaster::find()->all();
//
//        return $this->render($this::mainViewPath . 'hrCompulsoryLeave', [
//                    'model' => $model,
//        ]);
//    }

    public function actionHrCompulsoryLeave() {
        $searchModel = new \frontend\models\office\leave\LeaveCompulsoryMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this::mainViewPath . 'hrCompulsoryLeave', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewCompulsoryLeave($id) {
        $cMaster = LeaveCompulsoryMaster::findOne($id);
        $cDetails = $cMaster->leaveCompulsoryDetails;

        $usersByType = [
            RefUserDesignation::TYPE_Executive => [],
            RefUserDesignation::TYPE_Office => [],
            RefUserDesignation::TYPE_Production => [],
        ];

        foreach ($cDetails as $cDetail) {
            $designationType = $cDetail->user->designation0->staff_type;
            $usersByType[$designationType][] = $cDetail->user;
        }

        $exec = $usersByType[RefUserDesignation::TYPE_Executive];
        $office = $usersByType[RefUserDesignation::TYPE_Office];
        $prod = $usersByType[RefUserDesignation::TYPE_Production];

        return $this->render($this::mainViewPath . '_viewCompulsoryLeave', [
                    'model' => $cMaster,
                    'cDetails' => $cDetails,
                    'execs' => $exec,
                    'offices' => $office,
                    'prods' => $prod]);
    }

    public function actionApplyCompulsoryLeave() {
        $clm = new LeaveCompulsoryMaster();
        $flag = false;

        if ($clm->load(Yii::$app->request->post())) {
            $clm->start_date = MyFormatter::changeDateFormat_readToDB($clm->start_date);
            $clm->end_date = MyFormatter::changeDateFormat_readToDB($clm->end_date);
            $clm->requestor = Yii::$app->user->id;
            $clm->status = LeaveMaster::STATUS_GetDirectorApproval;
            $clm->days = \common\models\myTools\MyCommonFunction::countDays($clm->start_date, $clm->end_date) + 1;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($clm->save()) {
                    foreach (Yii::$app->request->post('selectedUsers') as $user) {
                        $cld = new LeaveCompulsoryDetail();
                        $cld->user_id = $user;
                        $cld->compulsory_master_id = $clm->id;
                        if (!$cld->save()) {
                            \common\models\myTools\Mydebug::dumpFileA($cld->getErrors());
                            $transaction->rollBack();
                            FlashHandler::err('Error saving LeaveCompulsoryDetail.');
                            break;
                        }
                    }
                    $transaction->commit();
                    $flag = true;
                } else {
                    \common\models\myTools\Mydebug::dumpFileA($clm->getErrors());
                    $transaction->rollBack();
                    FlashHandler::err('Error saving LeaveCompulsoryMaster.');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                \common\models\myTools\Mydebug::dumpFileA($e);
                FlashHandler::err('An error occurred.');
            }
        }

        if ($flag) {
            FlashHandler::success('Application for Compulsory Leave sent');
            return $this->redirect(['hr-compulsory-leave']);
        }

        return $this->render($this::mainViewPath . '_formCompulsoryLeave', [
                    'model' => $clm,
                    'cDetails' => LeaveCompulsoryDetail::findAll(['compulsory_master_id' => $clm->id]),
                    'execs' => User::getStaffTypeList(RefUserDesignation::TYPE_Executive),
                    'offices' => User::getStaffTypeList(RefUserDesignation::TYPE_Office),
                    'prods' => User::getStaffTypeList(RefUserDesignation::TYPE_Production)
        ]);
    }

    public function actionUpdateCompulsoryLeave($id) {
        $cMaster = LeaveCompulsoryMaster::findOne($id);
        $cDetails = $cMaster->leaveCompulsoryDetails;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $cMaster->load($post);
            $cMaster->start_date = MyFormatter::changeDateFormat_readToDB($cMaster->start_date);
            $cMaster->end_date = MyFormatter::changeDateFormat_readToDB($cMaster->end_date);
            $cMaster->days = \common\models\myTools\MyCommonFunction::countDays($cMaster->start_date, $cMaster->end_date) + 1;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$cMaster->update(false)) {
                    $error = $cMaster->getErrors();
                    \common\models\myTools\Mydebug::dumpFileA($error);
                    $transaction->rollBack();
                } else {
                    // If the master record updated successfully
                    if (LeaveCompulsoryDetail::deleteAll(['compulsory_master_id' => $id])) {
                        foreach ($post['selectedUsers'] as $user) {
                            $cld = new LeaveCompulsoryDetail();
                            $cld->user_id = $user;
                            $cld->compulsory_master_id = $id;
                            if (!$cld->save()) {
                                $transaction->rollBack();
                                break; // Exit the loop on the first save failure
                            }
                        }
                        if ($transaction->getIsActive()) {
                            $transaction->commit();
                            FlashHandler::success('Application for Compulsory Leave edited');
                            return $this->redirect(['hr-compulsory-leave']);
                        }
                    } else {
                        $transaction->rollBack();
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }

        return $this->render($this::mainViewPath . '_formCompulsoryLeave', [
                    'model' => $cMaster,
                    'cDetails' => $cDetails,
                    'execs' => User::getStaffTypeList(RefUserDesignation::TYPE_Executive),
                    'offices' => User::getStaffTypeList(RefUserDesignation::TYPE_Office),
                    'prods' => User::getStaffTypeList(RefUserDesignation::TYPE_Production),
        ]);
    }

    public function actionDirCompulsoryLeaveApproval($id) {
        $cMaster = LeaveCompulsoryMaster::findOne($id);
        $cDetails = $cMaster->leaveCompulsoryDetails;

        $usersByType = [
            RefUserDesignation::TYPE_Executive => [],
            RefUserDesignation::TYPE_Office => [],
            RefUserDesignation::TYPE_Production => [],
        ];

        foreach ($cDetails as $cDetail) {
            $designationType = $cDetail->user->designation0->staff_type;
            $usersByType[$designationType][] = $cDetail->user;
        }

        $exec = $usersByType[RefUserDesignation::TYPE_Executive];
        $office = $usersByType[RefUserDesignation::TYPE_Office];
        $prod = $usersByType[RefUserDesignation::TYPE_Production];

        if ($cMaster->load(Yii::$app->request->post())) {
            if ($this->processCompulsoryLeave($id)) {
                $cMaster->approval_by = Yii::$app->user->id;
                $cMaster->update(false);
                if ($cMaster->status == LeaveMaster::STATUS_Approved) {
                    FlashHandler::success('Compulsory Leave Approved');
                } else if ($cMaster->status == LeaveMaster::STATUS_Rejected) {
                    FlashHandler::success('Compulsory Leave Rejected');
                }
            } else {
                FlashHandler::err('Compulsory Leave Not Approved');
            }
            return $this->redirect(['hr-compulsory-leave']);
        }

        return $this->render($this::mainViewPath . '_formApprovalCompulsoryLeave', [
                    'model' => $cMaster,
                    'cDetails' => $cDetails,
                    'execs' => $exec,
                    'offices' => $office,
                    'prods' => $prod
        ]);
    }

    public function processCompulsoryLeave($id) {
        $cMaster = LeaveCompulsoryMaster::findOne($id);

        if (!$cMaster) {
            return false;
        }

        $year = date('Y', strtotime($cMaster->start_date));
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($cMaster->leaveCompulsoryDetails as $cDetail) {
                $leave = new LeaveMaster([
                    'requestor_id' => $cDetail->user_id,
                    'start_date' => $cMaster->start_date,
                    'start_section' => 1,
                    'end_date' => $cMaster->end_date,
                    'end_section' => 2,
                    'year_of_leave' => $year,
                    'reason' => $cMaster->requestor_remark,
                    'emergency_leave' => 0,
                    'total_days' => $cMaster->days,
                    'leave_status' => LeaveMaster::STATUS_Approved,
                    'compulsory_leave' => $cDetail->id,
                ]);
                $leaveStatus = LeaveStatus::getPersonalLeaveStatus($cDetail->user_id, $year);

                if ($leaveStatus->annual_balanceCurrentCanApply > 0) {
                    $leave->leave_type_code = RefLeaveType::codeAnnual;
                } else {
                    $leave->leave_type_code = RefLeaveType::codeUnpaid;
                }

                if (!$leave->save(false)) {
                    $transaction->rollBack();
                    return false;
                }

                $leaveDetail = new LeaveDetailBreakdown([
                    'leave_id' => $leave->id,
                    'start_date' => $cMaster->start_date,
                    'start_section' => 1,
                    'end_date' => $cMaster->end_date,
                    'end_section' => 2,
                    'total_days' => $cMaster->days,
                    'leave_confirm_year' => $year,
                    'leave_confirm_month' => date('m', strtotime($cMaster->start_date)),
                    'confirm_flag' => 0,
                    'days_sick' => 0,
                    'days_others' => 0,
                ]);

                $leaveDetail->days_annual = ($leave->leave_type_code == RefLeaveType::codeAnnual) ? $cMaster->days : 0;
                $leaveDetail->days_unpaid = ($leave->leave_type_code == RefLeaveType::codeAnnual) ? 0 : $cMaster->days;

                if (!$leaveDetail->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function actionDeleteCompulsoryLeave($id) {
        $clm = LeaveCompulsoryMaster::findOne($id);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $cld = $clm->leaveCompulsoryDetails;

            foreach ($cld as $detail) {
                if (!$detail->delete()) {
                    throw new \Exception('Failed to delete Leave Compulsory Details.');
                }
            }

            if (!$clm->delete()) {
                throw new \Exception('Failed to delete Leave Compulsory.');
            }
            $transaction->commit();
            FlashHandler::success('Compulsary Leave deleted');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err('An error occurred while deleting the compulsory leave.');
        }

        $this->redirect('hr-compulsory-leave');
    }
}
