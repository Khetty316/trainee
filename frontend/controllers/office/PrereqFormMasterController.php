<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\preReqForm\PrereqFormMaster;
use frontend\models\office\preReqForm\PrereqFormMasterSearch;
use frontend\models\office\preReqForm\PrereqFormItem;
use frontend\models\office\preReqForm\VPrereqFormMasterDetail;
use frontend\models\office\preReqForm\PrereqFormItemWorklist;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\RefGeneralStatus;
use common\models\myTools\FlashHandler;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;
use common\models\User;
use yii\helpers\Url;
use frontend\models\inventory\InventorySupplier;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryReorder;
use frontend\models\inventory\InventoryReorderItem;
use frontend\models\RefInventoryStatus;
use frontend\models\inventory\RefInventoryDepartments;
use frontend\models\inventory\InventoryDetail;
use frontend\models\inventory\InventoryReorderMaster;
use frontend\models\inventory\VInventoryDetail;

/**
 * PrereqFormMasterController implements the CRUD actions for PrereqFormMaster model.
 */
class PrereqFormMasterController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/prereq-form-master/');
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
                        'actions' => ['get-file', 'view', 'get-suppliers', 'get-brands', 'get-models', 'get-inventory-id'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['personal-pending-approval', 'personal-all-approval', 'create', 'update', 'delete', 'ajax-add-form-item', 'ajax-delete-item', 'user-manual-personal', 'proceed-to-purchasing'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PRF_Normal],
                    ],
                    [
                        'actions' => ['save-superior-update', 'superior-pending-approval', 'superior-all-approval', 'user-manual-superior'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PRF_Superior],
                    ],
                    [
                        'actions' => ['superuser-pending-approval', 'superuser-all-approval', 'user-manual-superuser'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_PRF_SuperUser],
                    ],
                ],
            ],
        ];
    }

    /*     * *************************************** Personal ********************************************* */

    public function actionPersonalPendingApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalPending');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'personal',
        ]);
    }

    public function actionPersonalAllApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'personalAll');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'all',
                    'moduleIndex' => 'personal',
        ]);
    }

    /**
     * Displays a single PrereqFormMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $moduleIndex) {
        $master = $this->findModel(PrereqFormMaster::class, $id);

        $vmodel = VPrereqFormMasterDetail::find()
                ->where(['master_id' => $id])
                ->with(['items'])
                ->all();
        // optional line
        $items = $master->prereqFormItems;

        $worklists = [];

        $hasSuperiorUpdate = false;

        foreach ($items as $i => $item) {
            $worklist = PrereqFormItemWorklist::findOne([
                'prereq_form_master_id' => $id,
                'prereq_form_item_id' => $item->id
            ]);

            if (!$worklist) {
                $worklist = new PrereqFormItemWorklist();
                $worklist->prereq_form_master_id = $id;
                $worklist->prereq_form_item_id = $item->id;
            } else {
                $hasSuperiorUpdate = true;
            }
            $worklists[$i] = $worklist;
//            $worklists[$i]->save(false);
        }
        return $this->render('view', [
                    'master' => $master,
                    'items' => $items,
                    'isView' => true,
                    'vmodel' => $vmodel,
                    'isUpdate' => false,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
        ]);
    }

    /**
     * created by Alicia 
     * updated by Khetty - 12/8/2025
     * /
     */
    public function actionCreate($moduleIndex) {
        $master = new PrereqFormMaster();
        $vmodel = new VPrereqFormMasterDetail();
        $items = [new PrereqFormItem()];
        $worklists = [];
        $hasSuperiorUpdate = false;
        foreach ($items as $i => $item) {
            $worklist = PrereqFormItemWorklist::findOne([
                'prereq_form_master_id' => $master->id,
                'prereq_form_item_id' => $item->id
            ]);

            if (!$worklist) {
                $worklist = new PrereqFormItemWorklist();
                $worklist->prereq_form_master_id = $master->id;
                $worklist->prereq_form_item_id = $item->id;
            } else {
                $hasSuperiorUpdate = true;
            }
            $worklists[$i] = $worklist;
//            $worklists[$i]->save(false);
        }

        if ($vmodel->load(Yii::$app->request->post())) {
            \common\models\myTools\Mydebug::dumpFileW(Yii::$app->request->post());
            // Start database transaction
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postMaster = Yii::$app->request->post('PrereqFormMaster');
                $master->date_of_material_required = $postMaster['date_of_material_required'];
//                $master->total_amount = $postMaster['total_amount'];
                $master->prf_no = $master->generatePrfNo();
                $master->superior_id = Yii::$app->user->identity->superior_id;
                $master->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
                $master->is_deleted = 0;
                $master->inventory_flag = 0;
                $master->source_module = 1;

                // Save master record first
                if (!$master->save()) {
                    throw new \Exception('Failed to save master record');
                }

                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail');

                // Save all items
                foreach ($postItems as $itemData) {
                    $item = new PrereqFormItem();
                    $item->prereq_form_master_id = $master->id;
                    $item->item_description = $itemData['item_description'];
                    $item->quantity = $itemData['quantity'] ?? null;
                    $item->currency = $itemData['currency'];
                    $item->unit_price = $itemData['unit_price'] ?? null;
                    $item->total_price = $itemData['total_price'] ?? null;
                    $item->purpose_or_function = $itemData['purpose_or_function'] ?? null;
                    $item->remark = $itemData['remark'] ?? null;
                    $item->department_code = $itemData['department_code'] ?? null;
                    $item->supplier_name = $itemData['supplier_name'] ?? null;
                    $item->brand_name = $itemData['brand_name'] ?? null;
                    $item->model_name = $itemData['model_name'] ?? null;

                    if (!$item->save()) {
//                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
                        throw new \Exception('Failed to save item: ' . json_encode($item->getErrors()));
                    }
                }

                // If we reach here, commit the transaction
                $transaction->commit();
                FlashHandler::success('Success!');
            } catch (Exception $e) {
                // Something went wrong, rollback the transaction
                $transaction->rollBack();
                FlashHandler::err('Failed: ' . $e->getMessage());
//                \common\models\myTools\Mydebug::dumpFileW([
//                    'master_errors' => $master->getErrors(),
//                    'exception' => $e->getMessage()
//                ]);
            }

            return $this->redirect(['personal-pending-approval']);
        }
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        return $this->render('create', [
                    'master' => $master,
                    'items' => $items,
                    'vmodel' => $vmodel,
                    'isUpdate' => false,
                    'isView' => false,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
        ]);
    }

