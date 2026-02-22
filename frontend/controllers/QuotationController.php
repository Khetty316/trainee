<?php

namespace frontend\controllers;

use Yii;
use frontend\models\quotation\QuotationMasters;
use frontend\models\quotation\QuotationMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\quotation\QuotationDetails;

/**
 * QuotationController implements the CRUD actions for QuotationMasters model.
 */
class QuotationController extends Controller {

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
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all QuotationMasters models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);

        return $this->redirect('/');
    }

    /**
     * Updates an existing QuotationMasters model.
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

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuotationMasters model.
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
     * Finds the QuotationMasters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return QuotationMasters the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = QuotationMasters::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($id) {
        $model = $this->findModel($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['quotation_req_file_path'] . $model->id . '/' . $model->file_reference;
        return Yii::$app->response->sendFile($completePath, $model->file_reference, ['inline' => true]);
    }

    public function actionGetFileQuotations($id) {
        $model = \frontend\models\quotation\QuotationDetails::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['quotation_req_file_path'] . $model->quotation_master_id . '/' . $model->filename;
        return Yii::$app->response->sendFile($completePath, $model->filename, ['inline' => true]);
    }

    /**
     * Lists all QuotationMasters models.
     * @return mixed
     */
    public function actionStaffViewQuotationListPending() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'staff_personal_pending');

        return $this->render('staffViewQuotationListPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStaffViewQuotationList() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'staff_personal_all');

        return $this->render('staffViewQuotationList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuotationMasters model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionStaffViewQuotationDetail($id) {
        return $this->render('staffViewQuotationDetail', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new QuotationMasters model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionStaffRequestQuotation() {
        $model = new QuotationMasters();

        if ($model->load(Yii::$app->request->post()) && $model->processAndSave()) {
            FlashHandler::success("Request sent!");
            return $this->redirect(['staff-view-quotation-detail', 'id' => $model->id]);
        }

        return $this->render('staffRequestQuotation', [
                    'model' => $model,
        ]);
    }

    public function actionStaffActionForwardToManager() {
        $id = Yii::$app->request->post('QuotationMasters')['id'];
        if (Yii::$app->request->isPost) {
            $quotationMaster = QuotationMasters::findOne($id);
            if ($quotationMaster->forwardToManager(Yii::$app->request->post())) {
                if ($quotationMaster->requestor_approval == 1) {
                    FlashHandler::success("Forwarded to manager");
                } else {
                    FlashHandler::success("Rejected");
                }
            }
        }

        return $this->redirect(['staff-view-quotation-detail', 'id' => $id]);
    }

    /**
     * ******************************************************* PROCUREMENT *******************************************************
     * ********************************************************* START *******************************************************
     * @return type
     */

    /**
     * View Pending List
     * @return type
     */
    public function actionProcViewQuotationListPending() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procurement_pending');

        return $this->render('procViewQuotationListPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageKey' => 1
        ]);
    }

    public function actionProcViewQuotationListPo() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procurement_po');

        return $this->render('procViewQuotationListPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageKey' => 2
        ]);
    }

    public function actionProcViewQuotationListAll() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procurement_all');

        return $this->render('procViewQuotationListPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pageKey' => 3
        ]);
    }

    public function actionProcViewProcessQuotationDetail($id) {
        $model = $this->findModel($id);
//        if ($model->proc_approval == 0) {
        return $this->render('procViewProcessQuotationDetail', [
                    'model' => $model,
        ]);
//        } else {
//            return $this->redirect(['proc-view-quotation-list-pending']);
//        }
    }

    public function actionProcActionAddQuotationFiles() {
        $id = Yii::$app->request->post('QuotationMasters')['id'];
        $quotationMaster = QuotationMasters::findOne($id);

        if ($quotationMaster->load(Yii::$app->request->post())) {
            if ($quotationMaster->procAddQuotationFiles()) {
                FlashHandler::success("Files added!");
            }
        }
        return $this->redirect(['proc-view-process-quotation-detail', 'id' => $id]);
    }

    public function actionProcActionForwardToRequestor() {
        $id = Yii::$app->request->post('QuotationMasters')['id'];
        $quotationMaster = QuotationMasters::findOne($id);


        if (Yii::$app->request->post('isSaveOnly') == 1) {
            $quotationMaster->forwardToRequestor(Yii::$app->request->post(), 1);
            FlashHandler::success("Saved");
            return $this->redirect(['proc-view-process-quotation-detail', 'id' => $id]);
        } else if ($quotationMaster->forwardToRequestor(Yii::$app->request->post())) {
            FlashHandler::success("Forwarded to requestor!");
        }

        return $this->redirect(['proc-view-quotation-list-pending']);
    }

    public function actionProcActionRemoveQuotationAjax() {
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $quotationDetail = QuotationDetails::findOne($id);
            if ($quotationDetail->delete()) {
                $filePath = Yii::$app->params['quotation_req_file_path'] . $quotationDetail->quotation_master_id . "/";
                if ($quotationDetail->filename && file_exists($filePath . $quotationDetail->filename)) {
                    unlink($filePath . $quotationDetail->filename);
                }
            }

            return json_encode(array("msg" => "File removed!!"));
        } else {
            return json_encode(array("msg" => "Fail to remove.."));
        }
    }

    /**
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ PROCUREMENT ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ENDS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * 
     * 
     * 
     * ******************************************************* MANAGER *******************************************************
     * ********************************************************* START *******************************************************
     * @return type
     */
    public function actionMgrViewQuotationListPending() {
        $searchModel = new QuotationMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'manager_pending');

        return $this->render('mgrViewQuotationListPending', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMgrViewProcessQuotationDetail($id) {
        $model = $this->findModel($id);
        if ($model->manager_approval == 0) {
            return $this->render('mgrViewProcessQuotationDetail', [
                        'model' => $model,
            ]);
        } else {
            return $this->redirect(['mgr-view-quotation-list-pending']);
        }
    }

    public function actionMgrActionApprove() {
        $id = Yii::$app->request->post('QuotationMasters')['id'];
        if (Yii::$app->request->isPost) {
            $quotationMaster = QuotationMasters::findOne($id);
            if ($quotationMaster->managerApprove(Yii::$app->request->post())) {
                if ($quotationMaster->manager_approval == $quotationMaster::APPROVE_YES) {
                    FlashHandler::success("Approved");
                } else {
                    FlashHandler::success("Rejected");
                }
            } else {
                FlashHandler::err("Update Fail. Someone might have already took action on this request.");
            }
        }

        return $this->redirect(['mgr-view-quotation-list-pending']);
    }

    /**
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ MANAGER ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ENDS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     */
}
