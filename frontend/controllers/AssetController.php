<?php

namespace frontend\controllers;

use Yii;
use frontend\models\asset\AssetMaster;
use frontend\models\asset\AssetMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\asset\AssetTracking;
use common\models\myTools\FlashHandler;
use frontend\models\asset\AssetService;
use frontend\models\common\RefAssetApprovalStatus;
use frontend\models\asset\AssetTransferRequest;

include_once( Yii::getAlias('@webroot') . "/library/phpqrcode/qrlib.php");

/**
 * AssetController implements the CRUD actions for AssetMaster model.
 */
class AssetController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['asset-on-hand', 'index', 'dashboard', 'index-asset-super', 'view-asset-super', 'super-transfer', 'super-transfer-cancel'],
                'rules' => [
                    [
                        'actions' => ['asset-checking'],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                    [
                        'actions' => ['index-asset-super', 'view-asset-super', 'super-transfer', 'super-transfer-cancel'],
                        'allow' => true,
                        'roles' => ['SYSADMIN1'],
                    ],
                    [
                        'actions' => ['asset-on-hand'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AssetMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AssetMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('viewAsset', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AssetMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAsset() {
        $model = new AssetMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('createAsset', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing AssetMaster model.
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
     * Deletes an existing AssetMaster model.
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
     * Finds the AssetMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AssetMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = AssetMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * ** 
     * ***  
     * ****************************************************** Common Functions Start *******************************************************
     */

    /**
     * Get image of the asset
     * @param type $assetId
     * @return type
     */
    public function actionGetImage($assetId) {
        $asset = AssetMaster::findOne($assetId);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['asset_folder'] . $asset->id . "/" . $asset->file_image;
        return Yii::$app->response->sendFile($completePath, $asset->file_image, ['inline' => true]);
    }

    public function actionGetInvoice($assetId) {
        $asset = AssetMaster::findOne($assetId);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['asset_folder'] . $asset->id . "/" . $asset->file_invoice_image;
        return Yii::$app->response->sendFile($completePath, $asset->file_invoice_image, ['inline' => true]);
    }

    /**
     * Generating QR
     * @param type $assetIdxNo
     * @return type
     */
    public function actionGenerateQr($assetIdxNo) {
        $path = Yii::$app->request->absoluteUrl;
        $hostName = Yii::$app->request->hostName;
        if ($hostName == Yii::$app->params['application_hostname']) {
            $hostName = 'www.' . $hostName;
        }
//\yii\helpers\Url::base(true)
        $path = $_SERVER["REQUEST_SCHEME"] . '://' . $hostName . '/asset/asset-checking';
        $params = 'assetIdxNo=' . $assetIdxNo;
        $fullPath = $path . '?' . $params;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        return $response->content = \QRcode::png($fullPath);
    }

    /**
     * Function called when QR is scanned
     * @param type $assetIdxNo
     * @return type
     */
    public function actionAssetChecking($assetIdxNo) {
        $model = AssetMaster::find()->where(['asset_idx_no' => $assetIdxNo])->one();

        if (Yii::$app->user->identity) {
            return $this->redirect(['view-personal-asset', 'id' => $model->id]);
        } else {
            return $this->render('_viewAssetDetailView', [
                        'model' => $model,
                        'isGuest' => true
            ]);
        }
    }

    public function actionGetSubCategoryDropdown($categoryId) {

        $subCategory = \frontend\models\common\RefAssetSubCategory::findAll(['asset_category_id' => $categoryId]);
        return json_encode(\yii\helpers\ArrayHelper::map($subCategory, "id", "name"));
    }

    /**
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Common Functions Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * 
     * 
     * 
     */
    //******************************************************* Personal Asset Start *******************************************************

    /**
     * Lists all AssetMaster models.
     * @return mixed
     */
    public function actionAssetOnHand() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetOnHand');

        return $this->render('assetOnHand', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssetPendingReceive() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetPendingReceive');

        return $this->render('assetPendingReceive', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssetPendingRegister() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetPendingRegister');

        return $this->render('assetPendingRegister', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssetAll() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetAll');

        return $this->render('assetAll', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateAssetPersonal() {
        $model = new AssetMaster();
        $modelTracking = new AssetTracking();
        if ($model->load(Yii::$app->request->post()) && $modelTracking->load(Yii::$app->request->post())) {

            if (!$model->purchased_by) {
                $model->purchased_by = Yii::$app->user->id;
            }
            $model->fileImage = \yii\web\UploadedFile::getInstance($model, 'fileImage');
            $model->fileInvoiceImage = \yii\web\UploadedFile::getInstance($model, 'fileInvoiceImage');
            $model->active_sts = 0;
            $model->approval_status = RefAssetApprovalStatus::STATUS_PENDING;
            $model->processAndSave();
            $modelTracking->asset_id = $model->id;
            $modelTracking->receive_user = Yii::$app->user->id;
            $modelTracking->receive_condition = $model->condition;
            $modelTracking->request_status = $modelTracking::STATUS_ACCEPT;
            $modelTracking->active_status = 1;
            if ($modelTracking->processAndSave()) {
                FlashHandler::success("Asset Registered!");
            }

            return $this->redirect(['asset-pending-register']);
        }

        $model->purchased_by = Yii::$app->user->id;
        return $this->render('createAssetPersonal', [
                    'model' => $model,
                    'modelTracking' => $modelTracking
        ]);
    }

    public function actionCancelAssetPersonal($id) {

        if (Yii::$app->request->post()) {
            $model = AssetMaster::findOne($id);
            if ($model->setCancelAndUpdate()) {
                FlashHandler::success("Asset registration cancelled!");
            } else {
                FlashHandler::err("Unable to cancel. Asset approval status outdated.");
            }
        }
        return $this->redirect('asset-pending-register');
    }

    public function actionViewPersonalAsset($id) {

        $model = $this->findModel($id);
        $currentTracking = $model->getAssetTrackingsActive();
        $pendingTracking = $model->getAssetTrackingsPending();
        $requestTransfer = $model->getAssetRequestTransfer();

//        if ($currentTracking['receive_user'] != Yii::$app->user->id && $pendingTracking['receive_user'] != Yii::$app->user->id) {
//            return $this->redirect('asset-on-hand');
//        }
        $assetService = AssetService::findAll(['asset_id' => $id]);

        return $this->render('viewPersonalAsset', [
                    'model' => $model,
                    'currentTracking' => $currentTracking,
                    'pendingTracking' => $pendingTracking,
                    'assetService' => $assetService,
                    'requestTransfer' => $requestTransfer
        ]);
    }

    public function actionEditPendingAsset($id) {

        $model = $this->findModel($id);
        if ($model->approval_status == RefAssetApprovalStatus::STATUS_PENDING) {
            if ($model->load(Yii::$app->request->post())) {
                $model->fileImage = \yii\web\UploadedFile::getInstance($model, 'fileImage');
                $model->fileInvoiceImage = \yii\web\UploadedFile::getInstance($model, 'fileInvoiceImage');
                if ($model->processAndSave()) {
                    FlashHandler::success("Asset edited!");
                    return $this->redirect(['view-asset-pending-register', 'assetId' => $model->id]);
                }
            }

            $model->purchased_by = Yii::$app->user->id;
            return $this->render('editAssetPersonal', [
                        'model' => $model,
            ]);
        } else {
            FlashHandler::err("Unable to cancel. Asset approval status outdated.");
            return $this->redirect('asset-pending-register');
        }
    }

//    public function actionViewGuestAsset($assetIdxNo) {
//        $model = AssetMaster::find()->where(['asset_idx_no' => $assetIdxNo])->one();
//        $currentTracking = $model->getAssetTrackingsActive();
//        $pendingTracking = $model->getAssetTrackingsPending();
//        return $this->render('viewPersonalAsset', [
//                    'model' => $model,
//                    'currentTracking' => $currentTracking,
//                    'pendingTracking' => $pendingTracking
//        ]);
//    }

    public function actionPersonalTransfer() {
        $modelTracking = new AssetTracking();
        if ($modelTracking->load(Yii::$app->request->post())) {

            // Checking before proceed to save
            // If have other pending record then stop
            $checkTracking = AssetTracking::findAll(['asset_id' => $modelTracking->asset_id, 'request_status' => AssetTracking::STATUS_PENDING]);
            if ($checkTracking) {
                FlashHandler::err("Transfer fail, the asset is under transfer!");
            } else {
                $modelTracking->request_status = $modelTracking::STATUS_PENDING;
                $modelTracking->active_status = 0;
                if ($modelTracking->processAndSave()) {
                    FlashHandler::success("Transfer request sent!");
                }
            }
        }
        return $this->redirect(['view-personal-asset', 'id' => $modelTracking->asset_id]);
    }

    public function actionPersonalTransferCancel($id) {
        $modelTracking = AssetTracking::findOne($id);

        if (Yii::$app->request->post()) {
            if ($modelTracking->request_status == $modelTracking::STATUS_PENDING) {
                $modelTracking->request_status = $modelTracking::STATUS_CANCEL;
                if ($modelTracking->update(false)) {
                    FlashHandler::success("Transfer cancelled!");
                } else {
                    FlashHandler::err_getHelp();
                }
            }
        } else {
            FlashHandler::err_getHelp();
        }

        return $this->redirect(['view-personal-asset', 'id' => $modelTracking->asset_id]);
    }

    public function actionPersonalTransferReject($id) {
        $modelTracking = AssetTracking::findOne($id);

        if (Yii::$app->request->post()) {
            if ($modelTracking->request_status == $modelTracking::STATUS_PENDING) {
                $modelTracking->request_status = $modelTracking::STATUS_REJECT;
                if ($modelTracking->update(false)) {
                    FlashHandler::success("Transfer rejected!");
                } else {
                    FlashHandler::err_getHelp();
                }
            }
        } else {
            FlashHandler::err_getHelp();
        }
        FlashHandler::success("Transfer rejected!");

        return $this->redirect(['view-personal-asset', 'id' => $modelTracking->asset_id]);
    }

    public function actionPersonalReceive() {
        if (!Yii::$app->request->post()) {
            FlashHandler::err_getHelp();
            return $this->redirect(['view-personal-asset', 'id' => $modelTracking->asset_id]);
        }
        $assetId = Yii::$app->request->post('AssetTracking')['asset_id'];

        $asset = AssetMaster::findOne($assetId);
        $activeTracking = $asset->getAssetTrackingsActive();
        $pendingTracking = $asset->getAssetTrackingsPending();

        if ($pendingTracking && $pendingTracking->receive_user == Yii::$app->user->id && $pendingTracking->load(Yii::$app->request->post())) {
            $activeTracking->active_status = 0;
            $activeTracking->update(false);
            $pendingTracking->active_status = 1;
            $pendingTracking->request_status = AssetTracking::STATUS_ACCEPT;
            if ($pendingTracking->processAndSave()) {
                FlashHandler::success("Asset received");
            } else {
                FlashHandler::err("Fail to receive");
            }

            $asset->condition = $pendingTracking->receive_condition;
            $asset->update(false);


            $assetRequest = AssetTransferRequest::find()->where(['requestor' => $pendingTracking->receive_user, 'asset_id' => $pendingTracking->asset_id, 'request_status' => 'pending', 'active_status' => 1])->one();
            if ($assetRequest) {
                $assetRequest->setComplete();
            }
        } else {
            FlashHandler::err("Fail to receive");
        }

        return $this->redirect(['view-personal-asset', 'id' => $asset->id]);
    }

    public function actionPersonalService() {
        $model = new AssetService();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Service record added!");
            }
        } else {
            FlashHandler::err_getHelp();
        }

        return $this->redirect(['view-personal-asset', 'id' => $model->asset_id]);
    }

    public function actionPersonalRequestAsset() {
        $model = new AssetTransferRequest();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Request is sent!");
            }
        } else {
            FlashHandler::err_getHelp();
        }

        return $this->redirect(['view-personal-asset', 'id' => $model->asset_id]);
    }

    public function actionPersonalCancelRequestAsset($id) {

        if (Yii::$app->request->post()) {
            $model = AssetTransferRequest::findOne($id);
            if ($model->setCancel()) {
                FlashHandler::success("Request is cancelled!");
            } else {
                FlashHandler::err_getHelp();
            }
        }
        return $this->redirect(['view-personal-asset', 'id' => $model->asset_id]);
    }

    public function actionViewAssetPendingRegister($assetId) {
        $model = AssetMaster::findOne($assetId);

        return $this->render('viewAssetPendingRegister', [
                    'model' => $model]);
    }

    /**
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  Personal Asset Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * 
     * 
     * 
     */
    //******************************************************* Super User Asset Start *******************************************************
    public function actionIndexAssetSuper() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'indexAssetSuper');
        return $this->render('indexAssetSuper', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssetPendingRegisterSuper() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetPendingRegisterSuper');

        return $this->render('assetPendingRegisterSuper', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssetRejectRegisterSuper() {
        $searchModel = new AssetMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assetRejectRegisterSuper');

        return $this->render('assetRejectRegisterSuper', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewAssetPendingRegisterSuper($assetId) {
        $model = AssetMaster::findOne($assetId);
        return $this->render('viewAssetPendingRegisterSuper', [
                    'model' => $model
        ]);
    }

    public function actionSuperApproveAsset($id) {
        if (Yii::$app->request->post()) {
            $model = AssetMaster::findOne($id);
            if ($model->setApproveAndUpdate()) {
                FlashHandler::success("Asset registration approved!");
            } else {
                FlashHandler::err("Update fail. The asset already been approved.");
            }
        }
        return $this->redirect('asset-pending-register-super');
    }

    public function actionSuperRejectAsset($id) {
        if (Yii::$app->request->post()) {
            $model = AssetMaster::findOne($id);
            if ($model->setRejectAndUpdate()) {
                FlashHandler::success("Asset registration REJECTED!");
            } else {
                FlashHandler::err("Update fail. The asset already been approved.");
            }
        }
        return $this->redirect('asset-pending-register-super');
    }

    public function actionViewAssetSuper($id) {
        $model = $this->findModel($id);
        $currentTracking = $model->getAssetTrackingsActive();
        $pendingTracking = $model->getAssetTrackingsPending();
        $assetTrackings = AssetTracking::findAll(['asset_id' => $id]);

        $assetService = AssetService::findAll(['asset_id' => $id]);
        return $this->render('viewAssetSuper', [
                    'model' => $model,
                    'currentTracking' => $currentTracking,
                    'pendingTracking' => $pendingTracking,
                    'assetTrackings' => $assetTrackings,
                    'assetService' => $assetService
        ]);
    }

    public function actionSuperCreateAsset() {
        $model = new AssetMaster();
        $modelTracking = new AssetTracking();
        if ($model->load(Yii::$app->request->post()) && $modelTracking->load(Yii::$app->request->post())) {

//            if (!$model->purchased_by) {
//                $model->purchased_by = Yii::$app->user->id;
//            }
            $model->fileImage = \yii\web\UploadedFile::getInstance($model, 'fileImage');
            $model->fileInvoiceImage = \yii\web\UploadedFile::getInstance($model, 'fileInvoiceImage');
            $model->setApprove();
            $model->generateAssetIdxNo();
            $model->processAndSave();
            $modelTracking->asset_id = $model->id;
//            $modelTracking->receive_user = Yii::$app->user->id;
            $modelTracking->receive_condition = $model->condition;
            $modelTracking->request_status = $modelTracking::STATUS_ACCEPT;
            $modelTracking->active_status = 1;
            if ($modelTracking->processAndSave()) {
                FlashHandler::success("Asset Registered!");
            }

            return $this->redirect(['index-asset-super']);
        }

//        $model->purchased_by = Yii::$app->user->id;
        return $this->render('createAssetSuper', [
                    'model' => $model,
                    'modelTracking' => $modelTracking
        ]);
    }

    public function actionSuperEditAsset($id) {

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->fileImage = \yii\web\UploadedFile::getInstance($model, 'fileImage');
            $model->fileInvoiceImage = \yii\web\UploadedFile::getInstance($model, 'fileInvoiceImage');
            if ($model->processAndSave()) {
                FlashHandler::success("Asset edited!");
                return $this->redirect(['view-asset-super', 'id' => $model->id]);
            }
        }

        $model->purchased_by = Yii::$app->user->id;
        return $this->render('editAssetPersonal', [
                    'model' => $model,
        ]);
    }

    public function actionSuperTransfer() {
        $modelTracking = new AssetTracking();
        if ($modelTracking->load(Yii::$app->request->post())) {

            // Checking before proceed to save
            // If have other pending record then stop
            $checkTracking = AssetTracking::findAll(['asset_id' => $modelTracking->asset_id, 'request_status' => AssetTracking::STATUS_PENDING]);
            if ($checkTracking) {
                FlashHandler::err("Transfer fail, the asset is under transfer!");
            } else {
                $modelTracking->request_status = $modelTracking::STATUS_PENDING;
                $modelTracking->active_status = 0;
                if ($modelTracking->processAndSave()) {
                    FlashHandler::success("Transfer request sent!");
                }
            }
        }
        return $this->redirect(['view-asset-super', 'id' => $modelTracking->asset_id]);
    }

    public function actionSuperTransferCancel($id) {
        $modelTracking = AssetTracking::findOne($id);

        if (Yii::$app->request->post()) {
            if ($modelTracking->request_status == $modelTracking::STATUS_PENDING) {
                $modelTracking->request_status = $modelTracking::STATUS_CANCEL;
                if ($modelTracking->update(false)) {
                    FlashHandler::success("Transfer cancelled!");
                } else {
                    FlashHandler::err_getHelp();
                }
            }
        } else {
            FlashHandler::err_getHelp();
        }
        return $this->redirect(['view-asset-super', 'id' => $modelTracking->asset_id]);
    }

    public function actionSuperService() {
        $model = new AssetService();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                FlashHandler::success("Service record added!");
            }
        } else {
            FlashHandler::err_getHelp();
        }
        return $this->redirect(['view-asset-super', 'id' => $model->asset_id]);
    }

    /**
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Super User Asset Ends ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * 
     * 
     * 
     */
}