//    public function actionCreate($moduleIndex) {
//        $master = new PrereqFormMaster();
//        $vmodel = new VPrereqFormMasterDetail();
//        $items = [new PrereqFormItem()];
//        $worklists = [];
//        $hasSuperiorUpdate = false;
//
//        if (Yii::$app->request->isPost) {
//            $transaction = Yii::$app->db->beginTransaction();
//
//            try {
//                // ===== SAVE MASTER =====
//                $postMaster = Yii::$app->request->post('PrereqFormMaster');
//                $master->date_of_material_required = $postMaster['date_of_material_required'] ?? null;
//                $master->prf_no = $master->generatePrfNo();
//                $master->superior_id = Yii::$app->user->identity->superior_id;
//                $master->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
//                $master->is_deleted = 0;
//
//                if (!$master->save()) {
//                    throw new \Exception('Master save failed: ' . json_encode($master->getErrors()));
//                }
//
//                // ===== SAVE ITEMS =====
//                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);
//                $this->saveItems($master->id, $postItems, false);
//
//                $transaction->commit();
//                FlashHandler::success('Purchase Requisition Form created successfully!');
//                return $this->redirect(['personal-pending-approval']);
//            } catch (\Exception $e) {
//                $transaction->rollBack();
//                FlashHandler::err('Failed to create form: ' . $e->getMessage());
//                Yii::error('Create PRF Error: ' . $e->getMessage(), __METHOD__);
//            }
//        }
//
//        // ===== DROPDOWN DATA =====
//        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
//        $supplierList = InventorySupplier::getAllDropDownSupplierList();
//        $brandList = InventoryBrand::getAllDropDownBrandList();
//        $modelList = InventoryModel::getAllDropDownModelList();
//        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();
//
//        return $this->render('create', [
//                    'master' => $master,
//                    'items' => $items,
//                    'vmodel' => $vmodel,
//                    'isUpdate' => false,
//                    'isView' => false,
//                    'moduleIndex' => $moduleIndex,
//                    'worklists' => $worklists,
//                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
//                    'departmentList' => $departmentList,
//                    'supplierList' => $supplierList,
//                    'brandList' => $brandList,
//                    'modelList' => $modelList,
//                    'currencyList' => $currencyList,
//        ]);
//    }

    /**
     * Updates the current PrereqFormMaster model.
     * If update is successful, the browser will be redirected to the 'view' page
     * $vmodel is passed in as an array
     * @param type $id (id of the current PrereqFormMaster model)
     * @return mixed
     */
    public function actionUpdate($id, $moduleIndex) {
        $master = PrereqFormMaster::find()
                ->where(['id' => $id])
                ->with(['prereqFormItems'])
                ->one();
//        echo "<script>console.log('number of items: " . count($master->items) . "');</script>";
        $vmodel = VPrereqFormMasterDetail::find()
                ->where(['master_id' => $id])
                ->with(['items'])
                ->all();
        // check for items returned
//        $itemIds = [];
//        foreach ($vmodel as $v) {
//            foreach ($v->items as $item) {
//                $itemIds[] = $item->id;
//                echo "<script>console.log('Item ID: " . $item->id . "')</script>";
//            }
//        }
        $items = $master->prereqFormItems;    // retrieves items with is_deleted == 0
        // pass in the worklist model as a variable
        $worklists = [];

        $hasSuperiorUpdate = false;
        foreach ($items as $i => $item) {
            $worklist = PrereqFormItemWorklist::findOne([
                'prereq_form_master_id' => $id,
                'prereq_form_item_id' => $item->id
            ]);

            if (!$worklist) {
                $worklist = new PrereqFormItemWorklist();
                $worklist->prereq_form_master_id = $id;
                $worklist->prereq_form_item_id = $item->id;
            } else {
                $hasSuperiorUpdate = true;
            }
            $worklists[$i] = $worklist;
//            $worklists[$i]->save(false);
        }

        if (empty($items)) {
            $items = [new PrereqFormItem()];
        }

        if (Yii::$app->request->isPost) {
            // Delete existing items before resaving
            $itemIds = PrereqFormItem::find()
                    ->select('id')
                    ->where(['prereq_form_master_id' => $id, 'is_deleted' => 0])
                    ->column();

//            PrereqFormItemWorklist::deleteAll(
//                    ['prereq_form_item_id' => $itemIds]
//            );

            PrereqFormItem::updateAll(
//                    ['prereq_form_master_id' => $id, 'is_deleted' => 0]
                    ['is_deleted' => 1],
                    ['id' => $itemIds]
            );

            $postMaster = Yii::$app->request->post('PrereqFormMaster');

            $master->date_of_material_required = $postMaster['date_of_material_required'];
//            $master->total_amount = $postMaster['total_amount'];

            $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);
            $postItemsNew = Yii::$app->request->post('PrereqFormItem', []);
            if(empty($postItems) && empty($postItemsNew)){
                $master->is_deleted = 1;
            }
            
            if ($master->save()) {
//                if (!empty($postItems)) {
                foreach ($postItems as $itemData) {
                    if (isset($itemData['id'])) {
                        $item = PrereqFormItem::findOne($itemData['id']);
                    } else {
                        // New item
                        $item = new PrereqFormItem();
                    }

                    $item->prereq_form_master_id = $master->id;
                    $item->item_description = $itemData['item_description'];
                    $item->quantity = $itemData['quantity'] ?? null;
                    $item->currency = $itemData['currency'];
                    $item->unit_price = $itemData['unit_price'] ?? null;
                    $item->total_price = $itemData['total_price'] ?? null;
                    $item->purpose_or_function = $itemData['purpose_or_function'] ?? null;
                    $item->remark = $itemData['remark'] ?? null;
                    $item->department_code = $itemData['department_code'] ?? null;
                    $item->supplier_name = $itemData['supplier_name'] ?? null;
                    $item->brand_name = $itemData['brand_name'] ?? null;
                    $item->model_name = $itemData['model_name'] ?? null;

                    if (!$item->save()) {
                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
                    }
                }

                foreach ($postItemsNew as $itemData) {
                    if (isset($itemData['id'])) {
                        $item = PrereqFormItem::findOne($itemData['id']);
                    } else {
                        // New item
                        $item = new PrereqFormItem();
                    }

                    $item->prereq_form_master_id = $master->id;
                    $item->item_description = $itemData['item_description'];
                    $item->quantity = $itemData['quantity'] ?? null;
                    $item->currency = $itemData['currency'];
                    $item->unit_price = $itemData['unit_price'] ?? null;
                    $item->total_price = $itemData['total_price'] ?? null;
                    $item->purpose_or_function = $itemData['purpose_or_function'] ?? null;
                    $item->remark = $itemData['remark'] ?? null;
                    $item->department_code = $itemData['department_code'] ?? null;
                    $item->supplier_name = $itemData['supplier_name'] ?? null;
                    $item->brand_name = $itemData['brand_name'] ?? null;
                    $item->model_name = $itemData['model_name'] ?? null;

                    if (!$item->save()) {
                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
                    }
                }

                // delete all records that are 'unintentionally' marked as deleted
                $redundantIds = PrereqFormItem::find()
                        ->select('id')
                        ->where(['is_deleted' => 1, 'updated_by' => null])
                        ->column();

                PrereqFormItemWorklist::deleteAll(
                        ['prereq_form_item_id' => $redundantIds]
                );

                PrereqFormItem::deleteAll(
                        ['is_deleted' => 1, 'updated_by' => null]
                );
//                }
                $this->rememberDBIds('prereq_form_item');

                FlashHandler::success('Updated!');
                return $this->redirect(['view', 'id' => $master->id, 'moduleIndex' => $moduleIndex]);
            } else {
                FlashHandler::err('Failed to update!');
//                \common\models\myTools\Mydebug::dumpFileW($master->getErrors());
            }
        }
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        return $this->render('update', [
                    'master' => $master,
                    'vmodel' => $vmodel,
                    'items' => $items,
                    'isUpdate' => true,
                    'isView' => false,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
        ]);
    }

    /**
     * Superior's response to PRF
     */
