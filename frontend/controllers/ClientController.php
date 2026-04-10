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
use frontend\models\client\ClientDebtSearch;
use common\models\myTools\Mydebug;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\Url;

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
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Clients models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexClient', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
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

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // limit results to this client
        $dataProvider->query->andWhere(['client_id' => $model->id]);
        $dataProvider->pagination = false;
        return $this->render('viewClient', [
                    'model' => $model,
                    'contacts' => $contacts,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
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
                        return $this->render('uploadToConfirmClients', [
                                    'buffer' => $buffer,
                                    'companyGroup' => $clientDebt->tk_group_code,
                                    'month' => $clientDebt->month,
                                    'year' => $clientDebt->year,
                        ]);
                    } else {
                        \common\models\myTools\FlashHandler::err(
                                "Upload failed: Please ensure that the Excel file contains valid data."
                        );
                        return $this->redirect(['add-by-template-clients']);
                    }
                } catch (\Throwable $e) {
                    Yii::$app->session->setFlash(
                            'error',
                            'Error reading the Excel file: ' . $e->getMessage()
                    );
                    return $this->redirect(['add-by-template-clients']);
                }
            }
        }

        $searchModel = new ClientDebtSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('addByTemplateClients', [
                    'model' => $model,
                    'clientDebt' => $clientDebt,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
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
                            throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
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
    //updated by khetty, 15/11/2025
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
                if (!$contact->save(false)) {
                    throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
                }
            }
            $postedIDs = array_filter(\yii\helpers\ArrayHelper::getColumn($contacts, 'id'));
            $deletedIDs = array_diff($oldIDs, $postedIDs);
            $valid = $model->validate() && \yii\base\Model::validateMultiple($contacts);

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->processAndSave()) {
                        if (!empty($deletedIDs)) {
                            \frontend\models\client\ClientContact::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($contacts as $contact) {
                            $contact->client_id = $model->id;
                            if (!$contact->save(false)) {
                                throw new \Exception("Failed saving contact: " . json_encode($contact->errors));
                            }
                        }
                        $transaction->commit();
                        FlashHandler::success("Client and contacts updated successfully.");
                        return $this->redirect(['view-client', 'id' => $model->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    FlashHandler::err($e->getMessage());
                    return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
                }
            } else {
                FlashHandler::err("Validation failed.");
            }
        }
        $contacts = $existingContacts ?: [new \frontend\models\client\ClientContact()];
        return $this->render('updateClient', ['model' => $model, 'contactModels' => $contacts, 'isUpdate' => true]);
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

//    public function actionProcessChunk() {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//
//        $clientData = Yii::$app->session->get('client_upload_data');
//        $companyGroup = Yii::$app->session->get('companyGroup');
//        $month = Yii::$app->session->get('month');
//        $year = Yii::$app->session->get('year');
//        $start = Yii::$app->request->post('index', 0);
//        $limit = 10;
//        $slice = array_slice($clientData, $start, $limit);
//        $columnMap = ['TK' => 'ac_no_tk', 'TKE' => 'ac_no_tke', 'TKM' => 'ac_no_tkm',];
//        $column = $columnMap[$companyGroup] ?? null;
//
//        foreach ($slice as $row) {
//
//            $client = Clients::find()->where([$column => $row['cust_no']])
//                    ->one();
//
//            if (!$client)
//                continue;
//
//            $debt = new ClientDebt();
//            $debt->client_id = $client->id;
//            $debt->tk_group_code = $companyGroup;
//            $debt->month = $month;
//            $debt->year = $year;
//            $debt->balance = $row['balance'];
//            $debt->save();
//        }
//        $nextIndex = $start + $limit;
//
//        return [
//            'nextIndex' => $nextIndex,
//            'done' => $nextIndex >= count($clientData)
//        ];
//    }

    public function actionConfirmSubmit() {
        $existData = Yii::$app->session->get('exist_data');
        $notExistData = Yii::$app->session->get('not_exist_data');

        return $this->render('checkClientData', [
                    'existData' => $existData,
                    'notExistData' => $notExistData,
                    'companyGroup' => Yii::$app->session->get('companyGroup'),
        ]);
    }

    public function actionCheckClientData() {
        $companyGroup = Yii::$app->session->get('companyGroup');
        $clientData = Yii::$app->session->get('client_upload_data');
        $existData = [];
        $notExistData = [];
        $columnMap = ['TK' => 'ac_no_tk', 'TKE' => 'ac_no_tke', 'TKM' => 'ac_no_tkm',];
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
                if (!$client->save(false)) {
                    throw new \Exception("Failed updating client outstanding balance: " . json_encode($client->errors));
                }
            }

            $transaction->commit();
            FlashHandler::success("Saved successfully.");
            return $this->redirect(['index']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed: " . $e->getMessage());
            return $this->redirect([Yii::$app->request->referrer]);
//            return $this->redirect(['index']);
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

        return $this->renderPartial('notFoundClientsCSV', [
                    'data' => $data
        ]);
    }
}
