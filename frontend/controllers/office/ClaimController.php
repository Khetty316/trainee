<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\claim\ClaimMaster;
use frontend\models\office\claim\ClaimMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\claim\ClaimDetail;
use frontend\models\office\claim\RefClaimType;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use frontend\models\office\employeeHandbook\EhOutpatientMedDetail;
use frontend\models\office\employeeHandbook\EhOutpatientMedMaster;
use frontend\models\office\employeeHandbook\EhTravelAllowanceMaster;
use frontend\models\office\employeeHandbook\EhTravelAllowanceDetail;
use frontend\models\office\claim\ClaimEntitlement;
use frontend\models\office\claim\ClaimEntitlementDetails;
use frontend\models\RefGeneralStatus;
use frontend\models\office\leave\LeaveMaster;
use yii\db\Expression;
use common\models\myTools\FlashHandler;
use common\models\User;
use yii\web\UploadedFile;
use frontend\models\office\claim\ClaimApprovalWorklist;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;
use common\models\myTools\MyCommonFunction;
use frontend\models\office\employeeHandbook\EhExecOtMeal;
use frontend\models\office\employeeHandbook\EhExecOtMealDetail;

/**
 * ClaimController implements the CRUD actions for ClaimMaster model.
 */
class ClaimController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/claim/');
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-file', 'ajax-get-prod-ot-meal-limit', 'ajax-prodotmeal-detail', 'ajax-sick-leave-detail', 'ajax-travel-detail', 'ajax-prf-detail', 'ajax-eh-travel-allowance', 'ajax-get-medical-limit', 'ajax-get-petrol-limit', 'ajax-get-monthly-limit'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['personal-claim-summary', 'ajax-get-exec-ot-meal-limit', 'personal-claim-pending', 'personal-claim-all', 'personal-view-claim', 'create-claim', 'claimant-update-claim', 'claimant-cancel-claim', 'ajax-claimant-cancel-claim', 'user-manual-personal'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_CM_Normal],
                    ],
                    [
                        'actions' => ['superior-approval-pending', 'superior-approval-all', 'superior-view-claim', 'user-manual-superior'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_CM_Superior],
                    ],
                    [
                        'actions' => ['finance-approval-pending', 'finance-approval-all', 'finance-view-claim', 'user-manual-finance', 'finance-claim-summary'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_CM_Finance],
                    ],
                ],
            ],
        ];
    }
    
    public function actionPersonalClaimSummary($year = '', $month = '', $claim_type = '', $staff = '') {
        $staff = Yii::$app->user->identity->id;
        $claimEntitlement = new ClaimEntitlement();
        $records = $claimEntitlement->getClaimSummary($year, $month, $claim_type, $staff);

        return $this->render('entitlementSummary', [
                    'claimSummarys' => $records['claimSummarys'],
                    'hasEntitlement' => $records['hasEntitlement'],
                    'yearList' => $records['yearList'],
                    'monthList' => $records['monthList'],
                    'claimTypes' => $records['claimTypes'],
                    'staffList' => $records['staffList'],
                    'intMonth' => $records['intMonth'],
                    'year' => $records['year'],
                    'month' => $records['month'],
                    'claimType' => $records['claimType'],
                    'staff' => $records['staff'],
                    'module' => 'personal',
                    'key' => 4
        ]);
    }

    public function actionFinanceClaimSummary($year = '', $month = '', $claim_type = '', $staff = '') {
        $claimEntitlement = new ClaimEntitlement();
        $records = $claimEntitlement->getClaimSummary($year, $month, $claim_type, $staff);
        
        return $this->render('entitlementSummary', [
                    'claimSummarys' => $records['claimSummarys'],
                    'hasEntitlement' => $records['hasEntitlement'],
                    'yearList' => $records['yearList'],
                    'monthList' => $records['monthList'],
                    'claimTypes' => $records['claimTypes'],
                    'staffList' => $records['staffList'],
                    'intMonth' => $records['intMonth'],
                    'year' => $records['year'],
                    'month' => $records['month'],
                    'claimType' => $records['claimType'],
                    'staff' => $records['staff'],
                    'module' => 'finance',
                    'key' => 4
        ]);
    }

    /*     * ****************************** Personal *********************************** */

    public function actionPersonalClaimPending() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingPersonal');

        return $this->render('personalPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPersonalClaimAll() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allPersonal');

        return $this->render('personalAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPersonalViewClaim($id) {
        $model = $this->findModel($id);
        $user = User::findOne($model->claimant_id);

        if ($model->claim_type === RefClaimType::codeTravel) {
            $travelAllowance = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => null])->one();
            $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => $travelAllowance->id])->all();
            $travelAllowancePerDay = $this->getTravelAllowanceRateFromEmployeeHandbook($travelAllowance->travel_location_code, $user->grade);
        } else {
            $travelAllowance = null;
            $travelAllowancePerDay = null;
            $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id])->all();
        }

        $allClaimDetails = array_merge(
                $travelAllowance ? [$travelAllowance] : [],
                is_array($claimDetail) ? $claimDetail : []
        );

        $superiorWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_SUPERIOR_APPROVAL, $allClaimDetails);
        $financeApprovalWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_APPROVAL, $allClaimDetails);
        $financePaymentWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_PAYMENT, $allClaimDetails);

        return $this->render('personalViewClaim', [
                    'model' => $model,
                    'travelAllowance' => $travelAllowance,
                    'claimDetail' => $claimDetail,
                    'travelAllowancePerDay' => $travelAllowancePerDay,
                    'superiorWorklists' => $superiorWorklists,
                    'financeApprovalWorklists' => $financeApprovalWorklists,
                    'financePaymentWorklists' => $financePaymentWorklists
        ]);
    }

    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@frontend/uploads/claim/' . $filename);
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    private function getTravelAllowanceRateFromEmployeeHandbook($locationCode, $grade) {
        // Find active employee handbook
        $employeeHandbookMaster = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();

        if (!$employeeHandbookMaster) {
            return false;
        }

        // Find travel allowance master
        $ehTravelAllowanceMaster = EhTravelAllowanceMaster::find()->where(['eh_master_id' => $employeeHandbookMaster->id])->one();

        if (!$ehTravelAllowanceMaster) {
            return false;
        }

        // Find specific allowance detail
        $ehTravelAllowanceDetail = EhTravelAllowanceDetail::find()->where(['eh_travel_allowance_master_id' => $ehTravelAllowanceMaster->id, 'eh_master_id' => $employeeHandbookMaster->id, 'grade' => $grade, 'location_type' => $locationCode])->one();

        if (!$ehTravelAllowanceDetail) {
            return false;
        }

        // Validate amount
        $allowanceAmount = (float) $ehTravelAllowanceDetail->amount_per_day;
        if ($allowanceAmount < 0) {
            return false;
        }

        return $allowanceAmount;
    }

    public function actionCreateClaim() {
        $model = new ClaimMaster();
        $claimDetail = [new ClaimDetail()];
        return $this->handleClaimForm($model, $claimDetail, false);
    }

    public function actionClaimantUpdateClaim($id) {
        $model = $this->findModel($id);
        $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $id, 'is_deleted' => 0])->all();
        if (!$claimDetail) {
            $claimDetail = [new ClaimDetail()];
        }

        return $this->handleClaimForm($model, $claimDetail, true);
    }

    private function handleClaimForm($model, $claimDetail, $isUpdate) {
        $user = User::findOne(Yii::$app->user->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        if (Yii::$app->request->isPost) {
            $result = $this->processClaimSubmission($model, $claimDetail, $user, $isUpdate);
            if ($result !== false) {
                return $result;
            }
        }

        $viewName = $isUpdate ? 'update' : 'create';
        return $this->render($viewName, $this->getViewData($model, $user, $claimDetail));
    }

    private function processClaimSubmission($model, $claimDetail, $user, $isUpdate) {
        try {
            $model->load(Yii::$app->request->post());
            $claimMasterData = Yii::$app->request->post("ClaimMaster");
            $receiptsData = Yii::$app->request->post("ClaimDetail");
            $uploadedFiles = $this->getUploadedFiles($receiptsData);

            if (empty($claimMasterData) || empty($receiptsData)) {
                throw new \Exception("Invalid claim data provided");
            }

            $result = $model->processAndSave($claimMasterData, $receiptsData, $uploadedFiles, $isUpdate);

            if ($result === true) {
                $action = $isUpdate ? 'updated' : 'submitted';
                FlashHandler::success("Claim {$action} successfully");
                return $this->redirect(['personal-claim-pending']);
            } else {
                throw new \Exception(is_string($result) ? $result : "Failed to process claim");
            }
        } catch (\Exception $e) {
            Yii::error("Claim processing failed (processClaimSubmission): " . $e->getMessage(), __METHOD__);
            FlashHandler::err($e->getMessage());
            $viewName = $isUpdate ? 'update' : 'create';
            return $this->render($viewName, $this->getViewData($model, $user, $claimDetail));
        }
    }

    private function getUploadedFiles($receiptsData) {
        $uploadedFiles = [];
        if (is_array($receiptsData)) {
            foreach ($receiptsData as $index => $receipt) {
                $uploadedFile = UploadedFile::getInstanceByName("ClaimDetail[{$index}][scannedFile]");
                $removeExisting = isset($receipt['remove_existing_file']) && $receipt['remove_existing_file'] == '1';

                $uploadedFiles[$index] = [
                    'new_file' => $uploadedFile,
                    'remove_existing' => $removeExisting
                ];
            }
        }
        return $uploadedFiles;
    }

    private function getViewData($model, $user, $claimDetail) {
        $claimTypes = RefClaimType::getDropDownList($user->grade);

        if (!MyCommonFunction::checkRoles([AuthItem::ROLE_CM_Finance])) {
            unset($claimTypes[RefClaimType::codeDirector]);
            $claimTypeList = $claimTypes;
        } else {
            $claimTypeList = $claimTypes;
        }

        return [
            'model' => $model,
            'claimTypeList' => $claimTypeList,
            'superior' => Yii::$app->user->identity->superior_id,
            'userList' => User::getActiveDropDownListExcludeOne($user->id),
            'claimDetail' => $claimDetail
        ];
    }

    public function actionClaimantCancelClaim($id) {
        $claimMaster = $this->findModel($id);

        if ($claimMaster->claimant_id !== Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You are not authorized to delete this claim.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $claimMaster->is_deleted = 1;
            $claimMaster->claim_status = RefGeneralStatus::STATUS_ClaimantCancelClaim;
            if (!$claimMaster->update()) {
                throw new \Exception('Failed to delete claim master record.');
            }

            $claimDetails = $claimMaster->getClaimDetails()->where(['is_deleted' => 0])->all();
            foreach ($claimDetails as $detail) {
//                if ($detail->receipt_file) {
//                    $this->deleteReceiptFile($detail->receipt_file);
//                }

                $detail->is_deleted = 1;
                $detail->deleted_at = new \yii\db\Expression('NOW()');
                $detail->deleted_by = Yii::$app->user->id;
                if (!$detail->update()) {
                    throw new \Exception("Failed to cancel claim detail ID: {$detail->id}");
                }
            }

            $leaveMaster = LeaveMaster::findOne(['leave_code' => $claimMaster->ref_code]);
            if ($leaveMaster !== null) {
                $leaveMaster->claim_flag = 0;
                if (!$leaveMaster->save()) {
                    throw new \Exception($leaveMaster->getErrors());
                }
            }

            $transaction->commit();

            FlashHandler::success('Claim deleted successfully.');
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHandler::err('Failed to delete claim: ' . $e->getMessage());
        }

        return $this->redirect(['personal-claim-pending']);
    }

    public function actionAjaxClaimantCancelClaim($id) {
        $claimMaster = $this->findModel($id);

        // Ensure the user can only cancel their own claim
        if ($claimMaster->claimant_id !== Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You are not authorized to cancel this claim.');
        }

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('ClaimMaster');
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Soft delete the claim master
                $claimMaster->is_deleted = 1;
                $claimMaster->claim_status = RefGeneralStatus::STATUS_ClaimantCancelClaim;
                $claimMaster->deleted_at = new \yii\db\Expression('NOW()');
                $claimMaster->deleted_by = Yii::$app->user->id;
                $claimMaster->delete_remark = $postData['delete_remark'] ?? null;

                if (!$claimMaster->save(false)) {
                    throw new \Exception('Failed to delete claim master record.');
                }

                // Soft delete all claim details
                $claimDetails = $claimMaster->getClaimDetails()->where(['is_deleted' => 0])->all();
                foreach ($claimDetails as $detail) {
                    $detail->is_deleted = 1;
                    $detail->deleted_at = new \yii\db\Expression('NOW()');
                    $detail->deleted_by = Yii::$app->user->id;

                    if (!$detail->save(false)) {
                        throw new \Exception("Failed to cancel claim detail ID: {$detail->id}");
                    }
                }

                // Reset related records (based on claim type)
                $leaveMaster = null;

                switch ($claimMaster->claim_type) {
                    case RefClaimType::codeMaterial:
                    case RefClaimType::codeRepair:
                        $leaveMaster = \frontend\models\office\preReqForm\PrereqFormMaster::findOne(['prf_no' => $claimMaster->ref_code]);
                        break;

                    case RefClaimType::codeMedical:
                        if ($claimMaster->ref_code_sts == 1 && $claimMaster->ref_code) {
                            $leaveMaster = \frontend\models\office\leave\LeaveMaster::findOne(['leave_code' => $claimMaster->ref_code]);
                        }
                        break;

                    case RefClaimType::codeTravel:
                        $leaveMaster = \frontend\models\office\leave\LeaveMaster::findOne(['leave_code' => $claimMaster->ref_code]);
                        break;

                    case RefClaimType::codeProdOTMeal:
                        $master = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::findOne(['ref_code' => $claimMaster->ref_code]);
                        if ($master) {
                            $master->status = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::STATUS_FINALIZE;
                            if (!$master->save(false)) {
                                throw new \Exception('Failed to reset Production OT Meal record.');
                            }
                        }
                        break;
                }

                // Reset claim flag if applicable
                if ($leaveMaster !== null) {
                    $leaveMaster->claim_flag = 0;
                    if (!$leaveMaster->save(false)) {
                        throw new \Exception('Failed to reset claim flag for leave record.');
                    }
                }

                $transaction->commit();
                FlashHandler::success('Claim cancelled successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err('Failed to cancel claim: ' . $e->getMessage());
            }

            return $this->redirect(['personal-claim-pending']);
        }

        // Load confirmation form via AJAX modal
        return $this->renderAjax('_formCancelClaim', [
                    'claimMaster' => $claimMaster,
        ]);
    }