//    public function actionSaveSuperiorUpdate($id, $moduleIndex) {
//        $master = PrereqFormMaster::find()
//                ->where(['id' => $id])
//                ->with(['prereqFormItems'])
//                ->one();
//        $vmodel = VPrereqFormMasterDetail::find()
//                ->where(['master_id' => $id])
//                ->with(['items'])
//                ->all();
//
//        $items = $master->prereqFormItems;
//        $worklists = [];
//
//        $hasSuperiorUpdate = false;
//        foreach ($items as $item) {
//            $worklists[$item->id] = PrereqFormItemWorklist::findOne([
//                'prereq_form_master_id' => $id,
//                'prereq_form_item_id' => $item->id
//            ]);
//
//            if (!$worklists[$item->id]) {
//                $worklists[$item->id] = new PrereqFormItemWorklist();
//                $worklists[$item->id]->prereq_form_master_id = $id;
//                $worklists[$item->id]->prereq_form_item_id = $item->id;
//            } else {
//                $hasSuperiorUpdate = true;
//            }
//        }
//
//        if (Yii::$app->request->isPost) {
//            try {
//                $postWorklist = Yii::$app->request->post('PrereqFormItemWorklist', []);
//                $postMaster = Yii::$app->request->post('PrereqFormMaster', []);
//                $postItem = Yii::$app->request->post('VPrereqFormMasterDetail', []);
//
//                foreach ($postWorklist as $itemId => $wlData) {
//                    $worklist = $worklists[$itemId];
//                    $worklist->status = $wlData['status'];
//                    $worklist->remark = trim($wlData['remark']);
//                    $worklist->responded_by = Yii::$app->user->identity->id;
//
//                    if (!$worklist->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($worklist->getErrors());
//                    }
//                }
//                // update items
//                $itemIds = PrereqFormItem::find()
//                        ->select('id')
//                        ->where(['prereq_form_master_id' => $id, 'is_deleted' => 0])
//                        ->column();
//                // avoid duplicate rows
//                PrereqFormItem::updateAll(
//                        ['is_deleted' => 1],
//                        ['id' => $itemIds]
//                );
//
//                $postWorklist = array_values($postWorklist);
//                $postItem = array_values($postItem);
//                $worklists = array_values($worklists);
//                foreach ($postWorklist as $itemId => $wl) {
//                    $wl = $worklists[$itemId];
//                    // access the referenced item
//                    $item = PrereqFormItem::findOne([
//                        'id' => $wl->prereq_form_item_id,
//                        'prereq_form_master_id' => $worklist->prereq_form_master_id,
//                    ]);
//
//                    if (!$item) {
//                        $item = new PrereqFormItem();
//                        $item->prereq_form_master_id = $master->id;
//                    }
//
//                    $item->prereq_form_master_id = $master->id;
//                    $item->quantity_approved = $postItem[$itemId]['quantity_approved'] ?? null;
//                    $item->currency_approved = $postItem[$itemId]['currency_approved'];
//                    $item->unit_price_approved = $postItem[$itemId]['unit_price_approved'] ?? null;
//                    $item->total_price_approved = $postItem[$itemId]['total_price_approved'] ?? null;
//                    $item->is_deleted = 0;
//
//                    if ($wl->status == RefGeneralStatus::STATUS_Approved) {
//                        $item->status = 0;
//                        $item->remark = '';
//                    } else if ($wl->status == RefGeneralStatus::STATUS_SuperiorRejected) {
//                        $item->status = 1;
//                        $item->remark = $wl->remark ?? null;
//                    }
//
//                    if (!$item->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
//                    }
//                }
//
//                // check prereq_form_item table
//                $checkMaster = PrereqFormMaster::find()
//                        ->where(['id' => $id])
//                        ->with(['checkPrereqFormItems'])
//                        ->one();
//
//                $checkItems = $checkMaster->checkPrereqFormItems;
//
//                if (!empty($checkItems)) {
//                    $master->status = RefGeneralStatus::STATUS_Approved;
//                } else {
//                    $master->status = RefGeneralStatus::STATUS_SuperiorRejected;
//                }
//
//                if ($master->save()) {
//                    FlashHandler::success('Success!');
//                    return $this->redirect([$moduleIndex . '-pending-approval']);
//                }
//            } catch (Exception $e) {
//                // Something went wrong, rollback the transaction
////                $transaction->rollBack();
//                FlashHandler::err('Failed: ' . $e->getMessage());
//            }
//        }
//        return $this->render('save_superior_update', [
//                    'master' => $master,
//                    'items' => $items,
//                    'worklists' => $worklists,
//                    'moduleIndex' => $moduleIndex,
//                    'vmodel' => $vmodel,
//                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
//                    'isView' => false,
//                    'isUpdate' => true,
//                    'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist(),
//        ]);
//    }

    public function actionSaveSuperiorUpdate($id, $moduleIndex) {
        // ===== Load master and related items =====
        $master = PrereqFormMaster::find()
                ->where(['id' => $id])
                ->with(['prereqFormItems'])
                ->one();

        if (!$master) {
            throw new NotFoundHttpException('Pre-Requisition Form not found');
        }

        $items = $master->prereqFormItems;
        $vmodel = VPrereqFormMasterDetail::find()
                ->where(['master_id' => $id])
                ->all();

        // ===== Prepare worklists =====
        $worklists = [];
        $hasSuperiorUpdate = false;

        foreach ($items as $item) {
            $worklist = PrereqFormItemWorklist::findOne([
                'prereq_form_master_id' => $id,
                'prereq_form_item_id' => $item->id
            ]);

            if (!$worklist) {
                $worklist = new PrereqFormItemWorklist();
                $worklist->prereq_form_master_id = $id;
                $worklist->prereq_form_item_id = $item->id;
            } else {
                $hasSuperiorUpdate = true;
            }

            $worklists[$item->id] = $worklist;
        }

        // ===== POST handling =====
        if (Yii::$app->request->isPost) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();

            try {
                $postWorklist = Yii::$app->request->post('PrereqFormItemWorklist', []);
                $postItems = Yii::$app->request->post('VPrereqFormMasterDetail', []);

                // 1️⃣ Save worklist updates
                foreach ($postWorklist as $itemId => $wlData) {
                    $wl = $worklists[$itemId];
                    $wl->status = $wlData['status'];
                    $wl->remark = trim($wlData['remark'] ?? '');
                    $wl->responded_by = Yii::$app->user->id;

                    if (!$wl->save(false)) {
                        throw new \Exception('Failed to save worklist for item ID ' . $itemId);
                    }
                }

                // 2️⃣ Update each item with approved data
                foreach ($items as $item) {
                    $itemId = $item->id;
                    $wl = $worklists[$itemId];
                    $postItem = $postItems[$itemId] ?? [];

                    $item->quantity_approved = $postItem['quantity_approved'] ?? null;
                    $item->currency_approved = $postItem['currency_approved'] ?? null;
                    $item->unit_price_approved = $postItem['unit_price_approved'] ?? null;
                    $item->total_price_approved = $postItem['total_price_approved'] ?? null;

                    if ($wl->status == RefGeneralStatus::STATUS_Approved) {
                        $item->status = 0; // approved
                        $item->remark = null;
                    } elseif ($wl->status == RefGeneralStatus::STATUS_SuperiorRejected) {
                        $item->status = 1; // rejected
                        $item->remark = $wl->remark ?? null;
                    }

                    $item->is_deleted = 0;

                    if (!$item->save(false)) {
                        throw new \Exception('Failed to save item ID ' . $itemId);
                    }

                    // sync source module
                    $item->syncSourceModule();
                }

                $approvedItems = PrereqFormItem::find()
                        ->where(['prereq_form_master_id' => $id, 'status' => 0, 'is_deleted' => 0])
                        ->exists();

                $master->status = $approvedItems ? RefGeneralStatus::STATUS_Approved : RefGeneralStatus::STATUS_SuperiorRejected;

                if (!$master->save(false)) {
                    throw new \Exception('Failed to update PRF master status');
                }

                $transaction->commit();
                FlashHandler::success('Approval saved successfully!');
                return $this->redirect([$moduleIndex . '-pending-approval']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage(), __METHOD__);
                FlashHandler::err('Failed: ' . $e->getMessage());
            }
        }

        return $this->render('save_superior_update', [
                    'master' => $master,
                    'items' => $items,
                    'worklists' => $worklists,
                    'vmodel' => $vmodel,
                    'moduleIndex' => $moduleIndex,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'isView' => false,
                    'isUpdate' => true,
                    'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist(),
        ]);
    }

    //by khetty
