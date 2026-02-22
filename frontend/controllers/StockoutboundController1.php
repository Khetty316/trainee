<?php

namespace frontend\controllers;

use Yii;
use frontend\models\bom\StockOutboundMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\bom\BomMaster;
use frontend\models\bom\StockOutboundDetails;
use frontend\models\bom\StockDispatchTrial;
use frontend\models\bom\VStockDispatchMasterSearch;
use frontend\models\bom\StockDispatchMaster;
use frontend\models\bom\VStockDispatchMaster;
use common\models\myTools\FlashHandler;
use frontend\models\bom\StockDispatchMasterSearch;
use common\modules\auth\models\AuthItem;

/**
 * StockoutboundController implements the CRUD actions for StockOutboundMaster model.
 */
class StockoutboundController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['@']
//                    ],
                    [
                        'actions' => ['index', 'view-panels', 'view-bom', 'update-stock-dispatch'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_Stock_Ob_View],
                    ],
                    [
                        'actions' => ['initiate-outbound-master', 'view-material-detail', 'add-material', 'deactivate-item', 'update-item-detail', 'dispatch-item-list', 'confirm-stock-dispatch', 'stock-return', 'return-dispatched-quantity'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal],
                    ],
                    [
                        'actions' => ['stock-adjustment', 'adjust-dispatch-quantity'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Stock_Ob_Super],
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

    public function actionIndex() {
        $searchModel = new \frontend\models\ProjectProduction\ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id) {
        if (($model = StockOutboundMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewPanels($id) {
        $model = \frontend\models\ProjectProduction\ProjectProductionMaster::findOne($id);
        $result = \frontend\models\projectproduction\VProjectProductionPanels::find()->where(['proj_prod_master' => $id])->all();
        return $this->render('viewPanels', [
                    'model' => $model,
                    'panelLists' => $result,
        ]);
    }

    public function actionInitiateOutboundMaster($productionPanelId) {
        if (Yii::$app->request->post()) {
            $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
            if ($bomMaster) {
                for ($i = 1; $i <= $bomMaster->productionPanel->quantity; $i++) {
                    $stockOutboundMaster = new StockOutboundMaster();
                    $stockOutboundMaster->production_panel_id = $bomMaster->production_panel_id;
                    $stockOutboundMaster->bom_master_id = $bomMaster->id;
                    $stockOutboundMaster->order = $i;
                    if ($stockOutboundMaster->save()) {
                        $stockOutboundMaster->copyDetail($bomMaster->bomDetails);
                    }
                }
            }

            return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
        }
    }

    public function actionViewBom($productionPanelId, $justCreated = false) {
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();
        if (!$bomMaster) {
            $bomMaster = new BomMaster();
            $bomMaster->production_panel_id = $productionPanelId;
            $bomMaster->save();
        }

        $searchModel = new \frontend\models\bom\bomdetailSearch();
        $params = Yii::$app->request->queryParams;
        $params['bomdetailSearch']['bom_master'] = $bomMaster->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('viewBom', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bomMaster' => $bomMaster,
                    'justCreated' => $justCreated
        ]);
    }

    public function actionViewMaterialDetail($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->joinWith('stockOutboundDetails')->all();
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);

        return $this->render('stockOutboundDetail', [
                    'stockMasters' => $stockMasters,
                    'bomMaster' => $bomMaster
        ]);
    }

    public function actionAddMaterial($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
        $model = new StockOutboundDetails();

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post('StockOutboundDetails');
            if (empty($stockMasters)) {
                FlashHandler::err("No StockOutboundMaster found for Production Panel ID: $productionPanelId");
            }

            $errors = [];
            foreach ($stockMasters as $stockMaster) {
                $newMaterial = new StockOutboundDetails();
                $newMaterial->attributes = $postData;
                $newMaterial->stock_outbound_master_id = $stockMaster->id;

                if (!$newMaterial->save()) {
                    $errors[] = "StockOutboundMaster ID {$stockMaster->id}: " . json_encode($newMaterial->errors);
                }
            }

            if (!empty($errors)) {
                FlashHandler::err("Failed to save some StockOutboundDetails: " . implode('; ', $errors));
            } else {
                FlashHandler::success('Success! The material has been added successfully.');
            }

            return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
        }

        return $this->renderAjax('_addMaterial', [
                    'model' => $model,
        ]);
    }

    public function actionUpdateItemDetail($productionPanelId, $stockDetailId) {
        $item = StockOutboundDetails::findOne($stockDetailId);
        $bomDetail = $item->bomDetail;

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            if (isset($postData['StockOutboundDetails'])) {
                $stockDetails = $postData['StockOutboundDetails'];
                $item->model_type = $stockDetails['model_type'];
                $item->brand = $stockDetails['brand'];
                $item->descriptions = $stockDetails['descriptions'];
                $item->qty = $stockDetails['qty'];

                if ($item->save()) {
                    FlashHandler::success("Success! The detail has been updated successfully.");
                } else {
                    FlashHandler::err("Error! Failed to update the detail. " . json_encode($item->getErrors()));
                }
            }

            return $this->redirect(['view-material-detail', 'productionPanelId' => $productionPanelId]);
        }

        return $this->renderAjax('_updateItemDetail', [
                    'item' => $item,
                    'bomDetail' => $bomDetail
        ]);
    }

    public function actionDeactivateItem($id) {
        if (Yii::$app->request->isPost) {
            $model = StockOutboundDetails::findOne($id);
            $model->active_sts = 0;
            $model->update();
            $stockMasters = VStockDispatchMaster::find()->where(['stock_outbound_details_id' => $id])->all();
            foreach ($stockMasters as $stockMaster) {
                $dispatchMaster = StockDispatchMaster::findOne($stockMaster->dispatch_id);
                $dispatchMaster->updateDispatchMasterStatus('deactiveItem');
            }
            return $this->redirect(['view-material-detail', 'productionPanelId' => $model->stockOutboundMaster->production_panel_id]);
        }
    }

    public function actionDispatchItemList($productionPanelId, $dispatchId, $action) {
        $searchModel = new VStockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $dispatchId);
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
        $model = StockDispatchMaster::findOne($dispatchId);
        $acknowledged = StockDispatchMaster::findOne(['id' => $dispatchId, 'status' => 1]);

        return $this->render('dispatchItemList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'bomMaster' => $bomMaster,
                    'model' => $model,
                    'acknowledged' => $acknowledged,
                    'action' => $action
        ]);
    }

    /*     * ********************** Update Stock Dispatch ****************************************** */

    public function actionUpdateStockDispatch($productionPanelId) {
        $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->joinWith('stockOutboundDetails')->all();
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);
        $model = new StockOutboundMaster();
        $receivers = $model->getReceivers($productionPanelId);

        if (Yii::$app->request->post()) {
            $postDataReceiver = Yii::$app->request->post('receiver');
            $postDataCurrentStatus = Yii::$app->request->post('current_sts');
            $postDataDispatch = Yii::$app->request->post('dispatch');

            function filterPostData($postDataDispatch) {
                return array_map(function ($obj) {
                    return array_filter($obj, function ($value) {
                return isset($value['dispatch_qty']) &&
                $value['dispatch_qty'] !== "0" &&
                $value['dispatch_qty'] !== "";
            });
                }, $postDataDispatch);
            }

            $filteredDataDispatch = array_filter(filterPostData($postDataDispatch));

            if (!empty($filteredDataDispatch)) {
                $combinedData = [
                    'receiver' => $postDataReceiver,
                    'current_sts' => $postDataCurrentStatus,
                    'dispatch' => $filteredDataDispatch
                ];
                Yii::$app->session->set('postData', $combinedData);
                return $this->redirect(['confirm-stock-dispatch', 'productionPanelId' => $productionPanelId]);
            }

            \common\models\myTools\FlashHandler::err("No item has been selected");
        }
        return $this->render('_updateStockDispatch', [
                    'stockMasters' => $stockMasters,
                    'bomMaster' => $bomMaster,
                    'receivers' => $receivers
        ]);
    }

    public function actionConfirmStockDispatch($productionPanelId) {
        $postData = Yii::$app->session->get('postData');
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();
        $model = new StockOutboundMaster();
        $receivers = $model->getReceivers($productionPanelId);
        $receiverIds = array_column($receivers, 'id');
        $receiverId = $postData['receiver']['id'] ?? null;
        if (!in_array($receiverId, $receiverIds)) {
            FlashHandler::err("No receiver found");
            return $this->redirect(['update-stock-dispatch', 'productionPanelId' => $productionPanelId]);
        }

        if (Yii::$app->request->post()) {
            $stockDetail = new StockOutboundDetails();
            if ($stockDetail->processStockDispatch($bomMaster, $productionPanelId, $postData, StockDispatchTrial::DISPATCH_STATUS)) {
                $stockMasters = StockOutboundMaster::find()->where(['production_panel_id' => $productionPanelId])->all();
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
                    $stockMaster->save();
                }

                Yii::$app->session->remove('postData');
                return $this->redirect(['view-panels', 'id' => $bomMaster->productionPanel->projProdMaster->id]);
            }
        }

        return $this->render('confirmStockDispatch', [
                    'postData' => $postData,
                    'bomMaster' => $bomMaster,
                    'receivers' => $receivers
        ]);
    }

    /*     * ********************** Stock Adjustment ****************************************** */

    public function actionStockAdjustment($productionPanelId) {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, $productionPanelId);
        $bomMaster = BomMaster::findOne(['production_panel_id' => $productionPanelId]);

        return $this->render('stockDispatchList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'productionPanelId' => $productionPanelId,
                    'bomMaster' => $bomMaster,
                    'action' => 'adjust'
        ]);
    }

    public function actionAdjustDispatchQuantity($dispatchId, $detailId) {
        $dispatchMaster = VStockDispatchMaster::findOne(['dispatch_id' => $dispatchId, 'stock_outbound_details_id' => $detailId]);
        $stockOutboundDetail = StockOutboundDetails::findOne($detailId);
        $stockMasters = StockOutboundMaster::findAll(['production_panel_id' => $dispatchMaster->production_panel_id]);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("dispatch");
            if ($stockOutboundDetail->processStockAdjustment($postData, $dispatchMaster, StockDispatchTrial::ADJUST_STATUS)) {
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
                    if ($stockMaster->save()) {
                        FlashHandler::success("Success! The quantity has been updated successfully.");
                    }
                }
            }

            return $this->redirect(['dispatch-item-list', 'productionPanelId' => $dispatchMaster->production_panel_id, 'dispatchId' => $dispatchId, 'action' => 'adjust']);
        }
        return $this->renderAjax('_stockAdjustment', [
                    'dispatchMaster' => $dispatchMaster
        ]);
    }

    /*     * ********************** Stock Return ****************************************** */

    public function actionStockReturn($productionPanelId) {
        $searchModel = new StockDispatchMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null, $productionPanelId);
        $bomMaster = BomMaster::find()->where(['production_panel_id' => $productionPanelId])->one();

        return $this->render('stockDispatchList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'productionPanelId' => $productionPanelId,
                    'bomMaster' => $bomMaster,
                    'action' => 'return'
        ]);
    }

    public function actionReturnDispatchedQuantity($dispatchId, $detailId) {
        $dispatchMaster = VStockDispatchMaster::findOne(['dispatch_id' => $dispatchId, 'stock_outbound_details_id' => $detailId]);
        $stockOutboundDetail = StockOutboundDetails::findOne($detailId);
        $stockMasters = StockOutboundMaster::findAll(['production_panel_id' => $dispatchMaster->production_panel_id]);

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("dispatch");
            if ($stockOutboundDetail->processStockReturn($postData, $dispatchMaster, StockDispatchTrial::RETURN_STATUS)) {
                foreach ($stockMasters as $stockMaster) {
                    $hasPendingDispatch = StockOutboundDetails::find()->where(['stock_outbound_master_id' => $stockMaster->id, 'fully_dispatch_status' => 0])->exists();
                    $stockMaster->fully_dispatched_status = $hasPendingDispatch ? 0 : 1;
                    if ($stockMaster->save()) {
                        FlashHandler::success("Success! The return dispatched quantity has been saved successfully.");
                    }
                }
            }

            return $this->redirect(['dispatch-item-list', 'productionPanelId' => $dispatchMaster->production_panel_id, 'dispatchId' => $dispatchId, 'action' => 'return']);
        }
        return $this->renderAjax('_stockReturn', [
                    'dispatchMaster' => $dispatchMaster
        ]);
    }
}
