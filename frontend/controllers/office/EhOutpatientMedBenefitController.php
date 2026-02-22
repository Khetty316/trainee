<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\employeeHandbook\EhOutpatientMedMaster;
use frontend\models\office\employeeHandbook\EhOutpatientMedMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use frontend\models\office\employeeHandbook\EhOutpatientMedDetail;
use common\models\myTools\FlashHandler;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * EhOutpatientMedBenefitController implements the CRUD actions for EhOutpatientMedMaster model.
 */
class EhOutpatientMedBenefitController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/eh-outpatient-med-benefit/');
    }

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
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Eh_Super],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new EhOutpatientMedMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($ehId, $superUser) {
        $master = EhOutpatientMedMaster::find()->where(['eh_master_id' => $ehId])->one();
        $details = [];  // Initialize as empty array, not object

        if ($master !== null) {
            $details = EhOutpatientMedDetail::find()->where(['eh_outpatient_med_master_id' => $master->id])->all();
        }

        $eh = EmployeeHandbookMaster::findOne($ehId);

        return $this->render('view', [
                    'master' => $master,
                    'eh' => $eh,
                    'details' => $details,
                    'superUser' => $superUser
        ]);
    }

    public function actionCreate() {
        $model = new EhOutpatientMedMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($ehId) {
        if (Yii::$app->request->post()) {
            // Get the posted value correctly
            $amount = Yii::$app->request->post('amount_per_receipt');
            $monthly_amount = Yii::$app->request->post('monthly_limit');

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $master = EhOutpatientMedMaster::findOne(['eh_master_id' => $ehId]);

                if ($master === null) {
                    $master = new EhOutpatientMedMaster();
                    $master->eh_master_id = $ehId;
                    $master->monthly_limit = $monthly_amount;
                    if (!$master->save()) {
                        throw new \Exception('Failed to create master record');
                    }
                } else {
                    $master->monthly_limit = $monthly_amount;
                    if (!$master->save()) {
                        throw new \Exception('Failed to update master record');
                    }
                    EhOutpatientMedDetail::deleteAll([
                        'eh_outpatient_med_master_id' => $master->id,
                        'eh_master_id' => $ehId
                    ]);
                }

                if (!empty($amount)) {
                    $detail = new EhOutpatientMedDetail();
                    $detail->eh_outpatient_med_master_id = $master->id;
                    $detail->eh_master_id = $ehId;
                    $detail->amount_per_receipt = $amount;

                    if (!$detail->save()) {
                        throw new \Exception("Failed to save detail");
                    }
                }

                $transaction->commit();
                FlashHandler::success("Outpatient medical benefit updated successfully.");
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }

        return $this->redirect(['/office/employee-handbook/view', 'id' => $ehId]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = EhOutpatientMedMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
