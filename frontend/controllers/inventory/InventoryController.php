<?php

namespace frontend\controllers\inventory;

use Yii;
use frontend\models\inventory\InventorySupplier;
use frontend\models\inventory\InventorySupplierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryBrandSearch;
use frontend\models\inventory\InventoryModelSearch;
use frontend\models\inventory\InventoryDetail;
use frontend\models\inventory\VInventoryDetailSearch;
use frontend\models\office\preReqForm\PrereqFormMaster;
use frontend\models\office\preReqForm\PrereqFormMasterSearch;
use frontend\models\office\preReqForm\PrereqFormItem;
use frontend\models\office\preReqForm\VPrereqFormMasterDetail;
use frontend\models\office\preReqForm\PrereqFormItemWorklist;
use frontend\models\RefGeneralStatus;
use frontend\models\inventory\InventoryPurchaseRequest;
use frontend\models\common\RefCurrencies;
use frontend\models\common\RefUserDepartments;
use frontend\models\inventory\InventoryOrderRequest;
use frontend\models\inventory\InventoryOrderRequestSearch;
use frontend\models\inventory\InventoryPurchaseOrder;
use frontend\models\inventory\InventoryPurchaseOrderItem;

/**
 * InventoryController implements the CRUD actions for InventorySupplier model.
 */
class InventoryController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/inventory/');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /*     * ************* Item Detail ************************ */

    public function actionItemList() {
        $searchModel = new VInventoryDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('itemList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewItemDetail($id) {
        $model = InventoryDetail::findOne($id);
        $vmodel = \frontend\models\inventory\VInventoryDetail::findOne($model->id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['item-list']);
        }

        return $this->render('_formItemDetail', [
                    'model' => $model,
                    'vmodel' => $vmodel
        ]);
    }

    //executive only
    public function actionAddNewItem() {
        $supplier = new InventorySupplier();
        $brand = new InventoryBrand();
        $modelClass = new InventoryModel();
        $supplierList = $supplier->getAllDropDownSupplierList();
        $brandList = $brand->getAllDropDownBrandList();
        $modelList = $modelClass->getAllDropDownModelList();
        $currencyList = RefCurrencies::getActiveDropdownlist_by_id();
        $itemList = [new InventoryDetail()];
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();

        if (Yii::$app->request->post()) {
            $itemList = [];
            $postItems = Yii::$app->request->post('InventoryDetail', []);

            foreach ($postItems as $i => $itemData) {
                $item = new InventoryDetail();
                $item->load(['InventoryDetail' => $itemData]);
                $itemList[] = $item;
            }

            $isValid = true;

            // Validate each item
            foreach ($itemList as $index => $item) {
                if (!$item->validate()) {
                    $isValid = false;
                }

                // Check for duplicates in database
                $existing = InventoryDetail::find()
                        ->where([
                            'department_code' => $item->department_code,
                            'supplier_id' => $item->supplier_id,
                            'brand_id' => $item->brand_id,
                            'model_id' => $item->model_id,
                        ])
                        ->one();

                if ($existing) {
                    $supplier = InventorySupplier::findOne($item->supplier_id);
                    $brand = InventoryBrand::findOne($item->brand_id);
                    $model = InventoryModel::findOne($item->model_id);
                    $department = \frontend\models\common\RefUserDepartments::findOne($item->department_code);

                    $departmentName = $department ? $department->department_name : $item->department_code;
                    $supplierName = $supplier ? $supplier->name : $item->supplier_id;
                    $brandName = $brand ? $brand->name : $item->brand_id;
                    $modelName = $model ? $model->description : $item->model_id;

                    $item->addError('model_id', "Item already exists: {$departmentName} - {$supplierName} - {$brandName} - {$modelName}");
                    $isValid = false;
                }

                // Check for duplicates within the form submission
                foreach ($itemList as $compareIndex => $compareItem) {
                    if ($index !== $compareIndex &&
                            $item->department_code === $compareItem->department_code &&
                            $item->supplier_id === $compareItem->supplier_id &&
                            $item->brand_id === $compareItem->brand_id &&
                            $item->model_id === $compareItem->model_id) {

                        $item->addError('model_id', "Duplicate entry found in row " . ($compareIndex + 1));
                        $isValid = false;
                        break;
                    }
                }
            }

            if ($isValid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($itemList as $item) {
                        $item->is_new = 1;
                        if (!$item->save(false)) {
                            throw new \Exception("Failed to save item");
                        }
                    }

                    $transaction->commit();
                    FlashHandler::success("Successfully saved all items");
                    return $this->redirect(['item-list']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    FlashHandler::err("Failed to save items: " . $e->getMessage());
                    Yii::error("Save items error: " . $e->getMessage(), __METHOD__);
                }
            } else {
                FlashHandler::err("Please correct the validation errors");
            }
        }

        return $this->render('createItemList', [
                    'itemList' => $itemList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'modelList' => $modelList,
                    'departmentList' => $departmentList,
                    'currencyList' => $currencyList
        ]);
    }

    public function actionCheckDuplicate() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $departmentCode = Yii::$app->request->post('department_code');
        $supplierId = Yii::$app->request->post('supplier_id');
        $brandId = Yii::$app->request->post('brand_id');
        $modelId = Yii::$app->request->post('model_id');

        $existing = InventoryDetail::find()
                ->where([
                    'department_code' => $departmentCode,
                    'supplier_id' => $supplierId,
                    'brand_id' => $brandId,
                    'model_id' => $modelId,
                    'active_sts' => 2
                ])
                ->one();

        if ($existing) {
            $supplier = InventorySupplier::findOne($supplierId);
            $brand = InventoryBrand::findOne($brandId);
            $model = InventoryModel::findOne($modelId);
            $department = \frontend\models\common\RefUserDepartments::findOne($departmentCode);

            $departmentName = $department ? $department->department_name : $departmentCode;
            $supplierName = $supplier ? $supplier->name : $supplierId;
            $brandName = $brand ? $brand->name : $brandId;
            $modelName = $model ? $model->description : $modelId;

            return [
                'exists' => true,
                'message' => "Item already exists: {$departmentName} - {$supplierName} - {$brandName} - {$modelName}"
            ];
        }

        return ['exists' => false];
    }

    /*     * ************* Supplier ************************ */

    public function actionSupplierList() {
        $searchModel = new InventorySupplierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('supplierList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    //executive
    public function actionAddNewSupplier() {
        $model = new InventorySupplier();

        if ($model->load(Yii::$app->request->post())) {

            // CHECK SIMILARITY
            $similar = InventorySupplier::findSimilarSupplier($model->name, 80);

            if ($similar['match']) {
                Yii::$app->session->setFlash('warning',
                        'Supplier name is ' . $similar['percent'] . '% similar to existing supplier: ' .
                        $similar['existing_name']
                );
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Supplier created successfully');
                return $this->redirect(['supplier-list']);
            }
        }

        return $this->render('createSupplier', [
                    'model' => $model,
        ]);
    }

    public function actionViewSupplier($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("The detail has been updated successfully");
            return $this->redirect(['supplier-list']);
        }

        return $this->render('updateSupplier', [
                    'model' => $model,
        ]);
    }

    public function actionAddByTemplateSupplier() {
        $model = new InventorySupplier();

        if (Yii::$app->request->isPost) {
            $excelFile = \yii\web\UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['add-by-template-supplier']);
                }

                try {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();

                    $buffer = []; // Buffer to store data temporarily

                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $name = $data[1];
                        $addr1 = $data[2];
                        $addr2 = $data[3];
                        $addr3 = $data[4];
                        $addr4 = $data[5];
                        $ctcName = $data[6];
                        $ctcNo = $data[7];
                        $ctcEmail = $data[8];
                        $ctcFax = $data[9];
                        $agentTerms = $data[10];

                        if (empty($name)) {
                            break;
                        }

                        $buffer[] = [
                            'name' => $name,
                            'addr1' => $addr1,
                            'addr2' => $addr2,
                            'addr3' => $addr3,
                            'addr4' => $addr4,
                            'ctcName' => $ctcName,
                            'ctcNo' => $ctcNo,
                            'ctcEmail' => $ctcEmail,
                            'ctcFax' => $ctcFax,
                            'agentTerms' => $agentTerms,
                        ];
                    }

                    if (!empty($buffer)) {
                        return $this->render('uploadToConfirmSupplier', ['buffer' => $buffer]);
                    } else {
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Name' column in your Excel file is not left blank.");
                        return $this->redirect(['add-by-template-supplier']);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['add-by-template-supplier']);
                }
            }
        }

        return $this->render('addByTemplateSupplier', [
                    'model' => $model,
        ]);
    }

    public function actionSaveSupplierDetails() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('InventorySupplier');

            if (empty($post['type'])) {
                Yii::$app->session->setFlash('error', 'No supplier data to save.');
                return $this->redirect(['add-supplier-by-template']);
            }

            $errors = [];
            $successCount = 0;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Check for duplicates within the Excel file
                $duplicates = $this->checkExcelDuplicateSuppliers($post);

                if (!empty($duplicates)) {
                    $transaction->rollBack();

                    // Format error message
                    $errorMessage = 'Duplicate suppliers found in Excel file:<br>';
                    foreach ($duplicates as $dup) {
                        $errorMessage .= "Row {$dup['row1']} ({$dup['name1']}) and Row {$dup['row2']} ({$dup['name2']}) are {$dup['percent']}% similar<br>";
                    }

                    Yii::$app->session->setFlash('error', $errorMessage);
                    return $this->render('uploadToConfirmSupplier', [
                                'buffer' => $this->rebuildBufferSupplier($post),
                                'errors' => []
                    ]);
                }

                foreach ($post['type'] as $index => $name) {
                    // Check if supplier name already exists in database
                    $similarCheck = InventorySupplier::findSimilarSupplier($name, 80);
                    if ($similarCheck['match']) {
                        $errors[$index] = "Similar supplier exists: {$similarCheck['existing_name']} ({$similarCheck['percent']}% match)";
                        continue;
                    }

                    // Create new supplier
                    $supplier = new InventorySupplier();
                    $supplier->name = $post['type'][$index];
                    $supplier->address1 = $post['address1'][$index] ?? null;
                    $supplier->address2 = $post['address2'][$index] ?? null;
                    $supplier->address3 = $post['address3'][$index] ?? null;
                    $supplier->address4 = $post['address4'][$index] ?? null;
                    $supplier->contact_name = $post['contact_name'][$index] ?? null;
                    $supplier->contact_number = $post['contact_number'][$index] ?? null;
                    $supplier->contact_email = $post['contact_email'][$index] ?? null;
                    $supplier->contact_fax = $post['contact_fax'][$index] ?? null;
                    $supplier->agent_terms = $post['agent_terms'][$index] ?? null;
                    $supplier->active_sts = 2;

                    if ($supplier->save()) {
                        $successCount++;
                    } else {
                        $errors[$index] = "Failed to save: " . implode(', ', $supplier->getFirstErrors());
                    }
                }

                if (!empty($errors)) {
                    // Rollback and return to form with errors
                    $transaction->rollBack();
                    return $this->render('uploadToConfirmSupplier', [
                                'buffer' => $this->rebuildBufferSupplier($post),
                                'errors' => $errors
                    ]);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', "{$successCount} supplier(s) added successfully.");
                return $this->redirect(['supplier-list']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error saving suppliers: ' . $e->getMessage());
                return $this->render('uploadToConfirmSupplier', [
                            'buffer' => $this->rebuildBufferSupplier($post),
                            'errors' => $errors
                ]);
            }
        }

        return $this->redirect(['add-supplier-by-template']);
    }

