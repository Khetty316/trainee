<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_frequency".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $interval
 * @property int|null $active_sts
 *
 * @property CmmsPreventiveWorkOrderMaster[] $cmmsPreventiveWorkOrderMasters
 */
class RefFrequency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_frequency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts'], 'integer'],
            [['name', 'interval'], 'string', 'max' => 255],
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
            'interval' => 'Interval',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsPreventiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPreventiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsPreventiveWorkOrderMaster::className(), ['frequency_id' => 'id']);
    }
    
    public static function getActiveDropdownlist_by_id() {
        return \yii\helpers\ArrayHelper::map(RefFrequency::findAll(["active_sts" => "1"]), "id", "name");
    }
}
