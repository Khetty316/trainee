<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;

class MyCommonFunction extends Model {
    /*
     * * ****************************************************** __FUNCTION RELATED TO DAYS__ ******************************************************** */

    /**
     * Returns the number of days in between 2 dates regardless of their order to get absolute difference
     */
    public static function countDays($date1, $date2) {
        $startTimeStamp = strtotime($date1);
        $endTimeStamp = strtotime($date2);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = intval($timeDiff / 86400); // 86400 seconds in one day
        return $numberDays;
    }

    /**
     * Returns the number of days in between 2 dates but keep the positive/negative notation to ensure clarity in their order
     */
    public static function dateDiff($date1, $date2) {
        $startTimeStamp = strtotime($date1);
        $endTimeStamp = strtotime($date2);
        $timeDiff = ($endTimeStamp - $startTimeStamp);
        $numberDays = intval($timeDiff / 86400); // 86400 seconds in one day
        return $numberDays;
    }

    public static function addDays($date, $days) {
        return date('Y-m-d', strtotime($date . ' + ' . $days . ' days'));
    }

    public static function getLastDayDate($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'Y-m-t');
    }

    public static function getFirstDayDate($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'Y-m-01');
    }

    /*
     * * ****************************************************** __FUNCTION RELATED TO MONTH__ ******************************************************** */

    /**
     * Returns an integer representing the total number of months between the two dates
     */
    public static function countMonths($date1, $date2) {
        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        return (($year2 - $year1) * 12) + ($month2 - $month1);
    }

    /**
     * Returns an array of numeric month between two dates in order eg.([11,12,01,02,03])
     */
    public static function findMonthInBetween($date1, $date2) {
        $start = new \DateTime($date1);
        $end = new \DateTime($date2);

        $months = [];

        while ($start <= $end) {
            $months[] = $start->format('m');
            $start->modify('+1 month');
        }

        return $months;
    }

    /**
     * Adds a specified number of months to a given date and returns the resulting date
     */
    public static function addMonths($date, $months) {
        return date('Y-m-d', strtotime($date . ' + ' . $months . ' months'));
    }

    public static function getMonthListArray() {
        $returnArr = [];
        for ($m = 1; $m <= 12; $m++) {
            $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
            $returnArr[$m] = $month;
        }
        return $returnArr;
    }

    public static function numberToMonthFull($monthNum) {
        $dateObj = \DateTime::createFromFormat('!m', $monthNum);
        return $dateObj->format('F');
    }

    /*
     * * ****************************************************** __FUNCTION RELATED TO YEAR__ ******************************************************** */

    /**
     * Get array of available year from any table
     */
    public static function getYearListFromTable($tableName, $yearAttributeName) {
        $year = (new \yii\db\Query())
                ->select(["min(`$yearAttributeName`) as minYear,max(`$yearAttributeName`) as maxYear"])
                ->from($tableName)
                ->one();
        $minYear = $year['minYear'] ? $year['minYear'] : date("Y");
        $maxYear = $year['maxYear'] ? $year['maxYear'] : date("Y");

        if (date("Y") < $minYear) {
            $minYear = (int) date("Y");
        }
        if (date("Y") > $maxYear) {
            $maxYear = (int) date("Y");
        }
        $yearsList = [];
//        $yearsList[$maxYear + 1] = "Year " . ($maxYear + 1);

        for ($i = $maxYear; $i >= $minYear; $i--) {
            $yearsList[$i] = "Year $i";
        }

        return $yearsList;
    }

    /**
     * Returns an array of year between two dates
     */
    public static function findYearInBetween($date1, $date2) {
        $start = new \DateTime($date1);
        $end = new \DateTime($date2);

        $startYear = (int) $start->format('Y');
        $endYear = (int) $end->format('Y');

        $years = range($startYear, $endYear);

        return $years;
    }

    /**
     * Returns either a simple list of years or an associative array with years as both keys and values depending on $associative
     */
    public static function generateYearList($startYear, $endYear, $associative = false) {
        $years = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            if ($associative) {
                $years[$year] = [];
            } else {
                $years[] = $year;
            }
        }

        return $years;
    }

    /*
     * * ****************************************************** __OTHER COMMON FUNCTIONS__ ******************************************************** */

    public static function mkDirIfNull($filePath) {
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
    }

    public static function DBSaveDate($date) {
        return ($date) ? MyFormatter::fromDateRead_toDateSQL($date) : "";
    }

    public static function myDropDown($arrayWithKey, $name, $class = '', $id = '', $value = '') {
        $str = '<select class="' . $class . '" name="' . $name . '" id="' . $id . '">';
        $str .= '<option value="">Select one...</option>';
        foreach ($arrayWithKey as $key => $item) {
            $str .= '<option value="' . $key . '" ' . ($value == $key ? 'selected' : '') . '>' . $item . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    public static function myDropDownNoEmpty($arrayWithKey, $name, $class = '', $id = '', $value = '') {
        $str = '<select class="' . $class . '" name="' . $name . '" id="' . $id . '">';
//        $str .= '<option value="">Select one...</option>';
        foreach ($arrayWithKey as $key => $item) {
            $str .= '<option value="' . $key . '" ' . ($value == $key ? 'selected' : '') . '>' . $item . '</option>';
        }
        $str .= '</select>';

        return $str;
    }

    public static function saveFile($file, $filePath, $filename = '') {

        MyCommonFunction::mkDirIfNull($filePath);
        if ($filename == '') {
            $filename = $file->baseName . '.' . $file->extension;
        }
        $finalPath = $filePath . '/' . $filename;
        $file->saveAs($finalPath);
    }

    public static function nowDateTime() {
        return date('Ymdhis', time());
    }

    public static function checkRoles($roleList = []) {
        foreach ($roleList as $role) {
            if (Yii::$app->user->can($role)) {
                return true;
            }
        }
        return false;
    }

    public static function activeFormDateInput($form, $model, $attribute, $label) {
        return $form->field($model, $attribute, ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(\yii\jui\DatePicker::className(), [
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'dd/mm/yyyy',
                                'autocomplete' => 'off',
                            ],
                            'clientOptions' => [
                                'dateFormat' => 'dd/mm/yy',
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                                'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                            ],
                        ])
                        ->label($label);
    }

    public static function htmlFormAutocompleteInput($name, $value, $source, $class) {
        $form = \yii\jui\AutoComplete::widget([
                    'name' => $name,
                    'value' => $value,
                    'clientOptions' => [
                        'source' => $source,
                    ],
                    'options' => [
                        'class' => $class,
                    ],
        ]);

        return $form;
    }

}