//    private function deleteReceiptFile($filename) {
//        if (!$filename)
//            return;
//
//        try {
//            $filePath = Yii::getAlias('@webroot/uploads/receipts/') . $filename;
//            if (file_exists($filePath)) {
//                if (unlink($filePath)) {
//                    Yii::info("Deleted receipt file: {$filename}", __METHOD__);
//                } else {
//                    Yii::warning("Failed to delete receipt file: {$filename}", __METHOD__);
//                }
//            }
//        } catch (Exception $e) {
//            Yii::error("Error deleting receipt file {$filename}: " . $e->getMessage(), __METHOD__);
//        }
//    }

    /*     * ****************************** Superior *********************************** */

    public function actionSuperiorApprovalPending() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingSuperior');

        return $this->render('superiorPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSuperiorApprovalAll() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allSuperior');

        return $this->render('superiorAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSuperiorViewClaim($id) {
        $model = $this->findModel($id);
        $user = User::findOne($model->claimant_id);
        $travelAllowance = null;
        $travelAllowancePerDay = null;
        $claimDetail = [];

        if ($model->claim_type === RefClaimType::codeTravel) {
            $travelAllowance = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => null, 'is_deleted' => 0])->one();
            if ($travelAllowance) {
                $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => $travelAllowance->id, 'is_deleted' => 0])->all();
                $travelAllowancePerDay = $this->getTravelAllowanceRateFromEmployeeHandbook($travelAllowance->travel_location_code, $user->grade);
            }
        } else {
            $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id])->all();
        }

        $allClaimDetails = array_merge(
                $travelAllowance ? [$travelAllowance] : [],
                is_array($claimDetail) ? $claimDetail : []
        );

        $superiorWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_SUPERIOR_APPROVAL, $allClaimDetails);
        $financeApprovalWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_APPROVAL, $allClaimDetails);
        $financePaymentWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_PAYMENT, $allClaimDetails);

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('ClaimApprovalWorklist');

            try {
                $this->processClaimApproval($model, $claimDetail, $postData);
                return $this->redirect(['superior-approval-pending']);
            } catch (\Exception $e) {
                return $this->redirect(['superior-view-claim', 'id' => $id]);
            }
        }

        return $this->render('superiorViewClaim', [
                    'model' => $model,
                    'travelAllowance' => $travelAllowance,
                    'claimDetail' => $claimDetail,
                    'travelAllowancePerDay' => $travelAllowancePerDay,
                    'superiorWorklists' => $superiorWorklists,
                    'financeApprovalWorklists' => $financeApprovalWorklists,
                    'financePaymentWorklists' => $financePaymentWorklists
        ]);
    }

    /*     * ****************************** Finance *********************************** */

    public function actionFinanceApprovalPending() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingFinance');

        return $this->render('financePending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinanceApprovalAll() {
        $searchModel = new ClaimMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('financeAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinanceViewClaim($id) {
        $model = $this->findModel($id);
        $user = User::findOne($model->claimant_id);
        $travelAllowance = null;
        $travelAllowancePerDay = null;
        $claimDetail = [];

        if ($model->claim_type === RefClaimType::codeTravel) {
            $travelAllowance = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => null, 'is_deleted' => 0])->one();
            if ($travelAllowance) {
                $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id, 'parent_id' => $travelAllowance->id, 'is_deleted' => 0])->all();
                $travelAllowancePerDay = $this->getTravelAllowanceRateFromEmployeeHandbook($travelAllowance->travel_location_code, $user->grade);
            }
        } else {
            $claimDetail = ClaimDetail::find()->where(['claim_master_id' => $model->id])->all();
        }

        $allClaimDetails = array_merge(
                $travelAllowance ? [$travelAllowance] : [],
                is_array($claimDetail) ? $claimDetail : []
        );

        $superiorWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_SUPERIOR_APPROVAL, $allClaimDetails);
        $financeApprovalWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_APPROVAL, $allClaimDetails);
        $financePaymentWorklists = $this->prepareWorklists($model->id, ClaimMaster::MODULE_FINANCE_PAYMENT, $allClaimDetails);

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('ClaimApprovalWorklist');

            try {
                $this->processClaimApproval($model, $claimDetail, $postData);
                return $this->redirect(['finance-approval-pending']);
            } catch (\Exception $e) {
                return $this->redirect(['finance-view-claim', 'id' => $id]);
            }
        }

        return $this->render('financeViewClaim', [
                    'model' => $model,
                    'travelAllowance' => $travelAllowance,
                    'claimDetail' => $claimDetail,
                    'travelAllowancePerDay' => $travelAllowancePerDay,
                    'superiorWorklists' => $superiorWorklists,
                    'financeApprovalWorklists' => $financeApprovalWorklists,
                    'financePaymentWorklists' => $financePaymentWorklists
        ]);
    }

    private function prepareWorklists($modelId, $module, $details) {
        $worklists = ClaimApprovalWorklist::find()->where(['claim_master_id' => $modelId, 'module' => $module])->indexBy('claim_detail_id')->all();

        foreach ($details as $detail) {
            if (!isset($worklists[$detail->id])) {
                $newWorklist = new ClaimApprovalWorklist();
                $newWorklist->claim_master_id = $modelId;
                $newWorklist->claim_detail_id = $detail->id;
                $newWorklist->responsed_by = Yii::$app->user->identity->id;
                $worklists[$detail->id] = $newWorklist;
            }
        }
        return $worklists;
    }

