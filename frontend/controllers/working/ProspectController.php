<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\project\ProspectMaster;
use frontend\models\working\project\ProspectMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\working\project\ProspectMasterScope;
use frontend\models\working\project\ProspectDetail;
use frontend\models\working\project\ProspectDetailRevisionScope;
use frontend\models\working\project\ProspectDetailRevision;

/**
 * ProspectController implements the CRUD actions for ProspectMaster model.
 */
class ProspectController extends Controller {

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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all ProspectMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ProspectMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Dropdown List For Filters:
        $projectTypeList = \frontend\models\common\RefProjectType::getDropDownList();

        return $this->render('indexProspectMaster', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'projectTypeList' => $projectTypeList,
        ]);
    }

    /**
     * Displays a single ProspectMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {

//        $prospectDetails = \frontend\models\working\project\ProspectDetail::find()->where("prospect_master=$id")->all();

        return $this->render('viewProspectMaster', [
                    'model' => $this->findModel($id),
//                    'prospectDetails' => $prospectDetails,
        ]);
    }

    /**
     * Creates a new ProspectMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ProspectMaster();

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstances($model, 'scannedFile');
            $model->processAndSave();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('createProspect', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProspectMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstances($model, 'scannedFile');
            $model->processAndSave();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProspectMaster model.
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
     * Finds the ProspectMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProspectMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ProspectMaster::findOne($id)) !== null) {

            $model->getFilesFromFolder();

            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFileProspect($projCode, $filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . '/' . $projCode . '/' . Yii::$app->params['tender_doc_folder'] . '/' . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    public function actionDeleteFileProspect() {
        $projCode = Yii::$app->request->post('projCode');
        $filename = htmlspecialchars_decode(Yii::$app->request->post('filename'));
        if (Yii::$app->request->isPost) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . '/' . $projCode . '/' . Yii::$app->params['tender_doc_folder'] . '/' . $filename;
            \yii\helpers\FileHelper::unlink($filePath);
            return json_encode(array("msg" => "File removed!!"));
        } else {
            return json_encode(array("msg" => "Fail to remove.."));
        }
    }

    //******************************** FUNCTIONS FOR PROSPECT SCOPE ********************************************
    public function actionGetScopeListAjax($master_prospect) {
        $scopes = \frontend\models\working\project\ProspectMasterScope::find()->where("master_prospect = $master_prospect")->all();
        return $this->renderAjax('_viewProspectScope', [
                    'scopes' => $scopes
        ]);
    }

    public function actionCreateScopeAjax() {
        $model = new ProspectMasterScope();
        $id = Yii::$app->request->get('id');
        $id2 = Yii::$app->request->post('id2');

        if ($id) {
            $model = $model::findOne($id);
        } else if ($id2) {
            $model = $model::findOne($id2);
        } else {
            $model->master_prospect = Yii::$app->request->get('master_prospect');
        }

        if ($model->load(Yii::$app->request->post())) {// && $model->save()) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            if ($model->processAndSave()) {
                return json_encode(['data' => ['success' => true, 'test' => 'testingonly']]);
            }
        }


        $scopeList = ProspectMasterScope::getDistinctScope();
        return $this->renderAjax('_createProspectScope', [
                    'model' => $model,
                    'scopeList' => $scopeList
        ]);
    }

    public function actionDeleteScopeAjax() {
        $id = Yii::$app->request->post('id');


        if (ProspectMasterScope::deleteAll(['id' => $id])) {
            return json_encode(['data' => ['success' => true, 'test' => 'testingonly']]);
        }
    }

    public function actionGetFileScope($projCode, $filename) {
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_file_path'] . '/' . $projCode . '/scopeMaster/' . $filename;
        return Yii::$app->response->sendFile($completePath, $filename, ['inline' => true]);
    }

    // ----------------------------- Function for Prospect Scope -----------------------------
    // 
    // 
    // 
    //******************************** FUNCTIONS FOR PROSPECT CLIENT ********************************************
    public function actionGetClientListAjax($master_prospect, $showDetail) {
        $prospectDetails = \frontend\models\working\project\ProspectDetail::find()->where("prospect_master = $master_prospect")->all();
        $awardSts = (new \yii\db\Query())
                ->select(['awarded_sts'])
                ->from('prospect_detail AS a')
                ->join('INNER JOIN', 'prospect_detail_revision AS b', 'b.prospect_detail_id = a.id')
                ->where('b.awarded_sts=1 AND a.prospect_master=' . $master_prospect)
                ->one();
        return $this->renderAjax('_viewProspectClient', [
                    'prospectDetails' => $prospectDetails,
                    'showDetail' => $showDetail,
                    'awardSts' => ($awardSts ? true : false)
        ]);
    }

    public function actionCreateClientAjax($id = "") {
        $model = new ProspectDetail();
        if ($id) {
            $model = $model::findOne($id);
        } else if (Yii::$app->request->post("id2")) {
            $model = $model::findOne(Yii::$app->request->post("id2"));
        } else {
            $model->prospect_master = Yii::$app->request->get('prospect_master');  // Create New
        }

        if ($model->load(Yii::$app->request->post())) { // && $model->save()) {
            if ($model->save()) {
                return json_encode(['data' => ['success' => true]]);
            }
        }

        $serviceList = ProspectDetail::getDistinctService();
        $clientList = \frontend\models\working\contact\ContactMaster::getClientList();
        return $this->renderAjax('_createProspectClient', [
                    'model' => $model,
                    'serviceList' => $serviceList,
                    'clientList' => $clientList
        ]);
    }

    public function actionDeleteClientAjax() {
        $id = Yii::$app->request->post('id');
        if (ProspectDetail::deleteAll(['id' => $id])) {
            return json_encode(['data' => ['success' => true]]);
        }
    }

    // ----------------------------- Function for Prospect Client -----------------------------
    // 
    // 
    // 
    //******************************** FUNCTIONS FOR PROSPECT CLIENT - SCOPES ************************************
    public function actionCreateClientRevisionAjax($prospectDetailId = "", $masterProspectId = "") {
        $clientScope = new ProspectDetailRevisionScope();
        $revision = new ProspectDetailRevision();

        if (Yii::$app->request->post()) {
            if ($revision->processAndSave(Yii::$app->request->post())) {
                return json_encode(['data' => ['success' => true]]);
            }
        }


        $revision->prospect_detail_id = $prospectDetailId;


        $masterScope = ProspectMasterScope::find()->where("master_prospect = " . $masterProspectId)->all();
//        $serviceList = ProspectDetail::getDistinctService();
//        $clientList = \frontend\models\working\contact\ContactMaster::getClientList();
        return $this->renderAjax('_createProspectClientRevision', [
                    'clientScope' => $clientScope,
                    'revision' => $revision,
                    'masterScope' => $masterScope
        ]);
    }

    public function actionCopyClientRevisionAjax($prospectDetailId = "", $masterProspectId = "") {
        if (Yii::$app->request->post()) {
            $revision = new ProspectDetailRevision();
            if ($revision->processCopyAndSave(Yii::$app->request->post())) {
                return json_encode(['data' => ['success' => true]]);
            }
        }
        $prospectDetail = ProspectDetail::find()->where("prospect_master = " . $masterProspectId)->all();
        return $this->renderAjax('_copyProspectClientRevision', [
                    'prospectDetailId' => $prospectDetailId,
                    'prospectDetail' => $prospectDetail
        ]);
    }

    public function actionAwardClientRevisionAjax() {

        if (!Yii::$app->request->isPost) {
            return ['data' => ['success' => false, 'message' => 'Fail']];
        }

        $model = ProspectDetailRevision::findOne(Yii::$app->request->post("revisionId"));
        if ($model->setAward()) {
            return json_encode(['data' => ['success' => true]]);
        } else {
            return json_encode(['data' => ['success' => false, 'message' => 'Fail to Award']]);
        }
    }

}
