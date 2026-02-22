<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

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
    public function processOrderReceive($po, $postData, $requestPost) {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            // Save the batch record first
            if (!$this->save()) {
                throw new \Exception('Failed to create receive batch: ' . json_encode($this->errors));
            }

            // Step 1: Validate that we have items to receive
            if (!isset($postData['receive']) || empty($postData['receive'])) {
                throw new \Exception('No items to receive.');
            }

            // Step 2: Save documents first
            $uploadedFiles = \yii\web\UploadedFile::getInstances(
                    new InventoryPurchaseOrderItemDoc(),
                    'filename'
            );

            $documentTypes = $requestPost['InventoryPurchaseOrderItemDoc']['document_type'] ?? [];
            $documentNos = $requestPost['InventoryPurchaseOrderItemDoc']['document_no'] ?? [];

            $uploadPath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/');

            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    throw new \Exception('Failed to create upload directory.');
                }
            }

            // Validate that at least one document is uploaded
            $hasValidDocument = false;
            foreach ($uploadedFiles as $index => $file) {
                if ($file && isset($documentTypes[$index]) && isset($documentNos[$index]) &&
                        $documentTypes[$index] !== '' && $documentNos[$index] !== '') {
                    $hasValidDocument = true;
                    break;
                }
            }

            if (!$hasValidDocument) {
                throw new \Exception('At least one document must be uploaded.');
            }

            // Track uploaded files for cleanup on error
            $uploadedFilePaths = [];

            // Save each document
            foreach ($uploadedFiles as $index => $file) {
                if ($file && isset($documentTypes[$index]) && isset($documentNos[$index]) &&
                        $documentTypes[$index] !== '' && $documentNos[$index] !== '') {

                    $attachment = new InventoryPurchaseOrderItemDoc();
                    $attachment->receive_batch_id = $this->id;
                    $attachment->document_type = $documentTypes[$index];
                    $attachment->document_no = trim($documentNos[$index]);

                    // Generate unique filename
                    $filename = 'receive_batch_' . $this->id . '_' . time() . '_' . $index . '.' . $file->extension;
                    $filePath = $uploadPath . $filename;

                    // Save file to disk
                    if (!$file->saveAs($filePath)) {
                        throw new \Exception('Failed to upload file: ' . $file->name);
                    }

                    $uploadedFilePaths[] = $filePath;
                    $attachment->filename = $filename;

                    if (!$attachment->save()) {
                        throw new \Exception('Failed to save attachment: ' . json_encode($attachment->errors));
                    }
                }
            }

            // Step 3: Save receive items and update stock
            foreach ($postData['receive'] as $items) {
                foreach ($items as $itemId => $item) {
                    // Validate received quantity
                    if (!isset($item['new_receive_qty']) || $item['new_receive_qty'] <= 0) {
                        throw new \Exception("Invalid receive quantity for item ID: {$itemId}");
                    }

                    // Get PO item with lock to prevent race conditions
                    $poItem = InventoryPurchaseOrderItem::find()
                            ->where(['id' => $itemId])
                            ->one();

                    if (!$poItem) {
                        throw new \Exception("PO item not found: {$itemId}");
                    }

                    // Validate we're not receiving more than ordered
                    $newReceivedTotal = ($poItem->received_qty ?? 0) + $item['new_receive_qty'];
                    if ($newReceivedTotal > $poItem->order_qty) {
                        throw new \Exception("Cannot receive more than ordered quantity for item ID: {$itemId}");
                    }

                    // Create receive record
                    $receive = new InventoryPurchaseOrderItemReceive();
                    $receive->receive_batch_id = $this->id;
                    $receive->inventory_po_item_id = $itemId;
                    $receive->received_quantity = $item['new_receive_qty'];

                    if (!$receive->save()) {
                        throw new \Exception('Failed to save receive record: ' . json_encode($receive->errors));
                    }

                    // Update PO item quantities
                    $poItem->received_qty = $newReceivedTotal;
                    $poItem->remaining_qty = $poItem->order_qty - $poItem->received_qty;

                    // Update status
                    if ($poItem->remaining_qty <= 0) {
                        $poItem->status = \frontend\models\RefInventoryStatus::STATUS_FullyReceived;
                    } else {
                        $poItem->status = \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived;
                    }

                    if (!$poItem->save()) {
                        throw new \Exception('Failed to update PO item: ' . json_encode($poItem->errors));
                    }

                    // Update stock on hand - INLINE to keep in transaction
                    $inventoryDetail = InventoryDetail::findOne($poItem->inventory_detail_id);
                    if (!$inventoryDetail) {
                        throw new \Exception("Inventory detail not found for PO item: {$itemId}");
                    }

                    $inventoryDetail->stock_on_hand += $receive->received_quantity;
                    $inventoryDetail->qty_pending_receipt -= $receive->received_quantity;

                    if (!$inventoryDetail->save()) {
                        throw new \Exception('Failed to update stock on hand: ' . json_encode($inventoryDetail->errors));
                    }
                }
            }

            // Step 4: Update PO status
            $this->updatePOStatus($po);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();

            // Clean up uploaded files on error
            if (isset($uploadedFilePaths)) {
                foreach ($uploadedFilePaths as $filePath) {
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            Yii::error('Error processing order receive: ' . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Update PO status based on all items
     * @param InventoryPurchaseOrder $po
     * @throws \Exception
     */
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