//submit -> superior approval -> finance approval -> payment
//    private function processClaimApproval($master, $claimDetail, $postData) {
//        $transaction = Yii::$app->db->beginTransaction();
//
//        try {
//            if (empty($postData)) {
//                throw new \Exception("Invalid claim approval data provided");
//            }
//
//// Process each claim detail
//            foreach ($postData as $claimDetail => $data) {
//                if (!isset($data['claim_status'])) {
//                    throw new \Exception("Missing claim status for claim detail ID: " . $data['claim_detail_id']);
//                }
//                $workList = new ClaimApprovalWorklist();
//                $workList->claim_master_id = $master->id;
//                $workList->claim_detail_id = $data['claim_detail_id'];
//                $workList->claim_status = $data['claim_status'];
//                if ($data['claim_status'] == ClaimMaster::STATUS_REJECTED) {
//                    if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
//                        $workList->status = RefGeneralStatus::STATUS_SuperiorRejected;
//                        $workList->module = ClaimMaster::MODULE_SUPERIOR_APPROVAL;
//                    } else if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
//                        $workList->status = RefGeneralStatus::STATUS_FinanceRejected;
//                        $workList->module = ClaimMaster::MODULE_FINANCE_APPROVAL;
//                    } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
//                        $workList->status = RefGeneralStatus::STATUS_WaitingForPayment;
//                        $workList->module = ClaimMaster::MODULE_FINANCE_PAYMENT;
//                        $claimDetail_status = ClaimMaster::STATUS_APPROVED;
//                        $claimDetail_paid = ClaimMaster::STATUS_HOLD_PAYMENT;
//                    }
//                } else {
//                    if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
//                        $workList->status = RefGeneralStatus::STATUS_GetFinanceApproval;
//                        $workList->module = ClaimMaster::MODULE_SUPERIOR_APPROVAL;
//                    } else if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
//                        $workList->status = RefGeneralStatus::STATUS_WaitingForPayment;
//                        $workList->module = ClaimMaster::MODULE_FINANCE_APPROVAL;
//                    } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
//                        $workList->status = RefGeneralStatus::STATUS_Paid;
//                        $workList->module = ClaimMaster::MODULE_FINANCE_PAYMENT;
//                        $claimDetail_paid = ClaimMaster::STATUS_PAID;
//                        $uploadedFile = \yii\web\UploadedFile::getInstanceByName("ClaimApprovalWorklist[{$claimDetail}][scannedFile]");
//
//                        if ($uploadedFile && $uploadedFile->error === UPLOAD_ERR_OK) {
//                            $fileName = $workList->savePaymentProof($uploadedFile, $master->claim_code);
//                            if ($fileName) {
//                                $workList->payment_proof_file = $fileName;
//                            } else {
//                                throw new \Exception("Failed to save uploaded payment proof file");
//                            }
//                        } else {
//                            throw new \Exception("No payment proof found");
//                        }
//                    }
//                }
//
//                $workList->remark = $data['remark'] ?? '';
//                if (!$workList->save()) {
//                    $errors = implode(', ', $workList->getFirstErrors());
//                    throw new \Exception("Failed to save worklist: " . $errors);
//                }
//
//// Update claim detail
//                $claimDetailRecord = ClaimDetail::findOne($workList->claim_detail_id);
//                if (!$claimDetailRecord) {
//                    throw new \Exception("Claim detail not found: " . $workList->claim_detail_id);
//                }
//
//                $claimDetailRecord->claim_status = $claimDetail_status ?? $workList->claim_status;
//                $claimDetailRecord->is_paid = $claimDetail_paid ?? 0;
//                $claimDetailRecord->payment_proof_file = $workList->payment_proof_file;
//                ;
//                if (!$claimDetailRecord->save()) {
//                    $errors = implode(', ', $claimDetailRecord->getFirstErrors());
//                    throw new \Exception("Failed to update claim detail: " . $errors);
//                }
//            }
//
//// Update master status logic
//            $this->updateMasterStatus($master);
//
//            if (!$master->save()) {
//                $errors = implode(', ', $master->getFirstErrors());
//                throw new \Exception("Failed to update master claim: " . $errors);
//            }
//
//            $transaction->commit();
//            FlashHandler::success('Claim approval processed successfully');
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            Yii::error("Claim processing failed: " . $e->getMessage(), __METHOD__);
//            FlashHandler::err($e->getMessage());
//            throw $e;
//        }
//    }
//
//    private function updateMasterStatus($master) {
//        if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
//            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
//            if ($hasPendingClaims) {
//                $master->claim_status = RefGeneralStatus::STATUS_GetFinanceApproval;
//            } else {
//                $master->claim_status = RefGeneralStatus::STATUS_SuperiorRejected;
//                $master->status_flag = 1;
//            }
//        } else if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
//            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
//            if ($hasPendingClaims) {
//                $master->claim_status = RefGeneralStatus::STATUS_WaitingForPayment;
//            } else {
//                $master->claim_status = RefGeneralStatus::STATUS_FinanceRejected;
//                $master->status_flag = 1;
//            }
//        } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
//            $hasPendingPayments = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 0, 'is_deleted' => 0])->exists();
//            if (!$hasPendingPayments) {
//                $master->claim_status = RefGeneralStatus::STATUS_Paid;
//                $master->status_flag = 1;
//                $master->has_payment = 0;
//            }
//
//            $hasPaymentRecord = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 1, 'is_deleted' => 0])->exists();
//            if ($hasPaymentRecord) {
//                $master->has_payment = 1;
//            }
//        }
//    }

    private function processClaimApproval($master, $claimDetail, $postData) {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (empty($postData)) {
                throw new \Exception("Invalid claim approval data provided");
            }

// Process each claim detail
            foreach ($postData as $claimDetail => $data) {
                if (!isset($data['claim_status'])) {
                    throw new \Exception("Missing claim status for claim detail ID: " . $data['claim_detail_id']);
                }
                $claimant = User::findOne($master->claimant_id);

                $workList = new ClaimApprovalWorklist();
                $workList->claim_master_id = $master->id;
                $workList->claim_detail_id = $data['claim_detail_id'];
                $workList->claim_status = $data['claim_status'];
                if ($data['claim_status'] == ClaimMaster::STATUS_REJECTED) {
                    if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
                        $workList->status = RefGeneralStatus::STATUS_SuperiorRejected;
                        $workList->module = ClaimMaster::MODULE_SUPERIOR_APPROVAL;
                    } else if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
                        $workList->status = RefGeneralStatus::STATUS_FinanceRejected;
                        $workList->module = ClaimMaster::MODULE_FINANCE_APPROVAL;
                    } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
                        $workList->status = RefGeneralStatus::STATUS_WaitingForPayment;
                        $workList->module = ClaimMaster::MODULE_FINANCE_PAYMENT;
                        $claimDetail_status = ClaimMaster::STATUS_APPROVED;
                        $claimDetail_paid = ClaimMaster::STATUS_HOLD_PAYMENT;
                    }
                } else {
                    if ($claimant->skip_claim_authorize == 1) {
                        if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
                            $workList->status = RefGeneralStatus::STATUS_WaitingForPayment;
                            $workList->module = ClaimMaster::MODULE_SUPERIOR_APPROVAL;
                        } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
                            $workList->status = RefGeneralStatus::STATUS_Paid;
                            $workList->module = ClaimMaster::MODULE_FINANCE_PAYMENT;
                            $claimDetail_paid = ClaimMaster::STATUS_PAID;
                            $uploadedFile = \yii\web\UploadedFile::getInstanceByName("ClaimApprovalWorklist[{$claimDetail}][scannedFile]");

                            if ($uploadedFile && $uploadedFile->error === UPLOAD_ERR_OK) {
                                $fileName = $workList->savePaymentProof($uploadedFile, $master->claim_code);
                                if ($fileName) {
                                    $workList->payment_proof_file = $fileName;
                                } else {
                                    throw new \Exception("Failed to save uploaded payment proof file");
                                }
                            } else {
                                throw new \Exception("No payment proof found");
                            }
                        }
                    } else {
                        if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
                            $workList->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
                            $workList->module = ClaimMaster::MODULE_FINANCE_APPROVAL;
                        } else if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
                            $workList->status = RefGeneralStatus::STATUS_WaitingForPayment;
                            $workList->module = ClaimMaster::MODULE_SUPERIOR_APPROVAL;
                        } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
                            $workList->status = RefGeneralStatus::STATUS_Paid;
                            $workList->module = ClaimMaster::MODULE_FINANCE_PAYMENT;
                            $claimDetail_paid = ClaimMaster::STATUS_PAID;
                            $uploadedFile = \yii\web\UploadedFile::getInstanceByName("ClaimApprovalWorklist[{$claimDetail}][scannedFile]");

                            if ($uploadedFile && $uploadedFile->error === UPLOAD_ERR_OK) {
                                $fileName = $workList->savePaymentProof($uploadedFile, $master->claim_code);
                                if ($fileName) {
                                    $workList->payment_proof_file = $fileName;
                                } else {
                                    throw new \Exception("Failed to save uploaded payment proof file");
                                }
                            } else {
                                throw new \Exception("No payment proof found");
                            }
                        }
                    }
                }

                $workList->remark = $data['remark'] ?? '';
                if (!$workList->save()) {
                    $errors = implode(', ', $workList->getFirstErrors());
                    throw new \Exception("Failed to save worklist: " . $errors);
                }

