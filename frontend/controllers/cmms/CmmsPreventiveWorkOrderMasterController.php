<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsPreventiveWorkOrderMaster;
use frontend\models\cmms\CmmsPreventiveWorkOrderMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use yii\web\UploadedFile;
use common\models\User;
use frontend\models\cmms\RefAssignedPic;
use frontend\models\cmms\RefProgressStatus;
use frontend\models\cmms\CmmsFaultList;
use frontend\models\cmms\RefCmmsStatus;
use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\CmmsPreventiveMaintenanceDetails;
use frontend\models\cmms\CmmsPmCategoryDesc;
use frontend\models\cmms\VwCmmsPmDescription;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * CmmsPreventiveWorkOrderMasterController implements the CRUD actions for CmmsPreventiveWorkOrderMaster model.
 */
class CmmsPreventiveWorkOrderMasterController extends Controller {

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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view-selected-material', 'view-assigned-tasks', 'view-superior',
                    'pm-wo-form', 'create-update-maintenance-details', 'report-fault', 'view-pm-wo-summary',
                    'update', 'view-reported-faults', 'delete', 'remove-pic', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view-selected-material', 'pm-wo-form', 'create-update-maintenance-details',
                            'report-fault', 'view-pm-wo-summary', 'view-reported-faults'],
                        'roles' => [AuthItem::ROLE_CMMS_Normal, AuthItem::ROLE_CMMS_Superior],
                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['view-selected-material'],
