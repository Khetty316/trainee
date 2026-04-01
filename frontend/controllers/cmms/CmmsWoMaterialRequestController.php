<?php

namespace frontend\controllers\cmms;

use Yii;
use frontend\models\cmms\CmmsWoMaterialRequestMaster;
use frontend\models\cmms\CmmsWoMaterialRequestMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\cmms\CmmsWoMaterialRequestDetails;
use frontend\models\inventory\InventoryModel;
use common\models\myTools\FlashHandler;
use frontend\models\cmms\CmmsPreventiveWorkOrderMaster;
use frontend\models\cmms\CmmsCorrectiveWorkOrderMaster;
use frontend\models\cmms\CmmsFaultList;
use frontend\models\inventory\InventoryOrderRequest;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * CmmsWoMaterialRequestController implements the CRUD actions for CmmsWoMaterialRequestMaster model.
 */
class CmmsWoMaterialRequestController extends Controller {

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
                        'roles' => [AuthItem::ROLE_INVENTORY_Executive, AuthItem::ROLE_INVENTORY_Assistant, AuthItem::ROLE_INVENTORY_MaintenanceHead, AuthItem::ROLE_CMMS_Superior],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CmmsWoMaterialRequestMaster models.
     * @return mixed
     */
    public function actionPendingMaterialRequestMasterList($moduleIndex = "inventory", $type = "pending") {
        $searchModel = new CmmsWoMaterialRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('materialRequestList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => $moduleIndex,
                    'type' => $type,
                    'key' => 1
        ]);
    }

    public function actionAllMaterialRequestMasterList($moduleIndex, $type) {
        $searchModel = new CmmsWoMaterialRequestMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);

        return $this->render('materialRequestList', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleIndex' => $moduleIndex,
                    'type' => $type,
                    'key' => 2
        ]);
    }

    public function actionViewSelectedMaterialPm($id, $moduleIndex) {
        $model = CmmsPreventiveWorkOrderMaster::findOne($id);
        $materialMaster = CmmsWoMaterialRequestMaster::findOne([
            'wo_type' => CmmsWoMaterialRequestMaster::WO_TYPE_PM,
            'wo_id' => $model->id,
        ]);
        $materialDetails = $materialMaster->cmmsWoMaterialRequestDetails ?? [];
        $partToolList = InventoryModel::getModelBrandCombinations();

        if (Yii::$app->request->isPost) {
            $postDataReceiver = Yii::$app->request->post('receiver');
            $postDataCurrentSts = Yii::$app->request->post('current_sts');
            $postDataDispatch = Yii::$app->request->post('dispatch');

            if (!empty($postDataDispatch)) {
                $filtered = [];
                foreach ($postDataDispatch as $indexGroup) {
                    foreach ($indexGroup as $detailId => $item) {
                        if (
                                isset($item['dispatch_qty']) &&
                                $item['dispatch_qty'] !== '' &&
                                $item['dispatch_qty'] !== '0'
                        ) {
                            $filtered[$detailId] = $item;
                        }
                    }
                }

                if (!empty($filtered)) {
                    Yii::$app->session->set('postData', [
                        'receiver' => $postDataReceiver,
                        'current_sts' => $postDataCurrentSts,
                        'dispatch' => [0 => $filtered], // ← wrap in group key, same shape as CM
                    ]);

                    return $this->redirect(['confirm-stock-dispatch-pm', 'id' => $id, 'moduleIndex' => $moduleIndex,]);
                }
            }

            FlashHandler::err('No item has been selected.');
        }

        return $this->render('materialRequestDetailPm', [
                    'model' => $model,
                    'materialMaster' => $materialMaster,
                    'materialDetails' => $materialDetails,
                    'partToolList' => $partToolList,
                    'moduleIndex' => $moduleIndex,
                    'wotype' => CmmsWoMaterialRequestMaster::WO_TYPE_PM,
        ]);
    }

    public function actionConfirmStockDispatchPm($id, $moduleIndex) {
        $postData = Yii::$app->session->get('postData');

        if (empty($postData)) {
            FlashHandler::err('Session expired. Please resubmit.');
            return $this->redirect(['view-selected-material-pm', 'id' => $id, 'moduleIndex' => $moduleIndex,]);
        }

        $detailIds = [];
        foreach ($postData['dispatch'] as $group) {
            foreach ($group as $detailId => $item) {
                $detailIds[] = $detailId;
            }
        }
        $detailModels = CmmsWoMaterialRequestDetails::find()
                ->where(['id' => $detailIds])
                ->indexBy('id')
                ->all();

        $model = CmmsWoMaterialRequestMaster::findOne([
            'wo_type' => CmmsWoMaterialRequestMaster::WO_TYPE_PM,
            'wo_id' => $id,
        ]);

        if (Yii::$app->request->isPost && Yii::$app->request->post('confirm') === '1') {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $requestDetail = new CmmsWoMaterialRequestDetails();
                if ($requestDetail->processStockDispatch($model, $postData, \frontend\models\bom\StockDispatchTrial::DISPATCH_STATUS)) {
                    $requestDetail->updateMaterialRequestMasterStatus($model);
                }

                $transaction->commit();
                Yii::$app->session->remove('postData');
                FlashHandler::success('Stock dispatched successfully.');
                return $this->redirect(['view-selected-material-pm', 'id' => $id, 'moduleIndex' => $moduleIndex]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                \common\models\myTools\Mydebug::dumpFileW($e);
                FlashHandler::err('Failed to dispatch. Please try again.');
            }
        }

        return $this->render('confirmStockDispatchMaintenancePm', [
                    'postData' => $postData,
                    'model' => $model,
                    'detailModels' => $detailModels,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    public function actionViewSelectedMaterialCm($id, $moduleIndex) {
        $model = CmmsWoMaterialRequestMaster::findOne([
            'wo_type' => CmmsWoMaterialRequestMaster::WO_TYPE_CM,
            'wo_id' => $id,
        ]);

        $details = $model->cmmsWoMaterialRequestDetails;
        $faults = [];

        foreach ($details as $detail) {
            $faultId = $detail->fault_id;
            if (!isset($faults[$faultId])) {
                $faults[$faultId] = [
                    'fault' => CmmsFaultList::findOne($faultId),
                    'materialDetails' => [],
                ];
            }
            $faults[$faultId]['materialDetails'][] = $detail;
        }

        $partToolList = InventoryModel::getModelBrandCombinations();

        if (Yii::$app->request->isPost) {
            $postDataReceiver = Yii::$app->request->post('receiver');
            $postDataCurrentSts = Yii::$app->request->post('current_sts');
            $postDataDispatch = Yii::$app->request->post('dispatch');

            if (!empty($postDataDispatch)) {
                // Filter out rows with zero / empty dispatch_qty
                $filtered = array_filter(
                        array_map(function ($fault) {
                            return array_filter($fault, fn($item) =>
                                    isset($item['dispatch_qty']) &&
                                    $item['dispatch_qty'] !== '' &&
                                    $item['dispatch_qty'] !== '0'
                            );
                        }, $postDataDispatch)
                );

                if (!empty($filtered)) {
                    Yii::$app->session->set('postData', [
                        'receiver' => $postDataReceiver,
                        'current_sts' => $postDataCurrentSts,
                        'dispatch' => $filtered,
                    ]);

                    return $this->redirect([
                                'confirm-stock-dispatch-cm',
                                'id' => $id,
                                'moduleIndex' => $moduleIndex,
                    ]);
                }
            }

            FlashHandler::err('No item has been selected.');
        }

        return $this->render('materialRequestDetailCm', [
                    'model' => $model,
                    'faults' => $faults,
                    'partToolList' => $partToolList,
                    'moduleIndex' => $moduleIndex,
                    'wotype' => CmmsWoMaterialRequestMaster::WO_TYPE_CM,
        ]);
    }

    public function actionConfirmStockDispatchCm($id, $moduleIndex) {
        $postData = Yii::$app->session->get('postData');
        $model = CmmsWoMaterialRequestMaster::findOne([
            'wo_type' => CmmsWoMaterialRequestMaster::WO_TYPE_CM,
            'wo_id' => $id,
        ]);
        if (empty($postData)) {
            FlashHandler::err('Session expired. Please resubmit.');
            return $this->redirect(['view-selected-material-cm', 'id' => $id, 'moduleIndex' => $moduleIndex]);
        }

        $detailIds = [];
        foreach ($postData['dispatch'] as $faultItems) {
            foreach ($faultItems as $detailId => $item) {
                $detailIds[] = $detailId;
            }
        }
        $detailModels = CmmsWoMaterialRequestDetails::find()
                ->where(['id' => $detailIds])
                ->indexBy('id')
                ->all();

        if (Yii::$app->request->isPost && Yii::$app->request->post('confirm') === '1') {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $requestDetail = new CmmsWoMaterialRequestDetails();
                if ($requestDetail->processStockDispatch($model, $postData, \frontend\models\bom\StockDispatchTrial::DISPATCH_STATUS)) {
                    $requestDetail->updateMaterialRequestMasterStatus($model);
                }
                FlashHandler::success("Success!");
                $transaction->commit();
                Yii::$app->session->remove('postData');
                FlashHandler::success('Stock dispatched successfully.');
                return $this->redirect(['view-selected-material-cm', 'id' => $id, 'moduleIndex' => $moduleIndex]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                \common\models\myTools\Mydebug::dumpFileW($e);

                FlashHandler::err('Failed to dispatch. Please try again.');
            }
        }

        return $this->render('confirmStockDispatchMaintenanceCm', [
                    'postData' => $postData,
                    'model' => $model,
                    'detailModels' => $detailModels,
                    'moduleIndex' => $moduleIndex,
        ]);
    }

    public function actionCreate($woId, $faultId = null, $wotype) {
        $url = Yii::$app->request->referrer ?? ['view-material-request', 'id' => $woId];
        $model = new CmmsWoMaterialRequestDetails();
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Find or create the master record
                $master = CmmsWoMaterialRequestMaster::findOne(['wo_type' => $wotype, 'wo_id' => $woId]);
                if ($master === null) {
                    $master = new CmmsWoMaterialRequestMaster();
                    $master->wo_type = $wotype;
                    $master->wo_id = $woId;
                    $master->finalized_status = 0;
                    if (!$master->save()) {
                        \common\models\myTools\Mydebug::dumpFileW($master->getErrors());
                        throw new \Exception('Failed to save material request master: ' . json_encode($master->getErrors()));
                    }
                }

                // Attach master ID and save detail
                $model->request_master_id = $master->id;
                $model->fault_id = $faultId;
                if (!$model->validate() || !$model->save()) {
                    \common\models\myTools\Mydebug::dumpFileW($model->getErrors());
                    throw new \Exception('Failed to save material request detail: ' . json_encode($model->getErrors()));
                }

                $transaction->commit();
                FlashHandler::success('Material saved successfully.');
            } catch (\Exception $e) {
                $transaction->rollBack();
                \common\models\myTools\Mydebug::dumpFileW($e->getMessage());
                FlashHandler::err('Failed to save material. Please try again.');
            }

            return $this->redirect($url);
        }

        $inventoryModel = new InventoryModel();
        $data = $inventoryModel->prepareFormData($model);
        return $this->renderAjax('create', $data);
    }

    public function actionUpdate($id, $wotype) {
        $model = CmmsWoMaterialRequestDetails::findOne($id);
        $url = Yii::$app->request->referrer;

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->validate() || !$model->save()) {
                    throw new \Exception('Failed to update material request detail: ' . json_encode($model->getErrors()));
                }

                $transaction->commit();
                FlashHandler::success('Material updated successfully.');
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err('Failed to save material. Please try again.');
            }

            return $this->redirect($url);
        }

        // Prepare data for view
        $inventoryModel = new InventoryModel();
        $data = $inventoryModel->prepareFormData($model);
        return $this->renderAjax('update', $data);
    }

    public function actionFinalizeSelectedMaterial() {
        $woId = Yii::$app->request->post('woId');
        $woType = Yii::$app->request->post('woType');
        $faultId = Yii::$app->request->post('faultId');
        $selectedIds = Yii::$app->request->post('ids');

        // Ensure it's always an array
        if (!is_array($selectedIds)) {
            $selectedIds = $selectedIds ? [$selectedIds] : [];
        }

        // Filter to valid integers only
        $selectedIds = array_filter(array_map('intval', $selectedIds));

        if (empty($selectedIds)) {
            return $this->asJson(['success' => false, 'message' => 'No items selected.']);
        }

        $master = CmmsWoMaterialRequestMaster::findOne(['wo_id' => $woId, 'wo_type' => $woType]);

        if ($master === null) {
            // Fallback: derive master from the first selected detail record
            $sampleDetail = CmmsWoMaterialRequestDetails::findOne(['id' => $selectedIds[0]]);
            if ($sampleDetail !== null) {
                $master = CmmsWoMaterialRequestMaster::findOne($sampleDetail->request_master_id);
            }
        }

        if ($master === null) {
            Yii::$app->session->setFlash('error', 'Material request master record not found.');
            return $this->asJson(['success' => false, 'message' => 'Material request master record not found.']);
        }

        $masterId = $master->id;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Finalize only the selected detail records that are not already finalized
            $updateCount = CmmsWoMaterialRequestDetails::updateAll(
                    ['is_finalized' => 2],
                    [
                        'and',
                        ['id' => $selectedIds],
                        ['request_master_id' => $masterId],
                        ['<>', 'is_finalized', 2],
                    ]
            );

            if ($updateCount === 0) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('warning', 'No items were finalized — they may already be finalized.');
                return $this->asJson(['success' => false, 'message' => 'No items were finalized (may already be finalized).']);
            }

            // Recalculate master finalized_status based on current state of all active details
            $totalItems = CmmsWoMaterialRequestDetails::find()
                    ->where(['request_master_id' => $masterId, 'active_sts' => 1])
                    ->andWhere(['<>', 'inventory_sts', 3])
                    ->count();

            $finalizedItems = CmmsWoMaterialRequestDetails::find()
                    ->where(['request_master_id' => $masterId, 'is_finalized' => 2, 'active_sts' => 1])
                    ->andWhere(['<>', 'inventory_sts', 3])
                    ->count();

            if ($totalItems > 0 && $finalizedItems == $totalItems) {
                $master->finalized_status = 1; // Fully finalized
            } elseif ($finalizedItems > 0) {
                $master->finalized_status = 2; // Partially finalized
            } else {
                $master->finalized_status = 0; // Not finalized
            }

            if (!$master->save()) {
                throw new \Exception('Failed to update master finalized status: ' . json_encode($master->getErrors()));
            }

            // Process inventory for each detail
            foreach ($selectedIds as $id) {
                $detail = CmmsWoMaterialRequestDetails::findOne($id);
                InventoryOrderRequest::processInventoryItem($detail, $detail->qty, Yii::$app->user->id, $master->wo_type, $detail->id);
            }

            $transaction->commit();

            Yii::$app->session->setFlash('success', "Finalized {$updateCount} item(s) successfully.");
            return $this->asJson(['success' => true, 'message' => "Finalized {$updateCount} item(s) successfully."]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Failed to finalize. Please try again.');
            return $this->asJson(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function actionDeactivateMaterialDetails($id) {
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\MethodNotAllowedHttpException('Only POST requests are allowed.');
        }

        $model = CmmsWoMaterialRequestDetails::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Material detail not found.');
            return $this->redirect(Yii::$app->request->referrer ?? ['/cmms/cmms-corrective-work-order-master/index']);
        }

        $master = CmmsWoMaterialRequestMaster::findOne($model->request_master_id);
        $url = Yii::$app->request->referrer ?? ['/cmms/cmms-wo-material-request/view-material-request', 'id' => $master->wo_id ?? $id];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->active_sts = 0;

            if (!$model->save(false)) {
                throw new \Exception('Failed to deactivate: ' . json_encode($model->getErrors()));
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Material item deactivated successfully.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            \common\models\myTools\Mydebug::dumpFileW($e->getMessage());
            Yii::$app->session->setFlash('error', 'Failed to deactivate material item. Please try again.');
        }

        return $this->redirect($url);
    }

    public function actionDeleteMultiple() {
        $woId = Yii::$app->request->post('woId');
        $woType = Yii::$app->request->post('woType');

        $selectedIds = Yii::$app->request->post('ids');

        if (empty($selectedIds)) {
            Yii::$app->session->setFlash('error', 'No items selected for deletion.');
            return $this->asJson(['success' => false, 'message' => 'No items selected.']);
        }

// Find master — fallback to deriving from first detail record
        $master = CmmsWoMaterialRequestMaster::findOne(['wo_id' => $woId, 'wo_type' => $woType]);
        if ($master === null) {
            $sampleDetail = CmmsWoMaterialRequestDetails::findOne(['id' => $selectedIds[0]]);
            if ($sampleDetail !== null) {
                $master = CmmsWoMaterialRequestMaster::findOne($sampleDetail->request_master_id);
            }
        }

        if ($master === null) {
            Yii::$app->session->setFlash('error', 'Material request master record not found.');
            return $this->asJson(['success' => false, 'message' => 'Master record not found.']);
        }

        $masterId = $master->id;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $deleteCount = CmmsWoMaterialRequestDetails::deleteAll([
                'and',
                ['id' => $selectedIds],
                ['request_master_id' => $masterId],
                ['active_sts' => 1],
            ]);

            if ($deleteCount === 0) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('warning', 'No items were deleted. Items may already be finalized or in outbound.');
                return $this->asJson(['success' => false, 'message' => 'No items deleted — items may be finalized or in outbound.']);
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Deleted {$deleteCount} item(s) successfully.");
            return $this->asJson(['success' => true, 'message' => "Deleted {$deleteCount} item(s) successfully."]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            \common\models\myTools\Mydebug::dumpFileW($e->getMessage());
            Yii::$app->session->setFlash('error', 'Failed to delete items. Please try again.');
            return $this->asJson(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function actionChangeQtyFinalizedItem($detailId) {
        $item = CmmsWoMaterialRequestDetails::findOne($detailId);

        if (!$item) {
            FlashHandler::err("Item not found.");
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post('CmmsWoMaterialRequestDetails');
            $newQty = $postData['qty'];

            // Validate new quantity
            if ($newQty < 0) {
                FlashHandler::err("Quantity cannot be negative.");
                return $this->redirect(Yii::$app->request->referrer);
            }

            $oldQty = $item->qty;
            $dispatchedQty = $item->dispatched_qty + $item->unacknowledged_qty;
            $availableQty = $item->qty_stock_available;

            // Calculate additional quantity needed
            $newRequiredQty = $newQty - $oldQty + ($oldQty - $dispatchedQty) - $availableQty;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($newRequiredQty > 0) {
                    InventoryOrderRequest::processInventoryItem(
                            $item,
                            $newRequiredQty,
                            $item->created_by,
                            $item->requestMaster->wo_type,
                            $item->id
                    );
                }

                // Update the quantity
                $item->qty = $newQty;
                if (!$item->save(false)) {
                    throw new \Exception("Failed to save item: " . json_encode($item->getErrors()));
                }

                // Update related records
                $item->updateAllQtyInStockDetail($item, $item->requestMaster->wo_type);
                $item->updateMaterialRequestMasterStatus($item->requestMaster);
                $transaction->commit();
                FlashHandler::success("The quantity has been updated successfully");
            } catch (\Exception $e) {
                $transaction->rollBack();
                FlashHandler::err("An error occurred while updating the item: " . $e->getMessage());
                Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
            }

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_changeQtyFinalizedItem', [
                    'model' => $item
        ]);
    }

    /**
     * Deletes an existing CmmsWoMaterialRequestMaster model.
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
     * Finds the CmmsWoMaterialRequestMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsWoMaterialRequestMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CmmsWoMaterialRequestMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
