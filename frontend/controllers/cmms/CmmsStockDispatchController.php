<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsStockDispatchMaster;
use frontend\models\cmms\CmmsStockDispatchMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\cmms\CmmsStockDispatchTrial;
use frontend\models\cmms\VCmmsStockDispatchTrial;
use frontend\models\cmms\VCmmsStockDispatchMaster;
use frontend\models\bom\StockDispatchMaster;
use frontend\models\bom\StockDispatchTrial;
use common\models\myTools\FlashHandler;
use frontend\models\cmms\CmmsWoMaterialRequestDetails;
use frontend\models\cmms\CmmsWoMaterialRequestMaster;
use yii\filters\AccessControl;
use common\modules\auth\models\AuthItem;

/**
 * CmmsStockDispatchController implements the CRUD actions for CmmsStockDispatchMaster model.
 */
class CmmsStockDispatchController extends Controller {

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
                'only' => ['change-receiver', 'return-dispatched-quantity', 'adjust-and-return-acknowledgement'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['change-receiver', 'return-dispatched-quantity', 'adjust-and-return-acknowledgement'],
                        'roles' => [AuthItem::ROLE_INVENTORY_Executive, AuthItem::ROLE_INVENTORY_Assistant, AuthItem::ROLE_INVENTORY_MaintenanceHead],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CmmsStockDispatchMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CmmsStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, null);

