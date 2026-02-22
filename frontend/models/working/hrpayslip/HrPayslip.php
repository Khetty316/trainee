<?php

namespace frontend\models\working\hrpayslip;

use Yii;
use common\models\User;

/**
 * This is the model class for table "hr_payslip".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $pay_year
 * @property int|null $pay_month
 * @property string|null $pay_period
 * @property string|null $designation
 * @property float|null $basic_salary
 * @property float|null $bonus
 * @property float|null $commission
 * @property float|null $director_fee
 * @property float|null $epf
 * @property float|null $socso
 * @property float|null $eis_sip
 * @property float|null $income_tax
 * @property float|null $unpaid_leave
 * @property float|null $annual_leave_pay
 * @property float|null $net_salary
 * @property float|null $employer_epf
 * @property float|null $employer_socso
 * @property float|null $employer_eis_sip
 * @property int $pdf_released
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $user
 * @property HrPayslipAdvance[] $hrPayslipAdvances
 * @property HrPayslipAllowance[] $hrPayslipAllowances
 * @property HrPayslipCommission[] $hrPayslipCommissions
 * @property HrPayslipGift[] $hrPayslipGifts
 * @property HrPayslipOvertime[] $hrPayslipOvertimes
 */
class HrPayslip extends \yii\db\ActiveRecord {

