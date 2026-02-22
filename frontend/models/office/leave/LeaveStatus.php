<?php

// Customize for NPL

namespace frontend\models\office\leave;

use yii\base\Model;
use frontend\models\working\leavemgmt\VLeaveEntitlementDashboard;
use frontend\models\working\leavemgmt\VLeaveAnnualSummary;
use frontend\models\office\leave\RefLeaveType;
use common\models\myTools\MyFormatter;

class LeaveStatus extends Model {

    public $user_id = null;
    public $annual_bringForward = 0.0;
    public $annual_toNextYear = 0.0;
    public $annual_approved = 0.0;
    public $annual_pending = 0.0;
    public $annual_entitlementCurrent = 0.0;
    public $annual_balanceCurrent = 0.0;
    public $annual_entitlementYearEnd = 0.0;
    public $annual_balanceYearEnd = 0.0;
    public $annual_balanceCurrentCanApply = 0.0;
    // 
    public $sick_entitlement = 0.0;
    public $sick_approved = 0.0;
    public $sick_pending = 0.0;
    public $sick_entitlementYearEnd = 0.0;
    public $sick_balanceYearEnd = 0.0;
    public $sick_balanceCurrentCanApply = 0.0;
    public $totalPending = 0.0;
    public $otherLeaveStatus = [];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
//            [['annual_bring_forward'],'number']
//            ['start_date', 'date', 'timestampAttribute' => 'start_date'],
//            ['end_date', 'date', 'timestampAttribute' => 'end_date'],
//            ['start_date', 'compare', 'compareAttribute' => 'end_date', 'operator' => '<','enableClientValidation' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'annual_bringForward' => 'Bring Forward',
        ];
    }

    /**
     * 
     * @param type $requestorId
     * VOID
     */
    public static function getPersonalLeaveStatus($requestorId, $year) {
        $model = new LeaveStatus();
        $model->user_id = $requestorId;

        $entSummaryDashboard = VLeaveEntitlementDashboard::find()
                ->where('user_id = ' . $requestorId . " AND year = " . $year)
                ->one();
        $entSummary = \frontend\models\working\leavemgmt\VLeaveEntitlementSummary::find()
                ->where('user_id = ' . $requestorId . " AND year = " . $year)
                ->all();

        if ($entSummaryDashboard) {

//            $leaveSummary = VLeaveAnnualSummary::find()->where(['year_of_leave' => $year, 'user_id' => $requestorId])->all();
            $annualLeaveSummary = VLeaveAnnualSummary::find()->where(['year_of_leave' => $year, 'user_id' => $requestorId, 'leave_type_code' => RefLeaveType::codeAnnual])->one();
            $sickLeaveSummary = VLeaveAnnualSummary::find()->where(['year_of_leave' => $year, 'user_id' => $requestorId, 'leave_type_code' => RefLeaveType::codeSick])->one();

            $otherLeavePendingDays = VLeaveAnnualSummary::find()->where(['year_of_leave' => $year, 'user_id' => $requestorId])
                            ->andWhere("leave_type_code NOT IN ('" . RefLeaveType::codeAnnual . "','" . RefLeaveType::codeSick . "')")->sum("total_pending");
            
            //Annual Leave
            $model->annual_bringForward = $entSummaryDashboard->annual_bring_forward_days ?? 0;
            $model->annual_toNextYear = $entSummaryDashboard->annual_bring_next_year_days ?? 0;
            $model->annual_entitlementCurrent = MyFormatter::floorWholeNum($entSummaryDashboard->annual_current);
            $model->annual_entitlementYearEnd = MyFormatter::floorWholeNum($entSummaryDashboard->annual_year);

            $model->annual_approved = $annualLeaveSummary->total_approved ?? 0;
            $model->annual_pending = $annualLeaveSummary->total_pending ?? 0;

            $model->annual_balanceCurrent = $model->annual_entitlementCurrent + $model->annual_bringForward - $model->annual_approved - $model->annual_toNextYear;
            $model->annual_balanceYearEnd = $model->annual_entitlementYearEnd + $model->annual_bringForward - $model->annual_approved - $model->annual_toNextYear;
            $model->annual_balanceCurrentCanApply = $model->annual_entitlementCurrent + $model->annual_bringForward - $model->annual_approved - $model->annual_pending - $model->annual_toNextYear;

            //Sick Leave
            $model->sick_entitlementYearEnd = $entSummaryDashboard->sick_year ?? 0;
            $model->sick_approved = $sickLeaveSummary->total_approved ?? 0;
            $model->sick_pending = $sickLeaveSummary->total_pending ?? 0;
            $model->sick_balanceYearEnd = MyFormatter::floorWholeNum($model->sick_entitlementYearEnd - $model->sick_approved);
            $model->sick_balanceCurrentCanApply = $model->sick_balanceYearEnd - $model->sick_pending;
            
            //Pending Leave
            $model->totalPending = $model->annual_pending + $model->sick_pending + ($otherLeavePendingDays ?? 0);

            $model->otherLeaveStatus = VLeaveAnnualSummary::find()->where(['year_of_leave' => $year, 'user_id' => $requestorId])
                    ->andWhere("leave_type_code NOT IN ('" . RefLeaveType::codeAnnual . "','" . RefLeaveType::codeSick . "')")->asArray()
                    ->all();

            foreach ($model->otherLeaveStatus as $key => $otherStatus) {
                $key2 = array_search($otherStatus['leave_type_code'], array_column($entSummary, 'leave_type_code'));
                $model->otherLeaveStatus[$key]['year_end_entitle'] = $entSummary[$key2]->year_end_entitle;
                $model->otherLeaveStatus[$key]['cur_entitle'] = $entSummary[$key2]->cur_entitle;
            }
        }

        return $model;
    }

}
