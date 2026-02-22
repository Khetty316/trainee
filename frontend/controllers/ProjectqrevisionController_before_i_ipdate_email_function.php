<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectquotation\ProjectQRevisions;
use frontend\models\projectquotation\QuotationEmails;
use frontend\models\projectquotation\QuotationEmailAttachments;
//use frontend\models\projectquotation\ProjectQRevisionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\myTools\MyFormatter;
use frontend\models\projectquotation\ProjectQPanelItems;
use frontend\models\projectquotation\ProjectQPanels;
use frontend\models\projectquotation\QuotationPdfMasters;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyCommonFunction;
use frontend\models\projectquotation\ProjectQClients;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

/**
 * ProjectqrevisionController implements the CRUD actions for ProjectQRevisions model.
 */
class ProjectqrevisionController extends Controller {

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
     * Displays a single ProjectQRevisions model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewProjectQRevision($id) {
        $email_model = null; // redundant code
        $quotation = QuotationPdfMasters::findOne(['revision_id' => $id]); //supposed to be all();
        if ($quotation) {
            $email_model = QuotationEmails::findOne(['quotation_id' => $quotation->id]);
        }
        if (!$email_model) {
            $email_model = new QuotationEmails();
//            $email_model->quotation_id = $id;
        }
        return $this->render('viewProjectQRevision', [
                    'model' => $this->findModel($id),
                    'email_model' => $email_model,
                    'quotation' => $quotation
        ]);
    }

