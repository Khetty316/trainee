<?php

namespace frontend\models\bom;

use Yii;
use common\models\myTools\FlashHandler;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryStockoutbound;

/**
 * This is the model class for table "stock_outbound_details".
 *
 * @property int $id
 * @property int $stock_outbound_master_id
 * @property int|null $bom_detail_id
 * @property int|null $inventory_model_id
 * @property string|null $model_type
 * @property int|null $inventory_brand_id
 * @property string|null $brand
 * @property string|null $descriptions
 * @property float|null $qty
 * @property string|null $engineer_remark
 * @property int|null $qty_stock_available
 * @property float|null $dispatched_qty
 * @property float|null $unacknowledged_qty
 * @property int $active_sts
 * @property int $fully_dispatch_status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $inventory_sts 1 = no, 2 = linked
 *
 * @property StockDispatchTrial[] $stockDispatchTrials
 * @property StockOutboundMaster $stockOutboundMaster
 * @property BomDetails $bomDetail
 * @property InventoryModel $inventoryModel
 * @property InventoryBrand $inventoryBrand
 */
class StockOutboundDetails extends \yii\db\ActiveRecord {

    CONST runningNoLength = 2;

// Virtual attributes for form input
    public $model_type_input;
    public $brand_input;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'stock_outbound_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['stock_outbound_master_id'], 'required'],
            [['stock_outbound_master_id', 'bom_detail_id', 'inventory_model_id', 'inventory_brand_id', 'qty_stock_available', 'active_sts', 'fully_dispatch_status', 'created_by', 'updated_by', 'inventory_sts'], 'integer'],
            [['qty', 'dispatched_qty', 'unacknowledged_qty'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['model_type', 'brand', 'descriptions', 'engineer_remark'], 'string', 'max' => 1000],
            [['stock_outbound_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => StockOutboundMaster::className(), 'targetAttribute' => ['stock_outbound_master_id' => 'id']],
            [['bom_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => BomDetails::className(), 'targetAttribute' => ['bom_detail_id' => 'id']],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
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
            'stock_outbound_master_id' => 'Stock Outbound Master ID',
            'bom_detail_id' => 'Bom Detail ID',
            'inventory_model_id' => 'Inventory Model ID',
            'model_type' => 'Model Type',
            'inventory_brand_id' => 'Inventory Brand ID',
            'brand' => 'Brand',
            'descriptions' => 'Description',
            'qty' => 'Quantity',
            'engineer_remark' => 'Engineer Remark',
            'qty_stock_available' => 'Qty Stock Available',
            'dispatched_qty' => 'Dispatched Qty',
            'unacknowledged_qty' => 'Unacknowledged Qty',
            'active_sts' => 'Active Sts',
            'fully_dispatch_status' => 'Fully Dispatch Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'inventory_sts' => 'Inventory Sts',
        ];
    }

    /**
     * Gets query for [[StockDispatchMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockDispatchMasters() {
        return $this->hasMany(StockDispatchMaster::className(), ['stock_outbound_details_id' => 'id']);
    }

    /**
     * Gets query for [[StockDispatchTrials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockDispatchTrials() {
        return $this->hasMany(StockDispatchTrial::className(), ['stock_outbound_details_id' => 'id']);
    }

    /**
     * Gets query for [[StockOutboundMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundMaster() {
        return $this->hasOne(StockOutboundMaster::className(), ['id' => 'stock_outbound_master_id']);
    }

    /**
     * Gets query for [[BomDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBomDetail() {
        return $this->hasOne(BomDetails::className(), ['id' => 'bom_detail_id']);
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

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    /**
     * Process each stock detail, update dispatched quantity and create a dispatch trial.
     * before inventory
     */
//    public function processStockDispatch($bomMaster, $productionPanelId, $postData, $actionType) {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            foreach ((isset($postData['dispatch']) ? $postData['dispatch'] : $postData) as $key => $data) {
//                $dispatchMasterId = $this->createDispatchMaster($bomMaster, $productionPanelId, $postData, $actionType);
//                foreach ($data as $detailId => $detail) {
//                    if (!isset($detail['dispatch_qty'], $detail['remark'])) {
//                        throw new \Exception("Invalid input data for detail ID {$detailId}");
//                    }
//
//                    $stockDetail = self::findOne($detailId);
//                    if (!$stockDetail) {
//                        throw new \Exception("Stock detail ID {$detailId} not found");
//                    }
//
//                    $totalDispatchedQty = StockDispatchTrial::find()->where(['stock_outbound_details_id' => $stockDetail->id])->sum('dispatch_qty') ?? 0;
//                    $newDispatchQty = $totalDispatchedQty + $detail['dispatch_qty'];
//                    if ($newDispatchQty > $stockDetail->qty) {
//                        throw new \Exception("Total dispatch quantity exceeds assigned quantity for detail ID {$stockDetail->id}");
//                    }
//
////                    $stockDetail->unacknowledged_qty = ($stockDetail->unacknowledged_qty + $newDispatchQty);
////                    $this->updateStockDetailDispatch($stockDetail, $detail['dispatch_qty']);
//                    $this->createDispatchTrial($detail['dispatch_qty'], $detail['remark'], $actionType, $stockDetail->id, $dispatchMasterId, $postData['current_sts']);
//                    $this->updateAllQtyInStockDetail($stockDetail);
//                }
//            }
//
//            $transaction->commit();
//            return true;
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            throw $e;
//            return false;
//        }
//    }

    public function processStockDispatch($bomMaster, $productionPanelId, $postData, $actionType) {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            foreach ((isset($postData['dispatch']) ? $postData['dispatch'] : $postData) as $key => $data) {

                $dispatchMasterId = $this->createDispatchMaster(
                        $bomMaster,
                        $productionPanelId,
                        $postData,
                        $actionType
                );

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

                    $totalAcknowledged = StockDispatchTrial::find()
                                    ->where([
                                        'stock_outbound_details_id' => $stockDetail->id,
                                        'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                                    ])
                                    ->sum('dispatch_qty') ?? 0;

                    $totalPendingPositive = StockDispatchTrial::find()
                                    ->where([
                                        'stock_outbound_details_id' => $stockDetail->id,
                                        'current_sts' => StockDispatchMaster::TO_BE_ACKNOWLEDGED
                                    ])
                                    ->andWhere(['>', 'dispatch_qty', 0])
                                    ->sum('dispatch_qty') ?? 0;

                    if ($dispatchQty > 0) {

                        $effectiveCommitted = $totalAcknowledged + $totalPendingPositive;

                        $newTotal = $effectiveCommitted + $dispatchQty;

                        if ($newTotal > $stockDetail->qty) {
                            throw new \Exception(
                                            "Dispatch exceeds assigned quantity for detail ID {$detailId}"
                                    );
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

                    $this->updateAllQtyInStockDetail($stockDetail);
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function processStockAdjustment($postData, $dispatchMaster, $actionType) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!isset($postData['dispatch_id'], $postData['dispatch_qty'], $postData['remark'])) {
                throw new \Exception("Invalid input data");
            }

            // Calculate the adjustment needed
            $newDispatchQty = $postData['dispatch_qty'] - $dispatchMaster->total_trial_dispatch_qty;
            $newTotalDispatchQty = $postData['dispatch_qty'];

            if ($newTotalDispatchQty > $this->qty) {
                throw new \Exception("Total dispatch quantity exceeds total stock quantity");
            }

            // Create the adjustment trial
            $this->createDispatchTrial(
                    $newDispatchQty,
                    $postData['remark'],
                    $actionType,
                    $this->id,
                    $postData['dispatch_id'],
                    StockDispatchMaster::TO_BE_ACKNOWLEDGED
            );

            $dispatchMasterSts = StockDispatchMaster::findOne($postData['dispatch_id']);
            $dispatchMasterSts->updateDispatchMasterStatus();

            // Recalculate all quantities
            $this->updateAllQtyInStockDetail($this);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err($e->getMessage());
            return false;
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

//            $this->updateStockDetailReturn($this, $postData);
//            $stockDetail->unacknowledged_qty = $stockDetail->unacknowledged_qty + $postData['dispatch_qty'];
            $this->createDispatchTrial($newDispatchQty, $postData['remark'], $actionType, $this->id, $postData['dispatch_id'], StockDispatchMaster::TO_BE_ACKNOWLEDGED);
            $dispatchMasterSts = StockDispatchMaster::findOne($postData['dispatch_id']);
            $dispatchMasterSts->updateDispatchMasterStatus();
            $this->updateAllQtyInStockDetail($this);
            // Revert inventory ONLY if stock available tracking exists
//            if ($this->qty_stock_available !== null) {
//                $this->revertInventoryStockQty($postData['dispatch_qty'], $this);
//            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err($e->getMessage());
            return false;
        }
    }

    public function updateAllQtyInStockDetail($stockDetail, $referenceType = 'bomstockoutbound') {

        /*
          |--------------------------------------------------------------------------
          | 1️⃣ Acknowledged (PHYSICAL movement only)
          |--------------------------------------------------------------------------
         */
        $totalAcknowledged = StockDispatchTrial::find()
                        ->where([
                            'stock_outbound_details_id' => $stockDetail->id,
                            'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                        ])
                        ->sum('dispatch_qty') ?? 0;

        /*
          |--------------------------------------------------------------------------
          | 2️⃣ Unacknowledged (display purpose only)
          |--------------------------------------------------------------------------
         */
        $totalUnacknowledged = StockDispatchTrial::find()
                        ->where(['stock_outbound_details_id' => $stockDetail->id])
                        ->andWhere([
                            'in',
                            'current_sts',
                            [
                                StockDispatchMaster::TO_BE_ACKNOWLEDGED,
                                StockDispatchMaster::TO_BE_COLLECTED
                            ]
                        ])
                        ->sum('dispatch_qty') ?? 0;

        /*
          |--------------------------------------------------------------------------
          | 3️⃣ Get allocated qty from inventory
          |--------------------------------------------------------------------------
         */
        $allocateQty = InventoryStockoutbound::find()
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

        if (!$stockDetail->save()) {
            throw new \Exception("Failed to update stock detail qty: " . json_encode($stockDetail->errors));
        }
    }

//    public function updateStockMasterStatus($productionPanelId) {
//        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
//        foreach ($stockMasters as $stockMaster) {
//            $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
//            $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
//            if (!$stockMaster->save(false)) {
//                throw new \Exception("Failed to update stock master status: " . json_encode($stockMaster->errors));
//            }
//        }
//    }

    public function updateStockMasterStatus($productionPanelId) {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $stockMasters = StockOutboundMaster::find()
                    ->where(['production_panel_id' => $productionPanelId])
                    ->all();

            foreach ($stockMasters as $stockMaster) {

                $hasPendingDispatch = StockOutboundDetails::find()
                        ->where([
                            'stock_outbound_master_id' => $stockMaster->id,
                            'fully_dispatch_status' => 0
                        ])
                        ->exists();

                $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;

                if (!$stockMaster->save(false)) {
                    throw new \Exception(
                                    "Failed to update stock master status: " . json_encode($stockMaster->errors)
                            );
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {

            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Update a stock detail with the new dispatch quantity.
     * before inventory module
     */
//    private function updateStockDetailDispatch($stockDetail, $newDispatchQty) {
//        $stockDetail->unacknowledged_qty = ($stockDetail->unacknowledged_qty + $newDispatchQty);
//        $stockDetail->fully_dispatch_status = ($stockDetail->qty == $stockDetail->dispatched_qty) ? 1 : 0;
//        $stockDetail->qty_stock_available = ($stockDetail->qty_stock_available === null ? null : ($stockDetail->qty_stock_available - $newDispatchQty));
//
//        if (!$stockDetail->save()) {
//            throw new \Exception("Failed to save stock detail: " . json_encode($stockDetail->errors));
//        }
//    }
//before inventory module
//    private function updateStockDetailAdjust($stockDetail, $postData) {
//        $totalDispatched = StockDispatchTrial::find()->where(['stock_outbound_details_id' => $stockDetail->id, 'stock_dispatch_master_id' => $postData['dispatch_id'], 'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED])->sum('dispatch_qty');
//        if ($totalDispatched > $postData['dispatch_qty']) {
//            $newQty = $totalDispatched - $postData['dispatch_qty'];
//            $stockDetail->dispatched_qty = $stockDetail->dispatched_qty - $totalDispatched + ($postData['dispatch_qty'] - $newQty);
//            $stockDetail->unacknowledged_qty = $stockDetail->unacknowledged_qty + $newQty;
//        } else {
//            $newQty = $postData['dispatch_qty'] - $totalDispatched;
//            $stockDetail->unacknowledged_qty = $stockDetail->unacknowledged_qty + $newQty;
//        }
//
//        $stockDetail->fully_dispatch_status = ($stockDetail->qty == $stockDetail->dispatched_qty) ? 1 : 0;
//        if (!$stockDetail->save()) {
//            throw new \Exception("Failed to save stock detail: " . json_encode($stockDetail->errors));
//        }
//    }
    //before inventory module
//    private function updateStockDetailReturn($stockDetail, $postData) {
//        $totalDispatched = StockDispatchTrial::find()->where(['stock_outbound_details_id' => $stockDetail->id, 'stock_dispatch_master_id' => $postData['dispatch_id'], 'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED])->sum('dispatch_qty');
//        if ($totalDispatched < $postData['dispatch_qty']) {
//            throw new \Exception("Return quantity exceeds total dispatched quantity");
//        } else {
//            $stockDetail->dispatched_qty = $stockDetail->dispatched_qty - $postData['dispatch_qty'];
//            $stockDetail->unacknowledged_qty = $stockDetail->unacknowledged_qty + $postData['dispatch_qty'];
//            $stockDetail->fully_dispatch_status = ($stockDetail->qty == $stockDetail->dispatched_qty) ? 1 : 0;
//            if (!$stockDetail->save()) {
//                throw new \Exception("Failed to save stock detail: " . json_encode($stockDetail->errors));
//            }
//        }
//    }

    private function createDispatchMaster($bomMaster, $productionPanelId, $postData, $actionType) {
        $runningNo = StockDispatchMaster::find()->where(['production_panel_id' => $productionPanelId])->count() + 1;
        $panelCode = $bomMaster->productionPanel->project_production_panel_code;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $dispatchNo = $panelCode . "-" . $runningNo;
        if ($postData['receiver']['id'] !== null || ($actionType !== StockDispatchTrial::ADJUST_STATUS && $actionType !== StockDispatchTrial::RETURN_STATUS)) {
            $dispatchMaster = new StockDispatchMaster();
            $dispatchMaster->dispatch_no = $dispatchNo;
            $dispatchMaster->production_panel_id = $productionPanelId;
            $dispatchMaster->received_by = $postData['receiver']['id'];
            $dispatchMaster->status = $postData['current_sts'];
            $dispatchMaster->save();
            return $dispatchMaster->id;
        } else {
            return null;
        }
    }

    /**
     * Create a new dispatch trial record.
     */
    private function createDispatchTrial($newDispatchQty, $remark, $actionType, $stockDetailId, $dispatchMasterId, $current_sts) {
        $dispatchTrial = new StockDispatchTrial();
        $dispatchTrial->stock_outbound_details_id = $stockDetailId;
        $dispatchTrial->stock_dispatch_master_id = $dispatchMasterId;
        $dispatchTrial->dispatch_qty = $newDispatchQty;
        $dispatchTrial->status = $actionType;
        $dispatchTrial->remark = $remark;
        $dispatchTrial->current_sts = $current_sts;

        if (!$dispatchTrial->save()) {
            throw new \Exception("Failed to save dispatch trial: " . json_encode($dispatchTrial->errors));
        }
    }

    public function revertInventoryStockQty($revertQty, $stockDetail) {
        $remainingQty = abs((float) $revertQty);

        // Find all inventory outbound records in reverse order (LIFO for reversion)
        $inventoryStockOutboundList = \frontend\models\inventory\InventoryStockoutbound::find()
                ->where([
                    'reference_type' => 'bomstockoutbound',
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

    public function updateInventoryStockQty($dispatchQty, $stockDetail) {
        $remainingQty = (float) $dispatchQty;
        $inventoryStockOutboundList = \frontend\models\inventory\InventoryStockoutbound::find()
                ->where([
                    'reference_type' => 'bomstockoutbound',
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

    public function isLegacyRecord() {
        return (!empty($this->model_type) || !empty($this->brand)) &&
                empty($this->inventory_model_id) &&
                empty($this->inventory_brand_id);
    }
}
