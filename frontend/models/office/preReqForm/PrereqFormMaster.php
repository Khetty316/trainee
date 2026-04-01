<?php

namespace frontend\models\office\preReqForm;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;
use frontend\models\RefInventoryStatus;
use frontend\models\inventory\InventoryReorderItem;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryDetail;
use frontend\models\inventory\InventorySupplier;
use frontend\models\office\preReqForm\PrereqFormItem;
use frontend\models\office\preReqForm\PrereqFormItemWorklist;
use frontend\models\bom\BomDetails;

/**
 * This is the model class for table "prereq_form_master".
 *
 * @property int $id
 * @property string $prf_no
 * @property string|null $date_of_material_required
 * @property float|null $total_amount
 * @property int|null $superior_id
 * @property string|null $filename
 * @property int|null $status
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $claim_flag 1 = claimed
 * @property int|null $inventory_flag 0 = no, 1 = yes
 * @property int|null $source_module 1 = general, 2 = inventory
 * @property int|null $reserved_by
 * @property string|null $reference_type reserve, bom, bomstockout,
 * @property int|null $reference_id
 *
 * @property InventoryPurchaseRequest[] $inventoryPurchaseRequests
 * @property InventoryReorderMaster[] $inventoryReorderMasters
 * @property PrereqFormItem[] $prereqFormItems
 * @property PrereqFormItemWorklist[] $prereqFormItemWorklists
 * @property User $superior
 * @property User $createdBy
 * @property User $updatedBy
 * @property RefGeneralStatus $status0
 * @property User $reservedBy
 */
class PrereqFormMaster extends \yii\db\ActiveRecord {

