<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\project\ProjectMaster;
use frontend\models\working\project\ProjectMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\working\project\ProjectProgressClaim;
use frontend\models\working\project\ProjectVo;
use frontend\models\working\project\ProjectSubcon;
use frontend\models\working\project\ProjectLetters;
use frontend\models\working\project\ProjectClosing;
use frontend\models\working\project\ProjectSubconClaim;
use common\models\myTools\FlashHandler;

/**
 * ProjectController implements the CRUD actions for ProjectMaster model.
 */
class ProjectController extends Controller {

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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProjectMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexProject', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('viewProject', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionViewProjectMain($id) {
        return $this->render('viewProjectMain', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionViewByProjectCode($projectCode) {
        $model = ProjectMaster::find()->where("proj_code='" . $projectCode . "'")->one();
        $model->getFilesFromFolder();
        return $this->render('viewProject', [
                    'model' => $model,
        ]);
    }

    /**
     * *********************************************** 1.CONTRACTS Start **********************************************
     */
    public function actionViewContract($id) {
        $vendorList = \frontend\models\working\contact\ContactMaster::getVendorList();
        return $this->render('viewContract', [
                    'model' => $this->findModel($id),
                    'vendorList' => $vendorList
        ]);
    }

    public function actionUpdateDates() {
        $id = Yii::$app->request->post('projId');
        $dateType = Yii::$app->request->post('projDateType');
        $date = Yii::$app->request->post('projDateInput');
        $model = ProjectMaster::findOne($id);
        if ($model->updateDates($dateType, $date)) {
            \common\models\myTools\FlashHandler::success("Date updated!");
        }
        return $this->redirect(['view-contract', 'id' => $id]);
    }

    public function actionCreateVo() {
        $id = Yii::$app->request->post('ProjectVo')['id'];

        if ($id) {
            $model = ProjectVo::findOne($id);
        } else {
            $model = new ProjectVo();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSave();
        }
        return $this->redirect(['view-contract', 'id' => $model->project_id]);
    }

    public function actionGetFileVo($id) {
        $model = ProjectVo::findOne($id);
        $projCode = $model->project->proj_code;
        $filename = $model->file;
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $projCode . '/' . Yii::$app->params['proj_vo_folder'] . '/' . $filename;

        return Yii::$app->response->sendFile($completePath, substr($model->file, 15), ['inline' => true]);
    }

    public function actionCreateSubcon() {
        $id = Yii::$app->request->post('ProjectSubcon')['id'];

        if ($id) {
            $model = ProjectSubcon::findOne($id);
        } else {
            $model = new ProjectSubcon();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSave();
        }
        return $this->redirect(['view-contract', 'id' => $model->project_id]);
    }

    public function actionGetFileSubcon($id) {
        $model = ProjectSubcon::findOne($id);
        $projCode = $model->project->proj_code;
        $filename = $model->file;
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $projCode . '/' . Yii::$app->params['proj_subcon_folder'] . '/' . $filename;

        return Yii::$app->response->sendFile($completePath, substr($model->file, 15), ['inline' => true]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 1.CONTRACTS Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * *********************************************** 2.LETTERS Start **********************************************
     */
    public function actionViewLetter($id) {
        return $this->render('viewLetter', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreateLetter() {
        $id = Yii::$app->request->post('ProjectLetters')['id'];

        if ($id) {
            $model = ProjectLetters::findOne($id);
        } else {
            $model = new ProjectLetters();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSave();
        }
        return $this->redirect(['view-letter', 'id' => $model->project_id]);
    }

    public function actionGetFileLetter($id) {
        $model = ProjectLetters::findOne($id);
        $projCode = $model->project->proj_code;
        $filename = $model->file;

        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $projCode . '/';
        if ($model->letter_type == "incoming") {
            $completePath .= Yii::$app->params['proj_letter_incoming_folder'] . '/';
        } else {
            $completePath .= Yii::$app->params['proj_letter_outgoing_folder'] . '/';
        }

        $completePath .= $filename;
        return Yii::$app->response->sendFile($completePath, substr($model->file, 15), ['inline' => true]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 2.LETTERS Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * ****************************************** 3. MAIN CON PROGESS CLAIMS Start**********************************************
     */
    public function actionViewProgressClaimMain($id) {
        $searchModel = new \frontend\models\working\project\ProjectProgressClaimSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'viewProgressClaimMain', array($id));

        return $this->render('viewProgressClaimMain', [
                    'project' => $this->findModel($id),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateProgressClaimMain() {
        $id = Yii::$app->request->post('ProjectProgressClaim')['id'];

        if ($id) {
            $model = ProjectProgressClaim::findOne($id);
        } else {
            $model = new ProjectProgressClaim();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSave();
        }
        return $this->redirect(['view-progress-claim-main', 'id' => $model->project_id]);
    }

    public function actionAddProgressClaimMainCertified() {
        $id = Yii::$app->request->post('ProjectProgressClaim')['id'];
        $model = ProjectProgressClaim::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSaveCertified();
            \common\models\myTools\FlashHandler::success("Claim cert added!");
        }
        return $this->redirect(['view-progress-claim-main', 'id' => $model->project_id]);
    }

    public function actionAddProgressClaimMainPayment() {
        $model = new \frontend\models\working\project\ProjectProgressClaimPay();
        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            \common\models\myTools\FlashHandler::success("Payment record added!");
        }
        return $this->redirect(['view-progress-claim-main', 'id' => $model->progressClaim->project_id]);
    }

    /**
     * Get files of Progress Claim Main Con
     */
    public function actionGetFilePClaimMain($id, $type) {
        $model = ProjectProgressClaim::findOne($id);
        $projCode = $model->project->proj_code;
        if ($type == 'submit') {
            $filename = $model->submit_file;
        } else if ($type == 'certified') {
            $filename = $model->certified_file;
        } else if ($type == 'invoice') {
            $filename = $model->invoice_file;
        }

        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $projCode . '/' . Yii::$app->params['proj_main_claim_folder'] . '/' . $filename;

        return Yii::$app->response->sendFile($completePath, substr($filename, 15), ['inline' => true]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 3. MAIN CON PROGESS CLAIMS Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * ******************************************************* 4. SUB CON PROGRESS CLAIMS Start *********************************************************
     */
    public function actionViewProgressClaimSub($id) {
        $project = $this->findModel($id);
        $subConList = $project->projectSubcons;
        return $this->render('viewProgressClaimSub', [
                    'project' => $project,
                    'subConList' => $subConList,
        ]);
    }

    public function actionCreateProgressClaimSub() {
        $id = Yii::$app->request->post('ProjectSubconClaim')['id'];

        if ($id) {
            $model = ProjectSubconClaim::findOne($id);
        } else {
            $model = new ProjectSubconClaim();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFileSubmit = \yii\web\UploadedFile::getInstance($model, 'scannedFileSubmit');
            $model->scannedFileCertified = \yii\web\UploadedFile::getInstance($model, 'scannedFileCertified');
            $model->scannedFileInvoice = \yii\web\UploadedFile::getInstance($model, 'scannedFileInvoice');
            $model->processAndSave();
        }
        return $this->redirect(['view-progress-claim-sub', 'id' => $model->projSub->project_id]);
    }

    public function actionGetFilePClaimSub($id, $type) {
        $model = ProjectSubconClaim::findOne($id);
        $projCode = $model->projSub->project->proj_code;
        if ($type == 'submit') {
            $filename = $model->submit_file;
            $filepath = Yii::$app->params['proj_subcon_claim_submit'] . '/' . $filename;
        } else if ($type == 'certified') {
            $filename = $model->certified_file;
            $filepath = Yii::$app->params['proj_subcon_claim_cert'] . '/' . $filename;
        } else if ($type == 'invoice') {
            $filename = $model->invoice_file;
            $filepath = Yii::$app->params['proj_subcon_claim_inv'] . '/' . $filename;
        }

        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . $projCode . '/' . $filepath;

        return Yii::$app->response->sendFile($completePath, substr($filename, 15), ['inline' => true]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 4. SUB CON PROGRESS CLAIMS Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * ******************************************************* 5. COSTING Start *********************************************************
     */
    public function actionViewCosting($id) {
        $model = $this->findModel($id);
        // Claims Total
        $sql = Yii::$app->db->createCommand("SELECT sum(amount) as total,claim_type,claim_name FROM v_claim_costing WHERE project_id=" . $id . " GROUP BY claim_type");
        $claimCostingTotal = $sql->queryAll();


        // Invoice Total
        $sql2 = Yii::$app->db->createCommand("SELECT c.doc_type_name,c.doc_type_id,d.currency_sign,SUM(b.amount) AS total
                FROM master_incomings AS a 
                JOIN mi_projects AS b ON a.id=b.mi_id
                JOIN ref_mi_doctypes AS c ON c.doc_type_id=a.doc_type_id AND c.doc_type_id IN (2,3,4)
                JOIN ref_currencies AS d ON d.currency_id=b.currency_id
                WHERE b.project_code='" . $model->proj_code . "' AND a.final_invoice IS NULL 
                    AND a.mi_status <> 3 
                    AND (SELECT 1 FROM mi_worklist AS checking WHERE checking.mi_id=a.id AND task_id = 1 AND approved_flag=1) IS NOT NULL
                GROUP BY c.doc_type_id,d.currency_id");
        $invoiceCostingTotal = $sql2->queryAll();

        $sql3 = Yii::$app->db->createCommand("SELECT c.company_name, b.description, SUM(certified_amount) AS 'total' FROM project_subcon_claim AS a JOIN project_subcon AS b ON b.id = a.proj_sub_id "
                . "JOIN contact_master AS c ON c.id = b.vendor_id WHERE b.project_id = " . $id . " GROUP BY 1,2");

        $subconClaimTotal = $sql3->queryAll();

        return $this->render('viewCosting', [
                    'model' => $model,
//                    'claimCosting' => $vClaimCosting,
                    'claimCostingTotal' => $claimCostingTotal,
                    'invoiceCostingTotal' => $invoiceCostingTotal,
                    'subconClaimTotal' => $subconClaimTotal
        ]);
    }

    public function actionViewCostingClaimDetail($projId, $claimType) {
        $claimDetail = \frontend\models\working\project\VClaimCosting::find()->where('project_id=' . $projId)
                ->andWhere('claim_type="' . $claimType . '"')
                ->orderBy(['date1' => SORT_ASC])
                ->all();

        return $this->renderAjax('_viewCostingClaimDetail', [
                    'claimDetail' => $claimDetail,
        ]);
    }

    public function actionViewCostingInvoiceDetail($projCode, $docTypeId) {
        $sql2 = Yii::$app->db->createCommand("SELECT e.po_id, e.po_number, c.doc_type_name, a.index_no, a.id AS mi_id, a.isPerforma,a.particular,a.reference_no,d.currency_sign,b.amount
                FROM master_incomings AS a 
                JOIN mi_projects AS b ON a.id=b.mi_id
                JOIN ref_mi_doctypes AS c ON c.doc_type_id=a.doc_type_id AND c.doc_type_id = " . $docTypeId . "
                JOIN ref_currencies AS d ON d.currency_id=b.currency_id
                LEFT JOIN purchase_order_master AS e ON e.po_id=a.po_id
                WHERE b.project_code='" . $projCode . "' AND a.final_invoice IS NULL
                    AND a.mi_status <> 3 
                    AND (SELECT 1 FROM mi_worklist AS checking WHERE checking.mi_id=a.id AND task_id = 1 AND approved_flag=1) IS NOT NULL
                ORDER BY a.particular, a.id");
        $invoiceDetail = $sql2->queryAll();

        return $this->renderAjax('_viewCostingInvoiceDetail', [
                    'invoiceDetail' => $invoiceDetail,
        ]);
    }

    public function actionUpdateMiPo() {
        $miId = Yii::$app->request->post('miId');
        $projId = Yii::$app->request->post('projId');

        $mi = \frontend\models\working\mi\MasterIncomings::findOne($miId);

        if ($mi->load(Yii::$app->request->post())) {
            $mi->update(false);
            $req = Yii::$app->request;
            $mi->miUpdateSubInvoice($req->post('subInvoice'), $req->post('subInvoiceUncheck'));
            $mi->miUpdateFinalInvoice($req->post('mainInvoice'));
            FlashHandler::success("Invoice Updated!");
        }
        return $this->redirect(['view-costing', 'id' => $projId]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 5. COSTING Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * ******************************************************* 6. CLOSING Start *********************************************************
     */
    public function actionViewClosing($id) {
        $model = $this->findModel($id);

        if (!$model->projectClosings) {
            $closing = new ProjectClosing();
            $closing->initiate($id);
        } else {
            $closing = $model->projectClosings[0];
        }

        return $this->render('viewClosing', [
                    'model' => $model,
                    'closing' => $closing
        ]);
    }

    public function actionCreateClosing() {
        $id = Yii::$app->request->post('ProjectLetters')['id'];

        if ($id) {
            $model = ProjectLetters::findOne($id);
        } else {
            $model = new ProjectLetters();
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->processAndSave();
        }
        return $this->redirect(['view-letter', 'id' => $model->project_id]);
    }

    public function actionUpdateClosing() {
        $model = ProjectClosing::findOne(Yii::$app->request->post('ProjectClosing')['id']);
        if ($model->load(Yii::$app->request->post())) {
            $updateType = Yii::$app->request->post('updateType');
            $updater = Yii::$app->user->identity->id;
            if ($updateType == "cpc") {
                $model->cpc_by = $updater;
                if ($model->update()) {
                    \common\models\myTools\FlashHandler::success("CPC Updated!");
                }
            } else if ($updateType == "cmgd") {
                $model->cmgd_by = $updater;

                if ($model->update()) {
                    \common\models\myTools\FlashHandler::success("CMGD Updated!");
                }
            } else if ($updateType == "finAcc") {
                $model->final_acc_by = $updater;
                if ($model->update()) {
                    \common\models\myTools\FlashHandler::success("Final Account Updated!");
                }
            }
        }

        return $this->redirect(['view-closing', 'id' => $model->project_id]);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 6. CLOSING End ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * ******************************************************* 7. ACCOUNT ISSUE INVOICE Start *********************************************************
     */
    public function actionIndexAccountIssueInvoice() {
        $searchModel = new \frontend\models\working\project\ProjectProgressClaimSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexAccountIssueInvoice');

        return $this->render('indexAccountIssueInvoice', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccountIssueInvoice() {
        $id = Yii::$app->request->post('ProjectProgressClaim')['id'];
        $progressClaim = ProjectProgressClaim::findOne($id);
        if ($progressClaim->load(Yii::$app->request->post())) {
            $progressClaim->scannedFile = \yii\web\UploadedFile::getInstance($progressClaim, 'scannedFile');
            if ($progressClaim->issueInvoice()) {
                FlashHandler::success("Invoice Attached");
            }
        }

        return $this->redirect(['index-account-issue-invoice']);
    }

    // * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 7. ACCOUNT ISSUE INVOICE End ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // 
    //

    /**
     * Creates a new ProjectMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ProjectMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            FlashHandler::success("Updated!");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $userList = \common\models\User::getActiveAutocompleteList();
        $clientList = \frontend\models\working\contact\ContactMaster::getClientList();

        return $this->render('updateProject', [
                    'model' => $model,
                    'userList' => $userList,
                    'clientList' => $clientList
        ]);
    }

    /**
     * Deletes an existing ProjectMaster model.
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
     * Finds the ProjectMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectMaster::findOne($id)) !== null) {
            $model->getFilesFromFolder();
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateProjectAjax() {

        if (!Yii::$app->request->isPost) {
            return false;
        }

        $revisionId = Yii::$app->request->post('revisionId');
        $project = new ProjectMaster();
        return json_encode(['data' => ['success' => $project->createProjectFromProspect($revisionId)]]);
    }

    public function actionDeleteAttachment() {
        $projCode = Yii::$app->request->post('projCode');
        $filename = Yii::$app->request->post('filename');
        if (Yii::$app->request->isPost) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . '/' . $projCode . '/' . Yii::$app->params['tender_doc_folder'] . '/' . $filename;
            \yii\helpers\FileHelper::unlink($filePath);
            return json_encode(array("msg" => "File removed!!"));
        } else {
            return json_encode(array("msg" => "Fail to remove.."));
        }
    }

    public function actionTest() {
        $str = '20210525043641-N001-Letters.pdf';
//            return date('Ymdhis', time());
        return substr($str, 15);
    }

}
