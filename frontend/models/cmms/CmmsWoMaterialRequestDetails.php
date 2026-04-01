<?php

namespace frontend\models\cmms;

use Yii;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use common\models\User;
use frontend\models\bom\StockDispatchMaster;

/**
 * This is the model class for table "cmms_wo_material_request_details".
 *
 * @property int $id
 * @property int $request_master_id
 * @property int|null $fault_id
 * @property int|null $part_or_tool 1 = part, 2 = tool
 * @property int|null $inventory_model_id
 * @property string|null $model_type
 * @property int|null $inventory_brand_id
 * @property string|null $brand
 * @property string|null $descriptions
 * @property float|null $qty
 * @property string|null $remark
 * @property int|null $qty_stock_available
 * @property float|null $dispatched_qty
 * @property float|null $unacknowledged_qty
 * @property int $active_sts
 * @property int $fully_dispatch_status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_finalized 1 = no, 2 = yes, 3 = outbound
 * @property int|null $inventory_sts 1 = pending approval, 2 = linked, 3 = rejected, 4 = pending requestor confirmation, 5 = Purchasing in Progress
 *
 * @property CmmsWoMaterialRequestMaster $requestMaster
 * @property InventoryModel $inventoryModel
 * @property InventoryBrand $inventoryBrand
 * @property User $createdBy
 * @property User $updatedBy
 * @property CmmsFaultList $fault
 */
class CmmsWoMaterialRequestDetails extends \yii\db\ActiveRecord {

    CONST runningNoLength = 2;