//    public function actionSaveSuperiorUpdate($id, $moduleIndex) {
//        $master = PrereqFormMaster::findOne($id);
//        if (!$master) {
//            FlashHandler::err('Record not found');
//            return $this->redirect([$moduleIndex . '-pending-approval']);
//        }
//
//        $vmodel = VPrereqFormMasterDetail::find()
//                ->where(['master_id' => $id])
//                ->all();
//
//        $items = $master->prereqFormItems;
//        $worklists = [];
//        $hasSuperiorUpdate = false;
//
//        // ===== BUILD WORKLISTS =====
//        foreach ($items as $item) {
//            $worklist = PrereqFormItemWorklist::findOne([
//                'prereq_form_master_id' => $id,
//                'prereq_form_item_id' => $item->id
//            ]);
//
//            if (!$worklist) {
//                $worklist = new PrereqFormItemWorklist();
//                $worklist->prereq_form_master_id = $id;
//                $worklist->prereq_form_item_id = $item->id;
//            } else if ($worklist->responded_by !== null) {
//                $hasSuperiorUpdate = true;
//            }
//
//            $worklists[$item->id] = $worklist;
//        }
//
//        if (Yii::$app->request->isPost) {
//            $transaction = Yii::$app->db->beginTransaction();
//
//            try {
//                $postWorklist = Yii::$app->request->post('PrereqFormItemWorklist', []);
//                $postItem = Yii::$app->request->post('VPrereqFormMasterDetail', []);
//
//                // ===== UPDATE WORKLISTS =====
//                foreach ($postWorklist as $itemId => $wlData) {
//                    $worklist = $worklists[$itemId];
//                    $worklist->status = $wlData['status'];
//                    $worklist->remark = trim($wlData['remark'] ?? '');
//
//                    if (!$worklist->save()) {
//                        throw new \Exception('Worklist save failed: ' . json_encode($worklist->getErrors()));
//                    }
//                }
//
//                // ===== MARK OLD ITEMS AS DELETED =====
//                PrereqFormItem::updateAll(
//                        ['is_deleted' => 1],
//                        ['prereq_form_master_id' => $id, 'is_deleted' => 0]
//                );
//
//                // ===== SAVE ITEMS WITH APPROVED VALUES =====
//                foreach ($items as $index => $originalItem) {
//                    $item = new PrereqFormItem();
//                    $item->prereq_form_master_id = $master->id;
//
//                    // Copy original values
//                    $item->department_code = $originalItem->department_code;
//                    $item->supplier_name = $originalItem->supplier_name;
//                    $item->brand_name = $originalItem->brand_name;
//                    $item->model_name = $originalItem->model_name;
//                    $item->item_description = $originalItem->item_description;
//                    $item->quantity = $originalItem->quantity;
//                    $item->currency = $originalItem->currency;
//                    $item->unit_price = $originalItem->unit_price;
//                    $item->total_price = $originalItem->total_price;
//                    $item->purpose_or_function = $originalItem->purpose_or_function;
//
//                    // Set approved values from post
//                    $itemPostData = $postItem[$originalItem->id] ?? [];
//                    $item->quantity_approved = $itemPostData['quantity_approved'] ?? null;
//                    $item->currency_approved = $itemPostData['currency_approved'] ?? null;
//                    $item->unit_price_approved = $itemPostData['unit_price_approved'] ?? null;
//                    $item->total_price_approved = $itemPostData['total_price_approved'] ?? null;
//
//                    // Set status and remark based on worklist
//                    $worklist = $worklists[$originalItem->id];
//                    if ($worklist->status == RefGeneralStatus::STATUS_Approved) {
//                        $item->status = 0;
//                        $item->remark = '';
//                    } else if ($worklist->status == RefGeneralStatus::STATUS_SuperiorRejected) {
//                        $item->status = 1;
//                        $item->remark = $worklist->remark;
//                    }
//
//                    $item->is_deleted = 0;
//                    $item->created_by = $originalItem->created_by;
//                    $item->created_at = $originalItem->created_at;
//                    $item->updated_by = Yii::$app->user->id;
//                    $item->updated_at = date('Y-m-d H:i:s');
//
//                    if (!$item->save()) {
//                        throw new \Exception('Item save failed: ' . json_encode($item->getErrors()));
//                    }
//
//                    // Update worklist to point to new item
//                    $worklist->prereq_form_item_id = $item->id;
//                    $worklist->save(false);
//                }
//
//                // ===== UPDATE MASTER STATUS =====
//                // ===== GET APPROVED ITEMS (ARRAY, NOT COUNT!) =====
//                $approvedItems = PrereqFormItem::find()
//                        ->where([
//                            'prereq_form_master_id' => $id,
//                            'is_deleted' => 0,
//                            'status' => 0
//                        ])
//                        ->all(); // ← USE ->all() NOT ->count()
//
//                if (!empty($approvedItems)) {
//                    $master->status = RefGeneralStatus::STATUS_Approved;
//                } else {
//                    $master->status = RefGeneralStatus::STATUS_SuperiorRejected;
//                }
//
//                $master->updated_by = Yii::$app->user->id;
//                $master->updated_at = date('Y-m-d H:i:s');
//
//                if (!$master->save()) {
//                    throw new \Exception('Master update failed: ' . json_encode($master->getErrors()));
//                }
//
//                $transaction->commit();
//                FlashHandler::success('Response saved successfully!');
//                return $this->redirect([$moduleIndex . '-pending-approval']);
//            } catch (\Exception $e) {
//                $transaction->rollBack();
//                FlashHandler::err('Failed to save response: ' . $e->getMessage());
//                Yii::error('Save Superior Update Error: ' . $e->getMessage(), __METHOD__);
//            }
//        }
//
//        $currencyList = \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist();
//
//        return $this->render('save_superior_update', [
//                    'master' => $master,
//                    'items' => $items,
//                    'worklists' => $worklists,
//                    'moduleIndex' => $moduleIndex,
//                    'vmodel' => $vmodel,
//                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
//                    'isView' => false,
//                    'isUpdate' => true,
//                    'currencyList' => $currencyList,
//        ]);
//    }

    /**
     * Save items (used by both Create and Update)
     */
