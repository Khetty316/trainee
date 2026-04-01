<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\bom\BomDetails;
use frontend\models\bom\BomMaster;

/**
 * This is the model class for table "inventory_purchase_order_receive_batch".
 *
 * @property int $id
 * @property int|null $inventory_po_id
 * @property int|null $received_by
 * @property string|null $received_at
 *
 * @property InventoryPurchaseOrderItemDoc[] $inventoryPurchaseOrderItemDocs
 * @property InventoryPurchaseOrderItemReceive[] $inventoryPurchaseOrderItemReceives
 * @property InventoryPurchaseOrder $inventoryPo
 * @property User $receivedBy
 */
class InventoryPurchaseOrderReceiveBatch extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_order_receive_batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_po_id', 'received_by'], 'integer'],
            [['received_at'], 'safe'],
            [['inventory_po_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrder::className(), 'targetAttribute' => ['inventory_po_id' => 'id']],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_po_id' => 'Inventory Po ID',
            'received_by' => 'Received By',
            'received_at' => 'Received At',
        ];
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItemDocs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItemDocs() {
        return $this->hasMany(InventoryPurchaseOrderItemDoc::className(), ['receive_batch_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItemReceives]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItemReceives() {
        return $this->hasMany(InventoryPurchaseOrderItemReceive::className(), ['receive_batch_id' => 'id']);
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
     * Gets query for [[ReceivedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedBy() {
        return $this->hasOne(User::className(), ['id' => 'received_by']);
    }

    public function beforeSave($insert) {
        $this->received_at = new \yii\db\Expression('NOW()');
        $this->received_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }

    /**
     * Process order receive with documents and item receives
     * @param InventoryPurchaseOrder $po
     * @param array $postData
     * @param array $requestPost
     * @return bool
     * @throws \Exception
     */
//    public function processOrderReceive($po, $postData, $requestPost) {
//        $transaction = Yii::$app->db->beginTransaction();
//
//        try {
//
//            // Save the batch record first
//            if (!$this->save()) {
//                throw new \Exception('Failed to create receive batch: ' . json_encode($this->errors));
//            }
//
//            // Step 1: Validate that we have items to receive
//            if (!isset($postData['receive']) || empty($postData['receive'])) {
//                throw new \Exception('No items to receive.');
//            }
//
//            // Step 2: Save documents first
//            $uploadedFiles = \yii\web\UploadedFile::getInstances(
//                    new InventoryPurchaseOrderItemDoc(),
//                    'filename'
//            );
//
//            $documentTypes = $requestPost['InventoryPurchaseOrderItemDoc']['document_type'] ?? [];
//            $documentNos = $requestPost['InventoryPurchaseOrderItemDoc']['document_no'] ?? [];
//
//            $uploadPath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/');
//
//            // Create directory if it doesn't exist
//            if (!is_dir($uploadPath)) {
//                if (!mkdir($uploadPath, 0755, true)) {
//                    throw new \Exception('Failed to create upload directory.');
//                }
//            }
//
//            // Validate that at least one document is uploaded
//            $hasValidDocument = false;
//            foreach ($uploadedFiles as $index => $file) {
//                if ($file && isset($documentTypes[$index]) && isset($documentNos[$index]) &&
//                        $documentTypes[$index] !== '' && $documentNos[$index] !== '') {
//                    $hasValidDocument = true;
//                    break;
//                }
//            }
//
//            if (!$hasValidDocument) {
//                throw new \Exception('At least one document must be uploaded.');
//            }
//
//            // Track uploaded files for cleanup on error
//            $uploadedFilePaths = [];
//
//            // Save each document
//            foreach ($uploadedFiles as $index => $file) {
//                if ($file && isset($documentTypes[$index]) && isset($documentNos[$index]) &&
//                        $documentTypes[$index] !== '' && $documentNos[$index] !== '') {
//
//                    $attachment = new InventoryPurchaseOrderItemDoc();
//                    $attachment->receive_batch_id = $this->id;
//                    $attachment->document_type = $documentTypes[$index];
//                    $attachment->document_no = trim($documentNos[$index]);
//
//                    // Generate unique filename
//                    $filename = 'receive_batch_' . $this->id . '_' . time() . '_' . $index . '.' . $file->extension;
//                    $filePath = $uploadPath . $filename;
//
//                    // Save file to disk
//                    if (!$file->saveAs($filePath)) {
//                        throw new \Exception('Failed to upload file: ' . $file->name);
//                    }
//
//                    $uploadedFilePaths[] = $filePath;
//                    $attachment->filename = $filename;
//
//                    if (!$attachment->save()) {
//                        throw new \Exception('Failed to save attachment: ' . json_encode($attachment->errors));
//                    }
//                }
//            }
//
//            // Step 3: Save receive items and update stock
//            foreach ($postData['receive'] as $items) {
//                foreach ($items as $itemId => $item) {
//                    // Validate received quantity
//                    if (!isset($item['new_receive_qty']) || $item['new_receive_qty'] <= 0) {
//                        throw new \Exception("Invalid receive quantity for item ID: {$itemId}");
//                    }
//
//                    // Get PO item with lock to prevent race conditions
//                    $poItem = InventoryPurchaseOrderItem::find()
//                            ->where(['id' => $itemId])
//                            ->one();
//
//                    if (!$poItem) {
//                        throw new \Exception("PO item not found: {$itemId}");
//                    }
//
//                    // Validate we're not receiving more than ordered
//                    $newReceivedTotal = ($poItem->received_qty ?? 0) + $item['new_receive_qty'];
//                    if ($newReceivedTotal > $poItem->order_qty) {
//                        throw new \Exception("Cannot receive more than ordered quantity for item ID: {$itemId}");
//                    }
//
//                    // Create receive record
//                    $receive = new InventoryPurchaseOrderItemReceive();
//                    $receive->receive_batch_id = $this->id;
//                    $receive->inventory_po_item_id = $itemId;
//                    $receive->received_quantity = $item['new_receive_qty'];
//
//                    if (!$receive->save()) {
//                        throw new \Exception('Failed to save receive record: ' . json_encode($receive->errors));
//                    }
//
//                    if (isset($item["allocation"])) {
//                        foreach ($item["allocation"] as $allocation) {
//                            $orderReceiveAllocation = new InventoryPoItemReceiveAllocation();
//                            $orderRequestAllocation = InventoryOrderRequestAllocation::findOne($allocation["id"]);
//                            $orderReceiveAllocation->inventory_po_item_receive_id = $receive->id;
//                            $orderReceiveAllocation->inventory_order_request_allocation_id = $orderRequestAllocation->id;
//                            $orderReceiveAllocation->allocated_qty = $allocation["qty"];
//                            if (!$orderReceiveAllocation->save()) {
//                                throw new \Exception('Failed to save order receive allocation record: ' . json_encode($orderReceiveAllocation->errors));
//                            }
//
//                            $orderRequestAllocation->received_qty += $orderReceiveAllocation->allocated_qty;
//                            $orderRequestAllocation->pending_qty = $orderReceiveAllocation->order_qty - $orderRequestAllocation->received_qty;
//                            if (!$orderRequestAllocation->save()) {
//                                throw new \Exception('Failed to save order request allocation record: ' . json_encode($orderRequestAllocation->errors));
//                            }
//
//                            $orderRequest = InventoryOrderRequest::findOne($orderRequestAllocation->inventory_order_request_id);
//                            $orderRequest->received_qty = InventoryOrderRequestAllocation::find()->where(['inventory_order_request_id' => $orderRequest->id])->sum('received_qty');
//                            $orderRequest->pending_qty = $orderRequest->order_qty - $orderRequest->received_qty;
//                            if (!$orderRequest->save()) {
//                                throw new \Exception('Failed to update order request record: ' . json_encode($orderRequest->errors));
//                            }
//
//                            if ($orderRequest->reference_type === "bom_detail") {
//                                $bomdetail = \frontend\models\bom\BomDetails::find()->where(['id' => $orderRequest->reference_id, 'inventory_model_id' => $orderRequest->inventory_model_id, 'active_status' => 1])->one();
//                                if ($bomdetail) {
//                                    $bomdetail->inventory_sts = 2;
//                                    if (!$bomdetail->save()) {
//                                        throw new \Exception('Failed to update order request record: ' . json_encode($orderRequest->errors));
//                                    }
//                                    $bomMaster = \frontend\models\bom\BomMaster::findOne($bomdetail->bom_master);
//                                    $totalPanels = $bomMaster->productionPanel->quantity;
//                                    $requiredQty = $totalPanels * $bomdetail->qty;
//                                    $balance = $orderReceiveAllocation->allocated_qty - $requiredQty;
//                                    if ($balance > 0) {
//                                        $inventory = InventoryDetail::findOne($orderRequest->inventory_detail_id);
//                                        $inventory->stock_available += $balance;
//                                    }
//                                    $reserveItem = new InventoryReserveItem();
//                                    $reserveItem->user_id = $bomMaster->productionPanel->projProdMaster->created_by;
//                                    $reserveItem->reference_type = $orderRequest->reference_type;
//                                    $reserveItem->reference_id = $orderRequest->reference_id; // Link to stock_outbound_details (consumer)
//                                    $reserveItem->inventory_detail_id = $orderRequest->inventory_detail_id;
//                                    $reserveItem->reserved_qty = $orderReceiveAllocation->allocated_qty;
//                                    $reserveItem->available_qty = $reserveItem->reserved_qty;
//                                    if (!$reserveItem->save()) {
//                                        throw new \Exception('Failed to save reserve item record: ' . json_encode($reserveItem->errors));
//                                    }
//                                    
//                                } else {
//                                    $inventory = InventoryDetail::findOne($orderRequest->inventory_detail_id);
//                                    $inventory->stock_available += $orderReceiveAllocation->allocated_qty;
//                                }
//                            } else if ($orderRequest->reference_type === "bomstockoutbound") {
//                                //stockoutbound detail
//                                $stockoutboundDetail = \frontend\models\bom\StockOutboundDetails::findOne(['id' => $orderRequest->reference_id, 'active_sts' => 1]);
//                                if (!$stockoutboundDetail) {
//                                    $inventory = InventoryDetail::findOne($orderRequest->inventory_detail_id);
//                                    $inventory->stock_available += $orderReceiveAllocation->allocated_qty;
//                                } else {
//                                    $dispatchedQty = \frontend\models\bom\StockDispatchTrial::find()->where(['stock_outbound_details_id' => $stockoutboundDetail->id])->sum('dispatch_qty');
//                                    $requiredQty = $stockoutboundDetail->qty - $dispatchedQty;
//                                    $balance = $orderReceiveAllocation->allocated_qty - $requiredQty;
//                                    if ($requiredQty > 0) {
//                                        $inventoryStockOutbound = new InventoryStockoutbound();
//                                        $inventoryStockOutbound->inventory_detail_id = $orderRequest->inventory_detail_id;
//                                        $inventoryStockOutbound->reference_type = $orderRequest->reference_type;
//                                        $inventoryStockOutbound->reference_id = $stockoutboundDetail->id; // Link to stock_outbound_details (consumer)
//                                        $inventoryStockOutbound->reserve_item_id = NULL; // NULL = general stock (source)
//                                        $inventoryStockOutbound->qty = $requiredQty;
//
//                                        if (!$inventoryStockOutbound->save()) {
//                                            throw new \Exception("Failed to save InventoryStockoutbound: " . json_encode($inventoryStockOutbound->errors));
//                                        }
//                                        
//                                        $inventory = InventoryDetail::findOne($orderRequest->inventory_detail_id);
//                                        $inventoryDetail->stock_reserved += $requiredQty;
//                                    }
//                                    
//                                    if ($balance > 0) {
//                                        $inventory = InventoryDetail::findOne($orderRequest->inventory_detail_id);
//                                        $inventory->stock_available += $balance;
//                                    }
//                                }
//
//                                $stockoutboundDetail->updateAllQtyInStockDetail($stockoutboundDetail);
//
//                            }
//                        }
//                    }
//
//
//                    // Update PO item quantities
//                    $poItem->received_qty = $newReceivedTotal;
//                    $poItem->remaining_qty = $poItem->order_qty - $poItem->received_qty;
//
//                    // Update status
//                    if ($poItem->remaining_qty <= 0) {
//                        $poItem->status = \frontend\models\RefInventoryStatus::STATUS_FullyReceived;
//                    } else {
//                        $poItem->status = \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived;
//                    }
//
//                    if (!$poItem->save()) {
//                        throw new \Exception('Failed to update PO item: ' . json_encode($poItem->errors));
//                    }
//
//                    // Update stock on hand - INLINE to keep in transaction
//                    $inventoryDetail = InventoryDetail::findOne($poItem->inventory_detail_id);
//                    if (!$inventoryDetail) {
//                        throw new \Exception("Inventory detail not found for PO item: {$itemId}");
//                    }
//
//                    $inventoryDetail->stock_in += $receive->received_quantity;
//                    $inventoryDetail->stock_on_hand += $receive->received_quantity;
//                    $inventoryDetail->stock_available += $receive->add_to_stock;
//                    $inventoryDetail->qty_pending_receipt -= $receive->received_quantity;
//
//                    if (!$inventoryDetail->save()) {
//                        throw new \Exception('Failed to update stock on hand: ' . json_encode($inventoryDetail->errors));
//                    }
//                }
//            }
//
//            // Step 4: Update PO status
//            $this->updatePOStatus($po);
//
//            $transaction->commit();
//            return true;
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//
//            // Clean up uploaded files on error
//            if (isset($uploadedFilePaths)) {
//                foreach ($uploadedFilePaths as $filePath) {
//                    if (file_exists($filePath)) {
//                        @unlink($filePath);
//                    }
//                }
//            }
//
//            Yii::error('Error processing order receive: ' . $e->getMessage(), __METHOD__);
//            throw $e;
//        }
//    }

    public function processOrderReceive($po, $requestPost) {
        $uploadedFiles = [];

        try {
            if (!$po) {
                throw new \Exception('PO object is null in processOrderReceive');
            }

// Get the ID safely
            $poId = null;
            try {
                $poId = $po->id;
            } catch (\Exception $e) {
                throw new \Exception('Cannot access PO ID: ' . $e->getMessage());
            }

            if (empty($poId)) {
                throw new \Exception('PO object has no valid ID property. ID value: ' . ($poId ?? 'null'));
            }

            // Debug: Check requestPost structure
            if (!is_array($requestPost)) {
                throw new \Exception('Request post is not an array: ' . gettype($requestPost));
            }

            // Check for receive data
            if (empty($requestPost['receive'])) {
                throw new \Exception('No items to receive. receive key missing or empty in request post');
            }

            if (!is_array($requestPost['receive'])) {
                throw new \Exception('receive data is not an array: ' . gettype($requestPost['receive']));
            }

            // Validate receive items with detailed error handling
            try {
                $this->validateReceiveItems($requestPost);
            } catch (\Exception $e) {
                throw new \Exception('Validation failed in validateReceiveItems: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            }

            // ✅ Resolve uploaded files ONCE and pass down — prevents Yii2 double-registration
            try {
                $docModel = new InventoryPurchaseOrderItemDoc();
                $resolvedFiles = \yii\web\UploadedFile::getInstances($docModel, 'filename');
            } catch (\Exception $e) {
                throw new \Exception('Failed to get uploaded file instances: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            }

            // Validate document upload
            try {
                $this->validateDocumentUpload($requestPost, $resolvedFiles);
            } catch (\Exception $e) {
                throw new \Exception('Document validation failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            }

            // Begin transaction
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Save batch record
                if (!$this->save()) {
                    $errors = json_encode($this->errors);
                    throw new \Exception('Failed to create receive batch: ' . $errors . ' at ' . __FILE__ . ':' . __LINE__);
                }

                // Handle document upload
                try {
                    $uploadedFiles = $this->handleDocumentUpload($requestPost, $resolvedFiles);
                } catch (\Exception $e) {
                    throw new \Exception('Document upload handling failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                }

                // Process receive items
                try {
                    $this->processReceiveItems($requestPost);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to process receive items: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                }

                // Update PO status
                try {
                    $this->updatePOStatus($po);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to update PO status: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                }

                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollBack();

                // Cleanup uploaded files
                if (!empty($uploadedFiles)) {
                    try {
                        $this->cleanupUploadedFiles($uploadedFiles);
                    } catch (\Exception $cleanupError) {
                        Yii::error('Failed to cleanup files during rollback: ' . $cleanupError->getMessage(), __METHOD__);
                    }
                }

                Yii::error('Error processing order receive: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine(), __METHOD__);
                throw $e;
            }
        } catch (\Exception $e) {
            // Re-throw with clear context that includes the original error location
            throw new \Exception(
                            'processOrderReceive failed: ' . $e->getMessage() .
                            ' (Original error at: ' . $e->getFile() . ':' . $e->getLine() . ')',
                            0,
                            $e);
        }
    }

    private function validateReceiveItems($requestPost) {
        if (!isset($requestPost['receive'])) {
            throw new \Exception('receive key not found in request post at ' . __FILE__ . ':' . __LINE__);
        }

        if (!is_array($requestPost['receive'])) {
            throw new \Exception('receive data is not an array at ' . __FILE__ . ':' . __LINE__);
        }

        foreach ($requestPost['receive'] as $index => $itemData) {
            $itemIdentifier = "Item at index {$index}";

            // Check if itemData is array
            if (!is_array($itemData)) {
                throw new \Exception("{$itemIdentifier} is not an array, got " . gettype($itemData) . ' at ' . __FILE__ . ':' . __LINE__);
            }

            // Check for id field
            if (!isset($itemData['id'])) {
                throw new \Exception("{$itemIdentifier} missing 'id' field. Available keys: " . implode(', ', array_keys($itemData)) . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if (empty($itemData['id'])) {
                throw new \Exception("{$itemIdentifier} has empty id field at " . __FILE__ . ':' . __LINE__);
            }

            // Check for new_receive_qty
            if (!isset($itemData['new_receive_qty'])) {
                throw new \Exception("{$itemIdentifier} (ID: {$itemData['id']}) missing 'new_receive_qty' field at " . __FILE__ . ':' . __LINE__);
            }

            if (!is_numeric($itemData['new_receive_qty'])) {
                throw new \Exception("{$itemIdentifier} (ID: {$itemData['id']}) new_receive_qty is not numeric: " . $itemData['new_receive_qty'] . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if ($itemData['new_receive_qty'] <= 0) {
                throw new \Exception("Invalid receive quantity ({$itemData['new_receive_qty']}) for item ID: {$itemData['id']} at " . __FILE__ . ':' . __LINE__);
            }

            // Find PO item
            $poItem = InventoryPurchaseOrderItem::findOne($itemData['id']);
            if (!$poItem) {
                throw new \Exception("PO item not found with ID: {$itemData['id']} at validation step. File: " . __FILE__ . ' Line: ' . __LINE__);
            }

            // FIXED: Validate PO item belongs to correct PO - remove property_exists check
            // Just check if we can access the value and if it matches
            $poItemPoId = null;
            try {
                $poItemPoId = $poItem->inventory_po_id;
            } catch (\Exception $e) {
                throw new \Exception("Cannot access inventory_po_id for PO item ID {$itemData['id']}: " . $e->getMessage() . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if ($poItemPoId === null) {
                throw new \Exception("PO item ID {$itemData['id']} has null inventory_po_id value at " . __FILE__ . ':' . __LINE__);
            }

            if ($poItemPoId != $this->inventory_po_id) {
                throw new \Exception("PO item ID {$itemData['id']} belongs to PO {$poItemPoId} but current batch is for PO {$this->inventory_po_id} at " . __FILE__ . ':' . __LINE__);
            }

            // Check received quantity against ordered quantity
            $receivedQty = 0;
            try {
                $receivedQty = $poItem->received_qty ?? 0;
            } catch (\Exception $e) {
                // If property doesn't exist, default to 0
                $receivedQty = 0;
            }

            $newTotal = $receivedQty + $itemData['new_receive_qty'];

            // Get order_qty safely
            $orderQty = null;
            try {
                $orderQty = $poItem->order_qty;
            } catch (\Exception $e) {
                throw new \Exception("Cannot access order_qty for PO item ID {$itemData['id']}: " . $e->getMessage() . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if ($orderQty === null) {
                throw new \Exception("PO item ID {$itemData['id']} has null order_qty value at " . __FILE__ . ':' . __LINE__);
            }

            if ($newTotal > $orderQty) {
                throw new \Exception("Cannot receive more than ordered quantity for item ID: {$poItem->id}. Order qty: {$orderQty}, Already received: {$receivedQty}, Trying to receive: {$itemData['new_receive_qty']} at " . __FILE__ . ':' . __LINE__);
            }

            // Validate allocations if present
            if (isset($itemData['allocation']) && !empty($itemData['allocation'])) {
                if (!is_array($itemData['allocation'])) {
                    throw new \Exception("Allocation data for item ID {$itemData['id']} is not an array at " . __FILE__ . ':' . __LINE__);
                }

                $totalAllocated = 0;

                foreach ($itemData['allocation'] as $allocIndex => $allocationData) {
                    $allocIdentifier = "Allocation at index {$allocIndex} for item ID {$itemData['id']}";

                    if (!is_array($allocationData)) {
                        throw new \Exception("{$allocIdentifier} is not an array, got " . gettype($allocationData) . ' at ' . __FILE__ . ':' . __LINE__);
                    }

                    if (!isset($allocationData['qty'])) {
                        throw new \Exception("{$allocIdentifier} missing 'qty' field. Available keys: " . implode(', ', array_keys($allocationData)) . ' at ' . __FILE__ . ':' . __LINE__);
                    }

                    if (!is_numeric($allocationData['qty'])) {
                        throw new \Exception("{$allocIdentifier} qty is not numeric: " . $allocationData['qty'] . ' at ' . __FILE__ . ':' . __LINE__);
                    }

                    $allocatedQty = (int) $allocationData["qty"];

                    if ($allocatedQty <= 0) {
                        continue;
                    }

                    if (!isset($allocationData["id"])) {
                        throw new \Exception("{$allocIdentifier} missing 'id' field at " . __FILE__ . ':' . __LINE__);
                    }

                    if (empty($allocationData["id"])) {
                        throw new \Exception("{$allocIdentifier} has empty id field at " . __FILE__ . ':' . __LINE__);
                    }

                    $totalAllocated += $allocatedQty;

                    $orderRequestAllocation = InventoryOrderRequestAllocation::findOne($allocationData["id"]);
                    if (!$orderRequestAllocation) {
                        throw new \Exception("Order request allocation not found with ID: {$allocationData['id']} for item ID: {$itemData['id']} at " . __FILE__ . ':' . __LINE__);
                    }
                }

                if ($totalAllocated > $itemData['new_receive_qty']) {
                    throw new \Exception("Allocated quantity ({$totalAllocated}) exceeds received quantity ({$itemData['new_receive_qty']}) for item ID: {$itemData['id']} at " . __FILE__ . ':' . __LINE__);
                }
            }

            // Validate inventory detail
            $inventoryDetailId = null;
            try {
                $inventoryDetailId = $poItem->inventory_detail_id;
            } catch (\Exception $e) {
                throw new \Exception("Cannot access inventory_detail_id for PO item ID {$itemData['id']}: " . $e->getMessage() . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if (!$inventoryDetailId) {
                throw new \Exception("PO item ID {$itemData['id']} has no inventory_detail_id value at " . __FILE__ . ':' . __LINE__);
            }

            $inventory = InventoryDetail::findOne($inventoryDetailId);
            if (!$inventory) {
                throw new \Exception("Inventory detail not found for item ID: {$poItem->id} with inventory_detail_id: {$inventoryDetailId} at " . __FILE__ . ':' . __LINE__);
            }

            // Get qty_pending_receipt safely
            $qtyPendingReceipt = null;
            try {
                $qtyPendingReceipt = $inventory->qty_pending_receipt;
            } catch (\Exception $e) {
                throw new \Exception("Cannot access qty_pending_receipt for inventory ID {$inventory->id}: " . $e->getMessage() . ' at ' . __FILE__ . ':' . __LINE__);
            }

            if ($qtyPendingReceipt < $itemData['new_receive_qty']) {
                throw new \Exception("Pending receipt quantity inconsistency for inventory ID: {$inventory->id}. Pending: {$qtyPendingReceipt}, Trying to receive: {$itemData['new_receive_qty']} at " . __FILE__ . ':' . __LINE__);
            }
        }
    }

// Accepts pre-resolved files — no getInstances() call here
    private function validateDocumentUpload($requestPost, array $resolvedFiles) {
        $documentTypes = $requestPost['InventoryPurchaseOrderItemDoc']['document_type'] ?? [];
        $documentNos = $requestPost['InventoryPurchaseOrderItemDoc']['document_no'] ?? [];

        $hasValidDocument = false;

        foreach ($resolvedFiles as $index => $file) {
            if (!$file || empty($documentTypes[$index]) || empty($documentNos[$index])) {
                continue;
            }
            $hasValidDocument = true;
            break;
        }

        if (!$hasValidDocument) {
            throw new \Exception('At least one document must be uploaded.');
        }
    }

    private function cleanupUploadedFiles(array $filePaths): void {
        foreach ($filePaths as $path) {
            if (!empty($path) && file_exists($path)) {
                try {
                    @unlink($path);
                } catch (\Throwable $e) {
                    Yii::error("Failed to delete file during rollback: {$path} - " . $e->getMessage(), __METHOD__);
                }
            }
        }
    }

// Accepts pre-resolved files — no getInstances() call here
    private function handleDocumentUpload($requestPost, array $resolvedFiles) {
        $documentTypes = $requestPost['InventoryPurchaseOrderItemDoc']['document_type'] ?? [];
        $documentNos = $requestPost['InventoryPurchaseOrderItemDoc']['document_no'] ?? [];

        $uploadPath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/');
        \yii\helpers\FileHelper::createDirectory($uploadPath);

        $savedPaths = [];

        foreach ($resolvedFiles as $index => $file) {
            if (!$file || empty($documentTypes[$index]) || empty($documentNos[$index])) {
                continue;
            }

            $attachment = new InventoryPurchaseOrderItemDoc();
            $attachment->receive_batch_id = $this->id;
            $attachment->document_type = $documentTypes[$index];
            $attachment->document_no = trim($documentNos[$index]);

            $filename = 'receive_batch_' . $this->id . '_' . time() . '_' . $index . '.' . $file->extension;
            $filePath = $uploadPath . $filename;

            if (!$file->saveAs($filePath)) {
                throw new \Exception('Failed to upload file: ' . $file->name);
            }

            $savedPaths[] = $filePath;
            $attachment->filename = $filename;

            if (!$attachment->save()) {
                throw new \Exception('Failed to save attachment: ' . json_encode($attachment->errors));
            }
        }

        return $savedPaths;
    }

    private function processReceiveItems($requestPost) {
        $processed = [];

        if (!isset($requestPost['receive']) || !is_array($requestPost['receive'])) {
            throw new \Exception('Invalid receive data structure in processReceiveItems');
        }

        foreach ($requestPost['receive'] as $itemIndex => $itemData) {
            if (!is_array($itemData)) {
                throw new \Exception("Invalid item data at index {$itemIndex}: expected array");
            }

            if (!isset($itemData['id'])) {
                throw new \Exception("Missing item ID at index {$itemIndex} in processReceiveItems");
            }

            if (isset($processed[$itemData['id']])) {
                Yii::info("Skipping duplicate item ID: {$itemData['id']} in processReceiveItems");
                continue;
            }

            $processed[$itemData['id']] = true;

            $poItem = InventoryPurchaseOrderItem::findOne($itemData['id']);
            if (!$poItem) {
                throw new \Exception("PO item not found with ID: {$itemData['id']} during processing");
            }

            $receive = $this->createReceiveRecord($poItem, $itemData);

            $balanceFromAllocations = 0;

            if (!empty($itemData['allocation'])) {
                if (!is_array($itemData['allocation'])) {
                    throw new \Exception("Invalid allocation data for item ID {$itemData['id']}: expected array");
                }
                $totalQtyFromAllocations = $this->processAllocations($receive, $itemData['allocation']);
            }

            $this->updatePOItemQty($poItem, (int) $itemData['new_receive_qty']);

            $this->updateInventoryStock($poItem, $receive, $totalQtyFromAllocations);
        }
    }

    private function createReceiveRecord($poItem, $itemData) {
        $receive = new InventoryPurchaseOrderItemReceive();
        $receive->receive_batch_id = $this->id;
        $receive->inventory_po_item_id = $poItem->id;
        $receive->received_quantity = $itemData['new_receive_qty'];
        $receive->add_to_stock = $itemData['add_to_stock'] ?? 0;

        if (!$receive->save()) {
            throw new \Exception('Failed to save receive record: ' . json_encode($receive->errors));
        }

        return $receive;
    }

    private function processAllocations($receive, $allocations) {
        if (!$receive) {
            throw new \Exception('processAllocations: $receive is null');
        }

        if (!isset($receive->id)) {
            throw new \Exception('processAllocations: $receive has no id property. Receive class: ' . get_class($receive));
        }

        $totalBalance = 0;
        $totalReserve = 0;

        foreach ($allocations as $allocIndex => $allocationData) {
            if (!is_array($allocationData)) {
                throw new \Exception("processAllocations: Invalid allocation data at index {$allocIndex}: not an array, got " . gettype($allocationData));
            }

            if (!isset($allocationData["qty"])) {
                throw new \Exception("processAllocations: Missing qty in allocation at index {$allocIndex}. Data: " . json_encode($allocationData));
            }

            $allocatedQty = (int) $allocationData["qty"];

            if ($allocatedQty <= 0) {
                continue;
            }

            if (!isset($allocationData["id"])) {
                throw new \Exception("processAllocations: Missing allocation ID in allocation at index {$allocIndex}");
            }

            if (empty($allocationData["id"])) {
                throw new \Exception("processAllocations: Empty allocation ID in allocation at index {$allocIndex}");
            }

            $orderRequestAllocation = InventoryOrderRequestAllocation::findOne($allocationData["id"]);
            if (!$orderRequestAllocation) {
                throw new \Exception("processAllocations: Order request allocation not found with ID: {$allocationData['id']}");
            }

            // Check if orderRequestAllocation has the required properties before accessing them
            try {
                $orderRequestAllocationId = $orderRequestAllocation->id;
            } catch (\Exception $e) {
                throw new \Exception("processAllocations: Cannot access id on orderRequestAllocation: " . $e->getMessage());
            }

            $allocation = new InventoryPoItemReceiveAllocation();
            $allocation->inventory_po_item_receive_id = $receive->id;
            $allocation->inventory_order_request_allocation_id = $orderRequestAllocationId;
            $allocation->allocated_qty = $allocatedQty;

            if (!$allocation->save()) {
                throw new \Exception('processAllocations: Failed to save allocation: ' . json_encode($allocation->errors));
            }

            // Check if orderRequestAllocation has inventory_order_request_id before accessing it
            $orderRequestId = null;
            try {
                $orderRequestId = $orderRequestAllocation->inventory_order_request_id;
            } catch (\Exception $e) {
                throw new \Exception("processAllocations: Cannot access inventory_order_request_id for allocation ID: {$orderRequestAllocationId}. Error: " . $e->getMessage());
            }

            if (!$orderRequestId) {
                throw new \Exception("processAllocations: Order request allocation ID {$orderRequestAllocationId} has null inventory_order_request_id");
            }

            $orderRequest = InventoryOrderRequest::findOne($orderRequestId);
            if (!$orderRequest) {
                throw new \Exception('processAllocations: Order request not found with ID: ' . $orderRequestId);
            }

            $this->updateOrderRequestAllocation($orderRequestAllocation, $orderRequest, $allocatedQty);

            $qtyFromAllocation = $this->handleReferenceType($orderRequest, $allocatedQty);

            $totalBalance += $qtyFromAllocation['balanceQty'];
            $totalReserve += $qtyFromAllocation['reservedQty'];
        }

        $totalQtyFromAllocation = [
            'totalReserve' => $totalReserve,
            'totalBalance' => $totalBalance
        ];

        return $totalQtyFromAllocation;
    }

    private function updateOrderRequestAllocation($orderRequestAllocation, $orderRequest, $allocatedQty) {
        if (!$orderRequestAllocation) {
            throw new \Exception('updateOrderRequestAllocation: orderRequestAllocation is null');
        }

        if (!$orderRequest) {
            throw new \Exception('updateOrderRequestAllocation: orderRequest is null');
        }

        // Check if required properties exist before accessing them
        try {
            $currentReceivedQty = $orderRequestAllocation->received_qty ?? 0;
            $orderQty = $orderRequestAllocation->order_qty ?? 0;

            $orderRequestAllocation->received_qty = $currentReceivedQty + $allocatedQty;
            $orderRequestAllocation->pending_qty = $orderQty - $orderRequestAllocation->received_qty;
        } catch (\Exception $e) {
            throw new \Exception('updateOrderRequestAllocation: Error accessing properties on orderRequestAllocation: ' . $e->getMessage() . ' at ' . __FILE__ . ':' . __LINE__);
        }

        if (!$orderRequestAllocation->save()) {
            throw new \Exception('updateOrderRequestAllocation: Failed to save order request allocation: ' . json_encode($orderRequestAllocation->errors));
        }

        // Calculate received_qty safely
        try {
            $receivedQty = InventoryOrderRequestAllocation::find()
                    ->where(['inventory_order_request_id' => $orderRequest->id])
                    ->sum('received_qty');

            $orderRequest->received_qty = $receivedQty ?: 0;
        } catch (\Exception $e) {
            throw new \Exception('updateOrderRequestAllocation: Error calculating received_qty for order request: ' . $e->getMessage());
        }

        // Check if order_qty exists
        try {
            $orderQty = $orderRequest->order_qty ?? 0;
            $requiredQty = $orderRequest->required_qty ?? $orderQty;
        } catch (\Exception $e) {
            throw new \Exception('updateOrderRequestAllocation: Error accessing order_qty on orderRequest: ' . $e->getMessage());
        }

        $orderRequest->pending_qty = $orderQty - $orderRequest->received_qty;
//        if ($requiredQty > $orderRequest->order_qty) {
//            $orderRequest->status = 0; // Pending order
//        } elseif ($requiredQty == $orderRequest->received_qty) {
//            $orderRequest->status = 3; // Fully received
//        } elseif ($orderRequest->received_qty > 0) {
//            $orderRequest->status = 2; // Partially received
//        } else {
//            $orderRequest->status = 1; // Ordered (not received yet)
//        }

        if ($requiredQty > $orderRequest->order_qty) {
            $orderRequest->status = 0; // Pending order
        } elseif ($orderRequest->received_qty >= $requiredQty) {
            $orderRequest->status = 3; // Fully received
        } elseif ($orderRequest->received_qty > 0) {
            $orderRequest->status = 2; // Partially received
        } else {
            $orderRequest->status = 1; // Ordered
        }

        if (!$orderRequest->save()) {
            throw new \Exception('updateOrderRequestAllocation: Failed to save order request: ' . json_encode($orderRequest->errors));
        }
    }

    private function handleReferenceType($orderRequest, $allocatedQty) {
        $reserved = 0;
        switch ($orderRequest->reference_type) {
            case "bom_detail":
                return $this->handleBomDetail($orderRequest, $allocatedQty);

            case "bomstockoutbound":
                return $this->handleBomStockOutbound($orderRequest, $allocatedQty);

            case "reserve":
                return $this->handleReservation($orderRequest, $allocatedQty);

            case "cm":
                return $this->handleCmms($orderRequest, $allocatedQty);

            case "pm":
                return $this->handleCmms($orderRequest, $allocatedQty);

            default:
                $qty = [
                    'reservedQty' => $reserved,
                    'balanceQty' => $allocatedQty
                ];
                return $qty;
        }
    }

    private function handleCmms($orderRequest, $allocatedQty) {
        $stockOutboundDetail = \frontend\models\cmms\CmmsWoMaterialRequestDetails::findOne([
            'id' => $orderRequest->reference_id,
            'active_sts' => 1
        ]);

        if (!$stockOutboundDetail)
            return $allocatedQty;

        $dispatchedQty = \frontend\models\cmms\CmmsStockDispatchTrial::find()
                ->where(['request_detail_id' => $stockOutboundDetail->id])
                ->sum('dispatch_qty');

        $requiredQty = $stockOutboundDetail->qty - $dispatchedQty;

        if ($requiredQty <= 0)
            return $allocatedQty;

        $consumeQty = min($allocatedQty, $requiredQty);

        $inventoryStockOutbound = new InventoryStockoutbound();
        $inventoryStockOutbound->inventory_detail_id = $orderRequest->inventory_detail_id;
        $inventoryStockOutbound->reference_type = $orderRequest->reference_type;
        $inventoryStockOutbound->reference_id = $stockOutboundDetail->id;
        $inventoryStockOutbound->reserve_item_id = null;
        $inventoryStockOutbound->qty = $consumeQty;

        if (!$inventoryStockOutbound->save()) {
            throw new \Exception("Failed to save InventoryStockoutbound");
        }

        $stockOutboundDetail->updateAllQtyInStockDetail($stockOutboundDetail, $orderRequest->reference_type);
        $stockOutboundDetail->updateMaterialRequestMasterStatus($stockOutboundDetail->requestMaster);

        $balance = $allocatedQty - $consumeQty;
        $balancePositive = $balance > 0 ? $balance : 0;

        $qty = [
            'reservedQty' => $inventoryStockOutbound->qty,
            'balanceQty' => $balancePositive
        ];

        return $qty;
    }

    private function handleReservation($orderRequest, $allocatedQty) {
        $reserveItem = new InventoryReserveItem();
        $reserveItem->user_id = $orderRequest->reference_id;
        $reserveItem->reference_type = $orderRequest->reference_type;
        $reserveItem->reference_id = $orderRequest->reference_id;
        $reserveItem->inventory_detail_id = $orderRequest->inventory_detail_id;
        $reserveItem->reserved_qty = $allocatedQty;
        $reserveItem->available_qty = $allocatedQty;
        if (!$reserveItem->save()) {
            throw new \Exception("Failed to save InventoryReserveItem");
        }

        $balance = 0;
        $reserved = $reserveItem->reserved_qty;

        $qty = [
            'reservedQty' => $reserved,
            'balanceQty' => $balance
        ];

        return $qty;
    }

    private function handleBomDetail($orderRequest, $allocatedQty) {
        $bomDetail = BomDetails::findOne($orderRequest->reference_id);
        if (!$bomDetail)
            return $allocatedQty;

        $bomMaster = BomMaster::findOne($bomDetail->bom_master);
        if (!$bomMaster)
            return $allocatedQty;

        if (!$bomMaster->productionPanel) {
            throw new \Exception('Production panel not found for BOM master: ' . $bomMaster->id);
        }

        if (!$bomMaster->productionPanel->projProdMaster) {
            throw new \Exception('Project production master not found for BOM master: ' . $bomMaster->id);
        }

        $totalPanels = $bomMaster->productionPanel->quantity;
        $requiredQty = $totalPanels * $bomDetail->qty;

        $reserveItem = new InventoryReserveItem();
        $reserveItem->user_id = $bomMaster->productionPanel->projProdMaster->created_by;
        $reserveItem->reference_type = $orderRequest->reference_type;
        $reserveItem->reference_id = $orderRequest->reference_id;
        $reserveItem->inventory_detail_id = $orderRequest->inventory_detail_id;
        $reserveItem->reserved_qty = min($allocatedQty, $requiredQty);
        $reserveItem->available_qty = min($allocatedQty, $requiredQty);

        if (!$reserveItem->save()) {
            throw new \Exception('Failed to save reserve item.');
        }

        $balance = $allocatedQty - $requiredQty;
        $balancePositive = $balance > 0 ? $balance : 0;

        $qty = [
            'reservedQty' => $reserveItem->reserved_qty,
            'balanceQty' => $balancePositive
        ];

        return $qty;
    }

    private function handleBomStockOutbound($orderRequest, $allocatedQty) {
        $stockOutboundDetail = \frontend\models\bom\StockOutboundDetails::findOne([
            'id' => $orderRequest->reference_id,
            'active_sts' => 1
        ]);

        if (!$stockOutboundDetail)
            return $allocatedQty;

        $dispatchedQty = \frontend\models\bom\StockDispatchTrial::find()
                ->where(['stock_outbound_details_id' => $stockOutboundDetail->id])
                ->sum('dispatch_qty');

        $requiredQty = $stockOutboundDetail->qty - $dispatchedQty;

        if ($requiredQty <= 0)
            return $allocatedQty;

        $consumeQty = min($allocatedQty, $requiredQty);

        $inventoryStockOutbound = new InventoryStockoutbound();
        $inventoryStockOutbound->inventory_detail_id = $orderRequest->inventory_detail_id;
        $inventoryStockOutbound->reference_type = $orderRequest->reference_type;
        $inventoryStockOutbound->reference_id = $stockOutboundDetail->id;
        $inventoryStockOutbound->reserve_item_id = null;
        $inventoryStockOutbound->qty = $consumeQty;

        if (!$inventoryStockOutbound->save()) {
            throw new \Exception("Failed to save InventoryStockoutbound");
        }

        $stockOutboundDetail->updateAllQtyInStockDetail($stockOutboundDetail);
        $stockOutboundDetail->updateStockMasterStatus($stockOutboundDetail->stockOutboundMaster->productionPanel->id);

        $balance = $allocatedQty - $consumeQty;
        $balancePositive = $balance > 0 ? $balance : 0;

        $qty = [
            'reservedQty' => $inventoryStockOutbound->qty,
            'balanceQty' => $balancePositive
        ];

        return $qty;
    }

    private function updatePOItemQty($poItem, $qty) {
        $poItem->received_qty += $qty;
        $poItem->remaining_qty = $poItem->order_qty - $poItem->received_qty;
        $poItem->status = $poItem->remaining_qty <= 0 ? \frontend\models\RefInventoryStatus::STATUS_FullyReceived : \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived;

        if (!$poItem->save()) {
            throw new \Exception('Failed to update PO item.');
        }
    }

    private function updateInventoryStock($poItem, $receive, $totalQtyFromAllocations) {
        $inventory = InventoryDetail::findOne($poItem->inventory_detail_id);

        if (!$inventory) {
            throw new \Exception("Inventory detail not found.");
        }

        if ($inventory->qty_pending_receipt < $receive->received_quantity) {
            throw new \Exception("Pending receipt quantity inconsistency for inventory ID: {$inventory->id}");
        }

        $inventory->stock_in += $receive->received_quantity;
        $inventory->stock_on_hand += $receive->received_quantity;
        $inventory->stock_reserved += $totalQtyFromAllocations['totalReserve'];
        $inventory->stock_available += ($receive->add_to_stock + $totalQtyFromAllocations['totalBalance']);
        $inventory->qty_pending_receipt -= $receive->received_quantity;

        if (!$inventory->save()) {
            throw new \Exception('Failed to update inventory stock.');
        }
    }

    public function updatePOStatus($po) {
        $poItems = InventoryPurchaseOrderItem::find()
                ->where(['inventory_po_id' => $po->id])
                ->all();

        if (empty($poItems)) {
            throw new \Exception('No items found for PO');
        }

        $allFullyReceived = true;
        $anyPartiallyReceived = false;

        foreach ($poItems as $item) {
            if ($item->remaining_qty > 0) {
                $allFullyReceived = false;
            }
            if ($item->received_qty > 0 && $item->remaining_qty > 0) {
                $anyPartiallyReceived = true;
            }
        }

        if ($allFullyReceived) {
            $po->status = \frontend\models\RefInventoryStatus::STATUS_FullyReceived;
        } elseif ($anyPartiallyReceived) {
            $po->status = \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived;
        }

        if (!$po->save()) {
            throw new \Exception('Failed to update PO status: ' . json_encode($po->errors));
        }
    }
}
