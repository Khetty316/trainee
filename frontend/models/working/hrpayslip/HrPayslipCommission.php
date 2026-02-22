<?php

namespace frontend\models\working\hrpayslip;

use Yii;

/**
 * This is the model class for table "hr_payslip_commission".
 *
 * @property int $id
 * @property int $payslip_id
 * @property string|null $description
 * @property float|null $amount
 *
 * @property HrPayslip $payslip
 */
class HrPayslipCommission extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_payslip_commission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['payslip_id'], 'required'],
            [['payslip_id'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string', 'max' => 255],
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
            'description' => 'Description',
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

}