        return $this->render('masterList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMyPendingAcknowledgements() {
        $searchModel = new CmmsStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pending', null);
        return $this->render('myPendingAcknowledgements', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMyAcknowledgementList() {
        $searchModel = new CmmsStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'acknowledged', null);

        return $this->render('myAcknowledgementList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDispatchItemList($dispatchId, $status, $moduleIndex = null) {
        $dispatchMaster = VCmmsStockDispatchMaster::findOne($dispatchId);
        if (!$dispatchMaster) {
            FlashHandler::err("Dispatch Master not found.");
        }

        $pendingDispatch = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS, 'active_sts' => 1]);
        $pendingAdjust = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $pendingReturn = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);
        $confirmedDispatch = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS]);
        $confirmedAdjust = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $confirmedReturn = VCmmsStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);

        return $this->render('myLists', [
                    'dispatchMaster' => $dispatchMaster ?? null,
                    'pendingDispatch' => $pendingDispatch ?? null,
                    'pendingAdjust' => $pendingAdjust ?? null,
                    'pendingReturn' => $pendingReturn ?? null,
                    'confirmedDispatch' => $confirmedDispatch ?? null,
                    'confirmedAdjust' => $confirmedAdjust ?? null,
                    'confirmedReturn' => $confirmedReturn ?? null,
                    'status' => $status,
                    'moduleIndex' => $moduleIndex
        ]);
    }

    public function actionDispatchAcknowledgement($productionPanelId, $dispatchId) {
        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post('dispatch') ?? null;
            if ($postData === null) {
                FlashHandler::err("No item has been selected");
                return $this->redirect(['dispatch-item-list', 'dispatchId' => $dispatchId, 'status' => 'pending']);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->acknowledgement($postData);

                $dispatchMaster = CmmsStockDispatchMaster::findOne($dispatchId);
                $dispatchMaster->updateDispatchMasterStatus(StockDispatchTrial::DISPATCH_STATUS);
                $stockMasters = CmmsWoMaterialRequestMaster::find()->where(['wo_id' => $productionPanelId])->all();
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = CmmsWoMaterialRequestDetails::find()->where(['request_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
                    $stockMaster->save();
                }

                $transaction->commit();
                FlashHandler::success('Success');
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['my-pending-acknowledgements']);
        }
    }

    private function acknowledgement($postData) {
        foreach ($postData as $trialId => $detail) {

            $detail = CmmsWoMaterialRequestDetails::findOne($detail['detailId']);
            $trial = CmmsStockDispatchTrial::findOne($trialId);

            if (!$trial) {
                throw new \Exception("Invalid request for trial");
            }

            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                // Validate before acknowledging
                $currentAcknowledged = CmmsStockDispatchTrial::find()
                                ->where([
                                    'request_detail_id' => $detail->id,
                                    'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                                ])
                                ->sum('dispatch_qty') ?? 0;

                $newAcknowledged = $currentAcknowledged + $trial->dispatch_qty;
                if ($newAcknowledged > $detail->qty) {
                    throw new \Exception("Total acknowledged quantity exceeds allocated quantity for detail ID: " . $detail->id);
                }

                if ($newAcknowledged < 0) {
                    throw new \Exception("Acknowledged quantity cannot be negative for detail ID: " . $detail->id);
                }

                // Update inventory if tracking exists
                if ($detail->qty_stock_available !== null) {
                    // ✅ Handle both positive and negative dispatch_qty
                    if ($trial->dispatch_qty > 0) {
                        // Normal dispatch or adjustment increase
                        $detail->updateInventoryStockQty($trial->dispatch_qty, $detail, $detail->requestMaster->wo_type);
                    } else if ($trial->dispatch_qty < 0) {
                        // Return or adjustment decrease
                        $detail->revertInventoryStockQty(abs($trial->dispatch_qty), $detail, $detail->requestMaster->wo_type);
                    }
                    // If dispatch_qty == 0, no inventory change needed
                }

                // Update trial status to acknowledged
                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
            } else {
                // Un-acknowledging (toggle back)
                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
            }

            if (!$trial->save()) {
                throw new \Exception("Failed to update trial");
            }

            // Recalculate all quantities from trials
            $detail->updateAllQtyInStockDetail($detail, $detail->requestMaster->wo_type);
        }
    }

    public function actionChangeReceiver($dispatchId) {
        $model = CmmsStockDispatchMaster::findOne($dispatchId);
        $stockMaster = new CmmsWoMaterialRequestMaster();

        $receivers = \frontend\models\cmms\RefAssignedPic::find()->where(['corrective_work_order_master_id' => $model->wo_id])->all();
        if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
            $receivers = \frontend\models\cmms\RefAssignedPic::find()->where(['preventive_work_order_master_id' => $model->wo_id])->all();
        }
        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post('receiver');
            $model->received_by = $postData['id'] ?? null;
            if ($model->update()) {
                FlashHandler::success("Success");
            }
            return $this->redirect(['master-item-list', 'dispatchId' => $dispatchId]);
        }
        return $this->renderAjax('_changeReceiver', [
                    'model' => $model,
                    'receivers' => $receivers
        ]);
    }

    public function actionDispatchItemDetailList($dispatchId) {
        $searchModel = new \frontend\models\cmms\VCmmsStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $dispatchId);
        $model = CmmsStockDispatchMaster::findOne($dispatchId);

        return $this->render('dispatchItemDetailList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'model' => $model
        ]);
    }

    public function actionReturnDispatchedQuantity($dispatchId, $detailId) {
        $dispatchMaster = VCmmsStockDispatchMaster::findOne(['dispatch_id' => $dispatchId, 'request_detail_id' => $detailId]);
        $stockOutboundDetail = CmmsWoMaterialRequestDetails::findOne($detailId);
        $requestMaster = CmmsWoMaterialRequestMaster::findOne($stockOutboundDetail->request_master_id);
        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("dispatch");
            if ($stockOutboundDetail->processStockReturn($postData, $dispatchMaster, StockDispatchTrial::RETURN_STATUS)) {
                $stockOutboundDetail->updateMaterialRequestMasterStatus($requestMaster);
                FlashHandler::success("Success! The return dispatched quantity has been saved successfully.");
            }

            return $this->redirect(['dispatch-item-detail-list', 'dispatchId' => $dispatchId]);
        }
        return $this->renderAjax('_cmmsStockReturn', [
                    'dispatchMaster' => $dispatchMaster
        ]);
    }

    public function actionAdjustAndReturnAcknowledgement($dispatchId) {
        if (Yii::$app->request->post()) {
            $postDataReturn = Yii::$app->request->post('return') ?? null;

            if ($postDataReturn === null) {
                FlashHandler::err("No item has been selected");
                return $this->redirect(['dispatch-item-list', 'dispatchId' => $dispatchId, 'status' => 'pending']);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($postDataReturn !== null) {
                    $this->returnAcknowledgement($postDataReturn);
                }

                $dispatchMaster = CmmsStockDispatchMaster::findOne($dispatchId);
                $dispatchMaster->updateDispatchMasterStatus();
                $stockMasters = CmmsWoMaterialRequestMaster::find()->where(['wo_id' => $dispatchMaster->wo_id, 'wo_type' => $dispatchMaster->wo_type])->all();
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = CmmsWoMaterialRequestDetails::find()->where(['request_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
                    $stockMaster->save();
                }

                $transaction->commit();
                FlashHandler::success('Success');
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }

            return $this->redirect(['my-pending-acknowledgements']);
        }
    }

    private function returnAcknowledgement($postData) {
        foreach ($postData as $trialId => $detail) {
            $detail = CmmsWoMaterialRequestDetails::findOne($detail['detailId']);
            $trial = CmmsStockDispatchTrial::findOne($trialId);

            if (!$trial) {
                throw new \Exception("Invalid request for trial");
            }

            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                $currentAcknowledged = CmmsStockDispatchTrial::find()
                                ->where([
                                    'request_detail_id' => $detail->id,
                                    'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                                ])
                                ->sum('dispatch_qty') ?? 0;

                $newAcknowledged = $currentAcknowledged + $trial->dispatch_qty;

                if ($newAcknowledged < 0) {
                    throw new \Exception("Cannot return more than dispatched quantity for detail ID: " . $detail->id);
                }

                // Revert inventory if tracking exists (dispatch_qty is negative, so we revert the absolute value)
                if ($detail->qty_stock_available !== null) {
                    $detail->revertInventoryStockQty(abs($trial->dispatch_qty), $detail, $detail->requestMaster->wo_type);
                }

                // Update trial status to acknowledged
                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
            } else {
                // Toggle back to unacknowledged
                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;

                // Re-dispatch to inventory since we're un-acknowledging the return
                if ($detail->qty_stock_available !== null) {
                    $detail->updateInventoryStockQty(abs($trial->dispatch_qty), $detail, $detail->requestMaster->wo_type);
                }
            }

            if (!$trial->save()) {
                throw new \Exception("Failed to update trial");
            }

            // Recalculate all quantities from trials
            $detail->updateAllQtyInStockDetail($detail, $detail->requestMaster->wo_type);
        }
    }
    
    /**
     * Displays a single CmmsStockDispatchMaster model.
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
     * Creates a new CmmsStockDispatchMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CmmsStockDispatchMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing CmmsStockDispatchMaster model.
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
     * Deletes an existing CmmsStockDispatchMaster model.
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
     * Finds the CmmsStockDispatchMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsStockDispatchMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CmmsStockDispatchMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
