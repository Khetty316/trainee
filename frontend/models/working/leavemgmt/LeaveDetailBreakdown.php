<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use frontend\models\office\leave\LeaveMaster;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "leave_detail_breakdown".
 *
 * @property int $id
 * @property int $leave_id
 * @property string $start_date
 * @property int $start_section
 * @property string $end_date
 * @property int $end_section
 * @property float|null $total_days
 * @property int|null $leave_confirm_year
 * @property string|null $leave_confirm_month
 * @property float|null $days_annual
 * @property float|null $days_unpaid
 * @property float|null $days_sick
 * @property float|null $days_others
 * @property int|null $is_recorded
 * @property int $confirm_flag
 * @property string $created_at
 *
 * @property LeaveMaster $leave
 */
class LeaveDetailBreakdown extends \yii\db\ActiveRecord {

    CONST SEC_AM = 1;
    CONST SEC_PM = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_detail_breakdown';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['leave_id', 'start_date', 'start_section', 'end_date', 'end_section'], 'required'],
            [['leave_id', 'start_section', 'end_section', 'leave_confirm_year', 'is_recorded', 'confirm_flag'], 'integer'],
            [['start_date', 'end_date', 'created_at'], 'safe'],
            [['total_days', 'days_annual', 'days_unpaid', 'days_sick', 'days_others'], 'number'],
            [['leave_confirm_month'], 'string', 'max' => 2],
            [['leave_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveMaster::className(), 'targetAttribute' => ['leave_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'leave_id' => 'Leave ID',
            'start_date' => 'Start Date',
            'start_section' => 'Start Section',
            'end_date' => 'End Date',
            'end_section' => 'End Section',
            'total_days' => 'Total Days',
            'leave_confirm_year' => 'Leave Confirm Year',
            'leave_confirm_month' => 'Leave Confirm Month',
            'days_annual' => 'Days Annual',
            'days_unpaid' => 'Days Unpaid',
            'days_sick' => 'Days Sick',
            'days_others' => 'Days Others',
            'is_recorded' => 'Is Recorded',
            'confirm_flag' => 'Confirm Flag',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Leave]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeave() {
        return $this->hasOne(LeaveMaster::className(), ['id' => 'leave_id']);
    }

    /**
     * *************************** Main Function, breaking ************************
     *      
     */
    public function break($leaveMaster) {

        // Disallow to continue if already break
        $testLm = LeaveDetailBreakdown::find()->where('leave_id = ' . $leaveMaster->id)->one();
        if ($testLm) {
            return false;
        }
        $monthDiff = MyCommonFunction::countMonths($leaveMaster->start_date, $leaveMaster->end_date) + 1;

        if ($monthDiff == 1) {
            // Directly "copy"
            $tempBean = new LeaveDetailBreakdown();
            $tempBean->leave_id = $leaveMaster->id;
            $tempBean->start_date = $leaveMaster->start_date;
            $tempBean->start_section = $leaveMaster->start_section;
            $tempBean->end_date = $leaveMaster->end_date;
            $tempBean->end_section = $leaveMaster->end_section;
            $tempBean->total_days = $tempBean->countDaysLeave();

            //Haziq 7/11/2022 Save year and month when HR approve
            $tempBean->leave_confirm_month = date("m", strtotime($tempBean->end_date));
            $tempBean->leave_confirm_year = date("Y", strtotime($tempBean->end_date));
            $tempBean->save();
        } else {
            for ($i = 0; $i < $monthDiff; $i++) {
                $tempBean = new LeaveDetailBreakdown();
                $tempBean->leave_id = $leaveMaster->id;
                if ($i == 0) {  // First Month
                    $tempBean->start_date = $leaveMaster->start_date;
                    $tempBean->start_section = $leaveMaster->start_section;
                    // Change Date To to end of this month
                    $tempBean->end_date = MyCommonFunction::getLastDayDate($leaveMaster->start_date);
                    $tempBean->end_section = self::SEC_PM;

                    //Haziq 7/11/2022 Save year and month when HR approve
                    $tempBean->leave_confirm_month = date("m", strtotime($tempBean->end_date));
                    $tempBean->leave_confirm_year = date("Y", strtotime($tempBean->end_date));
                } else if ($i == ($monthDiff - 1)) {    // Last Month
                    // Set date from to starting of this month
                    $tempBean->start_date = MyCommonFunction::getFirstDayDate(MyCommonFunction::addMonths($leaveMaster->start_date, $i));
                    $tempBean->start_section = self::SEC_AM;
                    $tempBean->end_date = $leaveMaster->end_date;
                    $tempBean->end_section = $leaveMaster->end_section;

                    //Haziq 7/11/2022 Save year and month when HR approve
                    $tempBean->leave_confirm_month = date("m", strtotime($tempBean->end_date));
                    $tempBean->leave_confirm_year = date("Y", strtotime($tempBean->end_date));
                } else {  // Middle Month       
                    $tempBean->start_date = MyCommonFunction::getFirstDayDate(MyCommonFunction::addMonths($leaveMaster->start_date, $i));
                    $tempBean->start_section = self::SEC_AM;
                    $tempBean->end_date = MyCommonFunction::getLastDayDate(MyCommonFunction::addMonths($leaveMaster->start_date, $i));
                    $tempBean->end_section = self::SEC_PM;

                    //Haziq 7/11/2022 Save year and month when HR approve
                    $tempBean->leave_confirm_month = date("m", strtotime($tempBean->end_date));
                    $tempBean->leave_confirm_year = date("Y", strtotime($tempBean->end_date));
                }

                $tempBean->total_days = $tempBean->countDaysLeave();
                $tempBean->save();
            }
        }
        return true;
    }

    /**     * ******************
     *  To count the days of leave after breakdown by month
     */
    /* public function countDays() {
      $startDate = $this->start_date;
      $startSec = $this->start_section;
      $endDate = $this->end_date;
      $endSec = $this->end_section;
      $holidays = LeaveHolidays::getByDateRange_array($startDate, $endDate);
      $daysDiff = MyCommonFunction::countDays($startDate, $endDate) + 1;

      $totalDays = 0;
      for ($i = 0; $i < $daysDiff; $i++) {
      $addDays = 0.0;
      $dateCheck = MyCommonFunction::addDays($startDate, $i);
      $theDay = date('N', strtotime($dateCheck));

      // If not holiday or Synday
      if (!array_key_exists($dateCheck, $holidays) && $theDay != 7) {
      // if is first day
      if ($i == 0) {
      if ($theDay == 6 || $startSec == 1) { // Saturday, or AM
      $addDays += 1;
      } else if ($startSec == 2) { // if is PM
      $addDays += 0.5;
      }
      } else {
      $addDays += 1;
      }

      // If it is last day, endSection is AM and is NOT Saturday
      if (($i + 1) == $daysDiff && ($endSec == 1 && $theDay != 6)) {
      $addDays -= 0.5;
      }
      } else {

      }
      $totalDays += $addDays;
      }
      return $totalDays;
      } */

    /**     * ******************
     *  To count the days of leave after breakdown by month no need section since only day leave 
     */
    public function countDaysLeave() {
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $leaveType = $this->leave->leave_type_code;
        $holidays = LeaveHolidays::getByDateRange_array($startDate, $endDate);
        $daysDiff = MyCommonFunction::countDays($startDate, $endDate) + 1;

        $totalDays = 0;
        for ($i = 0; $i < $daysDiff; $i++) {
            $addDays = 0.0;
            $dateCheck = MyCommonFunction::addDays($startDate, $i);
            $theDay = date('N', strtotime($dateCheck));

            // If not holiday or Sunday
            if ((!array_key_exists($dateCheck, $holidays) && $theDay != 7) || ($leaveType == \frontend\models\office\leave\RefLeaveType::codeMatern)) {
                $addDays += 1;
            }
            $totalDays += $addDays;
        }
        return $totalDays;
    }

    /**
     * To confirm the leave, MONTHLY
     */
    public function confirmLeave($month, $year) {
        $date = date_create(MyCommonFunction::getLastDayDate("$year-$month-01"));
        $staffList = \common\models\User::find()->all();

        foreach ($staffList as $staff) {

            // Get this year's history
            $confirmedLeaveDetail = (new \yii\db\Query())->select([
                        'SUM(IFNULL(days_annual, 0)) AS L_annual',
                        'SUM(IFNULL(days_unpaid, 0)) AS L_unpaid',
                        'SUM(IFNULL(days_sick, 0)) AS L_sick',
                        'SUM(IFNULL(days_others, 0)) AS L_other'])
                    ->from('leave_detail_breakdown')
                    ->join('JOIN', 'leave_master', 'leave_master.id = leave_detail_breakdown.leave_id')
                    ->where("leave_detail_breakdown.leave_confirm_year = " . $year)
                    ->andWhere(['leave_master.requestor_id' => $staff->id])
                    ->one();

            // Get users' leave entitlement
            $leaveEntitle = LeaveEntitlement::find()->where(["user_id" => $staff->id, "year" => $year])->one();
            $annualLeaveBalance = floor($month / 12 * ($leaveEntitle['annual_entitled'] ?? 0) * 2) / 2 + ($leaveEntitle['annual_bring_forward_days'] ?? 0) - ($confirmedLeaveDetail['L_annual'] ?? 0);
            $sickLeaveBalance = ($leaveEntitle['sick_entitled'] ?? 0) - ($confirmedLeaveDetail['L_sick'] ?? 0);
            // Get all un-confirm leave, up to current month
            $unconfirmedLeaves = VMasterLeaveBreakdown::find()
                    ->where('break_start_date <= "' . date_format($date, 'Y-m-t') . '"')
                    ->andWhere("requestor_id=" . $staff->id . " AND confirm_flag=0 AND break_id IS NOT NULL ")
                    ->all();

            $monthly_annual = 0.0;
            $monthly_sick = 0.0;
            $monthly_unpaid = 0.0;
            $monthly_others = 0.0;
            foreach ($unconfirmedLeaves as $unconfirmedLeave) {
                $leave = LeaveDetailBreakdown::findOne($unconfirmedLeave->break_id);
                $leave->total_days = $leave->countDaysLeave(); // Count again before confirm
                // if is annual
                if ($unconfirmedLeave->leave_type_code == 1) {
                    $this->processAnnualLeave($annualLeaveBalance, $leave);
                } else if ($unconfirmedLeave->leave_type_code == 3) { // If is unpaid
                    $this->processUnpaidLeave($leave);
                } else if ($unconfirmedLeave->leave_type_code == 2) { // If is sick
                    $this->processSickLeave($sickLeaveBalance, $leave, $annualLeaveBalance);
                } else { // if others, maybe can exclude work from home
                    $this->processOtherLeave($leave);
                }
                $leave->leave_confirm_month = $month;
                $leave->leave_confirm_year = $year;
                $leave->confirm_flag = 1;
                $leave->update();
                $monthly_annual += $leave->days_annual;
                $monthly_sick += $leave->days_sick;
                $monthly_unpaid += $leave->days_unpaid;
                $monthly_others += $leave->days_others;
            }



            // *********************** HERE DO THE SUMMARY OF THIS STAFF THIS MONTH & YEAR IN TABLE leave_monthly_summary
            $monthlySummary = new LeaveMonthlySummary();
            $monthlySummary->createRecord($staff->id, $year, $month, $monthly_annual, $monthly_unpaid, $monthly_sick, $monthly_others);
        }




        return true;
    }

    public function processAnnualLeave(&$totalAnnualBalance, $leave) {
//        $leave = new LeaveDetailBreakdown();
        // If annual is enough
        if ($totalAnnualBalance >= $leave->total_days) {
            $totalAnnualBalance -= $leave->total_days;
            $leave->days_annual = $leave->total_days;
            $leave->days_unpaid = 0;
            $leave->days_sick = 0;
            $leave->days_others = 0;
        } else {
            // If not enough, finish annual, then the rest fall into unpaid
            $leave->days_annual = $totalAnnualBalance;
            $totalAnnualBalance = 0;
            $leave->days_unpaid = $leave->total_days - $leave->days_annual;
            $leave->days_sick = 0;
            $leave->days_others = 0;
        }
    }

    public function processSickLeave(&$sickLeaveBalance, $leave, &$totalAnnualBalance) {
        // If annual is enough
        if ($sickLeaveBalance >= $leave->total_days) {
            $sickLeaveBalance -= $leave->total_days;
            $leave->days_annual = 0;
            $leave->days_unpaid = 0;
            $leave->days_sick = $leave->total_days;
            $leave->days_others = 0;
        } else {
            // If not enough, , then the rest fall into annual
            $this->processAnnualLeave($totalAnnualBalance, $leave);
            $leave->days_sick = $sickLeaveBalance;
            $sickLeaveBalance = 0;
        }
    }

    public function processUnpaidLeave($leave) {
        $leave->days_annual = 0;
        $leave->days_unpaid = $leave->total_days;
        $leave->days_sick = 0;
        $leave->days_others = 0;
    }

    public function processOtherLeave($leave) {
        $leave->days_annual = 0;
        $leave->days_unpaid = 0;
        $leave->days_sick = 0;
        $leave->days_others = $leave->total_days;
    }

    /*
     * End of Confirm Leave
     */

    /**
     * To count days and update; Triggered when have holiday change
     * @return type
     */
    public function recountDays() {
        $this->total_days = $this->countDaysLeave();
        return $this->update();
    }

}