    /**
     * Updates an existing ProjectQRevisions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateProjectqrevision($id) {
//        $id = Yii::$app->request->post('ProjectQRevisions')['id'];
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->processAndUpdate()) {
            FlashHandler::success("Updated.");
            return $this->redirect(['view-project-q-revision', 'id' => $id]);
        }

        return $this->render('updateProjectQRevision', [
                    'model' => $model,
                    'currencyList' => \frontend\models\common\RefCurrencies::getActiveDropdownlist_by_id()
        ]);
    }

    public function actionUpdateRevisionDiscount() {
        $req = Yii::$app->request;
        $id = $req->post('ProjectQRevisions')['id'];
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->processAndUpdate()) {
            FlashHandler::success("Updated.");
            return $this->redirect(['view-project-q-revision', 'id' => $id]);
        }
    }

    /**
     * Finds the ProjectQRevisions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectQRevisions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectQRevisions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionNewPanel($revisionId = "") {
        $model = new ProjectQPanels();

        if (Yii::$app->request->isAjax) {
            $model->revision_id = $revisionId;
            $model->by_item_price = 0;
            $model->amount = 0;
            $model->quantity = 1; // Requested by Liew, 2022-12-12, default to 1
            return $this->renderAjax('_ajaxPanelDetail', [
                        'formAction' => 'new-panel',
                        'submitBtnText' => 'Create',
                        'model' => $model
            ]);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                $model->revision->updateRevisionAmount();
                FlashHandler::success("New panel created");
            }
        }
        return $this->redirect(['view-project-q-revision', 'id' => $model->revision_id]);
    }

    public function actionClonePanelSameRevision() {
        $req = Yii::$app->request;
        $motherPanelId = $req->post('motherPanelId');
        $clonePanelNewName = $req->post('clonePanelNewName');
        $newPanel = new ProjectQPanels();
        $newPanel->cloneFromMother($motherPanelId, $clonePanelNewName);
        $revision = $newPanel->revision;
        $revision->updateRevisionAmount();

        return $this->redirect(['view-project-q-revision', 'id' => $newPanel->revision_id]);
    }

//    public function actionRemovePanelAjax() {
//        $panelId = Yii::$app->request->post('panelId');
//        $panel = ProjectQPanels::findOne($panelId);
//        ProjectQPanelItems::deleteAll(['panel_id' => $panelId]);
//        if ($panel->delete()) {
//            $revision = $panel->revision;
//            $revision->updateRevisionAmount();
//            return json_encode(["success" => true, "msg" => "Panel removed"]);
//        }
//        return json_encode(["success" => false, "msg" => "Fail to remove"]);
//    }

    public function actionRemovePanelAjax($id) {
        $panelIds = Yii::$app->request->post('panelIds');

        foreach ($panelIds as $panelId) {
            $panel = ProjectQPanels::findOne($panelId);

            if ($panel === null) {
                throw new \Exception("Panel with ID {$panelId} not found.");
            }

            ProjectQPanelItems::deleteAll(['panel_id' => $panelId]);
            \frontend\models\ProjectProduction\ProjectProductionPanels::deleteAll(['panel_id' => $panelId]);

            if ($panel->delete()) {
                $revision = $panel->revision;
                $revision->updateRevisionAmount();
            }
        }

        FlashHandler::success("Panel(s) deleted successfully.");
        return $this->redirect(['view-project-q-revision', 'id' => $id]);
    }

    public function actionGetApprovalQuotationAjax($id) {
        $quotationIds = Yii::$app->request->post('quotationIds');

        foreach ($quotationIds as $quotationId) {
            $quotation = QuotationPdfMasters::findOne($quotationId);

            if ($quotation === null) {
                throw new \Exception("Quotaion with ID {$quotationId} not found.");
            }

            $quotation->md_approval_status = QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL;
            $quotation->save();
        }

        FlashHandler::success("Submitted successfully.");
        return $this->redirect(['view-project-q-revision', 'id' => $id]);
    }

    public function actionLoadRevisionvAmount($revisionId) {
        $revision = $this->findModel($revisionId);
        return json_encode(["amount" => MyFormatter::asDecimal2($revision->amount)]);
    }

    public function actionSortPanelsAjax() {
        $req = Yii::$app->request;

        $revisionId = $req->post('revisionId');
        $moveId = substr_replace($req->post('moveId'), "", 0, 3);
        $previousId = substr_replace($req->post('previousId'), "", 0, 3);
        $panelList = ProjectQPanels::find()->where(['revision_id' => $revisionId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortPanels($moveId, $previousId, $panelList);
        return json_encode(['success' => $result]);
    }

    public function actionControlSstAjax() {
        $req = Yii::$app->request;
        $revision = $this->findModel($req->post('revisionId'));
        $revision->with_sst = $req->post('enableFlag');
        $result = $revision->update(false);
        return json_encode(['success' => $result]);
    }

    private function sortPanels($moveId, $previousId, $panelList) {

        $idxNo = 0;
        foreach ($panelList as $key => $panel) {
            if ($panel->id == $moveId) {
                $idxNo = $key;
                break;
            }
        }
        $tempObject = $panelList[$idxNo];
        unset($panelList[$idxNo]);
        $theKey = $previousId == "" ? 0 : (array_search($previousId, array_column($panelList, 'id')) + 1);
        array_splice($panelList, $theKey, 0, array($tempObject));

        foreach ($panelList as $key => $panel) {
            $panel->updateSort(($key + 1));
        }

        return true;
    }

    /** Generate quotation in PDF Form
     * 
     * @param type $revisionId
     * @param type $projQClientId
     * @return type
     */
//    public function actionGenerateQuotationPdf($revisionId = '', $projQClientId = '') {
//        $model = new QuotationPdfMasters();
//        if (Yii::$app->request->isPost) {
//            $checkModelExist = QuotationPdfMasters::find()
//                            ->where([
//                                'revision_id' => Yii::$app->request->post("QuotationPdfMasters")["revision_id"],
//                                'project_q_client_id' => Yii::$app->request->post("QuotationPdfMasters")["project_q_client_id"]
//                            ])->one();
//            if ($checkModelExist) {
//                $model = $checkModelExist;
//            }
//
//
//            if ($model->load(Yii::$app->request->post())) {
//                $revision = ProjectQRevisions::findOne($model->revision_id);
//                $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
//                $mpdf = $this->generatePdf($model, $revision);
////                return $mpdf->Output("test" . '.pdf', "I");
//                $dir = Yii::$app->params['temp_folder'];
//                $tempFilename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
//                $filename = $tempFilename . ".pdf";
//                MyCommonFunction::mkDirIfNull($dir);
//                $mpdf->Output($dir . $filename);
//                $model->savePdfToBlob($dir, $filename);
//                $model->processAndSave();
//                unlink($dir . $filename);
//                FlashHandler::success("Released.");
//                return $this->redirect(['view-project-q-revision', 'id' => $revisionId]);
//            }
//        }
//
//        if ($revisionId != '' && $projQClientId != '') {
//            $revision = ProjectQRevisions::findOne($revisionId);
//            $projQClient = ProjectQClients::findOne($projQClientId);
//            $client = $projQClient->client;
//            $model->q_date = date("m/d/Y");
//            $model->project_q_client_id = $projQClientId;
//            $model->copyRevisionAndClient($revision, $client);
//        }
//
//        return $this->render('generateQuotationPdf', [
//                    'revision' => $revision,
//                    'model' => $model,
//                    'client' => $client
//        ]);
//    }

