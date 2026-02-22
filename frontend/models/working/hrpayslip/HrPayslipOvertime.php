<?php

namespace frontend\models\working\hrpayslip;

use Yii;
use frontend\models\common\RefPayOvertime;

/**
 * This is the model class for table "hr_payslip_overtime".
 *
 * @property int $id
 * @property int $payslip_id
 * @property string|null $overtime_code
 * @property float|null $amount
 *
 * @property HrPayslip $payslip
 * @property RefPayOvertime $overtimeCode
 */
class HrPayslipOvertime extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_payslip_overtime';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['payslip_id'], 'required'],
            [['payslip_id'], 'integer'],
            [['amount'], 'number'],
            [['overtime_code'], 'string', 'max' => 20],
            [['payslip_id'], 'exist', 'skipOnError' => true, 'targetClass' => HrPayslip::className(), 'targetAttribute' => ['payslip_id' => 'id']],
            [['overtime_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefPayOvertime::className(), 'targetAttribute' => ['overtime_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'payslip_id' => 'Payslip ID',
            'overtime_code' => 'Overtime Code',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[Payslip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayslip() {
        return $this->hasOne(HrPayslip::className(), ['id' => 'payslip_id']);
    }

    /**
     * Gets query for [[OvertimeCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOvertimeCode() {
        return $this->hasOne(RefPayOvertime::className(), ['code' => 'overtime_code']);
    }

}
