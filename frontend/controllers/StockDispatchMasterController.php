<?php

namespace frontend\controllers;

use Yii;
use frontend\models\bom\StockDispatchMaster;
use frontend\models\bom\StockDispatchMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\bom\StockDispatchTrial;
use common\models\myTools\FlashHandler;
use frontend\models\bom\StockOutboundMaster;
use frontend\models\bom\StockOutboundDetails;
use frontend\models\bom\VStockDispatchMaster;
use frontend\models\bom\VStockDispatchTrial;

/**
 * StockDispatchMasterController implements the CRUD actions for StockDispatchMaster model.
 */
class StockDispatchMasterController extends Controller {

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
        ];
    }

    /**
     * Lists all StockDispatchMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, null);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMasterItemList($dispatchId) {
        $dispatchMaster = VStockDispatchMaster::findOne($dispatchId);
        if (!$dispatchMaster) {
            FlashHandler::err("Dispatch Master not found.");
        }

        $pendingDispatch = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS, 'active_sts' => 1]);
        $pendingAdjust = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $pendingReturn = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);
        $confirmedDispatch = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS]);
        $confirmedAdjust = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $confirmedReturn = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);

        return $this->render('masterList', [
                    'dispatchMaster' => $dispatchMaster ?? null,
                    'pendingDispatch' => $pendingDispatch ?? null,
                    'pendingAdjust' => $pendingAdjust ?? null,
                    'pendingReturn' => $pendingReturn ?? null,
                    'confirmedDispatch' => $confirmedDispatch ?? null,
                    'confirmedAdjust' => $confirmedAdjust ?? null,
                    'confirmedReturn' => $confirmedReturn ?? null,
                    'status' => null
        ]);
    }

    public function actionChangeReceiver($dispatchId) {
        $model = StockDispatchMaster::findOne($dispatchId);
        $stockMaster = new StockOutboundMaster();
        $receivers = $stockMaster->getReceivers($model->production_panel_id);

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

    public function actionMyPendingAcknowledgements() {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'pending', null);

        return $this->render('myPendingAcknowledgements', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMyAcknowledgementList() {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'acknowledged', null);

        return $this->render('myAcknowledgementList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDispatchItemList($dispatchId, $status) {
        $dispatchMaster = VStockDispatchMaster::findOne($dispatchId);
        if (!$dispatchMaster) {
            FlashHandler::err("Dispatch Master not found.");
        }

        $pendingDispatch = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS, 'active_sts' => 1]);
        $pendingAdjust = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $pendingReturn = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);
        $confirmedDispatch = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::DISPATCH_STATUS]);
        $confirmedAdjust = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::ADJUST_STATUS, 'active_sts' => 1]);
        $confirmedReturn = VStockDispatchTrial::findAll(['stock_dispatch_master_id' => $dispatchMaster->dispatch_id, 'current_sts' => [StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED], 'status' => StockDispatchTrial::RETURN_STATUS, 'active_sts' => 1]);

        return $this->render('myLists', [
                    'dispatchMaster' => $dispatchMaster ?? null,
                    'pendingDispatch' => $pendingDispatch ?? null,
                    'pendingAdjust' => $pendingAdjust ?? null,
                    'pendingReturn' => $pendingReturn ?? null,
                    'confirmedDispatch' => $confirmedDispatch ?? null,
                    'confirmedAdjust' => $confirmedAdjust ?? null,
                    'confirmedReturn' => $confirmedReturn ?? null,
                    'status' => $status
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
                $dispatchMaster = StockDispatchMaster::findOne($dispatchId);
                $dispatchMaster->updateDispatchMasterStatus(StockDispatchTrial::DISPATCH_STATUS);
                $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
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

    public function actionAdjustAndReturnAcknowledgement($productionPanelId, $dispatchId) {
        if (Yii::$app->request->post()) {
            $postDataAdjust = Yii::$app->request->post('adjust') ?? null;
            $postDataReturn = Yii::$app->request->post('return') ?? null;
            if ($postDataAdjust === null && $postDataReturn === null) {
                FlashHandler::err("No item has been selected");
                return $this->redirect(['dispatch-item-list', 'dispatchId' => $dispatchId, 'status' => 'pending']);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($postDataAdjust !== null) {
                    $this->acknowledgement($postDataAdjust);
                }

                if ($postDataReturn !== null) {
                    $this->returnAcknowledgement($postDataReturn);
                }

                $dispatchMaster = StockDispatchMaster::findOne($dispatchId);
                $dispatchMaster->updateDispatchMasterStatus();
                $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
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

    //before inventory module
//    private function acknowledgement($postData) {
//        foreach ($postData as $trialId => $detail) {
//            $detail = StockOutboundDetails::findOne($detail['detailId']);
//            $trial = StockDispatchTrial::findOne($trialId);
//            if (!$trial) {
//                throw new \Exception("Invalid request for trial");
//            }
//
//            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
//                $detail->unacknowledged_qty = ($detail->unacknowledged_qty - abs($trial->dispatch_qty));
//                $detail->dispatched_qty = ($detail->dispatched_qty + abs($trial->dispatch_qty));
//                $detail->fully_dispatch_status = ($detail->qty == $detail->dispatched_qty) ? 1 : 0;
//                $validateTotalQty = $detail->dispatched_qty + $detail->unacknowledged_qty;
//                if ($validateTotalQty > $detail->qty) {
//                    throw new \Exception("Total dispatch quantity exceeds available stock quantity for detail ID: " . $detail->id);
//                }
//
//                if (!$detail->save()) {
//                    throw new \Exception("Failed to update stock detail");
//                }
//                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
//            } else {
//                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
//            }
//            if (!$trial->save()) {
//                throw new \Exception("Failed to update trial");
//            }
//        }
//    }

    private function acknowledgement($postData) {
        foreach ($postData as $trialId => $detail) {
            $detail = StockOutboundDetails::findOne($detail['detailId']);
            $trial = StockDispatchTrial::findOne($trialId);

            if (!$trial) {
                throw new \Exception("Invalid request for trial");
            }

            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                // Validate before acknowledging
                $currentAcknowledged = StockDispatchTrial::find()
                                ->where([
                                    'stock_outbound_details_id' => $detail->id,
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
                        $detail->updateInventoryStockQty($trial->dispatch_qty, $detail);
                    } else if ($trial->dispatch_qty < 0) {
                        // Return or adjustment decrease
                        $detail->revertInventoryStockQty(abs($trial->dispatch_qty), $detail);
                    }
                    // If dispatch_qty == 0, no inventory change needed
                }

                // Update trial status to acknowledged
                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
            } else {
                // Un-acknowledging (toggle back)
                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;

                // Reverse the inventory operation
                if ($detail->qty_stock_available !== null) {
                    if ($trial->dispatch_qty > 0) {
                        // Was a dispatch, now revert it
                        $detail->revertInventoryStockQty(abs($trial->dispatch_qty), $detail);
                    } else if ($trial->dispatch_qty < 0) {
                        // Was a return, now re-dispatch it
                        $detail->updateInventoryStockQty(abs($trial->dispatch_qty), $detail);
                    }
                }
            }

            if (!$trial->save()) {
                throw new \Exception("Failed to update trial");
            }

            // Recalculate all quantities from trials
            $detail->updateAllQtyInStockDetail($detail);
        }
    }

    private function returnAcknowledgement($postData) {
        foreach ($postData as $trialId => $detail) {
            $detail = StockOutboundDetails::findOne($detail['detailId']);
            $trial = StockDispatchTrial::findOne($trialId);

            if (!$trial) {
                throw new \Exception("Invalid request for trial");
            }

            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                // ❌ REMOVE manual unacknowledged_qty update
                // $detail->unacknowledged_qty = ($detail->unacknowledged_qty + $trial->dispatch_qty);
                // Note: dispatch_qty is negative for returns
                // Validate: ensure we're not returning more than dispatched
                $currentAcknowledged = StockDispatchTrial::find()
                                ->where([
                                    'stock_outbound_details_id' => $detail->id,
                                    'current_sts' => StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED
                                ])
                                ->sum('dispatch_qty') ?? 0;

                $newAcknowledged = $currentAcknowledged + $trial->dispatch_qty; // Adding negative

                if ($newAcknowledged < 0) {
                    throw new \Exception("Cannot return more than dispatched quantity for detail ID: " . $detail->id);
                }

                // Revert inventory if tracking exists (dispatch_qty is negative, so we revert the absolute value)
                if ($detail->qty_stock_available !== null) {
                    $detail->revertInventoryStockQty(abs($trial->dispatch_qty), $detail);
                }

                // Update trial status to acknowledged
                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
            } else {
                // Toggle back to unacknowledged
                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;

                // Re-dispatch to inventory since we're un-acknowledging the return
                if ($detail->qty_stock_available !== null) {
                    $this->updateInventoryStockQty(abs($trial->dispatch_qty), $detail);
                }
            }

            if (!$trial->save()) {
                throw new \Exception("Failed to update trial");
            }

            // Recalculate all quantities from trials
            $detail->updateAllQtyInStockDetail($detail);
        }
    }

//before inventory
//    private function returnAcknowledgement($postData) {
//        foreach ($postData as $trialId => $detail) {
//            $detail = StockOutboundDetails::findOne($detail['detailId']);
//            $trial = StockDispatchTrial::findOne($trialId);
//            if (!$trial) {
//                throw new \Exception("Invalid request for trial");
//            }
//            if ($trial->current_sts == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
//                $detail->unacknowledged_qty = ($detail->unacknowledged_qty + $trial->dispatch_qty);
//                $detail->fully_dispatch_status = ($detail->qty == $detail->dispatched_qty) ? 1 : 0;
//                $validateTotalQty = $detail->dispatched_qty + $detail->unacknowledged_qty;
//                if ($validateTotalQty > $detail->qty) {
//                    throw new \Exception("Total dispatch quantity exceeds available stock quantity " . $detail->id);
//                }
//
//                if (!$detail->save()) {
//                    throw new \Exception("Failed to update stock detail");
//                }
//
//                $trial->current_sts = StockDispatchMaster::HAS_BEEN_ACKNOWLEDGED;
//            } else {
//                $trial->current_sts = StockDispatchMaster::TO_BE_ACKNOWLEDGED;
//            }
//            if (!$trial->save()) {
//                throw new \Exception("Failed to update trial");
//            }
//        }
//    }

    /**
     * Finds the StockDispatchMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockDispatchMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = StockDispatchMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
