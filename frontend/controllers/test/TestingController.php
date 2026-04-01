<?php

namespace frontend\controllers\test;

use Yii;
use frontend\models\test\TestMain;
use frontend\models\test\TestMainSearch;
use frontend\models\test\TestMaster;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\test\TestTemplate;
use common\models\myTools\FlashHandler;
use yii\bootstrap4\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\test\TestFormAttendance;
use frontend\models\test\TestFormInsuhipot;
use frontend\models\test\TestFormDimension;
use frontend\models\test\TestFormVisualpaint;
use frontend\models\test\TestFormComponent;
use frontend\models\test\TestFormAts;
use frontend\models\test\TestFormFunctionality;
use frontend\models\test\TestFormPunchlist;
use frontend\models\test\TestDetailAttendance;
use frontend\models\test\TestDetailFunctionality;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestItemFunctionality;
use frontend\models\test\TestDetailComponent;
use frontend\models\test\RefTestCompType;
use frontend\models\test\TestDetailAts;
use frontend\models\test\RefTestCompFunc;
use frontend\models\test\RefTestAccessory;
use frontend\models\test\RefTestPoints;
use frontend\models\test\RefTestFormList;
use frontend\models\test\VTestMaster;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use Smalot\PdfParser\Parser;

/**
 * TestingController implements the CRUD actions for TestMain model.
 */
