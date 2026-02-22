<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;

class MyFormatter extends Model {

    /**
     * Round the number in 0.5 as unit
     * @param type $num
     * @return string
     */
    public static function floorHalfDecimal($num) {
        if (is_numeric($num)) {
            return floor($num * 2) / 2;
        } else {
            return "0.0";
        }
    }

    /**
     * Round the number down but check if its numeric first
     * @param type $num
     * @return string
     */
    public static function floorWholeNum($num) {
        if (is_numeric($num)) {
            return floor($num);
        } else {
            return "0.0";
        }
    }

    public static function asDecimal0($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 0);
        } else {
            return "";
        }
    }

    public static function asDecimal2($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "";
        }
    }

    public static function asDecimal1_emptyDash($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 1);
        } else {
            return "-";
        }
    }

    public static function asDecimal2_emptyDash($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "-";
        }
    }

    public static function asDecimal2_emptyZero($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "0.00";
        }
    }

    public static function asDecimal2NoSeparator($num) {
        if (is_numeric($num)) {
            return str_replace(",", "", Yii::$app->formatter->asDecimal($num, 2));
        } else {
            return "";
        }
    }

    public static function asCurrency($num) {
        if (is_numeric($num)) {
            return "RM " . Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "#ERROR, NOT NUMBER";
        }
    }

    public static function asDateTime_Read($date) {
        if ($date) {
            $theDate = date_create($date);
            if ($theDate) {
                return date_format($theDate, "d/m/Y H:i:s");
            }
        } else {
            return null;
        }
    }

    public static function getYear($date) {
        $theDate = date_create($date);
        return date_format($theDate, "Y");
    }

    public static function asDateTime_ReaddmYHi($date) {
        if ($date) {
            $theDate = date_create($date);
            if ($theDate) {
                return date_format($theDate, "d/m/Y H:i");
            }
        } else {
            return null;
        }
    }

    public static function fromDateRead_toDateSQL($date) {
        if ($date == "") {
            return null;
        } else {
            $theDate = str_replace('/', '-', $date);
            return date('Y-m-d', strtotime($theDate));
        }
    }

    public static function fromDateTimeSql_toDateSql($date) {
        if ($date == "") {
            return null;
        } else {
            $theDate = date_create($date);
            return date_format($theDate, "Y-m-d");
        }
    }

    public static function asDate_Read($date) {
        if ($date) {
            $theDate = date_create($date);
            return date_format($theDate, 'd/m/Y');
        } else {
            return null;
        }
    }

    public static function asDate_Read_dm($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'd/m');
    }

    public static function asDate_Read_dnY($date) {
        if ($date) {
            $theDate = date_create($date);
            return date_format($theDate, 'd-M-Y');
        } else {
            return " - ";
        }
    }

    public static function asDay_Read($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'D');
    }

    public static function asDayLong_Read($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'l');
    }

    public static function changeDateFormat_readToDB($date) {
        $var = str_replace('/', '-', $date);
        return date('Y-m-d', strtotime($var));
    }

    public static function fromDateTimeExcelMDY_toDateSql($date) {
        if ($date == "") {
            return null;
        } else {
            $ymd = \DateTime::createFromFormat('d/m/Y', $date);
            return $ymd ? $ymd->format('Y-m-d') : false;
        }
    }

}
