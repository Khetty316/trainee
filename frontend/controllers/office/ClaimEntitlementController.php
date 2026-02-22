<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\claim\ClaimEntitlement;
use frontend\models\office\claim\ClaimEntitlementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\claim\RefClaimType;
use common\models\User;
use common\models\myTools\MyCommonFunction;
use frontend\models\office\claim\ClaimEntitlementDetails;
use common\models\myTools\FlashHandler;
use frontend\models\RefGeneralStatus;
use frontend\models\office\claim\ClaimEntitleWorklist;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * ClaimEntitlementController implements the CRUD actions for ClaimEntitlement model.
 */
class ClaimEntitlementController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/claim-entitlement/');
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
                        'actions' => ['superior-all-approval', 'superior-pending-approval', 'superior-view-detail', 'ajax-superior-approval', 'user-manual'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_CE_Superior],
                    ],
                    [
                        'actions' => ['export-to-excel', 'pending-approval', 'hr-all-approval', 'create', 'update', 'user-manual', 'deactivate', 'migrate-claim-entitlement-summary', 'hr-claim-summary'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_CE_HR],
                    ],
                ],
            ],
        ];
    }

    public function actionExportToExcel() {
        $request = Yii::$app->request;

        // Get parameters
        $year = $request->post('year');
        $month = $request->post('month');
        $claimTypeCode = $request->post('claimType');
        $staff = $request->post('staff');
        $hasEntitlement = $request->post('hasEntitlement');

        // Decode JSON parameters
        $claimSummarys = json_decode($request->post('claimSummarys'), true) ?: [];
        $monthList = json_decode($request->post('monthlist'), true) ?: [];
        $intMonth = json_decode($request->post('intMonth'), true) ?: [];

        // Determine intMonth if empty
        if (empty($intMonth) && !empty($month)) {
            $intMonth = [(int) $month];
        } elseif (empty($intMonth) && !empty($monthList)) {
            $intMonth = array_map('intval', array_keys($monthList));
        }

        // Sort by fullname
        usort($claimSummarys, function ($a, $b) {
            return strcasecmp($a['fullname'] ?? '', $b['fullname'] ?? '');
        });

        // Get claim type name
        $claimType = RefClaimType::find()
                ->select('claim_name')
                ->where(['code' => $claimTypeCode])
                ->scalar();

        // Get month name
        $monthName = (count($intMonth) === 1 && isset($monthList[$month])) ? $monthList[$month] : '';

        // Generate filename (KEEP THIS - JavaScript reads it from header)
        $filename = 'Claim_Summary_Report_' . $year;
        if (!empty($monthName)) {
            $filename .= '_' . $monthName;
        } elseif (count($intMonth) > 1) {
            $filename .= '_Yearly';
        }
        if (!empty($claimType)) {
            $filename .= '_' . str_replace(' ', '_', $claimType);
        }
        $filename .= '.xls';

        // Set response headers
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $response->data = $this->renderPartial('_entitlementSummaryCSV', [
            'claimSummarys' => $claimSummarys,
            'hasEntitlement' => $hasEntitlement,
            'intMonth' => $intMonth,
            'year' => $year,
            'month' => $month,
            'claimType' => $claimType,
            'staff' => $staff,
            'monthList' => $monthList,
            'monthName' => $monthName,
        ]);

        return $response;
    }

    public function actionHrClaimSummary($year = '', $month = '', $claim_type = '', $staff = '') {
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
        ]);
    }

    public function actionDeactivate() {
        $master = ClaimEntitlement::findOne(Yii::$app->request->get('id'));
        $master->is_active = 0;
        if ($master->save(false)) {
            FlashHandler::success("Successfully deactivated.");
        }

        $this->redirect('pending-approval');
    }

    public function actionPendingApproval() {
        $searchModel = new ClaimEntitlementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingHr');

        return $this->render('pendingApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hr' => true
        ]);
    }

    public function actionHrAllApproval() {
        $searchModel = new ClaimEntitlementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'all');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hr' => true
        ]);
    }

    public function actionCreate() {
        $model = new ClaimEntitlement();
        $claimType = RefClaimType::getClaimTypeforEntitlement();
        $staffList = User::getActiveExexGradeDropDownList();
        $currentYear = (int) date("Y");
        $yearsList = [
            ($currentYear + 1) => ($currentYear + 1),
            $currentYear => $currentYear,
            ($currentYear - 1) => ($currentYear - 1)
        ];

        $selectYear = (int) date("Y");

        // Create empty detail models for the form
        $claimDetails = [];
        foreach ($claimType as $key => $type) {
            $claimDetails[$key] = new ClaimEntitlementDetails();
            $claimDetails[$key]->claim_type_code = $type['code'];
        }

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->year = Yii::$app->request->post('selectYear');
                $model->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
                $model->user_id = Yii::$app->request->post('user_id');
                $model->superior_id = $model->user->superior_id;
                if ($model->save()) {
                    // Load and save details
                    $detailsData = Yii::$app->request->post('ClaimEntitlementDetails', []);

                    foreach ($detailsData as $key => $data) {
                        $detail = new ClaimEntitlementDetails();
                        $detail->attributes = $data;
                        $detail->claim_entitle_id = $model->id; // Link to parent

                        if (!$detail->save()) {
                            throw new Exception('Failed to save detail: ' . json_encode($detail->errors));
                        }
                    }

                    $transaction->commit();
                    return $this->redirect(['pending-approval']);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Failed to save: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
                    'model' => $model,
                    'claimType' => $claimType,
                    'claimDetails' => $claimDetails,
                    'staffList' => $staffList,
                    'yearsList' => $yearsList,
                    'selectYear' => $selectYear,
        ]);
    }

