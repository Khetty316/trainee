<?php

namespace frontend\models\attendance;

use Yii;
use yii\base\Model;
use common\models\User;
use frontend\models\attendance\MonthlyAttendance;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyCommonFunction;
use \Exception;

class BulkProcessMonthlyAttendance extends Model {

    const STARTING_CELL_VALUE = "No.";
    const EXC_USER_ID = "User ID";
    const EXC_NAME = "Name";
    const EXC_PERFECT = "√";
    const EXC_TOTAL_DAYS = "Total Days";
    const EXC_TOTAL_PRESENT = "Total Present";
    const EXC_WORKDAY_PRESENT = "WP";
    const EXC_UNPAID_LEAVE_PRESENT = "OP";
    const EXC_REST_HOOLIDAY_PRESENT = "R/H P";
    const EXC_ABSENT = "AB";
    const EXC_LEAVE_TAKEN = "LV";
    const EXC_LATE_IN = "LI";
    const EXC_EARLY_OUT = "EO";
    const EXC_MISS_PUNCH = "MP";
    const EXC_SHORT = "Short";
    const EXC_SCHE = null;
    const EXC_WORKDAY = "Workday";
    const EXC_WORKDAY_OT = "WorkdayOT";
    const EXC_HOLIDAY = "Holiday";
    const EXC_HOLIDAY_OT = "HolidayOT";
    const EXC_RESTDAY = "Restday";
    const EXC_RESTDAY_OT = "RestdayOT";
    const EXC_UNPAID_LEAVE = "Unpaid Leave";
    const EXC_UNPAID_LEAVE_OT = "Unpaid LeaveOT";
    const EXCEL_COLUMN_NUM = [
        self::EXC_TOTAL_DAYS, self::EXC_TOTAL_PRESENT, self::EXC_WORKDAY_PRESENT, self::EXC_UNPAID_LEAVE_PRESENT, self::EXC_REST_HOOLIDAY_PRESENT,
        self::EXC_ABSENT, self::EXC_LEAVE_TAKEN, self::EXC_LATE_IN, self::EXC_EARLY_OUT, self::EXC_MISS_PUNCH, self::EXC_SHORT, self::EXC_WORKDAY,
        self::EXC_WORKDAY_OT, self::EXC_HOLIDAY, self::EXC_HOLIDAY_OT, self::EXC_RESTDAY, self::EXC_RESTDAY_OT, self::EXC_UNPAID_LEAVE, self::EXC_UNPAID_LEAVE_OT
    ];

    public $scannedFile;
    public $year, $month;

    public function rules() {
        return [
            [['year', 'month'], 'required'],
            [['scannedFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
        ];
    }

    public function uploadFile($year, $month) {
        $errorMsg = "";

        if ($this->scannedFile) {
            $file = $this->scannedFile;

            $filePath = Yii::$app->params['monthlyappraisal_file_path'] . "$year/";
            MyCommonFunction::mkDirIfNull($filePath);

            $currentTime = time();
            $fileName = date("Ymd_His", $currentTime) . "." . $file->extension;
            $fullPath = $filePath . $fileName;

            if ($file->saveAs($fullPath)) {
                $result = $this->processFile($fullPath, $year, $month);
                if ($result === true) {
                    return true;
                }
                if ($result === false) {
                    return false;
                }
                $err = implode("<br/>", $result);
                FlashHandler::err("Some user/s cant be processed. Please check.<br/>");
                return $err;
            } else {
                $errorMsg = "Failed to save the file.";
            }
        } else {
            $errorMsg = "No file provided.";
        }

        FlashHandler::err($errorMsg);
        return false;
    }

    /**
     * Process excel file to get data into MonthlyAttendance model
     * @param type $filePath
     * @param type $year
     * @param type $month
     * @return boolean
     * @throws Exception
     */
    private function processFile($filePath, $year, $month) {
        $userFail = [];
        $offset = [self::EXC_PERFECT => -1];

        $rowsOfDatas = \common\models\myTools\DynamicExcelProcessor::getArrayOfData($filePath, self::STARTING_CELL_VALUE, $offset);

        if (isset($rowsOfDatas["error"])) {
            return false;
        } else {
            foreach ($rowsOfDatas as $singleRowData) {
                if (!$this->processArrayData($singleRowData, $year, $month)) {
                    $userFail[] = $singleRowData[self::EXC_NAME];
                }
            }
        }

        if ($userFail) {
            return $userFail;
        } else {
            return true;
        }
    }

    /**
     * Process a single row of data, database related condition are placed here
     * @param array $rowData
     * @param int $year
     * @param int $month
     * @return boolean
     */
    private function processArrayData($rowData, $year, $month) {
        $user = User::find()
                ->where(['=', new \yii\db\Expression('LOWER(fullname)'), trim(strtolower($rowData[self::EXC_NAME]))])
                ->one();

        if ($user !== null) {
            $attendance = MonthlyAttendance::findOne(['user_id' => $user->id, 'year' => $year, 'month' => $month]);

            if (!$attendance) {
                $attendance = new MonthlyAttendance();
                $attendance->user_id = $user->id;
                $attendance->year = $year;
                $attendance->month = $month;
            }

            $attendance->perfect = $rowData[self::EXC_PERFECT] ?? null;
            $attendance->total_days = $rowData[self::EXC_TOTAL_DAYS] ?? null;
            $attendance->total_present = $rowData[self::EXC_TOTAL_PRESENT] ?? null;
            $attendance->workday_present = $rowData[self::EXC_WORKDAY_PRESENT] ?? null;
            $attendance->unpaid_leave_present = $rowData[self::EXC_UNPAID_LEAVE_PRESENT] ?? null;
            $attendance->rest_holiday_present = $rowData[self::EXC_REST_HOOLIDAY_PRESENT] ?? null;
            $attendance->absent = $rowData[self::EXC_ABSENT] ?? null;
            $attendance->leave_taken = $rowData[self::EXC_LEAVE_TAKEN] ?? null;
            $attendance->late_in = $rowData[self::EXC_LATE_IN] ?? null;
            $attendance->early_out = $rowData[self::EXC_EARLY_OUT] ?? null;
            $attendance->miss_punch = $rowData[self::EXC_MISS_PUNCH] ?? null;
            $attendance->short = $rowData[self::EXC_SHORT] ?? null;
//            $attendance->sche = $rowData[self::EXC_SCHE];
            $attendance->workday = $rowData[self::EXC_WORKDAY] ?? null;
            $attendance->workday_ot = $rowData[self::EXC_WORKDAY_OT] ?? null;
            $attendance->holiday = $rowData[self::EXC_HOLIDAY] ?? null;
            $attendance->holiday_ot = $rowData[self::EXC_HOLIDAY_OT] ?? null;
            $attendance->restday = $rowData[self::EXC_RESTDAY] ?? null;
            $attendance->restday_ot = $rowData[self::EXC_RESTDAY_OT] ?? null;
            $attendance->unpaid_leave = $rowData[self::EXC_UNPAID_LEAVE] ?? null;
            $attendance->unpaid_leave_ot = $rowData[self::EXC_UNPAID_LEAVE_OT] ?? null;

            return $attendance->save();
        }

        return false;
    }

}