// Update claim detail
                $claimDetailRecord = ClaimDetail::findOne($workList->claim_detail_id);
                if (!$claimDetailRecord) {
                    throw new \Exception("Claim detail not found: " . $workList->claim_detail_id);
                }

                $claimDetailRecord->claim_status = $claimDetail_status ?? $workList->claim_status;
                $claimDetailRecord->is_paid = $claimDetail_paid ?? 0;
                $claimDetailRecord->payment_proof_file = $workList->payment_proof_file;
                ;
                if (!$claimDetailRecord->save()) {
                    $errors = implode(', ', $claimDetailRecord->getFirstErrors());
                    throw new \Exception("Failed to update claim detail: " . $errors);
                }
            }

// Update master status logic
            $this->updateMasterStatus($master);

            if (!$master->save()) {
                $errors = implode(', ', $master->getFirstErrors());
                throw new \Exception("Failed to update master claim: " . $errors);
            }

            $transaction->commit();
            FlashHandler::success('Claim approval processed successfully');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Claim processing failed: " . $e->getMessage(), __METHOD__);
            FlashHandler::err($e->getMessage());
            throw $e;
        }
    }

//    private function updateMasterStatus($master) {
//        if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
//            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
//            if ($hasPendingClaims) {
//                $master->claim_status = RefGeneralStatus::STATUS_GetFinanceApproval;
//            } else {
//                $master->claim_status = RefGeneralStatus::STATUS_SuperiorRejected;
//                $master->status_flag = 1;
//            }
//        } else if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
//            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
//            if ($hasPendingClaims) {
//                $master->claim_status = RefGeneralStatus::STATUS_WaitingForPayment;
//            } else {
//                $master->claim_status = RefGeneralStatus::STATUS_FinanceRejected;
//                $master->status_flag = 1;
//            }
//        } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
//            $hasPendingPayments = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 0, 'is_deleted' => 0])->exists();
//            if (!$hasPendingPayments) {
//                $master->claim_status = RefGeneralStatus::STATUS_Paid;
//                $master->status_flag = 1;
//                $master->has_payment = 0;
//            }
//
//            $hasPaymentRecord = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 1, 'is_deleted' => 0])->exists();
//            if ($hasPaymentRecord) {
//                $master->has_payment = 1;
//            }
//        }
//    }

    private function updateMasterStatus($master) {
        if ($master->claim_status == RefGeneralStatus::STATUS_GetFinanceApproval) {
            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
            if ($hasPendingClaims) {
                $master->claim_status = RefGeneralStatus::STATUS_GetSuperiorApproval;
            } else {
                $master->claim_status = RefGeneralStatus::STATUS_FinanceRejected;
                $master->status_flag = 1; //complete
            }
        } else if ($master->claim_status == RefGeneralStatus::STATUS_GetSuperiorApproval) {
            $hasPendingClaims = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_deleted' => 0])->exists();
            if ($hasPendingClaims) {
                $master->claim_status = RefGeneralStatus::STATUS_WaitingForPayment;
            } else {
                $master->claim_status = RefGeneralStatus::STATUS_SuperiorRejected;
                $master->status_flag = 1; //complete
            }
        } else if ($master->claim_status == RefGeneralStatus::STATUS_WaitingForPayment) {
            $hasPendingPayments = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 0, 'is_deleted' => 0])->exists();
            if (!$hasPendingPayments) {
                $master->claim_status = RefGeneralStatus::STATUS_Paid;
                $master->status_flag = 1; //complete
                $master->has_payment = 0; //no
            }

            $hasPaymentRecord = ClaimDetail::find()->where(['claim_master_id' => $master->id, 'claim_status' => 0, 'is_paid' => 1, 'is_deleted' => 0])->exists();
            if ($hasPaymentRecord) {
                $master->has_payment = 1; //yes
            }
        }
    }

    protected function findModel($id) {
        if (($model = ClaimMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*     * ************************* modal ************************************ */

    public function actionAjaxProdotmealDetail($prodotmealCode) {
        $record = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::find()->where(['ref_code' => $prodotmealCode])->one();

        return $this->renderAjax('_prodotmealDetail', [
                    'record' => $record,
        ]);
    }

    public function actionAjaxSickLeaveDetail($leaveCode) {
        $leaveRecord = LeaveMaster::find()->where(['leave_code' => $leaveCode])->one();

        return $this->renderAjax('_sickLeaveDetail', [
                    'leaveRecord' => $leaveRecord,
        ]);
    }

    public function actionAjaxTravelDetail($leaveCode, $claimMasterId = null) {
        $model = null;

        if ($claimMasterId && !empty(trim($claimMasterId))) {
            $model = ClaimMaster::findOne($claimMasterId);
            $detail = ClaimDetail::find()->where(['claim_master_id' => $claimMasterId, 'parent_id' => null])->one();
        }

        if (!$model) {
            $model = new ClaimMaster();
            $detail = new ClaimDetail();
        }
        $leaveRecord = LeaveMaster::find()->where(['leave_code' => $leaveCode])->one();

        if (!$leaveRecord) {
            return '<div class="alert alert-warning">Leave record not found.</div>';
        }

        return $this->renderAjax('_travelDetail', [
                    'leaveRecord' => $leaveRecord,
                    'model' => $model,
                    'detail' => $detail
        ]);
    }

    public function actionAjaxPrfDetail($prfCode) {
        $prfMaster = \frontend\models\office\preReqForm\PrereqFormMaster::find()->where(['prf_no' => $prfCode])->one();

        return $this->renderAjax('_prfDetail', [
                    'prfMaster' => $prfMaster
        ]);
    }

    /*     * ************************* AJAX Get Limit Value ************************************ */

    public function actionAjaxGetProdOtMealLimit($prodotmealCode) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $record = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::find()->where(['ref_code' => $prodotmealCode])->one();
        $limit = $record->total_amount;

        return [
            'success' => true,
            'limit' => $limit,
        ];
    }

    public function actionAjaxEhTravelAllowance($locationCode, $grade) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            // Validate input parameters
            if (empty($locationCode) || empty($grade)) {
                return [
                    'success' => false,
                    'message' => 'Missing required parameters: location code and grade are required',
                    'allowancePerDay' => 0
                ];
            }

            // Validate grade format if needed
            if (!$grade) {
                return [
                    'success' => false,
                    'message' => 'Invalid grade format',
                    'allowancePerDay' => 0
                ];
            }

            // Find active employee handbook
            $employeeHandbookMaster = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();

            if (!$employeeHandbookMaster) {
                return [
                    'success' => false,
                    'message' => 'No active employee handbook found. Please contact administrator.',
                    'allowancePerDay' => 0
                ];
            }

            // Find travel allowance master
            $ehTravelAllowanceMaster = EhTravelAllowanceMaster::find()->where(['eh_master_id' => $employeeHandbookMaster->id])->one();

            if (!$ehTravelAllowanceMaster) {
                return [
                    'success' => false,
                    'message' => 'Travel allowance configuration not found. Please contact administrator.',
                    'allowancePerDay' => 0
                ];
            }

            // Find specific allowance detail
            $ehTravelAllowanceDetail = EhTravelAllowanceDetail::find()->where(['eh_travel_allowance_master_id' => $ehTravelAllowanceMaster->id, 'eh_master_id' => $employeeHandbookMaster->id, 'grade' => $grade, 'location_type' => $locationCode])->one();

            if (!$ehTravelAllowanceDetail) {
                return [
                    'success' => false,
                    'message' => "No travel allowance found for grade '{$grade}' and selected location. Please contact HR department.",
                    'allowancePerDay' => 0
                ];
            }

            // Validate amount
            $allowanceAmount = (float) $ehTravelAllowanceDetail->amount_per_day;
            if ($allowanceAmount < 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid allowance amount found in system. Please contact administrator.',
                    'allowancePerDay' => 0
                ];
            }

            return [
                'success' => true,
                'allowancePerDay' => $allowanceAmount,
                'locationCode' => $locationCode,
                'grade' => $grade,
                'handbookVersion' => $employeeHandbookMaster->version ?? 'N/A'
            ];
        } catch (\yii\db\Exception $e) {
            Yii::error('Database error in actionAjaxEhTravelAllowance: ' . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'Database error occurred. Please try again later.',
                'allowancePerDay' => 0
            ];
        } catch (\Exception $e) {
            Yii::error('General error in actionAjaxEhTravelAllowance: ' . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'allowancePerDay' => 0
            ];
        }
    }

    //by receipt 
