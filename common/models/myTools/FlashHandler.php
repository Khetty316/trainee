<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;

class FlashHandler extends Model {

    public static function err($msg) {
        \yii::$app->session->setFlash("error", $msg);
    }

    public static function errAddon($msg) {
        $originalMsg = \yii::$app->session->getFlash("error");
        $msg = $originalMsg . '<br/>' . $msg;
        \yii::$app->session->setFlash('error', $msg);
    }

    public static function success($msg) {
        \yii::$app->session->setFlash("success", $msg);
    }

    public static function suc_stsUpdate() {
        \yii::$app->session->setFlash("success", "Status Update Success!");
    }

    public static function err_outdated() {
        \yii::$app->session->setFlash("error", "Status Update Fail! Someone might have updated this record!");
    }

    public static function err_getHelp() {
        \yii::$app->session->setFlash("error", "Status Update Fail! Please contact System Admin");
    }

    public static function err_getITHelp() {
        \yii::$app->session->setFlash("error", "Status Update Fail! Please contact IT Support!");
    }

}
