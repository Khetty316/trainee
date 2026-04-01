<?php

namespace frontend\models\office\leave;

use Yii;
use common\models\User;
use common\models\myTools\MyCommonFunction;
use \frontend\models\working\leavemgmt\LeaveDetailBreakdown;
use frontend\models\working\leavemgmt\LeaveWorklist;
use frontend\models\working\leavemgmt\LeaveDelegateList;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;
use frontend\models\office\leave\RefLeaveStatus;

/**
 * This is the model class for table "leave_master".
 *
 * @property int $id
 * @property int $requestor_id
 * @property string|null $leave_type_code
 * @property string|null $leave_code
 * @property int|null $relief_user_id
 * @property int|null $superior_id
 * @property string $reason
 * @property string $start_date
 * @property int $start_section
 * @property string $end_date
 * @property int $end_section
 * @property int $year_of_leave
 * @property int|null $emergency_leave
 * @property int|null $back_date
 * @property float|null $total_days
 * @property int|null $leave_status
 * @property string|null $support_doc
 * @property string $created_at
 * @property int|null $parent_id
 * @property int|null $claim_flag 1 = claimed
 * @property int|null $compulsory_leave
 *
 * @property LeaveDetailBreakdown[] $leaveDetailBreakdowns
 * @property User $reliefUser
 * @property User $requestor
 * @property RefLeaveSection $endSection
 * @property RefLeaveSection $startSection
 * @property RefLeaveStatus $leaveStatus
 * @property User $superior
 * @property RefLeaveType $leaveTypeCode
 * @property LeaveMaster $parent
 * @property LeaveMaster[] $leaveMasters
 * @property LeaveMaster $parent0
 * @property LeaveMaster[] $leaveMasters0
 * @property LeaveWorklist[] $leaveWorklists
 */
class LeaveMaster extends \yii\db\ActiveRecord {

    public $scannedFile;