// Helper method to rebuild buffer from POST data
    private function rebuildBufferSupplier($post) {
        $buffer = [];
        foreach ($post['type'] as $index => $name) {
            $buffer[] = [
                'name' => $name,
                'addr1' => $post['address1'][$index] ?? '',
                'addr2' => $post['address2'][$index] ?? '',
                'addr3' => $post['address3'][$index] ?? '',
                'addr4' => $post['address4'][$index] ?? '',
                'ctcName' => $post['contact_name'][$index] ?? '',
                'ctcNo' => $post['contact_number'][$index] ?? '',
                'ctcEmail' => $post['contact_email'][$index] ?? '',
                'ctcFax' => $post['contact_fax'][$index] ?? '',
                'agentTerms' => $post['agent_terms'][$index] ?? '',
            ];
        }
        return $buffer;
    }

    public function checkExcelDuplicateSuppliers($post) {
        $normalized = [];
        $errors = [];

        // Check if name array exists
        if (!isset($post['type']) || !is_array($post['type'])) {
            return $errors;
        }

        foreach ($post['type'] as $index => $name) {
            // Skip empty names
            if (empty($name)) {
                continue;
            }

            $current = InventorySupplier::normalizeName($name);

            foreach ($normalized as $prevIndex => $prevName) {
                similar_text($current, $prevName, $percent);

                if ($percent >= 90) {
                    $errors[] = [
                        'row1' => $prevIndex + 1,
                        'row2' => $index + 1,
                        'name1' => $post['type'][$prevIndex],
                        'name2' => $name,
                        'percent' => round($percent, 1),
                    ];
                }
            }

            $normalized[$index] = $current;
        }

        return $errors;
    }

    /*     * ************* Brand ************************ */

    public function actionBrandList() {
        $searchModel = new InventoryBrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('brandList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    //executive
    public function actionAddNewBrand() {
        $model = new InventoryBrand();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                FlashHandler::success("Brand created successfully");
                return $this->redirect(['brand-list']);
            }

            FlashHandler::err("Failed to save brand");
        }

        return $this->renderAjax('_formBrand', [
                    'model' => $model,
        ]);
    }

    public function actionViewBrand($id) {
        $model = InventoryBrand::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("The detail has been updated successfully");
            return $this->redirect(['brand-list']);
        }

        return $this->renderAjax('_formBrand', [
                    'model' => $model,
        ]);
    }

    public function actionAddByTemplateBrand() {
        $model = new InventorySupplier();

        if (Yii::$app->request->isPost) {
            $excelFile = \yii\web\UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['add-by-template-brand']);
                }

                try {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();

                    $buffer = []; // Buffer to store data temporarily

                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $name = $data[1];

                        if (empty($name)) {
                            break;
                        }

                        $buffer[] = [
                            'name' => $name,
                        ];
                    }

                    if (!empty($buffer)) {
                        return $this->render('uploadToConfirmBrand', ['buffer' => $buffer]);
                    } else {
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Name' column in your Excel file is not left blank.");
                        return $this->redirect(['add-by-template-brand']);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['add-by-template-brand']);
                }
            }
        }

        return $this->render('addByTemplateBrand', [
                    'model' => $model,
        ]);
    }

    public function actionSaveBrandDetails() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('InventoryBrand');

            if (empty($post['type'])) {
                Yii::$app->session->setFlash('error', 'No brand data to save.');
                return $this->redirect(['add-brand-by-template']);
            }

            $errors = [];
            $successCount = 0;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Check for duplicates within the Excel file
                $duplicates = $this->checkExcelDuplicateBrands($post);

                if (!empty($duplicates)) {
                    $transaction->rollBack();

                    // Format error message
                    $errorMessage = 'Duplicate brands found in Excel file:<br>';
                    foreach ($duplicates as $dup) {
                        $errorMessage .= "Row {$dup['row1']} ({$dup['name1']}) and Row {$dup['row2']} ({$dup['name2']}) are {$dup['percent']}% similar<br>";
                    }

                    Yii::$app->session->setFlash('error', $errorMessage);
                    return $this->render('uploadToConfirmBrand', [
                                'buffer' => $this->rebuildBufferBrand($post),
                                'errors' => []
                    ]);
                }

                foreach ($post['type'] as $index => $name) {
                    // Check if brand name already exists in database
                    $similarCheck = InventoryBrand::findSimilarBrand($name, 80);
                    if ($similarCheck['match']) {
                        $errors[$index] = "Similar brand exists: {$similarCheck['existing_name']} ({$similarCheck['percent']}% match)";
                        continue;
                    }

                    // Create new brand
                    $brand = new InventoryBrand();
                    $brand->name = $post['type'][$index];
                    $brand->active_sts = 2;

                    if ($brand->save()) {
                        $successCount++;
                    } else {
                        $errors[$index] = "Failed to save: " . implode(', ', $brand->getFirstErrors());
                    }
                }

                if (!empty($errors)) {
                    // Rollback and return to form with errors
                    $transaction->rollBack();
                    return $this->render('uploadToConfirmBrand', [
                                'buffer' => $this->rebuildBufferBrand($post),
                                'errors' => $errors
                    ]);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', "{$successCount} brand(s) added successfully.");
                return $this->redirect(['brand-list']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error saving brand: ' . $e->getMessage());
                return $this->render('uploadToConfirmBrand', [
                            'buffer' => $this->rebuildBufferBrand($post),
                            'errors' => $errors
                ]);
            }
        }

        return $this->redirect(['add-brand-by-template']);
    }