//    public function actionUpdate($id) {
//        $model = ClaimEntitlement::findOne($id);
//        if (!$model) {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//
//        $claimType = RefClaimType::getClaimTypeforEntitlement();
//        $staffList = User::getActiveExexGradeDropDownList();
//        $yearsList = MyCommonFunction::getYearListFromTable('claim_entitlement', 'year');
//        $selectYear = $model->year;
//        $entitleStatus = ClaimEntitleWorklist::find()->where(['claim_entitle_id' => $model->id])->orderBy(['created_at' => SORT_DESC])->one();
//
//        // Load existing details
//        $existingDetails = ClaimEntitlementDetails::find()
//                ->where(['claim_entitle_id' => $id])
//                ->indexBy('claim_type_code')
//                ->all();
//
//        $claimDetails = [];
//        foreach ($claimType as $key => $type) {
//            $claimTypeCode = $type['code'];
//            if (isset($existingDetails[$claimTypeCode])) {
//                $claimDetails[$key] = $existingDetails[$claimTypeCode];
//            } else {
//                $claimDetails[$key] = new ClaimEntitlementDetails();
//                $claimDetails[$key]->claim_type_code = $claimTypeCode;
//                $claimDetails[$key]->claim_entitle_id = $id;
//            }
//        }
//
//        if ($model->load(Yii::$app->request->post())) {
//
//            $transaction = Yii::$app->db->beginTransaction();
//            try {
//                $model->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
//
//                if ($model->save()) {
//                    $detailsData = Yii::$app->request->post('ClaimEntitlementDetails', []);
//                    $processedClaimTypes = [];
//
//                    foreach ($detailsData as $key => $data) {
//                        $claimTypeCode = $data['claim_type_code'];
//                        $processedClaimTypes[] = $claimTypeCode;
//
//                        // Update existing or create new
//                        if (isset($existingDetails[$claimTypeCode])) {
//                            $detail = $existingDetails[$claimTypeCode];
//                        } else {
//                            $detail = new ClaimEntitlementDetails();
//                            $detail->claim_entitle_id = $id;
//                        }
//
//                        $detail->attributes = $data;
//
//                        if (!$detail->save()) {
//                            throw new \Exception('Failed to save detail: ' . json_encode($detail->errors));
//                        }
//                    }
//
//                    // Delete any existing details that weren't processed
//                    foreach ($existingDetails as $claimTypeCode => $detail) {
//                        if (!in_array($claimTypeCode, $processedClaimTypes)) {
//                            $detail->delete();
//                        }
//                    }
//
//                    $transaction->commit();
//                    FlashHandler::success('Claim entitlement updated successfully.');
//                } else {
//                    
//                }
//            } catch (Exception $e) {
//                $transaction->rollBack();
//                throw new \Exception('Failed to update: ' . $e->getMessage());
//            }
//            return $this->redirect(['pending-approval']);
//        }
//
//        return $this->render('update', [
//                    'model' => $model,
//                    'claimType' => $claimType,
//                    'claimDetails' => $claimDetails,
//                    'staffList' => $staffList,
//                    'yearsList' => $yearsList,
//                    'selectYear' => $selectYear,
//                    'entitleStatus' => $entitleStatus,
//                    'hr' => true
//        ]);
//    }

    public function actionUpdate($id) {
        $model = ClaimEntitlement::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $claimType = RefClaimType::getClaimTypeforEntitlement();
        $staffList = User::getActiveExexGradeDropDownList();
        $yearsList = MyCommonFunction::getYearListFromTable('claim_entitlement', 'year');
        $selectYear = $model->year;
        $entitleStatus = ClaimEntitleWorklist::find()->where(['claim_entitle_id' => $model->id])->orderBy(['created_at' => SORT_DESC])->one();

        // Load existing details
        $claimDetails = ClaimEntitlementDetails::find()
                ->where(['claim_entitle_id' => $id])
                ->indexBy('claim_type_code')
                ->all();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = RefGeneralStatus::STATUS_GetSuperiorApproval;

                if ($model->save()) {
                    $detailsData = Yii::$app->request->post('ClaimEntitlementDetails', []);
                    $processedClaimTypes = [];

                    foreach ($detailsData as $key => $data) {
                        $claimTypeCode = $data['claim_type_code'];
                        $processedClaimTypes[] = $claimTypeCode;

                        // Update existing or create new
                        if (isset($claimDetails[$claimTypeCode])) {
                            $detail = $claimDetails[$claimTypeCode];
                        } else {
                            $detail = new ClaimEntitlementDetails();
                            $detail->claim_entitle_id = $id;
                        }

                        $detail->attributes = $data;

                        if (!$detail->save()) {
                            throw new \Exception('Failed to save detail: ' . json_encode($detail->errors));
                        }
                    }

                    // Delete any existing details that weren't processed
                    foreach ($claimDetails as $claimTypeCode => $detail) {
                        if (!in_array($claimTypeCode, $processedClaimTypes)) {
                            $detail->delete();
                        }
                    }

                    $transaction->commit();
                    FlashHandler::success('Claim entitlement updated successfully.');
                } else {
                    
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                throw new \Exception('Failed to update: ' . $e->getMessage());
            }
            return $this->redirect(['pending-approval']);
        }

        return $this->render('update', [
                    'model' => $model,
                    'claimType' => $claimType,
                    'claimDetails' => $claimDetails,
                    'staffList' => $staffList,
                    'yearsList' => $yearsList,
                    'selectYear' => $selectYear,
                    'entitleStatus' => $entitleStatus,
                    'hr' => true
        ]);
    }

    public function actionSuperiorAllApproval() {
        $searchModel = new ClaimEntitlementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'all');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hr' => false
        ]);
    }

    public function actionSuperiorPendingApproval() {
        $searchModel = new ClaimEntitlementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingSuperior');

        return $this->render('pendingApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hr' => false
        ]);
    }

