<?php

namespace frontend\models\inventory;

use Yii;
use frontend\models\RefInventoryStatus;
use common\models\User;
use frontend\models\common\RefCurrencies;

/**
 * This is the model class for table "inventory_purchase_order".
 *
 * @property int $id
 * @property string|null $po_no
 * @property string|null $po_date
 * @property int|null $supplier_id
 * @property int|null $status 1 = finalized
 * @property int|null $currency_id
 * @property string|null $company_group
 * @property int|null $total_qty
 * @property float|null $total_amount
 * @property float|null $total_discount
 * @property float|null $net_amount
 * @property float|null $tax_amount
 * @property float|null $gross_amount
 * @property string|null $comment
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $quotation_no
 * @property string|null $quotation_date
 * @property string|null $quotation_filename
 * @property int|null $uploaded_by
 * @property string|null $uploaded_at
 * @property int|null $active_sts 1 = no, 2 = yes
 *
 * @property RefInventoryStatus $status0
 * @property User $createdBy
 * @property User $updatedBy
 * @property RefCurrencies $currency
 * @property InventorySupplier $supplier
 * @property User $uploadedBy
 * @property InventoryPurchaseOrderItem[] $inventoryPurchaseOrderItems
 * @property InventoryPurchaseOrderReceiveBatch[] $inventoryPurchaseOrderReceiveBatches
 */
class InventoryPurchaseOrder extends \yii\db\ActiveRecord {

    const Prefix_Code = "PO";
    const runningNoLength = 3;

