<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_pay_gift".
 *
 * @property string $code
 * @property string|null $gift_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property HrPayslipGift[] $hrPayslipGifts
 */
class RefPayGift extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_pay_gift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['gift_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'gift_name' => 'Gift Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[HrPayslipGifts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrPayslipGifts()
    {
        return $this->hasMany(HrPayslipGift::className(), ['gift_code' => 'code']);
    }
}
