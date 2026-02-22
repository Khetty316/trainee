<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\mi\MasterIncomings;
use frontend\models\working\mi\MasterIncomingsSearch;
use frontend\models\working\mi;
use frontend\models\working\project;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use \common\models\myTools\FlashHandler;

/**
 * MiController implements the CRUD actions for MasterIncomings model.
 */
class MiController extends Controller {

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
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all MasterIncomings models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new MasterIncomingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, "general");
        $docTypeList = mi\RefMiDoctypes::getActiveDropDownList();
//        $subDocTypeList = mi\RefMiSubdoctypes::getActiveDropDownList();
        $fileTypeList = mi\RefMiFiletypes::getActiveDropDownList();
        $userList = User::getActiveDropDownList();
        $taskList = mi\RefMiTasks::getActiveDropDownList();
        $statusList = mi\RefMiStatus::getActiveDropDownList();

        return $this->render('/working/mi/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'docTypeList' => $docTypeList,
                    'fileTypeList' => $fileTypeList,
                    'userList' => $userList,
                    'taskList' => $taskList,
                    'statusList' => $statusList
        ]);
    }

    /**
     * Displays a single MasterIncomings model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $model = $this->findModel($id);

//        return $model->uploader->fullname;
//        $workList = mi\MiWorklist::find()->where('mi_id=' . $model->id)->orderBy(['created_at' => SORT_ASC])->all();

        return $this->render('/working/mi/view', [
                    'model' => $model,
//                    'workList' => $workList
        ]);
    }

    public function actionViewonly($id) {
        $model = $this->findModel($id);
//        $workList = mi\MiWorklist::find()->where('mi_id=' . $model->id)->orderBy(['created_at' => SORT_ASC])->all();
        return $this->renderAjax('/working/mi/view', [
                    'model' => $model,
//                    'workList' => $workList
        ]);
    }

    /**
     * Creates a new MasterIncomings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new MasterIncomings();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->session->get('insertMiRecord') == true) {
                Yii::$app->session->set('insertMiRecord', false);
                $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
                if ($model->processAndSave()) {
                    FlashHandler::success("Document incoming registration success!");
                }
            }
            return $this->redirect('index');
        }

        $docTypeList = mi\RefMiDoctypes::getActiveDropDownList();
        $fileTypeList = mi\RefMiFiletypes::getActiveDropDownList();
        $userList = User::getActiveDropDownList();
        $projectList = project\MasterProjects::getActiveDropDownList();
        Yii::$app->session->set('insertMiRecord', true);
        return $this->render('create', [
                    'model' => $model,
                    'docTypeList' => $docTypeList,
                    'fileTypeList' => $fileTypeList,
                    'userList' => $userList,
                    'projectList' => $projectList
        ]);
    }

    /**
     * Updates an existing MasterIncomings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }


        $docTypeList = mi\RefMiDoctypes::getActiveDropDownList();
//        $subDocTypeList = mi\RefMiSubdoctypes::getActiveDropDownList();
        $fileTypeList = mi\RefMiFiletypes::getActiveDropDownList();
        $userList = User::getActiveDropDownList();

        return $this->render('update', [
                    'model' => $model,
                    'docTypeList' => $docTypeList,
//                    'subDocTypeList' => $subDocTypeList,
                    'fileTypeList' => $fileTypeList,
                    'userList' => $userList
        ]);
    }

    /**
     * Deletes an existing MasterIncomings model.
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
     * Finds the MasterIncomings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MasterIncomings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = MasterIncomings::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // *********** Render - Proc, GRN***********
    public function actionProcurementgrn() {
        return $this->mainRender("procurementGrn");
    }

    public function actionProcurementEditGrn() {
        return $this->mainRender("procurementGrnEdit");
    }

    public function actionGetPoRelatedInv($poId, $miId) {
        $model = \frontend\models\working\po\PurchaseOrderMaster::findOne($poId);
        $mi = MasterIncomings::findOne($miId);
        $invoices = MasterIncomings::find()->where('po_id=' . $poId)->andWhere('mi_status!=3')->all();

        return $this->renderAjax('_viewPoRelatedInv', [
                    'model' => $model,
                    'invoices' => $invoices,
                    'mi' => $mi
        ]);
    }

    // *********** Render - Proc, Receive Doc***********
    public function actionProcurementreceivedoc() {
        return $this->mainRender("procurementReceiveDoc");
    }

    // *********** Render - Director, Review***********
    public function actionDirectorreview() {
        return $this->mainRender("directorReview");
    }

    // *********** Render - Director, Acknowledge***********
    public function actionDirectorAcknowledge() {
        return $this->mainRender("directorAcknowledge");
    }

    // *********** Render - Requestor, Review***********
    public function actionRequestorreview() {
        return $this->mainRender("requestorReview");
    }

    // *********** Render - Requestor, Review History***********
    public function actionRequestorreviewHistory() {
        return $this->mainRender("requestorReviewHistory");
    }

    // *********** Render - Requestor, Acknowledge***********
    public function actionRequestorAcknowledge() {
        return $this->mainRender("requestorAcknowledge");
    }

    // *********** Render - Account, payment ***********
    public function actionAccountpay() {
        return $this->mainRender("accountPay");
    }

    // *********** Render - Account, Receive Doc ***********
    public function actionAccountreceivedoc() {
        return $this->mainRender("accountReceiveDoc");
    }

    // *********** Render - Account, All Documents***********
    public function actionAccountalldoc() {
        return $this->mainRender("accountAll");
    }

    // *********** Render Excel - All Documents***********
    public function actionAccountalldocExcel() {
//        return $this->mainRender("accountAllExcel");
        $taskName = 'accountAllExcel';

        $searchModel = new MasterIncomingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $taskName);
        $dataProvider->pagination = ['pageSize' => false];
        return $this->renderPartial('/working/mi/' . $taskName, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    // *********** Render - Admin, ***********
    public function actionAdminactiverecord() {
        return $this->mainRender("adminActiveRecord");
    }

    public function actionAdminkeepdoc() {
        return $this->mainRender("adminKeepDoc");
    }

    public function actionAdminsenddocacc() {
        return $this->mainRender("adminSendDocAcc");
    }

    public function actionAdminsenddocproc() {
        return $this->mainRender("adminSendDocProc");
    }

    // *********** Render - Main Renderer ***********
    public function mainRender($taskName) {
        $searchModel = new MasterIncomingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $taskName);
        $docTypeList = mi\RefMiDoctypes::getActiveDropDownList();
        $fileTypeList = mi\RefMiFiletypes::getActiveDropDownList();
        $userList = User::getActiveDropDownList();
        $taskList = mi\RefMiTasks::getActiveDropDownList();
        $statusList = mi\RefMiStatus::getActiveDropDownList();




        return $this->render('/working/mi/' . $taskName, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'docTypeList' => $docTypeList,
                    'fileTypeList' => $fileTypeList,
                    'userList' => $userList,
                    'taskList' => $taskList,
                    'statusList' => $statusList
        ]);
    }

    // *********************************** RESPOND ACTIONS ************************************************
    public function actionDirectorapprove() {
        $this->validateAndResponse(Yii::$app->request->post('mi_id'), Yii::$app->request->post('approval'), Yii::$app->request->post('remarks'), Yii::$app->request->post('currentstep'));
        $this->redirect('directorreview');
    }

    public function actionDirectorbulkapprove() {
        $this->bulkValidateAndResponse();
        $this->redirect('directorreview');
    }

    public function actionDirectorbulkreject() {
        $checkedList = explode(",", Yii::$app->request->post("checkedList"));
        $stepList = explode(",", Yii::$app->request->post("stepList"));
        $remarks = Yii::$app->request->post('remarks');

        foreach ($checkedList as $key => $mi_id) {
            $this->validateAndResponse($mi_id, 0, $remarks, $stepList[$key]);
        }
        $this->redirect('directorreview');
    }

    /**
     * ** Response from Director
     * Acknowledge M.I., single
     */
    public function actionDirectorAcknowledging($mi_id = 0, $currentStep = 0) {
        // if zero, meaning is single acknowledgement
        $id = ($mi_id == 0 ? Yii::$app->request->post('mi_id') : $mi_id);
        $currentstep = ($currentStep == 0 ? Yii::$app->request->post('currentstep') : $currentStep);
        $mi = MasterIncomings::findOne($id);

        if ($mi->current_step != $currentstep) {
            FlashHandler::err_outdated();
        } else {
            $mi->setAcknowledged(13);
        }
        if ($mi_id == 0 && $currentStep == 0) {
            $this->redirect('director-acknowledge');
        }
    }

    /**
     * ** Response from Director
     * Acknowledge M.I., in BULK
     */
    public function actionDirectorBulkAcknowledging() {
        $checkedList = explode(",", Yii::$app->request->post("checkedList"));
        $stepList = explode(",", Yii::$app->request->post("stepList"));
        foreach ($checkedList as $key => $mi_id) {
            $this->actionDirectorAcknowledging($mi_id, $stepList[$key]);
        }
        $this->redirect('director-acknowledge');
    }

    public function actionRequestorapprove() {
        $this->validateAndResponse(Yii::$app->request->post('mi_id'), Yii::$app->request->post('approval'), Yii::$app->request->post('remarks'), Yii::$app->request->post('currentstep'));
        $this->redirect('requestorreview');
    }

    /**
     * ** Response from Requestor
     * Acknowledge Requestor, M.I., single
     */
    public function actionRequestorAcknowledging($mi_id = 0, $currentStep = 0) {
        // if zero, meaning is single acknowledgement
        $id = ($mi_id == 0 ? Yii::$app->request->post('mi_id') : $mi_id);
        $currentstep = ($currentStep == 0 ? Yii::$app->request->post('currentstep') : $currentStep);
        $mi = MasterIncomings::findOne($id);

        if ($mi->current_step != $currentstep) {
            FlashHandler::err_outdated();
        } else {
            $mi->setAcknowledged(14);
        }
        if ($mi_id == 0 && $currentStep == 0) {
            $this->redirect('requestor-acknowledge');
        }
    }

    public function actionProcinsertgrn() {
        $id = Yii::$app->request->post('mi_id');
        $grn = Yii::$app->request->post('grn');
        $poId = Yii::$app->request->post('po_id');
        $approval = Yii::$app->request->post('approval');
        $remarks = Yii::$app->request->post('remarks');
        $currentstep = Yii::$app->request->post('currentstep');
        $mi = MasterIncomings::findOne($id);
        if ($mi->current_step != $currentstep) {
            FlashHandler::err_outdated();
        } else if ($mi->miUpdateGrn($grn, $poId, $approval, $remarks)) {
            $req = Yii::$app->request;
            $mi->miUpdateSubInvoice($req->post('subInvoice'), $req->post('subInvoiceUncheck'));
            $mi->miUpdateFinalInvoice($req->post('mainInvoice'));
            FlashHandler::suc_stsUpdate();
        } else {
            FlashHandler::err_getHelp();
        }
        $this->redirect('procurementgrn');
    }

    public function actionProcEditGrn() {
        $id = Yii::$app->request->post('mi_id');
        $grn = Yii::$app->request->post('grn');
        $poId = Yii::$app->request->post('po_id');
        $mi = MasterIncomings::findOne($id);

        $mi->grn_no = $grn;
        $mi->po_id = $poId;
        $mi->update(false);

        $req = Yii::$app->request;
        $mi->miUpdateSubInvoice($req->post('subInvoice'), $req->post('subInvoiceUncheck'));
        $mi->miUpdateFinalInvoice($req->post('mainInvoice'));
        FlashHandler::success("GRN / PO Number updated!");
        $this->redirect('procurement-edit-grn');
    }

    public function actionProcreceiveddoc() {
        $this->bulkValidateAndResponse();
        $this->redirect('procurementreceivedoc');
    }

    public function actionAccpaid() {
        $this->validateAndResponse(Yii::$app->request->post('mi_id'), Yii::$app->request->post('approval'), Yii::$app->request->post('remarks'), Yii::$app->request->post('currentstep'));
        $this->redirect('accountpay');
    }

    public function actionAccreceiveddoc() {
        $this->bulkValidateAndResponse();
        $this->redirect('accountreceivedoc');
    }

    public function actionForceclose() {
        $id = Yii::$app->request->post('mi_id');
        $remarks = Yii::$app->request->post('remarks');
        $currentstep = Yii::$app->request->post('currentstep');
        $mi = MasterIncomings::findOne($id);

        if ($mi->current_step != $currentstep) {
            FlashHandler::err_outdated();
        } else if ($mi->miForceClose($remarks)) {
            FlashHandler::suc_stsUpdate();
        } else {
            FlashHandler::err_getHelp();
        }
        $this->redirect('adminactiverecord');
    }

    public function actionAdminkeepingdoc() {
        $this->bulkValidateAndResponse();
        $this->redirect('adminkeepdoc');
    }

    public function actionAdminsendingdocacc() {
        $this->bulkValidateAndResponse();
        $this->redirect('adminsenddocacc');
    }

    public function actionAdminsendingdocproc() {
        $this->bulkValidateAndResponse();
        $this->redirect('adminsenddocproc');
    }

    public function bulkValidateAndResponse() {
        $checkedList = explode(",", Yii::$app->request->post("checkedList"));
        $stepList = explode(",", Yii::$app->request->post("stepList"));
        $remarks = Yii::$app->request->post('remarks');

        foreach ($checkedList as $key => $mi_id) {
            $this->validateAndResponse($mi_id, 1, $remarks, $stepList[$key]);
        }
    }

    public function validateAndResponse($id, $approval, $remarks, $currentstep) {
        $currentstep == "" ? 1 : $currentstep;
        $mi = MasterIncomings::findOne($id);

        if ($mi->current_step != $currentstep) {
            FlashHandler::err_outdated();
        } else if ($mi->miResponse($approval, $remarks)) {
            FlashHandler::suc_stsUpdate();
        } else {
            FlashHandler::err_getHelp();
        }
    }

    /**
     * ************************************* Sub Function
     * ****************************** to get images
     * @param type $filename
     * @return type
     */
    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['MI_file_path'] . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    public function actionGetPriorGrn() {
        $mi_id = Yii::$app->request->get("mi_id");
        $claimsMaster = \frontend\models\working\claim\ClaimsMaster::find()->where("claims_mi_id=$mi_id")->one();

        $priorGRN = null;
        if ($claimsMaster) {
            $priorGRN = $claimsMaster->getPriorGRN();
        }

        return $this->asJson($priorGRN);
    }

    /**
     * *********************************** Super User Start ***********************************************
     */
    public function actionSuperMiAll() {
        return $this->mainRender("superMiAll");
    }

    public function actionSuperMiEdit() {

        if (Yii::$app->request->post()) {
            $model = MasterIncomings::findOne(Yii::$app->request->post('miId'));
            if ($model->load(Yii::$app->request->post())) {

                $model->po_id = Yii::$app->request->post('po_id');

                if ($model->update(false)) {
                    FlashHandler::success("Document incoming update success!");
                }
                return $this->redirect('super-mi-all');
            }
        }

        $model = MasterIncomings::findOne(Yii::$app->request->get('miId'));


        $docTypeList = mi\RefMiDoctypes::getActiveDropDownList();
        $fileTypeList = mi\RefMiFiletypes::getActiveDropDownList();
        $userList = User::getActiveDropDownList();
        $projectList = project\MasterProjects::getActiveDropDownList();
        return $this->render('superMiEdit', [
                    'model' => $model,
                    'docTypeList' => $docTypeList,
                    'fileTypeList' => $fileTypeList,
                    'userList' => $userList,
                    'projectList' => $projectList
        ]);
    }

    public function actionSuperMiInvoice($projCodeSearch = '', $invTypeSearch = '') {
        $docTypeList = mi\RefMiDoctypes::getInvoiceDropDownList();


        return $this->render('superMiInvoice', [
                    'projCodeSearch' => $projCodeSearch,
                    'invTypeSearch' => $invTypeSearch,
                    'docTypeList' => $docTypeList,
        ]);
    }

    public function actionSuperViewInvoiceDetail($projCode, $docTypeId = '') {
        $sql2 = Yii::$app->db->createCommand("SELECT e.po_id, e.po_number, c.doc_type_name, a.index_no, a.id AS mi_id, a.isPerforma,a.particular,a.reference_no,d.currency_sign,b.amount
                FROM master_incomings AS a 
                JOIN mi_projects AS b ON a.id=b.mi_id
                JOIN ref_mi_doctypes AS c ON c.doc_type_id=a.doc_type_id " .
                ($docTypeId == "" ? " AND c.doc_type_id IN (2,3,4) " : " AND c.doc_type_id = " . $docTypeId ) . "
                JOIN ref_currencies AS d ON d.currency_id=b.currency_id
                LEFT JOIN purchase_order_master AS e ON e.po_id=a.po_id
                WHERE b.project_code='" . $projCode . "' 

                ORDER BY a.particular, a.id");
        $invoiceDetail = $sql2->queryAll();

        return $this->renderAjax('_superViewInvoiceDetail', [
                    'invoiceDetail' => $invoiceDetail,
        ]);
    }

    public function actionUpdateMiPo() {
        $miId = Yii::$app->request->post('miId');
        $projCodeSearch = Yii::$app->request->post('projCodeSearch');
        $invTypeSearch = Yii::$app->request->post('invTypeSearch');

        $mi = \frontend\models\working\mi\MasterIncomings::findOne($miId);

        if ($mi->load(Yii::$app->request->post())) {
            $mi->update(false);
            $req = Yii::$app->request;
            $mi->miUpdateSubInvoice($req->post('subInvoice'), $req->post('subInvoiceUncheck'));
            $mi->miUpdateFinalInvoice($req->post('mainInvoice'));
            FlashHandler::success("Invoice Updated!");
        }
        return $this->redirect(['super-mi-invoice', 'projCodeSearch' => $projCodeSearch, 'invTypeSearch' => $invTypeSearch]);
    }

// Get the P.O. Related Invoice, and the amount filter by project code    
    public function actionGetPoProjRelatedInv($poId, $miId, $projCode) {
        $model = \frontend\models\working\po\PurchaseOrderMaster::findOne($poId);
        $mi = MasterIncomings::findOne($miId);
        $invoices = MasterIncomings::find()->where('po_id=' . $poId)->andWhere('mi_status!=3')->all();


        return $this->renderAjax('_viewPoProjRelatedInv', [
                    'model' => $model,
                    'invoices' => $invoices,
                    'mi' => $mi,
                    'projCode' => $projCode
        ]);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ SUPER USER ENDS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
}
