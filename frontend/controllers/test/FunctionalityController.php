<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormFunctionality;
use frontend\models\test\TestFormFunctionalitySearch;
use frontend\models\test\TestDetailFunctionality;
use frontend\models\test\TestItemFunctionality;
use frontend\models\test\RefTestStatus;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\test\TestItemWitness;
use frontend\models\test\TestMaster;

/**
 * FunctionalityController implements the CRUD actions for TestFormFunctionality model.
 */
class FunctionalityController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/test/functionality');
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
     * Lists all TestFormFunctionality models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormFunctionality::findOne($id);
        $master = $model->testMaster;
        $details = $model->testDetailFunctionalities;
        $functionalities = [];
        $customContents = null;
        if ($model->got_custom_content == 1) {
            $testCustomContents = new \frontend\models\test\TestCustomContent();
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_FUNCTIONALITY, $id);
        }

        foreach ($details as $detail) {
            $functionalities[$detail->id] = [
                'detail' => $detail,
                'items' => TestItemFunctionality::find()->where(['detail_functionality_id' => $detail->id])->orderBy(['order' => SORT_ASC])->all()
            ];
        }

        return $this->render('index', [
                    'model' => $model,
                    'master' => $master,
                    'functionalities' => $functionalities,
                    'witnessList' => TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_FUNCTIONALITY),
                    'customContents' => $customContents
        ]);
    }

    public function actionAddDetail($id) {
        $detail = new TestDetailFunctionality();
        $detail->form_functionality_id = $id;

        if ($detail->load(Yii::$app->request->post())) {

            if ($detail->save()) {
                $this->redirect(['index', 'id' => $id]);
            }
        }
        return $this->renderAjax('_formDetail', [
                    'detail' => $detail,
                    'potList' => \frontend\models\test\RefTestPoints::getDropDownList(\frontend\models\test\RefTestPoints::TYPE_METER),
        ]);
    }

    public function actionEditpot($id) {
        $detail = TestDetailFunctionality::findOne($id);
        if ($detail->load(Yii::$app->request->post())) {
            if ($detail->save()) {
                $this->redirect(['edit-functionality-list', 'id' => $id]);
            }
        }
        return $this->renderAjax('_formDetail', [
                    'detail' => $detail,
                    'potList' => \frontend\models\test\RefTestPoints::getDropDownList(\frontend\models\test\RefTestPoints::TYPE_METER),
        ]);
    }

    public function actionEditFunctionalityList($id) {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $functionalities = $req->post('testItemFunctionality');
            foreach ($functionalities as $functionality) {
                if ($functionality['functionalityId']) {
                    $function = TestItemFunctionality::find()->where(['id' => $functionality['functionalityId']])->one();
                } else {
                    $function = new TestItemFunctionality();
                    $count = TestItemFunctionality::find()->where(['detail_functionality_id' => $id])->count();
                    $function->order = $count++;
                }
                if ($functionality['toDelete'] == 1) {
                    $function->delete();
                } else {
                    $function->detail_functionality_id = $id;
                    $function->no = ucwords($functionality['functionalityNo']);
                    $function->feeder_tag = ucwords($functionality['functionalityFeeder']);
                    $function->voltage_apt = $functionality['functionalityPower'];
                    $function->wiring_tc = $functionality['functionalityWiring'];
                    $function->voltage_apt_sts = $functionality['functionalityVPass'] == 'pass' ? 1 : ($functionality['functionalityVPass'] == 'fail' ? 0 : null);
                    $function->wiring_tc_sts = $functionality['functionalityWPass'] == 'pass' ? 1 : ($functionality['functionalityWPass'] == 'fail' ? 0 : null);
                    !$function->feeder_tag ? null : ($function->isNewRecord ? $function->save() : $function->update(false));
                }
            }
            \common\models\myTools\FlashHandler::success("Saved.");
            $detail = TestDetailFunctionality::findOne($id);
            return $this->redirect(["index", 'id' => $detail->form_functionality_id]);
        }
        $models = TestItemFunctionality::find()->where(['detail_functionality_id' => $id])->all();
        $detail = TestDetailFunctionality::findOne($id);
        $testForm = $detail->formFunctionality;

        $master = $testForm->testMaster;
        return $this->render('editFunctionalityList', [
                    'functionalityList' => $models,
                    'testForm' => $testForm,
                    'master' => $master,
                    'detail' => $detail
        ]);
    }

    public function actionAjaxAddFunctionalityItem($key) {
        $functionality = new TestItemFunctionality();
        return $this->renderPartial('_formFunctionalityItem', [
                    'functionality' => $functionality,
                    'key' => $key,
        ]);
    }

    public function actionFunctionalityStatus($id, $sts) {
        $model = TestFormFunctionality::findOne($id);
        $master = $model->testMaster;

        $model->status = $sts;

        if ($model->update(false)) {
            $master->checkMasterStatus($master->id);
            \common\models\myTools\FlashHandler::success('Saved');
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                if ($sts == RefTestStatus::STS_FAIL || $sts == RefTestStatus::STS_COMPLETE) {
                    $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                    if ($attendances) {
                        foreach ($attendances as $attendance) {
                            $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_FUNCTIONALITY, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                            if (!$witness->isNewRecord) {
                                continue;
                            }
                            $witness->test_master_id = $master->id;
                            $witness->form_type = TestMaster::CODE_FUNCTIONALITY;
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
            return $this->redirect(['index', 'id' => $id]);
        } else {
            \common\models\myTools\FlashHandler::err('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionSaveWitness($id) {
        $form = TestFormFunctionality::findOne($id);
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

    public function actionAjaxReorderItem() {
        $post = Yii::$app->request->post();
        $detailId = $post['detailId'];
        $itemId = $post['id'];
        $newOrder = $post['order'];

        $itemToMove = TestItemFunctionality::findOne($itemId);

        if (!$itemToMove) {
            return \common\models\myTools\Mydebug::dumpFileW('Item not found');
        }

        $currentOrder = $itemToMove->order;

        $models = TestItemFunctionality::find()->where(['detail_functionality_id' => $detailId])->orderBy(['order' => SORT_ASC])->all();

        foreach ($models as $model) {
            if ($model->id == $itemId) {
                continue;
            }

            if ($model->order >= $newOrder && $model->order < $currentOrder) {
                $model->order += 1;
            } elseif ($model->order <= $newOrder && $model->order > $currentOrder) {
                $model->order -= 1;
            }

            $model->update(false);
        }

        $itemToMove->order = $newOrder;
        $itemToMove->save(false);

        return true;
    }

    public function actionDeleteForm($id) {
        $model = TestFormFunctionality::findOne($id);
        $master = $model->testMaster;
        $details = $model->testDetailFunctionalities;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($details as $detail) {
                foreach ($detail->testItemFunctionalities as $item) {
                    if (!$item->delete()) {
                        $transaction->rollBack();
                    }
                }
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            if (!$model->delete()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            FlashHandler::success('Functionality form deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
        }
        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
    }

    public function actionRevertForm($id) {
        $model = TestFormFunctionality::findOne($id);
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

    /**
     * by Khetty, 8/2/2024
     * Duplicate Created Table
     */
    public function actionDuplicateTableAjax() {
        $detailId = Yii::$app->request->post('detailId');
        $id = Yii::$app->request->post('id');
        $detail = TestDetailFunctionality::findOne($detailId);
        $transaction = Yii::$app->db->beginTransaction();
        $detailSuccess = true;
        $itemSuccess = true;

        if ($detail) {
            $dupDetail = new TestDetailFunctionality();
            $dupDetail->form_functionality_id = $detail->form_functionality_id;
            $dupDetail->pot = $detail->pot;
            $dupDetail->pot_val = $detail->pot_val;
            $dupDetail->status = $detail->status;
            if ($dupDetail->save()) {
                $items = TestItemFunctionality::find()->where(['detail_functionality_id' => $detailId])->all();
                if ($items !== null) {
                    foreach ($items as $item) {
                        $dupItem = new TestItemFunctionality();
                        $dupItem->detail_functionality_id = $dupDetail->id;
                        $dupItem->no = $item->no;
                        $dupItem->feeder_tag = $item->feeder_tag;
                        $dupItem->voltage_apt = $item->voltage_apt;
                        $dupItem->voltage_apt_sts = $item->voltage_apt_sts;
                        $dupItem->wiring_tc = $item->wiring_tc;
                        $dupItem->wiring_tc_sts = $item->wiring_tc_sts;
                        $dupItem->group_num = $item->group_num;
                        $dupItem->order = $item->order;
                        if (!$dupItem->save()) {
                            $itemSuccess = false;
                        }
                    }
                }
            } else {
                $detailSuccess = false;
            }

            if ($detailSuccess && $itemSuccess) {
                $transaction->commit();
                \common\models\myTools\FlashHandler::success('Table Duplicated Successfully');
            } else {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to Duplicate Table');
            }
            $this->redirect(['index', 'id' => $id]);
        }
    }

    /*
     * by Khetty, 8/2/2024
     * ****** Delete Table ******
     */

    public function actionDeleteTable($detailId, $id) {
        $transaction = Yii::$app->db->beginTransaction();
        $detailSuccess = true;
        $itemSuccess = true;

        $items = TestItemFunctionality::find()->where(['detail_functionality_id' => $detailId])->all();
        if ($items !== null) {
            foreach ($items as $item) {
                if (!$item->delete()) {
                    $itemSuccess = false;
                }
            }
        }

        if ($itemSuccess = true) {
            $detail = TestDetailFunctionality::findOne($detailId);
            if ($detail && $detail->delete()) {
                $items = TestItemFunctionality::find()->where(['detail_functionality_id' => $detailId])->all();
                if ($items !== null) {
                    foreach ($items as $item) {
                        if (!$item->delete()) {
                            $itemSuccess = false;
                        }
                    }
                }
            } else {
                $detailSuccess = false;
            }
        }

        if ($detailSuccess && $itemSuccess) {
            $transaction->commit();
            \common\models\myTools\FlashHandler::success('Table Deleted Successfully');
        } else {
            $transaction->rollBack();
            \common\models\myTools\FlashHandler::err('Failed to Delete Table');
        }
        $this->redirect(['index', 'id' => $id]);
    }

    public function actionAddCustomContent($id) {
        $model = TestFormFunctionality::findOne($id);
        $customContents = null;
        $testCustomContents = new \frontend\models\test\TestCustomContent();

        if ($model->got_custom_content == 1) {
            $customContents = $testCustomContents->getCustomContents(TestMaster::CODE_FUNCTIONALITY, $id);
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
                    $saveCustomContents = $testCustomContents->saveCustomContents($model->id, TestMaster::CODE_FUNCTIONALITY, $processedContent);
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
