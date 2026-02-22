<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\po\PurchaseOrderMaster;
use frontend\models\working\po\PurchaseOrderMasterSearch;
use yii\web\Controller;
use frontend\models\working\project;
use common\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * POController implements the CRUD actions for PurchaseOrderMaster model.
 */
class PoController extends Controller {

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
     * Lists all PurchaseOrderMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PurchaseOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


//        $userList = User::getActiveDropDownList();
//        $projectList = project\MasterProjects::getActiveDropDownList();
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();


        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'addressList' => $addressList
        ]);
    }

    /**
     * Displays a single PurchaseOrderMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PurchaseOrderMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new PurchaseOrderMaster();
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->processAndSave()) {
                if ($model->quotation_master_id) {
                    $quotation = \frontend\models\quotation\QuotationMasters::findOne($model->quotation_master_id);
                    $quotation->setComplete();
                }

                \common\models\myTools\FlashHandler::success("P.O. uploaded.");
                return $this->redirect(['view', 'id' => $model->po_id]);
            }
        }

        $userList = User::getActiveDropDownList();
        $projectList = project\MasterProjects::getAutoCompleteList();
        $quotationList = \frontend\models\quotation\QuotationMasters::getAutoCompleteList_activeOnly();
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();
        
        $quotationId = Yii::$app->request->get('quotationId');
        if ($quotationId) {
            $model->quotation_master_id=$quotationId;
        }
        return $this->render('create', [
                    'model' => $model,
                    'userList' => $userList,
                    'projectList' => $projectList,
                    'addressList' => $addressList,
                    'quotationList' => $quotationList
        ]);
    }

    /**
     * Updates an existing PurchaseOrderMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->po_date) {
                $model->po_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($model->po_date);
            }
            if ($model->save()) {
                \common\models\myTools\FlashHandler::success("P.O. updated");
                return $this->redirect(['view', 'id' => $model->po_id]);
            }
        }
        $userList = User::getActiveDropDownList();
        $projectList = project\MasterProjects::getAutoCompleteList();
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();

        return $this->render('update', [
                    'model' => $model,
                    'userList' => $userList,
                    'projectList' => $projectList,
                    'addressList' => $addressList
        ]);
    }

    /**
     * Deletes an existing PurchaseOrderMaster model.
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
     * Finds the PurchaseOrderMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseOrderMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PurchaseOrderMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['po_file_path'] . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    /**
     * 
     */
    public function actionProcTrackingList() {
        $searchModel = new PurchaseOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pending');
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();

        return $this->render('procTrackingList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'addressList' => $addressList
        ]);
    }

    public function actionProcTrackingListIndividual() {
        $searchModel = new PurchaseOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'procTrackingListIndividual');
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();

        return $this->render('procTrackingListIndividual', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'addressList' => $addressList
        ]);
    }

    public function actionViewIndividual($id) {
        return $this->render('viewIndividual', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionProcTrackingListAll() {
        $searchModel = new PurchaseOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $addressList = \frontend\models\common\RefAddress::getActiveDropDownList();

        return $this->render('procTrackingListAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'addressList' => $addressList
        ]);
    }

    public function actionReceivedOnSite() {
        $model = $this->findModel(Yii::$app->request->post('poId'));
        $pageName = Yii::$app->request->post('pageName');
        $model->po_receive_status = 1;
        $model->onsite_receive_by = Yii::$app->user->id;
        if ($model->update(false)) {
            \common\models\myTools\FlashHandler::success("Received!");
        } else {
            \common\models\myTools\FlashHandler::err("Fail to receive doc. Kindly contact IT Department for support.");
        }
        return $this->redirect([$pageName]);
    }

}
