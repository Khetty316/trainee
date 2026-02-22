<?php

namespace frontend\models\working\hrpayslip;

use Yii;
use common\models\User;
use frontend\models\common\RefPayAllowance;

/**
 * This is the model class for table "hr_payslip_allowance".
 *
 * @property int $id
 * @property int $payslip_id
 * @property string|null $allowance_code
 * @property float|null $amount
 *
 * @property RefPayAllowance $allowanceCode
 * @property HrPayslip $payslip
 */
class HrPayslipAllowance extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_payslip_allowance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['payslip_id'], 'required'],
            [['payslip_id'], 'integer'],
            [['amount'], 'number'],
            [['allowance_code'], 'string', 'max' => 20],
            [['allowance_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefPayAllowance::className(), 'targetAttribute' => ['allowance_code' => 'code']],
            [['payslip_id'], 'exist', 'skipOnError' => true, 'targetClass' => HrPayslip::className(), 'targetAttribute' => ['payslip_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'payslip_id' => 'Payslip ID',
            'allowance_code' => 'Allowance Code',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[AllowanceCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAllowanceCode() {
        return $this->hasOne(RefPayAllowance::className(), ['code' => 'allowance_code']);
    }

    /**
     * Gets query for [[Payslip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayslip() {
        return $this->hasOne(HrPayslip::className(), ['id' => 'payslip_id']);
    }

}
