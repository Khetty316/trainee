<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormDimension;
use frontend\models\test\TestFormDimensionSearch;
use frontend\models\test\TestDetailDimension;
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
 * DimensionController implements the CRUD actions for TestFormDimension model.
 */
class DimensionController extends Controller {

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
     * Lists all TestFormDimension models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormDimension::findOne($id);
        $master = $model->testMaster;
        $dimensionList = $model->testDetailDimensions;
        $panel = $master->testMain->panel;
        $template = true;
        $procedures = [];
        $witnessList = TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_DIMENSION) ?? null;

        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_DIMENSION, 'active_sts' => 1])->one();
            if (!empty($testTemplate)) {
                $procedures = $model->customProcedures($testTemplate, false, false);
            }
            $template = false;
        } else {
            $procedures = $model->customProcedures($model, true, false);
        }

        $customContents = null;
        if ($model->got_custom_content == 1) {
            $testCustomContents = new \frontend\models\test\TestCustomContent();
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_DIMENSION, $id);
        }
        return $this->render('index', [
                    'model' => $model,
                    'master' => $master,
                    'dimensionList' => $dimensionList,
                    'panel' => $panel,
                    'procedures' => $procedures,
                    'template' => $template,
                    'witnessList' => $witnessList,
                    'customContents' => $customContents
        ]);
    }

    public function actionAjaxEditThreshold($id) {
        $model = TestFormDimension::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post('TestFormDimension');
            $model->treshold_a = $postData['treshold_a'] !== "" ? $postData['treshold_a'] : TestFormDimension::THRESHOLD_A;
            $model->treshold_b = $postData['treshold_b'] !== "" ? $postData['treshold_b'] : TestFormDimension::THRESHOLD_B;

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
        $model = TestFormDimension::findOne($id);
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

    public function actionAjaxAddDimensionItem($key, $masterId, $status, $id) {
        $dimension = new TestDetailDimension();
        $master = \frontend\models\test\TestMaster::findOne($masterId);
        $panel = $master->testMain->panel;
        $dimension->form_dimension_id = $id;
        $dimension->panel_name = $panel->panel_description;
        $dimension->save();

        return $this->renderPartial('_formDimensionItem', [
                    'dimension' => $dimension,
                    'key' => $key,
                    'panel' => $panel,
                    'status' => $status
        ]);
    }

    public function actionUpdateDimension($id) {
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isPost) {
            $dimensions = $post['testDetailDimension'] ?? null;
            if ($dimensions) {
                foreach ($dimensions as $dimension) {
                    if ($dimension['dimensionId']) {
                        $dimen = TestDetailDimension::find()->where(['id' => $dimension['dimensionId']])->one();
                    } else {
                        $dimen = new TestDetailDimension();
                    }
                    if ($dimension['toDelete'] == 1) {
                        $dimen->delete();
                    } else {
                        $dimen->form_dimension_id = $id;
                        $dimen->panel_name = ($dimension['dimensionPanel']);
                        $dimen->drawing_h = ($dimension['dimensionDrawingH']);
                        $dimen->drawing_w = ($dimension['dimensionDrawingW']);
                        $dimen->drawing_d = ($dimension['dimensionDrawingD']);
                        $dimen->built_h = $dimension['dimensionBuiltH'];
                        $dimen->built_w = $dimension['dimensionBuiltW'];
                        $dimen->built_d = $dimension['dimensionBuiltD'];
                        $dimen->error_h = $dimension['dimensionErrorH'];
                        $dimen->error_w = $dimension['dimensionErrorW'];
                        $dimen->error_d = $dimension['dimensionErrorD'];
                        $dimen->res_h = $dimension['dimensionResH'] ?? null;
                        $dimen->res_w = $dimension['dimensionResW'] ?? null;
                        $dimen->res_d = $dimension['dimensionResD'] ?? null;
                        !$dimen->panel_name ? null : ($dimen->isNewRecord ? $dimen->save() : $dimen->update(false));
                    }
                }
            }

            $model = TestFormDimension::findOne($id);
            $formDimen = $post['TestFormDimension'];
            $model->status = $formDimen['status'];
            $model->update(false);
            $master = $model->testMaster;
            $master->checkMasterStatus($master->id);

            \common\models\myTools\FlashHandler::success("Saved");
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                if ($attendances) {
                    foreach ($attendances as $attendance) {
                        $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_DIMENSION, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                        if (!$witness->isNewRecord) {
                            continue;
                        }
                        $witness->test_master_id = $master->id;
                        $witness->form_type = TestMaster::CODE_DIMENSION;
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
            return $this->redirect(["index", 'id' => $id]);
        }
    }

    public function actionSaveWitness($id) {
        $form = TestFormDimension::findOne($id);
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

    public function actionDimensionStatus($id, $sts) {
        $model = TestFormDimension::findOne($id);
        $master = $model->testMaster;

        $model->status = $sts;

        if ($model->update(false)) {
            $master->checkMasterStatus($master->id);

            if ($model->status == RefTestStatus::STS_IN_TESTING) {
                \common\models\myTools\FlashHandler::success('Dimension Testing Starts');
                return $this->redirect(['index', 'id' => $id]);
            }
            \common\models\myTools\FlashHandler::success('Dimension Status Updated');
            return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
        } else {
            \common\models\myTools\FlashHandler::success('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionDeleteForm($id) {
        $model = TestFormDimension::findOne($id);
        $master = $model->testMaster;
        $details = $model->testDetailDimensions;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($details as $detail) {
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            if (!$model->delete()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            FlashHandler::success('Dimension form deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
        }
        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

    public function actionEditProcedure($id) {
        $model = TestFormDimension::findOne($id);
        $template = true;
        $testTemplate = new TestTemplate();

        $procedures = [];
        if ($model->template === null) {
            $testTemplate = TestTemplate::find()->where(['formcode' => TestMaster::CODE_DIMENSION, 'active_sts' => 1])->one();
            if (!empty($testTemplate)) {
                $testTemplate = $procedures;
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
            $model = TestFormDimension::findOne($id);
            if (empty(trim(strip_tags($proctest1)))) {
                $model->template = null;
            } else {
//                $model->template = $proctest1;
                $newhtml = $testTemplate->cleanHtmlContent($proctest1);
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

//    public function actionSetWitness() {
//
//
//        if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
//            return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
//        }
//    }
    
    public function actionAddCustomContent($id) {
        $model = TestFormDimension::findOne($id);
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        if ($model->got_custom_content == 1) {
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_DIMENSION, $id);
        }

        $customContentArray = [];
        if ($customContents) {
            foreach ($customContents as $content) {
                $customContentArray[] = $content->content;
            }
        }
        // If no content exists, provide empty array with one empty field
        if (empty($customContentArray)) {
            $customContentArray = [''];
        }
        if (Yii::$app->request->post()) {
            $customContentArray = Yii::$app->request->post('custom_content', []);
            $processedContent = [];

            foreach ($customContentArray as $index => $content) {
                // Trim whitespace and decode HTML entities
                $cleanContent = trim($content);
                // Remove HTML tags except img tags, then decode entities
                $textOnly = trim(strip_tags(html_entity_decode($cleanContent), '<img>'));
                // If there's actual readable text, keep the content
                if (!empty($textOnly)) {
                    $processedContent[] = [
                        'content' => $cleanContent, // Save original content (with tags)
                        'content_order' => $index
                    ];
                }
            }

            // Only proceed if there is valid content
            if (!empty($processedContent)) {
                try {
                    $saveCustomContents = $testCustomContents->saveCustomContents($model->id, TestMaster::CODE_DIMENSION, $processedContent);
                    if ($saveCustomContents) {
                        $model->got_custom_content = 1;
                        $model->update();
                        FlashHandler::success('Custom content saved successfully.');
                    }
                } catch (Exception $e) {
                    FlashHandler::err('Failed to save custom content: ' . $e->getMessage());
                }
            } else {
                try {
                    $model->got_custom_content = 0;
                    $model->update();
                    FlashHandler::success('No custom content to save.');
                } catch (Exception $e) {
                    FlashHandler::err('Failed to update model: ' . $e->getMessage());
                }
            }

            $this->redirect(['index', 'id' => $id]);
        }

        return $this->render('addCustomContent', [
                    'model' => $model,
                    'customContents' => $customContents,
                    'customContentArray' => $customContentArray
        ]);
    }
}
