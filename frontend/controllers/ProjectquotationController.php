<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectquotation\ProjectQMasters;
use frontend\models\projectquotation\ProjectQMastersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;
use frontend\models\projectquotation\ProjectQClients;
use common\models\myTools\FlashHandler;
use frontend\models\projectquotation\ProjectQTypes;
use yii\web\Response;
use frontend\models\projectquotation\QuotationPdfMasters;

/**
 * ProjectquotationController implements the CRUD actions for ProjectQMasters model.
 */
class ProjectquotationController extends Controller {

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

    public function actionDirectorPendingApproval() {
        $searchModel = new \frontend\models\projectquotation\QuotationPdfMastersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pending');

//        if (Yii::$app->request->post()) {
//            $postData = Yii::$app->request->post();
//
//            FlashHandler::success("Created");
//            return $this->redirect(['view-projectquotation', 'id' => $model->id]);
//        }

        return $this->render('directorPendingApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDirectorAllApproval() {
        $searchModel = new \frontend\models\projectquotation\QuotationPdfMastersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'all');

        return $this->render('directorAllApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDirectorApproveOneQuotation($id) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = QuotationPdfMasters::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('Quotation not found.');
            }

            $model->md_approval_status = QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED;
            $model->md_approval_date = new \yii\db\Expression('NOW()');
            $model->md_user_id = Yii::$app->user->identity->id;

            if (!$model->save()) {
                throw new \Exception('Failed to update quotation: ' . implode(', ', $model->getFirstErrors()));
            }

            $revision = \frontend\models\projectquotation\ProjectQRevisions::findOne($model->revision_id);
            $projectQmaster = $revision->projectQType->project;
            $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
            $model->q_date = date("d/m/Y", strtotime($model->q_date));
            $generatePdf = ($projectQmaster->company_group_code === "TKTKM" ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision));
            $mpdf = $generatePdf;

            $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
            $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
            $mpdf->Output($pdfPath, 'F');

            $model->processAndSave();
            $transaction->commit();

