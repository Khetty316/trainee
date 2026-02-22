<?php

namespace frontend\controllers;

use Yii;
use frontend\models\bom\bomdetails;
use frontend\models\bom\bomdetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\bom\BomMaster;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\modules\auth\models\AuthItem;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use yii\web\Response;
use common\models\myTools\FlashHandler;
use frontend\models\office\preReqForm\PrereqFormMaster;
use frontend\models\office\preReqForm\VPrereqFormMasterDetail;
use frontend\models\office\preReqForm\PrereqFormItem;
use frontend\models\inventory\InventorySupplier;

/**
 * BomController implements the CRUD actions for bomdetails model.
 */
class BomController extends Controller {

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
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal, AuthItem::ROLE_Bom_View],
                    ],
                    [
                        'actions' => ['prerequisition', 'finalize-selected-material', 'get-model-details', 'get-brands', 'create', 'update', 'delete', 'delete-multiple', 'finalize-bom', 'upload-excel', 'save-bom-details', 'deactivate-bom-details'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal],
                    ],
                    [
                        'actions' => ['revert-bom'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Bom_Super],
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

    /**
     * 
     * @param type $productionPanelId
     * @return type
     */
    public function actionIndex($productionPanelId, $justCreated = false) {
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();
        if (!$bomMaster) {
            $bomMaster = new BomMaster();
            $bomMaster->production_panel_id = $productionPanelId;
            $bomMaster->save();
        }


        $searchModel = new bomdetailSearch();
        $params = Yii::$app->request->queryParams;
        $params['bomdetailSearch']['bom_master'] = $bomMaster->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bomMaster' => $bomMaster,
                    'justCreated' => $justCreated
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

//    public function actionCreate($bomMasterId) {
//        $model = new bomdetails();
//        $model->bom_master = $bomMasterId;
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id, 'justCreated' => true]);
//        }
//
//        return $this->renderAjax('create', [
//                    'model' => $model,
//                    'modelList' => $modelList,
//                    'brandList' => $brandList,
//        ]);
//    }

    public function actionCreate($bomMasterId) {
        $model = new BomDetails();
        $model->bom_master = $bomMasterId;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', 'BOM detail saved successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Please fix the errors below.');
            }
            return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id, 'justCreated' => true]);
        }

        // Prepare data for view
        $inventoryModel = new InventoryModel();
        $data = $inventoryModel->prepareFormData($model);

        return $this->renderAjax('create', $data);
    }

    /**
     * Get model details including description via AJAX
     * NOTE: This method can be removed if not used elsewhere
     * @param int $modelId
     * @return array
     */
    public function actionGetModelDetails($modelId) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryModel = InventoryModel::findOne($modelId);
        if ($inventoryModel) {
            return [
                'success' => true,
                'data' => [
                    'id' => $inventoryModel->id,
                    'type' => $inventoryModel->type,
                    'description' => $inventoryModel->description,
                    'group' => $inventoryModel->group,
                    'unit_type' => $inventoryModel->unit_type,
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Model not found'
        ];
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', 'BOM detail saved successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Please fix the errors below.');
            }
            return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id]);
        }

        // Prepare data for view
        $inventoryModel = new InventoryModel();
        $data = $inventoryModel->prepareFormData($model);

        return $this->renderAjax('update', $data);
    }