    public $staffId;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_payslip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'basic_salary', 'pay_period', 'pay_year', 'pay_month'], 'required'],
            [['user_id', 'pay_month', 'pdf_released', 'created_by', 'updated_by'], 'integer'],
            ['pay_year', 'string', 'length' => [4, 4]],
            [['created_at', 'updated_at'], 'safe'],
            [['basic_salary', 'bonus', 'commission', 'director_fee', 'epf', 'socso', 'eis_sip', 'income_tax', 'unpaid_leave', 'annual_leave_pay', 'net_salary', 'employer_epf', 'employer_socso', 'employer_eis_sip'], 'number'],
            [['designation', 'staffId'], 'string', 'max' => 255],
            [['user_id', 'pay_year', 'pay_month'], 'unique', 'targetAttribute' => ['user_id', 'pay_year', 'pay_month']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'pay_year' => 'Pay Year',
            'pay_month' => 'Pay Month',
            'pay_period' => 'Pay Period',
            'designation' => 'Designation',
            'basic_salary' => 'Basic Salary',
            'bonus' => 'Bonus',
            'commission' => 'Commission',
            'director_fee' => 'Director Fee',
            'epf' => 'Epf',
            'socso' => 'Socso',
            'eis_sip' => 'Eis Sip',
            'income_tax' => 'Income Tax',
            'unpaid_leave' => 'Unpaid Leave',
            'annual_leave_pay' => 'Annual Leave Pay',
            'net_salary' => 'Net Salary',
            'employer_epf' => 'Employer Epf',
            'employer_socso' => 'Employer Socso',
            'employer_eis_sip' => 'Employer Eis Sip',
            'pdf_released' => 'Pdf Released',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[HrPayslipAdvances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipAdvances() {
        return $this->hasMany(HrPayslipAdvance::className(), ['payslip_id' => 'id']);
    }

    /**
     * Gets query for [[HrPayslipAllowances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipAllowances() {
        return $this->hasMany(HrPayslipAllowance::className(), ['payslip_id' => 'id']);
    }

    /**
     * Gets query for [[HrPayslipCommissions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipCommissions() {
        return $this->hasMany(HrPayslipCommission::className(), ['payslip_id' => 'id']);
    }

    /**
     * Gets query for [[HrPayslipGifts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipGifts() {
        return $this->hasMany(HrPayslipGift::className(), ['payslip_id' => 'id']);
    }

    /**
     * Gets query for [[HrPayslipOvertimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipOvertimes() {
        return $this->hasMany(HrPayslipOvertime::className(), ['payslip_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        $this->pay_period = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->pay_period);
        $this->save();
        $this->sub_saveAllowance();
        $this->sub_saveCommission();
        $this->sub_payTravelClaim();
        $this->sub_saveOT();
        $this->sub_saveAdvance();
        $this->sub_saveGift();
        return true;
    }

    private function sub_saveAllowance() {
        HrPayslipAllowance::deleteAll('payslip_id=' . $this->id);
        $allowanceAmount = Yii::$app->request->post('allowanceAmount');
        $allowanceCode = Yii::$app->request->post('allowanceCode');



        foreach ($allowanceCode as $key => $type) {
            if ($allowanceAmount[$key] && $allowanceAmount[$key] > 0) {
                $allowance = new HrPayslipAllowance();
                $allowance->payslip_id = $this->id;
                $allowance->allowance_code = $type;
                $allowance->amount = $allowanceAmount[$key];
                $allowance->save();
            }
        }
    }

    private function sub_saveOT() {
        HrPayslipOvertime::deleteAll('payslip_id=' . $this->id);
        $otAmount = Yii::$app->request->post('otAmount');
        $otCode = Yii::$app->request->post('otCode');

        foreach ($otCode as $key => $type) {
            if ($otAmount[$key] && $otAmount[$key] > 0) {
                $OT = new HrPayslipOvertime();
                $OT->payslip_id = $this->id;
                $OT->overtime_code = $type;
                $OT->amount = $otAmount[$key];
                $OT->save();
            }
        }
    }

    private function sub_saveAdvance() {
        HrPayslipAdvance::deleteAll('payslip_id=' . $this->id);
        $advAmount = Yii::$app->request->post('advAmount');
        $advDesc = Yii::$app->request->post('advDesc');
        $disableInput = 0;
        if ($advDesc) {
            foreach ($advDesc as $key => $desc) {
                if ($desc == "") {
                    $disableInput++;
                    continue;
                }
                $adv = new HrPayslipAdvance();
                $adv->payslip_id = $this->id;
                $adv->description = $desc;
                $adv->amount = $advAmount[$key - $disableInput];
                $adv->save();
            }
        }
    }

    private function sub_saveCommission() {
        HrPayslipCommission::deleteAll('payslip_id=' . $this->id);
        $commAmount = Yii::$app->request->post('commAmount');
        $commDesc = Yii::$app->request->post('commDesc');
        $disableInput = 0;
        if ($commDesc) {
            foreach ($commDesc as $key => $desc) {
                if ($desc == "") {
                    $disableInput++;
                    continue;
                }
                $adv = new HrPayslipCommission();
                $adv->payslip_id = $this->id;
                $adv->description = $desc;
                $adv->amount = $commAmount[$key - $disableInput];
                $adv->save();
            }
        }
    }

    private function sub_saveGift() {
        HrPayslipGift::deleteAll('payslip_id=' . $this->id);
        $giftCode = Yii::$app->request->post('giftCode');
        $giftDesc = Yii::$app->request->post('giftDesc');
        $giftAmount = Yii::$app->request->post('giftAmount');
        $disableInput = 0;
        if ($giftCode) {
            foreach ($giftCode as $key => $code) {
                if ($code == "") {
                    $disableInput++;
                    continue;
                }
                $gift = new HrPayslipGift();
                $gift->payslip_id = $this->id;
                $gift->gift_code = $code;
                $gift->description = $giftDesc[$key - $disableInput];
                $gift->amount = $giftAmount[$key - $disableInput];
                $gift->save();
            }
        }
    }

    private function sub_payTravelClaim() {
        if (Yii::$app->request->post('claimIds') == "") {
            return true;
        }
        $claimIds = explode(",", Yii::$app->request->post('claimIds'));
        if (sizeof($claimIds) > 0) {
            foreach ($claimIds as $claimId) {
                $claim = \frontend\models\working\claim\ClaimsMaster::findOne($claimId);
                $claim->payClaim();
            }
        }
    }

}