    public function actionGenerateQuotationPdf($revisionId = '', $projQClientId = '') {
        $model = new QuotationPdfMasters();
        if (Yii::$app->request->isPost) {
            $checkModelExist = QuotationPdfMasters::find()
                            ->where([
                                'revision_id' => Yii::$app->request->post("QuotationPdfMasters")["revision_id"],
                                'project_q_client_id' => Yii::$app->request->post("QuotationPdfMasters")["project_q_client_id"]
                            ])->one();

            if ($checkModelExist) {
                $model = $checkModelExist;
            }

            if ($model->load(Yii::$app->request->post())) {
                $with_sst = Yii::$app->request->post("QuotationPdfMasters")["with_sst"];

                $revision = ProjectQRevisions::findOne($model->revision_id);
                $projectQmaster = $revision->projectQType->project;

                $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
                $model->md_approval_status = 0;
                $model->md_approval_date = null;
                $model->md_user_id = null;
                $generatePdf = ($projectQmaster->company_group_code === "TKTKM" ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision));
                $mpdf = $generatePdf;
//                $mpdf = $this->generatePdf($model, $revision);
                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
                $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
                $mpdf->Output($pdfPath, 'F');
                $model->with_sst = (int) $with_sst;
                if ($model->processAndSave()) {
                    FlashHandler::success("Released.");
                } else {
                    FlashHandler::err("Failed to save.");
                }

                return $this->redirect(['view-project-q-revision', 'id' => $revisionId]);
            }
        }

        if ($revisionId != '' && $projQClientId != '') {
            $revision = ProjectQRevisions::findOne($revisionId);
            $projQClient = ProjectQClients::findOne($projQClientId);
            $client = $projQClient->client;
            $model->q_date = date("m/d/Y");
            $model->project_q_client_id = $projQClientId;
            $model->copyRevisionAndClient($revision, $client);
        }
        return $this->render('generateQuotationPdf', [
                    'revision' => $revision,
                    'model' => $model,
                    'client' => $client
        ]);
    }

    /** DELETE quotation in PDF Form
     * 
     * @param type $revisionId
     * @param type $projQClientId
     * @return type
     */
    public function actionDeleteQuotationPdf($id) {

        if (!Yii::$app->request->isPost) {
            return "FALSE!";
        }
        $model = QuotationPdfMasters::findOne($id);
        $revId = $model->revision_id;

        if ($model->delete()) {
            FlashHandler::success("Quotation removed.");
        } else {
            FlashHandler::err("Fail to remove. Kindly call for support");
        }

        return $this->redirect(['view-project-q-revision', 'id' => $revId]);
    }

    public function actionSetRevisionAsTemplate() {
        if (!Yii::$app->request->isPost) {
            FlashHandler::err("Illegal access");
            return $this->redirect(['/']);
        }

        $revisionId = Yii::$app->request->post('ProjectQRevisions')['id'];
        $templateName = Yii::$app->request->post('ProjectQRevisions')['revision_description'];
        $revision = $this->findModel($revisionId);

        $revisionTemplate = new \frontend\models\projectquotation\ProjectQRevisionsTemplate();
        $revisionTemplate->setAsTemplate($revision, $templateName);
        FlashHandler::success("Template created");

        return $this->redirect(['view-project-q-revision', 'id' => $revisionId]);
    }

