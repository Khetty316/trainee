<?php

namespace frontend\controllers\working;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\auth\models\AuthItem;
use frontend\models\working\hrdoc\HrPublicDocumentsRead;
use common\models\myTools\FlashHandler;

/**
 * HrPublicDocumentController implements the CRUD actions for HrPublicDocument model.
 */
class HrPublicDocumentController extends Controller {

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
     * Lists all DocumentReminderMaster models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \frontend\models\working\hrdoc\HrPublicDocumentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexPublic', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentReminderMaster model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('viewPublic', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DocumentReminderMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate() {
//        $model = new \frontend\models\working\hrdoc\HrPublicDocuments();
//        if ($model->load(Yii::$app->request->post())) {
//            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
//            $model->file_date = \common\models\myTools\MyFormatter::changeDateFormat_readToDB($model->file_date);
//            if ($model->processAndSave()) {
//                return $this->redirect(['view', 'id' => $model->id]);
//            }
//        }
//
//        return $this->render('createPublic', [
//                    'model' => $model,
//        ]);
//    }

    public function actionCreate() {
        $model = new \frontend\models\working\hrdoc\HrPublicDocuments();
        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->file_date = \common\models\myTools\MyFormatter::changeDateFormat_readToDB($model->file_date);
            if ($model->processAndSave()) {
                $activeEmployeeIds = \common\models\User::find()
                        ->select('id')
                        ->where(['status' => \common\models\User::STATUS_ACTIVE])
                        ->column();

                foreach ($activeEmployeeIds as $employeeId) {
                    if (!HrPublicDocumentsRead::find()
                                    ->where(['employee_id' => $employeeId, 'hr_public_doc_id' => $model->id])
                                    ->exists()) 
                    {
                        $readModel = new HrPublicDocumentsRead();
                        $readModel->employee_id = $employeeId;
                        $readModel->hr_public_doc_id = $model->id;
                        $readModel->is_read = 0;
                        $readModel->save(false);
                    }
                }

                FlashHandler::success("Document and read tracking records saved successfully!");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('createPublic', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing DocumentReminderMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->scannedFile = \yii\web\UploadedFile::getInstance($model, 'scannedFile');
            $model->file_date = \common\models\myTools\MyFormatter::changeDateFormat_readToDB($model->file_date);
            if ($model->processAndSave()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('updatePublic', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentReminderMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
      License            $model->file_date = \common\models\myTools\MyFormatter::changeDateFormat_readToDB($model->file_data);
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        if ($this->findModel($id)->delete()) {
            \common\models\myTools\FlashHandler::success('Document deleted.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the DocumentReminderMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentReminderMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = \frontend\models\working\hrdoc\HrPublicDocuments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($id) {
        $doc = \frontend\models\working\hrdoc\HrPublicDocuments::findOne($id);
        $completePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['publicdocument_file_path'] . $doc->filename;
        return Yii::$app->response->sendFile($completePath, substr($doc->filename, 15), ['inline' => true]);
    }
}
