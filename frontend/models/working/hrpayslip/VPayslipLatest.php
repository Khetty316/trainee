<?php

namespace frontend\models\working\hrpayslip;

use Yii;

/**
 * This is the model class for table "v_payslip_latest".
 *
 * @property string|null $staff_id
 * @property string|null $fullname
 * @property int|null $pay_year
 * @property int|null $pay_month
 */
class VPayslipLatest extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_payslip_latest';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['pay_year', 'pay_month','user_id'], 'integer'],
            [['staff_id'], 'string', 'max' => 10],
            [['fullname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_id'=>'User ID',
            'staff_id' => 'Staff ID',
            'fullname' => 'Fullname',
            'pay_year' => 'Pay Year',
            'pay_month' => 'Pay Month',
        ];
    }

}
