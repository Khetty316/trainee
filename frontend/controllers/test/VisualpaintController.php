<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormVisualpaint;
use frontend\models\test\TestFormVisualpaintSearch;
use frontend\models\test\RefTestStatus;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\test\TestTemplate;
use common\models\myTools\FlashHandler;
use frontend\models\test\TestMaster;
use frontend\models\test\TestItemWitness;

/**
 * VisualpaintController implements the CRUD actions for TestFormVisualpaint model.
 */
class VisualpaintController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/visualpaint');
    }

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    /**
     * Lists all TestFormVisualpaint models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormVisualpaint::findOne($id);
        $master = $model->testMaster;
        $template = true;

        $procedures = [];
        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_VISUALPAINT, 'active_sts' => 1])->one();
            if (!empty($testTemplate)) {
                $procedures = $model->customProcedures($testTemplate, false, false);
            }
            $template = false;
        } else {
            $procedures = $model->customProcedures($model, true, false);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $master->checkMasterStatus($master->id);
            FlashHandler::success('Saved');
            if (in_array($model->status, [RefTestStatus::STS_COMPLETE, RefTestStatus::STS_FAIL])) {
                if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                    $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                    if ($attendances) {
                        foreach ($attendances as $attendance) {
                            $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_VISUALPAINT, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                            if (!$witness->isNewRecord) {
                                continue;
                            }
                            $witness->test_master_id = $master->id;
                            $witness->form_type = TestMaster::CODE_VISUALPAINT;
                            $witness->name = $attendance->name;
                            $witness->org = $attendance->org;
                            $witness->designation = $attendance->designation;
                            $witness->role = $attendance->role;
                            if (!$witness->save()) {
                                \common\classes\myTools\Mydebug::dumpFileW($witness->getErrors());
                            }
                        }
                    }
                }
            }
        }

        if ($model->hasErrors()) {
            $model->status = $model->oldAttributes['status'];
            FlashHandler::err('Error. Please check fields.');
        }

        return $this->render('index', [
                    'model' => $model,
                    'master' => $master,
                    'procedures' => $procedures,
                    'template' => $template,
                    'witnessList' => TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_VISUALPAINT)
        ]);
    }

    public function actionSaveWitness($id) {
        $form = TestFormVisualpaint::findOne($id);
        $req = Yii::$app->request;
        if ($req->isPost) {
            $postWitnesses = $req->post('testItemWitness');
            foreach ($postWitnesses as $postWitness) {
                if ($postWitness['witnessId']) {
                    $witness = TestItemWitness::find()->where(['id' => $postWitness['witnessId']])->one();
                    $witness->signature = $postWitness['witnessSign'];
                    $witness->update(false);
                }
            }
            \common\models\myTools\FlashHandler::success("Saved.");
        }
        $form->testMaster->checkMasterStatus($form->test_master_id);
//        return $this->redirect(['/test/testing/index-master-detail', 'id' => $form->test_master_id]);
        return $this->redirect(['index', 'id' => $id]);
    }

    public function actionAjaxEditThreshold($id) {
        $model = TestFormVisualpaint::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post('TestFormVisualpaint');
            $model->treshold_a = $postData['treshold_a'] !== "" ? $postData['treshold_a'] : TestFormVisualpaint::THRESHOLD_A;

            if ($model->save()) {
                FlashHandler::success("Updated");
            } else {
                FlashHandler::err("Unable to edit");
            }
            return $this->redirect(['index', 'id' => $id]);
        }

        return $this->renderAjax('__ajaxFormEditThreshold', [
                    'model' => $model,
                    'title' => 'Update Threshold',
                    'btnText' => 'Update'
        ]);
    }

    public function actionRevertForm($id) {
        $model = TestFormVisualpaint::findOne($id);
        $master = $model->testMaster;

        if ($model) {
            if ($model->status === RefTestStatus::STS_READY_FOR_TESTING) {
                $model->status = RefTestStatus::STS_SETUP;
            } elseif ($model->status === RefTestStatus::STS_FAIL || $model->status === RefTestStatus::STS_COMPLETE) {
                $model->status = RefTestStatus::STS_IN_TESTING;
            }

            $model->save();
            $master->checkMasterStatus($master->id);
            FlashHandler::success('Form reverted successfully');
        } else {
            FlashHandler::success('Failed');
        }

        return $this->redirect(['index', 'id' => $model->id]);
    }

    public function actionDeleteForm($id) {
        $model = TestFormVisualpaint::findOne($id);
        $master = $model->testMaster;

        if ($model->delete()) {
            FlashHandler::success('Visual Inspection and Painting Work Check form deleted');
        } else {
            FlashHandler::err('Error processing request');
        }

        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

    public function actionEditProcedure($id) {
        $model = TestFormVisualpaint::findOne($id);
        $template = true;
        $testTemplate = new TestTemplate();

        $procedures = [];
        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_VISUALPAINT, 'active_sts' => 1])->one();
            if (!empty($testTemplate)) {
                $procedures = $testTemplate;
            }
//            else {
//                $testTemplate = new TestTemplate();
//            }
            $template = false;
        } else {
//            $testTemplate = new TestTemplate();
            $procedures = $model->template;
        }

        if (Yii::$app->request->post()) {
            $proctest1 = Yii::$app->request->post('TestTemplate')['proctest1'];
            $proctest2 = Yii::$app->request->post('TestTemplate')['proctest2'];
            $proctest3 = Yii::$app->request->post('TestTemplate')['proctest3'];

            $model = TestFormVisualpaint::findOne($id);
            if (empty(trim(strip_tags($proctest1))) && empty(trim(strip_tags($proctest2))) && empty(trim(strip_tags($proctest3)))) {
                $model->template = null;
            } else {
                $templateData = [
                    'proctest1' => $proctest1,
                    'proctest2' => $proctest2,
                    'proctest3' => $proctest3,
                ];
                $htmlContent = implode('|', $templateData);
                $newhtml = $testTemplate->cleanHtmlContent($htmlContent);
                $model->template = preg_replace('/<(\w+)(\s*[^>]*)><\/\1>/', '', $newhtml);
            }

            if ($model->save()) {
                FlashHandler::success("Saved");
                return $this->redirect(['index', 'id' => $id]);
            }
        }

        return $this->renderAjax('editProcedure', [
                    'procedures' => $procedures,
                    'template' => $template,
                    'testTemplate' => $testTemplate
        ]);
    }

    public function actionUploadImage() {
        $testTemplate = new TestTemplate();
        $response = $testTemplate->uploadImage();
        return $response;
    }

}
