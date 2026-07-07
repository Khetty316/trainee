<?php

namespace frontend\controllers;

use Yii;
use frontend\models\client\Clients;
use frontend\models\client\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\myTools\FlashHandler;
use yii\helpers\VarDumper;
use frontend\models\common\RefCompanyGroupList;
use frontend\models\client\ClientDebt;
use frontend\models\client\ClientDebtSeXarch;
use common\models\myTools\Mydebug;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\Url;
use frontend\models\client\ClientReminderLetterTemplate;
use frontend\models\client\ClientReminderLetterTemplateSearch;
use frontend\models\client\ClientReminderLetterEmails;
use yii\web\UploadedFile;
use Mpdf\Mpdf;
use yii\helpers\FileHelper;
use frontend\models\client\ClientReminderLetterEmailAttachment;
use frontend\models\client\ClientReminderLetterEmailsSearch;
use frontend\models\client\ClientGeneralDebt;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;
use frontend\models\client\ClientReminderLetterEmailDetail;
use frontend\models\client\ClientDebtSearch;

//debug
/**
 * ClientController implements the CRUD actions for Clients model.
 */
class ClientController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['user-manual', 'index', 'view-client', 'create-client', 'update-client', 'delete-client', 'ajax-add-contact', 'remove-temp-file-ajax'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Client_Module_Director, AuthItem::ROLE_Client_Module_Projcoor, AuthItem::ROLE_Client_Module_Procurement, AuthItem::ROLE_Client_Module_Finance],
                    ],
                    [
                        'actions' => ['get-client-emails', 'add-by-template-clients', 'process-client-data', 'confirm-submit', 'check-client-data', 'save-exist-client', 'export-not-found-clients',
                            'index-reminder-letter-template', 'create-reminder-letter-template', 'view-client-reminder-letter-template', 'update-client-reminder-letter-template', 'create-reminder-letter-emails',
                            'view-client-reminder-letter-emails', 'get-reminder-letter-content', 'delete-reminder-letter-emails', 'update-reminder-letter-emails', 'generate-reminder-letter-pdf',
                            'remove-temp-file', 'index-debt-reminder-letter-template', 'index-general-client-debt', 'index-general-debt-reminder-letter-email-log', 'update-debt', 'delete-debt', 'update-client-balances',
                            'create-new-entry', 'get-client-code', 'client-autocomplete', 'confirm-reminder-letter-emails',],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Client_Module_Director, AuthItem::ROLE_Client_Module_Finance]
                    ],
                ],
            ],
        ];
    }

    public function actionUserManual() {
        $this->layout = false;
        $fileName = "T6B1-Client Module-00.pdf";
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    /**
     * Lists all Clients models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexClient', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    /**
     * Displays a single Clients model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewClient($id) {
        $model = $this->findModel($id);

        $contacts = \frontend\models\client\ClientContact::find()
                ->where(['client_id' => $id])
                ->indexBy('id')
                ->all();

        $searchModel = new ClientDebtSearch();

        $emailLogSearchModel = new ClientReminderLetterEmailsSearch();
        $emailLogDataProvider = $emailLogSearchModel->search(Yii::$app->request->queryParams);
        $emailLogDataProvider->query->andWhere(['client_id' => $model->id]);
        $emailLogDataProvider->query->orderBy(['sent_at' => SORT_DESC]);
        $emailLogDataProvider->pagination = ['pageSize' => 20,];

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $params = Yii::$app->request->queryParams;

        if (isset($params['resetDebt'])) {
            unset($params['ClientDebtSearch']);
        }

        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['client_id' => $model->id]);
        $dataProvider->pagination = ['pageSize' => 20,];

        return $this->render('viewClient', [
                    'model' => $model,
                    'contacts' => $contacts,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'emailLogSearchModel' => $emailLogSearchModel,
                    'emailLogDataProvider' => $emailLogDataProvider,
        ]);
    }

    /**
     * Creates a new Clients model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreateClient() {
//        $model = new \frontend\models\client\Clients();
//        $contacts = [new \frontend\models\client\ClientContact()]; // start with one row
//
//        if ($model->load(Yii::$app->request->post())) {
//            // dynamically create multiple contact models based on POST
//            $contacts = \frontend\models\ModelHelper::createMultiple(
//                    \frontend\models\client\ClientContact::class
//            );
//
//            $email_model = new \frontend\models\projectquotation\QuotationEmails();
//            $validationErrors = [];
//            foreach ($contacts as $index => $contact) {
//                if (!empty($contact->email_address) && !$email_model->validateEmailAddress($contact->email_address)) {
//                    $contactName = !empty($contact->name) ? $contact->name : "Contact " . ($index + 1);
//                    $validationErrors[] = "Invalid email address for {$contactName}: {$contact->email_address}";
//                }
//            }
//
//            // If there are email validation errors, show them and return
//            if (!empty($validationErrors)) {
//                foreach ($validationErrors as $error) {
//                    FlashHandler::err($error);
//                }
//
//                return $this->render('createClient', [
//                            'model' => $model,
//                            'contactModels' => $contacts,
//                            'isUpdate' => false
//                ]);
//            }
//
//            foreach ($contacts as $contact) {
//                $contact->client_id = $model->id ?: 0; // or temporary dummy
//            }
//
//            $transaction = Yii::$app->db->beginTransaction();
//            try {
//                if ($model->processAndSave()) {
//                    $apiKey = "ad19915da90114ff03712278f513e26b";
//                    foreach ($contacts as $contact) {
//                        $contact->client_id = $model->id; // Update with actual client_id
//                        if (!$contact->save(false)) {
//                            throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                        }
//                    }
//                    $transaction->commit();
//                    FlashHandler::success("Client and contacts created successfully.");
//                    return $this->redirect(['view-client', 'id' => $model->id]);
//                }
//            } catch (\Exception $e) {
//                $transaction->rollBack();
//                FlashHandler::err($e->getMessage());
//                return $this->render('createClient', [
//                            'model' => $model,
//                            'contactModels' => $contacts,
//                            'isUpdate' => false
//                ]);
//            }
//        }
//
//        return $this->render('createClient', [
//                    'model' => $model,
//                    'contactModels' => $contacts ?: [new \frontend\models\client\ClientContact()],
//                    'isUpdate' => false
//        ]);
//    }

    public function actionCreateClient() {
        $model = new \frontend\models\client\Clients();
        $contacts = [new \frontend\models\client\ClientContact()];

        if ($model->load(Yii::$app->request->post())) {
            $contacts = \frontend\models\ModelHelper::createMultiple(
                    \frontend\models\client\ClientContact::class
            );
            \yii\base\Model::loadMultiple($contacts, Yii::$app->request->post());

            $email_model = new \frontend\models\projectquotation\QuotationEmails();
            $validationErrors = [];

            foreach ($contacts as $index => $contact) {
                if (!empty($contact->email_address) && !$email_model->validateEmailAddress($contact->email_address)) {
                    $contactName = !empty($contact->name) ? $contact->name : "Contact " . ($index + 1);
                    $validationErrors[] = "Invalid email address for {$contactName}: {$contact->email_address}";
                }
            }

            if (!empty($validationErrors)) {
                foreach ($validationErrors as $error) {
                    FlashHandler::err($error);
                }
                return $this->render('createClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => false]);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->processAndSave()) {
                    foreach ($contacts as $contact) {
                        $contact->client_id = $model->id;
                        if (!$contact->save(false)) {
                            throw new \Exception("Failed throwsaving contact: " . json_encode($contact->errors));
                        }
                    }
                    $transaction->commit();
                    FlashHandler::success("Client and contacts created successfully.");
                    return $this->redirect(['view-client', 'id' => $model->id]);
                } else {
                    throw new \Exception("Failed saving client: " . json_encode($model->errors));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
                return $this->render('createClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => false]);
            }
        }
        return $this->render('createClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => false]);
    }

    /**
     * Updates an existing Clients model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    //updated by khetty, 15/11/2025, 17/4/2026 - got bug validation failed!
//    public function actionUpdateClient($id) {
//        $model = $this->findModel($id);
//        $existingContacts = \frontend\models\client\ClientContact::find()
//                ->where(['client_id' => $id])
//                ->indexBy('id')
//                ->all();
//
//        if ($model->load(Yii::$app->request->post())) {
//
//            $oldIDs = array_keys($existingContacts);
//            $contacts = \frontend\models\ModelHelper::createMultiple(
//                    \frontend\models\client\ClientContact::class, $existingContacts
//            );
//            $email_model = new \frontend\models\projectquotation\QuotationEmails();
//            $validationErrors = [];
//            foreach ($contacts as $index => $contact) {
//                if (!empty($contact->email_address) && !$email_model->validateEmailAddress($contact->email_address)) {
//                    $validationErrors[] = "Invalid email address for contact " . ($contact->name ?: ($index + 1)) . ": " . $contact->email_address;
//                }
//            }
//            if (!empty($validationErrors)) {
//                foreach ($validationErrors as $error) {
//                    FlashHandler::err($error);
//                }
//                return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
//            }
//
//            foreach ($contacts as $contact) {
//                $contact->client_id = $model->id;
//                if (!$contact->save(false)) {
//                    throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                }
//            }
//            $postedIDs = array_filter(\yii\helpers\ArrayHelper::getColumn($contacts, 'id'));
//            $deletedIDs = array_diff($oldIDs, $postedIDs);
//            $valid = $model->validate() && \yii\base\Model::validateMultiple($contacts);
//
//            if ($valid) {
//                $transaction = Yii::$app->db->beginTransaction();
//                try {
//                    if ($model->processAndSave()) {
//                        if (!empty($deletedIDs)) {
//                            \frontend\models\client\ClientContact::deleteAll(['id' => $deletedIDs]);
//                        }
//                        foreach ($contacts as $contact) {
//                            $contact->client_id = $model->id;
//                            if (!$contact->save(false)) {
//                                throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                            }
//                        }
//                        $transaction->commit();
//                        FlashHandler::success("Client and contacts updated successfully.");
//                        return $this->redirect(['view-client', 'id' => $model->id]);
//                    }
//                } catch (\Exception $e) {
//                    $transaction->rollBack();
//                    FlashHandler::err($e->getMessage());
//                    return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
//                }
//            } else {
//                FlashHandler::err("Validation failed.");
//            }
//        }
//        $contacts = $existingContacts ?: [new \frontend\models\client\ClientContact()];
//        return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
//    }

    public function actionUpdateClient($id) {
        $model = $this->findModel($id);
        $existingContacts = \frontend\models\client\ClientContact::find()
                ->where(['client_id' => $id])
                ->indexBy('id')
                ->all();

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = array_keys($existingContacts);
            $contacts = \frontend\models\ModelHelper::createMultiple(
                    \frontend\models\client\ClientContact::class, $existingContacts
            );

            $email_model = new \frontend\models\projectquotation\QuotationEmails();
            $validationErrors = [];
            foreach ($contacts as $index => $contact) {
                if (!empty($contact->email_address) && !$email_model->validateEmailAddress($contact->email_address)) {
                    $validationErrors[] = "Invalid email address for contact " . ($contact->name ?: ($index + 1)) . ": " . $contact->email_address;
                }
            }

            if (!empty($validationErrors)) {
                foreach ($validationErrors as $error) {
                    FlashHandler::err($error);
                }
                return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
            }

            foreach ($contacts as $contact) {
                $contact->client_id = $model->id;
            }

            // Calculate deleted IDs
            $postedIDs = array_filter(\yii\helpers\ArrayHelper::getColumn($contacts, 'id'));
            $deletedIDs = array_diff($oldIDs, $postedIDs);

            // VALIDATE FIRST (before any saves)
            $valid = $model->validate() && \yii\base\Model::validateMultiple($contacts);

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->processAndSave()) {
                        // Delete removed contacts
                        if (!empty($deletedIDs)) {
                            \frontend\models\client\ClientContact::deleteAll(['id' => $deletedIDs]);
                        }

                        // Save all contacts (ONLY ONCE, inside transaction)
                        foreach ($contacts as $contact) {
                            if (!$contact->save(false)) {
                                throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
                            }
                        }

                        $transaction->commit();
                        FlashHandler::success("Client and contacts updated successfully.");
                        return $this->redirect(['view-client', 'id' => $model->id]);
                    } else {
                        throw new \Exception("Failed to save client: " . json_encode($model->errors));
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    FlashHandler::err($e->getMessage());
                    return $this->render('updateClient', [
                                'model' => $model,
                                'contactModels' => $contacts,
                                'isUpdate' => true
                    ]);
                }
            } else {
                // Add specific error messages
                if ($model->hasErrors()) {
                    foreach ($model->getErrors() as $attribute => $errors) {
                        FlashHandler::err("Client {$attribute}: " . implode(', ', $errors));
                    }
                }
                foreach ($contacts as $index => $contact) {
                    if ($contact->hasErrors()) {
                        foreach ($contact->getErrors() as $attribute => $errors) {
                            FlashHandler::err("Contact " . ($index + 1) . " {$attribute}: " . implode(', ', $errors));
                        }
                    }
                }
                FlashHandler::err("Validation failed.");
            }
        }

        $contacts = $existingContacts ?: [new \frontend\models\client\ClientContact()];
        return $this->render('updateClient', [
                    'model' => $model,
                    'contactModels' => $contacts,
                    'isUpdate' => true
        ]);
    }

//    public function actionUpdateClient($id) {
//        $model = $this->findModel($id);
//
//        $existingContacts = \frontend\models\client\ClientContact::find()
//                ->where(['client_id' => $id])
//                ->indexBy('id')
//                ->all();
//
//        if ($model->load(Yii::$app->request->post())) {
//            $oldIDs = array_keys($existingContacts);
//
//            $contacts = \frontend\models\ModelHelper::createMultiple(
//                    \frontend\models\client\ClientContact::class, $existingContacts
//            );
//
////        \yii\base\Model::loadMultiple($contacts, Yii::$app->request->post());
//
//            foreach ($contacts as $contact) {
//                $contact->client_id = $model->id;
//                if (!$contact->save(false)) {
//                    throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                }
//            }
//            $postedIDs = array_filter(\yii\helpers\ArrayHelper::getColumn($contacts, 'id')); // Debug: check what is in the array
//            $deletedIDs = array_diff($oldIDs, $postedIDs); // Debug: Check what is in the deleted IDs
//
//            $valid = $model->validate() && \yii\base\Model::validateMultiple($contacts);
// 
//            if ($valid) {
//                $transaction = Yii::$app->db->beginTransaction();
//                try {
//                    if ($model->processAndSave()) {
//                        if (!empty($deletedIDs)) {
//                            \frontend\models\client\ClientContact::deleteAll(['id' => $deletedIDs]);
//                        }
//
////                    $apiKey = "ad19915da90114ff03712278f513e26b";
//                        foreach ($contacts as $contact) {
//                            $contact->client_id = $model->id;
//                            if (!empty($contact->email_address)) {
////                            $response = @file_get_contents($url);
////                            if ($response === false) {
////                                $error = error_get_last();
////                                Yii::error("Email validation API request failed: " . json_encode($error), __METHOD__);
////
////                                $transaction->rollBack();
////                                FlashHandler::err("Failed to reach the email validation API. Please try again later.");
////                                return $this->render('createClient', [
////                                    'model' => $model,
////                                    'contactModels' => $contacts,
////                                ]);
////                            }
////                            $data = json_decode($response, true);
//
//                                $ch = curl_init();
//
//                                // Set the URL that you want to GET by using the CURLOPT_URL option.
//                                curl_setopt($ch, CURLOPT_URL, "https://emailreputation.abstractapi.com/v1/?api_key=b0a1b5fe90f34ba1bf5064296db85cdb&email=" . urlencode($contact->email_address));
//
//                                // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
//                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//                                // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
//                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//
//                                // Execute the request.
//                                $response = curl_exec($ch);
//                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//
//                                // Close the cURL handle.
//                                curl_close($ch);
//
//                                if ($httpCode !== 200 || !$response) {
//                                    $transaction->rollBack();
//                                    FlashHandler::err("Failed to contact email validation API (HTTP $httpCode).");
//                                    return $this->render('createClient', [
//                                                'model' => $model,
//                                                'contactModels' => $contacts,
//                                    ]);
//                                }
//
//                                $data = json_decode($response, true);
//                                if (!is_array($data) || empty($data['email_deliverability'])) {
//                                    $transaction->rollBack();
//                                    FlashHandler::err("Invalid response from email validation API for {$contact->email_address}");
//                                    return $this->render('createClient', [
//                                                'model' => $model,
//                                                'contactModels' => $contacts,
//                                                'isUpdate' => false
//                                    ]);
//                                }
//
//                                $isFormatValid = $data['email_deliverability']['is_format_valid'] ?? null;
//                                $isSmtpValid = $data['email_deliverability']['is_smtp_valid'] ?? null;
//                                if (!$isFormatValid || !$isSmtpValid) {
////                            if (!is_array($data) || isset($data['error']) || !($data['smtp_check'] ?? false)) {
//                                    FlashHandler::err("The email is either invalid or does not exist in any server: {$contact->email_address}");
//                                    $contact->email_address = NULL;
//                                    if (!$contact->save(false)) {
//                                        throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                                    }
//                                    $transaction->commit();
//                                    return $this->render('updateClient', [
//                                                'model' => $model,
//                                                'contactModels' => $contacts,
//                                                'isUpdate' => true
//                                    ]);
//                                }
//                            }
//
//                            if (!$contact->save(false)) {
//                                throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
//                            }
//                        }
//
//                        $transaction->commit();
//                        FlashHandler::success("Client and contacts updated successfully.");
//                        return $this->redirect(['view-client', 'id' => $model->id]);
//                    }
//                } catch (\Exception $e) {
//                    $transaction->rollBack();
//                    FlashHandler::err($e->getMessage());
//                    return $this->render('updateClient', [
//                                'model' => $model,
//                                'contactModels' => $contacts,
//                                'isUpdate' => true
//                    ]);
//                }
//            } else {
//                FlashHandler::err("Validation failed.");
//            }
//        }
//
//        // Initial render
//        $contacts = $existingContacts ?: [new \frontend\models\client\ClientContact()];
//        return $this->render('updateClient', [
//                    'model' => $model,
//                    'contactModels' => $contacts,
//                    'isUpdate' => true
//        ]);
//    }

    /**
     * Deletes an existing Clients model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteClient($id) {
        \frontend\models\client\ClientContact::deleteAll(['client_id' => $id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Clients model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Clients the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Clients::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findDebtModel($id) {
        if (($model = ClientDebt::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetClientEmails() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $clientId = Yii::$app->request->get('clientId');

        if (!$clientId) {
            return [];
        }
        $client = Clients::findOne($clientId);
        if (!$client) {
            return [];
        }
        $emails = $client->getEmailsList();
        $results = [];
        foreach ($emails as $email) {
            $results[] = ['id' => $email, 'text' => $email];
        }

        return $results;
    }

    public function actionAjaxAddContact($key, $isUpdate) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $contact = new \frontend\models\client\ClientContact();

        return $this->renderPartial('_formClient_row', ['contact' => $contact, 'index' => $key, 'isUpdate' => $isUpdate]);
    }

    //Mydebug::dumpFileW();
    public function actionAddByTemplateClients() {
        $model = new Clients();
        $clientDebt = new \frontend\models\client\ClientDebt();

        if (Yii::$app->request->isPost) {

            $clientDebt->load(Yii::$app->request->post());

            Yii::$app->session->set('companyGroup', $clientDebt->tk_group_code);
            Yii::$app->session->set('month', $clientDebt->month);
            Yii::$app->session->set('year', $clientDebt->year);

            $excelFile = \yii\web\UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {

                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if (!in_array($extension, ['xls', 'xlsx'])) {
                    Yii::$app->session->setFlash('error', 'Please upload only Excel files (.xls or .xlsx).');
                    return $this->redirect(['add-by-template-clients']);
                }

                try {

                    if ($extension === 'xlsx') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    } else {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    }
                    $reader->setReadDataOnly(true);

                    \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(
                            new \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder()
                    );
                    $spreadsheet = $reader->load($excelFile->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $buffer = [];

                    foreach ($worksheet->getRowIterator(4) as $row) {

                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);
                        $data = [];
                        $companyGroup = $clientDebt->tk_group_code;

                        foreach ($cells as $cell) {

                            $data[] = $cell ? $cell->getCalculatedValue() : null;
                        }
                        $custNo = isset($data[0]) ? trim((string) $data[0]) : null;
                        $name = isset($data[1]) ? trim((string) $data[1]) : null;
                        $balance = isset($data[15]) ? (float) $data[15] : 0;

                        if ($custNo === 'Cust.No.' || empty($custNo)) {
                            continue;
                        }
                        $buffer[] = ['cust_no' => $custNo, 'name' => $name, 'balance' => $balance, 'company_group' => $companyGroup,];
                    }

                    Yii::$app->session->set('client_upload_data', $buffer);

                    if (!empty($buffer)) {
                        return $this->render('uploadToConfirmClients', ['buffer' => $buffer, 'companyGroup' => $clientDebt->tk_group_code, 'month' => $clientDebt->month, 'year' => $clientDebt->year,]);
                    } else {
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the Excel file contains valid data.");
                        return $this->redirect(['add-by-template-clients']);
                    }
                } catch (\Throwable $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['add-by-template-clients']);
                }
            }
        }

        $searchModel = new ClientDebtSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('addByTemplateClients', ['model' => $model, 'clientDebt' => $clientDebt, 'searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    public function actionProcessClientData() {
        $companyGroup = Yii::$app->session->get('companyGroup');
        $postClients = Yii::$app->request->post('Clients');

        if (!$postClients) {
            return $this->redirect(['add-by-template-clients']);
        }
        $custNos = $postClients['cust_no'];
        $names = $postClients['name'];
        $balances = $postClients['balance'];
        $buffer = [];

        foreach ($custNos as $index => $custNo) {
            if (empty($custNo))
                continue;

            $buffer[] = ['cust_no' => trim($custNo), 'name' => $names[$index] ?? '', 'balance' => $balances[$index] ?? 0, 'company_group' => $companyGroup,];
        }

        Yii::$app->session->set('client_upload_data', $buffer);

        return $this->redirect(['check-client-data']);
    }

    public function actionConfirmSubmit() {
        $existData = Yii::$app->session->get('exist_data');
        $notExistData = Yii::$app->session->get('not_exist_data');

        return $this->render('checkClientData', ['existData' => $existData, 'notExistData' => $notExistData, 'companyGroup' => Yii::$app->session->get('companyGroup'), 'month' => Yii::$app->session->get('month'), 'year' => Yii::$app->session->get('year'),]);
    }

    public function actionCheckClientData() {
        $companyGroup = Yii::$app->session->get('companyGroup');
        $clientData = Yii::$app->session->get('client_upload_data');
        $existData = [];
        $notExistData = [];
        $columnMap = ['TK' => 'ac_no_tk', 'TKE' => 'ac_no_tke', 'TKM' => 'ac_no_tkm'];
        $column = $columnMap[$companyGroup] ?? null;
        $custNos = array_column($clientData, 'cust_no');
        $allClients = Clients::find()->where([$column => $custNos])->all();

        $clientMap = [];
        foreach ($allClients as $client) {
            $acNo = $client->$column;
            if (!isset($clientMap[$acNo])) {
                $clientMap[$acNo] = [];
            }
            $clientMap[$acNo][] = $client;
        }

        foreach ($clientData as $row) {
            $row['company_group'] = $row['company_group'] ?? $companyGroup;

            if (isset($clientMap[$row['cust_no']])) {
                $existData[] = $row;
            } else {
                $notExistData[] = $row;
            }
        }

        Yii::$app->session->set('exist_data', $existData);
        Yii::$app->session->set('not_exist_data', $notExistData);
        Yii::$app->session->set('companyGroup', $companyGroup);

        return $this->redirect(['confirm-submit']);
    }

    public function actionSaveExistClient() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $existData = Yii::$app->session->get('exist_data') ?? [];
        $existDataChunk = $existData;
        $companyGroup = Yii::$app->session->get('companyGroup');
        $month = Yii::$app->session->get('month');
        $year = Yii::$app->session->get('year');

        if (empty($existDataChunk)) {
            return ['success' => true, 'done' => true, 'next' => $start];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $columnMap = ['TK' => 'ac_no_tk', 'TKE' => 'ac_no_tke', 'TKM' => 'ac_no_tkm',];
            $fieldMap = ['TK' => 'tk_balance', 'TKE' => 'tke_balance', 'TKM' => 'tkm_balance',];
            $column = $columnMap[$companyGroup] ?? null;
            $field = $fieldMap[$companyGroup] ?? null;

            if (!$column || !$field) {
                throw new \Exception("Invalid company group");
            }

            $custNos = array_column($existDataChunk, 'cust_no');
            $allClients = Clients::find()->where([$column => $custNos])->all();
            $clientMap = [];
            foreach ($allClients as $client) {
                $clientMap[$client->$column][] = $client;
            }

            $clientIds = array_map(fn($c) => $c->id, $allClients);
            $allDebts = ClientDebt::find()->where(['client_id' => $clientIds, 'tk_group_code' => $companyGroup, 'year' => $year, 'month' => $month])->all();
            $debtMap = [];
            foreach ($allDebts as $debt) {
                $debtMap[$debt->client_id] = $debt;
            }

            $insertRows = [];
            $updateRows = [];
            $updatedClients = [];

            foreach ($existDataChunk as $row) {
                $clients = $clientMap[$row['cust_no']] ?? [];

                if (empty($clients)) {
                    continue;
                }

                foreach ($clients as $client) {

                    $oldDebt = $debtMap[$client->id] ?? null;

                    if ($oldDebt === null) {
                        $insertRows[] = [$client->id, $companyGroup, $year, $month, (float) $row['balance']];
                    } else {
                        $updateRows[] = ['id' => $oldDebt->id, 'balance' => (float) $row['balance']];
                    }
                    $client->$field = (float) $row['balance'];
                    $client->current_outstanding_balance = (float) ($client->tk_balance ?? 0) +
                            (float) ($client->tke_balance ?? 0) +
                            (float) ($client->tkm_balance ?? 0);

                    $updatedClients[$client->id] = $client;
                }
            }

            if (!empty($insertRows)) {
//                Yii::$app->db->createCommand()->batchInsert('client_debt', ['client_id', 'tk_group_code', 'year', 'month', 'balance'], $insertRows)->execute();
                foreach ($insertRows as $insert) {

                    $newDebt = new ClientDebt();
                    $newDebt->client_id = $insert[0];
                    $newDebt->tk_group_code = $insert[1];
                    $newDebt->year = $insert[2];
                    $newDebt->month = $insert[3];
                    $newDebt->balance = $insert[4];

                    if (!$newDebt->save()) {
                        throw new \Exception("Failed saving new client debt: " . json_encode($newDebt->errors));
                    }
                }
            }

            if (!empty($updateRows)) {
                foreach ($updateRows as $row) {
//                Yii::$app->db->createCommand()->update('client_debt', ['balance' => $row['balance']], ['id' => $row['id']])->execute();
                    $updateOldRecord = ClientDebt::find()->where(['id' => $row['id']])->one();
                    $updateOldRecord->balance = $row['balance'];
                    if (!$updateOldRecord->save(false)) {
                        throw new \Exception("Failed updating new client debt: " . json_encode($updateOldRecord->errors));
                    }
                }
            }

            foreach ($updatedClients as $client) {

                $latestRecord = ClientDebt::find()
                        ->where(['client_id' => $client->id, 'tk_group_code' => $companyGroup])
                        ->orderBy(['year' => SORT_DESC, 'month' => SORT_DESC])
                        ->one();

                if ($latestRecord && $field) {
                    $client->$field = $latestRecord->balance;
                }

                $client->current_outstanding_balance = ($client->tk_balance ?? 0) + ($client->tke_balance ?? 0) + ($client->tkm_balance ?? 0);

                if (!$client->save(false)) {
                    throw new \Exception("Failed updating client: " . json_encode($client->errors));
                }
            }

            $transaction->commit();
            FlashHandler::success("Saved successfully.");
            return $this->redirect(['index']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed: " . $e->getMessage());
            return $this->redirect([Yii::$app->request->referrer]);
        }
    }

    public function actionExportNotFoundClients() {
        $rawData = Yii::$app->request->post('data');
        $data = json_decode($rawData);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        if (empty($data)) {
            $data = [];
        }

        return $this->renderPartial('notFoundClientsCSV', ['data' => $data]);
    }

    public function actionIndexReminderLetterTemplate() {
        $searchModel = new ClientReminderLetterTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexReminderLetterTemplate', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    public function actionCreateReminderLetterTemplate() {
        $model = new ClientReminderLetterTemplate();

        if (Yii::$app->request->post()) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

                $contentTemplate = Yii::$app->request->post('ClientReminderLetterTemplate');

                $model->letter_name = $contentTemplate["letter_name"];
                $model->active_sts = (int) $contentTemplate["active_sts"];
                $content = $contentTemplate["content"];
                $content = preg_replace('/<\/?(ul|ol|li|table|tbody|thead|tr|td|th|span|font)[^>]*>/i', '', $content);
                $content = strip_tags($content, '<p><br><b><strong><i><em>');
                $content = preg_replace('/ style=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/ class=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/ lang=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/<p>\s*(<br\s*\/?>|&nbsp;)?\s*<\/p>/i', '', $content);
                $content = preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br>', $content);
                $content = trim($content);
                $model->content = $content;

                if (!$model->save()) {
                    throw new \Exception("Failed saving template: " . json_encode($model->errors));
                }

                $transaction->commit();
                FlashHandler::success("Client Reminder Letter created successfully.");
                return $this->redirect(['view-client-reminder-letter-template', 'id' => $model->id]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }

        return $this->render('_formReminderLetterTemplate', ['model' => $model,]);
    }

    public function actionViewClientReminderLetterTemplate($id) {

        $model = ClientReminderLetterTemplate::find()->where(['id' => $id])->one();
        return $this->render('viewClientReminderLetterTemplate', ['model' => $model,]);
    }

    public function actionUpdateClientReminderLetterTemplate($id) {
        $model = ClientReminderLetterTemplate::find()->where(['id' => $id])->one();

        if (Yii::$app->request->post()) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $contentTemplate = Yii::$app->request->post('ClientReminderLetterTemplate');

                $model->letter_name = $contentTemplate["letter_name"];

                $content = $contentTemplate["content"];
                $content = preg_replace('/<\/?(ul|ol|li|table|tbody|thead|tr|td|th|span|font)[^>]*>/i', '', $content);
                $content = strip_tags($content, '<p><br><b><strong><i><em>');
                $content = preg_replace('/ style=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/ class=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/ lang=("|\')(.*?)("|\')/i', '', $content);
                $content = preg_replace('/<p>\s*(<br\s*\/?>|&nbsp;)?\s*<\/p>/i', '', $content);
                $content = preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br>', $content);
                $content = trim($content);

                $model->content = $content;
                $model->active_sts = (int) $contentTemplate["active_sts"];

                if (!$model->save()) {
                    throw new \Exception("Failed updating template: " . json_encode($model->errors));
                }

                $transaction->commit();
                FlashHandler::success("Client Reminder Letter updated successfully.");
                return $this->redirect(['view-client-reminder-letter-template', 'id' => $model->id]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }
        return $this->render('_formReminderLetterTemplate', ['model' => $model,]);
    }

    public function actionCreateReminderLetterEmails($client_id, $id = null) {
        if ($id === null) {

            $model = new ClientReminderLetterEmails();
            $model->client_id = $client_id;
            $model->sender = Yii::$app->user->identity->email;
        } else {

            $model = ClientReminderLetterEmails::findOne($id);

            if ($model === null) {
                throw new \yii\web\NotFoundHttpException('Draft not found.');
            }

            if ($model->status != 1) {
                throw new \yii\web\ForbiddenHttpException('Only draft emails can be edited.');
            }
        }

        $session = Yii::$app->session;
        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $keyReminderRows = 'reminder_rows_' . $client_id;

        if (
                !Yii::$app->request->isPost &&
                Yii::$app->request->get('restore') != 1
        ) {
            $session->remove($keyPdf);
            $session->remove($keyFiles);
            $session->remove($keyForm);
            $session->remove($keyReminderRows);
        }
        if ($id != null && empty($session->get($keyForm))) {
            $formData = $model->attributes;
            $session->set($keyForm, $formData);
            $reminderRows = [];

            foreach ($model->clientReminderLetterEmailDetails as $detail) {
                $reminderRows[] = [
                    'company_group' => $detail->company_group,
                    'template_id' => $detail->template_id,
                    'template_content' => $detail->template_content,
                ];
            }
            $uploadedFiles = [];

            foreach ($model->attachments as $attachment) {
                if (strpos($attachment->file_name, 'Reminder Letter') === 0) {
                    continue;
                }
                $uploadedFiles[] = $attachment->file_name;
            }
            $session->set($keyFiles, $uploadedFiles);
        } else {
            $formData = $session->get($keyForm, []);
            $uploadedFiles = $session->get($keyFiles, []);
            $reminderRows = $session->get($keyReminderRows, []);
        }
        if (!empty($formData)) {
            $model->attributes = $formData;
        }
        $templates = ClientReminderLetterTemplate::find()
                ->select(['id', 'letter_name'])
                ->where(['active_sts' => 0])
                ->all();

        if ($model->load(Yii::$app->request->post())) {

            $sender = trim($model->sender);

            $cc = preg_split('/[\r\n,]+/', $model->Cc);
            $cc = array_filter(array_map('trim', $cc));

            if (!in_array($sender, $cc)) {
                array_unshift($cc, $sender);
            }

            $model->Cc = implode(', ', array_unique($cc));

            switch (Yii::$app->request->post('action')) {

                case 'preview':
                    return $this->previewReminderLetterEmail(
                                    $model,
                                    $client_id,
                                    $session
                            );

                case 'draft':
                    return $this->saveReminderLetterDraft(
                                    $model,
                                    $client_id,
                                    $session
                            );

                case 'send':
                    return $this->sendReminderLetterEmail(
                                    $model,
                                    $client_id,
                                    $session
                            );
                default:
                    Yii::$app->session->setFlash('error', 'Invalid action.');
                    return $this->refresh();
            }
        }

        return $this->render('createClientReminderLetterEmails', [
                    'model' => $model,
                    'templates' => $templates,
                    'uploadedFiles' => $uploadedFiles,
                    'reminderRows' => $reminderRows,
        ]);
    }

    private function previewReminderLetterEmail($model, $client_id, $session) {
        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $keyReminderRows = 'reminder_rows_' . $client_id;
        $postData = Yii::$app->request->post('ClientReminderLetterEmails');
        $reminderRows = Yii::$app->request->post('ReminderRows', []);

        $model->content = $postData['content'] ?? '';

        $pathFolder = Yii::getAlias('@frontend/web/uploads/client-reminder-letter-attachment/');

        if (!is_dir($pathFolder)) {
            \yii\helpers\FileHelper::createDirectory($pathFolder, 0775, true);
        }

        $uploadedFilesInput = UploadedFile::getInstances($model, 'attachment');
        $uploadedFileNames = $session->get($keyFiles, []);

        foreach ($uploadedFilesInput as $file) {

            if (!$file) {
                continue;
            }

            $cleanName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $file->baseName);
            $clientName = preg_replace('/[^A-Za-z0-9]+/', '_', $model->client->company_name);

            $month = date('F');
            $year = date('Y');

            $baseName = $cleanName . '_' . $clientName . '_' . $month . '_' . $year;
            $extension = '.' . $file->extension;

            $newName = $baseName . $extension;
            $filePath = $pathFolder . $newName;

            $counter = 2;

            while (file_exists($filePath)) {
                $newName = $baseName . '_(' . $counter . ')' . $extension;
                $filePath = $pathFolder . $newName;
                $counter++;
            }

            $file->saveAs($filePath);

            $uploadedFileNames[] = $newName;
        }

        $pdfFiles = [];

        $clientName = preg_replace('/[^A-Za-z0-9]+/', '_', $model->client->company_name);
        $month = date('F');
        $year = date('Y');

        foreach ($reminderRows as $index => $row) {

            if (empty($row['template_content'])) {
                continue;
            }

            $pdfContent = $row['template_content'];

            $pdfContent = preg_replace('/<\/?(ul|ol|li|table|tbody|thead|tr|td|th)[^>]*>/i', '', $pdfContent);
            $pdfContent = preg_replace('/ style=("|\')(.*?)("|\')/i', '', $pdfContent);
            $pdfContent = preg_replace('/ class=("|\')(.*?)("|\')/i', '', $pdfContent);

            $companyGroup = $row['company_group'];

            $pdfName = 'Reminder Letter_' .
                    $companyGroup . '_' .
                    $clientName . '_' .
                    $month . '_' .
                    $year . '.pdf';

            $pdfPath = $pathFolder . $pdfName;

            if (empty($companyGroup)) {
                Yii::$app->session->setFlash(
                        'error',
                        'Please select a Company Group for Reminder Letter #' . ($index + 1) . '.'
                );

                return $this->redirect([
                            'create-reminder-letter-emails',
                            'client_id' => $client_id,
                            'restore' => 1,
                ]);
            }

            $company = RefCompanyGroupList::find()
                    ->where(['code' => $companyGroup])
                    ->one();

            if ($company === null) {
                Yii::$app->session->setFlash(
                        'error',
                        'Invalid Company Group selected for Reminder Letter #' . ($index + 1) . '.'
                );

                return $this->redirect([
                            'create-reminder-letter-emails',
                            'client_id' => $client_id,
                            'restore' => 1,
                ]);
            }

            $this->generateReminderLetterPDF(
                    $pdfContent,
                    $pdfPath,
                    $companyGroup
            );

            $pdfFiles[] = $pdfName;
        }

        $formData = $model->attributes;

        $session->set($keyReminderRows, $reminderRows);
        $session->set($keyPdf, $pdfFiles);
        $session->set($keyFiles, $uploadedFileNames);
        $session->set($keyForm, $formData);

        return $this->redirect([
                    'confirm-reminder-letter-emails',
                    'client_id' => $client_id,
        ]);
    }

    private function saveReminderLetterDraft($model, $client_id, $session) {
        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $keyReminderRows = 'reminder_rows_' . $client_id;

        $formData = $session->get($keyForm, []);
        $reminderRows = $session->get($keyReminderRows, []);

        if (!empty($formData['id'])) {

            $existingModel = ClientReminderLetterEmails::findOne($formData['id']);

            if ($existingModel !== null) {
                $model = $existingModel;
            }
        }

        $model->attributes = $formData;
        $model->content = $formData['content'] ?? '';
        $model->recipient = trim($formData['recipient'] ?? '');
        $model->subject = $formData['subject'] ?? '';
        $model->Cc = $formData['Cc'] ?? '';
        $model->Bcc = $formData['Bcc'] ?? '';

        if (!empty($reminderRows[0]['template_id'])) {
            $model->template_id = $reminderRows[0]['template_id'];
        }

        $model->status = 1;
        $model->sent_by = null;
        $model->sent_at = null;

        if ($model->isNewRecord) {
            $model->created_at = date('Y-m-d H:i:s');
        }

        if ($model->save(false)) {

            ClientReminderLetterEmailDetail::deleteAll([
                'email_id' => $model->id,
            ]);

            ClientReminderLetterEmailAttachment::deleteAll([
                'email_id' => $model->id,
            ]);

            $reminderRows = $session->get($keyReminderRows, []);

            foreach ($reminderRows as $row) {
                $detail = new ClientReminderLetterEmailDetail();
                $detail->email_id = $model->id;
                $detail->template_id = $row['template_id'];
                $detail->company_group = $row['company_group'];
                $detail->template_content = $row['template_content'];
                $detail->save(false);
            }
            $pdfFiles = $session->get($keyPdf, []);
            $uploadedFiles = $session->get($keyFiles, []);

            $this->saveAttachments(
                    $model->id,
                    $pdfFiles,
                    $uploadedFiles,
                    $reminderRows
            );

            $session->remove($keyPdf);
            $session->remove($keyFiles);
            $session->remove($keyForm);
            $session->remove($keyReminderRows);

            Yii::$app->session->setFlash(
                    'success',
                    'Draft saved successfully.'
            );

            return $this->redirect([
                        'view-client-reminder-letter-emails',
                        'id' => $model->id,
            ]);
        }

        Yii::$app->session->setFlash(
                'error',
                'Unable to save draft.'
        );

        return $this->redirect([
                    'create-reminder-letter-emails',
                    'client_id' => $client_id,
                    'restore' => 1,
        ]);
    }

    private function sendReminderLetterEmail($model, $client_id, $session) {
        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $keyReminderRows = 'reminder_rows_' . $client_id;
        $formData = $session->get($keyForm, []);
        $reminderRows = $session->get($keyReminderRows, []);
        $pdfFiles = $session->get($keyPdf, []);
        $uploadedFiles = $session->get($keyFiles, []);

        $id = $formData['id'] ?? null;

        if (!empty($id)) {

            $draft = ClientReminderLetterEmails::findOne($id);
            if ($draft === null) {
                throw new \yii\web\NotFoundHttpException('Draft not found.');
            }
            $model = $draft;
        }

        $model->attributes = $formData;
        $model->content = $formData['content'] ?? '';
        $content = trim($model->content);
        $htmlContent = $content;
        $userCc = $formData['Cc'] ?? '';
        $ccArray = [];

        if (!empty($userCc)) {
            $ccArray = array_map('trim', explode(',', $userCc));
        }

        $senderEmail = trim($model->sender);

        if (!empty($senderEmail) && !in_array($senderEmail, $ccArray)) {
            $ccArray[] = $senderEmail;
        }

        $ccArray = array_unique(array_filter($ccArray));
        $model->Cc = implode(', ', $ccArray);

        if (empty($model->recipient)) {
            Yii::$app->session->setFlash('error', 'Recipient missing');
            return $this->refresh();
        }

        $hasContent = false;

        foreach ($reminderRows as $row) {

            if (!empty(trim(strip_tags($row['template_content'])))) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent) {
            Yii::$app->session->setFlash('error', 'At least one reminder letter content is required.');
            return $this->refresh();
        }

        $model->status = 2;
        $model->sent_by = Yii::$app->user->id;
        $model->sent_at = date('Y-m-d H:i:s');
        $model->content = $htmlContent;

        foreach (array_filter(array_map('trim', explode(',', $model->recipient))) as $email) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Yii::$app->session->setFlash('error', 'Invalid recipient email.');
                return $this->refresh();
            }
        }
        foreach (array_filter(array_map('trim', explode(',', $model->Cc))) as $email) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Yii::$app->session->setFlash('error', 'Invalid CC email.');
                return $this->refresh();
            }
        }
        foreach (array_filter(array_map('trim', explode(',', $formData['Bcc'] ?? ''))) as $email) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Yii::$app->session->setFlash('error', 'Invalid BCC email.');
                return $this->refresh();
            }
        }
        if (!empty($reminderRows[0]['template_id'])) {
            $model->template_id = $reminderRows[0]['template_id'];
        }
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', 'Unable to save email.');
            return $this->refresh();
        }
        ClientReminderLetterEmailDetail::deleteAll([
            'email_id' => $model->id,
        ]);

        ClientReminderLetterEmailAttachment::deleteAll([
            'email_id' => $model->id,
        ]);

        foreach ($reminderRows as $row) {

            $detail = new ClientReminderLetterEmailDetail();
            $detail->email_id = $model->id;
            $detail->template_id = $row['template_id'];
            $detail->company_group = $row['company_group'];
            $detail->template_content = $row['template_content'];
            $detail->save(false);
        }

        $toList = array_filter(array_map('trim', explode(',', $model->recipient)));
        $CcList = array_filter(array_map('trim', explode(',', $model->Cc)));
        $BccList = array_filter(array_map('trim', explode(',', $formData['Bcc'] ?? '')));

        $textContent = html_entity_decode(
                trim(
                        strip_tags(
                                str_replace(
                                        ['</p>', '</div>', '<br>', '<br/>', '<br />'],
                                        PHP_EOL,
                                        $htmlContent
                                )
                        )
                )
        );

        $textContent = preg_replace("/(\r?\n){3,}/", "\n\n", $textContent);

        $mail = Yii::$app->mailer->compose()
                ->setFrom($model->sender)
                ->setTo($toList)
                ->setSubject($model->subject)
//                ->setHtmlBody($htmlContent) // causes white bg
                ->setTextBody($textContent);

        if (!empty($CcList)) {
            $mail->setCc($CcList);
        }

        if (!empty($BccList)) {
            $mail->setBcc($BccList);
        }

        $pathFolder = Yii::getAlias('@frontend/web/uploads/client-reminder-letter-attachment/');

        foreach ($pdfFiles as $pdfFile) {

            if (!empty($pdfFile) && file_exists($pathFolder . $pdfFile)) {
                $mail->attach($pathFolder . $pdfFile);
            }
        }

        foreach ($uploadedFiles as $file) {

            if (file_exists($pathFolder . $file)) {
                $mail->attach($pathFolder . $file);
            }
        }

        try {

            if ($mail->send()) {

                $this->saveAttachments(
                        $model->id,
                        $pdfFiles,
                        $uploadedFiles,
                        $reminderRows
                );

                $session->remove($keyPdf);
                $session->remove($keyFiles);
                $session->remove($keyForm);
                $session->remove($keyReminderRows);

                Yii::$app->session->setFlash(
                        'success',
                        'Email sent successfully.'
                );

                return $this->redirect([
                            'view-client-reminder-letter-emails',
                            'id' => $model->id,
                ]);
            }

            Yii::$app->session->setFlash(
                    'error',
                    'Email failed to send.'
            );

            return $this->refresh();
        } catch (\Exception $e) {

            Yii::$app->session->setFlash(
                    'error',
                    $e->getMessage()
            );

            return $this->refresh();
        }
    }

    private function saveAttachments($emailId, $pdfFiles = [], $uploadedFiles = [], $reminderRows = []) {
        // Save generated reminder letter PDFs
        foreach ($pdfFiles as $index => $pdfFile) {

            $attachment = new ClientReminderLetterEmailAttachment();
            $attachment->email_id = $emailId;
            $attachment->file_name = $pdfFile;

            if (isset($reminderRows[$index])) {
                $attachment->company_group = $reminderRows[$index]['company_group'] ?? null;
                $attachment->template_id = $reminderRows[$index]['template_id'] ?? null;
                $attachment->template_content = $reminderRows[$index]['template_content'] ?? null;
            }
            $attachment->save(false);
        }

        // Save uploaded attachments
        foreach ($uploadedFiles as $file) {

            $attachment = new ClientReminderLetterEmailAttachment();
            $attachment->email_id = $emailId;
            $attachment->file_name = $file;

            // General attachments are not tied to a reminder template
            $attachment->company_group = null;
            $attachment->template_id = null;
            $attachment->template_content = null;

            $attachment->save(false);
        }
    }

    public function actionViewClientReminderLetterEmails($id) {
        $model = ClientReminderLetterEmails::findOne($id);
        return $this->render('viewClientReminderLetterEmails', ['model' => $model,]);
    }

    public function actionGetReminderLetterContent($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $template = \frontend\models\client\ClientReminderLetterTemplate::findOne($id);

        if ($template) {
            return $template->content;
        }
        return '';
    }

    public function actionDeleteReminderLetterEmails($id) {
        $attachments = ClientReminderLetterEmailAttachment::findAll(['email_id' => $id]);
        foreach ($attachments as $file) {
            $filePath = Yii::getAlias('@frontend/uploads/client-reminder-letter-attachment/' . $file->file_name);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        ClientReminderLetterEmailAttachment::deleteAll(['email_id' => $id]);
        $model = ClientReminderLetterEmails::findOne($id);
        if ($model) {
            $clientId = $model->client_id;
            $model->delete();
        }
        return $this->redirect(['view-client', 'id' => $clientId]);
    }

    public function actionUpdateReminderLetterEmails($id) {
        $model = ClientReminderLetterEmails::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Record not found');
        }
        $templates = ClientReminderLetterTemplate::find()
                ->select(['id', 'letter_name'])
                ->where(['active_sts' => 0])
                ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->attachment = UploadedFile::getInstances($model, 'attachment');
            $model->sent_by = Yii::$app->user->id;
            $model->sent_at = date('Y-m-d H:i:s');
            $model->content = preg_replace('/class="Mso[a-zA-Z0-9]+"/i', '', $model->content);
            $model->content = preg_replace('/style="[^"]*"/i', '', $model->content);
            $model->content = preg_replace('/<font[^>]*>/i', '', $model->content);
            $model->content = preg_replace('/<\/font>/i', '', $model->content);
            $model->content = preg_replace('/<o:p>.*?<\/o:p>/i', '', $model->content);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Email updated successfully!');
                return $this->redirect(['view-client-reminder-letter-emails', 'id' => $model->id]);
            }
        }
        return $this->render('updateClientReminderLetterEmails', ['model' => $model, 'templates' => $templates,]);
    }

    private function generateReminderLetterPDF($content, $filePath, $companyGroup) {
        $content = html_entity_decode($content);
        $content = preg_replace('/<\/?(ul|ol|li)[^>]*>/i', '', $content);
        $content = preg_replace('/ style=("|\')(.*?)("|\')/i', '', $content);
        $content = preg_replace('/ class=("|\')(.*?)("|\')/i', '', $content);
        $content = preg_replace('/ lang=("|\')(.*?)("|\')/i', '', $content);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 10,
            'default_font' => 'Times',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
            'defaultheaderline' => 0,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
        ]);

        $mpdf->defaultfooterline = 0;
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:8pt;"> Page: {PAGENO} of {nbpg} </div> ');

        $company = RefCompanyGroupList::find()
                ->where(['code' => $companyGroup])
                ->one();

        $htmlHeader = $this->renderPartial('_reminderLetterHeader', ['company' => $company]);
        $mpdf->SetHTMLHeader($htmlHeader);

        $content .= '<htmlpagefooter name="lastFooter"> 
                     <div style="text-align:center; font-size:8pt;"> This is a computer-generated reminder letter, no signature required. </div>
                     <div style="text-align:right; font-size:8pt;"> Page: {PAGENO} of {nbpg} </div> </htmlpagefooter>
                     <sethtmlpagefooter name="lastFooter" value="on" page="LAST" /> ';

        $mpdf->WriteHTML($content);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
    }

    public function actionRemoveTempFile($file, $client_id, $from = null) {

        $session = Yii::$app->session;
        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $pdfFiles = $session->get($keyPdf, []);
        $files = $session->get($keyFiles, []);

        $pathFolder = Yii::getAlias('@frontend/web/uploads/client-reminder-letter-attachment/');
        $filePath = $pathFolder . $file;

        if (in_array($file, $pdfFiles)) {

            $pdfFiles = array_filter($pdfFiles, function ($f) use ($file) {
                return trim($f) !== trim($file);
            });

            $pdfFiles = array_values($pdfFiles);
            $session->set($keyPdf, $pdfFiles);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } else {

            $files = array_filter($files, function ($f) use ($file) {
                return trim($f) !== trim($file);
            });

            $files = array_values($files);
            $session->set($keyFiles, $files);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($from === 'confirm') {

            return $this->redirect([
                        'confirm-reminder-letter-emails',
                        'client_id' => $client_id,
            ]);
        }

        return $this->redirect([
                    'client/create-reminder-letter-emails',
                    'client_id' => $client_id,
                    'restore' => 1,
        ]);
    }

    public function actionRemoveTempFileAjax() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $file = Yii::$app->request->post('file');
        $client_id = Yii::$app->request->post('client_id');

        $session = Yii::$app->session;

        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;

        $pdfFiles = $session->get($keyPdf, []);
        $files = $session->get($keyFiles, []);

        $path = Yii::getAlias('@frontend/web/uploads/client-reminder-letter-attachment/');
        $filePath = $path . $file;

        if (in_array($file, $pdfFiles)) {

            $pdfFiles = array_values(array_filter($pdfFiles, function ($f) use ($file) {
                        return trim($f) !== trim($file);
                    }));

            $session->set($keyPdf, $pdfFiles);
        } else {

            $files = array_values(array_filter($files, function ($f) use ($file) {
                        return trim($f) !== trim($file);
                    }));

            $session->set($keyFiles, $files);
        }

        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        return [
            'success' => true
        ];
    }

    public function actionConfirmReminderLetterEmails($client_id) {
        $session = Yii::$app->session;

        $keyPdf = 'temp_pdf_' . $client_id;
        $keyFiles = 'uploaded_files_' . $client_id;
        $keyForm = 'form_data_' . $client_id;
        $keyReminderRows = 'reminder_rows_' . $client_id;
        $formData = $session->get($keyForm, []);
        $reminderRows = $session->get($keyReminderRows, []);

        if (empty($formData)) {
            return $this->redirect([
                        'create-reminder-letter-emails',
                        'client_id' => $client_id,
            ]);
        }

        $id = $formData['id'] ?? null;

        if (!empty($id)) {

            $model = ClientReminderLetterEmails::findOne($id);

            if ($model === null) {
                throw new \yii\web\NotFoundHttpException('Draft not found.');
            }

            $model->attributes = $formData;
        } else {

            $model = new ClientReminderLetterEmails();
            $model->attributes = $formData;
        }

        return $this->render('confirmReminderLetterEmails', [
                    'model' => $model,
                    'pdfFiles' => $session->get($keyPdf, []),
                    'uploadedFiles' => $session->get($keyFiles, []),
                    'reminderRows' => $reminderRows,
        ]);
    }

    public function actionIndexDebtReminderLetterTemplate() {
        $searchModel = new ClientReminderLetterTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexDebtReminderLetterTemplate', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    public function actionIndexGeneralClientDebt() {
        $searchModel = new ClientDebtSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexGeneralClientDebt', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    public function actionIndexGeneralDebtReminderLetterEmailLog() {
        $searchModel = new ClientReminderLetterEmailsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexGeneralDebtReminderLetterEmailLog', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]);
    }

    public function actionUpdateDebt($id) {
        $model = $this->findDebtModel($id);

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                $duplicateRecords = ClientDebt::find()
                        ->where(['client_id' => $model->client_id, 'month' => $model->month, 'year' => $model->year, 'tk_group_code' => $model->tk_group_code,])
                        ->andWhere(['!=', 'id', $model->id])
                        ->all();

                foreach ($duplicateRecords as $record) {
                    $record->delete();
                }

                $this->updateClientBalances($model->client_id);
                Yii::$app->session->setFlash('success', 'Updated successfully.');
            }
            return $this->redirect(array_merge(['index-general-client-debt'], Yii::$app->request->queryParams));
        }
        return $this->renderAjax('_formUpdateClientDebt', ['model' => $model,]);
    }

    public function actionDeleteDebt($id) {
        $model = $this->findDebtModel($id);
        $clientId = $model->client_id;
        $model->delete();
        $this->updateClientBalances($clientId);

        Yii::$app->session->setFlash('success', 'Debt summary deleted successfully.');
        return $this->redirect(array_merge(['index-general-client-debt'], Yii::$app->request->queryParams));
    }

    private function updateClientBalances($clientId) {
        $latestRecords = ClientDebt::find()
                ->where(['client_id' => $clientId,])
                ->orderBy(['year' => SORT_DESC, 'month' => SORT_DESC, 'id' => SORT_DESC,])
                ->all();

        $tkBalance = 0;
        $tkeBalance = 0;
        $tkmBalance = 0;

        foreach ($latestRecords as $record) {
            if (
                    $record->tk_group_code == 'TK' && $tkBalance == 0
            ) {
                $tkBalance = $record->balance;
            }
            if (
                    $record->tk_group_code == 'TKE' && $tkeBalance == 0
            ) {
                $tkeBalance = $record->balance;
            }
            if (
                    $record->tk_group_code == 'TKM' && $tkmBalance == 0
            ) {
                $tkmBalance = $record->balance;
            }
        }
        $total = $tkBalance + $tkeBalance + $tkmBalance;
        $client = Clients::findOne($clientId);

        if ($client) {
            $client->tk_balance = $tkBalance;
            $client->tke_balance = $tkeBalance;
            $client->tkm_balance = $tkmBalance;
            $client->current_outstanding_balance = $total;
            $client->save(false);
        }
    }

    public function actionCreateNewEntry() {
        $model = new ClientDebt();
        $clientList = \yii\helpers\ArrayHelper::map(Clients::find()->orderBy(['company_name' => SORT_ASC])->all(), 'id', 'company_name');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $existingRecord = ClientDebt::find()
                        ->where(['client_id' => $model->client_id, 'tk_group_code' => $model->tk_group_code, 'month' => $model->month, 'year' => $model->year,])
                        ->one();

                if ($existingRecord) {
                    $existingRecord->balance = $model->balance;
                    $existingRecord->updated_at = date('Y-m-d H:i:s');
                    $existingRecord->updated_by = Yii::$app->user->id;

                    if (!$existingRecord->save(false)) {
                        throw new \Exception('Failed to update existing record.');
                    }
                } else {
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->created_by = Yii::$app->user->id;

                    if (!$model->save(false)) {
                        throw new \Exception('Failed to save new record.');
                    }
                }
                $latestRecords = ClientDebt::find()
                        ->where(['client_id' => $model->client_id])
                        ->orderBy(['year' => SORT_DESC, 'month' => SORT_DESC, 'id' => SORT_DESC])
                        ->all();

                $tkBalance = 0;
                $tkeBalance = 0;
                $tkmBalance = 0;

                foreach ($latestRecords as $record) {
                    if ($record->tk_group_code == 'TK' && $tkBalance == 0) {
                        $tkBalance = $record->balance;
                    }
                    if ($record->tk_group_code == 'TKE' && $tkeBalance == 0) {
                        $tkeBalance = $record->balance;
                    }
                    if ($record->tk_group_code == 'TKM' && $tkmBalance == 0) {
                        $tkmBalance = $record->balance;
                    }
                }
                $client = Clients::findOne($model->client_id);
                if ($client) {
                    $client->tk_balance = $tkBalance;
                    $client->tke_balance = $tkeBalance;
                    $client->tkm_balance = $tkmBalance;
                    $client->current_outstanding_balance = $tkBalance + $tkeBalance + $tkmBalance;
                    $client->save(false);
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Client debt record saved successfully.');
                return $this->redirect(['index-general-client-debt']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->renderAjax('_formNewEntry', ['model' => $model, 'clientList' => $clientList,]);
    }

    public function actionGetClientCode($id) {
        $client = Clients::findOne($id);

        if ($client) {
            return $client->client_code;
        }
        return '';
    }

    public function actionClientAutocomplete($term = '') {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $clients = Clients::find()
                ->where(['like', 'company_name', $term . '%', false])
                ->orderBy(['company_name' => SORT_ASC])
                ->all();

        $result = [];

        foreach ($clients as $client) {
            $result[] = [
                'label' => $client->client_code . ' - ' . $client->company_name,
                'value' => $client->company_name,
                'id' => $client->id,
                'client_code' => $client->client_code,
            ];
        }
        return $result;
    }
}
