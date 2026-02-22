<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\claim\ClaimsDetail;
use frontend\models\working\claim\ClaimsDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\working\claim\ClaimsMaster;
use frontend\models\working\claim\ClaimsMasterSearch;
use yii\filters\AccessControl;
use frontend\models\working\claim\VClaimDetailSearch;
use common\models\myTools\FlashHandler;
use frontend\models\working\claim\RefClaimStatus;
use frontend\models\working\claim\RefClaimType;
use yii\helpers\ArrayHelper;

/**
 * ClaimsController implements the CRUD actions for ClaimsDetail model.
 */
class ClaimController extends Controller {

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
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ClaimsDetail models.
     * @return mixed
     */
    public function actionIndex() {
        return $this->redirect('/site/dashboard');
    }

    /**
     * *************************************** RENDER FOR VIEW - PERSONAL CLAIM MAIN
     * Lists all ClaimsDetail models.
     * @return mixed
     */
    public function actionPersonalClaim() {
        $searchModel = new ClaimsDetailSearch();
        $dataProviders = array();
        $claimsCategory = \frontend\models\working\claim\RefClaimType::find()->all();
        foreach ($claimsCategory as $claimGroup) {
            $dataProviders[$claimGroup->claim_name] = $searchModel->search(Yii::$app->request->queryParams, 'personalClaim', $claimGroup->code);
            $this->displayDetails_dataProvider($dataProviders[$claimGroup->claim_name]);
        }

        $dataProviderOutdated = $searchModel->search(Yii::$app->request->queryParams, 'outdated');
        $dataProviderRejected = $searchModel->search(Yii::$app->request->queryParams, 'rejected');

        $this->displayDetails_dataProvider($dataProviderOutdated);
        $this->displayDetails_dataProvider($dataProviderRejected);

        $dataProviderOthers = array();
        $dataProviderOthers['outdated'] = $dataProviderOutdated;
        $dataProviderOthers['rejected'] = $dataProviderRejected;

        $claimTypeList = \frontend\models\working\claim\RefClaimType::getDropDownList();

        return $this->render('personalClaim', [
                    'searchModel' => $searchModel,
                    'claimTypeList' => $claimTypeList,
                    'dataProviders' => $dataProviders,
                    'dataProviderOthers' => $dataProviderOthers
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - PERSONAL SUBMITTED CLAIM MAIN
     * Lists all ClaimsDetail models.
     */
    public function actionPersonalSubmittedClaim() {
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalSubmittedClaim');

        return $this->render('personalSubmittedClaim', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - HR TRAVEL CLAIM (PENDING)
     */
    public function actionHrTravelClaim() {
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrTravelClaim');
        $this->displayMasterDetails_dataProvider($dataProvider);
        return $this->render('hrTravelClaim', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - HR TRAVEL CLAIM (ALL)
     */
    public function actionHrTravelClaimAll() {
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrTravelClaimAll');
        $this->displayMasterDetails_dataProvider($dataProvider);
        return $this->render('hrTravelClaimAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER & ACTION - HR PAY TRAVEL CLAIM
     * Set travel claim status from document received to "Pay"
     */
    public function actionHrPayTravelClaim($claimsMasterId) {

        $claimMaster = ClaimsMaster::findOne($claimsMasterId);
        $setPay = Yii::$app->request->post('setPay');
        if ($setPay == "pay" && $claimMaster->payClaim()) {
            FlashHandler::success("Status updated!");
        } else if ($setPay == "reject" && $claimMaster->rejectClaim()) {
            FlashHandler::success("Status updated!");
        } else if ($setPay == "approve" && $claimMaster->hrApproveClaim()) {
            FlashHandler::success("Status updated!");
        }
        $this->displayDetails($claimMaster->claimsDetails);

        $searchModel = new ClaimsDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'hrPayTravelClaim', $claimsMasterId);
        return $this->render('hrPayTravelClaim', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'claimMaster' => $claimMaster
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - ACCOUNT PENDING CLAIM (To Be Paid)
     */
    public function actionAccountClaimPending() {
        $claimsMasterId = Yii::$app->request->post('claimIds');

        if ($claimsMasterId != "") {

            $claimsMasterId = explode(",", $claimsMasterId);
//                        ClaimsMaster::updateAll(['claims_status' => RefClaimStatus::STATUS_PAID], ['claims_master_id' => $claimsMasterId]);
            if (ClaimsMaster::updateAll(['claims_status' => RefClaimStatus::STATUS_PAID], ['claims_master_id' => $claimsMasterId])) {
                FlashHandler::success("Set to Paid");
            }
            return $this->redirect('account-claim-pending');
        }

        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'accountClaimPending');
        $this->displayMasterDetails_dataProvider($dataProvider);

        return $this->render('accountClaimPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - ACCOUNT CLAIM ALL
     */
    public function actionAccountClaimAll() {

        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'accountClaimAll');
        $this->displayMasterDetails_dataProvider($dataProvider);

        return $this->render('accountClaimAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - PROCUREMENT
     */
    public function actionProcClaimGrn() {
        $recordCountSQL = (new \yii\db\Query())->select(['claim_master_id, COUNT(1) pending'])
                        ->from("claims_detail")
                        ->join('INNER JOIN', 'claims_master', 'claims_detail.claim_master_id = claims_master.claims_master_id')
                        ->where('claims_master.claim_type IN ("mat", "pet") AND claims_master.claims_status IN (2, 3) AND claims_detail.grn_no IS NULL')
                        ->groupBy(['claim_master_id'])->all();

        $recordCount = ArrayHelper::map($recordCountSQL, 'claim_master_id', 'pending');
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procClaimGRN');
        $this->displayMasterDetails_dataProvider($dataProvider);

        return $this->render('procClaimGRN', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'recordCount' => $recordCount
        ]);
    }

    /**
     * *************************************** RENDER & ACTION - PROCUREMENT To Insert PRE GRN
     */
    public function actionProcClaimAssignGrn($claimsMasterId = "") {
        if ($claimsMasterId == "") {
            $claimsMasterId = Yii::$app->request->post('claimsMasterId');
        }
        $claimsDetailId = Yii::$app->request->post('claimsDetailId');
        $grn = Yii::$app->request->post('grn');

        $claimMaster = ClaimsMaster::findOne($claimsMasterId);
        if ($claimsDetailId != "") {
            $claim = ClaimsDetail::findOne($claimsDetailId);
            $claim->grn_no = $grn == "" ? "-" : $grn;
            if ($claim->update(false)) {
                FlashHandler::success("GRN updated!");
            }
        }

        $searchModel = new ClaimsDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procClaimAssignGRN', $claimsMasterId);
        $this->displayDetails_dataProvider($dataProvider);

        return $this->render('procClaimAssignGRN', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'claimMaster' => $claimMaster
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - SUPER USER View ALL CLAIMS 
     */
    public function actionSuperClaimAll() {
        $searchModel = new ClaimsMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'General');
        $this->displayMasterDetails_dataProvider($dataProvider);

        return $this->render('superClaimAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - SUPER USER - Reporting, Medical Claims Summary
     * @return type
     */
    public function actionSuperClaimMedical() {
        $medicalClaims = ArrayHelper::toArray((new \yii\db\Query())->select(['YEAR(invoice_date) as theYear,claimant, claimant_id,SUM(amount) as total'])
                                ->from('v_claim_detail')
                                ->where('claim_type = "' . RefClaimType::TYPE_MEDICAL . '"') // ent
                                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                                ->groupBy(['YEAR(invoice_date)', 'claimant'])
                                ->orderBy(['YEAR(invoice_date)' => SORT_DESC, 'claimant' => SORT_ASC, 'claims_detail_id' => SORT_DESC])
                                ->all());
        $dataList = [];
        foreach ($medicalClaims as $claim) {
            $dataList[substr($claim['theYear'], 0, 4)][$claim['claimant']]['total'] = $claim['total'];
            $dataList[substr($claim['theYear'], 0, 4)][$claim['claimant']]['claimant_id'] = $claim['claimant_id'];
        }

        return $this->render('superClaimMedical', [
                    'dataList' => $dataList,
        ]);
    }

    /**
     * *************************************** AJAX, RENDER FOR VIEW - SUPER USER - Reporting, Medical Claims Summary Detail
     * @param type $year
     * @param type $claimantId
     */
    public function actionSuperClaimMedicalDetailAjax($year, $claimantId) {
        $searchModel = new VClaimDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '_viewClaimMedicalDetail', [$year, $claimantId]);
        return $this->renderPartial('_viewClaimMedicalDetail', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - SUPER USER - Reporting, Entertainment Summary by Months
     * @return type
     */
    public function actionSuperClaimEntertainment($selectYear = "") {

        if ($selectYear == "") {
            $selectYear = date("Y");
        }
        $claims = ArrayHelper::toArray((new \yii\db\Query())
                                ->select(['YEAR(invoice_date) as theYear, MONTH(invoice_date) as theMonth, project_account, SUM(claims_detail_sub.amount) as total'])
                                ->from('v_claim_detail')
                                ->where('claim_type = "' . RefClaimType::TYPE_ENTERTAINMENT . '"') // ent
                                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                                ->andWhere("YEAR(invoice_date) = $selectYear")
                                ->join('INNER JOIN', 'claims_detail_sub', 'claims_detail_sub.claims_detail_id=v_claim_detail.claims_detail_id')
                                ->groupBy(['YEAR(invoice_date),MONTH(invoice_date),project_account'])
                                ->orderBy(['YEAR(invoice_date)' => SORT_ASC, 'MONTH(invoice_date)' => SORT_ASC, 'v_claim_detail.claims_detail_id' => SORT_DESC])
                                ->all());
        $projects = ArrayHelper::toArray((new \yii\db\Query())
                                ->select(['project_account as projCode'])
                                ->from('v_claim_detail')
                                ->where('claim_type = "' . RefClaimType::TYPE_ENTERTAINMENT . '"') // ent
                                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                                ->andWhere("YEAR(invoice_date) = $selectYear")
                                ->join('INNER JOIN', 'claims_detail_sub', 'claims_detail_sub.claims_detail_id=v_claim_detail.claims_detail_id')
                                ->orderBy(['projCode' => SORT_ASC])
                                ->distinct()
                                ->all());
        $dataLists = [];
        foreach ($claims as $key => $claim) {
            $dataLists[$claim['theYear'] . "-" . $claim['theMonth']]['year'] = $claim['theYear'];
            $dataLists[$claim['theYear'] . "-" . $claim['theMonth']]['month'] = $claim['theMonth'];
            $dataLists[$claim['theYear'] . "-" . $claim['theMonth']]['detail'][$claim['project_account']] = $claim['total'];
        }



        $yearMinMax = (new \yii\db\Query())
                ->select(['min(YEAR(`invoice_date`)) as minYear,max(YEAR(`invoice_date`)) as maxYear'])
                ->from('v_claim_detail')
                ->where('claim_type = "' . RefClaimType::TYPE_ENTERTAINMENT . '"') // ent
                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                ->one();
        return $this->render('superClaimEntertainment', [
                    'dataLists' => $dataLists,
                    'projects' => $projects,
                    'selectYear' => $selectYear,
                    'yearMinMax' => $yearMinMax
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - SUPER USER - Reporting, Entertainment Summary by Detail
     * @return type
     */
    public function actionSuperClaimEntertainmentDetail($selectYear = "", $exportToExcel = false) {

        if ($selectYear == "") {
            $selectYear = date("Y");
        }

        $claimsDetails = ArrayHelper::toArray((new \yii\db\Query())
                                ->select(['invoice_date, claimant, project_account, (claims_detail_sub.amount) as total'])
                                ->from('v_claim_detail')
                                ->where('claim_type = "' . RefClaimType::TYPE_ENTERTAINMENT . '"') // ent
                                ->andWhere("YEAR(invoice_date) = $selectYear")
                                ->join('INNER JOIN', 'claims_detail_sub', 'claims_detail_sub.claims_detail_id=v_claim_detail.claims_detail_id')
                                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                                ->orderBy(['invoice_date' => SORT_ASC])
                                ->all());
        if ($exportToExcel) {
            return $this->renderPartial('superClaimEntertainmentDetailExcel', [
                        'claimsDetails' => $claimsDetails,
                        'selectYear' => $selectYear,
            ]);
        }

        $yearMinMax = (new \yii\db\Query())
                ->select(['min(YEAR(`invoice_date`)) as minYear,max(YEAR(`invoice_date`)) as maxYear'])
                ->from('v_claim_detail')
                ->where('claim_type = "' . RefClaimType::TYPE_ENTERTAINMENT . '"') // ent
                ->andWhere('claims_status in (' . RefClaimStatus::STATUS_PAID . ')')
                ->one();

        return $this->render('superClaimEntertainmentDetail', [
                    'claimsDetails' => $claimsDetails,
                    'selectYear' => $selectYear,
                    'yearMinMax' => $yearMinMax
        ]);
    }

    /**
     * *************************************** AJAX, RENDER FOR VIEW - SUPER USER - Reporting, Medical Claims Summary Detail
     * @param type $year
     * @param type $claimantId
     */
    public function actionSuperClaimEntertainmentDetailAjax($year, $claimantId) {
        $searchModel = new VClaimDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '_viewClaimMedicalDetail', [$year, $claimantId]);
        return $this->renderPartial('_viewClaimMedicalDetail', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * *************************************** RENDER FOR VIEW - Super User - Modify Claim Item
     * ********************* To modify the claim type
     */
    public function actionSuperClaimModify() {
        $searchModel = new VClaimDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superClaimModify');
        return $this->render('superClaimModify', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * *************************************** Function & Redirect - Super User - Modify Claim Item Type
     * Modify claim item type, and the redirect back to the same page
     * Get response from Modal
     */
    public function actionSuperModifyClaimType() {
        if (!Yii::$app->request->isPost) {
            return '';
        }

        $model = ClaimsDetail::findOne(Yii::$app->request->post('claims_detail_id'));
        if ($model->changeClaimType(Yii::$app->request->post('claims_type'))) {
            FlashHandler::success("Modified!");
        }

        return $this->redirect('super-claim-modify');
    }

    /**
     * *************************************** Function & Redirect - Super User - Modify Claim Form Claimant
     * Modify claim form claimant, and redirect
     */
    public function actionSuperTransferClaimant() {
        if (!Yii::$app->request->isPost) {
            return 'ERROR';
        }
        $model = ClaimsMaster::findOne(Yii::$app->request->post('claims_master_id'));

        if (!in_array($model->claims_status, array(1, 2, 3), true)) {
            FlashHandler::err('Claim\'s status is not allowed to do claim transfer now.');
        } else if ($model->transferClaimant(Yii::$app->request->post('new_claimant'))) {
            FlashHandler::success("Transferred!");
        }

        return $this->redirect('super-claim-all');
    }

    /**
     * *************************************** Render for View - Normal User - Show Claims Form Detail
     */
    public function actionViewClaimmasterDetail($claimsMasterId) {
        $model = ClaimsMaster::findOne($claimsMasterId);
        $this->displayDetails($model->claimsDetails);
        return $this->render('viewClaimmasterDetail', [
                    'model' => $model
        ]);
    }

    /**
     * *************************************** Render Partial for Ajax View - (MULTIPLE) - Show Claims Form Detail
     * Show claim form details in Modal, 
     */
    public function actionViewonly($claimsMasterId) {
        $model = ClaimsMaster::findOne($claimsMasterId);
        $this->displayDetails($model->claimsDetails);
        return $this->renderPartial('viewClaimmasterDetail', [
                    'model' => $model
        ]);
    }

    /**
     * *************************************** Render for View - Authorizer - List of Auth Item
     * View list of claims pending for authorization
     */
    public function actionAuthorizeClaim() {
        $searchModel = new ClaimsDetailSearch();

        $claimsDetailIdsApprove = Yii::$app->request->post('claims_detail_ids_authorize');
        $claimsDetailIdsReject = Yii::$app->request->post('claims_detail_ids_reject');
        $claimMasterId = Yii::$app->request->post('claim_master_id');
        if (($claimsDetailIdsApprove != "" || $claimsDetailIdsReject != "" ) && $claimMasterId != "") {
            $claimMaster = ClaimsMaster::findOne($claimMasterId);
            $claimMaster->authorizeResponse($claimsDetailIdsApprove, $claimsDetailIdsReject);
            FlashHandler::success('Record status updated!');
            return $this->redirect('authorize-claim');
        }

        $claimDetail = ClaimsDetail::find()
                ->join('INNER JOIN', 'claims_master', 'claims_master.claims_master_id = claims_detail.claim_master_id ')
                ->where("is_submitted=1 AND authorize_status=1 AND is_deleted=0 AND authorized_by=" . Yii::$app->user->identity->id)->groupBy("claim_master_id")
                ->all();

        $dataProviders = array();
        foreach ($claimDetail as $detail) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'AuthorizeClaim', $detail->claim_master_id);
            $this->displayDetails_dataProvider($dataProvider);
            array_push($dataProviders, $dataProvider);
        }

        return $this->render('AuthorizeClaim', [
                    'searchModel' => $searchModel,
                    'dataProviders' => $dataProviders,
        ]);
    }

    /**
     * *************************************** Render For PDF - MAIN FUNCTION - "Claim Form"
     * PDF
     */
    public function actionPrintClaimForm($claimsMasterId) {
        $model = ClaimsMaster::findOne($claimsMasterId);
        $this->displayDetails($model->claimsDetails);
        $htmlHeader = $this->renderPartial("claimformHeader", [
            'model' => $model
        ]);

        $htmlBody = "";

        // Travel claim's form differ from normal claim form
        if ($model->claim_type == "tra") {
            $htmlBody = $this->renderPartial("claimformBody_travel", [
                'model' => $model
            ]);
        } else {
            $htmlBody = $this->renderPartial("claimformBody", [
                'model' => $model
            ]);
        }
        $htmlFooter = $this->renderPartial("claimformFooter", [
            'model' => $model
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'serif',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
        ]);
        $mpdf->SetHeader($htmlHeader);
        $mpdf->WriteHTML($htmlBody);
        $mpdf->SetFooter($htmlFooter);
        return $mpdf->Output($model->claims_id . '.pdf', "I");
    }

    /**
     * *************************************** Render For PDF - MAIN FUNCTION - "Lost Receipt Form" 
     * PDF
     */
    public function actionPrintReceiptLostForm($claimsMasterId) {
        $models = ClaimsDetail::find()->where('claim_master_id=' . $claimsMasterId)->andWhere("receipt_lost=1")->all();

        $htmlHeader = $this->renderPartial("receiptLostFormHeader", [
            'models' => $models
        ]);

        $htmlBody = $this->renderPartial("receiptLostFormBody", [
            'models' => $models
        ]);

        $htmlFooter = $this->renderPartial("receiptLostFormFooter", [
            'models' => $models
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'serif',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
        ]);

        $mpdf->SetHeader($htmlHeader);
        $mpdf->SetFooter($htmlFooter);
        $mpdf->WriteHTML($htmlBody);

        return $mpdf->Output();
    }

    /**
     * *************************************** Render for View - Directors - Special Approval Page
     */
    public function actionDirectorSpecialApproval() {
        $claimIds = Yii::$app->request->post('claimIds');
        $approval = Yii::$app->request->post('approval');

        if ($claimIds != "" && $approval != "") {
            $claimDetail = new ClaimsDetail();
            $claimDetail->specialApprove($claimIds, $approval);
        }
        $searchModel = new ClaimsDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'directorSpecialApproval');
        $this->displayDetails_dataProvider($dataProvider);

        return $this->render('directorSpecialApproval', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClaimsDetail model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {

        $model = $this->findModel($id);
        $this->displayDetails($model);
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    /**
     * *************************************** Render & Action for Create / Update Claim Detail - Normal User
     * Creates a new ClaimsDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new ClaimsDetail();

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($model->processAndSave() == "yes") { // Yes meaning save and create next
                FlashHandler::success("Record Saved!");
            } else {
                return $this->redirect(['personal-claim', 'id' => $model->claims_detail_id]);
            }
        }

        $claimTypeList = \frontend\models\working\claim\RefClaimType::getDropDownList();
        $superior = Yii::$app->user->identity->superior;
        $newModel = new ClaimsDetail();
        return $this->render('createClaim', [
                    'model' => $newModel,
                    'claimTypeList' => $claimTypeList,
                    'superior' => $superior
        ]);
    }

    /**
     * ************************************** Render & Action for Create / Update - Normal User - Get Special Approval
     * Request for special approval from directors if the request is expired
     * @param type $id
     * @return type
     */
    public function actionGetSpecialApproval($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->special_approved = 1;
            $model->update(false);
            return $this->redirect(['personal-claim', 'id' => $id]);
        }

        return $this->render('specialApproval', [
                    'model' => $model,
        ]);
    }

    /**
     * ************************************** Action & Redirect for Update - Normal User - Submit Claim Items 
     * And generate a claim form
     */
    public function actionSubmitClaim() {

        $claimIds = Yii::$app->request->post('claimIds');
        $claimFamily = Yii::$app->request->post('claimFamily');

        $claimmaster = new ClaimsMaster();
        if ($claimmaster->submitClaims($claimIds, $claimFamily)) {
            FlashHandler::success("Claim Submitted!");
        }
        return $this->redirect('view-claimmaster-detail?claimsMasterId=' . $claimmaster->claims_master_id);
    }

    /**
     * ************************************** Action & Redirect for Update - Normal User - Edit Claim Items
     * Updates an existing ClaimsDetail model.
     * If update is successful, the browser will be redirected to the 'personal Claim's page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateClaim($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');

            if ($model->processAndSave()) {
                FlashHandler::success("Record Updated!");
            }
            return $this->redirect(['personal-claim', 'id' => $model->claims_detail_id]);
        }

        $claimTypeList = \frontend\models\working\claim\RefClaimType::getDropDownList();

        return $this->render('updateClaim', [
                    'model' => $model,
                    'claimTypeList' => $claimTypeList
        ]);
    }

    /**
     * ************************************** Action & Redirect for Insert - Normal User - Copy Claim Items
     * Copy from rejected claims, and then set the claim form to "Closed"
     */
    public function actionCopyClaimItems() {
        $claimsMasterId = Yii::$app->request->post('claimsMasterId');
        if ($claimsMasterId != "") {
            $claimsMaster = ClaimsMaster::findOne($claimsMasterId);
            $claimsMaster->copyItems();
            return $this->redirect('personal-claim');
        }
    }

    /**
     * ************************************** Action & Redirect for Delete - Normal User - Delete ClaimItems
     * Deletes an existing ClaimsDetail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->update();
        return $this->redirect(['personal-claim']);
    }

    /**
     * ************************************** (PROTECTED) Default Function - (MULTIPLE) - Find Claim Detail Active Record
     * Finds the ClaimsDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClaimsDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ClaimsDetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * ************************************** Default Function - (MULTIPLE) - Find Claim Detail Active Record
     * @param type $filename
     * @return type
     */
    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['claim_file_path'] . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    /**
     * ************************************** (PRIVATE) Function - (MULTIPLE) - Display all Claim Items Detail
     * If a claim item has multiple projects, detail and amount, then this function will make them into a string for display
     */
    private function displayDetails($model) {
        foreach ($model as $claimsDetail) {
            if (sizeof($claimsDetail['claimsDetailSubs']) > 1) {
                $claimsDetail->project_account = "";
                $claimsDetail->detail = "";
                foreach ($claimsDetail['claimsDetailSubs'] as $detail) {
                    $claimsDetail->detail .= "- " . $detail->detail . "<br/>";
                    $claimsDetail->project_account .= "- " . $detail->project_account . " (RM " . $detail->amount . ")" . "<br/>";
                }
            }
        }
    }

    private function displayDetails_dataProvider($dataProvider) {
        foreach ($dataProvider->getModels() as $claimsDetail) {
            if (sizeof($claimsDetail['claimsDetailSubs']) > 1) {
                $claimsDetail->project_account = "";
                $claimsDetail->detail = "";
                foreach ($claimsDetail['claimsDetailSubs']as $detail) {
                    $claimsDetail->detail .= "- " . $detail->detail . "<br/>";
                    $claimsDetail->project_account .= "- " . $detail->project_account . " (RM " . $detail->amount . ")" . "<br/>";
                }
            }
        }
    }

    /**
     * To display the project code, detail and amount if there are more than one
     * @param type $dataProvider
     */
    private function displayMasterDetails_dataProvider($dataProvider) {
        if ($dataProvider->getModels()) {
            foreach ($dataProvider->getModels()[0]['claimsDetails'] as $claimsDetail) {
                if (sizeof($claimsDetail['claimsDetailSubs']) > 1) {
                    $claimsDetail->project_account = "";
                    $claimsDetail->detail = "";
                    foreach ($claimsDetail['claimsDetailSubs']as $detail) {
                        $claimsDetail->detail .= "- " . $detail->detail . "<br/>";
                        $claimsDetail->project_account .= "- " . $detail->project_account . " (RM " . $detail->amount . ")" . "<br/>";
                    }
                }
            }
        }
    }

//
//    public function actionTestingonly() {
//        $claim = new ClaimsMaster();
//        return($claim->rejectClaimDocument(1593));
//    }
}
