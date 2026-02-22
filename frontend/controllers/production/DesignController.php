<?php

namespace frontend\controllers\production;

use Yii;
use yii\filters\VerbFilter;
use frontend\models\ProjectProduction\ProjectProductionMasterSearch;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\ProjectProduction\ProjectProductionPanelDesignMaster;
use common\models\myTools\MyCommonFunction;
use frontend\models\ProjectProduction\ProjectProductionPanelDesign;
use frontend\models\ProjectProduction\ProjectProductionPanelDesignForm;
use frontend\models\ProjectProduction\ProjectProductionPanels;

class DesignController extends \yii\web\Controller {

    CONST mainViewPath = "/projectproduction/design/";

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

    public function actionIndexAwaitingDesign() {
        $searchModel = new ProjectProductionMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this::mainViewPath . 'indexAwaitingDesign', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewAwaitingDesign($id) {
        $design = new ProjectProductionPanelDesignForm();
        $model = ProjectProductionMaster::findOne($id);
        if ($design->load(Yii::$app->request->post())) {
            $designList = [];
            // Save File into server
            $design->scannedFile = \yii\web\UploadedFile::getInstances($design, 'scannedFile');
            if ($design->validate() && $design->scannedFile) {
                $filePath = Yii::$app->params['project_design_file_path'] . $model->project_production_code . '/';
                MyCommonFunction::mkDirIfNull($filePath);
                foreach ($design->scannedFile as $file) {
                    $file->saveAs($filePath . $file->name);
                    $designMaster = new ProjectProductionPanelDesignMaster();
                    $designMaster->sub_folder_name = $model->project_production_code;
                    $designMaster->filename = $file->name;
                    $designMaster->remarks = $design->remarks;
                    $designMaster->scannedFile = '';
                    $designMaster->save();
                    $designList[] = $designMaster;
                }
            }
            $panelIds = Yii::$app->request->post('selectedPanelIds');
            foreach ($designList as $design) {

                foreach ($panelIds as $panelId) {
                    $fileLink = new ProjectProductionPanelDesign();
                    $fileLink->design_master_id = $design->id;
                    $fileLink->proj_prod_panel_id = $panelId;
                    $fileLink->save();
                }
            }
            return $this->redirect(['view-awaiting-design', 'id' => $id]);
        }

        return $this->render($this::mainViewPath . 'viewAwaitingDesign', [
                    'model' => $model,
                    'design' => $design
        ]);
    }

    public function actionFinalizePanels($id) {
        $design = new ProjectProductionPanelDesignForm();
        if ($design->load(Yii::$app->request->post())) {
            $panelIds = Yii::$app->request->post('selectedPanelIds');
            foreach ($panelIds as $panelId) {
                $panel = ProjectProductionPanels::findOne($panelId);
                $panel->finalizeDesign();
            }
        }

        return $this->redirect(['view-awaiting-design', 'id' => $id]);
    }

    public function actionGetFileById($id) {
        $designMaster = ProjectProductionPanelDesignMaster::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['project_design_file_path'] . $designMaster->sub_folder_name . "/" . $designMaster->filename;
        return Yii::$app->response->sendFile($completePath, $designMaster->filename, ['inline' => true]);
    }

    /**
     * "Disconnect" design from panels
     * Using AJAX
     * @param type $id
     */
    public function actionAjaxDeleteDesign() {

        $id = Yii::$app->request->post('id');
        if ($id) {
            $design = ProjectProductionPanelDesign::findOne($id);
            if ($design->delete()) {
                return \yii\helpers\Json::encode(['success' => true]);
            } else {
                return \yii\helpers\Json::encode(['success' => false]);
            }
        }
        return \yii\helpers\Json::encode(['success' => false]);
    }

}
