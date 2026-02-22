<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_pay_allowance".
 *
 * @property string $code
 * @property string|null $allowance_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property HrPayslipAllowance[] $hrPayslipAllowances
 */
class RefPayAllowance extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_pay_allowance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['allowance_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'allowance_name' => 'Allowance Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[HrPayslipAllowances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipAllowances() {
        return $this->hasMany(HrPayslipAllowance::className(), ['allowance_code' => 'code']);
    }

}