    const Prefix_PrfNo = "PRF";
    const runningNoLength = 5;
    const PERSONAL_USER_MANUAL_FILENAME = "T6B3-Pre Requisition Form Personal Module-01.pdf";
    const SUPERIOR_USER_MANUAL_FILENAME = "T6B3-Pre Requisition Form Superior Module-01.pdf";
    const SUPERUSER_USER_MANUAL_FILENAME = "T6B3-Pre Requisition Form Super User Module-01.pdf";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prereq_form_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prf_no'], 'required'],
            [['date_of_material_required', 'created_at', 'updated_at'], 'safe'],
//            [['total_amount'], 'number'],
            [['superior_id', 'status', 'is_deleted', 'created_by', 'updated_by', 'claim_flag', 'inventory_flag', 'source_module', 'reserved_by', 'reference_id'], 'integer'],
            [['prf_no', 'filename'], 'string', 'max' => 255],
            [['reference_type'], 'string', 'max' => 100],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['reserved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['reserved_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prf_no' => 'Prf No',
            'date_of_material_required' => 'Date Of Material Required',
//            'total_amount' => 'Total Amount',
            'superior_id' => 'Superior ID',
            'filename' => 'Filename',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'claim_flag' => 'Claim Flag',
            'inventory_flag' => 'Inventory Flag',
            'source_module' => 'Source Module',
            'reserved_by' => 'Reserved By',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'claim_flag' => 'Claim Submitted',
        ];
    }

    /**
     * Gets query for [[PrereqFormItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormItems() {
        return $this->hasMany(PrereqFormItem::className(), ['prereq_form_master_id' => 'id'])
                        ->andWhere(['is_deleted' => 0]);
    }

    public function getCheckPrereqFormItems() {
        return $this->hasMany(PrereqFormItem::className(), ['prereq_form_master_id' => 'id'])
                        ->andWhere(['is_deleted' => 0, 'status' => 0]);
    }

    /**
     * Gets query for [[Superior]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperior() {
        return $this->hasOne(User::className(), ['id' => 'superior_id']);
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
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[PrereqFormItemWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormItemWorklists() {
        return $this->hasMany(PrereqFormItemWorklist::className(), ['prereq_form_master_id' => 'id']);
    }

    /**
     * Gets query for [[PrereqFormStatusTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormStatusTrails() {
        return $this->hasMany(PrereqFormStatusTrail::className(), ['prereq_form_master_id' => 'id']);
    }

    /**
     * Gets query for [[ReservedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedBy() {
        return $this->hasOne(User::className(), ['id' => 'reserved_by']);
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

    public function generatePrfNo() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialPrfNo = self::Prefix_PrfNo;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $prfNo = $initialPrfNo . $runningNo . "-" . $currentMonth . $currentYearShort;

        return $prfNo;
    }

    /**
     * Create reorder in inventory module
     * @param array $items Array of PrereqFormItem objects
     * @throws \Exception
     */
    public function createReorderItem($reorderMaster, $items) {
        try {
            if (empty($items)) {
                throw new \Exception('No items provided for reorder');
            }

// Make sure $items is an array
            if (!is_array($items)) {
                $items = [$items];
            }

            $createdCount = 0;
            $skippedCount = 0;

// ===== CREATE REORDER ITEMS =====
            foreach ($items as $prfItem) {

// Validate quantity
                $orderQty = $prfItem->quantity_approved ?? $prfItem->quantity;
                if ($orderQty <= 0) {
                    \Yii::warning("PRF Item {$prfItem->id} has invalid quantity: {$orderQty}, skipping");
                    $skippedCount++;
                    continue;
                }

// Check if reorder item already exists
                $existingReorderItem = InventoryReorderItem::findOne([
                    'inventory_reorder_master_id' => $reorderMaster->id,
                    'prereq_form_item_id' => $prfItem->id
                ]);

                if ($existingReorderItem) {
                    \Yii::warning("Reorder item already exists for PRF Item {$prfItem->id}, skipping");
                    $skippedCount++;
                    continue;
                }

// Create reorder item
                $reorderItem = new InventoryReorderItem();
                $reorderItem->inventory_reorder_master_id = $reorderMaster->id;
                $reorderItem->prereq_form_item_id = $prfItem->id;
                $reorderItem->order_qty = $orderQty;
                $reorderItem->received_qty = 0;
                $reorderItem->remaining_qty = $orderQty;
                $reorderItem->receipt_status = RefInventoryStatus::STATUS_PendingReceive;
                $reorderItem->created_by = Yii::$app->user->id;
                $reorderItem->created_at = new \yii\db\Expression('NOW()');

                if (!$reorderItem->save()) {
                    throw new \Exception('Failed to create reorder item for PRF Item ' . $prfItem->id . ': ' . json_encode($reorderItem->getErrors()));
                }

// ===== UPDATE INVENTORY DETAIL QUANTITIES =====
                $inventoryDetail = \frontend\models\inventory\InventoryDetail::findOne($reorderItem->inventory_detail_id);

                if (!$inventoryDetail) {
                    throw new \Exception('Inventory detail not found for ID: ' . $reorderItem->inventory_detail_id);
                }

// Transfer from required_qty to reorder_qty
// Decrease required_qty (moving from "approved" to "ordered" status)
                $inventoryDetail->required_qty -= $orderQty;

// Increase reorder_qty (now officially in reorder/PO)
                $inventoryDetail->reorder_qty += $orderQty;

// Ensure required_qty doesn't go negative
                if ($inventoryDetail->required_qty < 0) {
                    $inventoryDetail->required_qty = 0; //required_qty went negative, resetting to 0
                }

                if (!$inventoryDetail->save(false)) {
                    throw new \Exception('Failed to update required and reorder quantity for Inventory Item ' . $reorderItem->inventory_detail_id . ': ' . json_encode($inventoryDetail->getErrors()));
                }

                $createdCount++;
            }

            if ($createdCount === 0) {
                \Yii::warning("No reorder items were created for reorder master {$reorderMaster->id}. Created: {$createdCount}, Skipped: {$skippedCount}");
            } else {
                \Yii::info("Reorder items summary - Created: {$createdCount}, Skipped: {$skippedCount}", __METHOD__);
            }

            return $createdCount > 0;
        } catch (\Exception $e) {
            \Yii::error("Reorder item creation failed: {$e->getMessage()}", __METHOD__);
            throw $e; // Re-throw to be caught by parent transaction
        }
    }

