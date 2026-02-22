<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\pettyCash\PettyCashRequestMaster;
use frontend\models\office\pettyCash\PettyCashRequestMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\pettyCash\PettyCashRequestPre;
use frontend\models\office\pettyCash\PettyCashRequestPost;
use frontend\models\office\pettyCash\PettyCashRequestPostAttachment;
use frontend\models\RefGeneralStatus;
use common\models\myTools\FlashHandler;
use frontend\models\office\pettyCash\PettyCashLedgerMaster;
use frontend\models\office\pettyCash\PettyCashLedgerDetail;
use frontend\models\office\pettyCash\PettyCashReplenishment;
use frontend\models\office\pettyCash\PettyCashReplenishmentSearch;
use frontend\models\office\pettyCash\PettyCashLedgerMasterSearch;
use frontend\models\office\pettyCash\PettyCashLedgerDetailSearch;
use yii\web\UploadedFile;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * PettyCashController implements the CRUD actions for PettyCashRequestMaster model.
 */
class PettyCashController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/petty-cash/');
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
                        'actions' => ['read-pdf', 'user-manual', 'personal-pending', 'personal-all', 'view', 'create', 'update', 'return-receipt', 'cancel-return-receipt', 'save-attachments', 'ajax-delete-attachment', 'cancel-form-pre', 'view-receipt'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PC_Normal],
                    ],
                    [
                        'actions' => ['read-pdf', 'user-manual', 'ajax-view-ledger-detail-list', 'cancel-form-pre', 'finance-approval-pending', 'finance-approval-all', 'finance-view-form', 'finance-verify-pre-form', 'view-receipt', 'finance-verify-receipt', 'finance-confirm-receipt-completed', 'finance-ledger', 'finance-replenishment', 'request-replenishment', 'finance-view-replenishment-request', 'update-replenishment-request', 'cancel-replenishment-request', 'finance-confirm-replenishment-completed', 'finance-view-ledger', 'ajax-export-ledger-csv'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PC_Finance],
                    ],
                    [
                        'actions' => ['user-manual', 'ajax-view-ledger-detail-list', 'director-approval-pending', 'director-approval-all', 'director-view-replenishment-request', 'director-approval-replenishment-request'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Director],
                    ],
                ],
            ],
        ];
    }

    public function actionUserManual() {
        $this->layout = false;
        $fileName = PettyCashRequestPre::USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionPersonalPending() {
        $searchModel = new PettyCashRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingPersonal');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 1,
                    'module' => 'personal'
        ]);
    }

    public function actionPersonalAll() {
        $searchModel = new PettyCashRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allPersonal');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 2,
                    'module' => 'personal'
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        $preForm = PettyCashRequestPre::findOne(['pc_request_master_id' => $model->id, 'deleted_by' => null]);
        $postForm = PettyCashRequestPost::find()->where(['pc_request_pre_id' => $preForm->id, 'deleted_by' => null, 'deleted_at' => null])->one() ?? null;

        return $this->render('view', [
                    'model' => $model,
                    'preForm' => $preForm,
                    'postForm' => $postForm,
                    'module' => 'personal'
        ]);
    }

    public function actionCreate() {
        $model = new PettyCashRequestMaster();
        $preForm = new PettyCashRequestPre();

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->ref_code = $model->generateRefCode();
                $model->status = RefGeneralStatus::STATUS_GetFinanceApproval;

                if (!$model->save()) {
                    throw new \Exception('Failed to save form.');
                }

                // Load and save related preForm
                $preForm->load(Yii::$app->request->post());
                $preForm->pc_request_master_id = $model->id;
                $preForm->status = 0;

                if (!$preForm->save()) {
                    throw new \Exception('Failed to save form detail.');
                }

                $transaction->commit();
                FlashHandler::success('Request submitted successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['personal-pending']);
        }

        return $this->renderAjax('_form', [
                    'model' => $model,
                    'preForm' => $preForm,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $preForm = PettyCashRequestPre::findOne(['pc_request_master_id' => $model->id]);
        if (!$preForm) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $preForm->load(Yii::$app->request->post());
                if (!$preForm->save(false)) {
                    throw new \Exception('Failed to save form detail.');
                }

                $transaction->commit();
                FlashHandler::success('Request updated successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['personal-pending']);
        }

        return $this->renderAjax('_form', [
                    'model' => $model,
                    'preForm' => $preForm,
        ]);
    }

    public function actionReturnReceipt($id) {
        $model = $this->findModel($id);
        $preForm = PettyCashRequestPre::findOne(['pc_request_master_id' => $model->id]);
        if (!$preForm) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        // Find existing postForm (in case user reopens to reupload)
        $postForm = PettyCashRequestPost::findOne(['pc_request_pre_id' => $preForm->id, 'deleted_by' => null]) ?? new PettyCashRequestPost();

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Update master status
                $model->status = RefGeneralStatus::STATUS_WaitingForReceiptVerification;
                if (!$model->save(false)) {
                    throw new \Exception('Failed to update master status.');
                }

                // Load submitted data
                if (!$postForm->load(Yii::$app->request->post())) {
                    throw new \Exception('Invalid form submission.');
                }

                // Ensure core fields are assigned
                $postForm->pc_request_master_id = $model->id;
                $postForm->pc_request_pre_id = $preForm->id;
                $postForm->status = 0;
                $postForm->responsed_by = null;
                $postForm->responsed_at = null;
                $postForm->responsed_remark = null;
                $postForm->amount_approved = null;

                if (!$postForm->save()) {
                    throw new \Exception('Failed to save receipt detail.');
                }

                // Handle attachments
                $uploadedFiles = UploadedFile::getInstances($postForm, 'attachments');

// if no attachments at all (new + existing)
                $existingAttachmentsCount = PettyCashRequestPostAttachment::find()
                        ->where(['pc_request_post_id' => $postForm->id, 'deleted_at' => null])
                        ->count();

                if (empty($uploadedFiles) && $existingAttachmentsCount == 0) {
                    throw new \Exception('Please upload at least one PDF attachment.');
                }

                // Upload directory
                $uploadDir = Yii::getAlias('@frontend/uploads/petty_cash_request_post_attachments/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                // Save new attachments only
                if (!empty($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        if (strtolower($file->extension) !== 'pdf') {
                            throw new \Exception('Only PDF files are allowed.');
                        }

                        $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->baseName) . '.' . $file->extension;
                        $savePath = $uploadDir . $filename;

                        if (!$file->saveAs($savePath)) {
                            throw new \Exception('Failed to save uploaded file.');
                        }

                        $attachment = new PettyCashRequestPostAttachment([
                            'pc_request_post_id' => $postForm->id,
                            'file_name' => $filename,
                            'uploaded_by' => Yii::$app->user->id,
                        ]);

                        if (!$attachment->save()) {
                            throw new \Exception('Failed to save attachment record.');
                        }
                    }
                }

                $transaction->commit();
                FlashHandler::success('Return Receipt submitted successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['personal-pending']);
        }

        return $this->renderAjax('_formReceipt', [
                    'model' => $model,
                    'postForm' => $postForm,
                    'attachments' => $postForm->pettyCashRequestPostAttachments ?? [],
                    'module' => 'personal',
        ]);
    }

    public function actionCancelReturnReceipt($id) {
        $postForm = PettyCashRequestPost::findOne($id);

        $attachments = $postForm->pettyCashRequestPostAttachments;
        $uploadDir = Yii::getAlias('@frontend/uploads/petty_cash_request_post_attachments/');
        if ($attachments) {
            foreach ($attachments as $file) {
                $filePath = $uploadDir . $file->file_name;

                if (file_exists($filePath)) {
                    if (!unlink($filePath))
                        Yii::warning("Failed to delete file: {$filePath}");
                } else
                    Yii::warning("File not found: {$filePath}");

                $file->delete();
            }
        }

        PettyCashRequestPostAttachment::deleteAll(['pc_request_post_id' => $id]);

        $postForm->deleted_by = Yii::$app->user->identity->id;
        $postForm->deleted_at = new \yii\db\Expression('NOW()');

        if (!$postForm->save()) {
            throw new \Exception("Failed to delete form.");
        }

        $model = $this->findModel($postForm->pc_request_master_id);
        // Update master status
        $model->status = RefGeneralStatus::STATUS_PendingSupportedDocument;
        if (!$model->save(false)) {
            throw new \Exception('Failed to update master status.');
        }

        FlashHandler::success("Return receipt submission has been cancelled successfully.");
        return $this->redirect(['personal-pending']);
    }

    public function actionSaveAttachments($id) {
        $postForm = PettyCashRequestPost::findOne($id);
        $model = $this->findModel($postForm->pc_request_master_id);

        if ($model->load(Yii::$app->request->post())) {
            $files = UploadedFile::getInstances($postForm, 'attachments');

            $uploadDir = Yii::getAlias('@frontend/uploads/petty_cash_request_post_attachments/');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            foreach ($files as $file) {
                $filename = pathinfo($file->baseName, PATHINFO_FILENAME) . '.' . $file->extension;
                if (!$file->saveAs($uploadDir . $filename)) {
                    FlashHandler::err('Failed to upload attachments. Please try again');
                    return $this->renderAjax('_formReceipt', [
                                'model' => $model,
                                'postForm' => $postForm,
                                'attachments' => $postForm->pettyCashRequestPostAttachments
                    ]);
                }

                $attachment = PettyCashRequestPostAttachment::findOne([
                    'pc_request_post_id' => $postForm->id,
                    'file_name' => $file->name
                ]);

                if (!$attachment) {
                    $attachment = new PettyCashRequestPostAttachment();
                    $attachment->pc_request_post_id = $postForm->id;
                }

                $attachment->file_name = $file->name;
                $attachment->uploaded_by = Yii::$app->user->identity->fullname;

                if (!$attachment->save()) {
                    \common\models\myTools\Mydebug::dumpFileW($attachment->getErrors());
                }
            }
        }

        FlashHandler::success('Attachments uploaded successfully');
        return $this->renderAjax('_formReceipt', [
                    'model' => $model,
                    'postForm' => $postForm,
                    'attachments' => $postForm->pettyCashRequestPostAttachments
        ]);
    }

    public function actionAjaxDeleteAttachment($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $attachment = PettyCashRequestPostAttachment::findOne(['id' => $id]);

        if (!$attachment) {
            return ['success' => false, 'error' => 'Attachment not found'];
        }

        $uploadPath = Yii::getAlias('@frontend/uploads/petty_cash_request_post_attachments/');
        $filePath = $uploadPath . $attachment->file_name;

        $attachment->deleted_at = new \yii\db\Expression('NOW()');
        $attachment->deleted_by = Yii::$app->user->identity->id;

        if (!$attachment->save()) {
            return ['success' => false, 'error' => 'Failed to update database.'];
        }

        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                return ['success' => false, 'error' => 'File exists but could not be deleted.'];
            }
        } else {
            return ['success' => true, 'warning' => 'File record deleted but file not found on disk.'];
        }

        FlashHandler::success('Attachments deleted successfully');
        return ['success' => true];
    }

    public function actionReadPdf($id, $file_name) {
        $model = $this->findModel($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("The requested PDF does not exist.");
        }
        $pdfDir = Yii::getAlias('@frontend/uploads/petty_cash_request_post_attachments/');

        $pdfPath = $pdfDir . $file_name;

        if (!file_exists($pdfPath)) {
            throw new \yii\web\NotFoundHttpException("The requested PDF file was not found.");
        }

        Yii::$app->response->sendFile($pdfPath, $file_name . '.pdf', [
            'inline' => true,
        ])->send();
    }

    public function actionCancelFormPre($id, $module) {
        $model = PettyCashRequestMaster::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Form not found.');
        }

        $model->status = RefGeneralStatus::STATUS_ClaimantCancelClaim;
        $model->deleted_at = new \yii\db\Expression('NOW()');
        $model->deleted_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            FlashHandler::err('Failed to cancel the request.');
        } else {
            $preForm = PettyCashRequestPre::findOne(['pc_request_master_id' => $model->id]);
            $preForm->deleted_at = new \yii\db\Expression('NOW()');
            $preForm->deleted_by = Yii::$app->user->identity->id;

            $masterLedger = PettyCashLedgerMaster::findOne(['created_by' => $model->finance_id]);
            if ($masterLedger) {
                if ($this->recalculateRecordLedgerTransaction($masterLedger, $model) && $masterLedger->updateLedgerMasterAmount()) {
                    FlashHandler::success('The request has been canceled successfully.');
                }
            }
        }

        if ($module === 'personal') {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->redirect(['finance-view-form', 'id' => $model->id]);
        }
    }

    private function recalculateRecordLedgerTransaction($masterLedger, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $detail = PettyCashLedgerDetail::find()
                    ->where([
                        'pc_ledger_master_id' => $masterLedger->id,
                        'voucher_no' => $model->voucher_no,
                        'created_by' => Yii::$app->user->identity->id
                    ])
                    ->one();

            if (!$detail) {
                throw new \Exception('Ledger detail not found for this request.');
            }

            // Mark the entry as canceled (nullify amounts)
            $detail->debit = null;
            $detail->credit = null;
            $detail->balance = null;
            $detail->description = $detail->description . ' (Canceled)';
            $detail->save(false);

            // Recalculate all balances from the beginning
            $allDetails = PettyCashLedgerDetail::find()
                    ->where(['pc_ledger_master_id' => $masterLedger->id])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();

            $runningBalance = 0;

            foreach ($allDetails as $row) {
                // Skip canceled entries but keep previous balance for display
                if ($row->debit === null && $row->credit === null) {
                    $row->balance = $runningBalance; // Keep showing the current balance
                    $row->save(false);
                    continue;
                }

                $debit = $row->debit ?? 0;
                $credit = $row->credit ?? 0;

                // Debit increases balance, Credit decreases balance
                $runningBalance += ($debit - $credit);

                // Store the balance
                $row->balance = $runningBalance;
                $row->save(false);
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Ledger recalculation failed: ' . $e->getMessage(), __METHOD__);
            FlashHandler::err('Failed to recalculate ledger: ' . $e->getMessage());
            return false;
        }
    }

    /*     * ********************************************************** Pre-form - finance **************************************************** */

    public function actionFinanceApprovalPending() {
        $searchModel = new PettyCashRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingFinance');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 1,
                    'module' => 'finance'
        ]);
    }

    public function actionFinanceApprovalAll() {
        $searchModel = new PettyCashRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'allFinance');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 2,
                    'module' => 'finance'
        ]);
    }

    public function actionFinanceViewForm($id = null, $voucher_no = null) {
        if ($voucher_no === null) {
            $model = $this->findModel($id);
        } else {
            $model = $this->findModel(['voucher_no' => $voucher_no]);
        }

        $preForm = PettyCashRequestPre::findOne(['pc_request_master_id' => $model->id, 'deleted_by' => null]);
        $postForm = PettyCashRequestPost::find()->where(['pc_request_pre_id' => $preForm->id, 'deleted_by' => null, 'deleted_at' => null])->one();
        return $this->render('view', [
                    'model' => $model,
                    'preForm' => $preForm,
                    'postForm' => $postForm,
                    'module' => 'finance'
        ]);
    }

    public function actionFinanceVerifyPreForm($id) {
        $preForm = PettyCashRequestPre::findOne($id);
        if (!$preForm) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        $model = $this->findModel($preForm->pc_request_master_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Master Form not found.');
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $preForm->load(Yii::$app->request->post());
                $userId = Yii::$app->user->id;

                $preForm->responsed_at = new \yii\db\Expression('NOW()');
                $preForm->responsed_by = $userId;

                // Handle status and amount
                if ($preForm->status == PettyCashRequestMaster::STATUS_REJECTED) {
                    $preForm->amount_approved = 0.00;
                    $model->status = RefGeneralStatus::STATUS_FinanceRejected;
                } elseif ($preForm->status == PettyCashRequestMaster::STATUS_APPROVED) {
                    $model->status = RefGeneralStatus::STATUS_PendingSupportedDocument;
                }

                // Save both models
                if (!$preForm->save(false)) {
                    throw new \Exception('Failed to save form detail.');
                }

                $model->finance_id = Yii::$app->user->identity->id;
                if (!$model->save(false)) {
                    throw new \Exception('Failed to update master status.');
                }

                // Handle ledger only for approved cases
                if ($preForm->status == PettyCashRequestMaster::STATUS_APPROVED) {
                    $masterLedger = PettyCashLedgerMaster::findOne(['created_by' => $userId]);
                    if (!$masterLedger) {
                        throw new \Exception('No ledger found for current user.');
                    }

                    $this->recordLedgerTransaction($masterLedger, $preForm->amount_approved, $model);
                }

                $transaction->commit();
                FlashHandler::success('Petty Cash Request status updated successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['finance-approval-pending']);
        }
    }

    private function recordLedgerTransaction($ledgerMaster, $amount, $model) {
        $detail = new PettyCashLedgerDetail([
            'pc_ledger_master_id' => $ledgerMaster->id,
            'date' => new \yii\db\Expression('NOW()'),
            'voucher_no' => $ledgerMaster->generateVoucherNoCredit(),
            'debit' => null,
            'credit' => $amount,
        ]);

        $detail->balance = $ledgerMaster->amount - $amount;

        if (!$detail->save()) {
            throw new \Exception('Failed to save ledger detail.');
        }

        if (!$ledgerMaster->updateLedgerMasterAmount()) {
            throw new \Exception('Failed to update ledger master amount.');
        }

        $model->voucher_no = $detail->voucher_no;
        if (!$model->save(false)) {
            throw new \Exception('Failed to save voucher no.');
        }
    }

    public function actionViewReceipt($id, $module) {
        $postForm = PettyCashRequestPost::findOne($id);
        if (!$postForm) {
            throw new \yii\web\NotFoundHttpException('Receipt not found.');
        }

        $model = $this->findModel($postForm->pc_request_master_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Master Form not found.');
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = RefGeneralStatus::STATUS_WaitingForReceiptVerification;

                if (!$model->save()) {
                    throw new \Exception('Failed to update master status.');
                }

                $postForm->load(Yii::$app->request->post());
                $postForm->pc_request_master_id = $model->id;
                $postForm->status = 0;

                if (!$postForm->save()) {
                    throw new \Exception('Failed to update receipt detail.');
                }

                $transaction->commit();
                FlashHandler::success('Return receipt submitted successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['personal-pending']);
        }

        return $this->renderAjax('_formReceipt', [
                    'model' => $model,
                    'postForm' => $postForm,
                    'attachments' => $postForm->pettyCashRequestPostAttachments,
                    'module' => $module
        ]);
    }

    public function actionFinanceVerifyReceipt($id) {
        $postForm = PettyCashRequestPost::findOne($id);
        if (!$postForm) {
            throw new \yii\web\NotFoundHttpException('Receipt Form not found.');
        }

        $model = $this->findModel($postForm->pc_request_master_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Master Form not found.');
        }

        if (Yii::$app->request->isPost) {
            // DON'T commit transaction yet - just validate and prepare
            try {
                $postForm->load(Yii::$app->request->post());
                $postForm->responsed_at = new \yii\db\Expression('NOW()');
                $postForm->responsed_by = Yii::$app->user->identity->id;

                if ($postForm->status == PettyCashRequestMaster::STATUS_REJECTED) {
                    $model->status = RefGeneralStatus::STATUS_FinanceRejected;
                    $postForm->amount_approved = 0.00;

                    // For rejection, commit immediately (no second form needed)
                    $transaction = Yii::$app->db->beginTransaction();

                    if (!$postForm->save(false)) {
                        throw new \Exception('Failed to save receipt detail.');
                    }

                    if (!$model->save(false)) {
                        throw new \Exception('Failed to update master status.');
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Petty Cash Request rejected successfully.');

                    if (Yii::$app->request->isAjax) {
                        return $this->asJson([
                                    'success' => true,
                                    'redirect' => Yii::$app->urlManager->createUrl(['/office/petty-cash/finance-approval-pending'])
                        ]);
                    }
                    return $this->redirect(['finance-approval-pending']);
                } elseif ($postForm->status == PettyCashRequestMaster::STATUS_APPROVED) {
                    // For approval, store data in session (DON'T save to DB yet)
                    Yii::$app->session->set('pending_verification_' . $id, [
                        'postForm' => [
                            'responsed_at' => date('Y-m-d H:i:s'),
                            'responsed_by' => Yii::$app->user->identity->id,
                            'status' => $postForm->status,
                            'amount_approved' => $postForm->receipt_amount,
                        ],
                        'model' => [
                            'status' => RefGeneralStatus::STATUS_Completed,
                        ]
                    ]);

                    if (Yii::$app->request->isAjax) {
                        return $this->asJson([
                                    'success' => true,
                                    'openModal' => true,
                                    'masterId' => $model->id,
                                    'postFormId' => $id,
                                    'message' => 'Please complete the ledger update to finalize.'
                        ]);
                    }

                    return $this->redirect(['finance-confirm-receipt-completed', 'id' => $model->id, 'postFormId' => $id]);
                }
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());

                if (Yii::$app->request->isAjax) {
                    return $this->asJson([
                                'success' => false,
                                'message' => $e->getMessage()
                    ]);
                }

                return $this->redirect(['finance-approval-pending']);
            }
        }

        return $this->render('_formFinanceVerify', [
                    'postForm' => $postForm,
                    'model' => $model,
        ]);
    }

    public function actionFinanceConfirmReceiptCompleted($id) {
        $model = PettyCashRequestMaster::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        $postForm = PettyCashRequestPost::findOne(['pc_request_master_id' => $model->id, 'deleted_by' => null, 'deleted_at' => null]);
        $detailLedger = PettyCashLedgerDetail::findOne(['voucher_no' => $model->voucher_no]);

        if (!$detailLedger) {
            throw new \yii\web\NotFoundHttpException('Ledger detail not found.');
        }

        // Get postFormId from request
        $postFormId = Yii::$app->request->get('postFormId', $postForm->id);

        // Check if there's pending verification data
        $pendingData = Yii::$app->session->get('pending_verification_' . $postFormId);

        $detailLedger->credit = $postForm->receipt_amount;

        if (Yii::$app->request->isPost) {
            // Start transaction for BOTH forms together
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Check if already completed
                if ($model->status == RefGeneralStatus::STATUS_Completed && $detailLedger->credit > 0 && !$pendingData) {
                    if (Yii::$app->request->isAjax) {
                        return $this->asJson([
                                    'success' => true,
                                    'message' => 'This request has already been completed.',
                                    'redirect' => Yii::$app->urlManager->createUrl(['/office/petty-cash/finance-approval-pending'])
                        ]);
                    }
                    Yii::$app->session->setFlash('info', 'This request has already been completed.');
                    return $this->redirect(['finance-approval-pending']);
                }

                // FIRST: Save the verification form data (from session)
                if ($pendingData) {
                    $postForm->responsed_at = $pendingData['postForm']['responsed_at'];
                    $postForm->responsed_by = $pendingData['postForm']['responsed_by'];
                    $postForm->status = $pendingData['postForm']['status'];
                    $postForm->amount_approved = $pendingData['postForm']['amount_approved'];

                    if (!$postForm->save(false)) {
                        throw new \Exception('Failed to save receipt detail.');
                    }

                    $model->status = $pendingData['model']['status'];
                    if (!$model->save(false)) {
                        throw new \Exception('Failed to update master status.');
                    }
                }

                // SECOND: Update ledger
                if (!$detailLedger->load(Yii::$app->request->post())) {
                    throw new \Exception('Failed to load ledger form data.');
                }

                $masterLedger = PettyCashLedgerMaster::findOne([
                    'id' => $detailLedger->pc_ledger_master_id,
                    'created_by' => Yii::$app->user->identity->id,
                ]);
                if (!$masterLedger) {
                    throw new \Exception('No ledger found.');
                }

                $detailLedger->date = new \yii\db\Expression('NOW()');
                $detailLedger->credit = $postForm->receipt_amount;

                if (!$detailLedger->save()) {
                    $errors = json_encode($detailLedger->errors);
                    throw new \Exception('Failed to save the ledger detail: ' . $errors);
                }

                if (!$masterLedger->updateLedgerMasterAmount()) {
                    throw new \Exception('Failed to update ledger master amount.');
                }

                // COMMIT: Both forms saved successfully
                $transaction->commit();

                // Clear pending data from session
                Yii::$app->session->remove('pending_verification_' . $postFormId);

                Yii::$app->session->setFlash('success', 'Verification and ledger update completed successfully!');

                if (Yii::$app->request->isAjax) {
                    return $this->asJson([
                                'success' => true,
                                'message' => 'Completed successfully!',
                                'redirect' => Yii::$app->urlManager->createUrl(['/office/petty-cash/finance-approval-pending'])
                    ]);
                }

                return $this->redirect(['finance-approval-pending']);
            } catch (\Throwable $e) {
                // ROLLBACK: If ledger fails, verification is also rolled back
                $transaction->rollBack();

                // Keep pending data in session for retry
                Yii::$app->session->setFlash('error', 'Failed to complete: ' . $e->getMessage());

                if (Yii::$app->request->isAjax) {
                    return $this->asJson([
                                'success' => false,
                                'message' => $e->getMessage()
                    ]);
                }

                // Don't redirect - let user retry
                return $this->renderAjax('_formLedgerCredit', [
                            'detailLedger' => $detailLedger,
                            'model' => $model,
                ]);
            }
        }

        // GET request: render modal content
        return $this->renderAjax('_formLedgerCredit', [
                    'detailLedger' => $detailLedger,
                    'model' => $model,
        ]);
    }

    /*     * ********************************************************** Replenishment **************************************************** */

    public function actionFinanceLedger() {
        $searchModel = new PettyCashLedgerMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'finance');

        return $this->render('indexLedger', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 4,
                    'module' => 'finance'
        ]);
    }

    public function actionFinanceReplenishment() {
        $searchModel = new PettyCashReplenishmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'finance');

        return $this->render('indexReplenishment', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 3,
                    'module' => 'finance'
        ]);
    }

    public function actionRequestReplenishment() {
        $model = new PettyCashReplenishment();

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->load(Yii::$app->request->post());

                $model->ref_code = $model->generateRefCode();
                $model->status = RefGeneralStatus::STATUS_GetDirectorApproval;
                if (!$model->save()) {
                    throw new \Exception('Failed to submit form.');
                }

                $masterLedger = new PettyCashLedgerMaster();
                if (!$masterLedger->createLedger()) {
                    throw new \Exception('Failed to create your ledger.');
                }

                $transaction->commit();
                FlashHandler::success('Request has been submitted successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['finance-replenishment']);
        }

        return $this->renderAjax('_formReplenishment', [
                    'model' => $model
        ]);
    }

    public function actionFinanceViewReplenishmentRequest($id = null, $voucher_no = null) {
        if($voucher_no === null){
            $model = PettyCashReplenishment::findOne($id);
        }else{
            $model = PettyCashReplenishment::findOne(['voucher_no' => $voucher_no]);
        }

        return $this->render('viewReplenishmentRequest', [
                    'model' => $model,
                    'module' => 'finance'
        ]);
    }

    public function actionUpdateReplenishmentRequest($id) {
        $model = PettyCashReplenishment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Form not found.');
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model->load(Yii::$app->request->post());
                if (!$model->save(false)) {
                    throw new \Exception('Failed to update form detail.');
                }

                $transaction->commit();
                FlashHandler::success('Record updated successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->render('viewReplenishmentRequest', [
                        'model' => $model,
                        'module' => 'finance'
            ]);
        }

        return $this->renderAjax('_formReplenishment', [
                    'model' => $model,
        ]);
    }

    public function actionCancelReplenishmentRequest($id) {
        $model = PettyCashReplenishment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Form not found.');
        }

        $model->status = RefGeneralStatus::STATUS_ClaimantCancelClaim;
        $model->deleted_at = new \yii\db\Expression('NOW()');
        $model->deleted_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            FlashHandler::err('Failed to cancel the request.');
        } else {
            FlashHandler::success('The request has been canceled successfully.');
        }

        return $this->redirect(['finance-replenishment']);
    }

    public function actionAjaxViewLedgerDetailList($id) {
        $model = PettyCashReplenishment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Replenishment record not found.');
        }

        $masterLedger = PettyCashLedgerMaster::find()
                ->where(['created_by' => $model->created_by])
                ->one();

        if (!$masterLedger) {
            throw new \yii\web\NotFoundHttpException('Ledger master not found.');
        }

        // Calculate 1 month range before created_at
        $endDate = date('Y-m-d', strtotime($model->created_at));
        $startDate = date('Y-m-d', strtotime('-1 month', strtotime($model->created_at)));

        // Find ledger details within that 1-month period
        $detailLedger = PettyCashLedgerDetail::find()
                ->where(['between', 'date', $startDate, $endDate])
                ->andWhere(['pc_ledger_master_id' => $masterLedger->id])
                ->orderBy(['date' => SORT_ASC])
                ->all();

        return $this->renderAjax('_ledgerDetailList', [
                    'masterLedger' => $masterLedger,
                    'detailLedger' => $detailLedger,
                    'model' => $model,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
        ]);
    }

    public function actionDirectorApprovalPending() {
        $searchModel = new PettyCashReplenishmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingDirector');

        return $this->render('indexReplenishment', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 1,
                    'module' => 'director'
        ]);
    }

    public function actionDirectorApprovalAll() {
        $searchModel = new PettyCashReplenishmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexReplenishment', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'key' => 2,
                    'module' => 'director'
        ]);
    }

    public function actionDirectorViewReplenishmentRequest($id) {
        $model = PettyCashReplenishment::findOne($id);

        return $this->render('viewReplenishmentRequest', [
                    'model' => $model,
                    'module' => 'director'
        ]);
    }

    public function actionDirectorApprovalReplenishmentRequest($id) {
        $model = PettyCashReplenishment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->load(Yii::$app->request->post());

                if ($model->director_responsed_status == PettyCashRequestMaster::STATUS_REJECTED) {
                    $model->status = RefGeneralStatus::STATUS_DirectorRejected;
                } elseif ($model->director_responsed_status == PettyCashRequestMaster::STATUS_APPROVED) {
                    $model->status = RefGeneralStatus::STATUS_WaitingForCashRelease;
                }

                $model->director_responsed_at = new \yii\db\Expression('NOW()');
                $model->director_responsed_by = Yii::$app->user->identity->id;
                if (!$model->save(false)) {
                    throw new \Exception('Failed to save the detail.');
                }

                $transaction->commit();
                FlashHandler::success('Petty Cash Replenishment Request status updated successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['director-approval-pending']);
        }
    }

    public function actionFinanceConfirmReplenishmentCompleted($id) {
        $model = PettyCashReplenishment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Request Form not found.');
        }

        $detailLedger = new PettyCashLedgerDetail();
        $detailLedger->debit = $model->amount_approved;

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Load POST data into detailLedger
                if (!$detailLedger->load(Yii::$app->request->post())) {
                    throw new \Exception('Failed to load form data.');
                }

                // Get or create master ledger
                $masterLedger = PettyCashLedgerMaster::findOne(['created_by' => Yii::$app->user->identity->id]);
                if (!$masterLedger) {
                    $masterLedger = new PettyCashLedgerMaster();
                    if (!$masterLedger->createLedger()) {
                        throw new \Exception('Failed to create your ledger.');
                    }
                    // Refresh the model to get the saved data with ID
                    $masterLedger->refresh();
                }

                // Update master ledger balance
                $masterLedger->amount = ($masterLedger->amount + $detailLedger->debit);
                if (!$masterLedger->save(false)) {
                    throw new \Exception('Failed to update the ledger master.');
                }

                // Populate ledger detail with master ledger info
                $detailLedger->pc_ledger_master_id = $masterLedger->id;
                $detailLedger->date = new \yii\db\Expression('NOW()');
                $detailLedger->voucher_no = $masterLedger->generateVoucherNoDebit();
                $detailLedger->credit = null;
                $detailLedger->balance = $masterLedger->amount;

                // Save ledger detail
                if (!$detailLedger->save()) {
                    $errors = json_encode($detailLedger->errors);
                    throw new \Exception('Failed to save the ledger detail: ' . $errors);
                }

                // Update replenishment model
                $model->voucher_no = $detailLedger->voucher_no;
                $model->status = RefGeneralStatus::STATUS_Completed;
                $model->finance_responsed_at = new \yii\db\Expression('NOW()');
                $model->finance_responsed_by = Yii::$app->user->identity->id;
                $model->finance_responsed_status = PettyCashRequestMaster::STATUS_APPROVED;

                if (!$model->save(false)) {
                    throw new \Exception('Failed to save the replenishment detail.');
                }
                
                $transaction->commit();
                FlashHandler::success('Replenishment Completed!');
                return $this->redirect(['finance-replenishment']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
                return $this->redirect(['finance-replenishment']);
            }
        }

        return $this->renderAjax('_formLedgerDebit', [
                    'detailLedger' => $detailLedger,
                    'model' => $model,
        ]);
    }

    public function actionFinanceViewLedger($id) {
        $master = PettyCashLedgerMaster::findOne($id);
        $searchModel = new PettyCashLedgerDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pendingFinance');

        return $this->render('viewLedger', [
                    'master' => $master,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'module' => 'finance'
        ]);
    }

    public function actionAjaxExportLedgerCsv($id) {
        $masterLedger = PettyCashLedgerMaster::findOne($id);
        if (!$masterLedger) {
            throw new \yii\web\NotFoundHttpException('Ledger not found.');
        }

        if (Yii::$app->request->isPost) {
            $startDate = \DateTime::createFromFormat('d/m/Y', Yii::$app->request->post('startDate'));
            $endDate = \DateTime::createFromFormat('d/m/Y', Yii::$app->request->post('endDate'));

            if ($startDate && $endDate) {
                $masterLedger->startDate = $startDate->format('Y-m-d');
                $masterLedger->endDate = $endDate->format('Y-m-d');
            }

            // Get ledger details within date range
            $detailLedger = PettyCashLedgerDetail::find()
                    ->where(['pc_ledger_master_id' => $masterLedger->id])
                    ->andWhere(['between', 'date', $masterLedger->startDate, $masterLedger->endDate])
                    ->orderBy(['date' => SORT_ASC])
                    ->all();

            // Totals
            $totalDebit = PettyCashLedgerDetail::find()
                    ->where(['pc_ledger_master_id' => $masterLedger->id])
                    ->andWhere(['between', 'date', $masterLedger->startDate, $masterLedger->endDate])
                    ->sum('debit');

            $totalCredit = PettyCashLedgerDetail::find()
                    ->where(['pc_ledger_master_id' => $masterLedger->id])
                    ->andWhere(['between', 'date', $masterLedger->startDate, $masterLedger->endDate])
                    ->sum('credit');

            $balance = $totalDebit - $totalCredit;

            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="Ledger_Report.xls"');

            return $this->renderPartial('_ledgerCSV', [
                        'detailLedger' => $detailLedger,
                        'masterLedger' => $masterLedger
            ]);
        }

        return $this->renderAjax('_formLedgerCSV', [
                    'model' => $masterLedger,
        ]);
    }

    /**
     * Deletes an existing PettyCashRequestMaster model.
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
     * Finds the PettyCashRequestMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PettyCashRequestMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PettyCashRequestMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