//    public function actionSuperiorViewDetail($id) {
//        $model = ClaimEntitlement::findOne($id);
//        if (!$model) {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//
//        $claimType = RefClaimType::getClaimTypeforEntitlement();
//        $staffList = User::getActiveExexGradeDropDownList();
//        $yearsList = MyCommonFunction::getYearListFromTable('claim_entitlement', 'year');
//        $selectYear = $model->year;
//        $entitleStatus = ClaimEntitleWorklist::find()->where(['claim_entitle_id' => $model->id])->orderBy(['created_at' => SORT_DESC])->one();
//
//        // Load existing details
//        $existingDetails = ClaimEntitlementDetails::find()
//                ->where(['claim_entitle_id' => $id])
//                ->indexBy('claim_type_code')
//                ->all();
//
//        $claimDetails = [];
//        foreach ($claimType as $key => $type) {
//            $claimTypeCode = $type['code'];
//            if (isset($existingDetails[$claimTypeCode])) {
//                $claimDetails[$key] = $existingDetails[$claimTypeCode];
//            } else {
//                $claimDetails[$key] = new ClaimEntitlementDetails();
//                $claimDetails[$key]->claim_type_code = $claimTypeCode;
//                $claimDetails[$key]->claim_entitle_id = $id;
//            }
//        }
//
//        return $this->render('superiorViewDetail', [
//                    'model' => $model,
//                    'claimType' => $claimType,
//                    'claimDetails' => $claimDetails,
//                    'staffList' => $staffList,
//                    'yearsList' => $yearsList,
//                    'selectYear' => $selectYear,
//                    'entitleStatus' => $entitleStatus,
//                    'hr' => false
//        ]);
//    }

    public function actionSuperiorViewDetail($id) {
        $model = ClaimEntitlement::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $claimType = RefClaimType::getClaimTypeforEntitlement();
        $staffList = User::getActiveExexGradeDropDownList();
        $yearsList = MyCommonFunction::getYearListFromTable('claim_entitlement', 'year');
        $selectYear = $model->year;
        $entitleStatus = ClaimEntitleWorklist::find()->where(['claim_entitle_id' => $model->id])->orderBy(['created_at' => SORT_DESC])->one();

        // Load existing details
        $claimDetails = ClaimEntitlementDetails::find()
                ->where(['claim_entitle_id' => $id])
                ->indexBy('claim_type_code')
                ->all();

        return $this->render('superiorViewDetail', [
                    'model' => $model,
                    'claimType' => $claimType,
                    'claimDetails' => $claimDetails,
                    'staffList' => $staffList,
                    'yearsList' => $yearsList,
                    'selectYear' => $selectYear,
                    'entitleStatus' => $entitleStatus,
                    'hr' => false
        ]);
    }

    public function actionAjaxSuperiorApproval($id, $status) {
        $model = ClaimEntitlement::findOne($id);

        if (!$model) {
            FlashHandler::err("Claim entitlement not found.");
            return $this->redirect(['director-pending-approval']);
        }

        $claimEntitleWorklist = new ClaimEntitleWorklist();

        if ($claimEntitleWorklist->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post('ClaimEntitleWorklist');
            $remark = $postData['remark'] ?? '';
            $claimEntitleWorklist->claim_entitle_id = $model->id;
            $claimEntitleWorklist->claim_entitle_status = $status;
            $claimEntitleWorklist->responsed_by = Yii::$app->user->id;
            $claimEntitleWorklist->remark = $remark;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($claimEntitleWorklist->save()) {
                    $model->status = $claimEntitleWorklist->claim_entitle_status;
                    if ($model->save()) {
                        $transaction->commit();
                        $statusText = ($status == RefGeneralStatus::STATUS_Approved) ? 'approved' : 'rejected';
                        FlashHandler::success("Claim entitlement successfully {$statusText}");
                    } else {
                        $transaction->rollBack();
                        FlashHandler::err("Operation failed. Failed to update the master status.");
                    }
                } else {
                    $transaction->rollBack();
                    FlashHandler::err("Operation failed. Please try again.");
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("An error occurred: " . $e->getMessage());
            }
            return $this->redirect(['superior-pending-approval']);
        }

        return $this->renderAjax('_formApproval', [
                    'claimEntitleWorklist' => $claimEntitleWorklist,
                    'status' => $status,
                    'hr' => false
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = ClaimEntitlement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUserManualSuperior() {
        $this->layout = false;
        $fileName = ClaimEntitlement::SUPERIOR_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManualHr() {
        $this->layout = false;
        $fileName = ClaimEntitlement::HR_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManual() {
        $this->layout = false;
        $fileName = ClaimEntitlement::USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionMigrateClaimEntitlementSummary() {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $summary = new \frontend\models\office\claim\ClaimEntitlementSummary();
            // Fetch all entitlement details
            $entitlementDetails = (new \yii\db\Query())
                    ->select(['ced.*', 'ce.user_id', 'ce.year'])
                    ->from(['ced' => 'claim_entitlement_details'])
                    ->innerJoin(['ce' => 'claim_entitlement'], 'ce.id = ced.claim_entitle_id')
                    ->where(['ce.is_active' => 1])
                    ->all();

            $insertCount = 0;
            $updateCount = 0;
            $errorCount = 0;

            foreach ($entitlementDetails as $detail) {
                // Get claim master ID by claim type code
                $claimMaster = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('claim_master')
                        ->where(['claim_type' => $detail['claim_type_code']])
                        ->one();

                if (!$claimMaster) {
                    echo "Warning: No claim master found for type: {$detail['claim_type_code']}\n";
                    $errorCount++;
                    continue;
                }

                $masterId = $claimMaster['id'];
                $userId = $detail['user_id'];
                $year = $detail['year'];
                $monthStart = intval($detail['month_start']);
                $monthEnd = intval($detail['month_end']);
                $limit = floatval($detail['amount']);

                // Process each month
                for ($month = $monthStart; $month <= $monthEnd; $month++) {
                    // Calculate date range
                    $dateFrom = $summary->getDateFrom($year, $month);
                    $dateTo = $summary->getDateTo($year, $month);

                    // Calculate claimed amount
                    $amountClaimed = $summary->getClaimedAmount($userId, $detail['claim_type_code'], $dateFrom, $dateTo);
                    $balanceAmt = $limit - $amountClaimed;

                    // Check if record exists
                    $exists = (new \yii\db\Query())
                            ->from('claim_entitlement_summary')
                            ->where([
                                'master_id' => $detail['claim_entitle_id'],
                                'detail_id' => $detail['id'],
                                'user_id' => $userId,
                                'month' => $month,
                                'year' => $year,
                                'claim_type_code' => $detail['claim_type_code']
                            ])
                            ->exists();

                    if (!$exists) {
                        // Insert new record
                        Yii::$app->db->createCommand()->insert('claim_entitlement_summary', [
                            'master_id' => $detail['claim_entitle_id'],
                            'detail_id' => $detail['id'],
                            'user_id' => $userId,
                            'month' => $month,
                            'year' => $year,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'claim_type_code' => $detail['claim_type_code'],
                            'monthly_limit' => $limit,
                            'amount_claimed' => $amountClaimed,
                            'balance_amt' => $balanceAmt,
                            'created_at' => new \yii\db\Expression('NOW()'),
                        ])->execute();

                        $insertCount++;
                        echo "<br>INSERT: User {$userId}, Type {$detail['claim_type_code']}, {$month}/{$year}, Claimed: {$amountClaimed}<br>";
                    } else {
                        // Update existing record
                        Yii::$app->db->createCommand()->update('claim_entitlement_summary', [
                                    'master_id' => $detail['claim_entitle_id'],
                                    'detail_id' => $detail['id'],
                                    'date_from' => $dateFrom,
                                    'date_to' => $dateTo,
                                    'claim_type_code' => $detail['claim_type_code'],
                                    'monthly_limit' => $limit,
                                    'amount_claimed' => $amountClaimed,
                                    'balance_amt' => $balanceAmt,
                                        ], [
                                    'user_id' => $userId,
                                    'month' => $month,
                                    'year' => $year,
                                    'master_id' => $detail['claim_entitle_id'],
                                    'detail_id' => $detail['id'],
                                    'claim_type_code' => $detail['claim_type_code'],
                                ])
                                ->execute();

                        $updateCount++;
                        echo "<br>UPDATE: User {$userId}, Type {$detail['claim_type_code']}, {$month}/{$year}, Claimed: {$amountClaimed}<br>";
                    }
                }
            }

            $transaction->commit();

            echo "<br>=================================<br>";
            echo "Migration completed successfully!<br>";
            echo "Total records inserted: {$insertCount}<br>";
            echo "Total records updated: {$updateCount}<br>";
            echo "Total errors: {$errorCount}<br>";
            echo "=================================<br>";
        } catch (Exception $e) {
            $transaction->rollback();
            echo "❌ Migration failed: " . $e->getMessage() . "<br>";
            echo "Stack trace: " . $e->getTraceAsString() . "<br>";
            Yii::error($e->getMessage(), 'migration');
        }
    }
}
