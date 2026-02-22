<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_asset_or_equipment_details".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $fault_type
 * @property string|null $fault_primary_detail
 * @property string|null $fault_secondary_detail
 * @property int|null $active_sts
 *
 * @property CmmsAssetOrEquipmentList[] $cmmsAssetOrEquipmentLists
 */
class CmmsAssetDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_asset_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'fault_type', 'fault_primary_detail', 'fault_secondary_detail'], 'string', 'max' => 255],
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
            'fault_type' => 'Fault Type',
            'fault_primary_detail' => 'Fault Primary Detail',
            'fault_secondary_detail' => 'Fault Secondary Detail',
        ];
    }

    /**
     * Gets query for [[CmmsAssetOrEquipmentLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetLists()
    {
        return $this->hasMany(CmmsAssetList::className(), ['asset_id' => 'id']);
    }
}
