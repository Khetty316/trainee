<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_corrective_machine_breakdown_type".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $active_sts
 *
 * @property CmmsCorrectiveWorkRequest[] $cmmsCorrectiveWorkRequests
 */
class RefCorrectiveMachineBreakdownType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_corrective_machine_breakdown_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'active_sts'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkRequests()
    {
        return $this->hasMany(CmmsCorrectiveWorkRequest::className(), ['machine_breakdown_type_id' => 'id']);
    }
}