//    public function actionAjaxGetMedicalLimit() {
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        try {
//            $activeEH = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();
//            if (!$activeEH) {
//                return ['success' => false, 'message' => 'No active employee handbook found'];
//            }
//
//            $medicalMaster = EhOutpatientMedMaster::findOne(['eh_master_id' => $activeEH->id]);
//            if (!$medicalMaster) {
//                return ['success' => false, 'message' => 'No medical master found'];
//            }
//
//            $medicalLimit = EhOutpatientMedDetail::findOne([
//                'eh_master_id' => $activeEH->id,
//                'eh_outpatient_med_master_id' => $medicalMaster->id
//            ]);
//            if (!$medicalLimit) {
//                return ['success' => false, 'message' => 'No medical limit found'];
//            }
//
//            $limitAmount = $medicalLimit->amount_per_receipt;
//            return [
//                'success' => true,
//                'medicalLimit' => $limitAmount
//            ];
//        } catch (\Exception $e) {
//            return ['success' => false, 'message' => $e->getMessage()];
//        }
//    }

    public function actionAjaxGetMedicalLimit($receiptDate, $claimTypeCode) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $dateObj = new \DateTime($receiptDate);
            $year = $dateObj->format('Y');
            $month = (int) $dateObj->format('n');

            // Get active employee handbook
            $activeEH = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();
            if (!$activeEH) {
                throw new \Exception('No active employee handbook found');
            }

            $medicalMaster = EhOutpatientMedMaster::findOne(['eh_master_id' => $activeEH->id]);
            if (!$medicalMaster) {
                throw new \Exception('No medical master found');
            }

            $perReceiptLimit = (float) ($medicalMaster->per_receipt_limit ?? 30); // RM30 per receipt
            $monthlyLimit = (float) ($medicalMaster->monthly_limit ?? 50);       // RM50 per month
            // Sum all claims submitted in the same month/year
            $submittedClaims = ClaimDetail::find()
                    ->alias('cd')
                    ->innerJoin('claim_master cm', 'cm.id = cd.claim_master_id')
                    ->where([
                        'cd.claimant_id' => Yii::$app->user->identity->id,
                        'cm.claim_type' => $claimTypeCode,
                        'cm.is_deleted' => 0,
                        'cd.claim_status' => 0,
                        'cd.is_deleted' => 0
                    ])
                    ->andWhere(new \yii\db\Expression('YEAR(cd.receipt_date) = :year AND MONTH(cd.receipt_date) = :month', [
                                ':year' => $year,
                                ':month' => $month,
                            ]))
                    ->sum('cd.amount_to_be_paid');

            $submittedClaim = (float) ($submittedClaims ?? 0);
            $balance = max($monthlyLimit - $submittedClaim, 0);

            return [
                'success' => true,
                'perReceiptLimit' => $perReceiptLimit,
                'monthlyLimit' => $monthlyLimit,
                'monthlyBalance' => $balance,
                'claimedAmount' => $submittedClaim,
                'periodYear' => $year,
                'periodMonth' => $month,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // petrol, telephone, repair and maintenance 
    public function actionAjaxGetMonthlyLimit($receiptDate, $claimTypeCode) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $dateObj = new \DateTime($receiptDate);
            $year = $dateObj->format('Y');
            $month = (int) $dateObj->format('n');

            $entitleMaster = ClaimEntitlement::find()->where(['user_id' => Yii::$app->user->identity->id, 'status' => RefGeneralStatus::STATUS_Approved, 'year' => $year, 'is_active' => 1])->one();

            if (!$entitleMaster) {
                return ['success' => false, 'message' => 'No active claim entitlement found.'];
            }

            $entitleDetail = ClaimEntitlementDetails::find()
                    ->where([
                        'claim_entitle_id' => $entitleMaster->id,
                        'claim_type_code' => $claimTypeCode,
                    ])
                    ->andWhere(['<=', 'month_start', $month])
                    ->andWhere(['>=', 'month_end', $month])
                    ->one();

            if (!$entitleDetail) {
                return ['success' => false, 'message' => 'No entitlement found for this claim type and month.'];
            }

            $submittedClaims = ClaimDetail::find()
                    ->alias('cd')
                    ->innerJoin('claim_master cm', 'cm.id = cd.claim_master_id')
                    ->where([
                        'cd.claimant_id' => Yii::$app->user->identity->id,
                        'cm.claim_type' => $claimTypeCode,
                        'cm.is_deleted' => 0,
                        'cd.claim_status' => 0,
                        'cd.is_deleted' => 0
                    ])
                    ->andWhere(new Expression('YEAR(cd.receipt_date) = :year AND MONTH(cd.receipt_date) = :month', [
                                ':year' => $year,
                                ':month' => $month,
                            ]))
                    ->sum('cd.amount_to_be_paid');

            $submittedClaim = (float) ($submittedClaims ?? 0);
            $balance = $entitleDetail->amount - $submittedClaim;
            return [
                'success' => true,
                'no_limit_sts' => $entitleDetail->no_limit,
                'claimLimit' => (float) $balance,
                'entitlementAmount' => $entitleDetail->amount
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionAjaxGetExecOtMealLimit($receiptDate, $claimTypeCode) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $dateObj = new \DateTime($receiptDate);
            $year = $dateObj->format('Y');
            $month = (int) $dateObj->format('n');

            // Get active Employee Handbook
            $activeEH = EmployeeHandbookMaster::find()->where(['is_active' => 1])->one();
            if (!$activeEH) {
                throw new \Exception('No active employee handbook found');
            }

            // Get Executive OT Meal settings
            $execOtMeal = EhExecOtMeal::find()->where(['eh_master_id' => $activeEH->id])->one();

            if (!$execOtMeal) {
                throw new \Exception('Executive OT Meal configuration not found');
            }

            $execOtMealDetail = EhExecOtMealDetail::find()->where([
                        'eh_exec_ot_meal_id' => $execOtMeal->id,
                        'eh_master_id' => $activeEH->id,
                        'type' => EhExecOtMeal::EXEC_PERSONAL_OT_MEAL_CODE])
                    ->one();

            $perdayLimit = (float) ($execOtMealDetail->amount_per_day ?? 0);

            // Get total claims submitted for the same date
            $submittedClaims = ClaimDetail::find()
                    ->alias('cd')
                    ->innerJoin('claim_master cm', 'cm.id = cd.claim_master_id')
                    ->where([
                        'cd.claimant_id' => Yii::$app->user->identity->id,
                        'cm.claim_type' => $claimTypeCode,
                        'cm.is_deleted' => 0,
                        'cd.claim_status' => 0,
                        'cd.is_deleted' => 0
                    ])
                    ->andWhere(['cd.receipt_date' => $dateObj->format('Y-m-d')])
                    ->sum('cd.amount_to_be_paid');

            $submittedClaim = (float) ($submittedClaims ?? 0);
            $balance = max($perdayLimit - $submittedClaim, 0);

            return [
                'success' => true,
                'perdayLimit' => $perdayLimit,
                'perdayBalance' => $balance,
                'claimedAmount' => $submittedClaim,
                'periodYear' => $year,
                'periodMonth' => $month,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionAjaxGetPetrolLimit($receiptDate, $claimTypeCode) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $dateObj = new \DateTime($receiptDate);
            $year = $dateObj->format('Y');
            $month = (int) $dateObj->format('n');

            $entitleMaster = ClaimEntitlement::find()->where(['user_id' => Yii::$app->user->identity->id, 'status' => RefGeneralStatus::STATUS_Approved, 'year' => $year, 'is_active' => 1])->one();

            if (!$entitleMaster) {
                return ['success' => false, 'message' => 'No active claim entitlement found.'];
            }

            $entitleDetail = ClaimEntitlementDetails::find()
                    ->where([
                        'claim_entitle_id' => $entitleMaster->id,
                        'claim_type_code' => $claimTypeCode,
                    ])
                    ->andWhere(['<=', 'month_start', $month])
                    ->andWhere(['>=', 'month_end', $month])
                    ->one();

            if (!$entitleDetail) {
                return ['success' => false, 'message' => 'No entitlement found for this claim type and month.'];
            }

            $submittedClaim = 0;
            $claimMaster = ClaimMaster::find()->where(['claimant_id' => Yii::$app->user->identity->id, 'claim_type' => RefClaimType::codePetrol, 'is_deleted' => 0])->one();
            if ($claimMaster) {
                $submittedClaim = ClaimDetail::find()
                        ->where([
                            'claimant_id' => Yii::$app->user->identity->id,
                            'claim_master_id' => $claimMaster->id,
                        ])
                        ->andWhere(new Expression('YEAR(receipt_date) = :year AND MONTH(receipt_date) = :month', [
                                    ':year' => $year,
                                    ':month' => $month,
                                ]))
                        ->sum('amount_to_be_paid');
            }

            $balance = ($entitleDetail->amount) - $submittedClaim;
            return [
                'success' => true,
                'claimLimit' => (float) $balance,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionUserManualPersonal() {
        $this->layout = false;
        $fileName = ClaimMaster::PERSONAL_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManualSuperior() {
        $this->layout = false;
        $fileName = ClaimMaster::SUPERIOR_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManualFinance() {
        $this->layout = false;
        $fileName = ClaimMaster::FINANCE_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }
}
