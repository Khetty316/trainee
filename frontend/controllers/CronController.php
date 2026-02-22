<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\myTools\MyLogTxt;
use frontend\models\cron\CronEmail;
use frontend\models\common\RefSystemConfig;

//use frontend\models\working\announcement\AnnouncementMaster;
//include_once( Yii::getAlias('@webroot') . "/library/phpqrcode/qrlib.php");

/**
 * AssetController implements the CRUD actions for AssetMaster model.
 */
class CronController extends Controller {

    public function actionCheckCronEveryMinute($key) {
        if ($key != Yii::$app->params['cronValidationKey']) {
            return false;
        }
        $this->retraceEmailSent();
        return true;
    }

    public function actionTestEmail($key,$id) {
        if ($key != Yii::$app->params['cronValidationKey']) {
            return false;
        }
        $this->sendCronEmails($id);
        return true;
    }

    //Check if email is sent using start_date_time column
    private function retraceEmailSent() {
        $emails = CronEmail::find()->where(['active_sts' => 1, 'emailed' => 0])->all();

        foreach ((array) $emails as $email) {
            $latestEmail = CronEmail::findOne($email->id);
            $timeDiff = (time()) - ($latestEmail->start_date_time);
            if (!$latestEmail->start_date_time) {
                $this->sendCronEmails($email->id);
                continue;
            } else
            if ($timeDiff > $this->caclSeconds()) {
                $this->sendCronEmails($email->id);
            }
        }
        return true;
    }

    //sends email
    private function sendCronEmails($id) {
        $email = CronEmail::findOne($id);
        $email->start_date_time = time();
        $email->sdt_timestamp = new \yii\db\Expression('NOW()');
        if ($email->update()) {
            if ($email->sendEmail()) {
                $email->emailed = 1;
                $email->update();
            }
        }
    }

    //calculate range of current time to start_date_time of email sent to compare with defaultRetraceTime
    private function caclSeconds() {
        $defaultRetraceTime = RefSystemConfig::getValue_defaultEmailRetraceTime();
        $inSeconds = $defaultRetraceTime * 60;

        return $inSeconds;
    }

}
