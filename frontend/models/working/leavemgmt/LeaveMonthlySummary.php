<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use common\models\User;

/**
 * This is the model class for table "leave_monthly_summary".
 *
 * @property int $id
 * @property int|null $requestor_id
 * @property int $year
 * @property string $month
 * @property float|null $days_annual
 * @property float|null $days_unpaid
 * @property float|null $days_sick
 * @property float|null $days_others
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $requestor
 */
class LeaveMonthlySummary extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_monthly_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['requestor_id', 'year', 'created_by'], 'integer'],
            [['year', 'month'], 'required'],
            [['days_annual', 'days_unpaid', 'days_sick', 'days_others'], 'number'],
            [['created_at'], 'safe'],
            [['month'], 'string', 'max' => 2],
            [['requestor_id', 'year', 'month'], 'unique', 'targetAttribute' => ['requestor_id', 'year', 'month']],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requestor_id' => 'Requestor ID',
            'year' => 'Year',
            'month' => 'Month',
            'days_annual' => 'Days Annual',
            'days_unpaid' => 'Days Unpaid',
            'days_sick' => 'Days Sick',
            'days_others' => 'Days Others',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
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
     * 
     * @param type $staffId
     * @param type $year
     * @param type $month
     * @param type $monthlyAnnual
     * @param type $monthlyUnpaid
     * @param type $monthlySick
     * @param type $monthlyOthers
     * 
     * Create record for monthly summary
     * 
     * @return type
     */
    public function createRecord($staffId, $year, $month, $monthlyAnnual, $monthlyUnpaid, $monthlySick, $monthlyOthers) {
        $this->requestor_id = $staffId;
        $this->year = $year;
        $this->month = $month;
        $this->days_annual = $monthlyAnnual;
        $this->days_unpaid = $monthlyUnpaid;
        $this->days_sick = $monthlySick;
        $this->days_others = $monthlyOthers;
        $this->created_by = Yii::$app->user->identity->id;
        return $this->save();
    }

    public static function getSummarizedTotal($requestorId, $year) {
        return (new \yii\db\Query())->select([
                            'SUM(IFNULL(days_annual, 0)) AS days_annual',
                            'SUM(IFNULL(days_unpaid, 0)) AS days_unpaid',
                            'SUM(IFNULL(days_sick, 0)) AS days_sick',
                            'SUM(IFNULL(days_others, 0)) AS days_others'])
                        ->from('leave_monthly_summary')
                        ->where("year = " . $year)
                        ->andWhere(['requestor_id' => $requestorId])
                        ->one();
    }

    public static function checkAlreadyConfirm($date) {
        $tempBean = LeaveMonthlySummary::find()->where('month = ' . date_format($date, "m"))->andWhere('year = ' . date_format($date, "Y"))->one();
        if ($tempBean) {
            return 1;
        }

        return 0;
    }

    public static function checkUnconfirmedMonth_5() {

        $returnArr = [];
        for ($i = 0; $i < 5; $i++) {
            if ($i > 0) {
                $str = "-" . $i . " month";
                $tempYear = date("Y", strtotime($str));
                $tempMonth = date("m", strtotime($str));
                $tempMonthName = date("M", strtotime($str));
                $tempOptionValue = date("mY", strtotime($str));
            } else {
                $tempYear = date("Y");
                $tempMonth = date("m");
                $tempMonthName = date("M");
                $tempOptionValue = date("mY");
            }

//            $alreadyConfirmed = LeaveMonthlySummary::find()->where(['month' => $tempMonth, 'year' => $tempYear])->count();
            $toBeConfirmed = User::find()->select(["user.*","leave_monthly_summary.id as leaveId"])->where(['status' => User::STATUS_ACTIVE])
                    ->leftJoin("leave_monthly_summary", "user.id=leave_monthly_summary.requestor_id AND leave_monthly_summary.month='$tempMonth' AND leave_monthly_summary.year='$tempYear'")
                    ->having("leaveId IS NULL")
                    ->count();

//            if ($alreadyConfirmed == 0) {
            if ($toBeConfirmed > 0) {
                $returnArr[$tempOptionValue] = $tempMonthName . " " . $tempYear;
            }
        }
        return $returnArr;
    }

    public function setLeaveStartMonth() {
//        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement = LeaveEntitlement::find()->where(['user_id' => $this->requestor_id, 'year' => $this->year])->one();
        $month = $this->month - 1;

        if ($month == 0) {
            return true;
        }

        $this->month = ((strlen($month) < 2) ? "0" : "") . $month;
        $this->days_annual = floor($leaveEntitlement->annual_entitled ?? null / 12 * $month * 2) / 2;
        $this->days_sick = floor($leaveEntitlement->sick_entitled ?? null / 12 * $month * 2) / 2;
        $this->days_unpaid = 0;
        $this->days_others = 0;
        $this->created_by = Yii::$app->user->identity->id;
        return $this->save();
    }

}