//    private function generatePdf($model, $revision) {
//        $mpdf = new \Mpdf\Mpdf([
//            'mode' => "utf-8",
//            'default_font_size' => 11,
//            'default_font' => 'Arial',
//            'setAutoTopMargin' => "stretch",
//            'setAutoBottomMargin' => "stretch",
//            'defaultheaderline' => 0,
//            'shrink_tables_to_fit' => 1,
//            'showImageErrors' => true,
//        ]);
//
//        $htmlHeader = $this->renderPartial("_quotationPdfHeader", ['revision' => $revision]);
//        $htmlFooter = $this->renderPartial("_quotationPdfFooter", ['model' => $model]);
//        $htmlBody = $this->renderPartial("_quotationPdf", [
//            'model' => $model,
//            'revision' => $revision
//        ]);
//
//        $mpdf->SetHeader($htmlHeader);
//        $mpdf->SetFooter($htmlFooter);
//        $mpdf->WriteHTML($htmlBody);
//
//        return $mpdf;
//    }
    //please update projectquotationController if this function is updated
    private function generatePdf($model, $revision) {
        ini_set("pcre.backtrack_limit", "10000000");
        ini_set("memory_limit", "1024M");

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'Arial',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
            'defaultheaderline' => 0,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
        ]);

        $htmlHeader = $this->renderPartial("_quotationPdfHeader", ['revision' => $revision]);
        $htmlFooter = $this->renderPartial("_quotationPdfFooter", ['model' => $model]);
        $htmlBody = $this->renderPartial("_quotationPdf", [
            'model' => $model,
            'revision' => $revision
        ]);

        $mpdf->SetHeader($htmlHeader);
        $mpdf->SetFooter($htmlFooter);

        // Split HTML into chunks
        $this->writeHtmlInChunks($mpdf, $htmlBody);

        return $mpdf;
    }

    private function generateConsolidatedPdf($model, $revision) {
        ini_set('pcre.backtrack_limit', '100000000'); // 100M
        ini_set('pcre.recursion_limit', '10000000');  // 10M
        ini_set('memory_limit', '4096M');             // 4GB

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font_size' => 11,
            'default_font' => 'Arial',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
            'defaultheaderline' => 0,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
        ]);

        // Define all sections
        $pdfParts = [
            [
                'header' => '_quotationPdfHeaderCon',
                'footer' => '_quotationPdfFooter',
                'body' => '_quotationPdfCon',
            ],
            [
                'header' => '_quotationPdfHeaderTK',
                'footer' => '_quotationPdfFooter',
                'body' => '_quotationPdfTK',
            ],
            [
                'header' => '_quotationPdfHeaderTKM',
                'footer' => '_quotationPdfFooter',
                'body' => '_quotationPdfTKM',
            ],
        ];

        // Render each section into the same PDF
        foreach ($pdfParts as $index => $part) {
            $htmlHeader = $this->renderPartial($part['header'], ['revision' => $revision]);
            $htmlFooter = $this->renderPartial($part['footer'], ['model' => $model]);
            $htmlBody = $this->renderPartial($part['body'], [
                'model' => $model,
                'revision' => $revision,
            ]);

            $pdf->SetHeader($htmlHeader);
            $pdf->SetFooter($htmlFooter);

            // Add a page break before each section except the first
            if ($index > 0) {
                $pdf->AddPage();
            }

            $this->writeHtmlInChunks($pdf, $htmlBody);
        }

        return $pdf;
    }

    private function writeHtmlInChunks($mpdf, $html) {
        $chunkSize = 500000; // 500KB chunks
        $htmlLength = strlen($html);

        if ($htmlLength <= $chunkSize) {
            $mpdf->WriteHTML($html);
            return;
        }

        $chunks = str_split($html, $chunkSize);

        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }
    }

//    public function actionReadPdf($id) {
//        $model = QuotationPdfMasters::findOne($id);
//        header('Pragma: public');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Content-Transfer-Encoding: binary');
//        header('Content-length: ' . $model->file_size);
//        header('Content-Type: ' . $model->file_type);
//        header('Content-Disposition: inline; filename=' . $model->quotation_no . '.pdf');
//        echo $model->file_blob;
////          base64_encode($model->file_blob);
////        echo $model->file_blob;
//    }
    //before tk + tkm