//    public function actionUpdate($id) {
//        $model = bomdetails::findOne($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id]);
//        }
//
//        return $this->renderAjax('update', [
//                    'model' => $model,
//        ]);
//    }


    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id]);
    }

    protected function findModel($id) {
        if (($model = bomdetails::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteMultiple() {
        $ids = Yii::$app->request->post('ids', []);
        if (!empty($ids)) {
            bomdetails::deleteAll(['id' => $ids]);
        }
        return $this->asJson(['success' => true]);
    }

    public function actionFinalizeBom() {
        $bomMasterId = Yii::$app->request->post('bomMasterId');
        $hasBomDetailsRecord = \frontend\models\bom\BomDetails::find()->where(['bom_master' => $bomMasterId])->exists();
        if ($hasBomDetailsRecord) {
            $bomMaster = BomMaster::findOne($bomMasterId);
            $bomMaster->finalized_status = 1;
            if ($bomMaster->update()) {
                \common\models\myTools\FlashHandler::success("Finalized");
            }
        }

        return $this->asJson(['success' => true]);
    }

    public function actionFinalizeSelectedMaterial() {
        $bomMasterId = Yii::$app->request->post('bomMasterId');
        $selectedIds = Yii::$app->request->post('ids'); // Get selected item IDs

        if (empty($selectedIds)) {
            \common\models\myTools\FlashHandler::err("No items selected");
            return $this->asJson(['success' => false]);
        }

        // Update only selected BomDetails records
        $updateCount = \frontend\models\bom\BomDetails::updateAll(
                ['is_finalized' => 2], // Set finalized flag
                [
                    'and',
                    ['id' => $selectedIds],
                    ['bom_master' => $bomMasterId],
                    ['<>', 'is_finalized', 2] // Only update items not already finalized
                ]
        );

        if ($updateCount > 0) {
            // Check if ALL items are finalized
            $totalItems = \frontend\models\bom\BomDetails::find()
                    ->where(['bom_master' => $bomMasterId])
                    ->andWhere(['active_status' => 1])
                    ->andWhere(['<>', 'inventory_sts', 3]) // Fixed syntax
                    ->count();

            $finalizedItems = \frontend\models\bom\BomDetails::find()
                    ->where([
                        'is_finalized' => 2,
                        'active_status' => 1,
                        'bom_master' => $bomMasterId
                    ])
                    ->andWhere(['<>', 'inventory_sts', 3])
                    ->count();

            $bomMaster = BomMaster::findOne($bomMasterId);

            if ($finalizedItems == $totalItems && $totalItems > 0) {
                $bomMaster->finalized_status = 1; // Fully finalized
            } elseif ($finalizedItems > 0) {
                $bomMaster->finalized_status = 2; // Partially finalized
            } else {
                $bomMaster->finalized_status = 0; // Not finalized
            }

            $bomMaster->save();

            \common\models\myTools\FlashHandler::success("Finalized {$updateCount} item(s)");
            return $this->asJson(['success' => true]);
        } else {
            \common\models\myTools\FlashHandler::err("No items were finalized (may already be finalized)");
            return $this->asJson(['success' => false]);
        }
    }

    public function actionPrerequisition() {
        $master = new PrereqFormMaster();
        $vmodel = new VPrereqFormMasterDetail();
        $items = [new PrereqFormItem()];
        $worklists = [];
        $hasSuperiorUpdate = false;

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // ===== SAVE MASTER =====
                $postMaster = Yii::$app->request->post('PrereqFormMaster');
                $master->date_of_material_required = $postMaster['date_of_material_required'] ?? null;
                $master->prf_no = $master->generatePrfNo();
                $master->superior_id = Yii::$app->user->identity->superior_id;
                $master->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
                $master->is_deleted = 0;
                $master->source_module = 2; //inventory

                if (!$master->save()) {
                    throw new \Exception('Master save failed: ' . json_encode($master->getErrors()));
                }

                // ===== SAVE ITEMS =====
                $moduleIndex = 'inventory';
                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);
                $master->saveItems($master->id, $postItems, false, $moduleIndex);

                $transaction->commit();
                FlashHandler::success('Purchase Requisition Form created successfully!');
                return $this->redirect(['executive-pre-requisition-pending-approval']);
            } catch (\Exception $e) {
                $transaction->rollBack();

                $master->load(Yii::$app->request->post());

                $items = [];
                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);

                foreach ($postItems as $index => $itemData) {
                    $item = new VPrereqFormMasterDetail();
                    $item->setAttributes($itemData, false);
                    $items[$index] = $item;
                }

                FlashHandler::err($e->getMessage());
            }
        }

        // ===== DROPDOWN DATA =====
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        $supplierList = InventorySupplier::getAllDropDownSupplierList();
        $brandList = InventoryBrand::getAllDropDownBrandList();
        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();

        return $this->renderAjax('..\inventory\_prereq_form_unified', [
                    'master' => $master,
                    'items' => $items,
                    'vmodel' => $vmodel,
                    'isUpdate' => false,
                    'isView' => false,
                    'moduleIndex' => 'inventory',
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'currencyList' => $currencyList,
        ]);
    }

    public function actionRevertBom() {
        $bomMasterId = Yii::$app->request->post('bomMasterId');
        $bomMaster = BomMaster::findOne($bomMasterId);
        $bomMaster->finalized_status = 0;
        if ($bomMaster->update()) {
            \common\models\myTools\FlashHandler::success("Reverted");
        }
        return $this->asJson(['success' => true]);
    }

