<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\FlashHandler;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetail;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordItem;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetailSearch;
use frontend\models\projectproduction\task\TaskAssignment;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;
use common\models\myTools\MyCommonFunction;

/**
 * ProdOtMealRecordMasterController implements the CRUD actions for ProdOtMealRecordMaster model.
 */
class ProdOtMealRecordMasterController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/prod-ot-meal-record-master/');
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
                        'allow' => true,
                        'actions' => ['ajax-view-daily-record'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'create', 'view', 'update', 'delete', 'finalize-record', 'revert-finalize-record', 'view-detail', 'add-new-record', 'update-detail-record', 'delete-selected', 'user-manual-personal'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PROD_OT_MEAL_EXEC],
                    ],
                    [
                        'actions' => ['index-finance', 'finance-view', 'user-manual-personal'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PROD_OT_MEAL_FINANCE],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new ProdOtMealRecordMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personal');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'module' => 'personal',
                    'key' => '3'
        ]);
    }

    public function actionCreate() {
        $model = new ProdOtMealRecordMaster();

        if ($model->load(Yii::$app->request->post())) {
            $exists = ProdOtMealRecordMaster::find()
                    ->where([
                        'month' => $model->month,
                        'year' => $model->year,
                        'created_by' => Yii::$app->user->id,
                        'deleted_by' => null,
                        'deleted_at' => null,
                    ])
                    ->exists();

            if (!$exists) {
                $model->ref_code = $model->generateRefCode();

                if ($model->save()) {
                    FlashHandler::success("Successfully created a new monthly record!");
                } else {
                    FlashHandler::err("Creation failed. Please try again.");
                }
            } else {
                FlashHandler::err("This monthly record already created! Please check you record.");
            }

            return $this->redirect(['index']);
        }

        return $this->renderAjax('create', [
                    'model' => $model,
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        $searchModel = new ProdOtMealRecordDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $model->id);

        return $this->render('view', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'module' => 'personal',
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("The detail has been updated successfully!");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->deleted_at = new \yii\db\Expression('NOW()');
        $model->deleted_by = Yii::$app->user->identity->id;
        $model->status = ProdOtMealRecordMaster::STATUS_DELETED;

        if ($model->save()) {
            FlashHandler::success("The record has been deleted successfully!");
        } else {
            FlashHandler::err("Failed to delete the record!");
        }

        return $this->redirect(['index']);
    }

    public function actionFinalizeRecord($id) {
        $model = $this->findModel($id);
        $model->status = ProdOtMealRecordMaster::STATUS_FINALIZE;

        if ($model->save()) {
            FlashHandler::success("The record has been finalized successfully!");
        } else {
            FlashHandler::err("Failed to finalize the record!");
        }

        return $this->redirect(['index']);
    }

    public function actionRevertFinalizeRecord($id) {
        $model = $this->findModel($id);
        $model->status = ProdOtMealRecordMaster::STATUS_NOT_FINALIZE;

        if ($model->save()) {
            FlashHandler::success("The record has been reverted successfully!");
        } else {
            FlashHandler::err("Failed to revert the record!");
        }

        return $this->redirect(['index']);
    }

//    ************************ detail and item table ***************************
    public function actionAjaxViewDailyRecord($id) {
        $model = $this->findModel($id);
        $searchModel = new ProdOtMealRecordDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $model->id);
         
        return $this->renderAjax('indexDetail', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hideFilter' => true
        ]);
    }

    public function actionViewDetail($id) {
        $detail = ProdOtMealRecordDetail::findOne($id);
        if (!$detail) {
            throw new NotFoundHttpException('The requested record does not exist.');
        }

        $model = $this->findModel($detail->prod_ot_meal_record_master_id);

        // Get already selected staff
        $selectedStaffIds = ProdOtMealRecordItem::find()
                ->select('user_id')
                ->where(['prod_ot_meal_record_detail_id' => $detail->id])
                ->column();

        // For dropdown/filtering
        $userModel = new \common\models\User;
        $staffListFab = $userModel->getStaffList_productionAssignee(\frontend\models\projectproduction\task\TaskAssignment::taskTypeFabrication);
        $staffListElec = $userModel->getStaffList_productionAssignee(\frontend\models\projectproduction\task\TaskAssignment::taskTypeElectrical);
        $staffListFabElec = array_merge($staffListFab, $staffListElec);
        usort($staffListFabElec, fn($a, $b) => strcasecmp($a['fullname'], $b['fullname']));

        return $this->render('viewDetail', [
                    'model' => $model,
                    'detail' => $detail,
                    'selectedStaffIds' => $selectedStaffIds,
                    'staffListFab' => $staffListFab,
                    'staffListElec' => $staffListElec,
                    'staffListFabElec' => $staffListFabElec,
        ]);
    }

    //daily record
    public function actionAddNewRecord($id) {
        $model = $this->findModel($id);
        $detail = new ProdOtMealRecordDetail();

        if ($detail->load(Yii::$app->request->post())) {
            $selectedStaff = Yii::$app->request->post('selectedStaff', []);
            $detail->prod_ot_meal_record_master_id = $model->id;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (empty($selectedStaff)) {
                    throw new \Exception('Please select at least one staff before saving.');
                }

                if (!$detail->checkReceiptDate()) {
                    throw new \Exception('The receipt date already exists.');
                }

                if (!$detail->save()) {
                    throw new \Exception(json_encode($detail->getErrors()));
                }

                // Delete old records if editing existing
                ProdOtMealRecordItem::deleteAll(['prod_ot_meal_record_detail_id' => $detail->id]);

                foreach ($selectedStaff as $staffId) {
                    $item = new ProdOtMealRecordItem([
                        'prod_ot_meal_record_detail_id' => $detail->id,
                        'user_id' => $staffId,
                    ]);
                    if (!$item->save(false)) {
                        throw new \Exception("Failed to save staff item for user ID {$staffId}");
                    }
                }

                if (!$model->updateTotalAmountMaster()) {
                    throw new \Exception("Failed to update total amount!");
                }

                $transaction->commit();
                FlashHandler::success('Record saved successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }

        // form data for initial render
        $userModel = new \common\models\User;
        $staffListFab = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication);
        $staffListElec = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical);
        $staffListFabElec = array_merge($staffListFab, $staffListElec);
        usort($staffListFabElec, fn($a, $b) => strcasecmp($a['fullname'], $b['fullname']));

        return $this->render('createDetail', [
                    'model' => $model,
                    'detail' => $detail,
                    'staffListFab' => $staffListFab,
                    'staffListElec' => $staffListElec,
                    'staffListFabElec' => $staffListFabElec,
                    'selectedStaffIds' => [],
        ]);
    }

    public function actionUpdateDetailRecord($id) {
        $detail = ProdOtMealRecordDetail::findOne($id);
        if (!$detail) {
            throw new \yii\web\NotFoundHttpException('Detail not found.');
        }

        $model = $this->findModel($detail->prod_ot_meal_record_master_id);
        $oldDate = $detail->receipt_date;

        if ($detail->load(Yii::$app->request->post())) {
            $selectedStaff = Yii::$app->request->post('selectedStaff', []);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (empty($selectedStaff)) {
                    throw new \Exception('Please select at least one staff before saving.');
                }

                $newDateChanged = ($detail->receipt_date !== $oldDate);
                if ($newDateChanged && !$detail->checkReceiptDate()) {
                    throw new \Exception('The receipt date already exists.');
                }

                if (!$detail->save()) {
                    throw new \Exception(json_encode($detail->getErrors()));
                }

                // Get existing staff IDs
                $existingStaffIds = ProdOtMealRecordItem::find()
                        ->select('user_id')
                        ->where(['prod_ot_meal_record_detail_id' => $detail->id])
                        ->column();

                // Staff to remove (unselected)
                $toRemove = array_diff($existingStaffIds, $selectedStaff);
                if (!empty($toRemove)) {
                    ProdOtMealRecordItem::deleteAll([
                        'prod_ot_meal_record_detail_id' => $detail->id,
                        'user_id' => $toRemove,
                    ]);
                }

                // Staff to add (newly selected)
                $toAdd = array_diff($selectedStaff, $existingStaffIds);
                foreach ($toAdd as $staffId) {
                    $item = new ProdOtMealRecordItem([
                        'prod_ot_meal_record_detail_id' => $detail->id,
                        'user_id' => $staffId,
                    ]);
                    if (!$item->save(false)) {
                        throw new \Exception("Failed to save staff item for user ID {$staffId}");
                    }
                }

                if (!$model->updateTotalAmountMaster()) {
                    throw new \Exception("Failed to update total amount!");
                }

                $transaction->commit();
                FlashHandler::success('Record updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }

        // --- Render form again for GET or failed POST ---
        $userModel = new \common\models\User;
        $staffListFab = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication);
        $staffListElec = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical);
        $staffListFabElec = array_merge($staffListFab, $staffListElec);
        usort($staffListFabElec, fn($a, $b) => strcasecmp($a['fullname'], $b['fullname']));

        // Pre-check staff that are already linked
        $selectedStaffIds = ProdOtMealRecordItem::find()
                ->select('user_id')
                ->where(['prod_ot_meal_record_detail_id' => $detail->id])
                ->column();

        return $this->render('viewDetail', [
                    'model' => $model,
                    'detail' => $detail,
                    'staffListFab' => $staffListFab,
                    'staffListElec' => $staffListElec,
                    'staffListFabElec' => $staffListFabElec,
                    'selectedStaffIds' => $selectedStaffIds,
        ]);
    }

    public function actionDeleteSelected() {
        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            FlashHandler::err("Please select at least one record to delete.");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            ProdOtMealRecordDetail::updateAll(
                    [
                        'deleted_at' => new \yii\db\Expression('NOW()'),
                        'deleted_by' => Yii::$app->user->id,
                    ],
                    ['id' => $ids]
            );

            $transaction->commit();
            FlashHandler::success("Selected records have been deleted successfully!");
        } catch (\Throwable $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed to delete records: " . $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    protected function findModel($id) {
        if (($model = ProdOtMealRecordMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUserManualPersonal() {
        $this->layout = false;
        $fileName = ProdOtMealRecordMaster::PERSONAL_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionIndexFinance() {
        $searchModel = new ProdOtMealRecordMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'finance');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'module' => 'finance',
                    'key' => '3'
        ]);
    }

    public function actionFinanceView($id) {
        $model = $this->findModel($id);
        $searchModel = new ProdOtMealRecordDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $model->id);

        return $this->render('view', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'module' => 'finance',
        ]);
    }
}
