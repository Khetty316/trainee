<?php

namespace frontend\models\cmms;

use Yii;
use frontend\models\inventory\InventoryModel;

/**
 * This is the model class for table "cmms_tool_list".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $inventory_model_id
 * @property int|null $qty
 * @property string|null $name
 *
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property InventoryModel $inventoryModel
 */
class CmmsToolList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_tool_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_model_id', 'qty'], 'integer'],
            [['description', 'name'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'inventory_model_id' => 'Inventory Model ID',
            'qty' => 'Qty',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['tool_list_id' => 'id']);
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
