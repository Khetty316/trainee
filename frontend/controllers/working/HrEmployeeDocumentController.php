<?php

namespace frontend\controllers\working;

use Yii;
use frontend\models\working\hrdoc\HrEmployeeDocuments;
use frontend\models\working\hrdoc\HrEmployeeDocumentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\auth\models\AuthItem;

/**
 * HrEmployeeDocumentController implements the CRUD actions for HrEmployeeDocuments model.
 */
class HrEmployeeDocumentController extends Controller {

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
                        'roles' => [AuthItem::ROLE_SystemAdmin, AuthItem::ROLE_HR_Senior],
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
     * Path for main view for controller
     * @return string
     */
    public function getViewPath() {
        return Yii::getAlias('@frontend/views/working/hrdoc/');
    }

    /**
     * Lists all HrEmployeeDocuments models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new HrEmployeeDocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexEmployee', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HrEmployeeDocuments model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('viewEmployee', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HrEmployeeDocuments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new HrEmployeeDocuments();

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstances($model, 'scannedFile');
            if ($model->processAndSave()) {
                \common\models\myTools\FlashHandler::success("Document added!");
                return $this->redirect(['index']);
            }
        }

        return $this->render('createEmployee', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing HrEmployeeDocuments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('updateEmployee', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HrEmployeeDocuments model.
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
     * Finds the HrEmployeeDocuments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HrEmployeeDocuments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = HrEmployeeDocuments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Opens the dialog in browser to open/save the image.
     */
    public function actionGetDbDocument($docId) {
        $model = HrEmployeeDocuments::findOne($docId);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-length: ' . $model->file_size);
        header('Content-Type: ' . $model->file_type);
        header('Content-Disposition: attachment; filename=' . $model->filename);
        echo $model->file_blob;
    }

    public function actionInactivate($id) {
        $model = $this->findModel($id);
        $model->active_sts = 0;
        $model->update();
        return $this->redirect(['index']);
    }

    public function actionGetFile($id) {
        $doc = HrEmployeeDocuments::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['personaldocument_file_path'] . "$doc->employee_id/" . $doc->filename;
        return Yii::$app->response->sendFile($completePath, substr($doc->filename, 15), ['inline' => true]);
    }

}
