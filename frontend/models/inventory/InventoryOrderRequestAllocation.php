<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_order_request_allocation".
 *
 * @property int $id
 * @property int|null $inventory_po_item_id
 * @property int|null $inventory_order_request_id
 * @property int|null $order_qty
 * @property int|null $received_qty
 * @property int|null $pending_qty
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property InventoryOrderRequest $inventoryOrderRequest
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryPurchaseOrderItem $inventoryPoItem
 */
class InventoryOrderRequestAllocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_order_request_allocation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_po_item_id', 'inventory_order_request_id', 'order_qty', 'received_qty', 'pending_qty', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['inventory_order_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryOrderRequest::className(), 'targetAttribute' => ['inventory_order_request_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['inventory_po_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderItem::className(), 'targetAttribute' => ['inventory_po_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_po_item_id' => 'Inventory Po Item ID',
            'inventory_order_request_id' => 'Inventory Order Request ID',
            'order_qty' => 'Order Qty',
            'received_qty' => 'Received Qty',
            'pending_qty' => 'Pending Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryOrderRequest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequest()
    {
        return $this->hasOne(InventoryOrderRequest::className(), ['id' => 'inventory_order_request_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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

    /**
     * Gets query for [[InventoryPoItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPoItem()
    {
        return $this->hasOne(InventoryPurchaseOrderItem::className(), ['id' => 'inventory_po_item_id']);
    }
    
    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }
}
