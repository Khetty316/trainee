<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormInsuhipot;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\test\TestTemplate;
use common\models\myTools\FlashHandler;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestItemWitness;

class InsuhipotController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/insuhipot');
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
     * Lists all TestFormInsuhipot models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormInsuhipot::findOne($id);
        $master = $model->testMaster;
        $template = true;

        $procedures = [];
        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_INSUHIPOT, 'active_sts' => 1])->one();
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
                $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                if ($attendances) {
                    foreach ($attendances as $attendance) {
                        $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_INSUHIPOT, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                        if (!$witness->isNewRecord) {
                            continue;
                        }
                        $witness->test_master_id = $master->id;
                        $witness->form_type = TestMaster::CODE_INSUHIPOT;
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

        if ($model->hasErrors()) {
            $model->status = $model->oldAttributes['status'];
            FlashHandler::err('Error. Please check fields.');
        }

        return $this->render('index', [
                    'model' => $model,
                    'master' => $master,
                    'procedures' => $procedures,
                    'template' => $template,
                    'witnessList' => TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_INSUHIPOT)
        ]);
    }

    public function actionSaveWitness($id) {
        $form = TestFormInsuhipot::findOne($id);
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
//        return $this->redirect(['/test/testing/index-master-detail', 'id' => $form->test_master_id]);
        return $this->redirect(['index', 'id' => $id]);
    }

    public function actionAjaxEditThreshold($id, $type) {
        $model = TestFormInsuhipot::findOne($id);
        if ($type === 'treshold_a') {
            $defaultValue = TestFormInsuhipot::THRESHOLD_A;
            $unit = TestFormInsuhipot::THRESHOLD_A_UNIT;
        } elseif ($type === 'treshold_b') {
            $defaultValue = TestFormInsuhipot::THRESHOLD_B;
            $unit = TestFormInsuhipot::THRESHOLD_B_UNIT;
        }

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post('TestFormInsuhipot');
            if ($type === 'treshold_a') {
                $model->treshold_a = $postData['treshold_a'] !== "" ? $postData['treshold_a'] : TestFormInsuhipot::THRESHOLD_A;
            } elseif ($type === 'treshold_b') {
                $model->treshold_b = $postData['treshold_b'] !== "" ? $postData['treshold_b'] : TestFormInsuhipot::THRESHOLD_B;
            }

            if ($model->save()) {
                FlashHandler::success("Updated");
            } else {
                FlashHandler::err("Unable to edit");
            }
            return $this->redirect(['index', 'id' => $id]);
        }

        return $this->renderAjax('__ajaxFormEditThreshold', [
                    'model' => $model,
                    'type' => $type,
                    'defaultValue' => $defaultValue,
                    'unit' => $unit,
                    'title' => 'Update Threshold',
                    'btnText' => 'Update'
        ]);
    }

    public function actionRevertForm($id) {
        $model = TestFormInsuhipot::findOne($id);
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

    public function actionMarkAttributeResult() {
        $post = Yii::$app->request->post();

        $model = TestFormInsuhipot::findOne($post['modelid']);
        $attribute = $post['id'];
        $value = $post['value'];

        $model->$attribute = $value;

        if ($model->update(false)) {
            return true;
        } else {
            return false;
        }
    }

    public function actionInsuhipotStatus($id, $sts) {
        $model = TestFormInsuhipot::findOne($id);
        $master = $model->testMaster;

        $model->status = $sts;

        if ($model->update(false)) {

            if ($model->status == \frontend\models\test\RefTestStatus::STS_IN_TESTING) {
                \common\models\myTools\FlashHandler::success('Insulation and Hipot Test Start');
                return $this->redirect(['index', 'id' => $id]);
            } else {
                \common\models\myTools\FlashHandler::success('Insulation and Hipot Status Updated');
                return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
            }
        } else {
            \common\models\myTools\FlashHandler::err('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionDeleteForm($id) {
        $model = TestFormInsuhipot::findOne($id);
        $master = $model->testMaster;

        if ($model->delete()) {
            FlashHandler::success('Insulation and Hipot test form deleted.');
        } else {
            FlashHandler::err('Error processing request.');
        }

        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

    public function actionEditProcedure($id) {
        $model = TestFormInsuhipot::findOne($id);
        $template = true;
        $testTemplate = new TestTemplate();

        $procedures = [];
        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_INSUHIPOT, 'active_sts' => 1])->one();
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

            $model = TestFormInsuhipot::findOne($id);
            if (empty(trim(strip_tags($proctest1))) && empty(trim(strip_tags($proctest2))) && empty(trim(strip_tags($proctest3)))) {
                $model->template = null;
            } else {
                $templateData = [
                    'proctest1' => $proctest1,
                    'proctest2' => $proctest2,
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
