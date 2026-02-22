<?php

namespace frontend\controllers\test;

use Yii;
use frontend\models\test\TestTemplate;
use frontend\models\test\TestTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\helpers\Json;
use \frontend\models\test\TestMain;
use \frontend\models\test\TestMaster;
use frontend\models\test\RefTestFormList;

/**
 * TestTemplateController implements the CRUD actions for TestTemplate model.
 */
class TestTemplateController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Path for main view for controller
     * @return string
     */
    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test-template/');
    }

    public function actionIndex() {
        $searchModel = new TestTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('viewTestTemplate', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new TestTemplate();
        $formName = \frontend\models\test\RefTestFormList::getDropDownList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('createTestTemplate', [
                    'model' => $model,
                    'formName' => $formName
        ]);
    }

    public function actionUploadImage() {
        $model = new TestTemplate();
        $response = $model->uploadImage();
        return $response;
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $array = RefTestFormList::getDropDownList();
        $mergeArray = array_merge($array, [TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE, TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE]);

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post('TestTemplate', []);

            if (empty(trim(strip_tags($postData['proctest1'])))) {
                $model->proctest1 = null;
            }
            if (empty(trim(strip_tags($postData['proctest2'])))) {
                $model->proctest2 = null;
            }

            if (empty(trim(strip_tags($postData['proctest3'])))) {
                $model->proctest3 = null;
            }

            if ($model->save()) {
                \common\models\myTools\FlashHandler::success('Saved');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                \common\models\myTools\FlashHandler::err('Save failed');
            }
        }

        return $this->render('updateTestTemplate', [
                    'model' => $model,
                    'formName' => $mergeArray
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = TestTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
