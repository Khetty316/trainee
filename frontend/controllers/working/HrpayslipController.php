<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\hrpayslip\HrPayslip;
use frontend\models\working\hrpayslip\HrPayslipSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\common\AuditTrailPageVisit;

/**
 * HrpayslipController implements the CRUD actions for HrPayslip model.
 */
class HrpayslipController extends Controller {

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
                        'roles' => ['HR2'],
                    ]
                ],
            ]
        ];
    }

    /**
     * Displays a single HrPayslip model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        AuditTrailPageVisit::addRecord('hrpayslip-view?id=' . $id, Yii::$app->user->id);
        $allowanceList = \frontend\models\common\RefPayAllowance::find()->asArray()->all();
        $OTList = \frontend\models\common\RefPayOvertime::find()->asArray()->all();
        $travelClaims = \frontend\models\working\claim\ClaimsMaster::find()->where('claimant_id=1 AND claim_type="tra" AND claims_status=5')->asArray()->all();
        return $this->render('viewHrpayslip', [
                    'model' => $this->findModel($id),
                    'allowanceList' => $allowanceList,
                    'OTList' => $OTList,
                    'travelClaims' => $travelClaims
        ]);
    }

    /**
     * Creates a new HrPayslip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new HrPayslip();

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            FlashHandler::success("Payslip Created");

            if (Yii::$app->request->post('nextStaffId') == "") {
                return $this->redirect(['index-by-staff', 'userId' => $model->user_id]);
            } else {
                return $this->redirect(['create', 'userId' => Yii::$app->request->post('nextStaffId')]);
            }
        }
        $user = \common\models\User::findOne(Yii::$app->request->get('userId'));
        $allowanceList = \frontend\models\common\RefPayAllowance::find()->asArray()->all();
        $OTList = \frontend\models\common\RefPayOvertime::find()->asArray()->all();
        $travelClaims = \frontend\models\working\claim\ClaimsMaster::find()->where('claimant_id=' . $user->id . ' AND claim_type="tra" AND claims_status=5')->asArray()->all();
        $nextUser = \common\models\User::getNextUserByStaffId($user->staff_id);

        $lastRecord = HrPayslip::find()->where('user_id=' . $user->id)->orderBy(['pay_year' => SORT_DESC, 'pay_month' => SORT_DESC])->limit(1)->one();
        $giftList = \frontend\models\common\RefPayGift::find()->all();
        return $this->render('createHrpayslip', [
                    'model' => $model,
                    'user' => $user,
                    'allowanceList' => $allowanceList,
                    'OTList' => $OTList,
                    'lastRecord' => $lastRecord,
                    'travelClaims' => $travelClaims,
                    'nextUser' => $nextUser,
                    'giftList' => $giftList
        ]);
    }

    /**
     * Updates an existing HrPayslip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            FlashHandler::success("Payslip Edited");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $allowanceList = \frontend\models\common\RefPayAllowance::find()->asArray()->all();
        $OTList = \frontend\models\common\RefPayOvertime::find()->asArray()->all();

        $giftList = \frontend\models\common\RefPayGift::find()->all();
        return $this->render('updateHrpayslip', [
                    'model' => $model,
                    'allowanceList' => $allowanceList,
                    'OTList' => $OTList,
                    'giftList' => $giftList
        ]);
    }

    /**
     * Deletes an existing HrPayslip model.
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
     * Finds the HrPayslip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HrPayslip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = HrPayslip::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * *************************************** INDEX
     * Lists all HrPayslip models.
     * @return mixed
     */
    public function actionIndex() {
        AuditTrailPageVisit::addRecord('hrpayslip-index', Yii::$app->user->id);

        $searchModel = new \frontend\models\sysadmin\user\UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'payrollList');

        return $this->render('indexHrpayroll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexByStaff($userId) {
        AuditTrailPageVisit::addRecord('hrpayslip-index-by-staff', Yii::$app->user->id);

        $searchModel = new HrPayslipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'single', array($userId));

        $user = \common\models\User::findOne($userId);

        return $this->render('indexUserHrpayslip', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'user' => $user
        ]);
    }

    /**
     * *************************************** INDEX 2
     * @return type
     */
    public function actionIndexHrpayslip() {
        AuditTrailPageVisit::addRecord('hrpayslip-index-hrpayslip', Yii::$app->user->id);


        $year = 0;
        $month = 0;
        $selectedMonthYear = "";
        if (Yii::$app->request->get('selectedMonthYear')) {
            $selectedMonthYear = Yii::$app->request->get('selectedMonthYear');

            $pos = strpos($selectedMonthYear, "-");
            $year = substr($selectedMonthYear, $pos + 1);
            $month = substr($selectedMonthYear, 0, $pos);
        }
        $searchModel = new HrPayslipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'generate-payslip', array($year, $month));

        $monthYearSel = \yii\helpers\ArrayHelper::map((new \yii\db\Query())
                                ->select(["CONCAT(MONTHNAME(STR_TO_DATE(pay_month, '%m')),'-',pay_year) AS label,CONCAT(pay_month,'-',pay_year) AS val"])
                                ->from('hr_payslip')
                                ->orderBy(['pay_year' => SORT_DESC, 'pay_month' => SORT_DESC])
                                ->all(), 'val', 'label');

        return $this->render('indexHrpaylist', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'monthYearSel' => $monthYearSel,
                    'selectedMonthYear' => $selectedMonthYear,
                    'year' => $year,
                    'month' => $month
        ]);
    }

    public function actionBulkReleasePayslip() {

        AuditTrailPageVisit::addRecord('hrpayslip-bulk-release-payslip', Yii::$app->user->id);
        $selectedMonthYear = Yii::$app->request->post('selectedMonthYear');
        if (Yii::$app->request->post()) {
            $payslipIds = Yii::$app->request->post('selection');

            if ($payslipIds) {
                $this->releasePayslipPdf(implode(',', $payslipIds));
                FlashHandler::success("Payslip Released");
            }
        }
        return $this->redirect(['index-hrpayslip',
                    'selectedMonthYear' => $selectedMonthYear,
        ]);
    }

    /**
     * HR Generate Payslip in PDF, with default HR password
     */
    public function actionHrViewPayslipPdf($payslipId) {
        AuditTrailPageVisit::addRecord('hrpayslip-hr-view-payslip-pdf?payslipId=' . $payslipId, Yii::$app->user->id);
        $model = HrPayslip::findOne($payslipId);
        $mpdf = $this->generatePdf($model);
        $mpdf->SetProtection(array('print'), Yii::$app->params['hr_common_password']);
        return $mpdf->Output("Payslip_hr" . '.pdf', "I");
    }

    /**
     * *************************************** HR Release payslip in bulk
     * PDF
     */
    public function releasePayslipPdf($payslipIds) {
        $payslipIdArr = explode(",", $payslipIds);
        foreach ($payslipIdArr as $payslipId) {
            $model = HrPayslip::findOne($payslipId);
            $mpdf = $this->generatePdf($model);
            $employee = $model->user;
            $mpdf->SetProtection(array('print'), $employee->staff_id . substr($employee->ic_no, -4));

            $dir = Yii::$app->params['hr_file_path'] . $model->user_id . "/temp/";
            $filename = $model->pay_year . "-" . $model->pay_month . ".pdf";
            \common\models\myTools\MyCommonFunction::mkDirIfNull($dir);
            $mpdf->Output($dir . $filename);

            $model->pdf_released = 1;
            $model->update(false);

            // Create new record in Employee HR
            $hrDoc = new \frontend\models\working\hrdoc\HrEmployeeDocuments();
            $hrDoc->processAndSaveSingle($model->user_id, $dir, $filename);
        }
    }

    /**
     * Main PDF Generator
     * @param type $model
     * @return \Mpdf\Mpdf
     */
    private function generatePdf($model) {
        $companyDetail = \yii\helpers\ArrayHelper::map(\frontend\models\common\RefCompanyDetails::find()->asArray()->all(), 'code', 'value');
        $htmlBody = $this->renderPartial("pdf_payslip", [
            'model' => $model,
            'companyDetail' => $companyDetail
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'Calibri',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
        ]);

        $mpdf->WriteHTML($htmlBody);
        return $mpdf;
    }

    public function actionGetMonthlySummary($month, $year) {
        $model = new \frontend\models\working\hrpayslip\HrPayslipSummary();
        $model->loadMonthlySummary($month, $year);
        return $this->renderPartial('viewMonthlySummary', [
                    'model' => $model,
        ]);
    }

    public function actionTest($month, $year) {
//        $model = HrPayslip::findOne($payslipId);
//        $companyDetail = \yii\helpers\ArrayHelper::map(\frontend\models\common\RefCompanyDetails::find()->asArray()->all(), 'code', 'value');
//        return $this->render("pdf/payslipHeader", [
//                    'model' => $model,
//                    'companyDetail' => $companyDetail
//        ]);
        $model = new \frontend\models\working\hrpayslip\HrPayslipSummary();
    }

}
