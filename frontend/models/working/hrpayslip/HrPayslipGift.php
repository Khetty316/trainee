<?php

namespace frontend\models\working\hrpayslip;

use Yii;
use frontend\models\common\RefPayGift;

/**
 * This is the model class for table "hr_payslip_gift".
 *
 * @property int $id
 * @property int $payslip_id
 * @property string $gift_code
 * @property string|null $description
 * @property float|null $amount
 *
 * @property RefPayGift $giftCode
 * @property HrPayslip $payslip
 */
class HrPayslipGift extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_payslip_gift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['payslip_id', 'gift_code'], 'required'],
            [['payslip_id'], 'integer'],
            [['amount'], 'number'],
            [['gift_code'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255],
            [['gift_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefPayGift::className(), 'targetAttribute' => ['gift_code' => 'code']],
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
            'gift_code' => 'Gift Code',
            'description' => 'Description',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[GiftCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGiftCode() {
        return $this->hasOne(RefPayGift::className(), ['code' => 'gift_code']);
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
