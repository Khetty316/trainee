<?php

namespace frontend\controllers;

use Yii;
use frontend\models\projectquotation\ProjectQRevisionsTemplate;
use frontend\models\projectquotation\ProjectQRevisionsTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\common\RefCurrencies;
use common\models\myTools\FlashHandler;
use frontend\models\projectquotation\ProjectQPanelsTemplate;
use frontend\models\projectquotation\ProjectQPanelItemsTemplate;
use common\models\myTools\MyFormatter;
use yii\helpers\Html;

/**
 * ProjectqtemplateController implements the CRUD actions for ProjectQRevisionsTemplate model.
 */
class ProjectqtemplateController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
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
     * Lists all ProjectQRevisionsTemplate models.
     * @return mixed
     */
    public function actionIndexpqrevision() {
        $searchModel = new ProjectQRevisionsTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/projectquotation/template/indexPQRevision', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectQRevisionsTemplate model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewpqrevision($id) {
        return $this->render('/projectquotation/template/viewPQRevision', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing ProjectQRevisionsTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdatepqrevision($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("Updated");
            return $this->redirect(['viewpqrevision', 'id' => $model->id]);
        }
        $currencyList = RefCurrencies::getActiveDropdownlist_by_id();

        return $this->render('/projectquotation/template/updatePQRevision', [
                    'model' => $model,
                    'currencyList' => $currencyList
        ]);
    }

    /**
     * Deletes an existing ProjectQRevisionsTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeletepqrevision($id) {
        $model = $this->findModel($id);
        if ($model->processAndDelete()) {
            FlashHandler::success("Template deleted.");
        }
//        $model->delete(false);

        return $this->redirect(['indexpqrevision']);
    }

    public function actionDeactivateSelected() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $request = Yii::$app->request;
            $selectAll = $request->post('selectAll', false);

            if ($selectAll) {
                // "Select All" mode: Deactivate ALL active templates except excluded ones
                $excludedIds = $request->post('excludedIds', []);

                // Build query for all active templates
                $query = ProjectQRevisionsTemplate::find()
                        ->where(['is_active' => 1]);

                // Exclude manually unchecked IDs
                if (!empty($excludedIds)) {
                    $query->andWhere(['not in', 'id', $excludedIds]);
                }

                $models = $query->all();

                if (empty($models)) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'No templates to deactivate.'
                    ];
                }

                $count = 0;
                foreach ($models as $model) {
                    $model->is_active = 0;
                    $model->deactivated_at = new \yii\db\Expression('NOW()');
                    $model->deactivated_by = Yii::$app->user->identity->id;

                    if (!$model->save()) {
                        throw new \Exception('Failed to update template ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                    }
                    $count++;
                }

                $transaction->commit();
                FlashHandler::success("$count template(s) deactivated successfully.");

                return [
                    'success' => true,
                    'message' => "$count template(s) deactivated successfully."
                ];
            } else {
                // Normal mode: Deactivate specific IDs only
                $ids = $request->post('ids', []);

                if (empty($ids)) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'No templates selected.'
                    ];
                }

                $count = 0;
                foreach ($ids as $id) {
                    $model = $this->findModel($id);
                    if (!$model) {
                        throw new NotFoundHttpException('Template not found.');
                    }

                    // Only deactivate if it's currently active
                    if ($model->is_active == 1) {
                        $model->is_active = 0;
                        $model->deactivated_at = new \yii\db\Expression('NOW()');
                        $model->deactivated_by = Yii::$app->user->identity->id;

                        if (!$model->save()) {
                            throw new \Exception('Failed to update template ID ' . $model->id . ': ' . implode(', ', $model->getFirstErrors()));
                        }
                        $count++;
                    }
                }

                $transaction->commit();
                FlashHandler::success("$count template(s) deactivated successfully.");

                return [
                    'success' => true,
                    'message' => "$count template(s) deactivated successfully."
                ];
            }
        } catch (NotFoundHttpException $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed to deactivate: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to deactivate: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Failed to deactivate: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to deactivate: ' . $e->getMessage()
            ];
        }
    }

    public function actionDeactivatepqrevision($id) {
        $model = $this->findModel($id);
        $model->is_active = 0;
        $model->deactivated_at = new \yii\db\Expression('NOW()');
        $model->deactivated_by = Yii::$app->user->identity->id;
        if ($model->save(false)) {
            FlashHandler::success("Template deactivated!");
        } else {
            FlashHandler::success("Failed to deactivate this template!");
        }

        return $this->render('/projectquotation/template/viewPQRevision', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionReactivatepqrevision($id) {
        $model = $this->findModel($id);
        $model->is_active = 1;
        $model->deactivated_at = null;
        $model->deactivated_by = null;
        if ($model->save(false)) {
            FlashHandler::success("Template reactivated!");
        } else {
            FlashHandler::success("Failed to activate this template!");
        }

        return $this->render('/projectquotation/template/viewPQRevision', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the ProjectQRevisionsTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectQRevisionsTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProjectQRevisionsTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionViewpqpanel($id) {
        $panelTemplate = \frontend\models\projectquotation\ProjectQPanelsTemplate::findOne($id);
        return $this->render('/projectquotation/template/viewPQPanel', [
                    'model' => $panelTemplate
        ]);
    }

    public function actionNewPanelTemplate() {
        $model = new ProjectQPanelsTemplate();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                $model->revisionTemplate->updateRevisionAmount();
                FlashHandler::success("New panel created");
            }
        }
        return $this->redirect(['update-p-q-template-revision-panel', 'id' => $model->revision_template_id]);
    }

    public function actionUpdatePQTemplateRevisionPanel($id) {
        $model = $this->findModel($id);
        return $this->render('/projectquotation/template/updatePQTemplateRevisionPanel', [
                    'model' => $model,
                    'currencyList' => \frontend\models\common\RefCurrencies::getActiveDropdownlist_by_id()
        ]);
    }

    /**
     * Added on 02/02/2024
     * @param type $panelId
     * @return type
     */
    public function actionUpdatePQPanel($panelId) {
        $model = ProjectQPanelsTemplate::findOne($panelId);
        if (empty($model)) {
            $model = new ProjectQPanelsTemplate();
        }
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            FlashHandler::success("Updated");
            return $this->redirect(['/projectqtemplate/update-p-q-template-revision-panel', 'id' => $model->revision_template_id]);
        }

        return $this->renderAjax('/projectquotation/template/_ajaxPanelDetail', [
                    'submitBtnText' => 'Update',
                    'model' => $model
        ]);
    }

    /**
     * Added on 02/02/2024
     * @param type $panelId
     * @return type
     */
    public function actionAddPQPanel($revisionId) {
        $model = new ProjectQPanelsTemplate();
        $model->revision_template_id = $revisionId;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            FlashHandler::success("Updated");
            return $this->redirect(['/projectqtemplate/update-p-q-template-revision-panel', 'id' => $model->revision_template_id]);
        }

        return $this->renderAjax('/projectquotation/template/_ajaxPanelDetail', [
                    'submitBtnText' => 'Update',
                    'model' => $model
        ]);
    }

    // ************* CLONE TEMPLATE **********************
    public function actionCloneTemplate() {
        if (!Yii::$app->request->isPost) {
            FlashHandler::err("Illegal access");
            return $this->redirect(['/']);
        }

        $revisionId = Yii::$app->request->post('ProjectQRevisionsTemplate')['id'];
        $templateName = Yii::$app->request->post('ProjectQRevisionsTemplate')['revision_description'];
        $revisionMother = $this->findModel($revisionId);
        $revisionTemplate = new ProjectQRevisionsTemplate();
        $revisionTemplate->cloneTemplate($revisionMother, $templateName);
        FlashHandler::success("Template cloned");
        return $this->redirect(['viewpqrevision', 'id' => $revisionTemplate->id]);
    }

    // ************* EDIT REVISION DETAIL **********************
    public function actionUpdatePQTemplateRevisionDetail($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->processAndUpdate()) {
            FlashHandler::success("Updated.");
            return $this->redirect(['update-p-q-template-revision-panel', 'id' => $id]);
        }

        return $this->render('/projectquotation/template/updatePQTemplateRevisionDetail', [
                    'model' => $model,
                    'currencyList' => \frontend\models\common\RefCurrencies::getActiveDropdownlist_by_id()
        ]);
    }

    // ************ EDIT REVISION - Panel ************************
    public function actionSortPanelsAjax() {
        $req = Yii::$app->request;
        $revisionId = $req->post('revisionId');
        $moveId = substr_replace($req->post('moveId'), "", 0, 3);
        $previousId = substr_replace($req->post('previousId'), "", 0, 3);
        $panelList = ProjectQPanelsTemplate::find()->where(['revision_template_id' => $revisionId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortPanels($moveId, $previousId, $panelList);
        return json_encode(['success' => $result]);
    }

    public function actionControlSstAjax() {
        $req = Yii::$app->request;
        $revision = $this->findModel($req->post('revisionId'));
        $revision->with_sst = $req->post('enableFlag');
        $result = $revision->update(false);
        return json_encode(['success' => $result]);
    }

    private function sortPanels($moveId, $previousId, $panelList) {
        $idxNo = 0;
        foreach ($panelList as $key => $panel) {
            if ($panel->id == $moveId) {
                $idxNo = $key;
                break;
            }
        }
        $tempObject = $panelList[$idxNo];
        unset($panelList[$idxNo]);
        $theKey = $previousId == "" ? 0 : (array_search($previousId, array_column($panelList, 'id')) + 1);
        array_splice($panelList, $theKey, 0, array($tempObject));

        foreach ($panelList as $key => $panel) {
            $panel->updateSort(($key + 1));
        }

        return true;
    }

    public function actionRemovePanelAjax() {
        $panelId = Yii::$app->request->post('panelId');
        $panel = ProjectQPanelsTemplate::findOne($panelId);
        ProjectQPanelItemsTemplate::deleteAll(['panel_template_id' => $panelId]);
        if ($panel->delete()) {
            $revision = $panel->revisionTemplate;
            $revision->updateRevisionAmount();
            return json_encode(["success" => true, "msg" => "Panel removed"]);
        }
        return json_encode(["success" => false, "msg" => "Fail to remove"]);
    }

    public function actionClonePanelSameRevision() {
        $req = Yii::$app->request;
        $motherPanelId = $req->post('motherPanelId');
        $clonePanelNewName = $req->post('clonePanelNewName');
        $newPanel = new ProjectQPanelsTemplate();
        $newPanel->cloneFromMother($motherPanelId, $clonePanelNewName);
        $revision = $newPanel->revisionTemplate;
        $revision->updateRevisionAmount();
        return $this->redirect(['update-p-q-template-revision-panel', 'id' => $newPanel->revision_template_id]);
    }

    // ************************ EDIT PANEL ITEM ****************************
    public function actionUpdatePQTemplatePanelItem($id) {

        return $this->render('/projectquotation/template/updatePQTemplatePanelItem', [
                    'model' => ProjectQPanelsTemplate::findOne($id),
        ]);
    }

    public function actionAddTemplatePanelItemAjax() {
        $req = Yii::$app->request;
        if ($req->isAjax) {
            $panelId = $req->post('panelId');
            $itemDesc = $req->post('itemDesc');
            $itemPrice = $req->post('itemPrice');
            $model = new ProjectQPanelItemsTemplate();

            if ($model->processNewItem($panelId, $itemDesc, $itemPrice)) {
                return json_encode(["success" => true, "msg" => "Item added", "itemDesc" => Html::encode($model->item_description),
                    "itemPrice" => MyFormatter::asDecimal2($model->amount), "itemId" => $model->id]);
            } else {
                return json_encode(["success" => false, "msg" => "Unable to add."]);
            }
        } else {
            return json_encode(["success" => false, "msg" => "Fail to add"]);
        }
    }

    public function actionLoadPanelItemTemplateAjax() {
        if (Yii::$app->request->isPost) {
            $model = ProjectQPanelItemsTemplate::findOne(Yii::$app->request->post('itemId'));
            if ($model) {
                return json_encode(["success" => true, "itemDesc" => ($model->item_description), "itemPrice" => MyFormatter::asDecimal2NoSeparator($model->amount)]);
            }
        }

        return json_encode(["success" => false, "msg" => "No found"]);
    }

    public function actionUpdatePanelItemTemplateAjax() {
        if (Yii::$app->request->isPost) {
            $model = ProjectQPanelItemsTemplate::findOne(Yii::$app->request->post('itemId'));
            $model->item_description = Yii::$app->request->post('itemDesc');
            $model->amount = Yii::$app->request->post('itemPrice');

            if ($model->update()) {
                return json_encode(["success" => true, "msg" => "Item added", "itemDesc" => Html::encode($model->item_description), "itemPrice" => MyFormatter::asDecimal2($model->amount), "itemId" => $model->id]);
            }
        }
        return json_encode(["success" => false, "msg" => "Fail to update"]);
    }

    public function actionRemovePanelItemTemplateAjax() {
        if (Yii::$app->request->isPost) {
            $model = ProjectQPanelItemsTemplate::findOne(Yii::$app->request->post('itemId'));
            if ($model->delete()) {
                return json_encode(["success" => true, "msg" => "Item removed"]);
            }
        }
        return json_encode(["success" => false, "msg" => "Fail to remove"]);
    }

    public function actionSortPanelItemTemplateAjax() {
        $panelId = Yii::$app->request->post('panelId');
        $moveId = substr_replace(Yii::$app->request->post('moveId'), "", 0, 3);
        $previousId = substr_replace(Yii::$app->request->post('previousId'), "", 0, 3);
        $itemList = ProjectQPanelItemsTemplate::find()->where(['panel_template_id' => $panelId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortItems($moveId, $previousId, $itemList);
        return json_encode(['success' => $result]);
    }

    public function actionCalculatePanelTemplateAmount() {
        $panelId = Yii::$app->request->post('panelId');
        $model = ProjectQPanelsTemplate::findOne($panelId);
        if ($model->updatePanelTemplateAmount()) {
            $revision = $model->revisionTemplate;
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

    public function actionUpdatePanelTemplateRemark() {
        $req = Yii::$app->request;

        $id = $req->post('ProjectQPanelsTemplate')['id'];
        $model = ProjectQPanelsTemplate::findOne($id);

        if ($model->load($req->post()) && $model->update()) {
            FlashHandler::success("Updated");
        }

        return $this->redirect(['update-p-q-template-panel-item', 'id' => $id]);
    }
}
