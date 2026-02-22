<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use common\models\User;

class LeaveMonthlySummaryTable extends \yii\base\Model {

    public $user;
    public $_01_annual;
    public $_01_unpaid;
    public $_01_sick;
    public $_01_other;
    public $_02_annual;
    public $_02_unpaid;
    public $_02_sick;
    public $_02_other;
    public $_03_annual;
    public $_03_unpaid;
    public $_03_sick;
    public $_03_other;
    public $_04_annual;
    public $_04_unpaid;
    public $_04_sick;
    public $_04_other;
    public $_05_annual;
    public $_05_unpaid;
    public $_05_sick;
    public $_05_other;
    public $_06_annual;
    public $_06_unpaid;
    public $_06_sick;
    public $_06_other;
    public $_07_annual;
    public $_07_unpaid;
    public $_07_sick;
    public $_07_other;
    public $_08_annual;
    public $_08_unpaid;
    public $_08_sick;
    public $_08_other;
    public $_09_annual;
    public $_09_unpaid;
    public $_09_sick;
    public $_09_other;
    public $_10_annual;
    public $_10_unpaid;
    public $_10_sick;
    public $_10_other;
    public $_11_annual;
    public $_11_unpaid;
    public $_11_sick;
    public $_11_other;
    public $_12_annual;
    public $_12_unpaid;
    public $_12_sick;
    public $_12_other;

    public function getSummary($year) {

        $userIdList = LeaveMonthlySummary::find()->select('requestor_id')->join('INNER JOIN', 'user', 'user.id=leave_monthly_summary.requestor_id AND user.status=10 AND (user.staff_id !="" OR user.staff_id IS NOT NULL)')->orderBy(['staff_id'=>'SORT_ASC'])->distinct()->where("year = " . $year)->all();

        $array = [];
        foreach ($userIdList as $userId) {
            $leaveSummary = LeaveMonthlySummary::find()->where(["year" => $year, 'requestor_id' => $userId->requestor_id])->all();
            $row = new LeaveMonthlySummaryTable();
            foreach ($leaveSummary as $leave) {
                if (!$row->user) {
                    $row->user = $leave->requestor;
                }
                switch ($leave->month) {
                    case '01':
                        $row->_01_annual = $leave->days_annual;
                        $row->_01_unpaid = $leave->days_unpaid;
                        $row->_01_sick = $leave->days_sick;
                        $row->_01_other = $leave->days_others;
                        break;
                    case '02':
                        $row->_02_annual = $leave->days_annual;
                        $row->_02_unpaid = $leave->days_unpaid;
                        $row->_02_sick = $leave->days_sick;
                        $row->_02_other = $leave->days_others;
                        break;
                    case '03':
                        $row->_03_annual = $leave->days_annual;
                        $row->_03_unpaid = $leave->days_unpaid;
                        $row->_03_sick = $leave->days_sick;
                        $row->_03_other = $leave->days_others;
                        break;
                    case '04':
                        $row->_04_annual = $leave->days_annual;
                        $row->_04_unpaid = $leave->days_unpaid;
                        $row->_04_sick = $leave->days_sick;
                        $row->_04_other = $leave->days_others;
                        break;
                    case '05':
                        $row->_05_annual = $leave->days_annual;
                        $row->_05_unpaid = $leave->days_unpaid;
                        $row->_05_sick = $leave->days_sick;
                        $row->_05_other = $leave->days_others;
                        break;
                    case '06':
                        $row->_06_annual = $leave->days_annual;
                        $row->_06_unpaid = $leave->days_unpaid;
                        $row->_06_sick = $leave->days_sick;
                        $row->_06_other = $leave->days_others;
                        break;
                    case '07':
                        $row->_07_annual = $leave->days_annual;
                        $row->_07_unpaid = $leave->days_unpaid;
                        $row->_07_sick = $leave->days_sick;
                        $row->_07_other = $leave->days_others;
                        break;
                    case '08':
                        $row->_08_annual = $leave->days_annual;
                        $row->_08_unpaid = $leave->days_unpaid;
                        $row->_08_sick = $leave->days_sick;
                        $row->_08_other = $leave->days_others;
                        break;
                    case '09':
                        $row->_09_annual = $leave->days_annual;
                        $row->_09_unpaid = $leave->days_unpaid;
                        $row->_09_sick = $leave->days_sick;
                        $row->_09_other = $leave->days_others;
                        break;
                    case '10':
                        $row->_10_annual = $leave->days_annual;
                        $row->_10_unpaid = $leave->days_unpaid;
                        $row->_10_sick = $leave->days_sick;
                        $row->_10_other = $leave->days_others;
                        break;
                    case '11':
                        $row->_11_annual = $leave->days_annual;
                        $row->_11_unpaid = $leave->days_unpaid;
                        $row->_11_sick = $leave->days_sick;
                        $row->_11_other = $leave->days_others;
                        break;
                    case '12':
                        $row->_12_annual = $leave->days_annual;
                        $row->_12_unpaid = $leave->days_unpaid;
                        $row->_12_sick = $leave->days_sick;
                        $row->_12_other = $leave->days_others;
                        break;
                }
            }
            array_push($array, $row);
        }





        return $array;
    }

}