//    public function actionUploadExcel($bomMasterId) {
//        if (Yii::$app->request->isPost) {
//            $excelFile = UploadedFile::getInstanceByName('excelTemplate');
//
//            if ($excelFile && $excelFile->tempName) {
//                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));
//
//                if ($extension !== 'xls') {
//                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
//                    return $this->redirect(['your-redirect-action']);
//                }
//
//                try {
//                    $reader = new Xls();
//                    $spreadsheet = $reader->load($excelFile->tempName);
//                    $worksheet = $spreadsheet->getActiveSheet();
//
//                    $buffer = []; // Buffer to store data temporarily
//
//                    foreach ($worksheet->getRowIterator(2) as $row) {
//                        $cells = $row->getCellIterator();
//                        $cells->setIterateOnlyExistingCells(false);
//
//                        $data = [];
//                        foreach ($cells as $cell) {
//                            $data[] = $cell->getValue();
//                        }
//
//                        $modelType = $data[0];
//                        $brand = $data[1];
//                        $description = $data[2];
//                        $quantity = $data[3];
//                        $remark = $data[4];
//
//                        if (empty($modelType)) {
//                            break;
//                        }
//
//                        $buffer[] = [
//                            'model_type' => $modelType,
//                            'brand' => $brand,
//                            'description' => $description,
//                            'quantity' => $quantity,
//                            'remark' => $remark,
//                        ];
//                    }
//
//                    if (!empty($buffer)) {
//                        return $this->render('uploadToConfirm', ['buffer' => $buffer, 'bomMasterId' => $bomMasterId]);
//                    } else {
//                        $bomMaster = BomMaster::findOne($bomMasterId);
//                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Model Type' column in your Excel file is not left blank.");
//                        return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
//                    }
//                } catch (\Exception $e) {
//                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
//                    return $this->redirect(['your-redirect-action']);
//                }
//            }
//        }
//
//        return $this->render('upload');
//    }
//
//    public function actionSaveBomDetails($bomMasterId) {
//        if (Yii::$app->request->isPost) {
//            $data = Yii::$app->request->post('BomDetails');
//            foreach ($data['model_type'] as $index => $modelType) {
//                $bomDetails = new BomDetails();
//                $bomDetails->model_type = $modelType;
//                $bomDetails->bom_master = $bomMasterId;
//                $bomDetails->brand = $data['brand'][$index];
//                $bomDetails->description = $data['description'][$index];
//                $bomDetails->qty = $data['quantity'][$index];
//                $bomDetails->remark = $data['remark'][$index];
//                $bomDetails->save();
//            }
//            Yii::$app->session->setFlash('success', 'Data successfully saved to the database.');
//        }
//
//        $bomMaster = BomMaster::findOne($bomMasterId);
//        return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
//    }

    public function actionUploadExcel($bomMasterId) {
        if (Yii::$app->request->isPost) {
            $excelFile = UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    $bomMaster = BomMaster::findOne($bomMasterId);
                    return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
                }

                try {
                    $reader = new Xls();
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();

                    $buffer = []; // Buffer to store data temporarily
                    $errors = []; // Track errors during processing

                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $modelType = $data[0];
                        $brand = $data[1];
                        $description = $data[2];
                        $quantity = $data[3];
                        $remark = $data[4];

                        if (empty($modelType)) {
                            break;
                        }

                        $buffer[] = [
                            'model_type' => $modelType,
                            'brand' => $brand,
                            'description' => $description,
                            'quantity' => $quantity,
                            'remark' => $remark,
                        ];
                    }

                    if (!empty($buffer)) {
                        return $this->render('uploadToConfirm', [
                                    'buffer' => $buffer,
                                    'bomMasterId' => $bomMasterId,
                                    'errors' => []
                        ]);
                    } else {
                        $bomMaster = BomMaster::findOne($bomMasterId);
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Model Type' column in your Excel file is not left blank.");
                        return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    $bomMaster = BomMaster::findOne($bomMasterId);
                    return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
                }
            }
        }

        return $this->render('upload', ['bomMasterId' => $bomMasterId]);
    }

    public function actionSaveBomDetails($bomMasterId) {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['upload-excel', 'bomMasterId' => $bomMasterId]);
        }

        $post = Yii::$app->request->post('BomDetails');
        $actionMode = Yii::$app->request->post('action_mode', 'check');

        if (empty($post) || empty($post['model_type'])) {
            Yii::$app->session->setFlash('error', 'No data to process.');
            return $this->redirect(['upload-excel', 'bomMasterId' => $bomMasterId]);
        }

        $errors = [];
        $warnings = [];
        $successCount = 0;

        /**
         * STEP 1 — DUPLICATE CHECK (ALWAYS BLOCK)
         */
        $duplicates = $this->checkExcelDuplicateModels($post);

        if (!empty($duplicates)) {
            $errors = [];
            foreach ($duplicates as $dup) {
                $errors[] = "Rows {$dup['row1']} & {$dup['row2']}: '{$dup['name1']}' ({$dup['brand1']}) is {$dup['percent']}% similar to '{$dup['name2']}' ({$dup['brand2']}) - please remove duplicate";
            }

            return $this->render('uploadToConfirm', [
                        'buffer' => $this->rebuildBuffer($post),
                        'bomMasterId' => $bomMasterId,
                        'errors' => $errors, // <-- send the errors array
                        'warnings' => [],
            ]);
        }

        /**
         * STEP 2 — INVENTORY CHECK (WARN ONLY)
         */
        foreach ($post['model_type'] as $i => $modelType) {
            $brand = $post['brand'][$i] ?? '';

            if (empty($modelType) || empty($brand)) {
                continue;
            }

            if (!$this->getModelAndBrand($modelType, $brand)) {
                $warnings[$i] = "'{$modelType}' ({$brand}) not found in inventory - Pre-Requisition will be required";
            }
        }

        /**
         * STEP 3 — CHECK MODE → STOP HERE
         */
        if ($actionMode === 'check') {
            return $this->render('uploadToConfirm', [
                        'buffer' => $this->rebuildBuffer($post),
                        'bomMasterId' => $bomMasterId,
                        'errors' => [],
                        'warnings' => $warnings,
            ]);
        }

        /**
         * STEP 4 — SAVE MODE
         */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($post['model_type'] as $i => $modelType) {
                $brand = $post['brand'][$i] ?? '';
                $qty = $post['quantity'][$i] ?? 0;
                $description = $post['description'][$i] ?? '';
                $remark = $post['remark'][$i] ?? '';

                if (empty($modelType) || empty($brand) || $qty <= 0) {
                    continue;
                }

                // Check if item exists in inventory
                $inventory = $this->getModelAndBrand($modelType, $brand);

                $bom = new BomDetails();
                $bom->bom_master = $bomMasterId;

                if ($inventory) {
                    // Item found in inventory - save with full details
                    $bom->inventory_model_id = $inventory->id;
                    $bom->inventory_brand_id = $inventory->inventory_brand_id;
                    $bom->model_type = $inventory->type;
                    $bom->brand = $inventory->inventoryBrand->name;
                    $bom->is_finalized = 1;  // not finalized
                    $bom->inventory_sts = 2;  //linked to inventory
                } else {
                    // Item NOT in inventory - save with pending status
                    $bom->inventory_model_id = null;
                    $bom->inventory_brand_id = null;
                    $bom->model_type = $modelType;
                    $bom->brand = $brand;
                    $bom->is_finalized = 1; // not finalized
                    $bom->inventory_sts = 0; // not linked to inventory
                }

                // Common fields for both scenarios
                $bom->description = $description;
                $bom->qty = $qty;
                $bom->remark = $remark;
                $bom->active_status = 1;

                if (!$bom->save(false)) {
                    $errors[$i] = 'Failed to save row: ' . implode(', ', $bom->getFirstErrors());
                } else {
                    $successCount++;
                }
            }

            if (!empty($errors)) {
                $transaction->rollBack();
                return $this->render('uploadToConfirm', [
                            'buffer' => $this->rebuildBuffer($post),
                            'bomMasterId' => $bomMasterId,
                            'errors' => $errors,
                            'warnings' => $warnings,
                ]);
            }

            $transaction->commit();

            // Count items saved without inventory
            $missingCount = count($warnings);

            if ($missingCount > 0) {
                Yii::$app->session->setFlash(
                        'success',
                        "{$successCount} item(s) saved successfully. {$missingCount} item(s) saved without inventory link and will require Pre-Requisition."
                );
            } else {
                Yii::$app->session->setFlash(
                        'success',
                        "{$successCount} item(s) saved and linked to inventory successfully."
                );
            }

            $bomMaster = BomMaster::findOne($bomMasterId);
            return $this->redirect(['index', 'productionPanelId' => $bomMaster->production_panel_id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['upload-excel', 'bomMasterId' => $bomMasterId]);
        }
    }

    /**
     * Find inventory model by type and brand name (case-insensitive, trim whitespace)
     */
    private function getModelAndBrand($modelType, $brandName) {
        // Normalize input
        $modelType = trim($modelType);
        $brandName = trim($brandName);

        // First, find the brand (case-insensitive, exact match after trim)
        $brand = InventoryBrand::find()
                ->where(['active_sts' => 2])
                ->andWhere(['=', new \yii\db\Expression('LOWER(TRIM([[name]]))'), strtolower($brandName)])
                ->one();

        if (!$brand) {
            return null;
        }

        // Then find the model with matching type and brand (case-insensitive, exact match after trim)
        $model = InventoryModel::find()
                ->where(['active_sts' => 2, 'inventory_brand_id' => $brand->id])
                ->andWhere(['=', new \yii\db\Expression('LOWER(TRIM([[type]]))'), strtolower($modelType)])
                ->one();

        return $model;
    }

    /**
     * Helper method to rebuild buffer from POST data
     */
    private function rebuildBuffer($post) {
        $buffer = [];

        // Re-index arrays to handle deleted rows
        $modelTypes = array_values($post['model_type']);
        $brands = array_values($post['brand'] ?? []);
        $descriptions = array_values($post['description'] ?? []);
        $quantities = array_values($post['quantity'] ?? []);
        $remarks = array_values($post['remark'] ?? []);

        foreach ($modelTypes as $index => $modelType) {
            $buffer[] = [
                'model_type' => $modelType,
                'brand' => $brands[$index] ?? '',
                'description' => $descriptions[$index] ?? '',
                'quantity' => $quantities[$index] ?? '',
                'remark' => $remarks[$index] ?? '',
            ];
        }

        return $buffer;
    }

    /**
     * Check for duplicate models within Excel data
     * Compares both model type AND brand combined
     */
    private function checkExcelDuplicateModels($post) {
        $normalized = [];
        $errors = [];

        // Check if arrays exist
        if (!isset($post['model_type']) || !is_array($post['model_type'])) {
            return $errors;
        }

        if (!isset($post['brand']) || !is_array($post['brand'])) {
            return $errors;
        }

        foreach ($post['model_type'] as $index => $modelType) {
            $brand = $post['brand'][$index] ?? '';

            // Skip empty entries
            if (empty($modelType) || empty($brand)) {
                continue;
            }

            // Normalize the combination of model type and brand
            $current = $this->normalizeName($modelType . ' ' . $brand);

            foreach ($normalized as $prevIndex => $prevNormalized) {
                similar_text($current, $prevNormalized, $percent);

                if ($percent >= 90) {
                    $errors[] = [
                        'row1' => $prevIndex + 1,
                        'row2' => $index + 1,
                        'name1' => $post['model_type'][$prevIndex],
                        'brand1' => $post['brand'][$prevIndex],
                        'name2' => $modelType,
                        'brand2' => $brand,
                        'percent' => round($percent, 1),
                    ];
                }
            }

            $normalized[$index] = $current;
        }

        return $errors;
    }

    /**
     * Normalize name for comparison
     */
    private function normalizeName($name) {
        // Convert to lowercase
        $normalized = strtolower($name);

        // Remove special characters and extra spaces
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim($normalized);
    }

    public function actionDeactivateBomDetails($id) {
        if (Yii::$app->request->isPost) {
            $model = \frontend\models\bom\BomDetails::findOne($id);
            $model->active_status = 0;
            $model->update();
            return $this->redirect(['index', 'productionPanelId' => $model->bomMaster->production_panel_id]);
        }
    }
}
