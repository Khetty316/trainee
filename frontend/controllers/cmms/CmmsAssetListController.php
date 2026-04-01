<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\CmmsAssetListSearch;
use frontend\models\cmms\CmmsPartList;
use frontend\models\cmms\VwCmmsAssetList;
use frontend\models\cmms\CmmsAssetFaults;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use yii\filters\AccessControl;
use common\modules\auth\models\AuthItem;

/**
 * CmmsAssetListController implements the CRUD actions for CmmsAssetList model.
 */
class CmmsAssetListController extends Controller {

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
                'only' => ['download-asset-template'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['download-asset-template'],
                        'roles' => [AuthItem::ROLE_CMMS_Superior],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CmmsAssetList models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CmmsAssetListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
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
     * Displays a single CmmsAssetList model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $vModel = VwCmmsAssetList::find()
//            ->where(['not', ['machine_detail_id' => null]])
                ->where(['id' => $id])
                ->all();

        $faults = CmmsAssetFaults::find()
                ->where(['asset_list_id' => $id])
                ->indexBy('id')
                ->all();
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'faults' => $faults,
                    'vModel' => $vModel
        ]);
    }

    /**
     * Creates a new CmmsAssetList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CmmsAssetList();
        $vModel = new VwCmmsAssetList();
        $faults = [new CmmsAssetFaults()];

//        $part = new CmmsPartList();
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (\Yii::$app->request->isPost) {

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $postData = Yii::$app->request->post('CmmsAssetList');
                //retrieve asset_code and name
                $assetCode = $postData['asset_id'] ?? null;
                $assetName = $postData['name'] ?? null;

                $existingAsset = null;

                if ($assetCode && $assetName) {
                    // Check if it already exists
                    $existingAsset = CmmsAssetList::find()
                            ->where([
                                'asset_id' => $assetCode,
                                'name' => $assetName,
                                'is_deleted' => 0
                            ])
                            ->one();
                }

                if ($existingAsset) {
                    // use existing asset
                    $assetId = $existingAsset->id;
                    $model = $existingAsset;
                } else {
                    if (!$model->load(Yii::$app->request->post())) {
                        throw new \Exception('Failed to load asset data');
                    }
                    $model->is_deleted = 0;
                    $model->active_sts = 1;
                    $model->updated_by = Yii::$app->user->identity->id;

                    if (!$model->save()) {
                        throw new \Exception("Failed to save fault list.");
                    }
                    $assetId = $model->id;
                }

                $postFaults = \Yii::$app->request->post('CmmsAssetFaults', []);
                foreach ($postFaults as $index => $postF) {
                    if (!empty($postF['id'])) {
                        $pF = CmmsAssetFaults::findOne($postF['id']);
                        if (!$pF) {
                            throw new \Exception('Asset fault not found');
                        }
                    } else {
                        $pF = new CmmsAssetFaults();
                    }

                    $pF->setAttributes($postF);
//                        $fLD->cmms_asset_list_id = $assetId;
//                    $pF->asset_id = $model->id;
                    $pF->is_deleted = 0;
                    $pF->updated_by = \Yii::$app->user->identity->id;
                    $pF->asset_id = $model->asset_id;
                    $pF->active_sts = 1;
                    $pF->asset_list_id = $model->id;

                    if (!$pF->save()) {
                        throw new \Exception("Failed to save fault.");
                    }
                }

                $transaction->commit();
                FlashHandler::success('Asset details saved!');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }

        return $this->render('create', [
                    'model' => $model,
                    'vModel' => $vModel,
                    'faults' => $faults,
                    'isUpdate' => false,
        ]);
    }

    private function applyListValidationRange(Worksheet $sheet, string $range, string $formula1): void {
        $dv = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $dv->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $dv->setAllowBlank(true);
        $dv->setShowDropDown(true);

        // allow typing new values ("datalist-like")
        $dv->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $dv->setShowErrorMessage(false);

        $dv->setFormula1($formula1);

        foreach ($sheet->getCellCollection()->getCoordinates() as $coord) {
            // ignore; not used
        }

        // Apply to every cell in the range
        foreach (\PhpOffice\PhpSpreadsheet\Cell\Coordinate::extractAllCellReferencesInRange($range) as $cellRef) {
            $sheet->getCell($cellRef)->setDataValidation(clone $dv);
        }
    }

    private function applyListValidationColumn(Worksheet $sheet, string $col, int $startRow, int $endRow, string $formula1): void {
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST);
        $dv->setAllowBlank(true);
        $dv->setShowDropDown(true);
        $dv->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dv->setShowErrorMessage(false);
        $dv->setFormula1($formula1);

        for ($r = $startRow; $r <= $endRow; $r++) {
            $sheet->getCell("{$col}{$r}")->setDataValidation(clone $dv);
        }
    }

    private function applyListValidationCell(Worksheet $sheet, string $cell, string $formula1): void {
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST);
        $dv->setAllowBlank(true);
        $dv->setShowDropDown(true);
        $dv->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dv->setShowErrorMessage(false);
        $dv->setFormula1($formula1);

        $sheet->getCell($cell)->setDataValidation($dv);
    }

    public function actionDownloadAssetTemplate() {
        $templatePath = Yii::getAlias('@webroot/template/template-cmms-asset-details.xlsx');

        // Read as Xlsx
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(false);

        if (!is_file($templatePath)) {
            throw new \Exception("Template not found: $templatePath");
        }
        $fh = fopen($templatePath, 'rb');
        $magic = fread($fh, 4);
        fclose($fh);
        if ($magic !== "PK\x03\x04") {
            throw new \Exception("Template is not a valid XLSX zip (missing PK header). Re-save as real .xlsx.");
        }

        $spreadsheet = $reader->load($templatePath);

        // MAIN sheet
        $main = $spreadsheet->getSheetByName('Sheet1');
        if (!$main) {
            throw new \Exception("Main sheet 'Sheet1' not found in template.");
        }

        // LOOKUPS sheet
        $lookup = $spreadsheet->getSheetByName('Lookups');
        if (!$lookup) {
            throw new \Exception("Template must contain a sheet named 'Lookups'.");
        }
        $lookup->setTitle('Lookups'); // keep formulas stable
        // Clear only what we use
        $lookup->fromArray(array_fill(0, 1000, array_fill(0, 60, null)), null, 'A1');

        // 1) Fetch & normalize assets
        $assets = CmmsAssetList::getAssetCodes();
        $assets = array_values((array) $assets);
        $assets = array_map(fn($v) => trim((string) $v), $assets);
        $assets = array_values(array_filter($assets, fn($x) => $x !== ''));

//        if (empty($assets)) {
//            throw new \Exception("getAssetCodes() returned empty after normalization.");
//        }
        // 2) Write assets vertically to Lookups!A2:A...
        $lookup->setCellValue('A1', 'ASSET_ID');
        $r = 2;
        foreach ($assets as $assetId) {
            $lookup->setCellValue("A{$r}", $assetId);
            $r++;
        }
        $assetLastRow = $r - 1;

        // 3) Create workbook-level named range for assets
        $spreadsheet->removeNamedRange('ASSETS_LIST');
        $spreadsheet->addNamedRange(new NamedRange(
                        'ASSETS_LIST',
                        $lookup,
                        "\$A\$2:\$A\${$assetLastRow}",
                        false
        ));

        $areas = CmmsAssetList::getAreas();
        $areas = array_values((array) $areas);
        $areas = array_map(fn($v) => trim((string) $v), $areas);
        $areas = array_values(array_filter($areas, fn($x) => $x !== ''));

        $lookup->setCellValue('B1', 'AREA');
        $ar = 2;
        foreach ($areas as $area) {
            $lookup->setCellValue("B{$ar}", $area);
            $ar++;
        }
        $areaLastRow = $ar - 1;
        $spreadsheet->removeNamedRange('AREA');
        $spreadsheet->addNamedRange(new NamedRange(
                        'AREA',
                        $lookup,
                        "\$B\$2:\$B\${$areaLastRow}",
                        false
        ));

        $sections = CmmsAssetList::getSections();
        $sections = array_values((array) $sections);
        $sections = array_map(fn($v) => trim((string) $v), $sections);
        $sections = array_values(array_filter($sections, fn($x) => $x !== ''));

        $lookup->setCellValue('C1', 'SECTION');
        $sr = 2;
        foreach ($sections as $section) {
            $lookup->setCellValue("C{$sr}", $section);
            $sr++;
        }
        $sectionLastRow = $sr - 1;
        $spreadsheet->removeNamedRange('SECTION');
        $spreadsheet->addNamedRange(new NamedRange(
                        'SECTION',
                        $lookup,
                        "\$C\$2:\$C\${$sectionLastRow}",
                        false
        ));

        $names = CmmsAssetList::getAssetNames();
        $names = array_values((array) $names);
        $names = array_map(fn($v) => trim((string) $v), $names);
        $names = array_values(array_filter($names, fn($x) => $x !== ''));

        $lookup->setCellValue('D1', 'ASSET_NAMES');
        $nr = 2;
        foreach ($names as $name) {
            $lookup->setCellValue("D{$nr}", $name);
            $nr++;
        }
        $nameLastRow = $nr - 1;
        $spreadsheet->removeNamedRange('ASSET_NAMES');
        $spreadsheet->addNamedRange(new NamedRange(
                        'ASSET_NAMES',
                        $lookup,
                        "\$D\$2:\$D\${$nameLastRow}",
                        false
        ));

        $fault_types = CmmsAssetFaults::getFaultTypes();
        $fault_types = array_values((array) $fault_types);
        $fault_types = array_map(fn($v) => trim((string) $v), $fault_types);
        $fault_types = array_values(array_filter($fault_types, fn($x) => $x !== ''));

        $lookup->setCellValue('E1', 'FAULT_TYPES');
        $fr = 5;
        foreach ($fault_types as $fault_type) {
            $alreadyExists = CmmsAssetFaults::find()
                    ->select('fault_type')
                    ->where(['fault_type' => $fault_type])
                    ->andWhere(['active_sts' => 1])
                    ->column();

            if ($alreadyExists)
                continue;
            $lookup->setCellValue("E{$fr}", $fault_type);
            $fr++;
        }
        $faultTypeLastRow = $fr - 1;
        $spreadsheet->removeNamedRange('FAULT_TYPES');
        $spreadsheet->addNamedRange(new NamedRange(
                        'FAULT_TYPES',
                        $lookup,
                        "\$E\$2:\$E\${$faultTypeLastRow}",
                        false
        ));

        // 4) Build mapping (Z/AA) and per-asset AREA named ranges
//        $lookup->setCellValue('Z1', 'ASSET_ID');
//        $lookup->setCellValue('AA1', 'AREA_RANGE');
//        
//        $lookup->setCellValue('AB1', 'ASSET_AREA_KEY');   // asset|area
//        $lookup->setCellValue('AC1', 'SECTION_RANGE');
//        
//        $lookup->setCellValue('AD1', 'ASSET_ID');
//        $lookup->setCellValue('AE1', 'NAME_RANGE');
//        $areaColIndex = 2; // B, then C, D...
//        $sectionColIndex = 30;
//        $nameColIndex = 50;
//        
//        $mapRow = 2;
//        $sectionMapRow = 2;
//        $nameMapRow = 2;
//        foreach ($assets as $assetId) {
//            $assetId = (string)$assetId;
//            $areaRangeName = $this->excelSafeName($assetId, 'AREA');
//            $nameRangeName = $this->excelSafeName($assetId, 'NAME'); // name depends on assetID
//
//            $lookup->setCellValue("Z{$mapRow}", $assetId);
//            $lookup->setCellValue("AA{$mapRow}", $areaRangeName);
//            
//            $lookup->setCellValue("AD{$nameMapRow}", $assetId);
//            $lookup->setCellValue("AE{$nameMapRow}", $nameRangeName);
//
//            $areas = CmmsAssetList::getAreas_by_Code($assetId);
//
//            // Normalize areas to flat list of strings
//            $areas = array_values((array)$areas);
//            $areas = array_map(function($v) {
//                if (is_object($v) && method_exists($v, '__toString')) return trim((string)$v);
//                if (is_array($v)) return trim((string)($v['area'] ?? $v['AREA'] ?? reset($v) ?? ''));
//                return trim((string)$v);
//            }, $areas);
//            $areas = array_values(array_filter($areas, fn($x) => $x !== ''));
//            
//            $names = CmmsAssetList::getAssetNames();
//
//            $colLetter = Coordinate::stringFromColumnIndex($areaColIndex);
//
//            // Write vertically
//            $rr = 2;
//            foreach ($areas as $a) {
//                $lookup->setCellValue("{$colLetter}{$rr}", $a);
//                $rr++;
//            }
//            $last = $rr - 1;
//
//            if ($last >= 2) {
//                $spreadsheet->removeNamedRange($areaRangeName);
//                $spreadsheet->addNamedRange(new NamedRange(
//                    $areaRangeName,
//                    $lookup,
//                    "\${$colLetter}\$2:\${$colLetter}\${$last}",
//                    false
//                ));
//            }
//
//            $areaColIndex++;
//            $mapRow++;
//        }
//        $mapLastRow = $mapRow - 1;
        // 5) Apply DV + helper col J
        $startRow = 2;
        $endRow = 500;

        $this->applyListValidationColumn($main, 'B', $startRow, $endRow, '=ASSETS_LIST');

//        $areaDvFormula = "=INDIRECT(IFERROR(VLOOKUP(\$B2,Lookups!\$Z\$2:\$AA\${$mapLastRow},2,FALSE),\"\"))";
        // apply per row (so B2 changes to B3 etc)
//        for ($row = $startRow; $row <= $endRow; $row++) {
//            $formula = "=INDIRECT(IFERROR(VLOOKUP(\$B{$row},Lookups!\$Z\$2:\$AA\${$mapLastRow},2,FALSE),\"\"))";
//            $this->applyListValidationCell($main, "C{$row}", $formula);
//        }
        // Hide lookups (VERYHIDDEN is best)
        $lookup->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);

        // Open on main sheet
        $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($main));

        // Output as .xlsx (IMPORTANT: extension must match!)
        $tmp = Yii::getAlias('@runtime') . '/cmms_asset_template_' . time() . '.xlsx';

        // prevent corrupting XLSX with stray output
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

//        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
//        $writer->save($tmp);
        try {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tmp);
        } catch (\Throwable $e) {
            Yii::error([
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                    ], 'XLSX_WRITE_FAIL');
            throw $e;
        }

        return Yii::$app->response->sendFile(
                        $tmp,
                        'cmms_asset_template.xlsx',
                        ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                )->on(\yii\web\Response::EVENT_AFTER_SEND, function () use ($tmp) {
                    @unlink($tmp);
                });
    }

    private function excelSafeName(string $value, string $prefix): string {
        $raw = trim($value);
        $v = strtoupper($raw);
        $v = preg_replace('/[^A-Z0-9_]/', '_', $v);
        $v = preg_replace('/_+/', '_', $v);
        if ($v === '' || ctype_digit($v[0]))
            $v = 'X_' . $v;

        $hash = substr(md5($raw), 0, 6);
        return $prefix . '_' . substr($v, 0, 35) . '_' . $hash;
    }

    public function actionUploadExcel() {
        if (Yii::$app->request->isPost) {
            $excelFile = UploadedFile::getInstanceByName('excelTemplate');

            if ($excelFile && $excelFile->tempName) {
                $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

//                if ($extension !== 'xls') {
                if (!in_array($extension, ['xls', 'xlsx'])) {
                    Yii::$app->session->setFlash('error', 'Please upload only .xls files.');
                    return $this->redirect(['index']);
                }

                try {
//                    $reader = new Xls();
                    $extension = strtolower(pathinfo($excelFile->name, PATHINFO_EXTENSION));

                    $kind = $extension === 'xlsx' ? 'Xlsx' : 'Xls';
//                    $reader = IOFactory::createReaderForFile($excelFile->tempName);
                    $reader = IOFactory::createReader($kind);
                    $reader->setReadDataOnly(true);           // ✅ reduces style/formula complexity
//                    $reader->setPreCalculateFormulas(false);
                    Cell::setValueBinder(new StringValueBinder());
                    $safePath = \Yii::getAlias('@runtime') . '/upload_' . uniqid() . '.' . $extension;
                    if (!copy($excelFile->tempName, $safePath)) {
                        throw new \Exception("Failed to copy uploaded file to runtime.");
                    }

                    try {
                        $spreadsheet = $reader->load($safePath);
//                        $spreadsheet = $reader->load($excelFile->tempName);
                    } finally {
                        @unlink($safePath);
                    }
//                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheet = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getSheet(0);

                    if ($worksheet === null) {
                        throw new \Exception("Asset List sheet not found in Excel file.");
                    }

                    $buffer = [];

                    $startRow = 2;
                    $lastRow = $worksheet->getHighestRow(); // safer than getHighestDataRow for mixed files

                    for ($row = $startRow; $row <= $lastRow; $row++) {
                        try {
                            $assetId = trim((string) $this->cellSafe($worksheet, "B{$row}"));
                            if ($assetId === '')
                                continue;

                            $buffer[] = [
                                'assetId' => $assetId,
                                'area' => (string) $this->cellSafe($worksheet, "C{$row}"),
                                'section' => (string) $this->cellSafe($worksheet, "D{$row}"),
                                'name' => (string) $this->cellSafe($worksheet, "E{$row}"),
                                'manufacturer' => (string) $this->cellSafe($worksheet, "F{$row}"),
                                'serial_no' => (string) $this->cellSafe($worksheet, "G{$row}"),
                                'date_of_purchase' => $this->cellSafe($worksheet, "H{$row}"), // keep raw
                                'date_of_installation' => $this->cellSafe($worksheet, "I{$row}"), // keep raw
                                'fault_type' => (string) $this->cellSafe($worksheet, "J{$row}"),
                                'fault_primary_detail' => (string) $this->cellSafe($worksheet, "K{$row}"),
                                'fault_secondary_detail' => (string) $this->cellSafe($worksheet, "L{$row}"),
                            ];
                        } catch (\Throwable $e) {
                            Yii::error([
                                'row' => $row,
                                'B_raw' => $worksheet->getCell("B{$row}")->getValue(),
                                'C_raw' => $worksheet->getCell("C{$row}")->getValue(),
                                'H_raw' => $worksheet->getCell("H{$row}")->getValue(),
                                'msg' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                    ], 'UPLOAD_ROW_FAIL');
                            throw $e;
                        }
                    }

                    if (!empty($buffer)) {
                        return $this->render('upload-to-confirm', ['buffer' => $buffer]);
                    } else {
//                        $asset = CmmsAssetList::findOne($id);
                        \common\models\myTools\FlashHandler::err("Upload failed: Please ensure that the 'Asset ID' column in your Excel file is not left blank.");
                        return $this->redirect(['index']);
                    }
                } catch (\Throwable $e) {
                    Yii::error([
                        'msg' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                            ], 'UPLOAD_XLSX_FAIL');
                    Yii::$app->session->setFlash('error', 'Error reading the Excel file: ' . $e->getMessage());
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('upload');
    }

    private function cellSafe(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $addr): string {
        $cell = $ws->getCell($addr);
        $v = $cell->getValue();

        if ($v instanceof RichText) {
            $v = $v->getPlainText();
        }

        if ($v === null)
            return '';
        if (is_bool($v))
            return $v ? '1' : '0';

        // IMPORTANT: do NOT calculate formulas in upload mode
        // if it's a formula, keep the displayed value as empty or the formula text
        if ($cell->isFormula()) {
            // safest: keep raw formula string or blank
            return (string) $v;
        }

        return trim((string) $v);
    }

    private function excelToMysqlDateTime($raw): ?string {
        if ($raw === null || $raw === '') {
            return null;
        }

        // Normalize
        if (is_string($raw)) {
            $raw = trim($raw);
            if ($raw === '')
                return null;

            // Remove thousands separators (WPS sometimes adds these)
            $raw = str_replace(',', '', $raw);
        }

        // 1) Excel numeric serial date (reasonable range check)
        if (is_numeric($raw)) {
            $num = (float) $raw;

            // Excel date serials are usually > 30000 (≈1982)
            if ($num > 30000 && $num < 60000) {
                try {
                    $dt = ExcelDate::excelToDateTimeObject($num);
                    return $dt->format('Y-m-d 00:00:00');
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        // 2) String-based date formats
        $raw = (string) $raw;
        foreach ([
    'Y-m-d',
    'Y/m/d',
    'd/m/Y',
    'd-m-Y',
    'Y-m-d H:i:s',
    'd/m/Y H:i:s',
        ] as $fmt) {
            $dt = \DateTime::createFromFormat('!' . $fmt, $raw);
            if ($dt !== false) {
                return $dt->format('Y-m-d 00:00:00');
            }
        }

        // 3) Fallback: try strtotime
        $ts = strtotime($raw);
        if ($ts !== false) {
            return date('Y-m-d 00:00:00', $ts);
        }

        return null;
//        if ($raw === null || $raw === '') return null;
//
//        // Excel stores dates as numbers
//        if (is_numeric($raw)) {
//            $dt = ExcelDate::excelToDateTimeObject((float)$raw);
//            return $dt->format('Y-m-d 00:00:00');
//        }
//
//        // If it ever comes as a string
//        $raw = trim((string)$raw);
//        foreach (['Y-m-d', 'Y/m/d', 'd/m/Y', 'd-m-Y'] as $fmt) {
//            $dt = \DateTime::createFromFormat('!' . $fmt, $raw);
//            if ($dt) return $dt->format('Y-m-d 00:00:00');
//        }
//
//        return null;
    }

    public function actionSaveAssetDetails() {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('CmmsAssetList');
            $faultData = Yii::$app->request->post('CmmsAssetFaults', []);

            if (!$data || empty($data['asset_id'])) {
                Yii::$app->session->setFlash('error', 'No asset data received.');
                return $this->redirect(['index']);
            }

            foreach ($data['asset_id'] as $index => $assetId) {
                $assetDetails = CmmsAssetList::find()
                        ->where(['asset_id' => $assetId])
                        ->andWhere(['active_sts' => 1])
                        ->andWhere(['is_deleted' => 0])
                        ->one();
                if (!$assetDetails) {
                    $assetDetails = new CmmsAssetList();
                }
                $assetDetails->asset_id = $assetId;
                $assetDetails->area = $data['area'][$index] ?? null;
                $assetDetails->section = $data['section'][$index] ?? null;
                $assetDetails->name = $data['name'][$index] ?? null;
                $assetDetails->manufacturer = $data['manufacturer'][$index] ?? null;
                $assetDetails->serial_no = $data['serial_no'][$index] ?? null;
//                $datePurchase = $data['date_of_purchase'][$index] ?? null;
//                if (!empty($datePurchase)) {
                $assetDetails->date_of_purchase = $this->excelToMysqlDateTime($data['date_of_purchase'][$index] ?? null);
//                }
//                $dateInstall = $data['date_of_installation'][$index] ?? null;
//                if (!empty($dateInstall)) {
                $assetDetails->date_of_installation = $this->excelToMysqlDateTime($data['date_of_installation'][$index] ?? null);
//                }
                $assetDetails->active_sts = 1;
                $assetDetails->is_deleted = 0;
                $assetDetails->updated_by = Yii::$app->user->identity->id;

                Yii::error([
                    'raw' => $data['date_of_purchase'][$index] ?? null,
                    'converted' => $this->excelToMysqlDateTime($data['date_of_purchase'][$index] ?? null),
                        ], 'DATE_DEBUG');

                if (!$assetDetails->save(false)) {
                    Yii::error($assetDetails->getErrors());
                }

                $assetFault = CmmsAssetFaults::find()
                        ->where(['asset_id' => $assetId])
                        ->andWhere(['fault_type' => $faultData['fault_type'][$index]])
                        ->andWhere(['fault_primary_detail' => $faultData['fault_primary_detail'][$index]])
                        ->andWhere(['fault_secondary_detail' => $faultData['fault_secondary_detail'][$index]])
                        ->andWhere(['active_sts' => 1])
                        ->one();

                if (!$assetFault) {
                    $assetFault = new CmmsAssetFaults();
                }
                $assetFault->asset_id = $assetId;
                $assetFault->fault_type = $faultData['fault_type'][$index] ?? null;
                $assetFault->fault_primary_detail = $faultData['fault_primary_detail'][$index] ?? null;
                $assetFault->fault_secondary_detail = $faultData['fault_secondary_detail'][$index] ?? null;
                $assetFault->active_sts = 1;
                $assetFault->is_deleted = 0;
                $assetFault->updated_by = Yii::$app->user->identity->id;
                $assetFault->asset_list_id = $assetDetails->id;

                if (!$assetFault->save(false)) {
                    Yii::error($assetFault->getErrors());
                }
            }
            Yii::$app->session->setFlash('success', 'Data successfully saved to the database.');
        }

//        $asset = CmmsAssetList::findOne($id);
        return $this->redirect(['index']);
    }

    public function actionAjaxAddFormItem() {

        $request = Yii::$app->request;

        $key = $request->post('key');
        $modelId = $request->post('modelId');
        $isUpdate = $request->post('isUpdate');

        if ($key === null || $modelId === null || $isUpdate === null) {
            throw new BadRequestHttpException('Missing required parameters');
        }
        $formItem = new CmmsAssetFaults();
        $formItem->is_deleted = 0;
        $formItem->active_sts = 1;
        $formItem->updated_by = \Yii::$app->user->identity->id;

//        UPDATE MODE
        if ($isUpdate && $modelId) {
            $model = CmmsAssetList::findOne($modelId);

            if (!$model) {
                throw new \yii\web\NotFoundHttpException('Fault List not found');
            }
            $formItem->asset_id = $model->asset_id;
            $formItem->asset_list_id = $model->id;
            $formItem->active_sts = 1;
        }
//        CREATE MODE
        else {
            $model = null;
        }

//           changed into this to enable addRow() to work
        return $this->renderPartial('_asset_details_form_row', [
                    'fault' => $formItem,
                    'key' => $key,
                    'model' => $model,
                    'isUpdate' => $isUpdate,
                    'form' => \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form'])
        ]);
    }

    public function actionAjaxDeleteItem($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $item = CmmsAssetFaults::findOne($id);

        if (!$item) {
            return ['success' => false, 'error' => 'Item or asset not found'];
        }

        $modelId = $item->asset_id;

        $item->is_deleted = 1;
        $item->active_sts = 0;
        $item->updated_by = Yii::$app->user->identity->id;
        if ($item->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($item->getErrors());

            $remainingCount = CmmsAssetFaults::find()
                            ->where([
                                'asset_id' => $modelId,
                                'is_deleted' => 0,
                                'active_sts' => 1
                            ])->count();

            if ($remainingCount == 0) {
                $model = CmmsAssetList::findOne($modelId);
                if ($model) {
                    $model->is_deleted = 1;
                    $model->active_sts = 0;
                    $model->save(false);
                }
                return [
                    'success' => true,
                    'redirect' => 'index',
                ];
            }

            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to deleted item'];
    }

    /**
     * Updates an existing CmmsAssetList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $vModel = VwCmmsAssetList::find()
                ->where(['id' => $id])
                ->all();
        $faults = $model->cmmsAssetFaults;

        if (\Yii::$app->request->isPost) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new \Exception('Failed to load asset data');
                }

                if (!$model->save()) {
                    throw new \Exception("Failed to save fault list.");
                }

                $postFaults = \Yii::$app->request->post('CmmsAssetFaults', []);
                foreach ($postFaults as $index => $postF) {
                    if (!empty($postF['id'])) {
                        $pF = CmmsAssetFaults::findOne($postF['id']);
                        if (!$pF) {
                            throw new \Exception('Asset fault not found');
                        }
                    } else {
                        $pF = new CmmsAssetFaults();
//                        $pF->asset_id = $model->id;
                        $pF->is_deleted = 0;
                        $pF->updated_by = \Yii::$app->user->identity->id;
                        $pF->asset_id = $model->asset_id;
                        $pF->active_sts = 1;
                        $pF->asset_list_id = $model->id;
                    }
                    $pF->setAttributes($postF);

                    if (!$pF->save()) {
                        throw new \Exception("Failed to save fault.");
                    }
                }

                $transaction->commit();
                FlashHandler::success('Asset details saved!');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $ex) {
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $ex->getMessage());
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'vModel' => $vModel,
                    'faults' => $faults,
                    'isUpdate' => true,
        ]);
    }

    /**
     * Deletes an existing CmmsAssetList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $faults = CmmsAssetFaults::findAll(['asset_id' => $id]);
        foreach ($faults as $mBD) {
            $mBD->is_deleted = 1;
            $mBD->active_sts = 0;
            $mBD->save(false);
        }

        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->active_sts = 0;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the CmmsAssetList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsAssetList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CmmsAssetList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
