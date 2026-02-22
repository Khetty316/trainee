<?php

namespace frontend\models\cmms;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cmms_part_list".
 *
 * @property int $id
 * @property int|null $asset_id
 * @property int|null $inventory_id
 *
 * @property CmmsAssetList[] $cmmsAssetLists
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 * @property CmmsAssetFaults $asset
 * @property VInventoryModel $inventory
 */
class CmmsPartList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_part_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asset_id', 'inventory_id'], 'integer'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetFaults::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['inventory_id'], 'exist', 'skipOnError' => true, 'targetClass' => VInventoryModel::className(), 'targetAttribute' => ['inventory_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_id' => 'Asset ID',
            'inventory_id' => 'Inventory ID',
        ];
    }

    /**
     * Gets query for [[CmmsAssetLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetLists()
    {
        return $this->hasMany(CmmsAssetList::className(), ['part_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['part_id' => 'id']);
    }

    /**
     * Gets query for [[Asset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(CmmsAssetFaults::className(), ['id' => 'asset_id']);
    }

    /**
     * Gets query for [[Inventory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(VInventoryModel::className(), ['id' => 'inventory_id']);
    }
}
