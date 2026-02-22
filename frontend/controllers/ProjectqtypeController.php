<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectquotation\ProjectQTypes;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use frontend\models\projectquotation\ProjectQRevisions;
use frontend\models\common\RefCurrencies;
use frontend\models\projectquotation\ProjectQRevisionsTemplate;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;
use frontend\models\ProjectProduction\ProjectProductionMaster;

/**
 * ProjectqtypeController implements the CRUD actions for ProjectQTypes model.
 */
class ProjectqtypeController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectQTypes models.
     * @return mixed
     */
//    public function actionIndex() {
//        $searchModel = new ProjectQTypeSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Displays a single ProjectQTypes model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewProjectQType($id) {
        $currencyList = RefCurrencies::getActiveDropdownlist_by_id();
        $revisionTemplateList = \yii\helpers\ArrayHelper::map(ProjectQRevisionsTemplate::find()->where(['is_active' => 1])->all(), 'id', 'revision_description');

        $masterId = ProjectProductionMaster::find()->select('id')->where(['revision_id' => ProjectQRevisions::find()->select('id')->where(['project_q_type_id' => $id])])->scalar();

        $model = $this->findModel($id);
        $revisions = $model->projectQRevisions;
        $emailHistory = [];

        if ($revisions) {
            foreach ($revisions as $revision) {
                $quotationPdfMasters = \frontend\models\projectquotation\QuotationPdfMasters::findAll(['revision_id' => $revision->id]);

                foreach ($quotationPdfMasters as $quotationPdfMaster) {
                    $quotationEmails = \frontend\models\projectquotation\QuotationEmails::findAll(['quotation_id' => $quotationPdfMaster->id]);

                    $emailsWithClients = [];

                    foreach ($quotationEmails as $email) {
                        if ($email->client_id) {
                            $projectqclient = \frontend\models\projectquotation\ProjectQClients::findOne($email->client_id);

                            if ($projectqclient) {
                                if ($projectqclient->client_id) {
                                    $client = \frontend\models\client\Clients::findOne($projectqclient->client_id);
                                    if ($client) {
                                        $clientName = $client->company_name;
                                    }
                                }
                            }
                        }

                        $emailsWithClients[] = [
                            'email' => $email,
                            'client_name' => $clientName
                        ];
                    }

                    if (!empty($emailsWithClients)) {
                        $emailHistory[] = [
                            'revision_description' => $revision->revision_description,
                            'emails_with_clients' => $emailsWithClients
                        ];
                    }
                }
            }
        }

        return $this->render('viewProjectQType', [
                    'model' => $model,
                    'currencyList' => $currencyList,
                    'revisionTemplateList' => $revisionTemplateList,
                    'masterId' => $masterId,
                    'emailHistory' => $emailHistory
        ]);
    }

    public function actionUploadPo($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($model->scannedFile) {
                // Set your upload path
                $uploadPath = Yii::getAlias('@frontend/uploads/po/');

                // Make sure directory exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Create unique filename
                $fileName = 'PO_' . $model->id . '.' . $model->scannedFile->extension;
                $filePath = $uploadPath . $fileName;

                // Save the uploaded file
                if ($model->scannedFile->saveAs($filePath)) {
                    $model->po_file = $fileName;
                    $model->save(false);
                }
            }
            FlashHandler::success("Saved!");
            return $this->redirect(['view-project-q-type', 'id' => $model->id]);
        }

        return $this->renderAjax('uploadPO', [
                    'model' => $model,
        ]);
    }

    public function actionReadPoPdf($id) {
        $model = $this->findModel($id);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Failed!");
        }

        $fileName = 'PO_' . $model->id;
        $pdfPath = Yii::getAlias('@frontend/uploads/po/' . $fileName . '.pdf');

        if (!file_exists($pdfPath)) {
            throw new \yii\web\NotFoundHttpException("The requested PDF does not exist.");
        }

        return Yii::$app->response->sendFile($pdfPath, $fileName . '.pdf', [
                    'inline' => true,
        ]);
    }

    public function actionUpdate() {
        $id = Yii::$app->request->post('ProjectQTypes')['id'];
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("Updated.");
        }
        return $this->redirect(['view-project-q-type', 'id' => $id]);
    }

    protected function findModel($id) {
        if (($model = ProjectQTypes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionNewRevision() {
        $model = new ProjectQRevisions();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("New revision created");
            }
        }
        return $this->redirect(['view-project-q-type', 'id' => $model->project_q_type_id]);
    }

    public function actionNewRevisionFromTemplate() {
        $model = new ProjectQRevisions();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->createFromTemplate()) {
                FlashHandler::success("New revision created");
            }
        }
        return $this->redirect(['view-project-q-type', 'id' => $model->project_q_type_id]);
    }

    public function actionCloneRevisionSameProjecttype() {
        $req = Yii::$app->request;
        $motherRevisionId = $req->post('motherRevisionId');
        $cloneRevisionNewName = $req->post('cloneRevisionNewName');
        $cloneRevisionNewRemark = $req->post('cloneRevisionNewRemark');
        $newRevision = new ProjectQRevisions();
        if ($newRevision->cloneRevisionsFromMother($motherRevisionId, $cloneRevisionNewName, $cloneRevisionNewRemark)) {
            FlashHandler::success("Revision cloned");
        }
//        $revision = $newPanel->revision;
//        $revision->updateRevisionAmount();
        return $this->redirect(['view-project-q-type', 'id' => $newRevision->project_q_type_id]);
    }

    public function actionLoadRevisionNumberAjax($projQTypeId) {
        $number = \frontend\models\projectquotation\ProjectQRevisions::find()->where(['project_q_type_id' => $projQTypeId])->sum(1);
        if (!$number) {
            $number = 0;
        }
        return json_encode(["number" => $number]);
    }

    /**
     * Set active Revision
     * @return type
     */
    public function actionSetActiveRevisionAjax() {
        $model = $this->findModel(Yii::$app->request->post('projectQTypeId'));
        $revisionId = Yii::$app->request->post('revisionId');

        $model->active_revision_id = $revisionId == 0 ? "" : $revisionId;
        if ($model->update()) {
            $activeRevision = ProjectQRevisions::findOne(Yii::$app->request->post('revisionId'));
            if ($activeRevision) {
                return json_encode(["success" => true, "total" => $activeRevision->currency->currency_sign . " " . MyFormatter::asDecimal2($activeRevision->amount)]);
            } else {
                return json_encode(["success" => true, "total" => "-"]);
            }
        } else {
            return json_encode(["success" => false]);
        }
    }

    /**
     * Set active Client
     * @return type
     */
    public function actionSetActiveClientAjax() {
        $model = $this->findModel(Yii::$app->request->post('projectQTypeId'));
        $clientId = Yii::$app->request->post('clientId');

        $model->active_client_id = $clientId == 0 ? "" : $clientId;
        if ($model->update()) {
            return json_encode(["success" => true]);
        } else {
            return json_encode(["success" => false]);
        }
    }

    public function actionAjaxFormConfirmOrder($typeId) {
        $projectQType = ProjectQTypes::findOne($typeId);
        if ($projectQType->load(Yii::$app->request->post())) {

            if ($projectQType->confirmOrder()) {
                FlashHandler::success("Order confirmed.");
            } else {
                FlashHandler::err("Fail to confirm order. Please select active client and revision.");
            }
            return $this->redirect(['view-project-q-type', 'id' => $typeId]);
        }
        return $this->renderAjax('_ajaxFormConfirmOrder', [
                    'projectQType' => $projectQType,
        ]);
    }

    public function actionAjaxFormReverseConfirmOrder($typeId) {
        $projectQType = ProjectQTypes::findOne($typeId);
        if ($projectQType->load(Yii::$app->request->post())) {
            $projectQType->po_file = null;
            $projectQType->update();
            FlashHandler::success("Order confirmation reversed.");
            return $this->redirect(['view-project-q-type', 'id' => $typeId]);
        }
        return $this->renderAjax('_ajaxFormReverseConfirmOrder', [
                    'projectQType' => $projectQType,
        ]);
    }

    public function actionSaveAttachments($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $files = \yii\web\UploadedFile::getInstances($model, 'attachments');

            $uploadDir = Yii::getAlias('@app') . '/uploads/quotation-attachments/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            foreach ($files as $file) {
                if (!$file->saveAs($uploadDir . $file->name)) {
                    FlashHandler::err('Failed to upload attachments. Please try again');
                    return $this->redirect(['view-project-q-type', 'id' => $model->id]);
                }
                $attachments = \frontend\models\projectquotation\ProjectQTypesAttachments::findOne([
                    'proj_q_type_id' => $id,
                    'filename' => $file->name
                ]);

                if (!$attachments) {
                    $attachments = new \frontend\models\projectquotation\ProjectQTypesAttachments();
                    $attachments->proj_q_type_id = $id;
                    $attachments->save();
                }

                $attachments->deleted_at = null;
                $attachments->deleted_by = null;
                $attachments->filename = $file->name;
                $attachments->save();
            }
        }

        FlashHandler::success('Attachments uploaded successfully');
        return $this->redirect(['view-project-q-type', 'id' => $model->id]);
    }

    public function actionAjaxDeleteAttachment($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $attachment = \frontend\models\projectquotation\ProjectQTypesAttachments::findOne(['id' => $id]);

        if (!$attachment) {
            return ['success' => false, 'error' => 'Attachment not found'];
        }

        $uploadPath = Yii::getAlias('@frontend/uploads/quotation-attachments/');
        $filePath = $uploadPath . $attachment->filename;

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
        $pdfDir = Yii::getAlias('@frontend/uploads/quotation-attachments/');

        $pdfPath = $pdfDir . $file_name;

        if (!file_exists($pdfPath)) {
            throw new \yii\web\NotFoundHttpException("The requested PDF file was not found.");
        }

        Yii::$app->response->sendFile($pdfPath, $file_name . '.pdf', [
            'inline' => true,
        ])->send();
    }
}
