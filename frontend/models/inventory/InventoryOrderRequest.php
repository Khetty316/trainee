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
 * @property int|null $status 0 = pending order, 1 = fully order, 2 = partially received, 3 = fully received
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
 * @property InventoryOrderRequestAllocation[] $inventoryOrderRequestAllocations
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
            [['inventory_brand_id', 'requested_at', 'updated_at'], 'safe'],
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

    /**
     * Gets query for [[InventoryOrderRequestAllocations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequestAllocations() {
        return $this->hasMany(InventoryOrderRequestAllocation::className(), ['inventory_order_request_id' => 'id']);
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

//    public function processInventoryItem($item, $userId, $referenceType, $referenceId) {
//        try {
//
//            if (!$item->inventory_model_id || !$item->inventory_brand_id) {
//                throw new \Exception("Item {$item->id}: No inventory model/brand specified");
//            }
//
//            $requiredQty = $item->qty;
//            $allocatedQty = 0;
//
//            // allocate from BOM reserve first 
//            $allocatedQty += self::allocateFromReserve($item, $userId, $referenceType, $referenceId, $requiredQty - $allocatedQty);
//
//            // then allocate from general reserves
//            if ($allocatedQty < $requiredQty) {
//                $allocatedQty += self::allocateFromReserve($item, $userId, 'reserve', $userId, $requiredQty - $allocatedQty);
//            }
//
//            // then allocate from general stock
//            if ($allocatedQty < $requiredQty) {
//                $allocatedQty += self::allocateFromGeneralStock($item, $requiredQty - $allocatedQty, $referenceType);
//            }
//
//            // Save updated item
//            if (!$item->save(false)) {
//                throw new \Exception("Failed to update item {$item->id}");
//            }
//
//            // create order request if still insufficient
//            if ($allocatedQty < $requiredQty) {
//                $balanceQty = $requiredQty - $allocatedQty;
//
//                if (!self::createOrderRequest($item, $balanceQty, $referenceType)) {
//                    throw new \Exception("Failed to create order request.");
//                }
//            }
//
//            return true;
//        } catch (\Exception $e) {
//            Yii::error("processInventoryItem failed for item {$item->id}: " . $e->getMessage());
//            throw $e;
//        }
//    }

    public function processInventoryItem($item, $newRequiredQty, $userId, $referenceType, $referenceId) {
        if (!$item->inventory_model_id || !$item->inventory_brand_id) {
            throw new \Exception("Item {$item->id}: No inventory model/brand specified");
        }

        $requiredQty = $newRequiredQty;
        $allocatedQty = 0;
        $balanceQty = $requiredQty - $allocatedQty;

        // Priority 1: specific reserve
        if ($referenceType === 'bom_detail' || $referenceType === 'bomstockoutbound') {
            $referenceType = 'bom_detail';
        }
        $allocatedQty += self::allocateFromReserve($item, $userId, $referenceType, $referenceId, $balanceQty);

        // Priority 2: General reserve
        if ($allocatedQty < $requiredQty) {
            $allocatedQty += self::allocateFromReserve($item, $userId, 'reserve', $userId, $balanceQty);
        }

        // Priority 3: General stock
        if ($allocatedQty < $requiredQty) {
            $allocatedQty += self::allocateFromGeneralStock($item, $balanceQty, $referenceType);
        }

        if (!$item->save(false)) {
            throw new \Exception("Failed to save item {$item->id}.");
        }

        // Priority 4
        // Raise an order request for any unmet balance
        if ($allocatedQty < $requiredQty) {

            $reference_type = $referenceType;
            if ($referenceType === 'bom_detail' || $referenceType === 'bomstockoutbound') {
                $reference_type = 'bomstockoutbound';
            }

            //check order request if exist
            $orderRequest = self::findOne(['reference_type' => $reference_type, 'reference_id' => $item->id]);
            if ($orderRequest !== null) {
                if ($orderRequest !== null) {

                    // OLD shortage (what was previously needed but not fulfilled)
                    $oldShortage = max(0, $orderRequest->required_qty - $orderRequest->received_qty);

                    // NEW shortage
                    $newShortage = $balanceQty;

                    // ONLY add the difference
                    $additionalQty = $newShortage - $oldShortage;

                    if ($additionalQty > 0) {
                        // Increase order
                        $orderRequest->required_qty += $additionalQty;
                    } elseif ($additionalQty < 0) {
                        // Decrease order
                        $orderRequest->required_qty += $additionalQty; // subtract

                        if ($orderRequest->required_qty < $orderRequest->received_qty) {
                            $orderRequest->required_qty = $orderRequest->received_qty;
                        }
                    }
                    $orderRequest->status = 0;
//
                    if (!$orderRequest->save(false)) {
                        throw new \Exception("Failed to update order qty reference ID {$item->id}.");
                    }
                }
            } else {
                if (!self::createOrderRequest($item, $balanceQty, $referenceType)) {
                    throw new \Exception("Failed to create order request for item {$item->id}.");
                }
            }
        }
    }

    public function allocateFromReserve($item, $userId, $referenceType, $referenceId, $remainingQty) {
        if ($remainingQty <= 0) {
            return 0;
        }

        $query = InventoryReserveItem::find()
                ->alias('iri')
                ->innerJoin('inventory_detail id', 'id.id = iri.inventory_detail_id')
                ->where([
                    'iri.user_id' => $userId,
                    'iri.reference_type' => $referenceType,
                    'iri.reference_id' => $referenceId,
                    'id.model_id' => $item->inventory_model_id,
                    'id.brand_id' => $item->inventory_brand_id,
                    'iri.status' => 2, //2 = active, 1 = inactive
                ])
                ->andWhere(['>', 'iri.available_qty', 0])
                ->orderBy(['iri.created_at' => SORT_ASC]); // FIFO

        $reserves = $query->all();
        $allocated = 0;

        foreach ($reserves as $reserve) {

            if ($allocated >= $remainingQty) {
                break;
            }

            $qtyToAllocate = min($reserve->available_qty, $remainingQty - $allocated);

            if ($qtyToAllocate <= 0) {
                continue;
            }

            // Update reserve
            $reserve->dispatched_qty += $qtyToAllocate;
            $reserve->available_qty -= $qtyToAllocate;

            if (!$reserve->save()) {
                throw new \Exception("Failed updating reserve {$reserve->id}");
            }

            $inventoryDetail = InventoryDetail::findOne($reserve->inventory_detail_id);

            if (!$inventoryDetail) {
                throw new \Exception("Inventory detail not found");
            }

            self::createStockOutbound($inventoryDetail->id, $item->id, $reserve->id, $qtyToAllocate, $referenceType);

            $inventoryDetail->stock_reserved += $qtyToAllocate;
            $inventoryDetail->stock_available -= $qtyToAllocate;

            if (!$inventoryDetail->save()) {
                throw new \Exception("Failed updating inventory detail {$inventoryDetail->id}");
            }

            $allocated += $qtyToAllocate;
            $item->qty_stock_available += $qtyToAllocate;
        }

        return $allocated;
    }

    public function allocateFromGeneralStock($item, $remainingQty, $referenceType) {
        if ($remainingQty <= 0) {
            return 0;
        }

        $inventoryDetails = InventoryDetail::find()
                ->where([
                    'model_id' => $item->inventory_model_id,
                    'brand_id' => $item->inventory_brand_id,
                    'active_sts' => 2
                ])
                ->andWhere(['>', 'stock_available', 0])
                ->orderBy([
                    'stock_available' => SORT_DESC,
                    'created_at' => SORT_ASC
                ])
                ->all();

        $allocated = 0;

        foreach ($inventoryDetails as $inventoryDetail) {
            if ($allocated >= $remainingQty) {
                break;
            }

            $qtyToAllocate = min($inventoryDetail->stock_available, $remainingQty - $allocated);

            if ($qtyToAllocate <= 0) {
                continue;
            }

            self::createStockOutbound($inventoryDetail->id, $item->id, null, $qtyToAllocate, $referenceType);

            $inventoryDetail->stock_reserved += $qtyToAllocate;
            $inventoryDetail->stock_available -= $qtyToAllocate;

            if ($inventoryDetail->stock_available < 0) {
                throw new \Exception("Stock negative for inventory {$inventoryDetail->id}");
            }

            if (!$inventoryDetail->save()) {
                throw new \Exception("Failed updating inventory detail {$inventoryDetail->id}");
            }

            $allocated += $qtyToAllocate;
            $item->qty_stock_available += $qtyToAllocate;
        }

        return $allocated;
    }

    public function createStockOutbound($inventoryDetailId, $referenceId, $reserveItemId, $qty, $referenceType) {
        if ($referenceType === "bomstockoutbound" || $referenceType === "bom_detail") {
            $referenceType = "bomstockoutbound";
        }

        $record = new InventoryStockoutbound();
        $record->inventory_detail_id = $inventoryDetailId;
        $record->reference_type = $referenceType;
        $record->reference_id = $referenceId;
        $record->reserve_item_id = $reserveItemId;
        $record->qty = $qty;

        if (!$record->save()) {
            throw new \Exception("Failed to save InventoryStockoutbound");
        }
    }

    public function createOrderRequest($item, $balanceQty, $referenceType) {
        try {
            $orderRequest = new InventoryOrderRequest();
            $orderRequest->inventory_model_id = $item->inventory_model_id;
            $orderRequest->inventory_brand_id = $item->inventory_brand_id;
            $orderRequest->reference_type = $referenceType;
            $orderRequest->reference_id = $item->id;
            $orderRequest->required_qty = $balanceQty;

            if (!$orderRequest->save()) {
                throw new \Exception("Failed to save order request: " . json_encode($orderRequest->errors));
            }

            return true;
        } catch (\Exception $e) {
            Yii::error("createOrderRequest failed: " . $e->getMessage());
            throw $e; // Re-throw to trigger rollback
        }
    }
}
