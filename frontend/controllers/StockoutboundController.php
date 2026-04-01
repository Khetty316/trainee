<?php

namespace frontend\controllers;

use Yii;
use frontend\models\bom\StockOutboundMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\bom\BomMaster;
use frontend\models\bom\StockOutboundDetails;
use frontend\models\bom\StockDispatchTrial;
use frontend\models\bom\VStockDispatchMasterSearch;
use frontend\models\bom\StockDispatchMaster;
use frontend\models\bom\VStockDispatchMaster;
use common\models\myTools\FlashHandler;
use frontend\models\bom\StockDispatchMasterSearch;
use common\modules\auth\models\AuthItem;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryDetail;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryOrderRequest;
use frontend\models\inventory\InventoryStockoutbound;
use frontend\models\inventory\InventoryReserveItem;

/**
 * StockoutboundController implements the CRUD actions for StockOutboundMaster model.
 */
class StockoutboundController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['@']
//                    ],
                    [
                        'actions' => ['index', 'view-panels', 'view-bom', 'update-stock-dispatch'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_Stock_Ob_View],
                    ],
                    [
                        'actions' => ['view-inventory-stockoutbound-detail', 'outbound-finalized-item', 'inventory-validation', 'initiate-outbound-master', 'view-material-detail', 'add-material', 'deactivate-item', 'update-item-detail', 'dispatch-item-list', 'confirm-stock-dispatch', 'stock-return', 'return-dispatched-quantity'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal],
                    ],
                    [
                        'actions' => ['stock-adjustment', 'adjust-dispatch-quantity'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new \frontend\models\ProjectProduction\ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id) {
        if (($model = StockOutboundMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewPanels($id) {
        $model = \frontend\models\ProjectProduction\ProjectProductionMaster::findOne($id);
        $result = \frontend\models\projectproduction\VProjectProductionPanels::find()->where(['proj_prod_master' => $id])->all();
        return $this->render('viewPanels', [
                    'model' => $model,
                    'panelLists' => $result,
        ]);
    }

//    public function actionInitiateOutboundMaster($productionPanelId) {
//        if (Yii::$app->request->post()) {
//            $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
//            if ($bomMaster) {
//                for ($i = 1; $i <= $bomMaster->productionPanel->quantity; $i++) {
//                    $stockOutboundMaster = new StockOutboundMaster();
//                    $stockOutboundMaster->production_panel_id = $bomMaster->production_panel_id;
//                    $stockOutboundMaster->bom_master_id = $bomMaster->id;
//                    $stockOutboundMaster->order = $i;
//                    if ($stockOutboundMaster->save()) {
//                        $stockOutboundMaster->copyDetail($bomMaster->bomDetails);
//                    }
//                }
//            }
//
//            return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
//        }
//    }
    // 10/2/2026
    public function actionOutboundFinalizedItem() {
// Get and validate selected items
        $productionPanelId = Yii::$app->request->post('productionPanelId');
        $ids = Yii::$app->request->post('ids', []);
        // Only allow POST requests
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['/bom/index', 'productionPanelId' => $productionPanelId]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Validate BOM Master exists
            $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
            $userId = $bomMaster->productionPanel->projProdMaster->created_by;

            if (!$bomMaster) {
                throw new \Exception('BOM Master not found for the specified production panel.');
            }


            if (empty($ids)) {
                throw new \Exception('No items selected. Please select at least one item to process.');
            }

            $selectedItems = \frontend\models\bom\BomDetails::find()->where(['id' => $ids, 'active_status' => 1])->all();
            if (empty($selectedItems)) {
                throw new \Exception('No valid active items found for the selected IDs.');
            }

            // Check if outbound masters already exist
            $stockOutboundMasters = StockOutboundMaster::find()->where(['bom_master_id' => $bomMaster->id])->all();
            if (!empty($stockOutboundMasters)) {
                // Update existing outbound masters with selected items
                foreach ($stockOutboundMasters as $stockMaster) {
                    // Copy/update selected items
                    if (!$stockMaster->copyDetail($selectedItems)) {
                        throw new \Exception("Failed to copy details for stock outbound master {$stockMaster->id}");
                    }

                    // Get the outbound details that were just created/updated for selected items
                    $selectedBomDetailIds = array_map(function ($item) {
                        return $item->id;
                    }, $selectedItems);

                    $outboundDetails = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'bom_detail_id' => $selectedBomDetailIds])->all();

                    // Process inventory for each detail
                    foreach ($outboundDetails as $detail) {
                        InventoryOrderRequest::processInventoryItem($detail, $detail->qty, $userId, 'bomstockoutbound', $detail->bom_detail_id);
                    }
                }

                $message = "Successfully updated " . count($selectedItems) . " item(s) for " . count($stockOutboundMasters) . " panel(s).";
            } else {
                // Create new outbound masters
                $totalPanels = $bomMaster->productionPanel->quantity;

                if ($totalPanels <= 0) {
                    throw new \Exception('Invalid total panels quantity: ' . $totalPanels);
                }

                for ($i = 1; $i <= $totalPanels; $i++) {
                    // Create stock outbound master
                    $stockOutboundMaster = new StockOutboundMaster();
                    $stockOutboundMaster->production_panel_id = $bomMaster->production_panel_id;
                    $stockOutboundMaster->bom_master_id = $bomMaster->id;
                    $stockOutboundMaster->order = $i;

                    if (!$stockOutboundMaster->save()) {
                        $errors = json_encode($stockOutboundMaster->getFirstErrors());
                        throw new \Exception("Failed to save StockOutboundMaster for panel {$i}: {$errors}");
                    }

                    // Copy selected BOM details to outbound details
                    if (!$stockOutboundMaster->copyDetail($selectedItems)) {
                        throw new \Exception("Failed to copy details for panel {$i}");
                    }

                    // Get the created outbound details
                    $selectedBomDetailIds = array_map(function ($item) {
                        return $item->id;
                    }, $selectedItems);

                    $outboundDetails = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockOutboundMaster->id, 'bom_detail_id' => $selectedBomDetailIds])->all();

                    // Process inventory for each detail
                    foreach ($outboundDetails as $detail) {
                        InventoryOrderRequest::processInventoryItem($detail, $detail->qty, $userId, 'bomstockoutbound', $detail->bom_detail_id);
                    }
                }

                $message = "Successfully initiated outbound for {$totalPanels} panel(s) with " . count($selectedItems) . " item(s).";
            }

            // Commit transaction
            $transaction->commit();
            Yii::$app->session->setFlash('success', $message);

            return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
        } catch (\Throwable $e) {
            // Rollback on any error
            $transaction->rollBack();

            Yii::$app->session->setFlash('error', 'Failed to initiate outbound: ' . $e->getMessage() . ' All changes have been rolled back.');

            // Redirect appropriately
            if (isset($bomMaster) && $bomMaster) {
                return $this->redirect(['bom/index', 'productionPanelId' => $bomMaster->productionPanel->id]);
            }

            return $this->redirect(['/bom/index', 'productionPanelId' => $productionPanelId]);
        }
    }

    /**
     * Check inventory before initiating outbound
     */
    public function actionInventoryValidation($productionPanelId) {
        if (Yii::$app->request->post()) {
            $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);

            if (!$bomMaster) {
                Yii::$app->session->setFlash('error', 'BOM Master not found.');
                return $this->redirect(['view-panels', 'id' => $production->id]);
            }

            $items = $bomMaster->bomDetails;
            $validationResults = [];
            $hasErrors = false;
            $itemsToUpdate = [];

            // Check each item
            foreach ($items as $item) {
                $result = $this->validateItemForOutbound($item, $bomMaster);
                $validationResults[] = $result;

                if ($result['status'] === 'error') {
                    $hasErrors = true;
                }

                if (isset($result['found_model_id']) && isset($result['found_brand_id'])) {
                    $itemsToUpdate[] = [
                        'item' => $item,
                        'modelId' => $result['found_model_id'],
                        'brandId' => $result['found_brand_id']
                    ];
                }
            }

            // Prepare data for view
            $data = [
                'bomMaster' => $bomMaster,
                'validationResults' => $validationResults,
                'hasErrors' => $hasErrors,
                'itemsToUpdate' => $itemsToUpdate,
                'productionPanelId' => $productionPanelId,
            ];

            return $this->render('inventoryValidationForm', $data);
        }

        // If not POST, redirect back
        return $this->redirect(['view-panels', 'id' => $production->id]);
    }

    /**
     * Update items and initiate outbound (combined)
     */
