<?php

namespace frontend\controllers\inventory;

use Yii;
use frontend\models\inventory\cmms\InventorySupplierCmms;
use frontend\models\inventory\cmms\InventorySupplierCmmsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\inventory\cmms\InventoryBrandCmms;
use frontend\models\inventory\cmms\InventoryModelCmms;
use frontend\models\inventory\cmms\InventoryBrandCmmsSearch;
use frontend\models\inventory\cmms\InventoryModelCmmsSearch;
use frontend\models\inventory\cmms\InventoryDetailCmms;
use frontend\models\inventory\cmms\InventoryDetailCmmsSearch;
use frontend\models\inventory\cmms\VInventoryDetailCmmsSearch;

/**
 * InventoryCmmsController implements the CRUD actions for InventorySupplierCmms model.
 */
class InventoryCmmsController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/inventory/cmms/');
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

    /*     * ************* Supplier ************************ */

    public function actionSupplierList() {
        $searchModel = new InventorySupplierCmmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('supplierList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddNewSupplier() {
        $model = new InventorySupplierCmms();

        if ($model->load(Yii::$app->request->post())) {
            $supplierCode = $model->generateSupplierCode();
            $model->code = $supplierCode;
            $model->save();
            FlashHandler::success("Success");
            return $this->redirect(['supplier-list']);
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

    /*     * ************* Brand ************************ */

    public function actionBrandList() {
        $searchModel = new InventoryBrandCmmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('brandList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddNewBrand() {
        $model = new InventoryBrandCmms();

        if ($model->load(Yii::$app->request->post())) {
            $brandCode = $model->generateBrandCode();
            $model->code = $brandCode;
            $model->save();
            FlashHandler::success("Success");
            return $this->redirect(['brand-list']);
        }

        return $this->renderAjax('_formBrand', [
                    'model' => $model,
        ]);
    }

    public function actionViewBrand($id) {
        $model = InventoryBrandCmms::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("The detail has been updated successfully");
            return $this->redirect(['brand-list']);
        }

        return $this->renderAjax('_formBrand', [
                    'model' => $model,
        ]);
    }

    /*     * ************* Model ************************ */

    public function actionModelList() {
        $searchModel = new InventoryModelCmmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('modelList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddNewModel() {
        $model = new InventoryModelCmms();
        if ($model->load(Yii::$app->request->post())) {
            $modelCode = $model->generateModelCode();
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($model->scannedFile) {
                $uploadPath = Yii::getAlias('@frontend/uploads/inventory-cmms-model/');

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $fileName = time() . '_' . $model->scannedFile->baseName . '.' . $model->scannedFile->extension;
                $filePath = $uploadPath . $fileName;

                if ($model->scannedFile->saveAs($filePath)) {
                    $model->image = $fileName;
                }
            }

            $model->code = $modelCode;
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
        $model = InventoryModelCmms::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            // Store the old image filename
            $oldImage = $model->image;

            // Get the uploaded file instance
            $uploadedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($uploadedFile) {
                // New file uploaded
                $uploadPath = Yii::getAlias('@frontend/uploads/inventory-cmms-model/');
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

        $filePath = Yii::getAlias('@frontend/uploads/inventory-cmms-model/' . $filename);

        if (file_exists($filePath) && is_file($filePath)) {
            $mimeType = mime_content_type($filePath);
            return Yii::$app->response->sendFile($filePath, $filename, [
                        'mimeType' => $mimeType,
                        'inline' => true
            ]);
        }

        throw new \yii\web\NotFoundHttpException('File not found.');
    }

    /*     * ************* Item Detail ************************ */

    public function actionItemList() {
        $searchModel = new VInventoryDetailCmmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('itemList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    //before check duplicate
//    public function actionAddNewItem() {
//        $supplier = new InventorySupplierCmms();
//        $brand = new InventoryBrandCmms();
//        $modelClass = new InventoryModelCmms();
//        $supplierList = $supplier->getAllDropDownSupplierList();
//        $brandList = $brand->getAllDropDownBrandList();
//        $modelList = $modelClass->getAllDropDownModelList();
//
//        $itemList = [new InventoryDetailCmms()];
//
//        if (Yii::$app->request->post()) {
//            $itemList = [];
//            $postItems = Yii::$app->request->post('InventoryDetailCmms', []);
//
//            foreach ($postItems as $i => $itemData) {
//                $item = new InventoryDetailCmms();
//                $item->load(['InventoryDetailCmms' => $itemData]);
//                $itemList[] = $item;
//            }
//
//            $isValid = true;
//            foreach ($itemList as $item) {
//                if (!$item->validate()) {
//                    $isValid = false;
//                }
//            }
//
//            if ($isValid) {
//                $transaction = Yii::$app->db->beginTransaction();
//                try {
//                    foreach ($itemList as $item) {
//                        $supplier = InventorySupplierCmms::findOne(['code' => $item->supplier_cmms_code]);
//                        $brand = InventoryBrandCmms::findOne(['code' => $item->brand_cmms_code]);
//                        $model = InventoryModelCmms::findOne(['code' => $item->model_cmms_code]);
//
//                        if (!$item->save(false)) {
//                            throw new \Exception("Failed to save item");
//                        }
//                    }
//
//                    $transaction->commit();
//                    FlashHandler::success("Successfully saved all items");
//                    return $this->redirect(['item-list']);
//                } catch (\Exception $e) {
//                    $transaction->rollBack();
//                    FlashHandler::err("Failed to save items: " . $e->getMessage());
//                }
//            } else {
//                FlashHandler::err("Please correct the validation errors");
//            }
//        }
//
//        return $this->render('createItemList', [
//                    'itemList' => $itemList,
//                    'supplierList' => $supplierList,
//                    'brandList' => $brandList,
//                    'modelList' => $modelList,
//        ]);
//    }

    public function actionAddNewItem() {
        $supplier = new InventorySupplierCmms();
        $brand = new InventoryBrandCmms();
        $modelClass = new InventoryModelCmms();
        $supplierList = $supplier->getAllDropDownSupplierList();
        $brandList = $brand->getAllDropDownBrandList();
        $modelList = $modelClass->getAllDropDownModelList();
        $itemList = [new InventoryDetailCmms()];

        if (Yii::$app->request->post()) {
            $itemList = [];
            $postItems = Yii::$app->request->post('InventoryDetailCmms', []);

            foreach ($postItems as $i => $itemData) {
                $item = new InventoryDetailCmms();
                $item->load(['InventoryDetailCmms' => $itemData]);
                $itemList[] = $item;
            }

            $isValid = true;

            // Validate each item
            foreach ($itemList as $index => $item) {
                if (!$item->validate()) {
                    $isValid = false;
                }

                // Check for duplicates in database
                $existing = InventoryDetailCmms::find()
                        ->where([
                            'supplier_cmms_code' => $item->supplier_cmms_code,
                            'brand_cmms_code' => $item->brand_cmms_code,
                            'model_cmms_code' => $item->model_cmms_code,
                        ])
                        ->one();

                if ($existing) {
                    $supplier = InventorySupplierCmms::findOne(['code' => $item->supplier_cmms_code]);
                    $brand = InventoryBrandCmms::findOne(['code' => $item->brand_cmms_code]);
                    $model = InventoryModelCmms::findOne(['code' => $item->model_cmms_code]);

                    $supplierName = $supplier ? $supplier->name : $item->supplier_cmms_code;
                    $brandName = $brand ? $brand->name : $item->brand_cmms_code;
                    $modelName = $model ? $model->description : $item->model_cmms_code;

                    $item->addError(
                            'supplier_cmms_code',
                            "Item already exists in inventory: {$supplierName} - {$brandName} - {$modelName}"
                    );
                    $isValid = false;
                }

                // Check for duplicates within the form submission
                foreach ($itemList as $compareIndex => $compareItem) {
                    if ($index !== $compareIndex &&
                            $item->supplier_cmms_code === $compareItem->supplier_cmms_code &&
                            $item->brand_cmms_code === $compareItem->brand_cmms_code &&
                            $item->model_cmms_code === $compareItem->model_cmms_code) {

                        $item->addError(
                                'supplier_cmms_code',
                                "Duplicate entry found in row " . ($compareIndex + 1)
                        );
                        $isValid = false;
                        break;
                    }
                }
            }

            if ($isValid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($itemList as $item) {
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
        ]);
    }

    /**
     * Deletes an existing InventorySupplierCmms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the InventorySupplierCmms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InventorySupplierCmms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = InventorySupplierCmms::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
