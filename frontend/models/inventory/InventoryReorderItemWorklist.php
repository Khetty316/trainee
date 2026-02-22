<?php

namespace frontend\models\inventory;

use Yii;

/**
 * This is the model class for table "inventory_reorder_item_worklist".
 *
 * @property int $id
 * @property int|null $inventory_reorder_item_id
 * @property int|null $received_quantity
 * @property int|null $received_by
 * @property string|null $received_at
 *
 * @property User $receivedBy
 * @property InventoryReorderItem $inventoryReorderItem
 */
class InventoryReorderItemWorklist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_reorder_item_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_reorder_item_id', 'received_quantity', 'received_by'], 'integer'],
            [['received_at'], 'safe'],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
            [['inventory_reorder_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryReorderItem::className(), 'targetAttribute' => ['inventory_reorder_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_reorder_item_id' => 'Inventory Reorder Item ID',
            'received_quantity' => 'Received Quantity',
            'received_by' => 'Received By',
            'received_at' => 'Received At',
        ];
    }

    /**
     * Gets query for [[ReceivedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'received_by']);
    }

    /**
     * Gets query for [[InventoryReorderItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItem()
    {
        return $this->hasOne(InventoryReorderItem::className(), ['id' => 'inventory_reorder_item_id']);
    }
}