    // Virtual attributes for form input
    public $model_type_input;
    public $brand_input;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cmms_wo_material_request_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['qty', 'request_master_id'], 'required'],
            [['request_master_id', 'fault_id', 'part_or_tool', 'inventory_model_id', 'inventory_brand_id', 'qty_stock_available', 'active_sts', 'fully_dispatch_status', 'created_by', 'updated_by', 'is_finalized', 'inventory_sts'], 'integer'],
            [['dispatched_qty', 'unacknowledged_qty'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['model_type', 'brand', 'descriptions', 'remark'], 'string', 'max' => 1000],
            [['request_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsWoMaterialRequestMaster::className(), 'targetAttribute' => ['request_master_id' => 'id']],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['fault_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsFaultList::className(), 'targetAttribute' => ['fault_id' => 'id']],
// Validation: Either use dropdown system (model_type_input + brand_input) OR legacy (model_type + brand)
            ['model_type', 'required', 'when' => function ($model) {
                    // Model type is required if not using dropdown system
                    return empty($model->model_type_input);
                }, 'message' => 'Model Type is required.'],
            ['brand', 'required', 'when' => function ($model) {
                    // Brand is required if not using dropdown system
                    return empty($model->brand_input);
                }, 'message' => 'Brand is required.'],
            ['model_type_input', 'required', 'when' => function ($model) {
                    // Required if using new system (not legacy)
                    return empty($model->model_type);
                }, 'message' => 'Please select a Model Type from the dropdown.'],
            ['brand_input', 'required', 'when' => function ($model) {
                    // Required if using new system (not legacy)
                    return empty($model->brand);
                }, 'message' => 'Please select a Brand from the dropdown.'],
            // Custom validation
            ['model_type_input', 'validateModelInput', 'skipOnEmpty' => false],
            ['brand_input', 'validateBrandInput', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Custom validation for model type input
     */
    public function validateModelInput($attribute, $params) {
        // If model_type_input is provided, it must be a valid dropdown selection
        if (!empty($this->model_type_input)) {
            $inventoryModel = InventoryModel::findOne($this->model_type_input);
            if (!$inventoryModel) {
                $this->addError($attribute, 'Selected Model Type is invalid.');
            } else {
                // Set the inventory_model_id for saving
                $this->inventory_model_id = $this->model_type_input;

                // Auto-fill model_type from inventory
                $this->model_type = $inventoryModel->type;

                $this->inventory_sts = 2;

                // Auto-fill description from inventory
                if (!empty($inventoryModel->description)) {
                    $this->descriptions = $inventoryModel->description;
                }
            }
        } else if (!empty($this->model_type)) {
            // Legacy record - clear inventory model ID
            $this->inventory_model_id = null;
        }
    }

    /**
     * Custom validation for brand input
     */
    public function validateBrandInput($attribute, $params) {
        // If brand_input is provided, it must be a valid dropdown selection
        if (!empty($this->brand_input)) {
            $inventoryBrand = InventoryBrand::findOne($this->brand_input);
            if (!$inventoryBrand) {
                $this->addError($attribute, 'Selected Brand is invalid.');
            } else {
                // Set the inventory_brand_id for saving
                $this->inventory_brand_id = $this->brand_input;

                // Auto-fill brand from inventory
                $this->brand = $inventoryBrand->name;
            }
        } else if (!empty($this->brand)) {
            // Legacy record - clear inventory brand ID
            $this->inventory_brand_id = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'request_master_id' => 'Request Master ID',
            'fault_id' => 'Fault ID',
            'part_or_tool' => 'Part/Tool',
            'inventory_model_id' => 'Inventory Model ID',
            'model_type' => 'Model Type',
            'inventory_brand_id' => 'Inventory Brand ID',
            'brand' => 'Brand',
            'descriptions' => 'Descriptions',
            'qty' => 'Quantity',
            'remark' => 'Remark',
            'qty_stock_available' => 'Qty Stock Available',
            'dispatched_qty' => 'Dispatched Qty',
            'unacknowledged_qty' => 'Unacknowledged Qty',
            'active_sts' => 'Active Sts',
            'fully_dispatch_status' => 'Fully Dispatch Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'is_finalized' => 'Is Finalized',
            'inventory_sts' => 'Inventory Sts',
        ];
    }

    /**
     * Gets query for [[RequestMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMaster() {
        return $this->hasOne(CmmsWoMaterialRequestMaster::className(), ['id' => 'request_master_id']);
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
     * Gets query for [[InventoryBrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'inventory_brand_id']);
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
     * Gets query for [[Fault]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFault() {
        return $this->hasOne(CmmsFaultList::className(), ['id' => 'fault_id']);
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            // The custom validators already set inventory_model_id and inventory_brand_id
            // Get text values from inventory if using dropdown
            if (!empty($this->inventory_model_id)) {
                $inventoryModel = InventoryModel::findOne($this->inventory_model_id);
                if ($inventoryModel) {
                    $this->model_type = $inventoryModel->type;
                }
            }

            if (!empty($this->inventory_brand_id)) {
                $inventoryBrand = InventoryBrand::findOne($this->inventory_brand_id);
                if ($inventoryBrand) {
                    $this->brand = $inventoryBrand->name;
                }
            }

            // Clear temporary input fields (they've already been processed)
            $this->model_type_input = null;
            $this->brand_input = null;

            // Set created_by and created_at for new records
            if ($insert) {
                if (empty($this->created_by)) {
                    $this->created_by = Yii::$app->user->id;
                }
                if (empty($this->created_at)) {
                    $this->created_at = new \yii\db\Expression('NOW()');
                }
            }

            return true;
        }
        return false;
    }

    public function isLegacyRecord() {
        return (!empty($this->model_type) || !empty($this->brand)) &&
                empty($this->inventory_model_id) &&
                empty($this->inventory_brand_id);
    }

    public function processStockDispatch($requestMaster, $postData, $actionType) {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ((isset($postData['dispatch']) ? $postData['dispatch'] : $postData) as $key => $data) {
                $dispatchMasterId = $this->createDispatchMaster($requestMaster, $postData, $actionType);

                foreach ($data as $detailId => $detail) {
                    if (!isset($detail['dispatch_qty'], $detail['remark'])) {
                        throw new \Exception("Invalid input data for detail ID {$detailId}");
                    }

                    $dispatchQty = (float) $detail['dispatch_qty'];
                    if ($dispatchQty == 0) {
                        continue;
                    }

                    $stockDetail = self::findOne($detailId);
                    if (!$stockDetail) {
                        throw new \Exception("Stock detail ID {$detailId} not found");
                    }

                    $totalAcknowledged = CmmsStockDispatchTrial::find()
                                    ->where([
                                        'request_detail_id' => $stockDetail->id,
                                        'current_sts' => \frontend\models\bom\StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                                    ])
                                    ->sum('dispatch_qty') ?? 0;

                    $totalPendingPositive = CmmsStockDispatchTrial::find()
                                    ->where([
                                        'request_detail_id' => $stockDetail->id,
                                        'current_sts' => \frontend\models\bom\StockDispatchMaster::TO_BE_ACKNOWLEDGED
                                    ])
                                    ->andWhere(['>', 'dispatch_qty', 0])
                                    ->sum('dispatch_qty') ?? 0;

                    if ($dispatchQty > 0) {
                        $effectiveCommitted = $totalAcknowledged + $totalPendingPositive;
                        $newTotal = $effectiveCommitted + $dispatchQty;
                        if ($newTotal > $stockDetail->qty) {
                            throw new \Exception("Dispatch exceeds assigned quantity for detail ID {$detailId}");
                        }
                    }

                    $this->createDispatchTrial(
                            $dispatchQty,
                            $detail['remark'],
                            $actionType,
                            $stockDetail->id,
                            $dispatchMasterId,
                            $postData['current_sts']
                    );

                    $this->updateAllQtyInStockDetail($stockDetail, $requestMaster->wo_type);
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function createDispatchMaster($requestMaster, $postData, $actionType) {
        $runningNo = CmmsStockDispatchMaster::find()->where(['wo_type' => $requestMaster->wo_type, 'wo_id' => $requestMaster->wo_id])->count() + 1;
        if ($requestMaster->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
            $cm = CmmsCorrectiveWorkOrderMaster::findOne($requestMaster->wo_id);
            $woCode = ("CM" . $requestMaster->wo_id);
        } else if ($requestMaster->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
            $pm = CmmsPreventiveWorkOrderMaster::findOne($requestMaster->wo_id);
            $woCode = ("PM" . $requestMaster->wo_id);
        }

        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $dispatchNo = $woCode . "-" . $runningNo;
        if ($postData['receiver']['id'] !== null || ($actionType !== \frontend\models\bom\StockDispatchTrial::ADJUST_STATUS && $actionType !== \frontend\models\bom\StockDispatchTrial::RETURN_STATUS)) {
            $dispatchMaster = new CmmsStockDispatchMaster();
            $dispatchMaster->dispatch_no = $dispatchNo;
            $dispatchMaster->wo_type = $requestMaster->wo_type;
            $dispatchMaster->wo_id = $requestMaster->wo_id;
            $dispatchMaster->received_by = $postData['receiver']['id'];
            $dispatchMaster->status = $postData['current_sts'];
            $dispatchMaster->save();
            return $dispatchMaster->id;
        } else {
            return null;
        }
    }

    private function createDispatchTrial($newDispatchQty, $remark, $actionType, $stockDetailId, $dispatchMasterId, $current_sts) {
        $dispatchTrial = new CmmsStockDispatchTrial();
        $dispatchTrial->request_detail_id = $stockDetailId;
        $dispatchTrial->stock_dispatch_master_id = $dispatchMasterId;
        $dispatchTrial->dispatch_qty = $newDispatchQty;
        $dispatchTrial->status = $actionType;
        $dispatchTrial->remark = $remark;
        $dispatchTrial->current_sts = $current_sts;

        if (!$dispatchTrial->save()) {
            throw new \Exception("Failed to save dispatch trial: " . json_encode($dispatchTrial->errors));
        }
    }

    public function updateAllQtyInStockDetail($stockDetail, $referenceType) {

        /*
          |--------------------------------------------------------------------------
          | 1️⃣ Acknowledged (PHYSICAL movement only)
          |--------------------------------------------------------------------------
         */
        $totalAcknowledged = CmmsStockDispatchTrial::find()
                        ->where([
                            'request_detail_id' => $stockDetail->id,
                            'current_sts' => \frontend\models\bom\StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                        ])
                        ->sum('dispatch_qty') ?? 0;

        /*
          |--------------------------------------------------------------------------
          | 2️⃣ Unacknowledged (display purpose only)
          |--------------------------------------------------------------------------
         */
        $totalUnacknowledged = CmmsStockDispatchTrial::find()
                        ->where(['request_detail_id' => $stockDetail->id])
                        ->andWhere([
                            'in',
                            'current_sts',
                            [
                                \frontend\models\bom\StockDispatchMaster::TO_BE_ACKNOWLEDGED,
                                \frontend\models\bom\StockDispatchMaster::TO_BE_COLLECTED
                            ]
                        ])
                        ->sum('dispatch_qty') ?? 0;

        /*
          |--------------------------------------------------------------------------
          | 3️⃣ Get allocated qty from inventory
          |--------------------------------------------------------------------------
         */
        $allocateQty = \frontend\models\inventory\InventoryStockoutbound::find()
                        ->where([
                            'reference_type' => $referenceType,
                            'reference_id' => $stockDetail->id,
                        ])
                        ->sum('qty') ?? 0;

        /*
          |--------------------------------------------------------------------------
          | 4️⃣ Available = Allocated - Acknowledged ONLY
          |--------------------------------------------------------------------------
         */
        $availableQty = $allocateQty - $totalAcknowledged;

        if ($availableQty < 0) {
            throw new \Exception("Available stock cannot be negative for detail ID: " . $stockDetail->id);
        }

        /*
          |--------------------------------------------------------------------------
          | 5️⃣ Update stock detail
          |--------------------------------------------------------------------------
         */
        $stockDetail->dispatched_qty = $totalAcknowledged;
        $stockDetail->unacknowledged_qty = $totalUnacknowledged;
        $currentDispatched = $stockDetail->dispatched_qty + $stockDetail->unacknowledged_qty;
        $stockDetail->qty_stock_available = $availableQty;

        $stockDetail->fully_dispatch_status = ($stockDetail->qty == $currentDispatched) ? 1 : 0;

        if (!$stockDetail->save(false)) {
            throw new \Exception("Failed to update stock detail qty: " . json_encode($stockDetail->errors));
        }
    }

    public function updateMaterialRequestMasterStatus($requestMaster) {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $hasPendingDispatch = CmmsWoMaterialRequestDetails::find()
                    ->where([
                        'request_master_id' => $requestMaster->id,
                        'fully_dispatch_status' => 0])
                    ->exists();

            $requestMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;

            if (!$requestMaster->save(false)) {
                throw new \Exception("Failed to update stock master status: " . json_encode($requestMaster->errors));
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function revertInventoryStockQty($revertQty, $stockDetail, $referenceType) {
        $remainingQty = abs((float) $revertQty);

        // Find all inventory outbound records in reverse order (LIFO for reversion)
        $inventoryStockOutboundList = \frontend\models\inventory\InventoryStockoutbound::find()
                ->where([
                    'reference_type' => $referenceType,
                    'reference_id' => $stockDetail->id
                ])
                ->andWhere(['>', 'dispatched_qty', 0]) // Only records with dispatched quantity
                ->orderBy(['created_at' => SORT_DESC]) // LIFO - reverse the FIFO acknowledgement
                ->all();

        if (empty($inventoryStockOutboundList)) {
            throw new \Exception("No dispatched inventory found for reversion.");
        }

        foreach ($inventoryStockOutboundList as $stockOutbound) {
            if ($remainingQty <= 0) {
                break;
            }

            $inventoryDetail = \frontend\models\inventory\InventoryDetail::findOne($stockOutbound->inventory_detail_id);
            if (!$inventoryDetail) {
                throw new \Exception("Failed to find item in inventory library.");
            }

            $dispatchedQty = (float) $stockOutbound->dispatched_qty;
            if ($dispatchedQty <= 0) {
                continue;
            }

            // Calculate how much to revert from this record
            $revertAmount = min($remainingQty, $dispatchedQty);

            // Safety check
            if ($revertAmount > $dispatchedQty) {
                throw new \Exception("Revert amount exceeds dispatched quantity.");
            }

            // Reverse the inventory adjustments made during acknowledgement
            $inventoryDetail->stock_reserved += $revertAmount;
            $inventoryDetail->stock_on_hand += $revertAmount;
            $inventoryDetail->stock_out -= $revertAmount;
            $inventoryDetail->stock_available = ($inventoryDetail->stock_on_hand - $inventoryDetail->stock_reserved);

            // Validation checks
            if ($inventoryDetail->stock_out < 0) {
                throw new \Exception("Stock out cannot be negative.");
            }

            if ($inventoryDetail->stock_reserved > $inventoryDetail->stock_on_hand) {
                throw new \Exception("Reserved stock cannot be more than stock on hand.");
            }

            if ($inventoryDetail->stock_available > $inventoryDetail->stock_on_hand) {
                throw new \Exception("Available stock cannot be more than stock on hand.");
            }

            if (!$inventoryDetail->save(false)) {
                throw new \Exception("Failed to revert inventory detail.");
            }

            // Update the outbound record
            $stockOutbound->dispatched_qty -= $revertAmount;
            if (!$stockOutbound->save(false)) {
                throw new \Exception("Failed to update outbound record during reversion.");
            }

            $remainingQty -= $revertAmount;
        }

        if ($remainingQty > 0) {
            throw new \Exception('Not enough dispatched stock to revert.');
        }
    }

    public function updateInventoryStockQty($dispatchQty, $stockDetail, $referenceType) {
        $remainingQty = (float) $dispatchQty;
        $inventoryStockOutboundList = \frontend\models\inventory\InventoryStockoutbound::find()
                ->where([
                    'reference_type' => $referenceType,
                    'reference_id' => $stockDetail->id
                ])
                ->orderBy(['created_at' => SORT_ASC]) // FIFO
                ->all();

        if (empty($inventoryStockOutboundList)) {
            throw new \Exception("No reserved inventory found for dispatch.");
        }

        foreach ($inventoryStockOutboundList as $stockOutbound) {
            if ($remainingQty <= 0) {
                break;
            }

            $inventoryDetail = \frontend\models\inventory\InventoryDetail::findOne($stockOutbound->inventory_detail_id);

            if (!$inventoryDetail) {
                throw new \Exception("Failed to find item in inventory library.");
            }

            $reservedQty = (float) $inventoryDetail->stock_reserved;

            if ($reservedQty <= 0) {
                continue;
            }

            $deductQty = min($remainingQty, $reservedQty);

            // Safety check
            if ($deductQty > $reservedQty) {
                throw new \Exception("Dispatch exceeds reserved quantity.");
            }

            $inventoryDetail->stock_reserved -= $deductQty;

            $inventoryDetail->stock_on_hand -= $deductQty;

            $inventoryDetail->stock_out += $deductQty;

            $inventoryDetail->stock_available = ($inventoryDetail->stock_on_hand - $inventoryDetail->stock_reserved);

            if ($inventoryDetail->stock_on_hand < 0) {
                throw new \Exception("Stock on hand cannot be negative.");
            }

            if ($inventoryDetail->stock_reserved > $inventoryDetail->stock_on_hand) {
                throw new \Exception("reserved stock cannot be more than stock on hand.");
            }

            if ($inventoryDetail->stock_available > $inventoryDetail->stock_on_hand) {
                throw new \Exception("Available stock cannot be more than stock on hand.");
            }

            if (!$inventoryDetail->save(false)) {
                throw new \Exception("Failed to update inventory detail.");
            }

            $stockOutbound->dispatched_qty += $deductQty;
            if (!$stockOutbound->save(false)) {
                throw new \Exception("Failed to update outbound record.");
            }

            $remainingQty -= $deductQty;
        }

        if ($remainingQty > 0) {
            throw new \Exception('Not enough reserved stock to dispatch.');
        }
    }
    
    public function processStockReturn($postData, $dispatchMaster, $actionType) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!isset($postData['dispatch_id'], $postData['dispatch_qty'], $postData['remark'])) {
                throw new \Exception("Invalid input data");
            }

            $newDispatchQty = -$postData['dispatch_qty'];
            $newTotalDispatchQty = $dispatchMaster->total_trial_dispatch_qty - $postData['dispatch_qty'];
            if ($newTotalDispatchQty > $this->qty) {
                throw new \Exception("Total dispatch quantity exceeds total stock quantity");
            }

            $this->createDispatchTrial($newDispatchQty, $postData['remark'], $actionType, $this->id, $postData['dispatch_id'], StockDispatchMaster::TO_BE_ACKNOWLEDGED);
            $dispatchMasterSts = CmmsStockDispatchMaster::findOne($postData['dispatch_id']);
            $dispatchMasterSts->updateDispatchMasterStatus();
            $this->updateAllQtyInStockDetail($this, $dispatchMaster->wo_type);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err($e->getMessage());
            return false;
        }
    }
}