//    public function actionUpdateAndInitiate($productionPanelId) {
//        $transaction = Yii::$app->db->beginTransaction();
//
//        try {
//            $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
//
//            if (!$bomMaster) {
//                throw new \Exception('BOM Master not found.');
//            }
//
//            // Get update items from POST
//            $updateItems = Yii::$app->request->post('updateItems', []);
//
//            // Update items that need linking
//            foreach ($updateItems as $update) {
//                if (isset($update['itemId']) && isset($update['modelId']) && isset($update['brandId'])) {
//                    $item = BomDetails::findOne($update['itemId']);
//                    if ($item) {
//                        $item->inventory_model_id = $update['modelId'];
//                        $item->inventory_brand_id = $update['brandId'];
//
//                        if (!$item->save()) {
//                            throw new \Exception("Failed to update item {$item->id}: " . json_encode($item->errors));
//                        }
//                    }
//                }
//            }
//
//            // Now initiate outbound (directly in same transaction)
//            $totalPanels = $bomMaster->productionPanel->quantity;
//
//            for ($i = 1; $i <= $totalPanels; $i++) {
//                // Create stock outbound master
//                $stockOutboundMaster = new StockOutboundMaster();
//                $stockOutboundMaster->production_panel_id = $bomMaster->production_panel_id;
//                $stockOutboundMaster->bom_master_id = $bomMaster->id;
//                $stockOutboundMaster->order = $i;
//
//                if (!$stockOutboundMaster->save()) {
//                    throw new \Exception("Failed to save StockOutboundMaster for panel {$i}: " .
//                                    json_encode($stockOutboundMaster->errors));
//                }
//
//                // Copy BOM details to outbound details
//                if (!$stockOutboundMaster->copyDetail($bomMaster->bomDetails)) {
//                    throw new \Exception("Failed to copy details for panel {$i}");
//                }
//
//                // Process each item in the outbound details
//                $items = StockOutboundDetails::findAll(['stock_outbound_master_id' => $stockOutboundMaster->id]);
//                foreach ($items as $item) {
//                    $this->processInventoryItem($item);
//                }
//            }
//
//            // If we reach here, everything was successful - COMMIT
//            $transaction->commit();
//
//            Yii::$app->session->setFlash('success', "Successfully updated items and initiated outbound for {$totalPanels} panels.");
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            Yii::$app->session->setFlash('error', 'Failed: ' . $e->getMessage() . ' All changes have been rolled back.');
//            return $this->redirect(['check-item-before-initiation', 'productionPanelId' => $productionPanelId]);
//        }
//
//        return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
//    }

    /**
     * Direct outbound initiation (without updating items)
     */