// Helper method to rebuild buffer from POST data
    private function rebuildBufferBrand($post) {
        $buffer = [];
        foreach ($post['type'] as $index => $name) {
            $buffer[] = [
                'name' => $name,
            ];
        }
        return $buffer;
    }

    public function checkExcelDuplicateBrands($post) {
        $normalized = [];
        $errors = [];

        // Check if name array exists
        if (!isset($post['type']) || !is_array($post['type'])) {
            return $errors;
        }

        foreach ($post['type'] as $index => $name) {
            // Skip empty names
            if (empty($name)) {
                continue;
            }

            $current = InventoryBrand::normalizeName($name);

            foreach ($normalized as $prevIndex => $prevName) {
                similar_text($current, $prevName, $percent);

                if ($percent >= 90) {
                    $errors[] = [
                        'row1' => $prevIndex + 1,
                        'row2' => $index + 1,
                        'name1' => $post['type'][$prevIndex],
                        'name2' => $name,
                        'percent' => round($percent, 1),
                    ];
                }
            }

            $normalized[$index] = $current;
        }

        return $errors;
    }

    /*     * ************* Model ************************ */

    public function actionModelList() {
        $searchModel = new InventoryModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('modelList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    //executive
    public function actionAddNewModel() {
        $model = new InventoryModel();
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->scannedFile) {
                $uploadPath = Yii::getAlias('@frontend/uploads/inventory-model/');

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $fileName = time() . '_' . $model->scannedFile->baseName . '.' . $model->scannedFile->extension;
                $filePath = $uploadPath . $fileName;

                if ($model->scannedFile->saveAs($filePath)) {
                    $model->image = $fileName;
                }
            }

            if ($model->save()) {
                FlashHandler::success("Success");
                return $this->redirect(['model-list']);
            }
        }

        return $this->renderAjax('_formModel', [
                    'model' => $model,
        ]);
    }

    public function actionViewModel($id) {
        $model = InventoryModel::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            // Store the old image filename
            $oldImage = $model->image;

            // Get the uploaded file instance
            $uploadedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($uploadedFile) {
                // New file uploaded
                $uploadPath = Yii::getAlias('@frontend/uploads/inventory-model/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $fileName = time() . '_' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                $filePath = $uploadPath . $fileName;

                if ($uploadedFile->saveAs($filePath)) {
                    // Delete old file if exists
                    if ($oldImage && file_exists($uploadPath . $oldImage)) {
                        unlink($uploadPath . $oldImage);
                    }
                    $model->image = $fileName;
                }
            } else {
                // No new file uploaded, keep the old image
                $model->image = $oldImage;
            }

            if ($model->save()) {
                FlashHandler::success("The detail has been updated successfully");
                return $this->redirect(['model-list']);
            }
        }

        return $this->renderAjax('_formModel', [
                    'model' => $model,
        ]);
    }

    public function actionGetModelImage($filename) {
        // Security: prevent directory traversal attacks
        $filename = basename($filename);

        $filePath = Yii::getAlias('@frontend/uploads/inventory-model/' . $filename);

        if (file_exists($filePath) && is_file($filePath)) {
            $mimeType = mime_content_type($filePath);
            return Yii::$app->response->sendFile($filePath, $filename, [
                        'mimeType' => $mimeType,
                        'inline' => true
            ]);
        }

        throw new \yii\web\NotFoundHttpException('File not found.');
    }

    public function actionAddByTemplateModel() {
        $model = new InventoryModel();

        if (Yii::$app->request->isPost) {
            $excelFile = \yii\web\UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['add-by-template-model']);
                }

                try {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();

                    $buffer = []; // Buffer to store data temporarily

                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $name = $data[1];
                        $brand = $data[2];
                        $desc = $data[3];
                        $group = $data[4];
                        $unitType = $data[5];
                        $stockonhand = $data[6];

                        if (empty($name) || empty($brand)) {
                            break;
                        }

                        $buffer[] = [
                            'name' => $name,
                            'brand' => $brand,
                            'desc' => $desc,
                            'group' => $group,
                            'unitType' => $unitType,
                            'stockonhand' => $stockonhand,
                        ];
                    }

                    if (!empty($buffer)) {
                        return $this->render('uploadToConfirmModel', ['buffer' => $buffer, 'errors' => []]);
                    } else {
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Name/Type' or 'Brand' column in your Excel file is not left blank.");
                        return $this->redirect(['add-by-template-model']);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['add-by-template-model']);
                }
            }
        }

        return $this->render('addByTemplateModel', [
                    'model' => $model,
        ]);
    }

    public function actionSaveModelDetails() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('InventoryModel');

            if (empty($post['type']) || empty($post['inventory_brand_id'])) {
                Yii::$app->session->setFlash('error', 'No model data to save.');
                return $this->redirect(['add-by-template-model']);
            }

            $errors = [];
            $successCount = 0;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Step 1: Check for duplicates within the Excel file
                $duplicates = $this->checkExcelDuplicateModels($post);

                if (!empty($duplicates)) {
                    $transaction->rollBack();

                    // Format error message
                    $errorMessage = 'Duplicate models found in Excel file:<br>';
                    foreach ($duplicates as $dup) {
                        $errorMessage .= "Row {$dup['row1']} and Row {$dup['row2']}: '{$dup['name1']}' + '{$dup['brand1']}' and '{$dup['name2']}' + '{$dup['brand2']}' are {$dup['percent']}% similar<br>";
                    }

                    Yii::$app->session->setFlash('error', $errorMessage);
                    return $this->render('uploadToConfirmModel', [
                                'buffer' => $this->rebuildBufferModel($post),
                                'errors' => []
                    ]);
                }

                foreach ($post['type'] as $index => $name) {
                    $brandName = $post['inventory_brand_id'][$index] ?? null;

                    if (empty($name) || empty($brandName)) {
                        continue;
                    }

                    // Step 2: Check if brand exists, if not create it
                    $brandId = $this->getOrCreateBrand($brandName);

                    if (!$brandId) {
                        $errors[$index] = "Failed to process brand: {$brandName}";
                        continue;
                    }

                    // Step 3: Check if model with same name and brand already exists in database
                    $similarCheck = InventoryModel::findSimilarModel($name, $brandName, 80);
                    if ($similarCheck['match']) {
                        $errors[$index] = "Similar model exists: '{$similarCheck['existing_name_model']}' with brand '{$similarCheck['existing_name_brand']}' ({$similarCheck['percent']}% match)";
                        continue;
                    }

                    // Create new model
                    $model = new InventoryModel();
                    $model->type = $name;
                    $model->inventory_brand_id = $brandId;
                    $model->description = $post['description'][$index] ?? null;
                    $model->group = $post['group'][$index] ?? null;
                    $model->unit_type = $post['unit_type'][$index] ?? null;
                    $model->total_stock_on_hand = $post['total_stock_on_hand'][$index] ?? null;
                    $model->active_sts = 2;

                    if ($model->save()) {
                        $successCount++;
                    } else {
                        $errors[$index] = "Failed to save: " . implode(', ', $model->getFirstErrors());
                    }
                }

                if (!empty($errors)) {
                    // Rollback and return to form with errors
                    $transaction->rollBack();
                    return $this->render('uploadToConfirmModel', [
                                'buffer' => $this->rebuildBufferModel($post),
                                'errors' => $errors
                    ]);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', "{$successCount} model(s) added successfully.");
                return $this->redirect(['model-list']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error saving models: ' . $e->getMessage());
                return $this->render('uploadToConfirmModel', [
                            'buffer' => $this->rebuildBufferModel($post),
                            'errors' => $errors
                ]);
            }
        }

        return $this->redirect(['add-by-template-model']);
    }

    /**
     * Get brand ID if exists, otherwise create new brand and return ID
     */
    private function getOrCreateBrand($brandName) {
        // Check if brand exists
        $brand = InventoryBrand::findOne(['name' => $brandName]);

        if ($brand) {
            return $brand->id;
        }

        // Create new brand
        $brand = new InventoryBrand();
        $brand->name = $brandName;
        $brand->active_sts = 2;

        if ($brand->save()) {
            return $brand->id;
        }

        return null;
    }

    /**
     * Helper method to rebuild buffer from POST data
     */
    private function rebuildBufferModel($post) {
        $buffer = [];
        foreach ($post['type'] as $index => $name) {
            $buffer[] = [
                'name' => $name,
                'brand' => $post['inventory_brand_id'][$index] ?? '',
                'desc' => $post['description'][$index] ?? '',
                'group' => $post['group'][$index] ?? '',
                'unitType' => $post['unit_type'][$index] ?? '',
                'stockonhand' => $post['total_stock_on_hand'][$index] ?? '',
            ];
        }
        return $buffer;
    }

    /**
     * Check for duplicate models within Excel data
     * Compares both name AND brand combined
     */
    public function checkExcelDuplicateModels($post) {
        $normalized = [];
        $errors = [];

        // Check if name and brand arrays exist
        if (!isset($post['type']) || !is_array($post['type'])) {
            return $errors;
        }

        if (!isset($post['inventory_brand_id']) || !is_array($post['inventory_brand_id'])) {
            return $errors;
        }

        foreach ($post['type'] as $index => $name) {
            $brand = $post['inventory_brand_id'][$index] ?? '';

            // Skip empty names or brands
            if (empty($name) || empty($brand)) {
                continue;
            }

            // Normalize the combination of name and brand
            $current = InventoryModel::normalizeName($name . ' ' . $brand);

            foreach ($normalized as $prevIndex => $prevNormalized) {
                similar_text($current, $prevNormalized, $percent);

                if ($percent >= 90) {
                    $errors[] = [
                        'row1' => $prevIndex + 1,
                        'row2' => $index + 1,
                        'name1' => $post['type'][$prevIndex],
                        'brand1' => $post['inventory_brand_id'][$prevIndex],
                        'name2' => $name,
                        'brand2' => $brand,
                        'percent' => round($percent, 1),
                    ];
                }
            }

            $normalized[$index] = $current;
        }

        return $errors;
    }

    /*     * ************* New Item ************************ */

    public function actionExecutivePreRequisitionPendingApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingInventory');

        return $this->render('prereqFormList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'execPendingPurchasing',
            'key' => 1
        ]);
    }

    public function actionExecutivePreRequisitionAllApplication() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allInventory');

        return $this->render('prereqFormList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'all',
                    'moduleIndex' => 'execAllPurchasing',
            'key' => 2
        ]);
    }

    public function actionExecutivePrerequisition() {
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

        return $this->render('applyPrereq', [
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

    public function actionProjcoorPreRequisitionPendingApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingApprovalInventoryProjcoor');

        return $this->render('prereqFormList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'projcoor',
                    'page' => 'newItem',
                    'key' => 1
        ]);
    }

    public function actionProjcoorReadyForProcurement() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingProcurementInventoryProjcoor');

        return $this->render('prereqFormList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'projcoor',
                    'page' => 'newItem',
                    'key' => 2
        ]);
    }

    public function actionConfirmOrderRequest() {
        $ids = Yii::$app->request->post('ids');
        $moduleIndex = Yii::$app->request->post('moduleIndex');

        $models = InventoryOrderRequest::find()
                ->where(['id' => $ids])
                ->with([
                    'inventoryDetail.supplier',
                    'inventoryModel.inventoryBrand'
                ])
                ->all();

        // Group by supplier
        $grouped = [];

        foreach ($models as $model) {

            $supplierId = $model->inventoryDetail->supplier->id ?? 0;
            $supplierName = $model->inventoryDetail->supplier->name ?? 'No Supplier';

            $grouped[$supplierId]['supplier_name'] = $supplierName;
            $grouped[$supplierId]['items'][] = $model;
        }

        return $this->render('confirmOrderRequest', [
                    'grouped' => $grouped,
                    'moduleIndex' => $moduleIndex
        ]);
    }

    public function actionCreatePurchaseOrders() {
        $ids = Yii::$app->request->post('ids');
        $moduleIndex = Yii::$app->request->post('moduleIndex');

        if (empty($ids)) {
            Yii::$app->session->setFlash('error', 'No order requests selected.');
            return $this->redirect(['pending-order-request-list', 'type' => $moduleIndex]);
        }

        $models = InventoryOrderRequest::find()
                ->where(['id' => $ids])
                ->with([
                    'inventoryDetail.supplier',
                    'inventoryDetail.currency',
                    'inventoryModel.inventoryBrand',
                ])
                ->all();

        // Group by supplier
        $grouped = [];
        foreach ($models as $model) {
            $supplierId = $model->inventoryDetail->supplier->id ?? 0;
            $grouped[$supplierId][] = $model;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($grouped as $supplierId => $items) {

                // --- Create Purchase Order ---
                $po = new InventoryPurchaseOrder();
                $po->po_date = date('Y-m-d');
                $po->supplier_id = $supplierId;
                $po->status = \frontend\models\RefInventoryStatus::STATUS_PoCreated;
                $po->currency_id = $items[0]->inventoryDetail->currency_id ?? null;
                $po->company_group = null;
                $po->total_qty = 0;
                $po->total_amount = 0;

                if (!$po->save()) {
                    throw new \Exception('Failed to create PO for supplier ID ' . $supplierId . ': ' . json_encode($po->errors));
                }

                // --- Merge duplicate items (same inventory_detail_id) ---
                $mergedItems = [];
                foreach ($items as $item) {
                    $detailId = $item->inventory_detail_id;
                    if (!isset($mergedItems[$detailId])) {
                        $mergedItems[$detailId] = [
                            'order_qty' => 0,
                            'detail' => $item->inventoryDetail,
                            'model' => $item->inventoryModel,
                            'requests' => [],
                        ];
                    }
                    $mergedItems[$detailId]['order_qty'] += $item->required_qty; // sum required_qty → order_qty
                    $mergedItems[$detailId]['requests'][] = $item;
                }

                $totalQty = 0;
                $totalAmount = 0;

                foreach ($mergedItems as $detailId => $merged) {
                    $detail = $merged['detail'];
                    $model = $merged['model'];

                    // --- Create PO Item ---
                    $poItem = new InventoryPurchaseOrderItem();
                    $poItem->inventory_po_id = $po->id;
                    $poItem->inventory_detail_id = $detailId;
                    $poItem->supplier_id = $supplierId;
                    $poItem->brand_id = $model->inventory_brand_id ?? null;
                    $poItem->model_id = $model->id ?? null;
                    $poItem->model_type = $model->type ?? null;
                    $poItem->model_group = $model->group ?? null;
                    $poItem->model_description = $model->description ?? null;
                    $poItem->order_qty = $merged['order_qty'];
                    $poItem->unit_type = $detail->unit_type ?? null;
                    $poItem->currency_id = $detail->currency_id ?? null;
                    $poItem->unit_price = $detail->unit_price ?? 0;
                    $poItem->discount_amt = null;
                    $poItem->total_price = $merged['order_qty'] * ($detail->unit_price ?? 0);
                    $poItem->received_qty = 0;
                    $poItem->remaining_qty = $merged['order_qty'];
                    $poItem->status = 0;
                    $poItem->department_code = $detail->department_code ?? null;

                    if (!$poItem->save()) {
                        throw new \Exception('Failed to create PO Item: ' . json_encode($poItem->errors));
                    }

                    $totalQty += $merged['order_qty'];
                    $totalAmount += $poItem->total_price;

                    // --- Update each Order Request ---
                    foreach ($merged['requests'] as $request) {
                        $request->inventory_po_item_id = $poItem->id;
                        $request->order_qty = $request->required_qty; // order_qty = required_qty
                        $request->status = 1;

                        if (!$request->save(false)) {
                            throw new \Exception('Failed to update Order Request ID ' . $request->id);
                        }
                    }
                }

                // --- Update PO totals ---
                $po->total_qty = $totalQty;
                $po->total_amount = $totalAmount;
                $po->net_amount = $totalAmount;
                $po->gross_amount = $totalAmount;

                if (!$po->save(false)) {
                    throw new \Exception('Failed to update PO totals: ' . json_encode($po->errors));
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Purchase Orders created successfully.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }

        return $this->redirect(['pending-order-request-list', 'type' => $moduleIndex]);
    }

    public function actionPendingOrderRequestList($type = null) {
        $searchModel = new InventoryOrderRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('orderRequestList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => $type,
        ]);
    }

    public function actionAllOrderRequestList($type = null) {
        $searchModel = new InventoryOrderRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('orderRequestList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => $type,
        ]);
    }

    public function actionProjcoorPreRequisitionAllApplication() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allInventoryProjcoor');

        return $this->render('prereqFormList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'all',
                    'moduleIndex' => 'projcoor',
                    'page' => 'newItem',
                    'key' => 3
        ]);
    }

    public function actionCreatePrerequisition($sourceModule = 'inventory', $referenceType = null, $referenceId = null, $selectedIds = null) {
        // ===== INITIAL MODELS FOR FIRST LOAD =====
        $master = new PrereqFormMaster();
        $items = [];
        $vmodel = [];

        // ===== PRE-FILL ITEMS IF SELECTED =====
        if ($selectedIds) {
            $ids = explode(',', $selectedIds);

            if ($referenceType === "bom") {
                foreach ($ids as $index => $id) {
                    $bomItem = \frontend\models\bom\BomDetails::findOne($id);
                    if ($bomItem) {
                        $item = new VPrereqFormMasterDetail();
                        $item->item_reference_type = "bom_detail";
                        $item->item_reference_id = $bomItem->id;
                        $item->model_name = $bomItem->model_type;
                        $item->brand_name = $bomItem->brand;
                        $item->item_description = $bomItem->description;
                        $item->quantity = $bomItem->qty;
                        // Add a temporary item_id for form rendering
                        $item->item_id = 'new_' . $index;
                        $items[] = $item;
                    }
                }
            }
        }

        // If no items were created, add one empty row
        if (empty($items)) {
            $item = new VPrereqFormMasterDetail();
            $item->item_id = 'new_0';
            $items[] = $item;
        }

        // Create vmodel array matching items
        $vmodel = $items;

        if ($referenceType === "bom") {
            $url = 'projcoor-pre-requisition-pending-approval';
        } else if ($referenceType === "exec") {
            $url = 'executive-pre-requisition-pending-approval';
        }

        if (Yii::$app->request->isPost) {

            \common\models\myTools\Mydebug::dumpFileW(Yii::$app->request->post());

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                // ===== MAIN CREATE LOGIC =====
                $master = PrereqFormMaster::createWithItems(Yii::$app->request->post(), $sourceModule, $referenceType, $referenceId);
                $transaction->commit();
                FlashHandler::success('Pre-Requisition Form created successfully!');
                return $this->redirect([$url]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $master->load(Yii::$app->request->post());

                // Reload items from POST with all submitted values
                $items = $this->reloadItemsFromPost(Yii::$app->request->post());
                $vmodel = $items;

                FlashHandler::err($e->getMessage());
            }
        }

        return $this->render('applyPrereq', [
                    'master' => $master,
                    'items' => $items,
                    'vmodel' => $vmodel,
                    'isUpdate' => false,
                    'isView' => false,
                    'moduleIndex' => $sourceModule,
                    'worklists' => [],
                    'hasSuperiorUpdate' => false,
                    'departmentList' => RefUserDepartments::getDropDownList(),
                    'supplierList' => InventorySupplier::getAllDropDownSupplierList(),
                    'brandList' => InventoryBrand::getAllDropDownBrandList(),
                    'currencyList' => RefCurrencies::getCurrencyActiveDropdownlist(),
        ]);
    }

    /**
     * Reload items from POST data after validation failure
     * @param array $postData
     * @return array
     */
    private function reloadItemsFromPost($postData) {
        $items = [];
        $postItems = $postData['VPrereqFormMasterDetail'] ?? [];

        foreach ($postItems as $index => $itemData) {
            $item = new VPrereqFormMasterDetail();

            // Load all attributes
            $item->setAttributes($itemData, false);

            // Preserve important fields
            $item->item_id = $itemData['id'] ?? 'new_' . $index;
            $item->item_reference_type = $itemData['item_reference_type'] ?? null;
            $item->item_reference_id = $itemData['item_reference_id'] ?? null;

            $items[$index] = $item;
        }

        return $items;
    }

    public function actionSendToProcurement($id, $page) {
        if ($page === "projcoor") {
            $url = 'projcoor-pre-requisition-all-application';
        } else if ($page === "exec") {
            $url = 'executive-pre-requisition-all-application';
        }

        $master = PrereqFormMaster::findOne($id);
        if (!$master) {
            FlashHandler::err('Record not found');
            return $this->redirect([$url]);
        }

        // Get items related to this master record
        $items = PrereqFormItem::find()
                ->where([
                    'prereq_form_master_id' => $id,
                    'status' => 0,
                    'is_deleted' => 0
                ])
                ->all();

        if (empty($items)) {
            FlashHandler::err('No items found to process');
            return $this->redirect([$url]);
        }

        // Use transaction for data integrity
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($items as $item) {
                // Find or create InventoryModel
                $model = InventoryModel::findOne([
                    'type' => $item->model_name,
                    'active_sts' => 2,
                    'inventory_brand_id' => $item->brand_id,
                ]);

                if (!$model) {
                    $model = new InventoryModel([
                        'type' => $item->model_name,
                        'group' => $item->model_group,
                        'description' => $item->item_description,
                        'active_sts' => 2,
                        'unit_type' => $item->model_unit_type,
                        'inventory_brand_id' => $item->brand_id
                    ]);

                    if (!$model->save()) {
                        throw new \Exception('Failed to save InventoryModel: ' . json_encode($model->errors));
                    }
                }

                // Find or create InventoryDetail
                $detail = InventoryDetail::findOne([
                    'supplier_id' => $item->supplier_id,
                    'brand_id' => $item->brand_id,
                    'model_id' => $model->id,
                    'active_sts' => 2
                ]);

                if (!$detail) {
                    $currency = RefCurrencies::findOne(['currency_code' => $item->currency]);
                    if (!$currency) {
                        throw new \Exception('Currency not found: ' . $item->currency);
                    }

                    $detail = new InventoryDetail([
                        'department_code' => $item->department_code,
                        'supplier_id' => $item->supplier_id,
                        'brand_id' => $item->brand_id,
                        'model_id' => $model->id,
                        'currency_id' => $currency->currency_id,
                        'unit_price' => $item->unit_price_approved,
                        'is_new' => 2,
                        'active_sts' => 2
                    ]);

                    if (!$detail->save()) {
                        throw new \Exception('Failed to save InventoryDetail: ' . json_encode($detail->errors));
                    }
                }

                // Create order request
                $orderRequest = new InventoryOrderRequest();
                $orderRequest->inventory_detail_id = $detail->id;
                $orderRequest->inventory_model_id = $detail->model_id;
                $orderRequest->reference_type = $item->reference_type;
                $orderRequest->reference_id = $item->reference_id;
                $orderRequest->required_qty = $item->quantity_approved;

                if (!$orderRequest->save()) {
                    throw new \Exception('Failed to save InventoryOrderRequest: ' . json_encode($orderRequest->errors));
                }

                // Update BOM detail if reference type is bom_detail
                if ($item->reference_type === "bom_detail") {
                    $bomDetail = \frontend\models\bom\BomDetails::findOne($item->reference_id);

                    if (!$bomDetail) {
                        throw new \Exception('BOM Detail not found: ' . $item->reference_id);
                    }

                    $bomDetail->inventory_model_id = $model->id;
                    $bomDetail->inventory_brand_id = $item->brand_id;
                    $bomDetail->model_type = $item->model_name;
                    $bomDetail->brand = $item->brand_id;
                    $bomDetail->description = $item->item_description;
                    $bomDetail->qty = $orderRequest->required_qty;
                    $bomDetail->is_finalized = 2;
                    $bomDetail->inventory_sts = 2;

                    if (!$bomDetail->save(false)) {
                        throw new \Exception('Failed to update BOM Detail: ' . json_encode($bomDetail->errors));
                    }
                }
            }

            // Update master record AFTER all items processed
            $master->inventory_flag = 1;
            if (!$master->save(false)) {
                throw new \Exception('Failed to update inventory flag: ' . json_encode($master->getErrors()));
            }

            $transaction->commit();
            FlashHandler::success('Successfully sent to procurement');
            return $this->redirect(['order-request-list', 'type' => $page]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err('Error: ' . $e->getMessage());
            return $this->redirect([$url]);
        }
    }

    public function actionAjaxAddFormItem($key, $masterId, $moduleIndex, $hasSuperiorUpdate) {
        $formItem = new VPrereqFormMasterDetail();
        $master = PrereqFormMaster::findOne($masterId);
        $isUpdate = !empty($masterId);

        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        $supplierList = InventorySupplier::getAllDropDownSupplierList();
        $brandList = InventoryBrand::getAllDropDownBrandList();
        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();

        return $this->renderPartial('_prereq_edit_cells', [
                    'model' => $formItem,
                    'index' => $key,
                    'master' => $master,
                    'isUpdate' => $isUpdate,
                    'isView' => false,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => [],
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'currencyList' => $currencyList,
        ]);
    }

    public function actionInventoryCheckDuplicate($supplier, $brand, $model, $department) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $exists = PrereqFormItem::checkInventoryDuplicate(
                $department,
                $supplier,
                $brand,
                $model
        );

        return ['exists' => $exists];
    }

    public function actionViewPreRequisition($id, $moduleIndex) {
        $master = PrereqFormMaster::findOne($id);

        if (!$master) {
            FlashHandler::err('Record not found');
            return $this->redirect(['index']);
        }

        $vmodel = VPrereqFormMasterDetail::find()
                ->where(['master_id' => $id])
                ->andWhere(['is_deleted' => 0])  // ADD THIS - only get active items
                ->with(['items'])
                ->all();

        // Get items - ONLY NON-DELETED ONES
        $items = PrereqFormItem::find()
                ->where(['prereq_form_master_id' => $id])
                ->andWhere(['is_deleted' => 0])  // ADD THIS - crucial!
                ->orderBy(['id' => SORT_DESC])  // Get latest versions
                ->all();

        // Build worklists
        $worklists = [];
        $hasSuperiorUpdate = false;

        foreach ($items as $item) {
            $worklist = PrereqFormItemWorklist::findOne([
                'prereq_form_master_id' => $id,
                'prereq_form_item_id' => $item->id
            ]);

            if (!$worklist) {
                $worklist = new PrereqFormItemWorklist();
                $worklist->prereq_form_master_id = $id;
                $worklist->prereq_form_item_id = $item->id;
            } else if ($worklist->responded_by !== null) {
                $hasSuperiorUpdate = true;
            }

            $worklists[$item->id] = $worklist;
        }

        // Dropdown data
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        $supplierList = \frontend\models\inventory\InventorySupplier::getAllDropDownSupplierList();
        $brandList = \frontend\models\inventory\InventoryBrand::getAllDropDownBrandList();
        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();

        return $this->render('viewPrereq', [
                    'master' => $master,
                    'items' => $items,
                    'vmodel' => $vmodel,
                    'isView' => true,
                    'isUpdate' => false,
                    'module' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'currencyList' => $currencyList,
//                    'page' => $page
        ]);
    }

    /**
     * Update existing PRF (before superior approval)
     */
    public function actionUpdatePreRequisition($id, $moduleIndex) {
        $master = PrereqFormMaster::findOne($id);
        if (!$master) {
            FlashHandler::err('Record not found');
            return $this->redirect(['executive-pre-requisition-pending-approval']);
        }

        $vmodel = VPrereqFormMasterDetail::find()
                ->where(['master_id' => $id])
                ->all();

        $items = $master->prereqFormItems;
        $worklists = [];
        $hasSuperiorUpdate = false;

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // ===== UPDATE MASTER =====
                $postMaster = Yii::$app->request->post('PrereqFormMaster');
                $master->date_of_material_required = $postMaster['date_of_material_required'] ?? null;
                $master->updated_by = Yii::$app->user->id;
                $master->updated_at = date('Y-m-d H:i:s');

                if (!$master->save()) {
                    throw new \Exception('Master update failed: ' . json_encode($master->getErrors()));
                }

                // ===== MARK OLD ITEMS AS DELETED =====
                PrereqFormItem::updateAll(
                        ['is_deleted' => 1],
                        ['prereq_form_master_id' => $id, 'is_deleted' => 0]
                );

                // ===== SAVE NEW/UPDATED ITEMS =====
                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);
                $master->saveItems($master->id, $postItems, false, $moduleIndex);

                $transaction->commit();
                FlashHandler::success('Purchase Requisition Form updated successfully!');
                return $this->redirect(['executive-pre-requisition-pending-approval']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err('Failed to update form: ' . $e->getMessage());
                Yii::error('Update PRF Error: ' . $e->getMessage(), __METHOD__);
            }
        }

        // ===== DROPDOWN DATA =====
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        $supplierList = InventorySupplier::getAllDropDownSupplierList();
        $brandList = InventoryBrand::getAllDropDownBrandList();
        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();

        return $this->render('updatePrereq', [
                    'master' => $master,
                    'items' => $items,
                    'vmodel' => $vmodel,
                    'isUpdate' => true,
                    'isView' => false,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'currencyList' => $currencyList,
        ]);
    }

    public function actionProceedToProcurement($id) {
        $prereqMaster = PrereqFormMaster::findOne($id);
        if (!$prereqMaster) {
            FlashHandler::err('Purchase requisition form not found');
            return $this->redirect(['executive-pre-requisition-all-application']);
        }

        $items = PrereqFormItem::find()->where(['prereq_form_master_id' => $prereqMaster->id, 'is_deleted' => 0, 'status' => 0])->all();

        // ===== GROUP BY SUPPLIER =====
        $itemsBySupplier = [];
        foreach ($items as $item) {
            if (empty($item->supplier_id)) {
                \Yii::warning("Item {$item->id} has no supplier id, skipping");
                continue;
            }
            $itemsBySupplier[$item->supplier_id][] = $item;
        }

        if (empty($itemsBySupplier)) {
            FlashHandler::err('No valid items with department codes found');
            return $this->redirect(['executive-pre-requisition-all-application']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $hasCreated = false;
            foreach ($itemsBySupplier as $supplierId => $supplierItems) {

                $purchaseRequest = new InventoryPurchaseRequest();
                $purchaseRequest->inventory_supplier_id = $supplierId;
                $purchaseRequest->source_type = 1; // new
                $purchaseRequest->source_id = $prereqMaster->id;
                $purchaseRequest->po_status = 1; // not yet PO

                if (!$purchaseRequest->save()) {
                    throw new \Exception(json_encode($purchaseRequest->errors));
                }

                $sourceType = 1;
                foreach ($supplierItems as $item) {
                    if (!$purchaseRequest->createPurchaseRequestItem($item, $sourceType)) {
                        throw new \Exception('Failed to create PR item');
                    }
                }

                $hasCreated = true;
            }

            if ($hasCreated) {
                $prereqMaster->inventory_flag = 1;

                if (!$prereqMaster->save(false)) {
                    throw new \Exception('Failed to update inventory flag: ' . json_encode($prereqMaster->getErrors()));
                }

                $transaction->commit();
                FlashHandler::success("Successfully created records");
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err('Failed to create reorder records: ' . $e->getMessage());
        }

        return $this->redirect(['executive-new-item-ready-for-po-list']);
    }

    public function actionPo($type) {
        $searchModel = new \frontend\models\inventory\InventoryPurchaseOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('_poList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => $type
        ]);
    }

    /*     * ************* Purchase Order ************************ */
    public function actionManagePo($id, $moduleIndex) {
        $po = InventoryPurchaseOrder::findOne($id);
        if (!$po) {
            throw new NotFoundHttpException('Purchase Order not found');
        }

//        $purchaseOrderItems = $po->inventoryPurchaseOrderItems;
        $purchaseOrderItems = InventoryPurchaseOrderItem::find()->where(['inventory_po_id' => $po->id, 'is_deleted' => 0])->all();

        if ($moduleIndex === 'execPendingPurchasing') {
            $url = 'po?type=execPendingPurchasing';
        } else if ($moduleIndex === 'execAllPurchasing') {
            $url = 'po?type=execAllPurchasing';
        } else if ($moduleIndex === 'execPendingReceiving') {
            $url = 'po?type=execPendingReceiving';
        } else if ($moduleIndex === 'asistPendingPurchasing') {
            $url = 'po?type=assistPendingPurchasing';
        } else if ($moduleIndex === 'asistAllPurchasing') {
            $url = 'po?type=assistAllPurchasing';
        } else if ($moduleIndex === 'asistReceiving') {
            $url = 'po?type=execPending';
        }

        $currencies = RefCurrencies::find()->select(['currency_id', 'currency_code', 'currency_name', 'currency_sign'])->asArray()->all();
        $currencyList = RefCurrencies::getActiveDropdownlist_by_id();
        $companyGroupList = \frontend\models\common\RefCompanyGroupList::getDropDownList();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            try {
                $po->updatePoProcess(Yii::$app->request->post('InventoryPurchaseOrder'), Yii::$app->request->post('POItem'));
                Yii::$app->session->setFlash('success', 'Purchase Order updated successfully.');
                return $this->redirect([$url]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('_issuePoQuotationForm', [
                    'po' => $po,
                    'purchaseOrderItems' => $purchaseOrderItems,
                    'currencies' => $currencies,
                    'currencyList' => $currencyList,
                    'companyGroupList' => $companyGroupList,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    public function actionViewPoItemDetail($poItemId, $moduleIndex) {
        $poItem = InventoryPurchaseOrderItem::findOne($poItemId);
        $orderRequests = $poItem->inventoryOrderRequests;

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (isset($data['InventoryOrderRequests'])) {
                    foreach ($data['InventoryOrderRequests'] as $itemData) {
                        $request = InventoryOrderRequest::findOne($itemData['id']);
                        if ($request === null) {
                            throw new \Exception("Order request ID {$itemData['id']} not found.");
                        }

                        if ($itemData['removed'] == 1) {
                            $request->inventory_po_item_id = null;
                            $request->order_qty = null;
                            $request->status = 0;
                        } else {
                            $request->order_qty = $itemData['order_qty'];
                        }

                        if (!$request->save(false)) {
                            throw new \Exception("Failed to save order request ID {$itemData['id']}.");
                        }
                    }
                }

                $poItem->order_qty = $data['total_order_qty'];
                $poItem->remaining_qty = $data['total_order_qty'] - $poItem->received_qty;
                $poItem->status = ($poItem->order_qty == $poItem->received_qty) ? 1 : 0;

                if (!$poItem->save(false)) {
                    throw new \Exception("Failed to save PO item ID {$poItem->id}.");
                }

                $po = InventoryPurchaseOrder::findOne($poItem->inventory_po_id);
                if ($po === null) {
                    throw new \Exception("Purchase order not found for PO item ID {$poItem->id}.");
                }

                $hasPendingReceive = InventoryPurchaseOrderItem::find()
                        ->where(['inventory_po_id' => $po->id, 'status' => 0, 'is_deleted' => 0])
                        ->exists();

                if (!$hasPendingReceive) {
                    $po->status = \frontend\models\RefInventoryStatus::STATUS_FullyReceived;
                    if (!$po->save(false)) {
                        throw new \Exception("Failed to update PO status for PO ID {$po->id}.");
                    }
                }

                $poItem->updateInventoryQtyPendingReceipt();

                $transaction->commit();
                FlashHandler::success("The quantity has been updated successfully");
                return $this->redirect(['manage-po', 'id' => $poItem->inventory_po_id, 'moduleIndex' => $moduleIndex]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::error("Update failed: " . $e->getMessage());
            }
        }

        return $this->renderAjax('_formPoItemDetail', [
                    'poItem' => $poItem,
                    'orderRequests' => $orderRequests,
        ]);
    }

    public function actionSearchInventoryItems() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = Yii::$app->request->get('q', '');
        $supplierId = (int) Yii::$app->request->get('supplier_id', 0);

        if (strlen($q) < 2 || $supplierId === 0)
            return [];

        $items = \frontend\models\inventory\InventoryDetail::find()
                ->joinWith(['supplier', 'model.inventoryBrand'])
                ->where(['inventory_detail.supplier_id' => $supplierId]) // lock to this PO's supplier
                ->andWhere(['or',
                    ['like', 'inventory_detail.code', $q],
                    ['like', 'inventory_model.type', $q],
                    ['like', 'inventory_model.group', $q],
                    ['like', 'inventory_model.description', $q],
                    ['like', 'inventory_brand.name', $q],
                    ['like', 'inventory_brand.code', $q],
                    ['like', 'inventory_supplier.name', $q], // ← search by supplier name
                    ['like', 'inventory_supplier.code', $q], // ← search by supplier code
                ])
                ->andWhere(['inventory_detail.active_sts' => 2])
                ->limit(30)
                ->all();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'inventory_detail_id' => $item->id,
                'code' => $item->code,
                'brand_id' => $item->model->inventoryBrand->id ?? null,
                'brand_name' => $item->model->inventoryBrand->name ?? '-',
                'model_type' => $item->model->type ?? '-',
                'model_group' => $item->model->group ?? '',
                'model_description' => $item->model->description ?? '',
                'department_code' => $item->department_code ?? '',
                'unit_type' => $item->model->unit_type ?? '',
                'unit_price' => number_format((float) ($item->unit_price ?? 0), 2, '.', ''),
                'supplier_name' => $item->supplier->name ?? '-', // ← return supplier name
            ];
        }

        return $result;
    }

    public function actionGetQuotation($filename) {
        // Folder where quotation files are stored
        $filePath = Yii::getAlias('@frontend/uploads/inventory-po-quotation/' . $filename);

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('The requested file does not exist.');
        }

        return Yii::$app->response->sendFile(
                        $filePath,
                        $filename,
                        [
                            'inline' => true, // open in browser instead of force download
                        ]
                );
    }

    public function actionGetPo($id) {
        try {
            $uploadDir = Yii::getAlias('@frontend/uploads/inventory-po-attachment/');
            \yii\helpers\FileHelper::createDirectory($uploadDir);

            // Get the master record
            $po = \frontend\models\inventory\InventoryPurchaseOrder::findOne($id);
            if (!$po) {
                throw new \yii\web\NotFoundHttpException('Record not found');
            }

            $filename = $po->po_no . '.pdf';
            $completePath = $uploadDir . $filename;

            // Generate new PDF
//            $items = $po->inventoryPurchaseOrderItems;
            $items = InventoryPurchaseOrderItem::find()->where(['inventory_po_id' => $po->id, 'is_deleted' => 0])->all();

            $po->amountWords = $this->convertNumberToWords((float) $po->gross_amount);

            $mpdf = $this->generatePoPdf($po, $items);
            $mpdf->Output($completePath, 'F');

            return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
        } catch (\Exception $e) {
            \Yii::error('PDF Generation Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            throw new \yii\web\ServerErrorHttpException('Unable to generate or retrieve PDF file: ' . $e->getMessage());
        }
    }

    private function generatePoPdf($po, $items) {
        ini_set("pcre.backtrack_limit", "10000000");
        ini_set("memory_limit", "1024M");

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 80,
            'margin_bottom' => 5,
            'margin_header' => 10,
            'margin_footer' => 10,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
        ]);

        // Set HTML Header
        $headerHtml = $this->renderPartial('_poHeaderPdf', [
            'po' => $po,
        ]);
        $mpdf->SetHTMLHeader($headerHtml);

        // Body content
        $htmlBody = $this->renderPartial("_poFormPdf", [
            'po' => $po,
            'items' => $items,
        ]);

        // Write HTML in chunks
        $this->writeHtmlInChunks($mpdf, $htmlBody);

        // Get footer HTML
        $footerHtml = $this->renderPartial('_poFooterPdf', [
            'po' => $po,
        ]);

        // Add spacer to push footer to bottom if needed
        $mpdf->WriteHTML('
        <div style="page-break-after: avoid;">
            <div style="height: 100mm;"></div>
            <div style="position: fixed; left: 0; right: 0; padding: 0 5mm;">
                ' . $footerHtml . '
            </div>
        </div>
    ');

        return $mpdf;
    }

    private function writeHtmlInChunks($mpdf, $html) {
        $chunkSize = 500000; // 500KB chunks
        $htmlLength = strlen($html);

        if ($htmlLength <= $chunkSize) {
            $mpdf->WriteHTML($html);
            return;
        }

        $chunks = str_split($html, $chunkSize);

        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }
    }

    /**
     * Convert number to words (Ringgit Malaysia)
     * @param float $number
     * @return string
     */
    private function convertNumberToWords($number) {
        $number = number_format($number, 2, '.', '');
        list($ringgit, $sen) = explode('.', $number);

        $ringgitWords = $this->numberToWords((int) $ringgit);
        $senWords = $this->numberToWords((int) $sen);

        $result = '';

        if ($ringgit > 0) {
            $result .= $ringgitWords;
            if ($sen > 0) {
                $result .= ' AND ' . $senWords . ' SEN';
            }
            $result .= ' ONLY';
        } else {
            if ($sen > 0) {
                $result = $senWords . ' SEN ONLY';
            } else {
                $result = 'ZERO ONLY';
            }
        }

        return $result;
    }

    /**
     * Convert number to English words
     * @param int $number
     * @return string
     */
    private function numberToWords($number) {
        $ones = array(
            0 => 'ZERO', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR',
            5 => 'FIVE', 6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE',
            10 => 'TEN', 11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN',
            14 => 'FOURTEEN', 15 => 'FIFTEEN', 16 => 'SIXTEEN', 17 => 'SEVENTEEN',
            18 => 'EIGHTEEN', 19 => 'NINETEEN'
        );

        $tens = array(
            2 => 'TWENTY', 3 => 'THIRTY', 4 => 'FORTY', 5 => 'FIFTY',
            6 => 'SIXTY', 7 => 'SEVENTY', 8 => 'EIGHTY', 9 => 'NINETY'
        );

        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[floor($number / 10)] . ($number % 10 != 0 ? ' ' . $ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return $ones[floor($number / 100)] . ' HUNDRED' . ($number % 100 != 0 ? ' ' . $this->numberToWords($number % 100) : '');
        } elseif ($number < 1000000) {
            return $this->numberToWords(floor($number / 1000)) . ' THOUSAND' . ($number % 1000 != 0 ? ' ' . $this->numberToWords($number % 1000) : '');
        } elseif ($number < 1000000000) {
            return $this->numberToWords(floor($number / 1000000)) . ' MILLION' . ($number % 1000000 != 0 ? ' ' . $this->numberToWords($number % 1000000) : '');
        }

        return (string) $number;
    }

    /*     * ************* Receiving ************************ */

    public function actionUpdateReceiveItems($id) {
        $po = \frontend\models\inventory\InventoryPurchaseOrder::findOne($id);
//        $poItems = $po->inventoryPurchaseOrderItems;
        $poItems = InventoryPurchaseOrderItem::find()->where(['inventory_po_id' => $po->id, 'is_deleted' => 0])->all();

        if (Yii::$app->request->post()) {
            $postDataReceive = Yii::$app->request->post('receive');

            // Filter function to remove items with 0 or empty quantities
            function filterPostData($postDataReceive) {
                $filtered = [];
                foreach ($postDataReceive as $key => $items) {
                    foreach ($items as $itemId => $data) {
                        if (isset($data['new_receive_qty']) &&
                                $data['new_receive_qty'] !== "0" &&
                                $data['new_receive_qty'] !== "" &&
                                $data['new_receive_qty'] > 0) {
                            $filtered[$key][$itemId] = $data;
                        }
                    }
                }
                return $filtered;
            }

            $filteredDataReceive = filterPostData($postDataReceive);

            if (!empty($filteredDataReceive)) {
                $combinedData = [
                    'receive' => $filteredDataReceive
                ];
                Yii::$app->session->set('postData', $combinedData);
                return $this->redirect(['confirm-and-upload-attachment', 'id' => $po->id]);
            }

            \common\models\myTools\FlashHandler::err("No item has been selected");
        }

        return $this->render('_updateReceiveItem', [
                    'po' => $po,
                    'poItems' => $poItems,
        ]);
    }

    public function actionConfirmAndUploadAttachment($id) {
        $postData = Yii::$app->session->get('postData');
        $po = \frontend\models\inventory\InventoryPurchaseOrder::findOne($id);
        $attachments = new \frontend\models\inventory\InventoryPurchaseOrderItemDoc();

        if (Yii::$app->request->post()) {
            $batch = new \frontend\models\inventory\InventoryPurchaseOrderReceiveBatch();
            $batch->inventory_po_id = $po->id;

            try {
                if ($batch->processOrderReceive($po, $postData, Yii::$app->request->post())) {
                    Yii::$app->session->remove('postData');
                    \common\models\myTools\FlashHandler::success("Order received successfully with attachments.");
                    return $this->redirect(['executive-pending-receive-purchase-order']);
                }
            } catch (\Exception $e) {
                \common\models\myTools\FlashHandler::err("Error: " . $e->getMessage());
            }
        }

        // Flatten the nested array for easier display
        $flattenedData = [];
        if (isset($postData['receive'])) {
            foreach ($postData['receive'] as $key => $items) {
                foreach ($items as $itemId => $data) {
                    $flattenedData[] = $data;
                }
            }
        }

        return $this->render('confirmOrderReceive', [
                    'postData' => $flattenedData,
                    'po' => $po,
                    'attachments' => $attachments
        ]);
    }

    public function actionExecutiveReceivingHistory() {
        $searchModel = new \frontend\models\inventory\inventoryPurchaseOrderReceiveBatchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('receivingBatchList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => "exec"
        ]);
    }

    public function actionViewBatchDetails($id, $moduleIndex) {
        $batch = \frontend\models\inventory\InventoryPurchaseOrderReceiveBatch::findOne($id);
        $items = $batch->inventoryPurchaseOrderItemReceives;
        $documents = $batch->inventoryPurchaseOrderItemDocs;

        return $this->render('receivingItemDetail', [
                    'batch' => $batch,
                    'items' => $items,
                    'documents' => $documents,
                    'moduleIndex' => $moduleIndex
        ]);
    }

    public function actionDownloadAttachment($id) {
        $doc = \frontend\models\inventory\InventoryPurchaseOrderItemDoc::findOne($id);

        if (!$doc) {
            throw new \yii\web\NotFoundHttpException('Document not found.');
        }

        $filePath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/') . $doc->filename;

        if (!file_exists($filePath)) {
            Yii::$app->session->setFlash('error', "File not found: " . $doc->filename);
            return $this->redirect(Yii::$app->request->referrer ?: ['executive-pending-receive-purchase-order']);
        }

        $extension = pathinfo($doc->filename, PATHINFO_EXTENSION);
        $downloadName = 'Batch_' . $doc->receive_batch_id . '_' .
                str_replace(' ', '_', $doc->document_no) . '.' . $extension;

        // Disable layout
        $this->layout = false;

        // Clear all output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Send the file
        Yii::$app->response->sendFile($filePath, $downloadName, ['inline' => false])->send();

        // Terminate the application
        Yii::$app->end();
    }

    public function actionGetPoAttachment($filename) {
        $filePath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/') . $filename;

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException('File not found.');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Set MIME type for different file types
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        // For PDF and images, display inline; for others, force download
        $inline = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);

        return Yii::$app->response->sendFile($filePath, $filename, [
                    'mimeType' => $mimeType,
                    'inline' => $inline
        ]);
    }

    /**
     * Finds the InventorySupplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InventorySupplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = InventorySupplier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