            FlashHandler::success("Quotation approved successfully.");
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed to approve quotation: " . $e->getMessage());
        }

        return $this->redirect(['director-pending-approval']);
    }

    //approve selected
    public function actionDirectorApproveQuotation() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $selectAll = $request->post('selectAll', false);

            if ($selectAll) {
                // --- Select All Mode ---
                $excludedIds = $request->post('excludedIds', []);

                // FIXED: Only select quotations pending director approval
                $query = QuotationPdfMasters::find()
                        ->where(['md_approval_status' => QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL]);

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

                foreach ($models as $model) {
                    try {
                        $model->md_approval_status = QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED;
                        $model->md_approval_date = new \yii\db\Expression('NOW()');
                        $model->md_user_id = Yii::$app->user->identity->id;

                        if (!$model->save()) {
                            throw new \Exception('Failed to update quotation ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                        }

                        // FIXED: Wrap PDF generation in try-catch to prevent one failure from breaking all
                        $revision = \frontend\models\projectquotation\ProjectQRevisions::findOne($model->revision_id);
                        if ($revision && $revision->projectQType && $revision->projectQType->project) {
                            try {
                                $projectQmaster = $revision->projectQType->project;
                                $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
                                $model->q_date = date("d/m/Y", strtotime($model->q_date));

                                $generatePdf = ($projectQmaster->company_group_code === "TKTKM") ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision);

                                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
                                $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
                                $generatePdf->Output($pdfPath, 'F');

                                $model->processAndSave();
                            } catch (\Exception $pdfError) {
                                \Yii::warning("PDF generation failed for quotation ID {$model->id}: " . $pdfError->getMessage());
                                $errors[] = "PDF generation failed for {$model->quotation_no}";
                                // Continue with next quotation instead of failing entire batch
                            }
                        }

                        $count++;
                    } catch (\Exception $e) {
                        \Yii::error("Failed to approve quotation ID {$model->id}: " . $e->getMessage());
                        $errors[] = "Failed to approve {$model->quotation_no}: " . $e->getMessage();
                        // Continue with next quotation
                    }
                }

                $transaction->commit();

                $message = "$count quotation(s) approved successfully.";
                if (!empty($errors)) {
                    $message .= " However, there were some issues: " . implode("; ", array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $message .= " (and " . (count($errors) - 3) . " more)";
                    }
                }

                FlashHandler::success($message);
                return ['success' => true, 'message' => $message, 'count' => $count, 'errors' => $errors];
            } else {
                // --- Normal Mode (selected IDs only) ---
                $ids = $request->post('ids', []);

                if (empty($ids)) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'No quotations selected.'];
                }

                $count = 0;
                $errors = [];

                foreach ($ids as $id) {
                    try {
                        $model = QuotationPdfMasters::findOne($id);
                        if (!$model) {
                            throw new NotFoundHttpException('Quotation not found.');
                        }

                        $model->md_approval_status = QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED;
                        $model->md_approval_date = new \yii\db\Expression('NOW()');
                        $model->md_user_id = Yii::$app->user->identity->id;

                        if (!$model->save()) {
                            throw new \Exception('Failed to update quotation ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                        }

                        // FIXED: Wrap PDF generation in try-catch
                        $revision = \frontend\models\projectquotation\ProjectQRevisions::findOne($model->revision_id);
                        if ($revision && $revision->projectQType && $revision->projectQType->project) {
                            try {
                                $projectQmaster = $revision->projectQType->project;
                                $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
                                $model->q_date = date("d/m/Y", strtotime($model->q_date));

                                $generatePdf = ($projectQmaster->company_group_code === "TKTKM") ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision);

                                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
                                $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
                                $generatePdf->Output($pdfPath, 'F');

                                $model->processAndSave();
                            } catch (\Exception $pdfError) {
                                \Yii::warning("PDF generation failed for quotation ID {$model->id}: " . $pdfError->getMessage());
                                $errors[] = "PDF generation failed for {$model->quotation_no}";
                            }
                        }

                        $count++;
                    } catch (NotFoundHttpException $e) {
                        \Yii::error("Quotation not found - ID: {$id}");
                        $errors[] = "Quotation ID {$id} not found";
                    } catch (\Exception $e) {
                        \Yii::error("Failed to approve quotation ID {$id}: " . $e->getMessage());
                        $errors[] = "Failed to approve ID {$id}: " . $e->getMessage();
                    }
                }

                $transaction->commit();

                $message = "$count quotation(s) approved successfully.";
                if (!empty($errors)) {
                    $message .= " However, there were some issues: " . implode("; ", $errors);
                }

                FlashHandler::success($message);
                return ['success' => true, 'message' => $message, 'count' => $count, 'errors' => $errors];
            }
        } catch (NotFoundHttpException $e) {
            $transaction->rollBack();
            \Yii::error("NotFoundHttpException in actionDirectorApproveQuotation: " . $e->getMessage());
            FlashHandler::err("Failed to approve: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::error("Exception in actionDirectorApproveQuotation: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            FlashHandler::err("Failed to approve: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve: ' . $e->getMessage()];
        }
    }

//    public function actionDirectorApproveQuotation() {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//
//            $ids = Yii::$app->request->post('ids', []);
//
//            foreach ($ids as $id) {
//                $model = QuotationPdfMasters::findOne($id);
//                if (!$model) {
//                    throw new NotFoundHttpException('Quotation not found.');
//                }
//
//                $model->md_approval_status = QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED;
//                $model->md_approval_date = new \yii\db\Expression('NOW()');
//                $model->md_user_id = Yii::$app->user->identity->id;
//
//                if (!$model->save()) {
//                    throw new \Exception('Failed to update quotation: ' . implode(', ', $model->getFirstErrors()));
//                }
//
//                $revision = \frontend\models\projectquotation\ProjectQRevisions::findOne($model->revision_id);
//                $projectQmaster = $revision->projectQType->project;
//                $model->q_delivery_ship_mode = trim($model->q_delivery_ship_mode) == "" ? null : $model->q_delivery_ship_mode;
//                $model->q_date = date("d/m/Y", strtotime($model->q_date));
//                $generatePdf = ($projectQmaster->company_group_code === "TKTKM" ? $this->generateConsolidatedPdf($model, $revision) : $this->generatePdf($model, $revision));
//                $mpdf = $generatePdf;
//
//                $filename = preg_replace("/[^a-zA-Z0-9.]/", "-", $model->quotation_no);
//                $pdfPath = Yii::getAlias(Yii::$app->params['quotation_pdf_path'] . $filename . '.pdf');
//                $mpdf->Output($pdfPath, 'F');
//
//                $model->processAndSave();
//            }
//            $transaction->commit();
//
//            FlashHandler::success("Quotation approved successfully.");
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            FlashHandler::err("Failed to approve quotation: " . $e->getMessage());
//        }
//
//        return $this->redirect(['director-pending-approval']);
//    }

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

        $htmlHeader = $this->renderPartial("/projectqrevision/_quotationPdfHeader", ['revision' => $revision]);
        $htmlFooter = $this->renderPartial("/projectqrevision/_quotationPdfFooter", ['model' => $model]);
        $htmlBody = $this->renderPartial("/projectqrevision/_quotationPdf", [
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
                'header' => '/projectqrevision/_quotationPdfHeaderCon',
                'footer' => '/projectqrevision/_quotationPdfFooter',
                'body' => '/projectqrevision/_quotationPdfCon',
            ],
            [
                'header' => '/projectqrevision/_quotationPdfHeaderTK',
                'footer' => '/projectqrevision/_quotationPdfFooter',
                'body' => '/projectqrevision/_quotationPdfTK',
            ],
            [
                'header' => '/projectqrevision/_quotationPdfHeaderTKM',
                'footer' => '/projectqrevision/_quotationPdfFooter',
                'body' => '/projectqrevision/_quotationPdfTKM',
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

    /**
     * Lists all ProjectQMasters models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProjectQMastersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexProjectquotation', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Get all filtered IDs (for select all across pages)
     */
    public function actionGetAllFilteredIds() {
        try {
            $request = Yii::$app->request;
            $filters = $request->post('filters', []);

            \Yii::info('Getting all filtered IDs', 'export');

            // Create search model
            $searchModel = new \frontend\models\projectquotation\ProjectQMastersSearch();

            // Convert filters to proper format
            $searchParams = [];
            foreach ($filters as $key => $value) {
                if ($value !== '') {
                    if (strpos($key, 'ProjectQMastersSearch[') !== false) {
                        $attribute = str_replace(['ProjectQMastersSearch[', ']'], '', $key);
                        $searchParams[$attribute] = $value;
                    }
                }
            }

            // Load search parameters
            $searchModel->load($searchParams, '');

            // Get data provider WITHOUT pagination
            $dataProvider = $searchModel->search([]);
            $dataProvider->pagination = false; // Disable pagination to get all records

            $query = $dataProvider->query;

            // Get only IDs
            $ids = $query->select('p.id')->column();

            \Yii::info('Found ' . count($ids) . ' filtered IDs', 'export');

            return $this->asJson([
                        'success' => true,
                        'count' => count($ids),
                        'ids' => $ids
            ]);
        } catch (\Exception $e) {
            \Yii::error('Error getting filtered IDs: ' . $e->getMessage(), 'export');

            return $this->asJson([
                        'success' => false,
                        'message' => 'Failed to get filtered items: ' . $e->getMessage()
            ]);
        }
    }

    //before startdate and enddate filter
//    public function actionExportQuotationsExcel() {
//        try {
//            $request = Yii::$app->request;
//
//            // CHANGED: Decode JSON string to array
//            $idsJson = $request->post('ids', '[]');
//            $ids = json_decode($idsJson, true);
//
//            // Validate that it's an array
//            if (!is_array($ids)) {
//                $ids = [];
//            }
//
//            // Convert to integers for security
//            $ids = array_map('intval', array_filter($ids));
//
//            $selectAll = $request->post('selectAll', false);
//
//            \Yii::info('Export request - SelectAll mode: ' . ($selectAll ? 'true' : 'false'), 'export');
//            \Yii::info('IDs count: ' . count($ids), 'export');
//
//            if (empty($ids)) {
//                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//                Yii::$app->response->statusCode = 400;
//                return ['success' => false, 'message' => 'No quotations selected.'];
//            }
//
//            // Direct query with the selected IDs
//            $models = \frontend\models\projectquotation\VProjectQuotationMaster::find()
//                    ->alias('p')
//                    ->select([
//                        'p.*',
//                        'total_amount' => 'COALESCE(SUM(r.amount), 0)',
//                        'currency_sign' => 'MAX(c.currency_sign)'
//                    ])
//                    ->leftJoin('project_q_types qt', 'qt.project_id = p.id')
//                    ->leftJoin(
//                            'project_q_revisions r',
//                            'r.id = qt.active_revision_id AND r.is_active = 0'
//                    )
//                    ->leftJoin('ref_currencies c', 'c.currency_id = r.currency_id')
//                    ->where(['p.active' => true])
//                    ->andWhere(['p.id' => $ids])
//                    ->groupBy('p.id')
//                    ->orderBy(['p.id' => SORT_DESC])
//                    ->all();
//
//            \Yii::info('Found ' . count($models) . ' models to export', 'export');
//
//            if (empty($models)) {
//                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//                Yii::$app->response->statusCode = 404;
//                return ['success' => false, 'message' => 'No quotations found.'];
//            }
//
//            // Set proper headers for Excel download
//            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
//            Yii::$app->response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
//
//            $filename = 'Project_Quotations_' . date('Ymd_His') . '.xls';
//
//            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
//            Yii::$app->response->headers->set('Pragma', 'no-cache');
//            Yii::$app->response->headers->set('Expires', '0');
//
//            return $this->renderPartial('_projectquotationCSV', [
//                        'models' => $models,
//            ]);
//        } catch (\Exception $e) {
//            \Yii::error('Export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), 'export');
//
//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//            Yii::$app->response->statusCode = 500;
//            return ['success' => false, 'message' => 'Export failed: ' . $e->getMessage()];
//        }
//        
//        return $this->renderAjax('_formProjectQuotationCSV', [
//                    'model' => $models,
//        ]);
//    }
    // Action to show the modal form
    public function actionAjaxExportSelectedWithDates() {
        $model = new \yii\base\DynamicModel(['startDate', 'endDate']);
        $model->addRule(['startDate', 'endDate'], 'required');

        return $this->renderAjax('_formProjectQuotationCSV', [
                    'model' => $model,
        ]);
    }

// Update the existing export action to handle date filtering
    public function actionExportQuotationsExcel() {
        try {
            $request = Yii::$app->request;
            $idsJson = $request->post('ids', '[]');
            $ids = json_decode($idsJson, true);

            // Get date parameters
            $startDate = $request->post('startDate');
            $endDate = $request->post('endDate');

            if (!is_array($ids)) {
                $ids = [];
            }

            $ids = array_map('intval', array_filter($ids));

            \Yii::info('Export request - IDs count: ' . count($ids), 'export');

            if (empty($ids)) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->statusCode = 400;
                return ['success' => false, 'message' => 'No quotations selected.'];
            }

            // Build query
            $query = \frontend\models\projectquotation\VProjectQuotationMaster::find()
                    ->alias('p')
                    ->select([
                        'p.*',
                        'total_amount' => 'COALESCE(SUM(r.amount), 0)',
                        'currency_sign' => 'MAX(c.currency_sign)'
                    ])
                    ->leftJoin('project_q_types qt', 'qt.project_id = p.id')
                    ->leftJoin(
                            'project_q_revisions r',
                            'r.id = qt.active_revision_id AND r.is_active = 0'
                    )
                    ->leftJoin('ref_currencies c', 'c.currency_id = r.currency_id')
                    ->where(['p.active' => true])
                    ->andWhere(['p.id' => $ids]);

            // Add date filter if provided
            if (!empty($startDate) && !empty($endDate)) {
                $startDateFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $startDate)));
                $endDateFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $endDate)));

                $query->andWhere(['>=', 'DATE(p.created_at)', $startDateFormatted])
                        ->andWhere(['<=', 'DATE(p.created_at)', $endDateFormatted]);

                \Yii::info('Date filter applied: ' . $startDate . ' to ' . $endDate, 'export');
            }

            $models = $query->groupBy('p.id')
                    ->orderBy(['p.id' => SORT_DESC])
                    ->all();

            \Yii::info('Found ' . count($models) . ' models to export', 'export');

            if (empty($models)) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->statusCode = 404;
                return ['success' => false, 'message' => 'No quotations found matching the criteria.'];
            }

            // Set headers for Excel download
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');

            $filename = 'Project_Quotations_';
            if (!empty($startDate) && !empty($endDate)) {
                $filename .= str_replace('/', '', $startDate) . '_to_' . str_replace('/', '', $endDate);
            } else {
                $filename .= date('Ymd_His');
            }
            $filename .= '.xls';

            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            Yii::$app->response->headers->set('Pragma', 'no-cache');
            Yii::$app->response->headers->set('Expires', '0');

            return $this->renderPartial('_projectquotationCSV', [
                        'models' => $models,
                        'startDate' => $startDate ?? null,
                        'endDate' => $endDate ?? null,
            ]);
        } catch (\Exception $e) {
            \Yii::error('Export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), 'export');
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 500;
            return ['success' => false, 'message' => 'Export failed: ' . $e->getMessage()];
        }
    }

    /**
     * Displays a single ProjectQMasters model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewProjectquotation($id) {
        $model = $this->findModel($id);
        $model->checkAndCreateQTypes();

        return $this->render('viewProjectquotation', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProjectQMasters model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateProjectquotation() {
        $model = new ProjectQMasters();

        if ($model->load(Yii::$app->request->post()) && $model->proccessAndSave()) {
            FlashHandler::success("Created");
            return $this->redirect(['view-projectquotation', 'id' => $model->id]);
        }

//        $tempQuotationNo = ProjectQMasters::getNextQuotationNumber();
        $model->quotation_no = ProjectQMasters::getNextQuotationNumber();
        $companyGroupList = \frontend\models\common\RefCompanyGroupList::getDropDownList();
        return $this->render('createProjectquotation', [
                    'model' => $model,
                    'companyGroupList' => $companyGroupList,
//            tempQuotationNo=>$tempQuotationNo
        ]);
    }

    /**
     * By Khetty 24/11/2023
     * Check for similar project names.
     * 
     * @return array JSON response indicating success or failure along with similar projects, if found.
     */
    public function actionCheckSimilarProject() {
        $model = new ProjectQMasters();

        $project_name = Yii::$app->request->post('projectName');
        $similarProjects = $model->findSimilarProjects($project_name);

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($similarProjects !== null) {
            return [
                'success' => true,
                'similarProjects' => $similarProjects,
            ];
        }

        return [
            'success' => false,
        ];
    }

    /**
     * Updates an existing ProjectQMasters model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateProjectquotation($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->proccessAndSave()) {
            FlashHandler::success("Updated");
            return $this->redirect(['view-projectquotation', 'id' => $model->id]);
        }

        $companyGroupList = \frontend\models\common\RefCompanyGroupList::getDropDownList();

        return $this->render('updateProjectquotation', [
                    'model' => $model,
                    'companyGroupList' => $companyGroupList
        ]);
    }

    /**
     * Deletes an existing ProjectQMasters model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
//        $this->findModel($id)->delete();
        if ($this->findModel($id)->setToInactive()) {
            FlashHandler::success("Project removed.");
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectQMasters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectQMasters the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectQMasters::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddClientAjax() {
        if (Yii::$app->request->isPost) {
            $clientId = Yii::$app->request->post('clientId');
            $projectQId = Yii::$app->request->post('projectQuotationId');
            $model = new ProjectQClients();
            if ($model->processNewClients($clientId, $projectQId)) {
                return json_encode(["success" => true, "msg" => "Client added", "projQClientId" => $model->id]);
            } else {
                return json_encode(["success" => false, "msg" => "Unable to add.\nClient already on the list"]);
            }
        } else {
            return json_encode(["success" => false, "msg" => "Fail to add client"]);
        }
    }

    public function actionRemoveClientAjax() {
        if (Yii::$app->request->isPost) {
            $projQClientId = Yii::$app->request->post('projQClientId');
            $projectQuotationId = Yii::$app->request->post('projectQuotationId');
            $model = ProjectQClients::find()->where(['project_q_master_id' => $projectQuotationId, 'id' => $projQClientId])->one();

            if ($model->delete()) {
                return json_encode(["success" => true, "msg" => "Client removed"]);
            }
        } else {
            return json_encode(["success" => false, "msg" => "Fail to remove client"]);
        }
    }

    public function actionGetAutocompleteProjectQuotationList($term = '') {
        $data = ProjectQMasters::find()
                ->select([
                    'quotation_no',
                    'project_q_masters.id as id',
                    'project_q_revisions.id as revisionId',
                    'quotation_no as value',
                    'CONCAT(quotation_no," - ", project_name) as label',
                    'project_q_masters.project_name',
                    'ref_currencies.currency_sign',
                    'project_q_types.type',
                    'project_q_revisions.revision_description',
                    'project_q_revisions.amount',
                    'ref_project_q_types.project_type_name',
                    'clients.company_name',
                    'project_q_clients.client_id',
                ])
                ->innerJoin("project_q_types", "project_q_types.project_id=project_q_masters.id AND project_q_types.is_finalized=1")
                ->join("INNER JOIN", "ref_project_q_types", "ref_project_q_types.code=project_q_types.type")
                ->join("INNER JOIN", "project_q_revisions", 'project_q_types.active_revision_id = project_q_revisions.id')
                ->join("INNER JOIN", "ref_currencies", 'ref_currencies.currency_id = project_q_revisions.currency_id')
                ->innerJoin("project_q_clients", "project_q_clients.id = project_q_types.active_client_id")
                ->innerJoin("clients", "project_q_clients.client_id = clients.id")
                ->where('quotation_no LIKE "%' . addslashes($term) . '%" OR project_name LIKE "%' . addslashes($term) . '%"')
                ->andWhere(['project_q_masters.active' => '1'])
                ->asArray()
                ->all();
        return \yii\helpers\Json::encode($data);
    }
}
