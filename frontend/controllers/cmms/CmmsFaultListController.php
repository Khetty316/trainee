<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsFaultList;
use frontend\models\cmms\CmmsFaultListSearch;
use frontend\models\cmms\RefMachineBreakdownType;
use frontend\models\cmms\RefMachinePriority;
use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\CmmsMachinePhotos;
use frontend\models\cmms\RefCmmsStatus;
use frontend\models\cmms\CmmsCorrectiveWorkOrderMaster;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use common\models\myTools\FlashHandler;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * CmmsFaultListController implements the CRUD actions for CmmsFaultList model.
 */
class CmmsFaultListController extends Controller
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
     * Lists all CmmsFaultList models.
     * @return mixed
     */
    public function actionPersonalActive() {
        $searchModel = new CmmsFaultListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalActive');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageStatus' => 'active',
                    'moduleIndex' => 'personal',
        ]);
    }
    
    public function actionPersonalAll() {
        $searchModel = new CmmsFaultListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalAll');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageStatus' => 'all',
                    'moduleIndex' => 'personal',
        ]);
    }
    
    public function actionPersonalReportFault() {
        $searchModel = new CmmsFaultListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalReportFault');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageStatus' => 'reportFault',
                    'moduleIndex' => 'personal',
        ]);
    }
    
    public function actionSuperiorActive() {
        $searchModel = new CmmsFaultListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superiorActive');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageStatus' => 'active',
                    'moduleIndex' => 'superior',
        ]);
    }
    
    public function actionSuperiorAll() {
        $searchModel = new CmmsFaultListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superiorAll');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageStatus' => 'all',
                    'moduleIndex' => 'superior',
        ]);
    }

    /**
     * Displays a single CmmsFaultList model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $moduleIndex)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'moduleIndex' => $moduleIndex
        ]);
    }

    /**
     * Creates a new CmmsFaultList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($moduleIndex)
    {
        $model = new CmmsFaultList();
        $previousModel = CmmsFaultList::find()
                ->orderBy(['reported_at' => SORT_DESC])
                ->one();
        
        if (Yii::$app->request->isPost) {
            
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                if ($previousModel) {
                    $model->last_record = $previousModel->reported_at;
                } 
                
                $postFaultList = Yii::$app->request->post('CmmsFaultList');
                
                $model->reported_by = Yii::$app->user->identity->id;
                $model->status = RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION;
                $model->is_deleted = 0;
//                $model->maintenance_type = 'Corrective';
                $model->superior_id = Yii::$app->user->identity->superior_id;
                $model->reported_at = new \yii\db\Expression('NOW()');
                $model->active_sts = 1;
                $model->updated_by = Yii::$app->user->identity->id;
//                $model->cmms_asset_list_id = $postFaultList['id'];
                
                $assetModel = CmmsAssetList::findOne(['asset_id' => $postFaultList['fault_asset_id']]);
                $model->fault_area = $assetModel->area;
                $model->fault_section = $assetModel->section;
                $model->cmms_asset_list_id = $assetModel->id;
                
                $model->setAttributes($postFaultList);
                
                if (!$model->save()) {
                    throw new \Exception("Failed to save fault list.");
                }
                
                // count frequency
                $count = CmmsFaultList::getFrequency($model->fault_primary_detail, $model->fault_secondary_detail);
                if ($count == 0) {
                    $model->frequency = 1;
                } else {
                    $model->frequency = $count;
                }
                if (!$model->save()) {
                    throw new \Exception("Failed to save fault list.");
                }
                    
                    $deletePhotos = Yii::$app->request->post('DeletePhotos');
                    
//                    if (!empty($deletePhotos[$index])) {
                    if (!empty($deletePhotos)) {
                        \frontend\models\cmms\CmmsMachinePhotos::updateAll(
                                ['is_deleted' => 1],
                                ['id' => $deletePhotos]
                        );
                    }
                    
                    $uploadDir = Yii::getAlias('@webroot/uploads/cmms-fault-list/');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $uploadedFiles = UploadedFile::getInstancesByName("CmmsMachinePhotos");
                    
                    if ($uploadedFiles) {
                        foreach ($uploadedFiles as $photo) {
                            $filename = pathinfo($photo->baseName, PATHINFO_FILENAME) . '.' . $photo->extension;
                            $savePath = $uploadDir . $filename;

                            $photo->saveAs($savePath);
                            
                            // avoid duplicate attachments
//                            $cmmsMachinePhoto = \frontend\models\cmms\CmmsMachinePhotos::findOne([
//                                'cmms_fault_list_details_id' => $fLD->id,
//                                'file_path' => $photo->name,
//                            ]);
//                            if (!$cmmsMachinePhoto) {
                            $cmmsMachinePhoto = new \frontend\models\cmms\CmmsMachinePhotos();
                            $cmmsMachinePhoto->cmms_fault_list_details_id = $model->id;
                            $cmmsMachinePhoto->is_deleted = 0;
//                            }
//                      $attachment->file_content = file_get_contents($file->tempName);
                            $cmmsMachinePhoto->file_name = $photo->name;

                            if (!$cmmsMachinePhoto->save()) {
                                \common\models\myTools\Mydebug::dumpFileW($cmmsMachinePhoto->getErrors());
                            }
                        }
                    }
                $transaction->commit();
                FlashHandler::success('Machine details saved!');
                return $this->redirect(['personal-active']);
//                return $this->redirect(['view', 'id' => $model->id, 'moduleIndex' => $moduleIndex]);
                
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'isUpdate' => false,
            'moduleIndex' => $moduleIndex
        ]);
    }
    
    public function actionApproveOneCmSubmission($id) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = CmmsFaultList::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('Fault not found.');
            }

            $model->status = RefCmmsStatus::$STATUS_WORK_ORDER_CREATION;
            $model->reviewed_at = new \yii\db\Expression('NOW()');
            $model->reviewed_by = Yii::$app->user->identity->id;

            if (!$model->save()) {
                throw new \Exception('Failed to update fault: ' . implode(', ', $model->getFirstErrors()));
            }

            $model->save();
            $transaction->commit();

            FlashHandler::success("Fault approved successfully.");
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed to approve quotation: " . $e->getMessage());
        }

        return $this->redirect(['superior-active']);
    }
    
    public function actionApproveCmSubmission() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $selectAll = $request->post('selectAll', false);

            if ($selectAll) {
                // --- Select All Mode ---
                $excludedIds = $request->post('excludedIds', []);

                // FIXED: Only select quotations pending director approval
                $query = CmmsFaultList::find()
                        ->where(['status' => RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION]);

                if (!empty($excludedIds)) {
                    $query->andWhere(['not in', 'id', $excludedIds]);
                }

                $models = $query->all();

                if (empty($models)) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'No quotations to approve.'];
                }

                // FIXED: Add limits for large datasets
                set_time_limit(0); // no limit
                ini_set('memory_limit', '512M');

                $count = 0;
                $errors = [];
                $faultIds = [];

                foreach ($models as $model) {
                    try {
                        $model->status = RefCmmsStatus::$STATUS_WORK_ORDER_CREATION;
                        $model->reviewed_at = new \yii\db\Expression('NOW()');
                        $model->reviewed_by = Yii::$app->user->identity->id;
                        $model->maintenance_type = "Corrective";
                        if (!$model->save()) {
                            throw new \Exception('Failed to update fault list ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                        }
                        $faultIds[] = $model->id;
                        $count++;
                    } catch (\Exception $e) {
                        \Yii::error("Failed to approve quotation ID {$model->id}: " . $e->getMessage());
                        $errors[] = "Failed to approve {$model->id}: " . $e->getMessage();
                        // Continue with next quotation
                    }
                }
                
                if (!empty($faultIds)) {
                    $this->createWorkOrdersFromApproval($faultIds);
                }
                $transaction->commit();

                $message = "$count fault list(s) approved successfully.";
                if (!empty($errors)) {
                    $message .= " However, there were some issues: " . implode("; ", array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $message .= " (and " . (count($errors) - 3) . " more)";
                    }
                }

                FlashHandler::success($message);
                return [
                    'success' => true, 
                    'message' => $message, 
                    'count' => $count, 
                    'errors' => $errors,
//                    'workOrderCount' => count($workOrders ?? []),
                ];
            } else {
                // --- Normal Mode (selected IDs only) ---
                $ids = $request->post('ids', []);

                if (empty($ids)) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'No faults selected.'];
                }

                $count = 0;
                $errors = [];

                foreach ($ids as $id) {
                    try {
                        $model = CmmsFaultList::findOne($id);
                        if (!$model) {
                            throw new NotFoundHttpException('Fault list not found.');
                        }

                        $model->status = RefCmmsStatus::$STATUS_WORK_ORDER_CREATION;
                        $model->reviewed_at = new \yii\db\Expression('NOW()');
                        $model->reviewed_by = Yii::$app->user->identity->id;
                        $model->maintenance_type = "Corrective";

                        if (!$model->save()) {
                            throw new \Exception('Failed to update fault list ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                        }
                        $count++;
                    } catch (NotFoundHttpException $e) {
                        \Yii::error("Fault not found - ID: {$id}");
                        $errors[] = "Fault list ID {$id} not found";
                    } catch (\Exception $e) {
                        \Yii::error("Failed to approve fault list ID {$id}: " . $e->getMessage());
                        $errors[] = "Failed to approve ID {$id}: " . $e->getMessage();
                    }
                }
//                $workOrders = [];
                
                if (!empty($ids)) {
                    $this->createWorkOrdersFromApproval($ids);
                }

                $transaction->commit();

                $message = "$count fault list(s) approved successfully.";
                if (!empty($errors)) {
                    $message .= " However, there were some issues: " . implode("; ", $errors);
                }

                FlashHandler::success($message);
                return [
                    'success' => true, 
                    'message' => $message, 
                    'count' => $count, 
                    'errors' => $errors,
//                    'workOrderCount' => count($workOrders ?? []),
                ];
            }
        } catch (NotFoundHttpException $e) {
            $transaction->rollBack();
            \Yii::error("NotFoundHttpException in actionApproveCMSubmission: " . $e->getMessage());
            FlashHandler::err("Failed to process: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::error("Exception in actionApproveCMSubmission: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            FlashHandler::err("Failed to process: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve: ' . $e->getMessage()];
        }
    }
    
    private function createWorkOrdersFromApproval(array $faultIds)
    {
        $model = new CmmsCorrectiveWorkOrderMaster();
        $model->active_sts = 1;
        
        if (!$model->save()) {
            throw new \Exception(json_encode($model->errors));
        }
        
        foreach ($faultIds as $faultId) {
            if (CmmsCorrectiveWorkOrderMaster::find()
                ->where(['cmms_fault_list_id' => $faultId])
                ->exists()) {
                continue;
            }

            $fault = CmmsFaultList::findOne($faultId);
            if (!$fault) continue;

            $fault->cmms_work_order_id = $model->id;
            if (!$fault->save()) {
                throw new \Exception(json_encode($fault->errors));
            }
        }
    }
    
//    public function actionAjaxAddFormItem() {
//        $request = Yii::$app->request;
//
//        $key = $request->post('key');
//        $modelId = $request->post('modelId');
//        $isUpdate = $request->post('isUpdate');
//        $moduleIndex = $request->post('moduleIndex');
//
//        if ($key === null || $modelId === null || $isUpdate === null) {
//            throw new BadRequestHttpException('Missing required parameters');
//        }
//        $formItem = new CmmsFaultListDetails();
//        $formItem->updated_by = Yii::$app->user->identity->id;
//        
////        UPDATE MODE
//        if ($isUpdate && $modelId) {
//            $model = CmmsFaultList::findOne($modelId);
//            
//            if (!$model) {
//                throw new \yii\web\NotFoundHttpException('Fault List not found');
//            }
//        
////            $assetCode = CmmsFaultListDetails::find()
////                ->select('fault_asset_id')
////                ->where(['fault_list_id' => $modelId])
////                ->andWhere(['not', ['fault_asset_id' => null]])
////                ->scalar(); // returns string directly
////
////            if (!$assetCode) {
////                throw new \yii\web\BadRequestHttpException('Asset code not found');
////            }
//
//            $detail = $model->getCmmsFaultListDetails()->one();
//            
//            if ($detail) {
//                $formItem->fault_asset_id = $detail->fault_asset_id;
//                $formItem->fault_area = $detail->fault_area;
//                $formItem->fault_section = $detail->fault_section;
//                $formItem->cmms_asset_list_id = $detail->cmms_asset_list_id;
//            }
//        }
////        CREATE MODE
//        else {
////            $assetCode = \Yii::$app->request->post('asset_id');
////            $assetArea = \Yii::$app->request->post('area');
////            $assetSection = \Yii::$app->request->post('section');
//            
////            $assetListID = CmmsAssetList::getAssetID($assetArea, $assetSection, $assetCode);
//            
//            if (!$assetCode) {
//                throw new \yii\web\BadRequestHttpException('Asset code missing');
//            }
//            
////            $formItem->fault_asset_id = $assetCode;
////            $formItem->fault_area = $assetArea;
////            $formItem->fault_section = $assetSection;
////            $formItem->cmms_asset_list_id = $assetListID; 
//            
//            $model = null;
//        }
//        
////           changed into this to enable addRow() to work
//        return $this->renderPartial('_form_row', [
////                    'machineBreakdownTypeList' => RefMachineBreakdownType::getActiveDropdownlist_by_id(),
//            'priorityList' => RefMachinePriority::getActiveDropdownlist_by_id(),
//            'faultListDetail' => $formItem,
////                    'faultAreaList' => \frontend\models\cmms\CmmsAssetList::getActiveDropdownlist(),
////                    'faultSectionList' => null,
//            'key' => $key,
//            'model' => $model,
//            'form' => \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form']),
//            'isUpdate' => $isUpdate,
////            'assetCode' => $assetCode,
//            'moduleIndex' => $moduleIndex
//        ]);
//    }
    
//    public function actionAjaxDeleteItem($id) {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//
//        $item = CmmsFaultListDetails::findOne($id);
//
//        if (!$item) {
//            return ['success' => false, 'error' => 'Item or asset not found'];
//        }
//
//        $modelId = $item->fault_list_id;
//        
//        foreach ($item->cmmsMachinePhotos as $photo) {
//            $photo->is_deleted = 1;
//            $photo->save(false);
//        } 
//        $item->is_deleted = 1;
//        $item->updated_by = Yii::$app->user->identity->id;
//        if ($item->save(false)) {
//            \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
//            
//            $remainingCount = CmmsFaultListDetails::find()
//                    ->where([
//                        'fault_list_id' => $modelId,
//                        'is_deleted' => 0,
//                      ])->count();
//
//            if ($remainingCount == 0) {
//                $model = CmmsFaultList::findOne($modelId);
//                if ($model) {
//                    $model->is_deleted = 1;
//                    $model->save(false);
//                }
//                return [
//                    'success' => true,
//                    'redirect' => 'personal-active',
//                ];
//            }
//
//            return ['success' => true];
//        }
//        return ['success' => false, 'error' => 'Failed to deleted item'];
//    }
    
    public function actionGetFaultTypeByAsset($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$id) {
            return ['options' => []];
        }

        $types = CmmsAssetList::getFaultType_by_ID($id); 
        // Returns array ['FT-01'=>'Mechanical','FT-02'=>'Electrical',...]

        return [
            'options' => $types
        ];
    }
    
    public function actionGetAssetAreas($assetCode)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return CmmsAssetList::getAreas_by_Code($assetCode);
    }
    
    public function actionGetSections($assetCode, $assetArea)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return CmmsAssetList::getSections_by_Code_Area($assetCode, $assetArea);
    }
    
    public function actionGetPrimaryFaultDetails()
    {
        $type = Yii::$app->request->post('type');
        
        $primary = CmmsAssetList::getPrimaryFault_by_type($type);
        
        echo \yii\helpers\Html::tag('option', 'Select Primary Fault', ['value' => '']);
        foreach ($primary as $value => $label) {
            echo \yii\helpers\Html::tag('option', $label, ['value' => $value]);
        }
        Yii::$app->end();
    }
    
    public function actionGetSecondaryFaultDetails()
    {
        $primaryFault = Yii::$app->request->post('primary');
        
        $secondary = \frontend\models\cmms\CmmsAssetList::getSecondaryFault($primaryFault);
        
        echo \yii\helpers\Html::tag('option', 'Select Secondary Fault', ['value' => '']);
        foreach ($secondary as $value => $label) {
            echo \yii\helpers\Html::tag('option', $label, ['value' => $value]);
        }
        
        Yii::$app->end();
    }
    
    public function actionFaultFormModal($id = null)
    {
        if ($id) {
            // Update scenario
            $model = CmmsFaultList::findOne($id);
            $isUpdate = true;
        } else {
            // Create scenario
            $model = new CmmsFaultList();
            $isUpdate = false;
        }

        return $this->renderAjax('_fault_details_form', [
            'model' => $model,
            'isUpdate' => $isUpdate,
            'moduleIndex' => $isUpdate ? 'personal' : 'create', // optional
        ]);
    }
    
    public function actionBulkUpdate($moduleStatus)
    {
        $post = Yii::$app->request->post('CmmsFaultList', []);
        
        foreach ($post as $id => $faultData) {
            $fault = CmmsFaultList::findOne($id);
            if ($fault) {
                
                $fault->load(['CmmsFaultList' => $faultData]);
                $fault->save(false); // or handle validation properly
            }
            $previousModel = CmmsFaultList::find()
                ->where(['fault_primary_detail' => $fault->fault_primary_detail])
                ->andWhere(['fault_secondary_detail' => $fault->fault_secondary_detail])
                ->orderBy(['reported_at' => SORT_DESC])
                ->one();
            
            if ($previousModel) {
                $fault->last_record = $previousModel->reported_at;
            }
            
            $count = CmmsFaultList::getFrequency($fault->fault_primary_detail, $fault->fault_secondary_detail);
            if ($count == 0) {
                $fault->frequency = 1;
            } else {
                $fault->frequency = $count;
            }
            
            $fault->save(false);
        }
        
        if ($moduleStatus === 'superior') {
            return $this->redirect(['/cmms/cmms-corrective-work-order-master/view-superior']);
        }
        
        return $this->redirect(['/cmms/cmms-corrective-work-order-master/view-assigned-tasks']);
    }

    /**
     * Updates an existing CmmsFaultList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $moduleIndex = null)
    {
        if ($moduleIndex === null) {
            $moduleIndex = Yii::$app->request->get('moduleIndex', 'personal');
        }
        
        $model = CmmsFaultList::find()
                ->where(['id' => $id])
                ->one();
        
        if (Yii::$app->request->isPost) {
            
            $transaction = Yii::$app->db->beginTransaction();
            
            $postFaultList = Yii::$app->request->post('CmmsFaultList');
            $assetModel = CmmsAssetList::findOne(['asset_id' => $postFaultList['fault_asset_id']]);
            $model->fault_area = $assetModel->area;
            $model->fault_section = $assetModel->section;
            $model->cmms_asset_list_id = $assetModel->id;

            $model->setAttributes($postFaultList);
            
            try {
                if (!$model->save()) {
                    throw new \Exception("Failed to save fault list.");
                }
                
                $previousModel = CmmsFaultList::find()
                    ->where(['fault_primary_detail' => $model->fault_primary_detail])
                    ->andWhere(['fault_secondary_detail' => $model->fault_secondary_detail])
                    ->orderBy(['reported_at' => SORT_DESC])
                    ->one();
                
                if ($previousModel) {
                    $model->last_record = $previousModel->last_record;
                }
                
                $count = CmmsFaultList::getFrequency($model->fault_primary_detail, $model->fault_secondary_detail);
                
                if ($count == 0) {
                    $model->frequency = 1;
                } else {
                    $model->frequency = $count;
                }
                if (!$model->save()) {
                    throw new \Exception("Failed to save fault details.");
                }
               
                $deletePhotos = Yii::$app->request->post('DeletePhotos');

                if (!empty($deletePhotos)) {
                    \frontend\models\cmms\CmmsMachinePhotos::updateAll(
                            ['is_deleted' => 1],
                            ['id' => $deletePhotos]
                    );
                }

                $uploadDir = Yii::getAlias('@webroot/uploads/cmms-fault-list/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                    $key = $model->id; // This must match the `data-key` in your HTML input

//                        $uploadedFiles = UploadedFile::getInstancesByName("CmmsMachinePhotos[$key]");
                    $uploadedFiles = UploadedFile::getInstancesByName("CmmsMachinePhotos[$key]");

                    if ($uploadedFiles) {
                        foreach ($uploadedFiles as $photo) {
                            $filename = $photo->baseName . '.' . $photo->extension;
                            $savePath = $uploadDir . $filename;
                            $photo->saveAs($savePath);

                            $cmmsMachinePhoto = new CmmsMachinePhotos();
                            $cmmsMachinePhoto->cmms_fault_list_details_id = $model->id;
                            $cmmsMachinePhoto->file_name = $filename;
                            $cmmsMachinePhoto->is_deleted = 0;
                            if (!$cmmsMachinePhoto->save()) {
                                \common\models\myTools\Mydebug::dumpFileW($cmmsMachinePhoto->getErrors());
                            }
                        }
                    }
//                }
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
            
            $transaction->commit();
            FlashHandler::success('Fault details saved!');
            if ($moduleIndex === 'superior') {
                return $this->redirect(['superior-active']);
            }
            return $this->redirect(['personal-active']);
//            return $this->redirect(['view', 'id' => $model->id, 'moduleIndex' => $moduleIndex]);
        }

        return $this->render('update', [
            'model' => $model,
            'isUpdate' => true,
            'moduleIndex' => $moduleIndex
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
                    
                    $buffer = [];
                    
                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $data = [];
                        foreach ($cells as $cell) {
                            $data[] = $cell->getValue();
                        }

                        $assetId = $data[1];
                        $fault_priority = $data[2];
                        $fault_type = $data[3];
                        $primary_description = $data[4];
                        $secondary_description = $data[5];
                        $remark = $data[6];
                        
                        if (empty(trim((string)$assetId))) {
                            continue;
                        }
                        
                        $buffer[] = [
                            'assetId' => $assetId,
                            'fault_type' => $fault_type,
                            'primary_description' => $primary_description,
                            'secondary_description' => $secondary_description,
                            'fault_priority' => $fault_priority,
                            'remark' => $remark
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
                    return $this->redirect(['personal-active']);
                }
            }
        }

        return $this->render('upload');
    }
    
    public function actionSaveFaultDetails() {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('CmmsFaultList');
            
            if (!$data || empty($data['fault_asset_id'])) {
                Yii::$app->session->setFlash('error', 'No fault data received.');
                return $this->redirect(['index']);
            }
            
            foreach ($data['fault_asset_id'] as $index => $assetId) {
                $faultDetails = new CmmsFaultList();
                $faultDetails->fault_asset_id = $assetId;
                $faultDetails->fault_type = $data['fault_type'][$index] ?? null;
                $faultDetails->fault_primary_detail = $data['fault_primary_detail'][$index] ?? null;
                $faultDetails->fault_secondary_detail = $data['fault_secondary_detail'][$index] ?? null;
                $faultDetails->machine_priority_id = $data['machine_priority_id'][$index] ?? null;
                $faultDetails->additional_remarks = $data['remark'][$index] ?? null;
                
                $faultDetails->active_sts = 1;
                $faultDetails->is_deleted = 0;
                $faultDetails->updated_by = Yii::$app->user->identity->id;
                $faultDetails->reported_by = Yii::$app->user->identity->id;
                $faultDetails->updated_at = new \yii\db\Expression('NOW()');
                $faultDetails->reported_at = new \yii\db\Expression('NOW()');;
                $faultDetails->status = RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION;
                
                if (!$faultDetails->save(false)) {
                    Yii::error($faultDetails->getErrors());
                }
            }
            Yii::$app->session->setFlash('success', 'Data successfully saved to the database.');
        }
//        $asset = CmmsAssetList::findOne($id);
        return $this->redirect(['personal-active']);
    }

    /**
     * Deletes an existing CmmsFaultList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $machinePhotos = $model->cmmsMachinePhotos;
            foreach ($machinePhotos as $photo) {
                $photo->is_deleted = 1;
                $photo->save(false);
                
                $filePath = Yii::getAlias('@webroot/uploads/cmms-fault-list/' . $photo->file_name);

                // Delete file if exists
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        $model->is_deleted = 1;
        $model->save(false);
        return $this->redirect(['personal-active']);
    }
    
    public function actionViewAssetDetails($asset_id) 
    {
//        $faultModel = CmmsFaultList::findOne(['id' => $model_id]);
        $assetModel = CmmsAssetList::findOne(['asset_id' => $asset_id]);
        
        return $this->renderAjax('view_asset_details', [
            'model' => $assetModel,
//            'faultModel' => $faultModel
        ]);
    }

    /**
     * Finds the CmmsFaultList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsFaultList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CmmsFaultList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