//    public function actionReadPdf($id) {
//        $model = QuotationPdfMasters::findOne($id);
//        if (!$model) {
//            throw new \yii\web\NotFoundHttpException("The requested PDF does not exist.");
//        }
//        $revision = ProjectQRevisions::findOne($model->revision_id);
//        $model->q_date = date("d/m/Y", strtotime($model->q_date));
//        $mpdf = $this->generatePdf($model, $revision);
//        $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
//        $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
//        $mpdf->Output($pdfPath, 'F');
//
//        Yii::$app->response->sendFile($pdfPath, $filename . '.pdf', [
//            'inline' => true,
//        ])->send();
//    }

    public function actionReadPdf($id) {
        $model = QuotationPdfMasters::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("The requested PDF does not exist.");
        }

        $revision = ProjectQRevisions::findOne($model->revision_id);
        $projectQmaster = $revision->projectQType->project;
        $model->q_date = date("d/m/Y", strtotime($model->q_date));

        $generatePdf = ($projectQmaster->company_group_code === "TKTKM" ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision));
        $mpdf = $generatePdf;

        $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
        $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
        $mpdf->Output($pdfPath, 'F');

        Yii::$app->response->sendFile($pdfPath, $filename . '.pdf', [
            'inline' => true,
        ])->send();
    }

    /**
     * *********** EXPORT - to CSV
     */
    public function actionExportToCsv($revisionId) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;
        $projQRevision = ProjectQRevisions::findOne($revisionId);
        $panelList = ProjectQPanels::find()->where(['revision_id' => $revisionId])->orderBy(['sort' => SORT_ASC])->all();
        return $this->renderPartial('_panelListCSV', [
                    'projQRevision' => $projQRevision,
                    'panelList' => $panelList,
                    'sst' => $sst
        ]);
    }

    public function actionUploadTemplate($revisionid) {
        $model = $this->findModel($revisionid);
        $quotation = QuotationPdfMasters::findOne(['revision_id' => $revisionid]);
        if ($model->projectQType->is_finalized) {
            return "Quotation is Finalized, unable to change";
        }

        if (Yii::$app->request->isPost) {
            $excelFile = UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                if ($extension !== 'xls') {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['view-project-q-revision', 'id' => $revisionid]);
                }

                try {
                    $reader = new Xls();
                    $reader->setReadDataOnly(true);

                    // Load only the first worksheet
                    $reader->setLoadSheetsOnly(0);

                    $spreadsheet = $reader->load($excelFile->tempName);

                    $this->processExcelSpreadsheet($spreadsheet, $revisionid);
                    $model->updateRevisionAmount();
                    Yii::$app->session->setFlash('success', 'Excel file processed successfully.');
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    Yii::error('Error reading Excel file: ' . $e->getMessage());
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error processing file: ' . $e->getMessage());
                    Yii::error('Error processing Excel file: ' . $e->getMessage());
                }
            } else {
                Yii::$app->session->setFlash('error', 'No file was uploaded or temporary file is missing.');
            }

            return $this->redirect(['view-project-q-revision', 'id' => $revisionid]);
        }

        return $this->render('uploadTemplate', [
                    'model' => $model
        ]);
    }

    private function processExcelSpreadsheet($spreadsheet, $revisionId) {
        $worksheet = $spreadsheet->getActiveSheet();
        $rowIterator = $worksheet->getRowIterator();

        // Get the maximum sort value for the given revision ID
        $maxSort = ProjectQPanels::find()
                ->where(['revision_id' => $revisionId])
                ->max('sort');

        $sort = $maxSort ? $maxSort + 1 : 1;
        $blankCount = 0;
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Check if we've reached the end
            if (strtolower(trim($rowData[0])) === 'end') {
                break;
            }

            // Skip the header row
            if ($row->getRowIndex() === 1) {
                continue;
            }

            // Check if Panel column is empty
            if (empty(trim($rowData[2]))) {
                $blankCount++;
                if ($blankCount >= 5) {
                    break;
                }
                continue;
            } else {
                $blankCount = 0;
            }

            // Create a new ProjectQPanels model
            $panel = new ProjectQPanels();
            $panel->revision_id = $revisionId;
            $panel->panel_type = $rowData[1];
            $panel->panel_description = $rowData[2];
            $panel->remark = $rowData[3];
            $panel->quantity = $rowData[4];
            $panel->unit_code = $rowData[5];
            $panel->amount = $rowData[6];
            $panel->sort = $sort++;

            if (!$panel->save()) {
                // Handle save error
                Yii::error("Failed to save panel: " . json_encode($panel->errors));
            }
        }
    }

    //Process 50 records at a time
    public function actionSecretGenerateQuotationPdfsForAll() {
        ini_set('memory_limit', '1024M'); // Increase memory limit to 1GB
        set_time_limit(0); // Disable PHP execution time limit

        $batchSize = 50; // Process 50 records at a time
        $totalRecords = QuotationPdfMasters::find()->count();
        $uploadDir = Yii::$app->params['quotation_pdf_path'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Process records in batches
        for ($offset = 0; $offset < $totalRecords; $offset += $batchSize) {
            $models = QuotationPdfMasters::find()->offset($offset)->limit($batchSize)->all();

            foreach ($models as $model) {
                try {
                    $revision = ProjectQRevisions::findOne($model->revision_id);
                    $projectQmaster = $revision->projectQType->project;
                    if (!$revision) {
                        Yii::error("Revision not found for Quotation ID: {$model->id}", __METHOD__);
                        continue;
                    }

                    $generatePdf = ($projectQmaster->company_group_code === "TKTKM" ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision));
                    $mpdf = $generatePdf;
                    $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
                    $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
                    $mpdf->Output($pdfPath, 'F');

                    unset($mpdf); // Free memory for $mpdf after use
                } catch (\Exception $e) {
                    Yii::error("Error generating PDF for Quotation ID: {$model->id}. Error: " . $e->getMessage(), __METHOD__);
                }
            }
        }
        return "PDF generation completed successfully for $totalRecords records.";
    }

    //Process only 50 records
