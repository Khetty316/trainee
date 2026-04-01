<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_po_item_receive_allocation".
 *
 * @property int $id
 * @property int|null $inventory_po_item_receive_id
 * @property int|null $inventory_order_request_allocation_id
 * @property int|null $allocated_qty
 * @property int|null $received_by
 * @property string|null $received_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property InventoryPurchaseOrderItemReceive $inventoryPoItemReceive
 * @property InventoryOrderRequestAllocation $inventoryOrderRequestAllocation
 * @property User $receivedBy
 * @property User $updatedBy
 */
class InventoryPoItemReceiveAllocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_po_item_receive_allocation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_po_item_receive_id', 'inventory_order_request_allocation_id', 'allocated_qty', 'received_by', 'updated_by'], 'integer'],
            [['received_at', 'updated_at'], 'safe'],
            [['inventory_po_item_receive_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderItemReceive::className(), 'targetAttribute' => ['inventory_po_item_receive_id' => 'id']],
            [['inventory_order_request_allocation_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryOrderRequestAllocation::className(), 'targetAttribute' => ['inventory_order_request_allocation_id' => 'id']],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_po_item_receive_id' => 'Inventory Po Item Receive ID',
            'inventory_order_request_allocation_id' => 'Inventory Order Request Allocation ID',
            'allocated_qty' => 'Allocated Qty',
            'received_by' => 'Received By',
            'received_at' => 'Received At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryPoItemReceive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPoItemReceive()
    {
        return $this->hasOne(InventoryPurchaseOrderItemReceive::className(), ['id' => 'inventory_po_item_receive_id']);
    }

    /**
     * Gets query for [[InventoryOrderRequestAllocation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequestAllocation()
    {
        return $this->hasOne(InventoryOrderRequestAllocation::className(), ['id' => 'inventory_order_request_allocation_id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    
    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->received_at = new \yii\db\Expression('NOW()');
            $this->received_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }
}
