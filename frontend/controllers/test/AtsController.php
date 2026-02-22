<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormAts;
use frontend\models\test\TestFormAtsSearch;
use frontend\models\test\TestDetailAts;
use frontend\models\test\RefTestStatus;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\test\TestItemWitness;
use frontend\models\test\TestMaster;

/**
 * AtsController implements the CRUD actions for TestFormAts model.
 */
class AtsController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/ats');
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
     * Lists all TestFormAts models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $form = TestFormAts::findOne($id);
        $master = $form->testMaster;
        $customContents = null;
        if ($form->got_custom_content == 1) {
            $testCustomContents = new \frontend\models\test\TestCustomContent();
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_ATS, $id);
        }
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isPost) {
            foreach ($post['TestFormAts'] as $attribute => $value) {
                if ($attribute != 'submitSts') {
                    $form->$attribute = $value;
                }
            }
            $form->update(false);
            foreach ($post['testDetailAts'] as $detaildata) {
                $detailModel = TestDetailAts::findOne($detaildata['id']);
                $detailModel->mode = $detaildata['mode'];
                $detailModel->update(false);
            }
            if (!empty($post['TestFormAts']['submitSts']) && $this->actionAtsStatus($id, $post['TestFormAts']['submitSts'])) {
                FlashHandler::success('ATS status updated');
                if ($form->status == RefTestStatus::STS_FAIL || $form->status == RefTestStatus::STS_COMPLETE) {
                    return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
                } else {
                    return $this->redirect(['index', 'id' => $form->id]);
                }
            }
        }

        if (empty($form->testDetailAts)) {
            $this->actionAjaxAddDetailRow($id);
        } else {
            $detailAcots = TestDetailAts::find()->where(['form_ats_id' => $id, 'form_type' => TestFormAts::FORM_TYPE_ACOT])->all();
            $detailMcots = TestDetailAts::find()->where(['form_ats_id' => $id, 'form_type' => TestFormAts::FORM_TYPE_MCOT])->all();
            $detailCbvcs = TestDetailAts::find()->where(['form_ats_id' => $id, 'form_type' => TestFormAts::FORM_TYPE_CBVC])->all();
            return $this->render('index', [
                        'model' => $form,
                        'master' => $master,
                        'detailAcots' => $detailAcots,
                        'detailMcots' => $detailMcots,
                        'detailCbvcs' => $detailCbvcs,
                        'witnessList' => TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_ATS),
                        'customContents' => $customContents
            ]);
        }
    }

    public function actionAjaxAddDetailRow($key, $formType = TestFormAts::FORM_ALL) {
        $formTypes = is_array($formType) ? $formType : [$formType];

        foreach ($formTypes as $type) {
            if ($type == TestFormAts::FORM_TYPE_ACOT) {
                $form = TestDetailAts::find()->where(['form_ats_id' => $key, 'form_type' => TestFormAts::FORM_TYPE_ACOT])->all();
                $nums = !empty($form) ? [count($form) + 1] : [1, 2, 3];
                foreach ($nums as $num) {
                    $detail = new TestDetailAts();
                    $detail->form_ats_id = $key;
                    $detail->form_type = $type;
                    $detail->mode = "Feature $num";
                    $detail->val_acot_1 = $num == 1 ? 1 : 0;
                    $detail->val_acot_2 = $num == 2 ? 1 : 0;
                    $detail->val_acot_3 = $num == 3 ? 1 : 0;
                    $detail->save();
                }
            }
            if ($type == TestFormAts::FORM_TYPE_MCOT) {
                $nums = (TestDetailAts::find()->where(['form_ats_id' => $key, 'form_type' => $type])->all()) ? ["Component"] : TestFormAts::DEFAULT_MCOT;
                foreach ($nums as $num) {
                    $detail = new TestDetailAts();
                    $detail->form_ats_id = $key;
                    $detail->form_type = $type;
                    $detail->mode = "$num";
                    $detail->save();
                }
            }
            if ($type == TestFormAts::FORM_TYPE_CBVC) {
                $form = TestDetailAts::find()->where(['form_ats_id' => $key, 'form_type' => TestFormAts::FORM_TYPE_CBVC])->all();
                $nums = !empty($form) ? [count($form) + 1] : [1, 2, 3];
                foreach ($nums as $num) {
                    $detail = new TestDetailAts();
                    $detail->form_ats_id = $key;
                    $detail->form_type = $type;
                    $detail->mode = "Feature $num";
                    $detail->val_cbvc_1 = $num == 1 || $num == 3 ? 1 : 0;
                    $detail->val_cbvc_2 = $num == 1 || $num == 2 ? 1 : 0;
                    $detail->val_cbvc_3 = $num == 2 || $num == 3 ? 1 : 0;
                    $detail->save();
                }
            }
            FlashHandler::success('Row added');
        }
        return $this->redirect(['index', 'id' => $key]);
    }

    public function actionAjaxAddDetailColumn($key, $type, $formType) {
        if ($formType == TestFormAts::FORM_TYPE_ACOT) {
            $form = TestFormAts::findOne($key);
            $form->addHeaderAcot();
        }
        if ($formType == TestFormAts::FORM_TYPE_CBVC) {
            $form = TestFormAts::findOne($key);
            $form->addHeaderCbvc($type);
        }
        FlashHandler::success('Column added');
        return $this->redirect(['index',
                    'id' => $key,
        ]);
    }

    public function actionAjaxDeleteColumn($key, $attribute, $formType) {
        if ($formType == TestFormAts::FORM_TYPE_ACOT) {
            $form = TestFormAts::findOne($key);
            $form->$attribute = null;
            $form->update(false);
        }
        if ($formType == TestFormAts::FORM_TYPE_CBVC) {
            $form = TestFormAts::findOne($key);
            $form->$attribute = null;
            $form->update(false);
        }
        FlashHandler::success('Column deleted');
        return $this->redirect(['index',
                    'id' => $key,
        ]);
    }

    public function actionAjaxDeleteRow($key) {
        $detail = TestDetailAts::findOne($key);
        $form = $detail->formAts;
        if ($detail->delete()) {
            FlashHandler::success('Row deleted');
        }
        return $this->redirect(['index',
                    'id' => $form->id,
        ]);
    }

    public function actionAtsStatus($id, $sts) {
        if ($sts == null) {
            return;
        }
        $model = TestFormAts::findOne($id);
        $model->status = $sts;
        $master = $model->testMaster;

        if ($model->update(false)) {
            $master->checkMasterStatus($master->id);
            FlashHandler::success('ATS Status Updated');
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                if ($attendances) {
                    foreach ($attendances as $attendance) {
                        $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_ATS, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                        if (!$witness->isNewRecord) {
                            continue;
                        }
                        $witness->test_master_id = $master->id;
                        $witness->form_type = TestMaster::CODE_ATS;
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
            return true;
        } else {
            FlashHandler::err('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionSaveWitness($id) {
        $form = TestFormAts::findOne($id);
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

    public function actionMarkAttributeResult() {
        $post = Yii::$app->request->post();
        $model = TestDetailAts::findOne($post['detailid']);
        $attribute = $post['attribute'];
        $value = $post['value'];

        $model->$attribute = $value;

        if ($model->update(false)) {
            return true;
        } else {
            return false;
        }
    }

    public function actionSaveTextInput() {
        $post = Yii::$app->request->post();
        $model = TestFormAts::findOne(intval($post['formid']));
        $attribute = $post['attribute'];
        $value = $post['userInput'];

        $model->$attribute = $value;

        if ($model->update(false)) {
            return true;
        } else {
            return false;
        }
    }

    public function actionSaveTextInputMode() {
        $post = Yii::$app->request->post();
        $model = TestDetailAts::findOne(intval($post['formid']));
        $attribute = $post['attribute'];
        $value = $post['userInput'];

        $model->$attribute = $value;

        if ($model->update(false)) {
            return true;
        } else {
            return false;
        }
    }

    public function actionRevertForm($id) {
        $model = TestFormAts::findOne($id);
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
        $model = TestFormAts::findOne($id);
        $details = $model->testDetailAts;
        $master = $model->testMaster;

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
            FlashHandler::success('ATS Functionality form deleted');
        } catch (Exception $exc) {
            $transaction->rollBack();
            FlashHandler::err('Error processing request');
        }

        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

    public function actionAddCustomContent($id) {
        $model = TestFormAts::findOne($id);
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        if ($model->got_custom_content == 1) {
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_ATS, $id);
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
                    $saveCustomContents = $testCustomContents->saveCustomContents($model->id, TestMaster::CODE_ATS, $processedContent);
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

    public function actionUploadImage() {
        $testTemplate = new \frontend\models\test\TestTemplate();
        $response = $testTemplate->uploadImage();
        return $response;
    }
}