    const STATUS_GetReliefApproval = 1;
    const STATUS_GetSuperiorApproval = 2;
    const STATUS_GetHrApproval = 3;
    const STATUS_Approved = 4;
    const STATUS_Cancelled = 6;
    const STATUS_Rejected = 7;
    const STATUS_ReliefRejected = 8;
    const STATUS_Recalled = 9;
    const STATUS_GetDirectorApproval = 10;
    const Prefix_WorkTravelReqCode = "WTR";
    const Prefix_LeaveCode = "LF";
    const runningNoLength = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['requestor_id', 'start_date', 'start_section', 'end_date', 'end_section', 'leave_type_code'], 'required'],
            [['requestor_id', 'relief_user_id', 'superior_id', 'start_section', 'end_section', 'year_of_leave', 'leave_status'], 'integer'],
            [['reason'], 'string'],
            [['start_date', 'end_date', 'created_at', 'relief_user_id', 'emergency_leave', 'back_date', 'reason', 'compulsory_leave'], 'safe'],
            [['total_days'], 'number'],
            [['leave_type_code'], 'string', 'max' => 10],
            [['leave_code', 'support_doc'], 'string', 'max' => 255],
            [['relief_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['relief_user_id' => 'id']],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
            [['end_section'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveSection::className(), 'targetAttribute' => ['end_section' => 'leave_section_id']],
            [['start_section'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveSection::className(), 'targetAttribute' => ['start_section' => 'leave_section_id']],
            [['leave_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveStatus::className(), 'targetAttribute' => ['leave_status' => 'leave_sts_id']],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['leave_type_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveType::className(), 'targetAttribute' => ['leave_type_code' => 'leave_type_code']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => 'png, jpg, pdf, jpeg', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
            ['start_date', 'validateDates'],
            ['start_date', 'validateDateForRelief'],
            [['reason'], 'required', 'when' => function ($model) {
                    return in_array($model->leave_type_code, [RefLeaveType::codeCompassion, RefLeaveType::codeMatern, RefLeaveType::codeMatrimonial, RefLeaveType::codePaternal, RefLeaveType::codeSick, RefLeaveType::codeUnpaid, RefLeaveType::codeEmergency, RefLeaveType::codeTravel]);
                }, 'whenClient' => "function(attribute, value) {
                    const validLeaveTypes = ['" . RefLeaveType::codeCompassion . "', '" . RefLeaveType::codeMatern . "', '" . RefLeaveType::codeMatrimonial . "', '" . RefLeaveType::codePaternal . "', '" . RefLeaveType::codeSick . "', '" . RefLeaveType::codeUnpaid . "', '" . RefLeaveType::codeEmergency . "', '" . RefLeaveType::codeTravel . "'];
                    return validLeaveTypes.includes($('#mainForm').find('[name=\"LeaveMaster[leave_type_code]\"]').val());}",
                'message' => 'Reason cannot be blank'],
//            [['reason'], 'required', 'when' => function ($model) {
//                    return ($model->emergency_leave == 1 || $model->back_date == 1);
//                }, 'whenClient' => "function(attribute, value) {
//            return $('#mainForm').find('[name=\"LeaveMaster[emergency_leave]\"]').val() === '1' || $('#mainForm').find('[name=\"LeaveMaster[back_date]\"]').val() === '1';}", 'message' => 'Reason cannot be blank'
//            ],
//            [['relief_user_id'], 'required', 'when' => function ($model) {
//                    return in_array($model->leave_type_code, [RefLeaveType::codeAnnual, RefLeaveType::codeUnpaid, RefLeaveType::codeMatrimonial]);
//                }, 'whenClient' => "function(attribute, value) {
//                        const validLeaveTypes = ['" . RefLeaveType::codeAnnual . "', '" . RefLeaveType::codeUnpaid . "', '" . RefLeaveType::codeMatrimonial . "'];
//                        return validLeaveTypes.includes($('#mainForm').find('[name=\"LeaveMaster[leave_type_code]\"]').val());}",
//                'message' => 'Relief must be selected for this leave type'],
//            [['relief_user_id'], 'required', 'when' => function ($model) {
//                    $leaveTypeCode = $model->leave_type_code;
//                    $backDateChecked = $model->back_date != null;
//                    return ($backDateChecked && in_array($leaveTypeCode, [RefLeaveType::codeAnnual, RefLeaveType::codeUnpaid, RefLeaveType::codeMatrimonial])) ||
//                    (!$backDateChecked && in_array($leaveTypeCode, [RefLeaveType::codeSick, RefLeaveType::codeCompassion, RefLeaveType::codePaternal]));
//                },
//                'whenClient' => "function(attribute, value) {
//                const leaveTypeCode = $('#mainForm').find('[name=\"LeaveMaster[leave_type_code]\"]').val();
//                const backDateChecked = $('#mainForm').find('[name=\"LeaveMaster[back_date]\"]').is(':checked');
//                return (backDateChecked && ['" . RefLeaveType::codeAnnual . "', '" . RefLeaveType::codeUnpaid . "', '" . RefLeaveType::codeMatrimonial . "'].includes(leaveTypeCode)) ||
//                       (!backDateChecked && ['" . RefLeaveType::codeSick . "', '" . RefLeaveType::codeCompassion . "', '" . RefLeaveType::codePaternal . "'].includes(leaveTypeCode));}",
//                'message' => 'Relief must be selected for this leave type'
//            ]
            [
                ['relief_user_id'], 'required', 'when' => function ($model) {
                    return ($model->emergency_leave == 0 && in_array($model->leave_type_code, [RefLeaveType::codeAnnual, RefLeaveType::codeUnpaid]));
                }, 'whenClient' => "function(attribute, value) {
                    const validLeaveTypes = ['" . RefLeaveType::codeAnnual . "', '" . RefLeaveType::codeUnpaid . "'];
                    return validLeaveTypes.includes($('#mainForm').find('[name=\"LeaveMaster[leave_type_code]\"]').val()) && $('#mainForm').find('[name=\"LeaveMaster[emergency_leave]\"]').val() == 0;}",
                'message' => 'Relief cannot be blank']
        ];
    }

    public function validateDateForRelief() {
        if (!$this->isNewRecord) {
            return true;
        }

        $dateForm = MyFormatter::changeDateFormat_readToDB($this->start_date);
        $gotRelief = false;

        $reliefLeaveList = self::find()->where(['relief_user_id' => Yii::$app->user->id])->andWhere("leave_status NOT IN (6,7,8,9)")->all();
        $username = null;

        foreach ($reliefLeaveList as $reliefLeave) {
            for ($i = 0; $i < $reliefLeave->total_days; $i++) {
                if ($dateForm == date('Y-m-d', strtotime("+$i day", strtotime($reliefLeave->start_date)))) {
                    $username = $reliefLeave->requestor->fullname;
                    $gotRelief = true;
                    break;
                }
            }
        }
//        if ($gotRelief) {
//            $this->addError('start_date', "You are relieving $username on this date.");
//        }
        if ($gotRelief && $this->leave_type_code != RefLeaveType::codeCompassion && $this->leave_type_code != RefLeaveType::codeSick) {
            $this->addError('start_date', "You are relieving $username on this date.");
        }

        $this->start_date = $dateForm;
        $this->end_date = MyFormatter::changeDateFormat_readToDB($this->end_date);
    }

    public function validateDates() {
        if (!$this->isNewRecord) {
            return true;
        }
        $selectedLeaveType = $this->leave_type_code;
        $todayDate = date('Y-m-d');

        $dateForm = MyFormatter::changeDateFormat_readToDB($this->start_date);
        $dateTo = MyFormatter::changeDateFormat_readToDB($this->end_date);

        if (strtotime($dateTo) < strtotime($dateForm)) {
            $this->addError('end_date', 'End date must be later than start date');
        } else if (strtotime($dateTo) == strtotime($dateForm) && $this->end_section < $this->start_section) {
            $this->addError('end_section', 'End section is earlier than start section');
        } else if (date('Y', strtotime($dateForm)) != date('Y', strtotime($dateTo))) {
            $this->addError('end_date', "Date range has to be in same year");
        } else {

            $leaveStatus = LeaveStatus::getPersonalLeaveStatus(Yii::$app->user->identity->id, date('Y', strtotime($dateForm)));
            $totalDays = $this->countDays();

            if ($this->emergency_leave == 0 && $this->leave_type_code == RefLeaveType::codeAnnual && $totalDays > $leaveStatus->annual_balanceCurrentCanApply) {
                $this->addError('end_date', "Requesting leave for: $totalDays days, and your available annual leave is/are: $leaveStatus->annual_balanceCurrentCanApply day(s)");
            } else if ($this->leave_type_code == RefLeaveType::codeSick && $totalDays > $leaveStatus->sick_balanceCurrentCanApply) {
                $this->addError('end_date', "Requesting leave for: $totalDays days, and your available sick leave is/are: $leaveStatus->sick_balanceCurrentCanApply day(s)");
            } else if ($this->leave_type_code != RefLeaveType::codeAnnual && $this->leave_type_code != RefLeaveType::codeSick) {
                $otherLeaves = \yii\helpers\ArrayHelper::toArray(RefLeaveType::find()
                                ->where(['not in', 'leave_type_code', [RefLeaveType::codeAnnual, RefLeaveType::codeSick]])
                                ->all());
                $key = array_search($this->leave_type_code, array_column($otherLeaves, "leave_type_code"), false);
                $tempModel = $otherLeaves[$key];
                if (($tempModel['default_days'] < $totalDays) && !in_array($tempModel['leave_type_code'], [RefLeaveType::codeUnpaid, RefLeaveType::codeEmergency, RefLeaveType::codeTravel])) {
                    if ($tempModel['leave_type_code'] != RefLeaveType::codeMatern) {
                        $this->addError('end_date', "Total days are: $totalDays working days, and your available " . $tempModel['leave_type_name'] . " is/are: " . $tempModel['default_days'] . " day(s)");
                    } else {
                        $this->addError('end_date', "Total days are: $totalDays days, and your available " . $tempModel['leave_type_name'] . " is/are: " . $tempModel['default_days'] . " day(s)");
                    }
                }
            }
        }

        //any validation condition under here is for not an emergency leave
//        if ($this->emergency_leave == 1) {
//            return true;
//        }
//        if ($this->emergency_leave == 1 && strtotime($todayDate) > strtotime($dateForm)) {
//            $this->addError('start_date', 'Only dates from today onwards are allowed.');
//        } else if ($this->emergency_leave == 1 && strtotime($todayDate) > strtotime($dateTo)) {
//            $this->addError('end_date', 'Only dates from today onwards are allowed.');
//        } else if ($this->back_date == 1 && strtotime($todayDate) <= strtotime($dateForm)) {
//            $this->addError('start_date', 'Only backdated dates are allowed');
//        } else if ($this->back_date == 0 && strtotime($todayDate) > strtotime($dateForm)) {
//            $this->addError('start_date', 'Back date only allowed for Back Date application.');
//        } else if ($this->back_date == 1 && strtotime($todayDate) <= strtotime($dateTo)) {
//            $this->addError('end_date', 'Only backdated dates are allowed');
//        }

        if (($this->emergency_leave == 0) && ($selectedLeaveType == RefLeaveType::codeAnnual || $selectedLeaveType == RefLeaveType::codeUnpaid)) {
            if (strtotime($todayDate) > strtotime($dateForm)) {
                $this->addError('start_date', 'Only dates from today onwards are allowed.');
            } else if (strtotime($todayDate) > strtotime($dateTo)) {
                $this->addError('end_date', 'Only dates from today onwards are allowed.');
            }
        }

        //check if start date is after 3 days as of today, error if within 3 days
        $holidays = \frontend\models\working\leavemgmt\LeaveHolidays::getByDateRange_array($dateForm, $dateTo);
        $workingDaysCount = 0;
        for ($i = 1; $workingDaysCount < 3; $i++) {
            $nextDay = strtotime("+$i day", strtotime($todayDate));
            $nextDayWeekday = date('N', $nextDay);

            // Check if the next day is not a weekend (Saturday or Sunday) and not a public holiday
            if ($nextDayWeekday < 7 && !in_array(date('Y-m-d', $nextDay), $holidays)) {
                $workingDaysCount++;
            }
        }

        $minimumStartDate = date('Y-m-d', $nextDay);

//        if (($this->emergency_leave == 0 && $this->back_date == 0) && ($selectedLeaveType == RefLeaveType::codeAnnual || $selectedLeaveType == RefLeaveType::codeUnpaid) && (strtotime($dateForm) < strtotime($minimumStartDate))) {
        if (($this->emergency_leave == 0) && ($selectedLeaveType == RefLeaveType::codeAnnual || $selectedLeaveType == RefLeaveType::codeUnpaid) && (strtotime($dateForm) < strtotime($minimumStartDate))) {
            $this->addError('start_date', 'Start date cannot be within the next 3 days for selected leave type.');
        }

        $this->start_date = $dateForm;
        $this->end_date = $dateTo;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requestor_id' => 'Requestor ID',
            'leave_type_code' => 'Leave Type',
            'relief_user_id' => 'Relief User ID',
            'superior_id' => 'Superior ID',
            'reason' => 'Reason',
            'start_date' => 'Start Date',
            'start_section' => 'Start Section',
            'end_date' => 'End Date',
            'end_section' => 'End Section',
            'year_of_leave' => 'Year Of Leave',
            'emergency_leave' => 'Emergency Leave',
            'back_date' => 'Back Date',
            'total_days' => 'Total Days',
            'leave_status' => 'Leave Status',
            'support_doc' => 'Support Doc',
            'created_at' => 'Created At',
            'parent_id' => 'Parent ID',
            'claim_flag' => 'Claim Flag',
            'compulsory_leave' => 'Compulsory Leave',
        ];
    }

    /**
     * Gets query for [[LeaveDetailBreakdowns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveDetailBreakdowns() {
        return $this->hasMany(LeaveDetailBreakdown::className(), ['leave_id' => 'id']);
    }

    /**
     * Gets query for [[ReliefUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReliefUser() {
        return $this->hasOne(User::className(), ['id' => 'relief_user_id']);
    }

    /**
     * Gets query for [[Requestor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor() {
        return $this->hasOne(User::className(), ['id' => 'requestor_id']);
    }

    /**
     * Gets query for [[EndSection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEndSection() {
        return $this->hasOne(RefLeaveSection::className(), ['leave_section_id' => 'end_section']);
    }

    /**
     * Gets query for [[StartSection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStartSection() {
        return $this->hasOne(RefLeaveSection::className(), ['leave_section_id' => 'start_section']);
    }

    /**
     * Gets query for [[LeaveStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveStatus() {
        return $this->hasOne(RefLeaveStatus::className(), ['leave_sts_id' => 'leave_status']);
    }

    /**
     * Gets query for [[Superior]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperior() {
        return $this->hasOne(User::className(), ['id' => 'superior_id']);
    }

    /**
     * Gets query for [[LeaveTypeCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveTypeCode() {
        return $this->hasOne(RefLeaveType::className(), ['leave_type_code' => 'leave_type_code']);
    }

    /**
     * Gets query for [[LeaveWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveWorklists() {
        return $this->hasMany(LeaveWorklist::className(), ['leave_id' => 'id']);
    }

    public function processAndSave() {
        $this->start_date = MyCommonFunction::DBSaveDate($this->start_date);
        $this->end_date = MyCommonFunction::DBSaveDate($this->end_date);
        $this->year_of_leave = MyFormatter::getYear($this->start_date);
        $code = $this->leave_type_code;

        if ($this->relief_user_id) {
            $this->leave_status = $this::STATUS_GetReliefApproval;
        } else if ($this->superior_id == "" || ($code != RefLeaveType::codeAnnual && $code != RefLeaveType::codeUnpaid && $code != RefLeaveType::codeTravel)) {
            $this->leave_status = $this::STATUS_GetHrApproval;
        } else {
            $this->leave_status = $this::STATUS_GetSuperiorApproval;
        }
        $this->total_days = $this->countDays();
        $leaveStatus = LeaveStatus::getPersonalLeaveStatus(Yii::$app->user->identity->id, date('Y', strtotime($this->start_date)));
        $currentAnnualLeaveBalance = $leaveStatus->annual_balanceCurrentCanApply;
        if ($code == RefLeaveType::codeEmergency) {
            $this->emergency_leave = 1;
            if ($this->total_days <= $currentAnnualLeaveBalance) {
                $this->leave_type_code = RefLeaveType::codeAnnual;
            } else if ($currentAnnualLeaveBalance == 0) {
                $this->leave_type_code = RefLeaveType::codeUnpaid;
            } else {
                $this->leave_type_code = RefLeaveType::codeAnnual;
                $remaining_days = $this->total_days - $currentAnnualLeaveBalance;
                $this->total_days = $currentAnnualLeaveBalance;
                $this->leave_code = $this->generateLeaveCode(RefLeaveType::codeAnnual);
                if ($this->save()) {
                    if ($this->validate() && $this->scannedFile) {
                        $this->support_doc = $this->id . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
                        MyCommonFunction::mkDirIfNull(Yii::$app->params['leave_file_path']);
                        $this->scannedFile->saveAs(Yii::$app->params['leave_file_path'] . $this->support_doc);
                        if ($this->update(false)) {
                            $unpaidLeave = new LeaveMaster();
                            $unpaidLeave->attributes = $this->attributes;
                            $unpaidLeave->leave_type_code = RefLeaveType::codeUnpaid;
                            $unpaidLeave->total_days = $remaining_days;
                            $unpaidLeave->parent_id = $this->id;
                            $unpaidLeave->leave_code = $unpaidLeave->generateLeaveCode(RefLeaveType::codeUnpaid);
                            if ($unpaidLeave->save()) {
                                FlashHandler::success("Leave application submitted!");
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        $leaveCode = $this->generateLeaveCode($this->leave_type_code);

        $this->leave_code = $leaveCode;
        if ($this->save()) {
            if ($this->validate() && $this->scannedFile) {
                $this->support_doc = $this->id . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
                MyCommonFunction::mkDirIfNull(Yii::$app->params['leave_file_path']);
                $this->scannedFile->saveAs(Yii::$app->params['leave_file_path'] . $this->support_doc);
                $this->update(false);
            }
            FlashHandler::success("Leave application submitted!");
            return true;
        } else {
            return false;
        }
    }

    public function generateLeaveCode($leaveTypeCode) {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        if ($leaveTypeCode === RefLeaveType::codeTravel) {
            $initialLeaveCode = self::Prefix_WorkTravelReqCode;
            $query = self::find()->where(['leave_type_code' => RefLeaveType::codeTravel])->andWhere(['YEAR(created_at)' => $currentYear])->andWhere(['MONTH(created_at)' => $currentMonth]);
        } else {
            $initialLeaveCode = self::Prefix_LeaveCode;
            $query = self::find()->where(['!=', 'leave_type_code', RefLeaveType::codeTravel])->andWhere(['YEAR(created_at)' => $currentYear])->andWhere(['MONTH(created_at)' => $currentMonth]);
        }

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $leaveCode = $initialLeaveCode . $runningNo . "-" . $currentMonth . $currentYearShort; // Generate the leave code

        $this->leave_code = $leaveCode;
        return $leaveCode;
    }

    /**
     * To count days and update; Triggered when have holiday change
     * @return type
     */
    public function recountDays() {
        $this->total_days = $this->countDays();
        return $this->update();
    }

    // *********Action, process the approval from Relief, Superior & HR
    public function processApproval($approval, $remarks, $step = 0) {
        //1 compare step
        if ($step != 0 && $this->leave_status != $step) {
            FlashHandler::err_outdated();
            return false;
        }
        //2 insert worklist
        $leaveWorkList = new LeaveWorklist();
        $leaveWorkList->leave_id = $this->id;
        $leaveWorkList->leave_status = $this->leave_status;
        $leaveWorkList->responsed_by = Yii::$app->user->identity->id;
        $leaveWorkList->approved_flag = $approval;
        $leaveWorkList->remarks = $remarks;
        $leaveWorkList->save();

        //3 update sts
        if ($approval == 0) {
            $this->leave_status = self::STATUS_Rejected;
        } else if ($approval == self::STATUS_ReliefRejected) {
            $this->leave_status = self::STATUS_ReliefRejected;
        } else {
            $this->leave_status++;
        }

        if ($this->leave_type_code != RefLeaveType::codeAnnual && $this->leave_type_code != RefLeaveType::codeUnpaid && $this->leave_type_code != RefLeaveType::codeTravel) {
            if ($this->leave_status == self::STATUS_GetSuperiorApproval) {
                $this->leave_status++;
            }
        }

        if ($this->leave_type_code == RefLeaveType::codeAnnual || $this->leave_type_code == RefLeaveType::codeUnpaid || $this->leave_type_code == RefLeaveType::codeTravel) {
            if ($this->leave_status == self::STATUS_GetHrApproval) {
                $this->leave_status = self::STATUS_Approved;
            }
        }

        $this->update(false);

        // If final confirmation is made by Director, then do breakdown
        if ($this->leave_status == $this::STATUS_Approved) {
            $leaveBreak = new LeaveDetailBreakdown();
            $leaveBreak->break($this);
        }

        FlashHandler::suc_stsUpdate();
    }

    // *********Action, delegate to current superior's superior (commented until further notice)
    /*
      public function delegateSuperior($remarks) {

      if ($this->leave_status != $this::STATUS_GetSuperiorApproval) {
      FlashHandler::err_outdated();
      return false;
      }

      $newSuperior = $this->superior->superior;

      if (!$newSuperior) {
      FlashHandler::err('No more superior to delegate');
      return false;
      }
      $leaveDelegateList = new LeaveDelegateList();
      $leaveDelegateList->leave_id = $this->id;
      $leaveDelegateList->delegate_from_user = $this->superior_id;
      $leaveDelegateList->delegate_to_user = $newSuperior->id;
      $leaveDelegateList->remark = $remarks;

      if (!$leaveDelegateList->save()) {
      FlashHandler::err("Delegate fail. Please contact IT Department for assist.");
      return false;
      }

      $this->superior_id = $newSuperior->id;
      $this->update();
      FlashHandler::success("Delegated to " . $newSuperior->fullname);
      } */

    public function countDays() {
        $startDate = MyCommonFunction::DBSaveDate($this->start_date);
        $endDate = MyCommonFunction::DBSaveDate($this->end_date);
        $leaveType = $this->leave_type_code;
        $holidays = \frontend\models\working\leavemgmt\LeaveHolidays::getByDateRange_array($startDate, $endDate);
        $daysDiff = MyCommonFunction::countDays($startDate, $endDate) + 1;

        $totalDays = 0;
        for ($i = 0; $i < $daysDiff; $i++) {
            $addDays = 0.0;
            $dateCheck = MyCommonFunction::addDays($startDate, $i);
            $theDay = date('N', strtotime($dateCheck));

            // If not holiday or Sunday
            if ((!array_key_exists($dateCheck, $holidays) && $theDay != 7) || ($leaveType == RefLeaveType::codeMatern)) {
                $addDays += 1;
            }
            $totalDays += $addDays;
        }
        return $totalDays;
    }

    public function cancelLeave($remark) {
        if ($this->requestor_id != Yii::$app->user->id) {
            return false;
        }

        if (in_array($this->leave_status, array($this::STATUS_Approved, $this::STATUS_Cancelled, $this::STATUS_Rejected))) {
            return false;
        }

        $this->leave_status = $this::STATUS_Cancelled;
        if ($this->update()) {
            $leaveWorkList = new LeaveWorklist();
            $leaveWorkList->leave_id = $this->id;
            $leaveWorkList->leave_status = $this->leave_status;
            $leaveWorkList->responsed_by = Yii::$app->user->identity->id;
            $leaveWorkList->approved_flag = 0;
            $leaveWorkList->remarks = $remark;
            return $leaveWorkList->save();
        }

        return false;
    }

    /**
     * Created by Ling Ee Hwang @ 03/10/2022
     * Function to recall leave, by HR
     * @param type $remark
     * @return boolean
     */
    public function hrRecallLeave($remark = "") {
        if ($this->leave_status != $this::STATUS_Approved) {
            return false;
        }

        $this->leave_status = $this::STATUS_Recalled;
        if ($this->update(false)) {
            $leaveWorkList = new LeaveWorklist();
            $leaveWorkList->leave_id = $this->id;
            $leaveWorkList->leave_status = $this->leave_status;
            $leaveWorkList->responsed_by = Yii::$app->user->identity->id;
            $leaveWorkList->approved_flag = 0;
            $leaveWorkList->remarks = "Recalled by HR";
            return $leaveWorkList->save();
        }

        return false;
    }

    /**
     * To get Leave total leave applied (Approved)
     * Created by Paul @ 05/12/2022 
     * @param type $leaveType
     * @param type $year
     * @param type $userId
     * @return type
     */
    public static function getApprovedLeaveTotal($leaveType, $year, $userId) {
        return MyFormatter::floorHalfDecimal(LeaveMaster::find()
                                ->where(['year_of_leave' => $year, 'requestor_id' => $userId, 'leave_type_code' => $leaveType])
                                ->andWhere('leave_status IN (' . RefLeaveStatus::STS_APPROVED . ')')
                                ->sum('total_days'));
    }

    /**
     * To get Leave total leave applied (Approved and Pending)
     * Created by Paul @ 05/12/2022 
     * @param type $leaveType
     * @param type $year
     * @param type $userId
     * @return type
     */
    public static function getActiveLeaveTotal($leaveType, $year, $userId) {
        return LeaveMaster::find()
                        ->where(['year_of_leave' => $year, 'requestor_id' => $userId, 'leave_type_code' => $leaveType])
                        ->andWhere('leave_status IN (' . implode(",", RefLeaveStatus::STS_ActiveList) . ')')
                        ->sum('total_days');
    }

    /**
     *  To get Pending Leave
     * Created by Paul @ 05/12/2022 
     * To get Leave total leave applied
     * @param type $leaveType
     * @param type $year
     * @param type $userId
     * @return type
     */
    public static function getPendingLeaveTotal($leaveType, $year, $userId) {
        return LeaveMaster::find()
                        ->where(['year_of_leave' => $year, 'requestor_id' => $userId, 'leave_type_code' => $leaveType])
                        ->andWhere('leave_status IN (' . implode(",", RefLeaveStatus::STS_Pending) . ')')
                        ->sum('total_days');
    }
}
