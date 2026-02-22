<?php

namespace frontend\controllers;

use Yii;
use frontend\models\resume\ResumeAcademicQualifications;
use frontend\models\resume\ResumeAcademicQualificationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\resume\ResumeEmployHistorySearch;
use frontend\models\resume\ResumeEmployHistory;
use common\models\myTools\FlashHandler;
use frontend\models\working\project\ProjectMaster;
use frontend\models\resume\ResumeProjectRef;
use frontend\models\resume\ResumeProjectRefSearch;

/**
 * ResumeController implements the CRUD actions for ResumeAcademicQualifications model.
 */
class ResumeController extends Controller {

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
        ];
    }

    public function actionIndex() {
        $searchModel = new ResumeAcademicQualificationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new ResumeAcademicQualifications();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = ResumeAcademicQualifications::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionIndexPersonal() {
        $userId = Yii::$app->user->id;

        $projectList = ProjectMaster::find()->where(['or', ['proj_director' => $userId], ['proj_manager' => $userId], ['site_manager' => $userId], ['proj_coordinator' => $userId],
                    ['project_engineer' => $userId], ['site_engineer' => $userId], ['site_supervisor' => $userId], ['project_qs' => $userId]])->all();

        return $this->render('indexPersonalResume', [
                    'projectList' => $projectList
        ]);
    }

    /*
     * ************************************ Academic Record Below this ******************************************************
     */

    public function actionCreateAcademicQualification() {
        $model = new ResumeAcademicQualifications();
        if ($model->load(Yii::$app->request->post())) {

            if ($model->processAndSave()) {
                \common\models\myTools\FlashHandler::success("Saved");
                return $this->redirect(['index-personal']);
            } else {
                \common\models\myTools\Mydebug::dumpFileA($model->errors);
            }
        }

        return $this->render('createResumeAcademicQualifications', [
                    'model' => $model,
        ]);
    }

    public function actionPersonalAcademicAjax() {

        $academicList = ResumeAcademicQualifications::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['sort' => SORT_ASC])->all();
        return $this->renderPartial('_indexPersonalAcademic', [
                    'academicList' => $academicList,
        ]);
    }

    public function actionUpdateAcademicQualification($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
            \common\models\myTools\FlashHandler::success("Updated");
            return $this->redirect(['index-personal']);
        }

        return $this->render('updateResumeAcademicQualifications', [
                    'model' => $model,
        ]);
    }

    public function actionSortAcademicQualificationAjax($action, $userId, $idxNo) {
        $academicList = ResumeAcademicQualifications::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortItems($action, $idxNo, $academicList);
        return json_encode(['data' => ['success' => $result]]);
    }

    public function actionDeleteAcademicQualification($id) {
        $resumeAcademic = ResumeAcademicQualifications::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id])->one();
        if ($resumeAcademic->delete()) {
            FlashHandler::success("Removed!");
        } else {
            FlashHandler::err_getHelp();
        }
        return $this->redirect(['index-personal']);
    }

    /*
     * ************************************ Employment History Below this ******************************************************
     */

    public function actionCreateEmploymentHistory() {
        $model = new ResumeEmployHistory();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                \common\models\myTools\FlashHandler::success("Saved");
                return $this->redirect(['index-personal']);
            } else {
                FlashHandler::err_getHelp();
            }
        }

        return $this->render('createResumeEmployHistory', [
                    'model' => $model,
        ]);
    }

    public function actionPersonalEmploymentHistoryAjax() {
        $employList = ResumeEmployHistory::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['sort' => SORT_ASC])->all();
        return $this->renderPartial('_indexPersonalEmployHistory', [
                    'employList' => $employList,
        ]);
    }

    public function actionUpdateEmploymentHistory($id) {
        $model = ResumeEmployHistory::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \common\models\myTools\FlashHandler::success("Updated");
            return $this->redirect(['index-personal']);
        }

        return $this->render('updateResumeEmployHistory', [
                    'model' => $model,
        ]);
    }

    public function actionSortEmploymentHistoryAjax($action, $userId, $idxNo) {
        $employList = ResumeEmployHistory::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortItems($action, $idxNo, $employList);
        return json_encode(['data' => ['success' => $result]]);
    }

    public function actionDeleteEmploymentHistory($id) {
        $resumeEmploy = ResumeEmployHistory::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id])->one();
        if ($resumeEmploy->delete()) {
            FlashHandler::success("Removed!");
        } else {
            FlashHandler::err_getHelp();
        }
        return $this->redirect(['index-personal']);
    }

    /*
     * ************************************ Project Reference Below this ******************************************************
     */

    public function actionCreateProjectRef() {
        $model = new ResumeProjectRef();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->processAndSave()) {
                \common\models\myTools\FlashHandler::success("Saved");
                return $this->redirect(['index-personal']);
            } else {
                FlashHandler::err_getHelp();
            }
        }

        return $this->render('createProjectRef', [
                    'model' => $model,
        ]);
    }

    public function actionPersonalProjectRefAjax() {
        $projectList = ResumeProjectRef::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['sort' => SORT_ASC])->all();
        return $this->renderPartial('_indexPersonalProjectRef', [
                    'projectList' => $projectList,
        ]);
    }

    public function actionUpdateProjectRef($id) {
        $model = ResumeProjectRef::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \common\models\myTools\FlashHandler::success("Updated");
            return $this->redirect(['index-personal']);
        }

        return $this->render('updateResumeProjectRef', [
                    'model' => $model,
        ]);
    }

    public function actionSortProjectRefAjax($action, $userId, $idxNo) {
        $projectList = ResumeProjectRef::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $result = $this->sortItems($action, $idxNo, $projectList);
        return json_encode(['data' => ['success' => $result]]);
    }

    public function actionDeleteProjectRef($id) {
        $resumeProjRef = ResumeProjectRef::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id])->one();
        if ($resumeProjRef->delete()) {
            FlashHandler::success("Removed!");
        } else {
            FlashHandler::err_getHelp();
        }
        return $this->redirect(['index-personal']);
    }

    /*
     * ************************************************************ Common Functions ******************************************
     */

    private function sortItems($action, $idxNo, $list) {
        if ($action == "up" && $idxNo > 0) {
            $tempObject = $list[$idxNo];
            $list[$idxNo] = $list[$idxNo - 1];
            $list[$idxNo - 1] = $tempObject;
        } else if ($action == "down" && $idxNo < sizeof($list) - 1) {
            $tempObject = $list[$idxNo];
            $list[$idxNo] = $list[$idxNo + 1];
            $list[$idxNo + 1] = $tempObject;
        }
        foreach ($list as $key => $employ) {
            $employ->updateSort(($key + 1));
        }

        return true;
    }

    public function actionGeneratePersonalResume($type) {
        return $this->actionGenerateResume(Yii::$app->user->id, $type);
    }

    public function actionGenerateResume($userId, $type) {

        $user = \common\models\User::findOne($userId);
        $academicList = ResumeAcademicQualifications::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $employList = ResumeEmployHistory::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $projectList = ResumeProjectRef::find()->where(['user_id' => $userId])->orderBy(['sort' => SORT_ASC])->all();
        $nplProjectList = ProjectMaster::find()->where(['or', ['proj_director' => $userId], ['proj_manager' => $userId], ['site_manager' => $userId], ['proj_coordinator' => $userId],
                    ['project_engineer' => $userId], ['site_engineer' => $userId], ['site_supervisor' => $userId], ['project_qs' => $userId]])->orderBy(['proj_code' => SORT_ASC])->all();

        if ($type == "docx") {
            return $this->generateDoc($user, $academicList, $employList, $projectList, $nplProjectList);
        } else {
            $mpdf = $this->generatePdf($user, $academicList, $employList, $projectList, $nplProjectList);
            return $mpdf->Output("Payslip_hr" . '.pdf', "I");
        }
    }

    private function generatePdf($user, $academicList, $employList, $projectList, $nplProjectList) {

        $htmlBody = $this->renderPartial("resume_output_template", [
            'user' => $user,
            'academicList' => $academicList,
            'employList' => $employList,
            'projectList' => $projectList,
            'nplProjectList' => $nplProjectList,
            'fileType' => 'pdf'
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => "utf-8",
            'default_font_size' => 11,
            'default_font' => 'Calibri',
//            'setAutoTopMargin' => "stretch",
//            'setAutoBottomMargin' => "stretch",
        ]);
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->WriteHTML($htmlBody);
        return $mpdf;
    }

    public function generateDoc($user, $academicList, $employList, $projectList, $nplProjectList) {
        $htmlBody = $this->renderPartial("resume_output_template", [
            'user' => $user,
            'academicList' => $academicList,
            'employList' => $employList,
            'projectList' => $projectList,
            'nplProjectList' => $nplProjectList,
            'fileType' => 'doc'
        ]);

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/vnd.ms-word; charset=utf-8');
        $headers->add('Expires', '0');
        $headers->add('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $headers->add('content-disposition', 'attachment;filename=Resume - ' . $user->fullname . '.doc');

        return $htmlBody;
    }

}
