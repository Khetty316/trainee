<?php

namespace frontend\models\cmms;

use Yii;
use frontend\models\inventory\InventoryModel;

/**
 * This is the model class for table "cmms_part_list".
 *
 * @property int $id
 * @property int|null $asset_id
 * @property int|null $inventory_model_id
 * @property int|null $qty
 * @property string|null $name
 *
 * @property CmmsAssetList[] $cmmsAssetLists
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property CmmsAssetFaults $asset
 * @property InventoryModel $inventoryModel
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
            [['asset_id', 'inventory_model_id', 'qty'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetFaults::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
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
            'inventory_model_id' => 'Inventory Model ID',
            'qty' => 'Qty',
            'name' => 'Name',
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
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['part_list_id' => 'id']);
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
     * Gets query for [[InventoryModel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryModel()
    {
        return $this->hasOne(InventoryModel::className(), ['id' => 'inventory_model_id']);
    }
}
