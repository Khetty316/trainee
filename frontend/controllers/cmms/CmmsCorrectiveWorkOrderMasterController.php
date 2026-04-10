<?php

namespace frontend\controllers\cmms;

use frontend\models\cmms\CmmsFaultList;
use frontend\models\cmms\RefAssignedPic;
use frontend\models\inventory\VInventoryModel;
use Yii;
use frontend\models\cmms\CmmsCorrectiveWorkOrderMaster;
use frontend\models\cmms\CmmsCorrectiveWorkOrderMasterSearch;
use frontend\models\cmms\CmmsPartList;
use frontend\models\cmms\CmmsToolList;
use frontend\models\cmms\RefProgressStatus;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * CmmsCorrectiveWorkOrderMasterController implements the CRUD actions for CmmsCorrectiveWorkOrderMaster model.
 */
class CmmsCorrectiveWorkOrderMasterController extends Controller {

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
                'only' => ['view-superior', 'view-assigned-tasks', 'view-fault-list-ids',
                    'update', 'delete', 'remove-pic', 'view-selected-material', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view-fault-list-ids', 'view-selected-material'],
                        'roles' => [AuthItem::ROLE_CMMS_Superior, AuthItem::ROLE_CMMS_Normal],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view-superior', 'update', 'delete', 'remove-pic'],
                        'roles' => [AuthItem::ROLE_CMMS_Superior],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view-assigned-tasks'],
                        'roles' => [AuthItem::ROLE_CMMS_Normal],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CmmsCorrectiveWorkOrderMaster models.
     * @return mixed
     */
//    public function actionIndex() {
//        $searchModel = new CmmsCorrectiveWorkOrderMasterSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//        ]);
//    }
    
    public function actionUserManualInventory() {
        $this->layout = false;
        $fileName = "T5B-CMMS Module-02.pdf";
        $fileUrl = Yii::getAlias('@web/uploads/user-manual/' . $fileName);

        // Add timestamp to prevent caching
        $fileUrl .= '?v=' . time();

        return $this->render('/user-manual', [
                    'fileUrl' => $fileUrl,
        ]);
    }

    public function actionViewSuperior() {
        $searchModel = new CmmsCorrectiveWorkOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'superior');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleStatus' => 'superior',
        ]);
    }

    public function actionViewAssignedTasks() {
        $searchModel = new CmmsCorrectiveWorkOrderMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'assignedTasks');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'moduleStatus' => 'assigned_pic',
        ]);
    }

    /**
     * Displays a single CmmsCorrectiveWorkOrderMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionViewFaultListIds($id, $moduleStatus) {
        $faultLists = CmmsFaultList::find()
                ->where(['cmms_corrective_work_order_id' => $id])
                ->all();

        $model = CmmsCorrectiveWorkOrderMaster::findOne(['id' => $id]);

        $partList = VInventoryModel::find()
                ->select(['brand_model', 'id'])
                ->where(['departments' => "mecha"])
                ->indexBy('id')
                ->column();

        $toolList = VInventoryModel::find()
                ->select(['brand_model', 'id'])
                ->where(['departments' => "mecha"])
                ->indexBy('id')
                ->column();

        return $this->renderAjax('_view_fault_lists', [
                    'model' => $model,
                    'faultLists' => $faultLists,
                    'partList' => $partList,
                    'toolList' => $toolList,
                    'moduleStatus' => $moduleStatus,
        ]);
    }

    /**
     * Creates a new CmmsCorrectiveWorkOrderMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CmmsCorrectiveWorkOrderMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing CmmsCorrectiveWorkOrderMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $moduleStatus) {
        $model = $this->findModel($id);

        $assignedPICs = $model->assignedPic ?: [
            new RefAssignedPic(['corrective_work_order_master_id' => $model->id])
        ];

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->load(Yii::$app->request->post())) {
                    throw new \Exception('Failed to load model data');
                }

                if (!$model->save()) {
                    throw new \Exception('Failed to save corrective work order');
                }

                //calculate the duration
                if (!empty($model->start_date) && !empty($model->end_date)) {
                    $start = new \DateTime($model->start_date);
                    $end = new \DateTime($model->end_date);

                    // Difference in days
                    $model->duration = $start->diff($end)->days;
                } else {
                    $model->duration = null;
                }

                $postPICs = Yii::$app->request->post('RefAssignedPic', []);
//                Yii::error($postPICs, 'DEBUG_PICS');
                $savedIds = [];
                foreach ($postPICs as $row) {
                    $pic = !empty($row['id']) ? RefAssignedPic::findOne($row['id']) : new RefAssignedPic();

                    $pic->load($row, '');
                    $pic->corrective_work_order_master_id = $model->id;
                    $pic->active_sts = 1;

                    $staffID = User::find()
                            ->select('id')
                            ->where(['fullname' => $row['name']])
                            ->scalar();
                    $pic->staff_id = $staffID;

                    if ($pic->save()) {
                        $savedIds[] = $pic->id; // ID exists NOW
                    } else {
                        Yii::error($pic->errors, 'assignedPIC');
                    }
                }

                RefAssignedPic::deleteAll([
                    'and',
                    ['corrective_work_order_master_id' => $model->id],
                    ['not in', 'id', $savedIds],
                ]);

                if (!empty($postPICs)) {
                    $model->progress_status_id = RefProgressStatus::$STATUS_ASSIGNED;
                }

                if (!$model->save()) {
                    throw new \Exception('Failed to save corrective work order');
                }

                $transaction->commit();

                // Re-query assignedPICs AFTER save
                $assignedPICs = $model->assignedPic;

                if ($moduleStatus === 'superior') {
                    return $this->redirect(['view-superior']);
                }

                return $this->redirect(['view-assigned-tasks']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return $this->renderAjax('update', [
                    'model' => $model,
                    'assignedPICs' => $assignedPICs,
                    'moduleStatus' => $moduleStatus
        ]);
    }

    /**
     * Deletes an existing CmmsCorrectiveWorkOrderMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        CmmsFaultList::updateAll(
                ['status' => \frontend\models\cmms\RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION],
                ['cmms_corrective_work_order_id' => $id],
        );
        CmmsFaultList::updateAll(
                ['cmms_corrective_work_order_id' => null],
                ['cmms_corrective_work_order_id' => $id]
        );
        $this->findModel($id)->delete();

        return $this->redirect(['view-superior']);
    }

    public function actionRemovePic() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $picId = Yii::$app->request->post('picID');
        if (!$picId) {
            throw new \yii\web\BadRequestHttpException('Missing picID');
        }
        RefAssignedPic::updateAll(
                ['active_sts' => 0],
                ['id' => $picId],
        );
        RefAssignedPic::updateAll(
                ['corrective_work_order_master_id' => null],
                ['id' => $picId],
        );

        return ['success' => true];
    }

    /**
     * Finds the CmmsCorrectiveWorkOrderMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CmmsCorrectiveWorkOrderMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CmmsCorrectiveWorkOrderMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewSelectedMaterial($id, $moduleIndex) {
        $model = CmmsCorrectiveWorkOrderMaster::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException("Work Order #$id not found.");
        }

        $materialMaster = \frontend\models\cmms\CmmsWoMaterialRequestMaster::findOne([
            'wo_type' => \frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM,
            'wo_id' => $model->id,
        ]);

        $details = [];
        $faults = [];

        if ($materialMaster !== null) {
            $details = $materialMaster->cmmsWoMaterialRequestDetails ?? [];
            foreach ($details as $detail) {
                $faultId = $detail->fault_id;
                if (!isset($faults[$faultId])) {
                    $fault = CmmsFaultList::findOne($faultId);
                    $faults[$faultId] = [
                        'fault' => $fault,
                        'materialDetails' => [],
                    ];
                }
                $faults[$faultId]['materialDetails'][] = $detail;
            }
        }

        // If no faults from material details, fall back to the WO's fault list
        if (empty($faults)) {
            $faultList = CmmsFaultList::findAll(['cmms_corrective_work_order_id' => $model->id]);
            foreach ($faultList as $fault) {
                $faults[$fault->id] = [
                    'fault' => $fault,
                    'materialDetails' => [],
                ];
            }
        }

        $partToolList = \frontend\models\inventory\InventoryModel::getModelBrandCombinations();

        return $this->render('materialRequestDetailCm', [
                    'model' => $model,
                    'materialMaster' => $materialMaster,
                    'faults' => $faults,
                    'partToolList' => $partToolList,
                    'moduleIndex' => $moduleIndex,
                    'wotype' => \frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM,
        ]);
    }
}