//    private function saveItems($masterId, $postItems, $saveWorklist) {
//        foreach ($postItems as $itemData) {
//            $item = isset($itemData['id']) ? PrereqFormItem::findOne($itemData['id']) : new PrereqFormItem();
//            if (!$item) {
//                $item = new PrereqFormItem();
//            }
//
//            $exists = InventoryDetail::find()
//        ->where([
//            'department_code' => $item['department_code'],
//            'supplier_id'     => $item['supplier_id'],
//            'brand_id'        => $item['brand_id'],
//            'is_deleted'      => 0,
//        ])
//        ->andWhere(['LOWER(model_name)' => mb_strtolower(trim($item['model_name']))])
//        ->exists();
//
//    if ($exists) {
//        throw new \Exception(
//            'Duplicate inventory item detected at row ' . ($index + 1)
//        );
//    }
//    
//            $item->prereq_form_master_id = $masterId;
//
//            $supplierName = $itemData['supplier_name'] ?? null;
//            $brandName = $itemData['brand_name'] ?? null;
//            $modelName = $itemData['model_name'] ?? null;
//
//            if (($itemData['department_code'] ?? '') === 'maintenance') {
//                if ($supplierName) {
//                    $supplier = InventorySupplier::findOne($supplierName);
//                    $supplierName = $supplier ? $supplier->name : $supplierName;
//                }
//                if ($brandName) {
//                    $brand = InventoryBrand::findOne($brandName);
//                    $brandName = $brand ? $brand->name : $brandName;
//                }
//                if ($modelName) {
//                    $model = InventoryModel::findOne($modelName);
//                    $modelName = $model ? $model->type : $modelName;
//                }
//            }
//
//            // ===== ASSIGN =====
//            $item->department_code = $itemData['department_code'] ?? null;
//            $item->supplier_name = $supplierName;
//            $item->brand_name = $brandName;
//            $item->model_name = $modelName;
////            $item->inventory_id = $itemData['inventory_id'] ?? null;
//            $item->item_description = $itemData['item_description'] ?? null;
//            $item->quantity = $itemData['quantity'] ?? null;
//            $item->currency = $itemData['currency'] ?? null;
//            $item->unit_price = $itemData['unit_price'] ?? null;
//            $item->total_price = $itemData['total_price'] ?? null;
//            $item->purpose_or_function = $itemData['purpose_or_function'] ?? null;
//            $item->remark = $itemData['remark'] ?? null;
//            $item->is_deleted = 0;
//
//            if (!$item->save()) {
//                throw new \Exception('Item save failed: ' . json_encode($item->getErrors()));
//            }
//
//            // ===== WORKLIST =====
//            if ($saveWorklist) {
//                $worklist = PrereqFormItemWorklist::findOne([
//                    'prereq_form_master_id' => $masterId,
//                    'prereq_form_item_id' => $item->id,
//                ]);
//
//                if (!$worklist) {
//                    $worklist = new PrereqFormItemWorklist();
//                    $worklist->prereq_form_master_id = $masterId;
//                    $worklist->prereq_form_item_id = $item->id;
//                    $worklist->status = RefGeneralStatus::STATUS_GetSuperiorApproval;
//                }
//
//                if (!$worklist->save()) {
//                    throw new \Exception('Worklist save failed: ' . json_encode($worklist->getErrors()));
//                }
//            }
//        }
//    }
    // Pre-requisition form to inventory control module