//    public function actionInitiateOutboundMaster($productionPanelId) {
//        if (Yii::$app->request->post()) {
//            $transaction = Yii::$app->db->beginTransaction();
//
//            try {
//                $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
//
//                if (!$bomMaster) {
//                    throw new \Exception('BOM Master not found.');
//                }
//
//                $totalPanels = $bomMaster->productionPanel->quantity;
//
//                for ($i = 1; $i <= $totalPanels; $i++) {
//                    // Create stock outbound master
//                    $stockOutboundMaster = new StockOutboundMaster();
//                    $stockOutboundMaster->production_panel_id = $bomMaster->production_panel_id;
//                    $stockOutboundMaster->bom_master_id = $bomMaster->id;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
//                    $stockOutboundMaster->order = $i;
//
//                    if (!$stockOutboundMaster->save()) {
//                        throw new \Exception("Failed to save StockOutboundMaster for panel {$i}: " .
//                                        json_encode($stockOutboundMaster->errors));
//                    }
//
//                    // Copy BOM details to outbound details
//                    if (!$stockOutboundMaster->copyDetail($bomMaster->bomDetails)) {
//                        throw new \Exception("Failed to copy details for panel {$i}");
//                    }
//
//                    // Process each item in the outbound details
//                    $items = StockOutboundDetails::findAll(['stock_outbound_master_id' => $stockOutboundMaster->id]);
//                    foreach ($items as $item) {
//                        $this->processInventoryItem($item);
//                    }
//                }
//
//                // If we reach here, everything was successful - COMMIT
//                $transaction->commit();
//
//                Yii::$app->session->setFlash('success', "Successfully initiated outbound for {$totalPanels} panels.");
//            } catch (\Exception $e) {
//                // ANY error occurs - ROLLBACK EVERYTHING
//                $transaction->rollBack();
//
//                Yii::$app->session->setFlash('error', 'Failed to initiate outbound: ' . $e->getMessage() . ' All changes have been rolled back.');
//
//                // Return to previous page
//                if (isset($bomMaster) && $bomMaster) {
//                    return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
//                } else {
//                    return $this->goBack();
//                }
//            }
//
//            return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
//        } else {
//            // If accessed directly without POST, redirect back
//            return $this->redirect(['check-item-before-initiation', 'productionPanelId' => $productionPanelId]);
//        }
//    }

    /**
     * Validate a single BOM detail item for outbound
     */
    private function validateItemForOutbound($item, $bomMaster = null) {
        $result = [
            'item' => $item,
            'status' => 'success',
            'message' => '',
            'inventory_model_id' => $item->inventory_model_id,
            'inventory_brand_id' => $item->inventory_brand_id,
            'model_type' => $item->model_type,
            'brand' => $item->brand,
            'qty' => $item->qty,
            'description' => $item->description,
        ];

        // Case 1: Already have inventory_model_id and inventory_brand_id
        if ($item->inventory_model_id && $item->inventory_brand_id) {
            $inventoryModel = InventoryModel::find()
                    ->where(['id' => $item->inventory_model_id])
                    ->andWhere(['inventory_brand_id' => $item->inventory_brand_id])
                    ->andWhere(['active_sts' => 2])
                    ->one();

            if (!$inventoryModel) {
                $result['status'] = 'error';
                $result['message'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Item not found in inventory</span>';
            } else {
                // Get the brand separately to ensure it exists
                $inventoryBrand = InventoryBrand::findOne($item->inventory_brand_id);
                if (!$inventoryBrand) {
                    $result['status'] = 'error';
                    $result['message'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Brand not found in inventory</span>';
                } else {
                    $result['message'] = '<span class="text-success"><i class="fas fa-check-circle"></i> Linked to inventory</span>';
                    $result['inventory_model'] = $inventoryModel;
                    $result['inventory_brand'] = $inventoryBrand;
                }
            }
        }
        // Case 2: Try to find by model_type and brand
        else if ($item->model_type && $item->brand) {
            $inventoryModel = InventoryModel::find()
                    ->where(['type' => $item->model_type])
                    ->andWhere(['active_sts' => 2])
                    ->one();

            if ($inventoryModel) {
                // Check if brand matches
                if ($inventoryModel->inventoryBrand && strcasecmp($inventoryModel->inventoryBrand->name, $item->brand) === 0) {

                    $result['status'] = 'warning';
                    $result['message'] = '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Found in inventory - will be linked</span>';
                    $result['found_model_id'] = $inventoryModel->id;
                    $result['found_brand_id'] = $inventoryModel->inventory_brand_id;
                    $result['found_model_name'] = $inventoryModel->type;
                    $result['found_brand_name'] = $inventoryModel->inventoryBrand->name;
                    $result['inventory_model'] = $inventoryModel;
                    $result['inventory_brand'] = $inventoryModel->inventoryBrand;
                } else {
                    $result['status'] = 'error';
                    $result['message'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Model exists but brand doesn\'t match. Found: "' .
                            ($inventoryModel->inventoryBrand ? $inventoryModel->inventoryBrand->name : 'N/A') . '"</span>';
                }
            } else {
                $result['status'] = 'error';
                $result['message'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Model not found in inventory</span>';
            }
        }
        // Case 3: No data at all
        else {
            $result['status'] = 'error';
            $result['message'] = '<span class="text-danger"><i class="fas fa-times-circle"></i> Missing model type and brand information</span>';
        }

        return $result;
    }

    /**
     * Process inventory for a single outbound detail item
     * Returns true on success, false on failure (will trigger rollback)
     */
//    private function processInventoryItem(StockOutboundDetails $item, $userId) {
//        try {
//            if (!$item->inventory_model_id || !$item->inventory_brand_id) {
//                throw new \Exception("Item {$item->id}: No inventory model/brand specified");
//            }
//
//            $requiredQty = $item->qty;
//            $allocatedQty = 0;
//
//            // STEP 1: Check for existing reserved items for this user
//            $existingReservesGenaral = InventoryReserveItem::find()
//                    ->alias('iri')
//                    ->innerJoin('inventory_detail id', 'id.id = iri.inventory_detail_id')
//                    ->where([
//                        'iri.user_id' => $userId,
//                        'iri.reference_type' => null, 
//                        'iri.reference_id' => null,
//                        'id.model_id' => $item->inventory_model_id,
//                        'id.brand_id' => $item->inventory_brand_id,
//                        'iri.status' => 1, // Active/available reserves
//                    ])
//                    ->andWhere(['>', 'iri.available_qty', 0])
//                    ->orderBy(['iri.created_at' => SORT_ASC]) // FIFO - oldest reserves first
//                    ->all();
//            
//            $existingReservesByBom = InventoryReserveItem::find()
//                    ->alias('iri')
//                    ->innerJoin('inventory_detail id', 'id.id = iri.inventory_detail_id')
//                    ->where([
//                        'iri.user_id' => $userId,
//                        'iri.reference_type' => "bom_detail", // use "bom_detail for stockoutbound module
//                        'iri.reference_id' => $item->bom_detail_id,
//                        'id.model_id' => $item->inventory_model_id,
//                        'id.brand_id' => $item->inventory_brand_id,
//                        'iri.status' => 1, // Active/available reserves
//                    ])
//                    ->andWhere(['>', 'iri.available_qty', 0])
//                    ->orderBy(['iri.created_at' => SORT_ASC]) // FIFO - oldest reserves first
//                    ->all();
//
//            // STEP 2: Allocate from existing reserves first
//            foreach ($existingReserves as $reserve) {
//                if ($allocatedQty >= $requiredQty) {
//                    break;
//                }
//
//                $availableReserveQty = $reserve->available_qty;
//                $qtyToAllocate = min($availableReserveQty, $requiredQty - $allocatedQty);
//
//                if ($qtyToAllocate > 0) {
//                    // Update reserve item quantities
//                    $reserve->dispatched_qty += $qtyToAllocate;
//                    $reserve->available_qty -= $qtyToAllocate;
//
//                    if (!$reserve->save()) {
//                        throw new \Exception("Failed to update reserve item {$reserve->id}: " . json_encode($reserve->errors));
//                    }
//
//                    // Get the inventory detail
//                    $inventoryDetail = InventoryDetail::findOne($reserve->inventory_detail_id);
//
//                    if (!$inventoryDetail) {
//                        throw new \Exception("Inventory detail {$reserve->inventory_detail_id} not found");
//                    }
//
//                    // Create stock outbound record
//                    // reference_type = 3 (bomstockoutbound) - tracks the CONSUMER
//                    // reference_id = stock_outbound_details.id - the consuming record
//                    // reserve_item_id = inventory_reserve_item.id - tracks the SOURCE
//                    $inventoryStockOutbound = new InventoryStockoutbound();
//                    $inventoryStockOutbound->inventory_detail_id = $inventoryDetail->id;
//                    $inventoryStockOutbound->reference_type = "bomstockoutbound";
//                    $inventoryStockOutbound->reference_id = $item->id; // Link to stock_outbound_details (consumer)
//                    $inventoryStockOutbound->reserve_item_id = $reserve->id; // Link to inventory_reserve_item (source)
//                    $inventoryStockOutbound->qty = $qtyToAllocate;
//
//                    if (!$inventoryStockOutbound->save()) {
//                        throw new \Exception("Failed to save InventoryStockoutbound: " . json_encode($inventoryStockOutbound->errors));
//                    }
//
//                    // Update inventory detail - reserve the stock
//                    $inventoryDetail->stock_reserved += $qtyToAllocate;
//                    $inventoryDetail->stock_available -= $qtyToAllocate;
//
//                    if (!$inventoryDetail->save()) {
//                        throw new \Exception("Failed to update inventory detail {$inventoryDetail->id}: " . json_encode($inventoryDetail->errors));
//                    }
//
//                    $allocatedQty += $qtyToAllocate;
//
//                    // Update item's ready to dispatch quantity
//                    $item->qty_stock_available = ($item->qty_stock_available ?? 0) + $qtyToAllocate;
//
//                    Yii::info("Allocated {$qtyToAllocate} from reserve item {$reserve->id} (inventory_detail {$inventoryDetail->id}) for BOM outbound detail {$item->id}");
//                }
//            }
//
//            // STEP 3: If still need more, allocate from general available stock
//            if ($allocatedQty < $requiredQty) {
//                $remainingQty = $requiredQty - $allocatedQty;
//
//                // Find available inventory details for this model and brand
//                $inventoryDetails = InventoryDetail::find()
//                        ->where([
//                            'model_id' => $item->inventory_model_id,
//                            'brand_id' => $item->inventory_brand_id,
//                            'active_sts' => 2
//                        ])
//                        ->andWhere(['>', 'stock_available', 0])
//                        ->orderBy([
//                            'stock_available' => SORT_DESC, // Allocate from highest available stock first
//                            'created_at' => SORT_ASC // Then by oldest stock (FIFO)
//                        ])
//                        ->all();
//
//                // Allocate stock from available suppliers
//                foreach ($inventoryDetails as $inventoryDetail) {
//                    if ($allocatedQty >= $requiredQty) {
//                        break;
//                    }
//
//                    $availableQty = $inventoryDetail->stock_available;
//
//                    if ($availableQty > 0) {
//                        $qtyToAllocate = min($availableQty, $requiredQty - $allocatedQty);
//
//                        if ($qtyToAllocate > 0) {
//                            // Calculate new values
//                            $newStockReserved = $inventoryDetail->stock_reserved + $qtyToAllocate;
//                            $newStockAvailable = $inventoryDetail->stock_available - $qtyToAllocate;
//
//                            // Validate: stock_available should not go negative
//                            if ($newStockAvailable < 0) {
//                                throw new \Exception("Insufficient stock available for inventory detail {$inventoryDetail->id}");
//                            }
//
//                            // Create stock outbound record
//                            // reference_type = 3 (bomstockoutbound) - tracks the CONSUMER
//                            // reference_id = stock_outbound_details.id - the consuming record
//                            // reserve_item_id = NULL - indicates general stock (not from reserve)
//                            $inventoryStockOutbound = new InventoryStockoutbound();
//                            $inventoryStockOutbound->inventory_detail_id = $inventoryDetail->id;
//                            $inventoryStockOutbound->reference_type = "bomstockoutbound";
//                            $inventoryStockOutbound->reference_id = $item->id; // Link to stock_outbound_details (consumer)
//                            $inventoryStockOutbound->reserve_item_id = NULL; // NULL = general stock (source)
//                            $inventoryStockOutbound->qty = $qtyToAllocate;
//
//                            if (!$inventoryStockOutbound->save()) {
//                                throw new \Exception("Failed to save InventoryStockoutbound: " . json_encode($inventoryStockOutbound->errors));
//                            }
//
//                            // UPDATE: Reserve the stock
//                            $inventoryDetail->stock_reserved = $newStockReserved;
//                            $inventoryDetail->stock_available = $newStockAvailable;
//
//                            if (!$inventoryDetail->save()) {
//                                throw new \Exception("Failed to update inventory detail {$inventoryDetail->id}: " . json_encode($inventoryDetail->errors));
//                            }
//
//                            $allocatedQty += $qtyToAllocate;
//
//                            // Update item's ready to dispatch quantity
//                            $item->qty_stock_available = ($item->qty_stock_available ?? 0) + $qtyToAllocate;
//
//                            Yii::info("Allocated {$qtyToAllocate} from general stock (inventory detail {$inventoryDetail->id}) for BOM outbound detail {$item->id}");
//                        }
//                    }
//                }
//            }
//
//            // Save updated item quantities
//            if (!$item->save()) {
//                throw new \Exception("Failed to update item {$item->id}: " . json_encode($item->errors));
//            }
//
//            // STEP 4: Check if we need to create order request
//            if ($allocatedQty < $requiredQty) {
//                $balanceQty = $requiredQty - $allocatedQty;
//
//                if (!$this->createOrderRequest($item, $balanceQty)) {
//                    throw new \Exception("Failed to create order request for item {$item->id}");
//                }
//
//                Yii::info("Created order request for {$balanceQty} units for BOM outbound detail {$item->id}");
//            }
//
//            Yii::info("Successfully processed inventory item {$item->id}: allocated {$allocatedQty}/{$requiredQty} (from reserve + general stock)");
//
//            return true;
//        } catch (\Exception $e) {
//            // Log and re-throw - this will bubble up to the main transaction
//            Yii::error("processInventoryItem failed for item {$item->id}: " . $e->getMessage());
//            throw $e;
//        }
//    }
    //unused
//    private function processInventoryItem(StockOutboundDetails $item, $userId) {
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
//            $allocatedQty += $this->allocateFromReserve($item, $userId, 'bom_detail', $item->bom_detail_id, $requiredQty - $allocatedQty);
//
//            // then allocate from general reserves
//            if ($allocatedQty < $requiredQty) {
//                $allocatedQty += $this->allocateFromReserve($item, $userId, 'reserve', $userId, $requiredQty - $allocatedQty);
//            }
//
//            // then allocate from general stock
//            if ($allocatedQty < $requiredQty) {
//                $allocatedQty += $this->allocateFromGeneralStock($item, $requiredQty - $allocatedQty);
//            }
//
//            // Save updated item
//            if (!$item->save()) {
//                throw new \Exception("Failed to update item {$item->id}");
//            }
//
//            // 4️⃣ Create order request if still insufficient
//            if ($allocatedQty < $requiredQty) {
//                $balanceQty = $requiredQty - $allocatedQty;
//
//                if (!$this->createOrderRequest($item, $balanceQty)) {
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
//
//    private function allocateFromReserve($item, $userId, $referenceType, $referenceId, $remainingQty) {
//        if ($remainingQty <= 0) {
//            return 0;
//        }
//
//        $query = InventoryReserveItem::find()
//                ->alias('iri')
//                ->innerJoin('inventory_detail id', 'id.id = iri.inventory_detail_id')
//                ->where([
//                    'iri.user_id' => $userId,
//                    'iri.reference_type' => $referenceType,
//                    'iri.reference_id' => $referenceId,
//                    'id.model_id' => $item->inventory_model_id,
//                    'id.brand_id' => $item->inventory_brand_id,
//                    'iri.status' => 2, //2 = active, 1 = inactive
//                ])
//                ->andWhere(['>', 'iri.available_qty', 0])
//                ->orderBy(['iri.created_at' => SORT_ASC]); // FIFO
//
//        $reserves = $query->all();
//        $allocated = 0;
//        
//        foreach ($reserves as $reserve) {
//
//            if ($allocated >= $remainingQty) {
//                break;
//            }
//
//            $qtyToAllocate = min($reserve->available_qty, $remainingQty - $allocated);
//
//            if ($qtyToAllocate <= 0) {
//                continue;
//            }
//
//            // Update reserve
//            $reserve->dispatched_qty += $qtyToAllocate;
//            $reserve->available_qty -= $qtyToAllocate;
//
//            if (!$reserve->save()) {
//                throw new \Exception("Failed updating reserve {$reserve->id}");
//            }
//
//            $inventoryDetail = InventoryDetail::findOne($reserve->inventory_detail_id);
//
//            if (!$inventoryDetail) {
//                throw new \Exception("Inventory detail not found");
//            }
//
//            $this->createStockOutbound($inventoryDetail->id, $item->id, $reserve->id, $qtyToAllocate);
//
//            $inventoryDetail->stock_reserved += $qtyToAllocate;
//            $inventoryDetail->stock_available -= $qtyToAllocate;
//
//            if (!$inventoryDetail->save()) {
//                throw new \Exception("Failed updating inventory detail {$inventoryDetail->id}");
//            }
//
//            $allocated += $qtyToAllocate;
//            $item->qty_stock_available += $qtyToAllocate;
//        }
//
//        return $allocated;
//    }
//
//    private function allocateFromGeneralStock($item, $remainingQty) {
//        if ($remainingQty <= 0) {
//            return 0;
//        }
//
//        $inventoryDetails = InventoryDetail::find()
//                ->where([
//                    'model_id' => $item->inventory_model_id,
//                    'brand_id' => $item->inventory_brand_id,
//                    'active_sts' => 2
//                ])
//                ->andWhere(['>', 'stock_available', 0])
//                ->orderBy([
//                    'stock_available' => SORT_DESC,
//                    'created_at' => SORT_ASC
//                ])
//                ->all();
//
//        $allocated = 0;
//
//        foreach ($inventoryDetails as $inventoryDetail) {
//            if ($allocated >= $remainingQty) {
//                break;
//            }
//
//            $qtyToAllocate = min($inventoryDetail->stock_available, $remainingQty - $allocated);
//
//            if ($qtyToAllocate <= 0) {
//                continue;
//            }
//
//            $this->createStockOutbound($inventoryDetail->id, $item->id, null, $qtyToAllocate);
//
//            $inventoryDetail->stock_reserved += $qtyToAllocate;
//            $inventoryDetail->stock_available -= $qtyToAllocate;
//
//            if ($inventoryDetail->stock_available < 0) {
//                throw new \Exception("Stock negative for inventory {$inventoryDetail->id}");
//            }
//
//            if (!$inventoryDetail->save()) {
//                throw new \Exception("Failed updating inventory detail {$inventoryDetail->id}");
//            }
//
//            $allocated += $qtyToAllocate;
//            $item->qty_stock_available += $qtyToAllocate;
//        }
//
//        return $allocated;
//    }
//
//    private function createStockOutbound($inventoryDetailId, $stockOutboundDetailId, $reserveItemId, $qty) {
//        $record = new InventoryStockoutbound();
//        $record->inventory_detail_id = $inventoryDetailId;
//        $record->reference_type = "bomstockoutbound";
//        $record->reference_id = $stockOutboundDetailId;
//        $record->reserve_item_id = $reserveItemId;
//        $record->qty = $qty;
//
//        if (!$record->save()) {
//            throw new \Exception("Failed to save InventoryStockoutbound");
//        }
//    }
//
//    /**
//     * Create reorder request for insufficient stock
//     * Returns true on success, false on failure (will trigger rollback)
//     */
//    private function createOrderRequest(StockOutboundDetails $item, $balanceQty) {
//        try {
//            $orderRequest = new InventoryOrderRequest();
//            $orderRequest->inventory_model_id = $item->inventory_model_id;
//            $orderRequest->inventory_brand_id = $item->inventory_brand_id;
//            $orderRequest->reference_type = 'bomstockoutbound';
//            $orderRequest->reference_id = $item->id;
//            $orderRequest->required_qty = $balanceQty;
//
//            if (!$orderRequest->save()) {
//                throw new \Exception("Failed to save order request: " . json_encode($orderRequest->errors));
//            }
//
//            return true;
//        } catch (\Exception $e) {
//            Yii::error("createOrderRequest failed: " . $e->getMessage());
//            throw $e; // Re-throw to trigger rollback
//        }
//    }

    public function actionViewBom($productionPanelId, $justCreated = false) {
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();
        if (!$bomMaster) {
            $bomMaster = new BomMaster();
            $bomMaster->production_panel_id = $productionPanelId;
            $bomMaster->save();
        }

        $searchModel = new \frontend\models\bom\bomdetailSearch();
        $params = Yii::$app->request->queryParams;
        $params['bomdetailSearch']['bom_master'] = $bomMaster->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('viewBom', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bomMaster' => $bomMaster,
                    'justCreated' => $justCreated
        ]);
    }

    public function actionViewMaterialDetail($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->joinWith('stockOutboundDetails')->all();
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);

        return $this->render('stockOutboundDetail', [
                    'stockMasters' => $stockMasters,
                    'bomMaster' => $bomMaster
        ]);
    }

    public function actionAddMaterial($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
        $model = new StockOutboundDetails();

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post('StockOutboundDetails');
            if (empty($stockMasters)) {
                FlashHandler::err("No StockOutboundMaster found for Production Panel ID: $productionPanelId");
            }

            $errors = [];
            foreach ($stockMasters as $stockMaster) {
                $newMaterial = new StockOutboundDetails();
                $newMaterial->attributes = $postData;
                $newMaterial->stock_outbound_master_id = $stockMaster->id;

                if (!$newMaterial->save()) {
                    $errors[] = "StockOutboundMaster ID {$stockMaster->id}: " . json_encode($newMaterial->errors);
                }
            }

            if (!empty($errors)) {
                FlashHandler::err("Failed to save some StockOutboundDetails: " . implode('; ', $errors));
            } else {
                FlashHandler::success('Success! The material has been added successfully.');
            }

            return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
        }

        return $this->renderAjax('_addMaterial', [
                    'model' => $model,
        ]);
    }

//    public function actionUpdateItemDetail($productionPanelId, $stockDetailId) {
//        $item = StockOutboundDetails::findOne($stockDetailId);
//        $bomDetail = $item->bomDetail;
//        if ($Yii::$app->request->post()) {
//            if (isset($postData['StockOutboundDetails'])) {
//                $stockDetails = $postData['StockOutboundDetails'];
//                $item->model_type = $stockDetails['model_type'];
//                $item->brand = $stockDetails['brand'];
//                $item->descriptions = $stockDetails['descriptions'];
//                $item->qty = $stockDetails['qty'];
//
//                if ($item->save()) {
//                    FlashHandler::success("Success! The detail has been updated successfully.");
//                } else {
//                    FlashHandler::err("Error! Failed to update the detail. " . json_encode($item->getErrors()));
//                }
//            }
//
//            return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
//        }
//
//        return $this->renderAjax('_updateItemDetail', [
//                    'item' => $item,
//                    'bomDetail' => $data
//        ]);
//    }

    public function actionUpdateItemDetail(int $productionPanelId, int $stockDetailId) {
        if ($productionPanelId <= 0 || $stockDetailId <= 0) {
            throw new \yii\web\BadRequestHttpException('Invalid parameters.');
        }

        $item = StockOutboundDetails::findOne($stockDetailId);
        if ($item === null) {
            throw new \yii\web\NotFoundHttpException('Item not found.');
        }

        $actualPanelId = $item->bomDetail->bomMaster->productionPanel->id ?? null;
        if ($actualPanelId !== $productionPanelId) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $oldQty = $item->qty;
        $oldModelId = $item->inventory_model_id;
        $oldBrandId = $item->inventory_brand_id;
        $dispatchedQty = $item->dispatched_qty + $item->unacknowledged_qty;
        $availableQty = $item->qty_stock_available;
        $userId = $item->bomDetail->bomMaster->productionPanel->projProdMaster->created_by ?? null;

        if ($userId === null) {
            throw new \yii\web\ServerErrorHttpException('Could not resolve project coordinator for this item.');
        }

        if (!$item->load(Yii::$app->request->post())) {
            return $this->renderAjax('_updateItemDetail', (new InventoryModel())->prepareFormData($item));
        }

        if (!$item->validate()) {
            Yii::$app->session->setFlash('error', 'Please fix the errors below.');
            return $this->renderAjax('_updateItemDetail', (new InventoryModel())->prepareFormData($item));
        }

        $post = Yii::$app->request->post('StockOutboundDetails', []);
        $newQty = filter_var($post['qty'] ?? 0, FILTER_VALIDATE_INT);
        $hasModelTypeInput = isset($post['model_type_input']) && $post['model_type_input'] !== null;

        if ($newQty === false || $newQty < 0) {
            Yii::$app->session->setFlash('error', 'Invalid quantity provided.');
            return $this->renderAjax('_updateItemDetail', (new InventoryModel())->prepareFormData($item));
        }

        if ($hasModelTypeInput) {

            // Compute how many additional units are needed
            if ($oldModelId === null && $oldBrandId === null) {
                $newRequiredQty = $newQty - $dispatchedQty;
            } elseif ($oldModelId !== null && $oldBrandId !== null) {
                $newRequiredQty = $newQty - $oldQty + ($oldQty - $dispatchedQty) - $availableQty;
            } else {
                // Mixed null/non-null state — reject rather than silently corrupt stock
                Yii::$app->session->setFlash('error', 'Inconsistent inventory model/brand state.');
                return $this->renderAjax('_updateItemDetail', (new InventoryModel())->prepareFormData($item));
            }

            if ($newRequiredQty > 0) {
                if (!$item->inventory_model_id || !$item->inventory_brand_id) {
                    Yii::$app->session->setFlash('error', 'No inventory model/brand specified.');
                    return $this->renderAjax('_updateItemDetail', (new InventoryModel())->prepareFormData($item));
                }

                // Wrap all mutations in a transaction — partial failures roll back cleanly
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    InventoryOrderRequest::processInventoryItem($item, $newRequiredQty, $userId, 'bom_detail', $item->id);
                    $item->updateAllQtyInStockDetail($item);
                    $item->updateStockMasterStatus($productionPanelId);
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error("actionUpdateItemDetail failed for item {$item->id}: " . $e->getMessage());
                    FlashHandler::err("An error occurred while updating the item. " . $e->getMessage());
                }

                return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
            }
        }

        try {
            if ($item->save()) {
                $item->updateAllQtyInStockDetail($item);
                $item->updateStockMasterStatus($productionPanelId);
                FlashHandler::success('The detail has been updated successfully.');
            } else {
                FlashHandler::err("Error! Failed to update the detail. " . json_encode($item->getErrors()));
            }
        } catch (\Exception $e) {
            Yii::error("updateAllQtyInStockDetail failed for item {$item->id}: " . $e->getMessage());
            FlashHandler::err("An error occurred while updating the item. " . $e->getMessage());
        }

        return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
    }

    public function actionViewInventoryStockoutboundDetail($id, $productionPanelId, $type) {
        $stockDetail = StockOutboundDetails::findOne($id);
        $inventoryStockoutbound = InventoryStockoutbound::find()
                ->where(['reference_id' => $stockDetail->id])
                ->andWhere(['reference_type' => $type])
                ->all();

        if (Yii::$app->request->post()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postData = Yii::$app->request->post("InventoryStockoutbound");

                foreach ($postData as $data) {
                    $intStockoutbound = InventoryStockoutbound::findOne($data['id']);
                    $newQty = $intStockoutbound->qty - $data['return_reserved_qty'];
                    $intStockoutbound->qty = $newQty;

                    if (!$intStockoutbound->save()) {
                        throw new \Exception('Failed to save InventoryStockoutbound.');
                    }

                    $inventorydetail = InventoryDetail::findOne($data['inventory_detail_id']);
                    $inventorydetail->stock_reserved -= $data['return_reserved_qty'];
                    $inventorydetail->stock_available += $data['return_reserved_qty'];
                    if (!$inventorydetail->save()) {
                        throw new \Exception('Failed to update InventoryDetail.');
                    }
                }

                $stockDetail->updateAllQtyInStockDetail($stockDetail);
                $stockDetail->updateStockMasterStatus($productionPanelId);
                $transaction->commit();
                FlashHandler::success("Reserved quantities have been updated successfully");
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
        }

        return $this->renderAjax('_formInventoryStockoutbound', [
                    'stockDetail' => $stockDetail,
                    'inventoryStockoutbound' => $inventoryStockoutbound,
        ]);
    }

    public function actionDeactivateItem($id) {
        if (Yii::$app->request->isPost) {
            $model = StockOutboundDetails::findOne($id);
            $model->active_sts = 0;
            $model->update();
            $stockMasters = VStockDispatchMaster::find()->where(['stock_outbound_details_id' => $id])->all();
            foreach ($stockMasters as $stockMaster) {
                $dispatchMaster = StockDispatchMaster::findOne($stockMaster->dispatch_id);
                $dispatchMaster->updateDispatchMasterStatus('deactiveItem');
            }
            return $this->redirect(['view-material-detail', 'productionPanelId' => $model->stockOutboundMaster->production_panel_id]);
        }
    }

    public function actionDispatchItemList($productionPanelId, $dispatchId, $action) {
        $searchModel = new VStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $dispatchId);
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
        $model = StockDispatchMaster::findOne($dispatchId);