//    public function actionSecretGenerateQuotationPdfsForAll($recordCount = 50) {
//        ini_set('memory_limit', '1024M'); // Increase memory limit to 1GB
//        set_time_limit(0); // Disable PHP execution time limit
//
//        $uploadDir = Yii::getAlias('@webroot/uploads/quotation-for-client/');
//        if (!is_dir($uploadDir)) {
//            mkdir($uploadDir, 0777, true);
//        }
//
//        // Fetch the specified number of records
//        $models = QuotationPdfMasters::find()->limit($recordCount)->all();
//        foreach ($models as $model) {
//            try {
//                $revision = ProjectQRevisions::findOne($model->revision_id);
//                if (!$revision) {
//                    Yii::error("Revision not found for Quotation ID: {$model->id}", __METHOD__);
//                    continue;
//                }
//
//                $mpdf = $this->generatePdf($model, $revision);
//                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
//                $pdfPath = Yii::getAlias('@webroot/uploads/quotation-for-client/' . $filename . '.pdf');
//                $mpdf->Output($pdfPath, 'F');
//
//                unset($mpdf); // Free memory after generating each PDF
//            } catch (\Exception $e) {
//                Yii::error("Error generating PDF for Quotation ID: {$model->id}. Error: " . $e->getMessage(), __METHOD__);
//            }
//        }
//
//        return "PDF generation completed successfully for $recordCount records.";
// }

    public function actionCreateEmailForm($id, $client_id, $email_type, $clientEmail, $subject, $content) {
        $email_model = QuotationEmails::find()
                ->where(['quotation_id' => $id])
                ->andWhere(['client_id' => $client_id])
                ->andWhere(['recipient' => null])
                ->with('quotationEmailAttachments')
                ->one();

        $quotation = QuotationPdfMasters::findOne(['id' => $id]);

        // automatically attach the quotation pdf
        if (!$email_model) {
            $email_model = new QuotationEmails();
            $email_model->quotation_id = $id;
            $email_model->Cc = Yii::$app->user->identity->email;

            if (!$email_model->save(false)) {
                \common\models\myTools\Mydebug::dumpFileW($email_model->getErrors());
            }
        }
        $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $quotation->quotation_no);
        $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');

        // find if there is an existing quotation pdf file
        $attachment = QuotationEmailAttachments::findOne([
            'file_name' => $filename . '.pdf',
            'email_id' => $email_model->id
        ]);

        if (!$attachment) {
            $attachment = new QuotationEmailAttachments();
            $attachment->email_id = $email_model->id;
        }
        $attachment->file_name = $filename . '.pdf';
