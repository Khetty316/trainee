<?php

namespace common\models\myTools;

use yii\base\Model;

class Mydebug extends Model {

    CONST filename = "debug.txt";

    public static function byFile($str = "TESTING HERE") {
        $file = fopen(self::filename, "a");
        fwrite($file, date("H:i:s") . " : " . $str . "\n");
        fclose($file);
    }

    public static function byFileW($str = "TESTING HERE") {
        $file = fopen(self::filename, "w");
        fwrite($file, date("H:i:s") . " : " . $str . "\n");
        fclose($file);
    }

    public static function dumpFileA($err) {
        ob_start();
        var_dump($err);
        $data = ob_get_clean();
        $fp = fopen(self::filename, "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function dumpFileW($err) {
        ob_start();
        var_dump($err);
        $data = ob_get_clean();
        $fp = fopen(self::filename, "w");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function vardump($str) {
        echo "<br/><br/><br/><br/><pre>";
//        var_dump($str,true);
        \yii\helpers\VarDumper::dump($str);
        echo "</pre>";
    }

    public static function printR($str) {
        echo "<pre>", print_r($str), "</pre>";
    }

    public static function echo($str) {
        echo "<br/><br/><br/>.$str";
    }

}
