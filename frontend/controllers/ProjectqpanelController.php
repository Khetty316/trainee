<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectquotation\ProjectQPanels;
use frontend\models\projectquotation\ProjectQPanelSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\projectquotation\ProjectQPanelItems;
use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;

/**
 * ProjectqpanelController implements the CRUD actions for ProjectQPanels model.
 */
class ProjectqpanelController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
//                            'actions' => ['asset-checking'],
                        'allow' => true,
                        'roles' => ['@']
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
     * Lists all ProjectQPanels models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProjectQPanelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectQPanels model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewProjectQPanel($id, $isDisabled) {
        return $this->render('viewProjectQPanel', [
                    'model' => $this->findModel($id),
                    'isDisabled' => $isDisabled
        ]);
    }

    /**
     * Creates a new ProjectQPanels model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ProjectQPanels();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectQPanels model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($panelId) {
        $model = $this->findModel($panelId);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('/projectqrevision/_ajaxPanelDetail', [
                        'formAction' => '/projectqpanel/update?panelId=' . $panelId,
                        'submitBtnText' => 'Update',
                        'model' => $model
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            $revision = $model->revision;
            $revision->updateRevisionAmount();
            FlashHandler::success("Updated");
            return $this->redirect(['/projectqrevision/view-project-q-revision', 'id' => $model->revision_id]);
        }
    }

    /**
     * Deletes an existing ProjectQPanels model.
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
     * Finds the ProjectQPanels model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectQPanels the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectQPanels::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdatePanelName($isDisabled) {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $editPanelName = $req->post('editPanelName');
            $editPanelId = $req->post('editPanelId');
            $panel = ProjectQPanels::findOne($editPanelId);
            $panel->panel_description = $editPanelName;
            $panel->update();
        }
        return $this->redirect(['view-project-q-panel', 'id' => $editPanelId, 'isDisabled' => $isDisabled]);
    }

    public function actionUpdatePanelRemark($isDisabled) {
        $req = Yii::$app->request;

        $id = $req->post('ProjectQPanels')['id'];
        $model = $this->findModel($id);

        if ($model->load($req->post()) && $model->update()) {
            FlashHandler::success("Updated");
        }

        return $this->redirect(['view-project-q-panel', 'id' => $id, 'isDisabled' => $isDisabled]);
    }

    public function actionAddPanelItemAjax() {
        if (Yii::$app->request->isAjax) {

            $panelId = Yii::$app->request->post('panelId');
            $itemDesc = Yii::$app->request->post('itemDesc');
            $itemPrice = Yii::$app->request->post('itemPrice');
            $itemQty = Yii::$app->request->post('itemQty');
            $itemUnit = Yii::$app->request->post('itemUnit');
            $model = new ProjectQPanelItems();

            if ($model->processNewItem($panelId, $itemDesc, $itemPrice, $itemQty, $itemUnit)) {
                return json_encode(["success" => true, "msg" => "Item added", "itemDesc" => Html::encode($model->item_description),
                    "itemPrice" => number_format($model->amount, 2), "itemId" => $model->id, 'itemQty' => $model->quantity, 'itemUnit' => $model->unitCode->unit_name, 'itemTotalAmt' => number_format($model->quantity * $model->amount, 2)]);
            } else {
                return json_encode(["success" => false, "msg" => "Unable to add."]);
            }
        } else {
            return json_encode(["success" => false, "msg" => "Fail to add"]);
        }
    }

    public function actionLoadPanelItemAjax() {
        if (Yii::$app->request->isPost) {
            $model = ProjectQPanelItems::findOne(Yii::$app->request->post('itemId'));
            if ($model) {
                return json_encode(["success" => true, "itemDesc" => ($model->item_description), "itemPrice" => MyFormatter::asDecimal2NoSeparator($model->amount), 'itemQty' => $model->quantity, 'itemUnit' => $model->unit_code,]);
            }
        }

        return json_encode(["success" => false, "msg" => "No found"]);
    }

    public function actionUpdatePanelItemAjax() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = ProjectQPanelItems::findOne(Yii::$app->request->post('itemId'));
            $model->item_description = Yii::$app->request->post('itemDesc');
            $model->amount = Yii::$app->request->post('itemPrice');
            $model->quantity = Yii::$app->request->post('itemQty');
            $model->unit_code = Yii::$app->request->post('itemUnit');
            if ($model->update()) {
                return json_encode(["success" => true, "msg" => "Item updated", "itemDesc" => Html::encode($model->item_description),
                    "itemPrice" => number_format($model->amount, 2), "itemId" => $model->id, 'itemQty' => $model->quantity, 'itemUnit' => $model->unitCode->unit_name, 'itemTotalAmt' => number_format($model->quantity * $model->amount, 2)]);
            }
        }
        return json_encode(["success" => false, "msg" => "Fail to update"]);
    }

    public function actionRemovePanelItemAjax() {
        if (Yii::$app->request->isPost) {
            $model = ProjectQPanelItems::findOne(Yii::$app->request->post('itemId'));
            if ($model->delete()) {
                return json_encode(["success" => true, "msg" => "Item removed"]);
            }
        }
        return json_encode(["success" => false, "msg" => "Fail to remove"]);
    }

    public function actionSortPanelItemAjax() {
        $panelId = Yii::$app->request->post('panelId');
        $moveId = substr_replace(Yii::$app->request->post('moveId'), "", 0, 3);
        $previousId = substr_replace(Yii::$app->request->post('previousId'), "", 0, 3);
        $itemList = ProjectQPanelItems::find()->where(['panel_id' => $panelId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortItems($moveId, $previousId, $itemList);
        return json_encode(['success' => $result]);
    }

    public function actionUpdatePanelPriceMethodAjax() {
        if (Yii::$app->request->isAjax) {
            $panelId = Yii::$app->request->post('panelId');
            $byItemPrice = Yii::$app->request->post('byItemPrice');
            $model = ProjectQPanels::findOne($panelId);
            $model->by_item_price = $byItemPrice;

            if ($model->update()) {
                return json_encode(["success" => true]);
            } else {
                return json_encode(["success" => false, "msg" => "Unable to add."]);
            }
        } else {
            return json_encode(["success" => false, "msg" => "Fail to add"]);
        }
    }

    public function actionCalculatePanelAmount() {
        $panelId = Yii::$app->request->post('panelId');
        $model = ProjectQPanels::findOne($panelId);
        if ($model->updatePanelAmount()) {
            $revision = $model->revision;
            $revision->updateRevisionAmount();
            return json_encode(["success" => true, "totalAmount" => MyFormatter::asDecimal2($model->amount)]);
        }
        return json_encode(["success" => false, "totalAmount" => MyFormatter::asDecimal2(0)]);
    }

    private function sortItems($moveId, $previousId, $list) {

        $idxNo = 0;
        foreach ($list as $key => $panelItem) {
            if ($panelItem->id == $moveId) {
                $idxNo = $key;
                break;
            }
        }
        $tempObject = $list[$idxNo];
        unset($list[$idxNo]);
        $theKey = $previousId == "" ? 0 : (array_search($previousId, array_column($list, 'id')) + 1);
        array_splice($list, $theKey, 0, array($tempObject));

        foreach ($list as $key => $panelItem) {
            $panelItem->updateSort(($key + 1));
        }

        return true;
    }

    /**
     * *********** EXPORT - to CSV
     */
    public function actionExportToCsv($panelId) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;

        $projQPanel = ProjectQPanels::findOne($panelId);
        $itemList = ProjectQPanelItems::find()->where(['panel_id' => $panelId])->orderBy(['sort' => SORT_ASC])->all();

        return $this->renderPartial('_itemListCSV', [
                    'projQPanel' => $projQPanel,
                    'itemList' => $itemList,
                    'sst' => $sst
        ]);
    }

    public function actionGetPanelFromProjQAutocomplete($projectQId, $term) {
        $data = ProjectQPanels::getPanelsFromActiveRevisionAutocompleteList($projectQId, $term);
        return \yii\helpers\Json::encode($data);
    }

}