//                        'roles' => [AuthItem::ROLE_CMMS_Superior, AuthItem::ROLE_CMMS_Normal],
//                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view-superior', 'update', 'delete', 'remove-pic'],
                        'roles' => [AuthItem::ROLE_CMMS_Superior],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view-assigned-tasks'],
                        'roles' => [AuthItem::ROLE_CMMS_Normal],
                    ],
                ],
            ],
        ];
    }

    public function actionViewAssignedTasks() {
        $searchModel = new CmmsPreventiveWorkOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assignedTasks');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => 'assigned_tasks',
        ]);
    }

    public function actionViewSuperior() {
        $searchModel = new CmmsPreventiveWorkOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superior');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => 'superior',
        ]);
    }
    
    public function actionUserManualInventory() {
        $this->layout = false;
        $fileName = "T5B-CMMS Module-02.pdf";
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    /**
     * Displays a single CmmsPreventiveWorkOrderMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CmmsPreventiveWorkOrderMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CmmsPreventiveWorkOrderMaster();

        $assignedPICs = $model->assignedPic ?: [
            new RefAssignedPic([
                'preventive_work_order_master_id' => $model->id,
                    ])
        ];

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postPM = Yii::$app->request->post('CmmsPreventiveWorkOrderMaster');

                $model->active_sts = 1;
                $model->assigned_by = Yii::$app->user->identity->id;
                $model->created_at = new \yii\db\Expression('NOW()');

                $model->setAttributes($postPM);

                if (!$model->save(false)) {
                    throw new \Exception("Failed to save PM schedule details.");
                }

//                if (!empty($model->start_time) && !empty($model->end_time)) {
//                    $start = new \DateTime($model->start_time);
//                    $end = new \DateTime($model->end_time);
//
//                    // Difference in days
//                    $model->duration = $start->diff($end)->days;
//                } else {
//                    $model->duration = null;
//                }

                $postPICs = Yii::$app->request->post('RefAssignedPic', []);
                // Remove completely empty rows
                $postPICs = array_filter($postPICs, function ($row) {
                    return !empty(trim($row['name'] ?? ''));
                });
                $savedIds = [];
                foreach ($postPICs as $row) {
                    $pic = !empty($row['id']) ? RefAssignedPic::findOne($row['id']) : new RefAssignedPic();

                    $pic->load($row, '');
                    $pic->preventive_work_order_master_id = $model->id;
                    $pic->active_sts = 1;
                    $staffID = User::find()
                            ->select('id')
                            ->where(['fullname' => $row['name']])
                            ->scalar();
                    $pic->staff_id = $staffID;

                    if ($pic->save()) {
                        $savedIds[] = $pic->id; // ID exists NOW
                    } else {
                        Yii::error($pic->errors, 'assignedPIC');
                    }
                }

                if (!empty($savedIds)) {
                    RefAssignedPic::deleteAll([
                        'and',
                        ['preventive_work_order_master_id' => $model->id],
                        ['not in', 'id', $savedIds],
                    ]);
                } else {
                    // If no saved PICs, remove all Preventive ones for this work order
                    RefAssignedPic::deleteAll([
                        'preventive_work_order_master_id' => $model->id,
                    ]);
                }

                if (!empty($postPICs)) {
                    $model->progress_status_id = RefProgressStatus::$STATUS_ASSIGNED;
                }

                if (!$model->save(false)) {
                    throw new \Exception("Failed to save PM schedule details.");
                }

                $transaction->commit();
                $assignedPICs = $model->assignedPic;

                FlashHandler::success('PM schedule details saved!');
                return $this->redirect(['view-superior']);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->render('create', [
                    'model' => $model,
                    'assignedPICs' => $assignedPICs,
                    'isUpdate' => false,
        ]);
    }

    public function actionPmWoForm($id, $moduleIndex) {
        $faultLists = CmmsFaultList::find()
                ->where(['cmms_preventive_work_order_id' => $id])
                ->all();

        $model = CmmsPreventiveWorkOrderMaster::findOne(['id' => $id]);
        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postPM = Yii::$app->request->post('CmmsPreventiveWorkOrderMaster', []);
                Yii::error($postPM, 'DEBUG_POSTPM');

                $assignedBy = !empty($postPM['assigned_by']) ? (int) $postPM['assigned_by'] : (int) Yii::$app->user->identity->id;

                $postedProgressStatusId = !empty($postPM['progress_status_id']) ? (int) $postPM['progress_status_id'] : null;

                $acknowledgedId = RefProgressStatus::find()
                        ->select('id')
                        ->where(['name' => 'Acknowledged', 'active_sts' => 1])
                        ->scalar();

                $completedId = RefProgressStatus::find()
                        ->select('id')
                        ->where(['name' => 'Completed', 'active_sts' => 1])
                        ->scalar();

                $attrs = [
                    'assigned_by' => $assignedBy,
                ];

                if ($postedProgressStatusId !== null) {
                    $attrs['progress_status_id'] = $postedProgressStatusId;
                }

                if (
                        $postedProgressStatusId !== null &&
                        (string) $postedProgressStatusId === (string) $acknowledgedId &&
                        empty($model->start_time)
                ) {
                    $attrs['start_time'] = date('Y-m-d H:i:s');
                }

                if (
                        $postedProgressStatusId !== null &&
                        (string) $postedProgressStatusId === (string) $completedId &&
                        empty($model->end_time)
                ) {
                    $endTime = date('Y-m-d H:i:s');
                    $attrs['end_time'] = $endTime;

                    if (!empty($model->start_time)) {
                        $start = new \DateTime($model->start_time);
                        $end = new \DateTime($endTime);

                        $interval = $start->diff($end);
                        $attrs['duration'] = $interval->format('%a days %h hours %i minutes');
                    }
                }

                Yii::error($attrs, 'DEBUG_PM_ATTRS');

                $model->updateAttributes($attrs);

                $transaction->commit();
                FlashHandler::success('PM schedule details saved!');
                return $this->redirect(['view-superior']);
            } catch (\Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }
        return $this->render('pm_wo_form', [
                    'model' => $model,
                    'faultLists' => $faultLists,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    public function actionCreateUpdateMaintenanceDetails($id, $moduleIndex) {
        $model = $this->findModel($id);

        $details = CmmsPreventiveMaintenanceDetails::find()
                ->where([
                    'cmms_preventive_maintenance_id' => $id,
                    'active_sts' => 1,
                ])
                ->all();

        if (empty($details)) {
            $details = [new CmmsPreventiveMaintenanceDetails()];
        }

        $vModel = VwCmmsPmDescription::find()->where(['id' => $id])->all();
        if (!$vModel) {
            $vModel = [];
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postDetails = Yii::$app->request->post('CmmsPreventiveMaintenanceDetails', []);
                $postDescs = Yii::$app->request->post('CmmsPmCategoryDesc', []);

                foreach ($postDetails as $detailKey => $detailRow) {
                    if (empty($detailRow['maintenance_category_id']) && empty($detailRow['remarks'])) {
                        continue;
                    }

                    $pmDeets = CmmsPreventiveMaintenanceDetails::find()
                            ->where([
                                'cmms_preventive_maintenance_id' => $id,
                                'maintenance_category_id' => $detailRow['maintenance_category_id'],
                                'active_sts' => 1,
                            ])
                            ->one();

                    if (!$pmDeets) {
                        $pmDeets = new CmmsPreventiveMaintenanceDetails();
                        $pmDeets->cmms_preventive_maintenance_id = $id;
                        $pmDeets->active_sts = 1;
                    }

                    $pmDeets->maintenance_category_id = $detailRow['maintenance_category_id'];
                    $pmDeets->remarks = $detailRow['remarks'] ?? null;

                    if (!$pmDeets->save()) {
                        throw new \Exception(json_encode($pmDeets->getErrors()));
                    }

                    $detailInstructions = $postDescs[$detailKey] ?? [];
                    $savedIds = [];

                    foreach ($detailInstructions as $instructionRow) {
                        $instructionText = trim($instructionRow['instruction'] ?? '');
                        if ($instructionText === '') {
                            continue;
                        }

                        $pmCD = !empty($instructionRow['id']) ? CmmsPmCategoryDesc::findOne($instructionRow['id']) : new CmmsPmCategoryDesc();

                        if (!$pmCD) {
                            $pmCD = new CmmsPmCategoryDesc();
                        }

                        $pmCD->cmms_pm_category_id = $pmDeets->id;
                        $pmCD->instruction = $instructionText;
                        $pmCD->yes_no = isset($instructionRow['yes_no']) ? (int) $instructionRow['yes_no'] : null;
                        $pmCD->check_status = $instructionRow['check_status'] ?? null;
                        $pmCD->observation_reading = $instructionRow['observation_reading'] ?? null;
                        $pmCD->pass_fail = isset($instructionRow['pass_fail']) ? (int) $instructionRow['pass_fail'] : null;

                        if (!$pmCD->save()) {
                            throw new \Exception(json_encode($pmCD->getErrors()));
                        }

                        $savedIds[] = $pmCD->id;
                    }

                    if (!empty($savedIds)) {
                        CmmsPmCategoryDesc::deleteAll([
                            'and',
                            ['cmms_pm_category_id' => $pmDeets->id],
                            ['not in', 'id', $savedIds],
                        ]);
                    } else {
                        CmmsPmCategoryDesc::deleteAll([
                            'cmms_pm_category_id' => $pmDeets->id,
                        ]);
                    }
                }

                $transaction->commit();
                FlashHandler::success('Maintenance details saved!');

                return $this->redirect([
                            '/cmms/cmms-preventive-work-order-master/pm-wo-form',
                            'id' => $id,
                            'moduleIndex' => $moduleIndex,
                ]);
            } catch (\Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->renderAjax('_maintenance_details', [
                    'moduleIndex' => $moduleIndex,
                    'vModel' => $vModel,
                    'details' => $details,
                    'model' => $model,
        ]);
    }

    public function actionAjaxAddFormItem($moduleIndex) {
        $request = Yii::$app->request;
        $key = $request->post('key');
        $modelId = $request->post('modelId');

        if ($key === null || $modelId === null) {
            throw new BadRequestHttpException('Missing required parameters');
        }

        $formItem = new CmmsPreventiveMaintenanceDetails();
        $formItem->cmms_preventive_maintenance_id = $modelId;
        $formItem->active_sts = 1;

        return $this->renderPartial('_maintenance_details_rows', [
                    'detail' => $formItem,
                    'key' => $key,
                    'moduleIndex' => $moduleIndex,
                    'pmCategoryDescs' => [new CmmsPmCategoryDesc()],
                    'form' => \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form'])
        ]);
    }

    public function actionAjaxDeleteItem($id, $moduleIndex) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $item = CmmsPreventiveMaintenanceDetails::findOne($id);

        if (!$item) {
            return ['success' => false, 'error' => 'PM details not found'];
        }

        $modelId = $item->cmms_preventive_maintenance_id;

        $item->active_sts = 0;
        if ($item->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($item->getErrors());

            $remainingCount = CmmsPreventiveMaintenanceDetails::find()
                            ->where([
                                'cmms_preventive_maintenance_id' => $modelId,
                                'active_sts' => 1
                            ])->count();

            if ($remainingCount == 0) {
//                $model = CmmsPreventiveMaintenanceDetails::findOne($modelId);
//                if ($model) {
//                    $model->is_deleted = 1;
//                    $model->active_sts = 0;
//                    $model->save(false);
//                }
                return [
                    'success' => true,
                    'redirect' => ['/cmms/cmms-preventive-work-order-master/pm-wo-form',
                        'id' => $id,
                        'moduleIndex' => $moduleIndex]
//                    'redirect' => 'index',
                ];
            }

            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to deleted item'];
    }

    public function actionReportFault($id, $assetCode, $moduleIndex) {
        $faultModel = new CmmsFaultList();
        $previousModel = CmmsFaultList::find()
                ->orderBy(['reported_at' => SORT_DESC])
                ->one();

        if (Yii::$app->request->isPost) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($previousModel) {
                    $faultModel->last_record = $previousModel->reported_at;
                }

                $postFaultList = Yii::$app->request->post('CmmsFaultList');

                $faultModel->reported_by = Yii::$app->user->identity->id;
                $faultModel->status = RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION;
                $faultModel->is_deleted = 0;
//                $model->maintenance_type = 'Corrective';
                $faultModel->superior_id = Yii::$app->user->identity->superior_id;
                $faultModel->reported_at = new \yii\db\Expression('NOW()');
                $faultModel->active_sts = 1;
                $faultModel->updated_by = Yii::$app->user->identity->id;
//                $model->cmms_asset_list_id = $postFaultList['id'];

                $assetModel = CmmsAssetList::findOne(['asset_id' => $assetCode]);
//                $model->fault_area = $assetModel->area;
//                $model->fault_section = $assetModel->section;
                $faultModel->cmms_asset_list_id = $assetModel->id;
                $faultModel->cmms_preventive_work_order_id = $id;

                $faultModel->setAttributes($postFaultList);

                if (!$faultModel->save()) {
                    throw new \Exception("Failed to save fault list.");
                }

                // count frequency
                $count = CmmsFaultList::getFrequency($faultModel->fault_primary_detail, $faultModel->fault_secondary_detail);
                if ($count == 0) {
                    $faultModel->frequency = 1;
                } else {
                    $faultModel->frequency = $count;
                }
                if (!$faultModel->save()) {
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
                        $cmmsMachinePhoto->cmms_fault_list_details_id = $faultModel->id;
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
                FlashHandler::success('Fault details saved!');
                $faultLists = CmmsFaultList::find()
                        ->where(['cmms_preventive_work_order_id' => $id])
                        ->all();
//                $model = CmmsPreventiveWorkOrderMaster::findOne(['id' => $id]);
                return $this->redirect([
                            '/cmms/cmms-preventive-work-order-master/pm-wo-form',
                            'id' => $id,
                            'moduleIndex' => $moduleIndex
                ]);
//                return $this->redirect(['view', 'id' => $model->id, 'moduleIndex' => $moduleIndex]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->renderAjax('_report_fault_form', [
                    'faultModel' => $faultModel,
                    'isUpdate' => false,
                    'assetCode' => $assetCode,
                    'moduleIndex' => $moduleIndex
        ]);
    }

    public function actionViewPmWoSummary($id, $moduleIndex) {
        $faultLists = CmmsFaultList::find()
                ->where(['cmms_preventive_work_order_id' => $id])
                ->all();

        $model = CmmsPreventiveWorkOrderMaster::findOne(['id' => $id]);

        return $this->renderAjax('_view_preventive_maintenance_summary', [
                    'model' => $model,
                    'faultLists' => $faultLists,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    /**
     * Updates an existing CmmsPreventiveWorkOrderMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $assignedPICs = $model->assignedPic ?: [
            new RefAssignedPic(['preventive_work_order_master_id' => $model->id])
        ];

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postPM = Yii::$app->request->post('CmmsPreventiveWorkOrderMaster');
//                $model->setAttributes($postPM);
//                $model->active_sts = 1;
//                $model->assigned_by = !empty($postPM['assigned_by'])
//                                    ? (int)$postPM['assigned_by']
//                                    : (int)Yii::$app->user->identity->id;

                $model->updateAttributes([
                    'active_sts' => 1,
                    'cmms_asset_list_id' => $postPM['cmms_asset_list_id'],
                    'frequency_id' => $postPM['frequency_id'],
                    'remarks' => $postPM['remarks'],
                    'commencement_date' => $postPM['commencement_date'],
//                    'end_time' => $postPM['end_time']
                ]);
//                if (!$model->save(false)) {
//                    throw new \Exception("Failed to save PM schedule details.");
//                }
//                $duration = null;
//                if (!empty($model->start_time) && !empty($model->end_time)) {
//                    $start = new \DateTime($model->start_time);
//                    $end = new \DateTime($model->end_time);
//
//                    // Difference in days
//                    $duration = $start->diff($end)->days;
//                } 

                $postPICs = Yii::$app->request->post('RefAssignedPic', []);
                // Remove completely empty rows
                $postPICs = array_filter($postPICs, function ($row) {
                    return !empty(trim($row['name'] ?? ''));
                });
                $savedIds = [];
                foreach ($postPICs as $row) {
                    $pic = !empty($row['id']) ? RefAssignedPic::findOne($row['id']) : new RefAssignedPic();

                    $pic->load($row, '');
                    $pic->preventive_work_order_master_id = $model->id;
                    $pic->active_sts = 1;
                    $staffID = User::find()
                            ->select('id')
                            ->where(['fullname' => $row['name']])
                            ->scalar();
                    $pic->staff_id = $staffID;

                    if ($pic->save(false)) {
                        $savedIds[] = $pic->id; // ID exists NOW
                    }
                }

                RefAssignedPic::deleteAll([
                    'and',
                    ['preventive_work_order_master_id' => $model->id],
                    ['not in', 'id', $savedIds],
                ]);

                $progressStatID = null;
                if (!empty($postPICs)) {
                    $progressStatID = RefProgressStatus::$STATUS_ASSIGNED;
                }

                $model->updateAttributes([
//                    'duration' => $duration,
                    'progress_status_id' => $progressStatID
                ]);

//                if (!$model->save(false)) {
//                    throw new \Exception("Failed to save PM schedule details.");
//                }

                $transaction->commit();
                $assignedPICs = $model->assignedPic;

                FlashHandler::success('PM schedule details saved!');
                return $this->redirect(['view-superior']);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->render('update', [
                    'model' => $model,
                    'assignedPICs' => $assignedPICs,
                    'isUpdate' => true,
        ]);
    }

    public function actionViewReportedFaults($id, $moduleIndex) {
        $faultLists = CmmsFaultList::find()
                ->where(['cmms_preventive_work_order_id' => $id])
                ->all();

        $model = CmmsPreventiveWorkOrderMaster::findOne(['id' => $id]);

//        $partList = VInventoryModel::find()
//                ->select(['brand_model', 'id'])
//                ->where(['departments' => "mecha"])
//                ->indexBy('id')
//                ->column();
//
//        $toolList = VInventoryModel::find()
//                ->select(['brand_model', 'id'])
//                ->where(['departments' => "mecha"])
//                ->indexBy('id')
//                ->column();

        return $this->renderAjax('_view_reported_faults', [
                    'model' => $model,
                    'faultLists' => $faultLists,
                    'assetCode' => $model->cmmsAssetList->asset_id,
//                    'partList' => $partList,
//                    'toolList' => $toolList,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    /**
     * Deletes an existing CmmsPreventiveWorkOrderMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
//        CmmsFaultList::updateAll(
//                ['status' => \frontend\models\cmms\RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION],
//                ['cmms_work_order_id' => $id],
//        );
//        CmmsFaultList::updateAll(
//                ['cmms_work_order_id' => null],
//                ['cmms_work_order_id' => $id]
//        );
        RefAssignedPic::deleteAll([
            'preventive_work_order_master_id' => $model->id
        ]);
        $model->delete();

        return $this->redirect(['view-superior']);
    }

    public function actionRemovePic() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $picId = Yii::$app->request->post('picID');
        if (!$picId) {
            throw new \yii\web\BadRequestHttpException('Missing picID');
        }
        RefAssignedPic::updateAll(
                ['active_sts' => 0],
                ['id' => $picId],
        );
        RefAssignedPic::updateAll(
                ['preventive_work_order_master_id' => null],
                ['id' => $picId],
        );

        return ['success' => true];
    }

    /**
     * Finds the CmmsPreventiveWorkOrderMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsPreventiveWorkOrderMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CmmsPreventiveWorkOrderMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewSelectedMaterial($id, $moduleIndex) {
        $model = CmmsPreventiveWorkOrderMaster::findOne($id);
        $materialMaster = \frontend\models\cmms\CmmsWoMaterialRequestMaster::findOne([
                    'wo_type' => \frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM,
                    'wo_id' => $model->id
                ]) ?? null;

        $materialDetails = $materialMaster->cmmsWoMaterialRequestDetails ?? null;
        $partToolList = \frontend\models\inventory\InventoryModel::getModelBrandCombinations();

        return $this->render('materialRequestDetailPm', [
                    'model' => $model,
                    'materialMaster' => $materialMaster,
                    'materialDetails' => $materialDetails,
                    'partToolList' => $partToolList,
                    'moduleIndex' => $moduleIndex,
                    'wotype' => \frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM,
        ]);
    }
}
