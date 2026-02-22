<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_pay_overtime".
 *
 * @property string $code
 * @property string|null $overtime_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property HrPayslipOvertime[] $hrPayslipOvertimes
 */
class RefPayOvertime extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_pay_overtime';
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
            [['overtime_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'overtime_name' => 'Overtime Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[HrPayslipOvertimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipOvertimes() {
        return $this->hasMany(HrPayslipOvertime::className(), ['overtime_code' => 'code']);
    }

}