//        $acknowledged = StockDispatchMaster::findOne(['id' => $dispatchId, 'status' => 1]);

        return $this->render('dispatchItemList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bomMaster' => $bomMaster,
                    'model' => $model,
//                    'acknowledged' => $acknowledged,
                    'action' => $action
        ]);
    }

    /*     * ********************** Update Stock Dispatch ****************************************** */

    public function actionUpdateStockDispatch($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->joinWith('stockOutboundDetails')->all();
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
        $model = new StockOutboundMaster();
        $receivers = $model->getReceivers($productionPanelId);

        if (Yii::$app->request->post()) {
            $postDataReceiver = Yii::$app->request->post('receiver');
            $postDataCurrentStatus = Yii::$app->request->post('current_sts');
            $postDataDispatch = Yii::$app->request->post('dispatch');

            if ($postDataDispatch !== null) {

                function filterPostData($postDataDispatch) {
                    return array_map(function ($obj) {
                        return array_filter($obj, function ($value) {
                            return isset($value['dispatch_qty']) &&
                                    $value['dispatch_qty'] !== "0" &&
                                    $value['dispatch_qty'] !== "";
                        });
                    }, $postDataDispatch);
                }

                $filteredDataDispatch = array_filter(filterPostData($postDataDispatch));

                if (!empty($filteredDataDispatch)) {
                    $combinedData = [
                        'receiver' => $postDataReceiver,
                        'current_sts' => $postDataCurrentStatus,
                        'dispatch' => $filteredDataDispatch
                    ];
                    Yii::$app->session->set('postData', $combinedData);
                    return $this->redirect(['confirm-stock-dispatch', 'productionPanelId' => $productionPanelId]);
                }
            }

            \common\models\myTools\FlashHandler::err("No item has been selected");
        }
        return $this->render('_updateStockDispatch', [
                    'stockMasters' => $stockMasters,
                    'bomMaster' => $bomMaster,
                    'receivers' => $receivers
        ]);
    }

    public function actionConfirmStockDispatch($productionPanelId) {
        $postData = Yii::$app->session->get('postData');
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();
        $model = new StockOutboundMaster();
        $receivers = $model->getReceivers($productionPanelId);
        $receiverIds = array_column($receivers, 'id');
        $receiverId = $postData['receiver']['id'] ?? null;
        if (!in_array($receiverId, $receiverIds)) {
            FlashHandler::err("No receiver found");
            return $this->redirect(['update-stock-dispatch', 'productionPanelId' => $productionPanelId]);
        }

        if (Yii::$app->request->post()) {
            $stockDetail = new StockOutboundDetails();
            if ($stockDetail->processStockDispatch($bomMaster, $productionPanelId, $postData, StockDispatchTrial::DISPATCH_STATUS)) {
//                $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
//                foreach ($stockMasters as $stockMaster) {
//                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
//                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
//                    $stockMaster->save();
//                }

                $stockDetail->updateStockMasterStatus($productionPanelId);
                FlashHandler::success("Success!");
                Yii::$app->session->remove('postData');
                return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
            }
        }

        return $this->render('confirmStockDispatch', [
                    'postData' => $postData,
                    'bomMaster' => $bomMaster,
                    'receivers' => $receivers
        ]);
    }

    /*     * ********************** Stock Adjustment ****************************************** */

    public function actionStockAdjustment($productionPanelId) {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, $productionPanelId);
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);

        return $this->render('stockDispatchList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'productionPanelId' => $productionPanelId,
                    'bomMaster' => $bomMaster,
                    'action' => 'adjust'
        ]);
    }

    public function actionAdjustDispatchQuantity($dispatchId, $detailId) {
        $dispatchMaster = VStockDispatchMaster::findOne(['dispatch_id' => $dispatchId, 'stock_outbound_details_id' => $detailId]);
        $stockOutboundDetail = StockOutboundDetails::findOne($detailId);
//        $stockMasters = StockOutboundMaster::findAll(['production_panel_id' => $dispatchMaster->production_panel_id]);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("dispatch");
            if ($stockOutboundDetail->processStockAdjustment($postData, $dispatchMaster, StockDispatchTrial::ADJUST_STATUS)) {
//                foreach ($stockMasters as $stockMaster) {
//                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
//                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
//                    if ($stockMaster->save()) {
//                        FlashHandler::success("Success! The quantity has been updated successfully.");
//                    }
//                }
                $stockOutboundDetail->updateStockMasterStatus($dispatchMaster->production_panel_id);
                FlashHandler::success("Success! The quantity has been updated successfully.");
            }

            return $this->redirect(['dispatch-item-list', 'productionPanelId' => $dispatchMaster->production_panel_id, 'dispatchId' => $dispatchId, 'action' => 'adjust']);
        }
        return $this->renderAjax('_stockAdjustment', [
                    'dispatchMaster' => $dispatchMaster
        ]);
    }

    /*     * ********************** Stock Return ****************************************** */

    public function actionStockReturn($productionPanelId) {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, $productionPanelId);
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();

        return $this->render('stockDispatchList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'productionPanelId' => $productionPanelId,
                    'bomMaster' => $bomMaster,
                    'action' => 'return'
        ]);
    }

    public function actionReturnDispatchedQuantity($dispatchId, $detailId) {
        $dispatchMaster = VStockDispatchMaster::findOne(['dispatch_id' => $dispatchId, 'stock_outbound_details_id' => $detailId]);
        $stockOutboundDetail = StockOutboundDetails::findOne($detailId);
//        $stockMasters = StockOutboundMaster::findAll(['production_panel_id' => $dispatchMaster->production_panel_id]);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("dispatch");
            if ($stockOutboundDetail->processStockReturn($postData, $dispatchMaster, StockDispatchTrial::RETURN_STATUS)) {
//                foreach ($stockMasters as $stockMaster) {
//                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
//                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
//                    if ($stockMaster->save()) {
//                        FlashHandler::success("Success! The return dispatched quantity has been saved successfully.");
//                    }
//                }

                $stockOutboundDetail->updateStockMasterStatus($dispatchMaster->production_panel_id);
                FlashHandler::success("Success! The return dispatched quantity has been saved successfully.");
            }

            return $this->redirect(['dispatch-item-list', 'productionPanelId' => $dispatchMaster->production_panel_id, 'dispatchId' => $dispatchId, 'action' => 'return']);
        }
        return $this->renderAjax('_stockReturn', [
                    'dispatchMaster' => $dispatchMaster
        ]);
    }
}
