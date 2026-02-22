<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryDetail;
use frontend\models\inventory\InventoryPurchaseOrderItem;

/**
 * This is the model class for table "inventory_order_request".
 *
 * @property int $id
 * @property int|null $inventory_detail_id
 * @property int|null $inventory_model_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $required_qty
 * @property int|null $order_qty
 * @property int|null $received_qty
 * @property int|null $pending_qty
 * @property int|null $status 0 = pending order, 1 = fully order, 2 = fully received
 * @property int|null $inventory_po_item_id
 * @property string|null $requested_at
 * @property int|null $requested_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property InventoryModel $inventoryModel
 * @property InventoryDetail $inventoryDetail
 * @property InventoryPurchaseOrderItem $inventoryPoItem
 * @property User $updatedBy
 * @property User $requestedBy
 */
class InventoryOrderRequest extends \yii\db\ActiveRecord {

    public $inventory_brand_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_order_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_detail_id', 'inventory_model_id', 'reference_id', 'required_qty', 'order_qty', 'received_qty', 'pending_qty', 'status', 'inventory_po_item_id', 'requested_by', 'updated_by'], 'integer'],
            [['requested_at', 'updated_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 100],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
            [['inventory_po_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderItem::className(), 'targetAttribute' => ['inventory_po_item_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['requested_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requested_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'inventory_model_id' => 'Inventory Model ID',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'required_qty' => 'Required Qty',
            'order_qty' => 'Order Qty',
            'received_qty' => 'Received Qty',
            'pending_qty' => 'Pending Qty',
            'status' => 'Status',
            'inventory_po_item_id' => 'Inventory Po Item ID',
            'requested_at' => 'Requested At',
            'requested_by' => 'Requested By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[InventoryModel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryModel() {
        return $this->hasOne(InventoryModel::className(), ['id' => 'inventory_model_id']);
    }

    /**
     * Gets query for [[InventoryDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetail() {
        return $this->hasOne(InventoryDetail::className(), ['id' => 'inventory_detail_id']);
    }

    /**
     * Gets query for [[InventoryPoItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPoItem() {
        return $this->hasOne(InventoryPurchaseOrderItem::className(), ['id' => 'inventory_po_item_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[RequestedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestedBy() {
        return $this->hasOne(User::className(), ['id' => 'requested_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->requested_at = new \yii\db\Expression('NOW()');
            $this->requested_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    public static function getStatusList() {
        return [
            0 => 'Pending Order',
            1 => 'Ordered',
            2 => 'Partially Received',
            3 => 'Fully Received',
        ];
    }

    public function getStatusLabel() {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function getBomDetail() {
        return $this->hasOne(
                        \frontend\models\bom\BomDetails::class,
                        ['id' => 'reference_id']
                );
    }

    public function getStockOutbound() {
        return $this->hasOne(
                        \frontend\models\bom\StockOutboundDetails::class,
                        ['id' => 'reference_id']
                );
    }
}
