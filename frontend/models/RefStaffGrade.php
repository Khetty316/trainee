<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ref_staff_grade".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $is_active? 0 = yes, 1 = no
 *
 * @property EhTravelAllowanceDetail[] $ehTravelAllowanceDetails
 */
class RefStaffGrade extends \yii\db\ActiveRecord
{
    CONST EXEC_CODE = 'exec';
    CONST NONEXEC_CODE = 'nonExec';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_staff_grade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['is_active?'], 'integer'],
            [['code'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'is_active?' => 'Is Active?',
        ];
    }

    /**
     * Gets query for [[EhTravelAllowanceDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhTravelAllowanceDetails()
    {
        return $this->hasMany(EhTravelAllowanceDetail::className(), ['grade' => 'code']);
    }
    
    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefStaffGrade::find()->orderBy(['code' => SORT_ASC])->all(), "code", "name");
    }
}
