<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\common\RefCurrencies;
use yii\db\Expression;

/**
 * This is the model class for table "inventory_purchase_order_item".
 *
 * @property int $id
 * @property int|null $inventory_po_id
 * @property int|null $inventory_detail_id
 * @property string|null $department_code
 * @property int|null $supplier_id
 * @property int|null $brand_id
 * @property int|null $model_id
 * @property string|null $model_type
 * @property string|null $model_group
 * @property string|null $model_description
 * @property int|null $order_qty
 * @property string|null $unit_type
 * @property int|null $currency_id
 * @property float|null $unit_price
 * @property float|null $discount_amt
 * @property float|null $total_price
 * @property int|null $received_qty
 * @property int|null $remaining_qty
 * @property int|null $status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $is_deleted 1 = deleted
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 *
 * @property InventoryOrderRequest[] $inventoryOrderRequests
 * @property InventoryPurchaseOrder $inventoryPo
 * @property User $deletedBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryBrand $brand
 * @property RefCurrencies $currency
 * @property InventorySupplier $supplier
 * @property InventoryDetail $inventoryDetail
 * @property InventoryModel $model
 * @property InventoryPurchaseOrderItemReceive[] $inventoryPurchaseOrderItemReceives
 */
class InventoryPurchaseOrderItem extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_order_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_po_id', 'inventory_detail_id', 'supplier_id', 'brand_id', 'model_id', 'order_qty', 'currency_id', 'received_qty', 'remaining_qty', 'status', 'created_by', 'updated_by', 'is_deleted', 'deleted_by'], 'integer'],
            [['model_description'], 'string'],
            [['unit_price', 'discount_amt', 'total_price'], 'number'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['department_code', 'model_type', 'model_group', 'unit_type'], 'string', 'max' => 255],
            [['inventory_po_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrder::className(), 'targetAttribute' => ['inventory_po_id' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventorySupplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_po_id' => 'Inventory Po ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'department_code' => 'Department Code',
            'supplier_id' => 'Supplier ID',
            'brand_id' => 'Brand ID',
            'model_id' => 'Model ID',
            'model_type' => 'Model Type',
            'model_group' => 'Model Group',
            'model_description' => 'Model Description',
            'order_qty' => 'Order Qty',
            'unit_type' => 'Unit Type',
            'currency_id' => 'Currency ID',
            'unit_price' => 'Unit Price',
            'discount_amt' => 'Discount Amt',
            'total_price' => 'Total Price',
            'received_qty' => 'Received Qty',
            'remaining_qty' => 'Remaining Qty',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[InventoryOrderRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequests() {
        return $this->hasMany(InventoryOrderRequest::className(), ['inventory_po_item_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPo() {
        return $this->hasOne(InventoryPurchaseOrder::className(), ['id' => 'inventory_po_id']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'brand_id']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * Gets query for [[Supplier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier() {
        return $this->hasOne(InventorySupplier::className(), ['id' => 'supplier_id']);
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
     * Gets query for [[Model]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModel() {
        return $this->hasOne(InventoryModel::className(), ['id' => 'model_id']);
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItemReceives]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItemReceives() {
        return $this->hasMany(InventoryPurchaseOrderItemReceive::className(), ['inventory_po_item_id' => 'id']);
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

    public function updateInventoryQtyPendingReceipt() {
        $detail = InventoryDetail::findOne($this->inventory_detail_id);

        if (!$detail) {
            throw new \Exception("Inventory detail ID {$this->inventory_detail_id} not found.");
        }

        $pendingQty = InventoryPurchaseOrderItem::find()
                ->select(new Expression('SUM(order_qty - COALESCE(received_qty, 0))'))
                ->where([
                    'inventory_detail_id' => $detail->id,
                    'is_deleted' => 0
                ])
                ->scalar();

        $detail->qty_pending_receipt = (int) $pendingQty;

        if (!$detail->save(false)) {
            throw new \Exception('Failed to update qty pending receipt: ' . json_encode($detail->errors));
        }
    }
}