//    public function saveItems($masterId, $postItems, $saveWorklist, $moduleIndex) {
//        foreach ($postItems as $index => $itemData) {
//            $item = isset($itemData['id']) ? PrereqFormItem::findOne($itemData['id']) : new PrereqFormItem();
//            if (!$item) {
//                $item = new PrereqFormItem();
//            }
//
//            // ===== NORMALIZE INPUT =====
//            $departmentCode = $itemData['department_code'] ?? null;
//            $supplierInput = $itemData['supplier_name'] ?? null;
//            $brandInput = $itemData['brand_name'] ?? null;
//            $modelName = trim($itemData['model_name'] ?? '');
//
//            // Initialize variables
//            $supplierId = null;
//            $supplierName = null;
//            $brandId = null;
//            $brandName = null;
//
//            if ($moduleIndex === 'inventory') {
//                // For mechanical department: supplier and brand inputs are IDs
//                $supplierId = $supplierInput;
//                $brandId = $brandInput;
//
//                // Get supplier details
//                if ($supplierId) {
//                    $supplier = InventorySupplier::findOne($supplierId);
//                    $supplierName = $supplier ? $supplier->name : null;
//                }
//
//                // Get brand details
//                if ($brandId) {
//                    $brand = InventoryBrand::findOne($brandId);
//                    $brandName = $brand ? $brand->name : null;
//                }
//
//                // Get model details
//                if ($modelName) {
//                    $model = InventoryModel::find()
//                            ->where('LOWER(type) = :name', [':name' => mb_strtolower($modelName)])
//                            ->one();
//                    $modelName = $model ? $model->type : $modelName;
//                }
//
//                // ===== DUPLICATE CHECK =====
//                $isDuplicate = PrereqFormItem::checkInventoryDuplicate(
//                        $departmentCode,
//                        $supplierId,
//                        $brandId,
//                        $modelName,
//                        $item->id ?? null // Exclude current item if updating
//                );
//
//                if ($isDuplicate) {
//                    throw new \Exception('Duplicate inventory item detected at Item ' . ($index + 1));
//                }
//            } else {
//                // For other departments: supplier and brand inputs are text/names
//                $supplierName = $supplierInput;
//                $brandName = $brandInput;
//                $supplierId = null;
//                $brandId = null;
//            }
//
//            // ===== ASSIGN ITEM =====
//            $item->prereq_form_master_id = $masterId;
//            $item->department_code = $departmentCode;
//            $item->supplier_id = $supplierId;
//            $item->supplier_name = $supplierName;
//            $item->brand_id = $brandId;
//            $item->brand_name = $brandName;
//            $item->model_name = $modelName;
//            $item->model_group = $itemData['model_group'];
//            $item->item_description = $itemData['item_description'] ?? null;
//            $item->quantity = $itemData['quantity'] ?? null;
//            $item->model_unit_type = $itemData['unit_type'] ?? null;
//            $item->currency = $itemData['currency'] ?? null;
//            $item->unit_price = $itemData['unit_price'] ?? null;
//            $item->total_price = $itemData['total_price'] ?? null;
//            $item->purpose_or_function = $itemData['purpose_or_function'] ?? null;
//            $item->remark = $itemData['remark'] ?? null;
//            $item->is_deleted = 0;
//
//            if (!$item->save()) {
//                throw new \Exception('Item save failed: ' . json_encode($item->getErrors()));
//            }
//
//            // ===== WORKLIST =====
//            if ($saveWorklist) {
//                $worklist = PrereqFormItemWorklist::findOne([
//                    'prereq_form_master_id' => $masterId,
//                    'prereq_form_item_id' => $item->id,
//                ]);
//
//                if (!$worklist) {
//                    $worklist = new PrereqFormItemWorklist();
//                    $worklist->prereq_form_master_id = $masterId;
//                    $worklist->prereq_form_item_id = $item->id;
//                    $worklist->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
//                }
//
//                if (!$worklist->save()) {
//                    throw new \Exception('Worklist save failed: ' . json_encode($worklist->getErrors()));
//                }
//            }
//        }
//    }

    public static function createWithItems($post, $sourceModule, $referenceType, $referenceId) {
// ===== SAVE MASTER =====
        $master = new self();
        $data = $post['PrereqFormMaster'];

        $master->date_of_material_required = $data['date_of_material_required'] ?? null;
        $master->prf_no = $master->generatePrfNo();
        $master->superior_id = Yii::$app->user->identity->superior_id;
        $master->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
        $master->source_module = $sourceModule === 'inventory' ? 2 : 1;
        $master->reference_type = $referenceType;
        $master->reference_id = $referenceId;
        $master->is_deleted = 0;

        if (!$master->save()) {
            throw new \Exception('Master save failed: ' . json_encode($master->getErrors()));
        }

// ===== SAVE ITEMS =====
        self::saveItems($master->id, $post['VPrereqFormMasterDetail'] ?? [], $sourceModule, $referenceType, $referenceId);
        return $master;
    }

    public static function saveItems($masterId, $items, $sourceModule, $referenceType, $referenceId) {
        foreach ($items as $index => $data) {
            try {
                $item = isset($data['id']) ? PrereqFormItem::findOne($data['id']) : new PrereqFormItem();

                if (!$item) {
                    $item = new PrereqFormItem();
                }

                // Debug: Check what normalizeItem returns
                $normalized = self::normalizeItem($data, $sourceModule);

                if ($sourceModule === 'inventory') {
                    $exists = PrereqFormItem::checkInventoryDuplicate($normalized['department_code'], $normalized['supplier_id'], $normalized['brand_id'], $normalized['model_name'], $item->id ?? null);
                    if ($exists) {
                        throw new \Exception('Duplicate inventory item detected.');
                    }
                }

                $reference = self::handleReference($referenceType, $normalized, $referenceId);

                // This is where the error might occur - if $reference is an array but assignItem expects string
                self::assignItem($item, $masterId, $normalized, $reference);

                if (!$item->save()) {
                    throw new \Exception('Item ' . ($index + 1) . ' save failed: ' . json_encode($item->getErrors()));
                }
            } catch (\Exception $e) {
                throw $e; // Re-throw to maintain original flow
            }
        }
    }

    private static function normalizeItem($data, $sourceModule) {
        $result = $data;

        if ($sourceModule === 'inventory') {
            $supplier = InventorySupplier::findOne($data['supplier_name']);
            $brand = InventoryBrand::findOne($data['brand_name']);

            $result['supplier_id'] = $supplier->id;
            $result['supplier_name'] = $supplier->name;
            $result['brand_id'] = $brand->id;
            $result['brand_name'] = $brand->name;
        } else {
            $result['supplier_id'] = null;
            $result['brand_id'] = null;
        }

        return $result;
    }

    private static function handleReference($type, $data, $parentId) {
        if (!$type || !$parentId) {
            return null;
        }

        switch ($type) {
            case 'bom':
                $bomItem = BomDetails::findOne($data['item_reference_id']);
                if (!$bomItem) {
                    $bomItem = new BomDetails();
                    $bomItem->bom_master = $parentId;
                }
                $bomItem->brand = $data['brand_name'];
                $bomItem->model_type = $data['model_name'];
                $bomItem->description = $data['item_description'] ?? null;
                $bomItem->qty = $data['quantity'] ?? null;
                $bomItem->inventory_sts = 1;

                if (!$bomItem->save()) {
                    throw new \Exception(json_encode($bomItem->getErrors()));
                }

                return [
                    'type' => 'bom_detail',
                    'id' => $bomItem->id,
                ];

            case 'reserve':
                return [
                    'type' => 'reserve',
                    'id' => $parentId,
                ];

            default:
                return null;
        }
    }

    private static function assignItem($item, $masterId, $data, $reference) {
        $item->prereq_form_master_id = $masterId;
        $item->department_code = $data['department_code'];
        $item->supplier_id = $data['supplier_id'];
        $item->supplier_name = $data['supplier_name'];
        $item->brand_id = $data['brand_id'];
        $item->brand_name = $data['brand_name'];
        $item->model_name = $data['model_name'];
        $item->model_group = $data['model_group'] ?? null;
        $item->item_description = $data['item_description'] ?? null;
        $item->quantity = $data['quantity'] ?? null;
        $item->model_unit_type = $data['unit_type'] ?? null;
        $item->unit_price = $data['unit_price'] ?? null;
        $item->total_price = $data['total_price'] ?? null;
        $item->purpose_or_function = $data['purpose_or_function'] ?? null;
        $item->currency = $data['currency'] ?? null;
        $item->remark = $data['remark'] ?? null;
        $item->is_deleted = 0;

        if ($reference) {
            $item->reference_type = $reference['type'];
            $item->reference_id = $reference['id'];
        }
    }
}
