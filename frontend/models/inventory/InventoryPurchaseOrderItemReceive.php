<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_purchase_order_item_receive".
 *
 * @property int $id
 * @property int|null $receive_batch_id
 * @property int|null $inventory_po_item_id
 * @property int|null $received_quantity
 * @property int|null $received_by
 * @property string|null $received_at
 *
 * @property User $receivedBy
 * @property InventoryPurchaseOrderReceiveBatch $receiveBatch
 * @property InventoryPurchaseOrderItem $inventoryPoItem
 */
class InventoryPurchaseOrderItemReceive extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_order_item_receive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['receive_batch_id', 'inventory_po_item_id', 'received_quantity', 'received_by'], 'integer'],
            [['received_at'], 'safe'],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
            [['receive_batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderReceiveBatch::className(), 'targetAttribute' => ['receive_batch_id' => 'id']],
            [['inventory_po_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderItem::className(), 'targetAttribute' => ['inventory_po_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'receive_batch_id' => 'Receive Batch ID',
            'inventory_po_item_id' => 'Inventory Po Item ID',
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
    public function getReceivedBy() {
        return $this->hasOne(User::className(), ['id' => 'received_by']);
    }

    /**
     * Gets query for [[ReceiveBatch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveBatch() {
        return $this->hasOne(InventoryPurchaseOrderReceiveBatch::className(), ['id' => 'receive_batch_id']);
    }

    /**
     * Gets query for [[InventoryPoItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPoItem() {
        return $this->hasOne(InventoryPurchaseOrderItem::className(), ['id' => 'inventory_po_item_id']);
    }

    public function beforeSave($insert) {
        $this->received_at = new \yii\db\Expression('NOW()');
        $this->received_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
}
