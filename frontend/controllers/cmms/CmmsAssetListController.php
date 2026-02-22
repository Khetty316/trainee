<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\CmmsAssetListSearch;
use frontend\models\cmms\CmmsPartList;
use frontend\models\cmms\VwCmmsAssetList;
use frontend\models\cmms\CmmsAssetFaults;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * CmmsAssetListController implements the CRUD actions for CmmsAssetList model.
 */
class CmmsAssetListController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CmmsAssetList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CmmsAssetListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CmmsAssetList model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $vModel = VwCmmsAssetList::find()
//            ->where(['not', ['machine_detail_id' => null]])
            ->where(['id' => $id])
            ->all();
        
        $faults = CmmsAssetFaults::find()
                ->where(['asset_id' => $id])
                ->indexBy('id')
                ->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'faults' => $faults,
            'vModel' => $vModel
        ]);
    }

    /**
     * Creates a new CmmsAssetList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CmmsAssetList();
        $vModel = new VwCmmsAssetList();
        $faults = [new CmmsAssetFaults()];
        
//        $part = new CmmsPartList();

//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (\Yii::$app->request->isPost) {
            
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $postData = Yii::$app->request->post('CmmsAssetList');
                //retrieve asset_code and name
                $assetCode = $postData['asset_id'] ?? null;
                $assetName = $postData['name'] ?? null;
                
                $existingAsset = null;
                
                if ($assetCode && $assetName) {
                    // Check if it already exists
                    $existingAsset = CmmsAssetList::find()
                        ->where([
                            'asset_id' => $assetCode,
                            'name' => $assetName,
                            'is_deleted' => 0
                                ])
                        ->one();
                }
                
                if ($existingAsset) {
                    // use existing asset
                    $assetId = $existingAsset->id;
                    $model = $existingAsset;
                } else {
                    if (!$model->load(Yii::$app->request->post())) {
                        throw new \Exception('Failed to load asset data');
                    }
                    $model->is_deleted = 0;
                    $model->active_sts = 1;
                    $model->updated_by = Yii::$app->user->identity->id;

                    if (!$model->save()) {
                       throw new \Exception("Failed to save fault list.");
                    }
                    $assetId = $model->id;
                }
                
                $postFaults = \Yii::$app->request->post('CmmsAssetFaults', []);
                foreach ($postFaults as $index => $postF) {
                    if (!empty($postF['id'])) {
                        $pF = CmmsAssetFaults::findOne($postF['id']);
                        if (!$pF) {
                            throw new \Exception('Asset fault not found');
                        }
                    } else {
                        $pF = new CmmsAssetFaults();
                    }
                    
                    $pF->setAttributes($postF);
//                        $fLD->cmms_asset_list_id = $assetId;
//                    $pF->asset_id = $model->id;
                    $pF->is_deleted = 0;
                    $pF->updated_by = \Yii::$app->user->identity->id;
                    $pF->asset_id = $model->asset_id;
                    $pF->active_sts = 1;
                    
                    if (!$pF->save()) {
                        throw new \Exception("Failed to save fault.");
                    }
                }
                
                $transaction->commit();
                FlashHandler::success('Asset details saved!');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'vModel' => $vModel,
            'faults' => $faults,
            'isUpdate' => false,
        ]);
    }
    
    public function actionUploadExcel() {
        if (Yii::$app->request->isPost) {
            $excelFile = UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
//                if (!in_array($extension, ['xls', 'xlsx'])) {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['index']);
                }

                try {
//                    $reader = new Xls();
                    $reader = IOFactory::createReaderForFile($excelFile->tempName);
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();
                    
                    if ($worksheet === null) {
                        throw new \Exception("Asset List sheet not found in Excel file.");
                    }

//                    $highestRow = $worksheet->getHighestDataRow();
//                    
//                    for ($row = 2; $row <= $highestRow; $row++) {
//                        $assetId = trim((string)$worksheet->getCell("B$row")->getValue());
//
//                        if (empty(trim((string)$assetId))) {
//                            continue;
//                        }
//
//                        $area = $worksheet->getCell("C$row")->getValue();
//                        $section = $worksheet->getCell("D$row")->getValue();
//                        $name = $worksheet->getCell("E$row")->getValue();
//                        $manufacturer = $worksheet->getCell("F$row")->getValue();
//                        $part_description = $worksheet->getCell("G$row")->getValue();
//                        $serial_no = $worksheet->getCell("H$row")->getValue();
//                        $date_of_purchase = $worksheet->getCell("I$row")->getValue();
//                        $date_of_installation = $worksheet->getCell("J$row")->getValue();
                    $buffer = [];
                    
                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $assetId = $data[1];
                        $area = $data[2];
                        $section = $data[3];
                        $name = $data[4];
                        $manufacturer = $data[5];
                        $serial_no = $data[6];
                        $date_of_purchase = $data[7];
                        $date_of_installation = $data[8];
                        
                        if (empty(trim((string)$assetId))) {
                            continue;
                        }
                        
                        $buffer[] = [
                            'assetId' => $assetId,
                            'area' => $area,
                            'section' => $section,
                            'name' => $name,
                            'manufacturer' => $manufacturer,
                            'serial_no' => $serial_no,
                            'date_of_purchase' => $date_of_purchase,
                            'date_of_installation' => $date_of_installation
                        ];
                    }

                    if (!empty($buffer)) {
                        return $this->render('upload-to-confirm', ['buffer' => $buffer]);
                    } else {
//                        $asset = CmmsAssetList::findOne($id);
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Asset ID' column in your Excel file is not left blank.");
                        return $this->redirect(['index']);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('upload');
    }
    
    private function excelToMysqlDateTime($raw): ?string {
        if ($raw === null || $raw === '') return null;

        // Excel stores dates as numbers
        if (is_numeric($raw)) {
            $dt = ExcelDate::excelToDateTimeObject((float)$raw);
            return $dt->format('Y-m-d 00:00:00');
        }

        // If it ever comes as a string
        $raw = trim((string)$raw);
        foreach (['Y-m-d', 'Y/m/d', 'd/m/Y', 'd-m-Y'] as $fmt) {
            $dt = \DateTime::createFromFormat('!' . $fmt, $raw);
            if ($dt) return $dt->format('Y-m-d 00:00:00');
        }

        return null;
    }
    
    public function actionSaveAssetDetails() {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('CmmsAssetList');
                     
            if (!$data || empty($data['asset_id'])) {
                Yii::$app->session->setFlash('error', 'No asset data received.');
                return $this->redirect(['index']);
            }
            
            foreach ($data['asset_id'] as $index => $assetId) {
                $assetDetails = CmmsAssetList::find()
                        ->where(['asset_id' => $assetId])
                        ->andWhere(['active_sts' => 1])
                        ->andWhere(['is_deleted' => 0])
                        ->one();
                if (!$assetDetails) {
                    $assetDetails = new CmmsAssetList();
                }
                $assetDetails->asset_id = $assetId;
                $assetDetails->area = $data['area'][$index] ?? null;
                $assetDetails->section = $data['section'][$index] ?? null;
                $assetDetails->name = $data['name'][$index] ?? null;
                $assetDetails->manufacturer = $data['manufacturer'][$index] ?? null;
                $assetDetails->serial_no = $data['serial_no'][$index] ?? null;
//                $datePurchase = $data['date_of_purchase'][$index] ?? null;
//                if (!empty($datePurchase)) {
                    $assetDetails->date_of_purchase = $this->excelToMysqlDateTime($data['date_of_purchase'][$index] ?? null);
//                }
//                $dateInstall = $data['date_of_installation'][$index] ?? null;
//                if (!empty($dateInstall)) {
                    $assetDetails->date_of_installation = $this->excelToMysqlDateTime($data['date_of_installation'][$index] ?? null);
//                }
                
                $assetDetails->active_sts = 1;
                $assetDetails->is_deleted = 0;
                $assetDetails->updated_by = Yii::$app->user->identity->id;
                
                if (!$assetDetails->save(false)) {
                    Yii::error($assetDetails->getErrors());
                }
            }
            Yii::$app->session->setFlash('success', 'Data successfully saved to the database.');
        }

//        $asset = CmmsAssetList::findOne($id);
        return $this->redirect(['index']);
    }
    
    public function actionAjaxAddFormItem() {

        $request = Yii::$app->request;

        $key = $request->post('key');
        $modelId = $request->post('modelId');
        $isUpdate = $request->post('isUpdate');

        if ($key === null || $modelId === null || $isUpdate === null) {
            throw new BadRequestHttpException('Missing required parameters');
        }
        $formItem = new CmmsAssetFaults();
        $formItem->is_deleted = 0;
        $formItem->updated_by = \Yii::$app->user->identity->id;
        
//        UPDATE MODE
        if ($isUpdate && $modelId) {
            $model = CmmsAssetList::findOne($modelId);
            
            if (!$model) {
                throw new \yii\web\NotFoundHttpException('Fault List not found');
            }
            $formItem->asset_id = $model->asset_id;
            $formItem->asset_id = $model->id;
            $formItem->active_sts = 1;
        }
//        CREATE MODE
        else {
            $model = null;
        }
        
//           changed into this to enable addRow() to work
        return $this->renderPartial('_asset_details_form_row', [
                    'fault' => $formItem,
                    'key' => $key,
                    'model' => $model,
                    'isUpdate' => $isUpdate,
                    'form' => \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form'])
        ]);
    }
    
    public function actionAjaxDeleteItem($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $item = CmmsAssetFaults::findOne($id);

        if (!$item) {
            return ['success' => false, 'error' => 'Item or asset not found'];
        }

        $modelId = $item->asset_id;
        
        $item->is_deleted = 1;
        $item->active_sts = 0;
        $item->updated_by = Yii::$app->user->identity->id;
        if ($item->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
            
            $remainingCount = CmmsAssetFaults::find()
                    ->where([
                        'asset_id' => $modelId,
                        'is_deleted' => 0,
                      ])->count();

            if ($remainingCount == 0) {
                $model = CmmsAssetList::findOne($modelId);
                if ($model) {
                    $model->is_deleted = 1;
                    $model->save(false);
                }
                return [
                    'success' => true,
                    'redirect' => 'index',
                ];
            }

            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to deleted item'];
    }

    /**
     * Updates an existing CmmsAssetList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $vModel = VwCmmsAssetList::find()
            ->where(['id' => $id])
            ->all();
        $faults = $model->cmmsAssetFaults;
        
        if (\Yii::$app->request->isPost) {
            
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new \Exception('Failed to load asset data');
                }
                
                if (!$model->save()) {
                   throw new \Exception("Failed to save fault list.");
                }
                
                $postFaults = \Yii::$app->request->post('CmmsAssetFaults', []);
                foreach ($postFaults as $index => $postF) {
                    if (!empty($postF['id'])) {
                        $pF = CmmsAssetFaults::findOne($postF['id']);
                        if (!$pF) {
                            throw new \Exception('Asset fault not found');
                        }
                    } else {
                        $pF = new CmmsAssetFaults();
//                        $pF->asset_id = $model->id;
                        $pF->is_deleted = 0;
                        $pF->updated_by = \Yii::$app->user->identity->id;
                        $pF->asset_id = $model->asset_id;
                        $pF->active_sts = 1;
                    }
                    $pF->setAttributes($postF);
                    
                    if (!$pF->save()) {
                        throw new \Exception("Failed to save fault.");
                    }
                }
                
                $transaction->commit();
                FlashHandler::success('Asset details saved!');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'vModel' => $vModel,
            'faults' => $faults,
            'isUpdate' => true,
        ]);
    }

    /**
     * Deletes an existing CmmsAssetList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $faults = CmmsAssetFaults::findAll(['asset_id' => $id]);
        foreach ($faults as $mBD) {
            $mBD->is_deleted = 1;
            $mBD->active_sts = 0;
            $mBD->save(false);
        }
        
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->active_sts = 0;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the CmmsAssetList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsAssetList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CmmsAssetList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