class TestingController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/');
    }

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    public function actionIndexProjectLists() {
        $projects = ProjectProductionMaster::find()
                ->orderBy(['id' => SORT_DESC])
                ->asArray()
                ->all();

        return $this->render('indexProjectLists', [
                    'projects' => json_encode($projects),
        ]);
    }

    public function actionIndexTestLists() {
        $searchModel = new \frontend\models\test\VTestMasterSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, null, 'testList');

        return $this->render('indexTestLists', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexProject($id) {
        $project = ProjectProductionMaster::findOne($id);
        $panels = ProjectProductionPanels::find()
                ->select(['project_production_panels.*', 'project_production_master.name as name'])
                ->leftJoin('project_production_master', 'project_production_panels.proj_prod_master = project_production_master.id')
                ->where(['project_production_master.id' => $id])
                ->orderBy(['project_production_master.id' => SORT_DESC, 'project_production_panel_code' => SORT_ASC])
                ->asArray()
                ->with('projProdMaster')
                ->with('testMains')
                ->all();

        return $this->render('indexProject', [
                    'project' => $project,
                    'panels' => json_encode($panels),
                    'fattype' => TestMain::TEST_FAT_TITLE,
                    'itptype' => TestMain::TEST_ITP_TITLE
        ]);
    }

    public function actionIndexTestProgress($id) {
        $project = ProjectProductionMaster::findOne($id);
        $searchModel = new \frontend\models\test\VTestMasterSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $id, 'testProgress');

        return $this->render('indexTestProgress', [
                    'project' => $project,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    public function actionIndexPanel($id) {
        $panel = ProjectProductionPanels::findOne($id);
        $project = $panel->projProdMaster;
        $mains = [];
        foreach (TestMain::TEST_LIST as $type) {
//            $main = TestMain::find()->where(['panel_id' => $id, 'test_type' => $type])->with('testMasters')->one() ?? new TestMain();
//            $main->panel_id = $id;
//            $main->test_type = $type;
//            $main->client = $project->client->company_name;
//            $main->elec_contractor = $main->client;
//            $main->elec_consultant = $main->client;
//            $main->save();
//            $mains[] = $main;
            $main = TestMain::find()->where(['panel_id' => $id, 'test_type' => $type])->with('testMasters')->one();
            if ($main == null) {
                $main = new TestMain();
                $main->panel_id = $id;
                $main->test_type = $type;
                $main->client = $project->client->company_name;
                $main->elec_contractor = $main->client;
                $main->elec_consultant = $main->client;
                $main->save();
            }

            $mains[] = $main;
        }

        return $this->render('indexPanel', [
                    'panel' => $panel,
                    'project' => $project,
                    'mains' => $mains,
        ]);
    }

    public function actionIndexMasterDetail($id) {
        $master = TestMaster::findOne($id);
        $main = $master->testMain;
        $panel = $main->panel;
        $project = $panel->projProdMaster;
        $attendance = $master->testFormAttendance ?? null;
        $punchlist = $master->testFormPunchlist ?? null;
        $insuhipot = $master->testFormInsuhipot ?? null;
        $dimension = $master->testFormDimension ?? null;
        $visualpaint = $master->testFormVisualpaint ?? null;
        $component = $master->testFormComponent ?? null;
        $ats = $master->testFormAts ?? null;
        $functionality = $master->testFormFunctionality ?? null;
        return $this->render('indexMasterDetail', [
                    'master' => $master,
                    'main' => $main,
                    'panel' => $panel,
                    'project' => $project,
                    'attendance' => $attendance,
                    'punchlist' => $punchlist,
                    'insuhipot' => $insuhipot,
                    'dimension' => $dimension,
                    'visualpaint' => $visualpaint,
                    'component' => $component,
                    'ats' => $ats,
                    'functionality' => $functionality,
        ]);
    }

    public function actionUpdateMain($mainId) {
        $main = TestMain::findOne($mainId);
        if ($main->load(Yii::$app->request->post())) {
            if ($main->update(false)) {
                FlashHandler::success("Test detail updated.");
                return $this->redirect(['index-master',
                            'id' => $main->panel_id,
                            'type' => $main->test_type
                ]);
            }
        }
        return $this->renderAjax('_initiateMain', [
                    'model' => $main,
                    'panel' => $main->panel
        ]);
    }

    public function actionInitiateFromPanel($panelid) {
        FlashHandler::success('Panel Testing.');
        return $this->redirect(['index-panel',
                    'id' => $panelid,
        ]);
    }

    public function actionAjaxFormMaster($mainId) {
        $model = TestMain::findOne($mainId);
        $panel = $model->panel;
        $template = $model->test_type == TestMain::TEST_FAT_TITLE ? TestMaster::TEMPLATE_FAT : TestMaster::TEMPLATE_ITP;
        $testMaster = new TestMaster();
        $testMaster->panel_qty = $panel->quantity;
        $testMaster->tested_by = Yii::$app->user->id;
        $testMaster->date = MyFormatter::asDateTime_Read(date('d/m/Y'));
        $testMaster->detail = Html::decode(TestTemplate::find()->where(['formcode' => $template, 'active_sts' => 1])->one()->getAttribute('proctest1'));

        $post = Yii::$app->request->post();
        if (Yii::$app->request->isPost && $testMaster->load($post) && $model->load($post)) {
            $type = $model->test_type == TestMain::TEST_FAT_TITLE ? 'FAT' : 'ITP';
            $num = VTestMaster::find()->where(['test_type' => $type == 'FAT' ? TestMain::TEST_FAT_TITLE : TestMain::TEST_ITP_TITLE, 'proj_id' => $testMaster->testMain->panel->proj_prod_master])->count();
            $numFormatted = str_pad($num, 2, '0', STR_PAD_LEFT);

            $testMaster->tc_ref = "TKTC/$type$numFormatted/" . $panel->projProdMaster->project_production_code;
            $testMaster->test_num = $num + 1;
            $testMaster->status = RefTestStatus::STS_SETUP;
            $testMaster->date = MyFormatter::fromDateRead_toDateSQL($testMaster->date);
            $testTemplate = new TestTemplate();
            $newhtml = $testTemplate->cleanHtmlContent($testMaster->detail);
            $testMaster->detail = preg_replace('/<(\w+)(\s*[^>]*)><\/\1>/', '', $newhtml);

            if ($model->save() && $testMaster->save()) {

                if ($model->test_type == TestMain::TEST_FAT_TITLE) {
                    $att = new TestFormAttendance();
                    $att->addForm($testMaster->id);
                }
                $pnch = new TestFormPunchlist();
                $pnch->addForm($testMaster->id);
                FlashHandler::success('Saved');
                return $this->redirect(['index-master-detail',
                            'id' => $testMaster->id,
                ]);
            } else {
                FlashHandler::err('Error. Please Contact IT Department');
                return $this->redirect(['index-panel',
                            'id' => $panel->proj_prod_master,
                ]);
            }
        }

        return $this->renderAjax('_formMaster', [
                    'userList' => User::getActiveAutocompleteList(),
                    'model' => $model,
                    'testMaster' => $testMaster,
                    'panel' => $panel
        ]);
    }

    public function actionUpdateMaster($id) {
        $post = Yii::$app->request->post();
        $master = TestMaster::findOne($id);
        $main = $master->testMain;
        $panel = $main->panel;

        if (Yii::$app->request->isPost && $master->load($post) && $main->load($post)) {
            $master->date = MyFormatter::fromDateRead_toDateSQL($master->date);
            $testTemplate = new TestTemplate();
            $newhtml = $testTemplate->cleanHtmlContent($master->detail);
            $master->detail = preg_replace('/<(\w+)(\s*[^>]*)><\/\1>/', '', $newhtml);

            if ($master->save() && $main->save()) {
                FlashHandler::success('Test Updated');
                return $this->redirect(['index-master-detail',
                            'id' => $master->id]);
            }
        }

        return $this->renderAjax('_formMaster', [
                    'userList' => User::getActiveAutocompleteList(),
                    'model' => $main,
                    'testMaster' => $master,
                    'panel' => $panel
        ]);
    }

    public function actionAjaxReferMaster($id) {
        $master = TestMaster::findOne($id);
        $main = $master->testMain;
        $model = new TestMaster();

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post('TestMaster');
            $model->test_main_id = $post['testType'];
            $model->date = MyFormatter::fromDateRead_toDateSQL($post['date']);
            $model->test_num = $master->testMain->test_type == $model->testMain->test_type ? $master->test_num + 1 : 1;

            $type = $model->testMain->test_type == TestMain::TEST_FAT_TITLE ? 'FAT' : 'ITP';
            $num = VTestMaster::find()->where(['test_type' => $type == 'FAT' ? TestMain::TEST_FAT_TITLE : TestMain::TEST_ITP_TITLE, 'proj_id' => $main->panel->proj_prod_master])->count();
            $numFormatted = str_pad($num, 2, '0', STR_PAD_LEFT);

            $model->tc_ref = "TKTC/$type$numFormatted/" . $model->testMain->panel->projProdMaster->project_production_code;
            $model->panel_qty = $master->panel_qty;
            $model->detail = $type == 'ITP' ? $master->detail : TestTemplate::find()->where(['formcode' => TestMaster::TEMPLATE_FAT, 'active_sts' => 1])->one()->getAttribute('proctest1');
            $model->status = RefTestStatus::STS_SETUP;
            $model->save();
            return $this->redirect(['refer-master',
                        'oldId' => $id,
                        'newId' => $model->id,
                        'mainId' => $main->id
            ]);
        }

        return $this->renderAjax('_formMainType', [
                    'model' => $model,
                    'main' => $master->testMain,
                    'userList' => User::getActiveAutocompleteList(),
        ]);
    }

    public function actionReferMaster($oldId, $newId, $mainId) {
        $main = TestMain::findOne($mainId);
        $availForm = TestMaster::getSelectedForms($oldId);
        foreach ($availForm as $formcode => $formdata) {
            $formClass = ucfirst($formdata['formclass']);
            if ($formClass == 'TestFormAttendance' || $formClass == 'TestFormPunchlist') {
                continue;
            }
            $class = "frontend\\models\\test\\" . $formClass;
            $class::copyOver($oldId, $newId);

            if ($main->test_type == TestMain::TEST_FAT_TITLE) {
                $att = new TestFormAttendance();
                $att->addForm($newId);
            }
            $pnch = new TestFormPunchlist();
            $pnch->addForm($newId);
            FlashHandler::success('Test created from Referred test');
        }

        return $this->redirect(['index-master-detail', 'id' => $newId]);
    }

    public function actionUpdateStatus($id) {
        $master = TestMaster::findOne($id);
        if ($master->load(Yii::$app->request->post())) {
            $master->update(false);
        }
        FlashHandler::success('Status Updated');
        return $this->redirect(['index-master-detail', 'id' => $master->id]);
    }

    public function actionAddFormToTest($id) {
        $master = TestMaster::findOne($id);
        if (Yii::$app->request->isPost) {
            $sts = true;
            if (!empty(Yii::$app->request->post('TestMaster')['testPlan'])) {
                foreach (Yii::$app->request->post('TestMaster')['testPlan'] as $formData) {
                    list($classname, $code) = explode('|', $formData);
                    $modelname = "frontend\models\\test\\" . ucfirst($classname);
                    $model = new $modelname();
                    $sts = $model->addForm($id);
                    FlashHandler::success('Form/s Added');
                }
            } else {
                FlashHandler::success('No Form Added');
            }
            if ($sts) {
                $master->checkMasterStatus($id);
                return $this->redirect(['index-master-detail', 'id' => $id]);
            }
        }

        $choices = TestMaster::getUnselectedForms($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_addFormToTest', [
                        'master' => $master,
                        'choices' => $choices
            ]);
        }
    }

    public function actionCreate() {
        $model = new TestMain();
        $searchModel = new \frontend\models\projectproduction\VProjectProductionPanelsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('initiateTesting', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStartTest($id) {
        $master = TestMaster::findOne($id);
        $forms = [$master->testFormAttendance, $master->testFormAts, $master->testFormComponent, $master->testFormDimension, $master->testFormFunctionality, $master->testFormInsuhipot, $master->testFormVisualpaint, $master->testFormPunchlist];
        foreach ($forms as $key => $form) {
            if ($key == 0) {
                continue;
            }
            if ($form && $form->status == RefTestStatus::STS_READY_FOR_TESTING) {
                $form->status = RefTestStatus::STS_IN_TESTING;
                $form->update();
            }
//                $form->status = RefTestStatus::STS_READY_FOR_TESTING;
//                $form->update();
        }
        FlashHandler::success('Test Starts');
        return $this->redirect(['index-master-detail',
                    'id' => $id
        ]);
    }

    public function actionGenerateReport($masterId, $mainId) {
        $main = TestMain::findOne($mainId);
        $panel = $main->panel ?? null;
        $project = $panel->projProdMaster ?? null;
        $master = TestMaster::findOne($masterId);

        $coverPdfPath = Yii::getAlias('@webroot/uploads/test-report/cover.pdf');
        $this->savePdf($main, $panel, $project, $master, null, null, null, null, null, "cover", "COVER", $coverPdfPath);
        $programPdfPath = Yii::getAlias('@webroot/uploads/test-report/program.pdf');
        $this->savePdf($main, $panel, $project, $master, null, null, null, null, null, "program", "PROGRAM", $programPdfPath);
        $attendancePdfPath = $this->getAttendanceData($main, $panel, $project, $master);
        $insuhipotPdfPath = $this->getInsuhipotData($main, $panel, $project, $master);
        $dimensionPdfPath = $this->getDimensionData($main, $panel, $project, $master);
        $visualpaintPdfPath = $this->getVisualpaintData($main, $panel, $project, $master);
        $componentPdfPath = $this->getComponentData($main, $panel, $project, $master);
        $atsPdfPath = $this->getAtsData($main, $panel, $project, $master);
        $functionalityPdfPath = $this->getFunctionalityData($main, $panel, $project, $master);
        $punchlistsPdfPath = $this->getPunchlistsData($main, $panel, $project, $master);

        $pdfPaths = array_filter([$coverPdfPath, $programPdfPath, $attendancePdfPath, $insuhipotPdfPath, $dimensionPdfPath, $visualpaintPdfPath, $componentPdfPath, $atsPdfPath, $functionalityPdfPath, $punchlistsPdfPath]);
        $this->mergePDFs(...$pdfPaths);
    }

    private function savePdf($main, $panel, $project, $master, $model1, $model2, $model3, $model4, $model5, $formName, $formDesc, $pdfPath) {
        // Increase memory and execution limits more aggressively
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '1024M'); // Increase to 1GB or higher
        ini_set('pcre.backtrack_limit', 10000000);

//        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new \common\models\myTools\CustomTCPDF();
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(TRUE, 30);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetMargins(20, 80, 20, 30);
        $pdf->SetFont('dejavusans', '', 8.5);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);
        $pdf->SetHeaderMargin(20);
        $pdf->SetFooterMargin(20);
        $pdf->setCellPaddings(0, 0, 0, 15);
        $header = $this->renderPartial('report/headerForms', ['desc' => $formDesc, 'main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master, 'model' => $model1]);
        $pdf->setHeaderData(0, 0, 0, $header, array(), array());
        $pdf->AddPage();

        $pdf->writeHTMLCell(0, 0, 0, 0, $header, 0, 1, 0, true, 'top', true);
        $headerHeight = $pdf->GetY();
        $pdf->AddPage();

        $html = $this->renderPartial("report/$formName", ['main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master, 'model1' => $model1, 'model2' => $model2, 'model3' => $model3, 'model4' => $model4, 'model5' => $model5]);
        $htmlContent = $this->updateHtmlContents($html);

        $pdf->SetY($headerHeight + ($formDesc === "COVER" ? 20 : 0));
        $pdf->WriteHTMLCell(0, 10, 20, $pdf->GetY(), $htmlContent, 0, 1);
        $pdf->Output($pdfPath, 'F');
        $this->removeFirstPage($pdfPath);
    }

    private function removeFirstPage($pdfPath) {
        $pdf = new \setasign\Fpdi\TcpdfFpdi();

        $pageCount = $pdf->setSourceFile($pdfPath);
        for ($i = 2; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, 0, 0, 210);
        }

        $totalPages = $pdf->getNumPages();
        for ($i = 1; $i <= $totalPages; $i++) {
            $pdf->setPage($i);
            $pdf->SetY(-290, true, false);
            $pdf->SetFont('dejavusans', 'I', 8);
            $pageNumText = 'Sheet: ' . $i . ' of ' . $totalPages;
            $pdf->Cell(0, 10, $pageNumText, 0, 1, 'R');
        }

        $pdf->Output($pdfPath, 'F');
    }

    //issue 1: tables are extending beyond the page width, use this on 30/3/2026
    public function updateHtmlContents($html) {
        $fontsize = preg_replace('/font-size:\s*[^;]+;?/', '', $html);
        $lineheight = preg_replace('/line-height:\s*[^;]+;?/', '12px', $fontsize);
        $newhtmlcontent = preg_replace('/font-family:\s*[^;]+;?/', '', $lineheight);
        
        return $newhtmlcontent;
    }
    //solution issue 1 - 2026/01/28, updated on 30/3/2026
//    public function updateHtmlContents($html) {
////        $css = '<style>
////        table {
////            width: 100% !important;
////            table-layout: auto;
////            border-collapse: collapse;
////        }
////        td, th {
////            padding: 1px 2px !important;
////            word-wrap: break-word;
////            overflow-wrap: break-word;
////            hyphens: auto;
////        }
////
////    </style>';
////
////        $html = $css . $html;
//
//        // Remove ALL width constraints
////        $html = preg_replace('/(<table[^>]*)\s+width\s*=\s*["\'][^"\']*["\']/i', '$1', $html);
////        $html = preg_replace('/(<td[^>]*)\s+width\s*=\s*["\'][^"\']*["\']/i', '$1', $html);
////        $html = preg_replace('/(<th[^>]*)\s+width\s*=\s*["\'][^"\']*["\']/i', '$1', $html);
////        $html = preg_replace('/style\s*=\s*["\']([^"\']*?)width:\s*[^;]+;?([^"\']*?)["\']/i', 'style="$1$2"', $html);
//
//        // Remove font specifications 
//        $html = preg_replace('/font-size:\s*[^;]+;?/i', '', $html);        
//        $html = preg_replace('/font-family:\s*[^;]+;?/i', '', $html);
//
//        return $html;
//    }

    private function mergePDFs(...$pdfPaths) {
        $pdf = new \setasign\Fpdi\TcpdfFpdi();

        foreach ($pdfPaths as $pdfPath) {
            $pageCount = $pdf->setSourceFile($pdfPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx, 0, 0, 210);
            }
        }

        $pdf->Output('Testing Report.pdf', 'I');
        // Save merged PDF to file
        $mergedPdfPath = Yii::getAlias('@runtime/merged_report_' . time() . '.pdf');
        $pdf->Output($mergedPdfPath, 'F'); // 'F' saves to file

        return $mergedPdfPath;
    }

    private function getAttendanceData($main, $panel, $project, $master) {
        $attendance = TestFormAttendance::findOne(['test_master_id' => $master->id]) ?? null;
        $attendanceList = $attendance !== null ? TestDetailAttendance::find()->where(['form_attendance_id' => $attendance->id])->orderBy(['name' => SORT_ASC])->all() : [];
        $attendancePdfPath = null;

        if ($attendanceList !== null) {
            $attendancePdfPath = Yii::getAlias('@webroot/uploads/test-report/attendance.pdf');
            $this->savePdf($main, $panel, $project, $master, $attendanceList, null, null, null, null, "attendance", "ATTENDANCE LIST", $attendancePdfPath);
        }

        return $attendancePdfPath;
    }

    private function getInsuhipotData($main, $panel, $project, $master) {
        $insuhipot = TestFormInsuhipot::findOne(['test_master_id' => $master->id]) ?? null;
        $insuhipotProcedures = $insuhipot !== null ? $insuhipot->customProcedures($insuhipot, true, true) : [];
        $insuhipotPdfPath = null;

        if ($insuhipot !== null) {
            $insuhipotPdfPath = Yii::getAlias('@webroot/uploads/test-report/insuhipot.pdf');
            $this->savePdf($main, $panel, $project, $master, $insuhipot, $insuhipotProcedures, null, null, null, "insuhipot", "INSULATION AND HIPOT TEST PROCEDURE", $insuhipotPdfPath);
        }
        return $insuhipotPdfPath;
    }

    private function getDimensionData($main, $panel, $project, $master) {
        $dimension = TestFormDimension::findOne(['test_master_id' => $master->id]) ?? null;
        $dimensionProcedures = $dimension !== null ? $dimension->customProcedures($dimension, true, true) : [];
        $dimensionList = $dimension !== null ? $dimension->testDetailDimensions : [];
        $dimensionPdfPath = null;
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        if ($dimension !== null) {
            if ($dimension->got_custom_content == 1) {
                $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_DIMENSION, $dimension->id);
            }
            $dimensionPdfPath = Yii::getAlias('@webroot/uploads/test-report/dimension.pdf');
            $this->savePdf($main, $panel, $project, $master, $dimension, $dimensionProcedures, $dimensionList, $customContents, null, "dimension", "DIMENSION CHECK", $dimensionPdfPath);
        }
        return $dimensionPdfPath;
    }

    private function getVisualpaintData($main, $panel, $project, $master) {
        $visualpaint = TestFormVisualpaint::findOne(['test_master_id' => $master->id]) ?? null;
        $visualpaintProcedures = $visualpaint !== null ? $visualpaint->customProcedures($visualpaint, true, true) : [];
        $visualpaintPdfPath = null;

        if ($visualpaint !== null) {
            $visualpaintPdfPath = Yii::getAlias('@webroot/uploads/test-report/visualpaint.pdf');
            $this->savePdf($main, $panel, $project, $master, $visualpaint, $visualpaintProcedures, null, null, null, "visualpaint", "VISUAL INPECTIONS AND PAINTING WORK CHECK", $visualpaintPdfPath);
        }
        return $visualpaintPdfPath;
    }

    private function getComponentData($main, $panel, $project, $master) {
        $component = TestFormComponent::findOne(['test_master_id' => $master->id]) ?? null;
        $detailDatas = [];
        $detailsComp = $component !== null ? TestDetailComponent::find()->where(['form_component_id' => $component->id])->all() : [];
        foreach ($detailsComp as $detail) {
            $detailDatas[$detail->id]['form'] = $detail;
            $detailDatas[$detail->id]['form'][TestDetailComponent::ATTRIBUTE_ACCESSORY] = explode(", ", $detailDatas[$detail->id]['form'][TestDetailComponent::ATTRIBUTE_ACCESSORY]);
            $attributeToRender = TestDetailComponent::getVisibilitySettingsForType($detail->compType->code);

            $detailDatas[$detail->id]['attributetorender'] = $attributeToRender;
            if ($detail->compType->code == RefTestCompType::TYPE_OTHER) {
                $detailDatas[$detail->id]['otheritem'] = $detail->testItemCompOthers ?? new TestItemCompOther();
            }
        }
        $conformities = $component !== null ? $component->testDetailConforms ?? null : null;
        $funcList = RefTestCompFunc::getDropDownList();
        $accesList = RefTestAccessory::getDropDownList();
        $componentPdfPath = null;

        if ($component !== null && $detailsComp !== null) {
            $componentPdfPath = Yii::getAlias('@webroot/uploads/test-report/component.pdf');
            $this->savePdf($main, $panel, $project, $master, $detailDatas, $conformities, $funcList, $accesList, $component, "component", "COMPONENT CHECK", $componentPdfPath);
        }
        return $componentPdfPath;
    }

    private function getAtsData($main, $panel, $project, $master) {
        $ats = TestFormAts::findOne(['test_master_id' => $master->id]) ?? null;
        $detailAcots = $ats !== null ? TestDetailAts::find()->where(['form_ats_id' => $ats->id, 'form_type' => TestFormAts::FORM_TYPE_ACOT])->all() : [];
        $detailMcots = $ats !== null ? TestDetailAts::find()->where(['form_ats_id' => $ats->id, 'form_type' => TestFormAts::FORM_TYPE_MCOT])->all() : [];
        $detailCbvcs = $ats !== null ? TestDetailAts::find()->where(['form_ats_id' => $ats->id, 'form_type' => TestFormAts::FORM_TYPE_CBVC])->all() : [];
        $atsPdfPath = null;
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        if ($ats !== null && $detailAcots !== null && $detailCbvcs !== null && $detailMcots !== null) {
            if ($ats->got_custom_content == 1) {
                $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_ATS, $ats->id);
            }
            $atsPdfPath = Yii::getAlias('@webroot/uploads/test-report/ats.pdf');
            $this->savePdf($main, $panel, $project, $master, $ats, $detailAcots, $detailMcots, $detailCbvcs, $customContents, "ats", "ATS FUNCTIONAL TEST", $atsPdfPath);
        }
        return $atsPdfPath;
    }

    private function getFunctionalityData($main, $panel, $project, $master) {
        $functionality = TestFormFunctionality::findOne(['test_master_id' => $master->id]) ?? null;
        $detailsFunc = $functionality !== null ? $functionality->testDetailFunctionalities : [];
        $functionalities = [];
        foreach ($detailsFunc as $detail) {
            $functionalities[$detail->id] = [
                'detail' => $detail,
                'items' => TestItemFunctionality::find()->where(['detail_functionality_id' => $detail->id])->orderBy(['order' => SORT_ASC])->all()
            ];
        }
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        $functionalityPdfPath = null;
        if ($functionality !== null) {
            if ($functionality->got_custom_content == 1) {
                $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_FUNCTIONALITY, $functionality->id);
            }
            $functionalityPdfPath = Yii::getAlias('@webroot/uploads/test-report/functionality.pdf');
            $this->savePdf($main, $panel, $project, $master, $functionalities, $customContents, $functionality, null, null, "functionality", "FUNCTIONALITY TEST", $functionalityPdfPath);
        }
        return $functionalityPdfPath;
    }

    private function getPunchlistsData($main, $panel, $project, $master) {
        $punchlistsForm = TestFormPunchlist::findOne(['test_master_id' => $master->id]) ?? null;
        $punchlists = $punchlistsForm !== null ? \frontend\models\test\TestDetailPunchlist::findAll(['form_punchlist_id' => $punchlistsForm->id]) : [];
        $punchlistsPdfPath = null;
        if ($punchlistsForm !== null) {
            $punchlistsPdfPath = Yii::getAlias('@webroot/uploads/test-report/punchlists.pdf');
            $this->savePdf($main, $panel, $project, $master, $punchlists, null, null, null, null, "editPunchlist", "PUNCH LIST", $punchlistsPdfPath);
        }
        return $punchlistsPdfPath;
    }
}