//            $attachment->file_content = file_get_contents($pdfPath);

        if (!$attachment->save()) {
            \common\models\myTools\Mydebug::dumpFileW($attachment->getErrors());
        }

        $email_model->client_id = $client_id;
        $email_model->sender = $clientEmail;
        $email_model->subject = $subject;
        $email_model->content = $content;
        $email_model->email_type = $email_type;

        if (!$email_model->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($email_model->getErrors());
        }

        $email_model->Cc = null;

        $emailHistory = QuotationEmails::find()->where([
                    'quotation_id' => $id,
                    'client_id' => $client_id
                ])->all();
        return $this->render('_email_form', [
                    'model' => $this->findModel($quotation->revision_id),
                    'emailHistory' => $emailHistory,
                    'id' => $id,
                    'client_id' => $client_id,
                    'email_model' => $email_model,
                    'revisionId' => $quotation->revision_id,
                    'quotation' => $quotation
        ]);
    }

    private function getComposeLink($sender) {
        $domain = substr(strrchr($sender, "@"), 1);

        switch ($domain) {
            case 'gmail.com':
                return 'https://mail.google.com/mail/u/0/#sent';
            case 'outlook.com':
            case 'hotmail.com':
            case 'live.com':
                return 'https://outlook.live.com/mail/0/sentitems';
            case 'office365.com':
            case 'yourcompany.com': // if using Office 365 tenant
                return 'https://outlook.office.com/mail/sentitems';
            case 'ds.network': // your Roundcube instance
                return 'https://rc.ds.network/?_task=mail&_mbox=Sent';
            default:
                // fallback: your own app "Sent Emails" page
                return \yii\helpers\Url::to(['quotation-emails/sent', 'id' => $email_model->id]);
        }
    }

    public function actionUpdateQuotationEmail($id, $email_type) {
        $email_model = QuotationEmails::find()->where(['id' => $id])->with('quotationEmailAttachments')->one();
        $quotation = QuotationPdfMasters::findOne(['id' => $email_model->quotation_id]);
        $revision_model = ProjectQRevisions::findOne(['id' => $quotation->revision_id]);

        $emailHistory = QuotationEmails::findAll([
            'quotation_id' => $quotation->id
        ]);

        $oldCc = $email_model->Cc;

        if ($email_model->load(Yii::$app->request->post())) {
            if ($email_model->validate()) {
                $postEmail = Yii::$app->request->post('QuotationEmails');

                $email_model->sender = $postEmail['sender'];
                $email_model->recipient = $postEmail['recipient'];
                $email_model->Cc = !empty(trim($postEmail['Cc'])) ? $postEmail['Cc'] : $oldCc;
                $email_model->Bcc = $postEmail['Bcc'];
                $email_model->subject = $postEmail['subject'];
                $email_model->content = $postEmail['content'];
                $email_model->sent_by = Yii::$app->user->identity->id;
                $email_model->sent_at = new \yii\db\Expression('NOW()');
                $CcList = $email_model->Cc ? array_map('trim', explode(',', $email_model->Cc)) : null;
                $BccList = $email_model->Bcc ? array_map('trim', explode(',', $email_model->Bcc)) : null;

                if (!$email_model->save(false)) {
                    \common\models\myTools\Mydebug::dumpFileW($email_model->getErrors());
                }

                $mail = Yii::$app->mailer->compose()
                        ->setFrom($email_model->sender)
                        ->setTo($email_model->recipient)
                        ->setCc($CcList)
                        ->setBcc($BccList)
                        ->setSubject($email_model->subject)
                        ->setTextBody($email_model->content);

                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $quotation->quotation_no);
                $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');

                $mail->attachContent(file_get_contents($pdfPath), [
                    'fileName' => $filename . '.pdf'
                ]);

                $uploadDir = Yii::getAlias('@frontend/uploads/quotation-email/');

                $uploadedFiles = UploadedFile::getInstances($email_model, 'attachments');

                if ($uploadedFiles) {
                    foreach ($uploadedFiles as $file) {
                        $filename = pathinfo($file->baseName, PATHINFO_FILENAME) . '.' . $file->extension;
                        $savePath = $uploadDir . $filename;

                        $file->saveAs($savePath);
                        // avoid duplicate attachments
                        $attachment = QuotationEmailAttachments::findOne([
                            'email_id' => $email_model->id,
                            'file_name' => $file->name
                        ]);
                        if (!$attachment) {
                            $attachment = new QuotationEmailAttachments();
                            $attachment->email_id = $email_model->id;
                        }
//                    $attachment->file_content = file_get_contents($file->tempName);
                        $attachment->file_name = $file->name;

                        if (!$attachment->save()) {
                            \common\models\myTools\Mydebug::dumpFileW($attachment->getErrors());
                        }

                        if (!$email_model->save(false)) {
                            \common\models\myTools\Mydebug::dumpFileW($email_model->getErrors());
                        }

//                    $email_model->refresh();
                        $mail->attachContent(file_get_contents($savePath), [
                            'fileName' => $attachment->file_name
                        ]);
                    }
                }

                $mail->send();

                FlashHandler::success('Email sent!');
//            Yii::$app->session->setFlash('openSentUrl', $url);
                return $this->redirect([
                            'view-project-q-revision',
                            'id' => $quotation->revision_id,
                ]);
            } else {
                Yii::error($email_model->getErrors(), __METHOD__);
                FlashHandler::err('Invalid email addresses');
            }
        } else {
            FlashHandler::err('Failed to send email!');
        }
        return $this->render('_email_form', [
                    'id' => $id,
                    'email_type' => $email_model->email_type,
                    'client_id' => $email_model->client_id,
                    'email_model' => $email_model,
                    'clientEmail' => $email_model->sender,
                    'subject' => $email_model->subject,
                    'content' => $email_model->content,
                    'quotation' => $quotation,
                    'model' => $revision_model,
                    'emailHistory' => $emailHistory
        ]);
    }

    public function actionAjaxDeleteAttachment($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $attachment = QuotationEmailAttachments::findOne(['id' => $id]);

        if (!$attachment) {
            return ['success' => false, 'error' => 'Attachment not found'];
        }

        $attachment->delete();
        return ['success' => true];
    }

    public function actionCheckEmailExists() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $email_address = Yii::$app->request->post('email_address');

        $ch = curl_init();

        // Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, "https://emailreputation.abstractapi.com/v1/?api_key=b0a1b5fe90f34ba1bf5064296db85cdb&email=" . urlencode($email_address));

        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute the request.
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL handle.
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
//                                FlashHandler::err("Failed to contact email validation API (HTTP $httpCode).");
            return ['success' => false, 'error' => ['type' => 'failed_to_contact_email_validation_API']];
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['email_deliverability'])) {
//                                FlashHandler::err("Invalid response from email validation API for {$email_address}");
            return ['success' => false, 'error' => ['type' => 'invalid_response_from_email_validation_API']];
        }

        $isFormatValid = $data['email_deliverability']['is_format_valid'] ?? null;
        $isSmtpValid = $data['email_deliverability']['is_smtp_valid'] ?? null;
        if (!$isFormatValid || !$isSmtpValid) {
//                                FlashHandler::err("The email is either invalid or does not exist in any server: {$email_address}");
            return ['success' => false, 'error' => ['type' => 'invalid_email_address']];
        }

        if (empty($email_address)) {
            return ['success' => false, 'error' => ['type' => 'no_email_address_supplied']];
//            FlashHandler::err('No email provided.');
        }

        return ['success' => true];
    }
}