    public $amountWords;
    public $quotation_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['quotation_date', 'po_date', 'created_at', 'updated_at', 'amountWords'], 'safe'],
            [['supplier_id', 'status', 'currency_id', 'total_qty', 'created_by', 'updated_by', 'uploaded_by', 'active_sts'], 'integer'],
            [['total_amount', 'total_discount', 'net_amount', 'tax_amount', 'gross_amount'], 'number'],
            [['po_no', 'comment', 'quotation_no', 'quotation_filename'], 'string', 'max' => 255],
            [['company_group'], 'string', 'max' => 10],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefInventoryStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventorySupplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploaded_by' => 'id']],
            [['quotation_file'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf, doc, docx, xls, xlsx, jpg, jpeg, png',
                'maxSize' => 1024 * 1024 * 10, // 10MB
                'maxFiles' => 1,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'po_no' => 'PO No.',
            'po_date' => 'PO Date',
            'supplier_id' => 'Supplier ID',
            'status' => 'Status',
            'currency_id' => 'Currency ID',
            'company_group' => 'Company Group',
            'total_qty' => 'Total Qty',
            'total_amount' => 'Total Amount',
            'total_discount' => 'Total Discount',
            'net_amount' => 'Net Amount',
            'tax_amount' => 'Tax Amount',
            'gross_amount' => 'Gross Amount',
            'comment' => 'Comment',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'quotation_no' => 'Quotation No',
            'quotation_date' => 'Quotation Date',
            'quotation_filename' => 'Quotation Filename',
            'uploaded_by' => 'Uploaded By',
            'uploaded_at' => 'Uploaded At',
            'active_sts' => 'Active',
        ];
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefInventoryStatus::className(), ['id' => 'status']);
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
     * Gets query for [[UploadedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedBy() {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItems() {
        return $this->hasMany(InventoryPurchaseOrderItem::className(), ['inventory_po_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPurchaseOrderReceiveBatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderReceiveBatches() {
        return $this->hasMany(InventoryPurchaseOrderReceiveBatch::className(), ['inventory_po_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->po_no = $this->generatePoCode();
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    private function generatePoCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialCode = self::Prefix_Code;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $code = $initialCode . "-" . $runningNo . "-" . $currentMonth . $currentYearShort; // Generate the claim code

        return $code;
    }

    public function convertDateFormat($date) {
        if (empty($date)) {
            return null;
        }

        // If date is in DD/MM/YYYY format, convert to YYYY-MM-DD
        if (strpos($date, '/') !== false) {
            $parts = explode('/', $date);
            if (count($parts) == 3) {
                // DD/MM/YYYY -> YYYY-MM-DD
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        }

        // If already in YYYY-MM-DD format or other format, return as is
        return $date;
    }

    /*     * ***************************** Update Issued PO ************************************ */

    public function updatePoProcess($poData, $poItems) {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Update PO items
            $this->updatePoItems($poItems, $poData);
            $this->updatePurchaseOrderData($poData); // Update the PO itself
            $transaction->commit();
            return $this;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function updatePurchaseOrderData($poData) {
        $this->quotation_no = $poData['quotation_no'];
        $this->quotation_date = $this->convertDateFormat($poData['quotation_date']);
        $this->status = RefInventoryStatus::STATUS_AwaitingDelivery;
        $this->uploaded_by = Yii::$app->user->id;
        $this->uploaded_at = new \yii\db\Expression('NOW()');

        // Handle file upload for quotation
        $uploadedFile = \yii\web\UploadedFile::getInstanceByName('InventoryPurchaseOrder[quotation_file]');

        if ($uploadedFile) {
            $uploadPath = Yii::getAlias('@frontend/uploads/inventory-po-quotation/');

            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $filename = 'po_quotation_' . $this->id . '_' . time() . '.' . $uploadedFile->extension;
            $filePath = $uploadPath . $filename;

            // Delete old file if exists
            if (!empty($this->quotation_filename)) {
                $oldFile = $uploadPath . $this->quotation_filename;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Upload new file
            if ($uploadedFile->saveAs($filePath)) {
                $this->quotation_filename = $filename;
            } else {
                throw new \Exception('Failed to upload quotation file');
            }
        }

        $this->po_date = $this->convertDateFormat($poData['po_date']);
        $this->company_group = $poData['company_group'];
        $this->total_qty = $poData['total_quantity'];
        $this->currency_id = $poData['currency_id'];
        $this->comment = $poData['comment'];
        $this->total_discount = $poData['total_discount'];
        $this->tax_amount = $poData['tax_amount'];
        $this->total_amount = $poData['total_amount'];
        $this->net_amount = $poData['net_amount'];
        $this->gross_amount = $poData['gross_amount'];

        if (!$this->save()) {
            throw new \Exception('Failed to update PO: ' . json_encode($this->errors));
        }
    }

    private function updatePoItems($poItems, $poData) {
        foreach ($poItems as $poItem) {
            $item = !empty($poItem['id']) ? InventoryPurchaseOrderItem::findOne($poItem['id']) : null;

            if (!$item) {
                // New item being added
                $detail = InventoryDetail::findOne($poItem['inventory_detail_id']);
                if (!$detail) {
                    throw new \Exception("Inventory detail ID {$poItem['inventory_detail_id']} not found.");
                }

                $item = new InventoryPurchaseOrderItem();
                $item->inventory_po_id = $this->id;
                $item->inventory_detail_id = $detail->id;
                $item->department_code = $detail->department_code;
                $item->supplier_id = $detail->supplier_id;
                $item->brand_id = $detail->brand_id;
                $item->model_id = $detail->model_id;
                $item->model_type = $detail->model->type;
                $item->model_group = $detail->model->group;
                $item->model_description = $detail->model->description;
            }

            if ($poItem['removed'] == 1) {
                $orderRequestAllocations = InventoryOrderRequestAllocation::findAll(['inventory_po_item_id' => $item->id]);
                if (!empty($orderRequestAllocations)) {
                    $orderRequestAllocationDetail = [];
                    foreach ($orderRequestAllocations as $detail) {
                        // Create a new array with the data from the model plus 'removed' flag
                        $allocationData = $detail->toArray();  // Convert model to array
                        $allocationData['removed'] = 1;        // Add removed flag
                        $orderRequestAllocationDetail[] = $allocationData;
                    }

                    $item->updateOrderRequestAllocation($orderRequestAllocationDetail);
                }

                $item->is_deleted = 1;
                $item->deleted_by = \Yii::$app->user->id;
                $item->deleted_at = new \yii\db\Expression('NOW()');
            } else {
                $item->order_qty = $poItem['order_qty'];
                $item->unit_type = $poItem['unit_type'];
                $item->currency_id = $poData['currency_id'];
                $item->unit_price = $poItem['unit_price'];
                $item->discount_amt = $poItem['discount_amt'];
                $item->total_price = $poItem['total_price'];
                $item->remaining_qty = $poItem['order_qty'] - ($item->received_qty ?? 0);
            }

            if (!$item->save()) {
                throw new \Exception('Failed to update PO item: ' . json_encode($item->errors));
            }

            // Only recalculate pending receipt for non-removed items
//            if ($poItem['removed'] != 1) {
            $item->updateInventoryQtyPendingReceipt();
//            }
        }
    }

    public function deactivatePoProcess() {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $items = InventoryPurchaseOrderItem::find()
                    ->where(['inventory_po_id' => $this->id, 'is_deleted' => 0])
                    ->all();

            foreach ($items as $item) {

                // Reverse allocation (same as removed = 1 logic)
                $allocations = InventoryOrderRequestAllocation::findAll([
                    'inventory_po_item_id' => $item->id
                ]);

                if (!empty($allocations)) {
                    $allocationData = [];

                    foreach ($allocations as $detail) {
                        $data = $detail->toArray();
                        $data['removed'] = 1;
                        $allocationData[] = $data;
                    }

                    $item->updateOrderRequestAllocation($allocationData);
                }

                // Soft delete item
                $item->is_deleted = 1;
                $item->deleted_by = \Yii::$app->user->id;
                $item->deleted_at = new \yii\db\Expression('NOW()');

                if (!$item->save()) {
                    throw new \Exception('Failed to deactivate PO item: ' . json_encode($item->errors));
                }

                $item->updateInventoryQtyPendingReceipt();
            }

            // Deactivate PO
            $this->active_sts = 1; // inactive
            if (!$this->save(false)) {
                throw new \Exception('Failed to deactivate PO');
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