//    public function actionProceedToPurchasing($id) {
//        $prereqMaster = PrereqFormMaster::findOne($id);
//
//        if (!$prereqMaster) {
//            FlashHandler::err('Purchase requisition form not found');
//            return $this->redirect(['index']);
//        }
//
//        $items = $prereqMaster->prereqFormItems;
//
//        // ===== GROUP BY DEPARTMENT =====
//        $itemsByDepartment = [];
//        foreach ($items as $item) {
//            if (empty($item->department_code)) {
//                \Yii::warning("Item {$item->id} has no department code, skipping");
//                continue;
//            }
//            $itemsByDepartment[$item->department_code][] = $item;
//        }
//
//        if (empty($itemsByDepartment)) {
//            FlashHandler::err('No valid items with department codes found');
//            return $this->redirect(['view', 'id' => $id]);
//        }
//
//        $transaction = Yii::$app->db->beginTransaction();
//
//        try {
//            $hasCreated = false;
//            $processedDepartments = [];
//
//            foreach ($itemsByDepartment as $deptCode => $deptItems) {
//                // Check if department has inventory system implemented
//                $hasBeenImplemented = InventoryDetail::find()
//                        ->where(['department_code' => $deptCode])
//                        ->exists();
//
//                if ($hasBeenImplemented) {
//                    // Check if reorder record already exists
//                    $reorderMaster = InventoryReorderMaster::findOne([
//                        'prereq_form_master_id' => $prereqMaster->id,
//                        'department_code' => $deptCode
//                    ]);
//
//                    if ($reorderMaster === null) {
//                        $reorderMaster = new InventoryReorderMaster();
//                        $reorderMaster->prereq_form_master_id = $prereqMaster->id;
//                        $reorderMaster->department_code = $deptCode;
//                        $reorderMaster->requested_at = $prereqMaster->created_at;
//                        $reorderMaster->requested_by = $prereqMaster->created_by;
//                        $reorderMaster->approved_by = $prereqMaster->superior_id;
//                        $reorderMaster->status = RefInventoryStatus::STATUS_PendingReceive;
//
//                        if (!$reorderMaster->save()) {
//                            throw new \Exception("Failed to save reorder master for dept {$deptCode}: " . json_encode($reorderMaster->getErrors()));
//                        }
//                    }
//
//                    // Create reorder items
//                    $created = $prereqMaster->createReorderItem($reorderMaster, $deptItems);
//
//                    if ($created) {
//                        $hasCreated = true;
//                        $processedDepartments[] = $deptCode;
//                    }
//                } else {
//                    \Yii::warning("Inventory system not yet implemented for department: {$deptCode}");
//                    continue;
//                }
//            }
//
//            if ($hasCreated) {
//                $prereqMaster->inventory_flag = 1;
//
//                if (!$prereqMaster->save(false)) {
//                    throw new \Exception('Failed to update inventory flag: ' . json_encode($prereqMaster->getErrors()));
//                }
//
//                $transaction->commit();
//                $deptCount = count($processedDepartments);
//                $deptList = implode(', ', $processedDepartments);
//                FlashHandler::success("Successfully created reorder records for {$deptCount} department(s): {$deptList}");
//            } else {
//                $transaction->commit();
//                FlashHandler::warn('No new reorder records were created. All items may already have reorder records or no items have inventory IDs.');
//            }
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            \Yii::error("Proceed to purchasing failed: {$e->getMessage()}", __METHOD__);
//            FlashHandler::err('Failed to create reorder records: ' . $e->getMessage());
//        }
//
//        return $this->redirect(['personal-all-approval']);
//    }

    /**
     * AJAX: Get inventory items based on department
     */
    public function actionGetInventoryByDepartment($department_code) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $inventories = VInventoryDetail::find()
                    ->where(['department_code' => $department_code])
                    ->distinct()
                    ->orderBy(['supplier_name' => SORT_ASC])
                    ->all();

            // Get unique suppliers
            $suppliers = [];
            $brands = [];
            $models = [];

            foreach ($inventories as $inv) {
                if (!isset($suppliers[$inv->supplier_id])) {
                    $suppliers[$inv->supplier_id] = $inv->supplier_name;
                }
                if (!isset($brands[$inv->brand_id])) {
                    $brands[$inv->brand_id] = $inv->brand_name;
                }
                if (!isset($models[$inv->model_id])) {
                    $models[$inv->model_id] = $inv->model_type;
                }
            }

            return [
                'success' => true,
                'suppliers' => $suppliers,
                'brands' => $brands,
                'models' => $models
            ];
        } catch (\Exception $e) {
            Yii::error('Get Inventory By Department Error: ' . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'suppliers' => [],
                'brands' => [],
                'models' => []
            ];
        }
    }

    /**
     * AJAX: Get suppliers based on department
     */
    public function actionGetSuppliers($department_code) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $suppliers = VInventoryDetail::find()
                    ->select(['supplier_id', 'supplier_name'])
                    ->where(['department_code' => $department_code])
                    ->distinct()
                    ->orderBy(['supplier_name' => SORT_ASC])
                    ->all();

            $supplierList = [];
            foreach ($suppliers as $supplier) {
                $supplierList[$supplier->supplier_id] = $supplier->supplier_name;
            }

            return $supplierList;
        } catch (\Exception $e) {
            Yii::error('Get Suppliers Error: ' . $e->getMessage(), __METHOD__);
            return [];
        }
    }

    /**
     * AJAX: Get brands based on department and supplier
     */
    public function actionGetBrands($department_code = null, $supplier_id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $query = VInventoryDetail::find()
                    ->select(['brand_id', 'brand_name'])
                    ->distinct()
                    ->orderBy(['brand_name' => SORT_ASC]);

            $where = [];
            if ($department_code) {
                $where['department_code'] = $department_code;
            }
            if ($supplier_id) {
                $where['supplier_id'] = $supplier_id;
            }

            if (!empty($where)) {
                $query->where($where);
            }

            $brands = $query->all();

            $brandList = [];
            foreach ($brands as $brand) {
                $brandList[$brand->brand_id] = $brand->brand_name;
            }

            return $brandList;
        } catch (\Exception $e) {
            Yii::error('Get Brands Error: ' . $e->getMessage(), __METHOD__);
            return [];
        }
    }

    /**
     * AJAX: Get models based on department, supplier and brand
     */
    public function actionGetModels($department_code = null, $supplier_id = null, $brand_id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $query = VInventoryDetail::find()
                    ->select(['model_id', 'model_type'])
                    ->distinct()
                    ->orderBy(['model_type' => SORT_ASC]);

            $where = [];
            if ($department_code) {
                $where['department_code'] = $department_code;
            }
            if ($supplier_id) {
                $where['supplier_id'] = $supplier_id;
            }
            if ($brand_id) {
                $where['brand_id'] = $brand_id;
            }

            if (!empty($where)) {
                $query->where($where);
            }

            $models = $query->all();

            $modelList = [];
            foreach ($models as $model) {
                $modelList[$model->model_id] = $model->model_type;
            }

            return $modelList;
        } catch (\Exception $e) {
            Yii::error('Get Models Error: ' . $e->getMessage(), __METHOD__);
            return [];
        }
    }

    /**
     * AJAX: Get inventory ID based on department, supplier, brand, and model
     */
    public function actionGetInventoryId($department_code = null, $supplier_id = null, $brand_id = null, $model_id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $where = [];
            if ($department_code) {
                $where['department_code'] = $department_code;
            }
            if ($supplier_id) {
                $where['supplier_id'] = $supplier_id;
            }
            if ($brand_id) {
                $where['brand_id'] = $brand_id;
            }
            if ($model_id) {
                $where['model_id'] = $model_id;
            }

            $inventory = VInventoryDetail::find()
                    ->where($where)
                    ->one();

            if ($inventory) {
                return [
                    'success' => true,
//                    'inventory_id' => $inventory->inventory_id,
                    'description' => $inventory->model_description ?? ''
                ];
            } else {
                return [
                    'success' => false,
//                    'inventory_id' => null,
                    'message' => 'Inventory not found'
                ];
            }
        } catch (\Exception $e) {
            Yii::error('Get Inventory ID Error: ' . $e->getMessage(), __METHOD__);
            return [
                'success' => false,
//                'inventory_id' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Rearranges the IDs of the items to avoid "gaps" in the ID listing
     * @param type $tableName   name of the table to be updated
     */
    protected function rememberDBIds($tableName) {
        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();

        Yii::$app->db->createCommand("SET @count = 0")->execute();
        Yii::$app->db->createCommand("
                                        UPDATE {$tableName}
                                        SET id = (@count := @count + 1)
                                        ORDER BY id
                                    ")->execute();

        Yii::$app->db->createCommand("ALTER TABLE prereq_form_item AUTO_INCREMENT = 1")->execute();

        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS = 1")->execute();
    }

    //updated on 4/10/2025 by khetty
//    public function actionSaveSuperiorUpdate($id, $moduleIndex) {
//        $master = PrereqFormMaster::find()
//                ->where(['id' => $id])
//                ->with(['prereqFormItems'])
//                ->one();
//        $vmodel = VPrereqFormMasterDetail::find()
//                ->where(['master_id' => $id])
//                ->with(['items'])
//                ->all();
//
//        $items = $master->prereqFormItems;
//        $worklists = [];
//
//        $hasSuperiorUpdate = false;
//        \common\models\myTools\Mydebug::dumpFileW($items);
//
//        foreach ($items as $item) {
//            $worklists[$item->id] = PrereqFormItemWorklist::findOne([
//                'prereq_form_master_id' => $id,
//                'prereq_form_item_id' => $item->id
//            ]);
//
//            if (!$worklists[$item->id]) {
//                $worklists[$item->id] = new PrereqFormItemWorklist();
//                $worklists[$item->id]->prereq_form_master_id = $id;
//                $worklists[$item->id]->prereq_form_item_id = $item->id;
//            } else {
//                $hasSuperiorUpdate = true;
//            }
//        }
//
//        if (Yii::$app->request->isPost) {
//            try {
//                $postWorklist = Yii::$app->request->post('PrereqFormItemWorklist', []);
//                $postMaster = Yii::$app->request->post('PrereqFormMaster', []);
//                $postItem = Yii::$app->request->post('VPrereqFormMasterDetail', []);
//
//                foreach ($postWorklist as $itemId => $wlData) {
//                    $worklist = $worklists[$itemId];
//                    $worklist->status = $wlData['status'];
//                    $worklist->remark = trim($wlData['remark']);
//                    $worklist->responded_by = Yii::$app->user->identity->id;
//
//                    if (!$worklist->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($worklist->getErrors());
//                    }
//                }
//                // update items
//                $itemIds = PrereqFormItem::find()
//                        ->select('id')
//                        ->where(['prereq_form_master_id' => $id, 'is_deleted' => 0])
//                        ->column();
//                // avoid duplicate rows
//                PrereqFormItem::updateAll(
//                        ['is_deleted' => 1],
//                        ['id' => $itemIds]
//                );
//
//                $postWorklist = array_values($postWorklist);
//                $postItem = array_values($postItem);
//                $worklists = array_values($worklists);
//                foreach ($postWorklist as $itemId => $wl) {
//                    $wl = $worklists[$itemId];
//                    // access the referenced item
//                    $item = PrereqFormItem::findOne([
//                        'id' => $wl->prereq_form_item_id,
//                        'prereq_form_master_id' => $worklist->prereq_form_master_id,
//                    ]);
//
//                    if (!$item) {
//                        $item = new PrereqFormItem();
//                        $item->prereq_form_master_id = $master->id;
//                    }
//
//                    $item->prereq_form_master_id = $master->id;
//                    $item->quantity_approved = $postItem[$itemId]['quantity_approved'] ?? null;
//                    $item->currency_approved = $postItem[$itemId]['currency_approved'];
//                    $item->unit_price_approved = $postItem[$itemId]['unit_price_approved'] ?? null;
//                    $item->total_price_approved = $postItem[$itemId]['total_price_approved'] ?? null;
//                    $item->is_deleted = 0;
//
//                    if ($wl->status == RefGeneralStatus::STATUS_Approved) {
//                        $item->status = 0;
//                        $item->remark = '';
//                    } else if ($wl->status == RefGeneralStatus::STATUS_SuperiorRejected) {
//                        $item->status = 1;
//                        $item->remark = $wl->remark ?? null;
//                    }
//
//                    if (!$item->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
//                    }
//                }
//
//                // check prereq_form_item table
//                $checkMaster = PrereqFormMaster::find()
//                        ->where(['id' => $id])
//                        ->with(['checkPrereqFormItems'])
//                        ->one();
//
//                $checkItems = $checkMaster->checkPrereqFormItems;
//
//                if (!empty($checkItems)) {
//                    $master->status = RefGeneralStatus::STATUS_Approved;
//                } else {
//                    $master->status = RefGeneralStatus::STATUS_SuperiorRejected;
//                }
//
//                if ($master->save()) {
//                    FlashHandler::success('Success!');
//                    return $this->redirect([$moduleIndex . '-pending-approval']);
//                }
//            } catch (Exception $e) {
//                // Something went wrong, rollback the transaction
////                $transaction->rollBack();
//                FlashHandler::err('Failed: ' . $e->getMessage());
//            }
//        }
//        return $this->render('save_superior_update', [
//                    'master' => $master,
//                    'items' => $items,
//                    'worklists' => $worklists,
//                    'moduleIndex' => $moduleIndex,
//                    'vmodel' => $vmodel,
//                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
//                    'isView' => false,
//                    'isUpdate' => true
//        ]);
//    }
//    public function actionSaveSuperiorUpdate($id, $moduleIndex) {
//        $master = PrereqFormMaster::find()
//                ->where(['id' => $id])
//                ->with(['prereqFormItems'])
//                ->one();
//        $vmodel = VPrereqFormMasterDetail::find()
//                ->where(['master_id' => $id])
//                ->with(['items'])
//                ->all();
//
//        $items = $master->prereqFormItems;    // retrieves items with is_deleted == 0
//        // pass in the worklist model as a variable
//        $worklists = [];
////        
//        $hasSuperiorUpdate = false;
//        foreach ($items as $item) {
//            $worklists[$item->id] = PrereqFormItemWorklist::findOne([
//                'prereq_form_master_id' => $id,
//                'prereq_form_item_id' => $item->id
//            ]);
//
//            if (!$worklists[$item->id]) {
//                $worklists[$item->id] = new PrereqFormItemWorklist();
//                $worklists[$item->id]->prereq_form_master_id = $id;
//                $worklists[$item->id]->prereq_form_item_id = $item->id;
//            } else {
//                $hasSuperiorUpdate = true;
//            }
////            $worklists[$item->id]->save(false);
//        }
//
//        if (Yii::$app->request->isPost) {
//            try {
//                $postWorklist = Yii::$app->request->post('PrereqFormItemWorklist', []);
//                $postMaster = Yii::$app->request->post('PrereqFormMaster', []);
//                $postItem = Yii::$app->request->post('VPrereqFormMasterDetail', []);
//
//                foreach ($postWorklist as $itemId => $wlData) {
//                    $worklist = $worklists[$itemId];
//                    $worklist->status = $wlData['status'];
//                    $worklist->remark = trim($wlData['remark']);
//                    $worklist->responded_by = Yii::$app->user->identity->id;
//
//                    if (!$worklist->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($worklist->getErrors());
//                    }
//                }
//                // update items
//                $itemIds = PrereqFormItem::find()
//                        ->select('id')
//                        ->where(['prereq_form_master_id' => $id, 'is_deleted' => 0])
//                        ->column();
//                // avoid duplicate rows
//                PrereqFormItem::updateAll(
//                        ['is_deleted' => 1],
//                        ['id' => $itemIds]
//                );
//
//                $postWorklist = array_values($postWorklist);
//                $postItem = array_values($postItem);
//                $worklists = array_values($worklists);
//                foreach ($postWorklist as $itemId => $wl) {
//                    $wl = $worklists[$itemId];
//                    // access the referenced item
//                    $item = PrereqFormItem::findOne([
//                        'id' => $wl->prereq_form_item_id,
//                        'prereq_form_master_id' => $worklist->prereq_form_master_id,
//                    ]);
//
//                    if (!$item) {
//                        $item = new PrereqFormItem();
//                        $item->prereq_form_master_id = $master->id;
//                    }
//
//                    $item->prereq_form_master_id = $master->id;
//                    $item->item_description = $postItem[$itemId]['item_description'];
//                    $item->quantity = $postItem[$itemId]['quantity'] ?? null;
//                    $item->currency = $postItem[$itemId]['currency'];
//                    $item->unit_price = $postItem[$itemId]['unit_price'] ?? null;
//                    $item->total_price = $postItem[$itemId]['total_price'] ?? null;
//                    $item->purpose_or_function = $postItem[$itemId]['purpose_or_function'] ?? null;
//                    $item->is_deleted = 0;
//
//                    if ($wl->status == RefGeneralStatus::STATUS_Approved) {
//                        $item->status = 0;
//                        $item->remark = '';
//                    } else if ($wl->status == RefGeneralStatus::STATUS_SuperiorRejected) {
////                    } else if ((int)$worklists[$wl]->status === RefGeneralStatus::STATUS_SuperiorRejected) {
//                        $item->status = 1;
//                        $item->remark = $wl->remark ?? null;
//                    }
//
//                    if (!$item->save(false)) {
//                        \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
//                    }
//                }
//
//                // check prereq_form_item table
//                $checkMaster = PrereqFormMaster::find()
//                        ->where(['id' => $id])
//                        ->with(['checkPrereqFormItems'])
//                        ->one();
//
//                $checkItems = $checkMaster->checkPrereqFormItems;
//
//                if (!empty($checkItems)) {
//                    $master->status = RefGeneralStatus::STATUS_Approved;
//                } else {
//                    $master->status = RefGeneralStatus::STATUS_SuperiorRejected;
//                }
//
//                if ($master->save()) {
//                    FlashHandler::success('Success!');
//                    return $this->redirect([$moduleIndex . '-pending-approval']);
//                }
//            } catch (Exception $e) {
//                // Something went wrong, rollback the transaction
////                $transaction->rollBack();
//                FlashHandler::err('Failed: ' . $e->getMessage());
//            }
//        }
//        return $this->render('save_superior_update', [
//                    'master' => $master,
//                    'items' => $items,
//                    'worklists' => $worklists,
//                    'moduleIndex' => $moduleIndex,
//                    'vmodel' => $vmodel,
//                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
//                    'isView' => false,
//                    'isUpdate' => true
//        ]);
////        return $this->redirect(['view', 'id' => $master->id, 'moduleIndex' => $moduleIndex]);
//    }

    /**
     * Deletes an existing PrereqFormMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $master = $this->findModel(PrereqFormMaster::class, $id);
        PrereqFormItem::deleteAll([
            'prereq_form_master_id' => $master->id]);
//        $master->delete();
        $master->is_deleted = 1;
        if (!$master->save()) {
            \common\models\myTools\Mydebug::dumpFileW($master->getErrors());
        }

        return $this->redirect(['personal-pending-approval']);
    }

    public function actionAjaxAddFormItem($key, $masterId, $moduleIndex, $hasSuperiorUpdate) {
        $departmentList = \frontend\models\common\RefUserDepartments::getDropDownList();
        $formItem = new VPrereqFormMasterDetail();
        $master = PrereqFormMaster::findOne($masterId);

        // assume that if masterId exists, it's in update mode
        $isUpdate = !empty($masterId);

//           changed into this to enable addRow() to work
        return $this->renderPartial('_form_row', [
                    'model' => $formItem,
                    'key' => $key,
                    'master' => $master,
                    'form' => \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form']),
                    'isUpdate' => $isUpdate,
                    'isView' => false,
                    'moduleIndex' => $moduleIndex,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist(),
                    'departmentList' => $departmentList
        ]);
    }

    /**
     * Deletes a PrereqFormItem
     */
    public function actionAjaxDeleteItem($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $item = PrereqFormItem::findOne($id);

        if (!$item) {
            return ['success' => false, 'error' => 'Item not found'];
        }

        $masterId = $item->prereq_form_master_id;

        $item->is_deleted = 1;
        $item->updated_by = Yii::$app->user->identity->id;
        if ($item->save(false)) {
//            \common\models\myTools\Mydebug::dumpFileW($item->getErrors());
//
//            $remainingCount = PrereqFormItem::find()
//                    ->where([
//                        'prereq_form_master_id' => $masterId,
//                        'is_deleted' => 0
//                    ])
//                    ->count();
//
//            if ($remainingCount == 0) {
//                $master = PrereqFormMaster::findOne($masterId);
//                if ($master) {
//                    $master->is_deleted = 1;
//                    $master->save(false);
//                }
//                return [
//                    'success' => true,
//                    'redirect' => 'personal-pending-approval',
//                ];
//            }

            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to deleted item'];
    }

    /*     * *************************************** Superior ********************************************* */

    public function actionSuperiorPendingApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superiorPending');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'superior'
        ]);
    }

    public function actionSuperiorAllApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superiorAll');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'all',
                    'moduleIndex' => 'superior',
        ]);
    }

    /*     * *************************************** Superior ********************************************* */

    public function actionSuperuserPendingApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superuserPending');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'pending',
                    'moduleIndex' => 'superuser'
        ]);
    }

    public function actionSuperuserAllApproval() {
        $searchModel = new PrereqFormMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'approvalStatus' => 'all',
                    'moduleIndex' => 'superuser',
        ]);
    }

    /**
     * Finds the table models based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PrereqFormMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($modelName, $id) {
        if (($model = $modelName::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($id) {
        try {
            $uploadDir = Yii::getAlias('@frontend/uploads/pre-requisition-form/');
            \yii\helpers\FileHelper::createDirectory($uploadDir);

            // Get the master record
            $master = PrereqFormMaster::findOne($id);
            if (!$master) {
                throw new \yii\web\NotFoundHttpException('Record not found');
            }

            // Generate filename from prf_no
            $filename = $master->prf_no . '.pdf';
            $completePath = $uploadDir . $filename;

            // Generate new PDF
            $items = PrereqFormItem::find()->where(['prereq_form_master_id' => $master->id, 'is_deleted' => 0])->all();
            $worklists = [];

            $hasSuperiorUpdate = false;
            foreach ($items as $item) {
                $worklists[$item->id] = PrereqFormItemWorklist::findOne([
                    'prereq_form_master_id' => $id,
                    'prereq_form_item_id' => $item->id
                ]);

                if (!$worklists[$item->id]) {
                    $worklists[$item->id] = new PrereqFormItemWorklist();
                    $worklists[$item->id]->prereq_form_master_id = $id;
                    $worklists[$item->id]->prereq_form_item_id = $item->id;
                } else {
                    $hasSuperiorUpdate = true;
                }
            }

            $mpdf = $this->generatePdf($master, $items, $worklists);
            $mpdf->Output($completePath, 'F');

            // Update database with the filename
            $master->filename = $filename;
            $master->save();

            return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
        } catch (\Exception $e) {
            \Yii::error('PDF Generation Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            throw new \yii\web\ServerErrorHttpException('Unable to generate or retrieve PDF file: ' . $e->getMessage());
        }
    }

    private function generatePdf($master, $items, $worklists) {
        ini_set("pcre.backtrack_limit", "10000000");
        ini_set("memory_limit", "1024M");

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'Arial',
            'orientation' => 'L',
            'setAutoTopMargin' => "stretch",
            'setAutoBottomMargin' => "stretch",
            'defaultheaderline' => 0,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
        ]);

        $htmlBody = $this->renderPartial("_prereqFormPdf", [
            'master' => $master,
            'items' => $items,
            'worklist' => $worklists
        ]);

        // Split HTML into chunks
        $this->writeHtmlInChunks($mpdf, $htmlBody);

        return $mpdf;
    }

    private function writeHtmlInChunks($mpdf, $html) {
        $chunkSize = 500000; // 500KB chunks
        $htmlLength = strlen($html);

        if ($htmlLength <= $chunkSize) {
            $mpdf->WriteHTML($html);
            return;
        }

        $chunks = str_split($html, $chunkSize);

        foreach ($chunks as $chunk) {
            $mpdf->WriteHTML($chunk);
        }
    }

    public function actionUserManualPersonal() {
        $this->layout = false;
        $fileName = PrereqFormMaster::PERSONAL_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManualSuperior() {
        $this->layout = false;
        $fileName = PrereqFormMaster::SUPERIOR_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionUserManualSuperuser() {
        $this->layout = false;
        $fileName = PrereqFormMaster::SUPERUSER_USER_MANUAL_FILENAME;
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }
}
