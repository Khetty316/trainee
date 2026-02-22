<?php

namespace frontend\controllers\test;

use frontend\models\test\TestFormComponent;
use frontend\models\test\TestFormComponentSearch;
use frontend\models\test\TestDetailComponent;
use frontend\models\test\TestItemCompOther;
use frontend\models\test\TestDetailConform;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestCompFunc;
use frontend\models\test\RefTestCompType;
use frontend\models\test\RefTestAccessory;
use frontend\models\test\RefTestPoints;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestItemWitness;

/**
 * ComponentController implements the CRUD actions for TestFormComponent model.
 */
class ComponentController extends Controller {

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
     * Lists all TestFormComponent models.
     *
     * @return string
     */
    public function actionIndex($id, $addComponentForm) {
        $form = TestFormComponent::findOne($id);
        $master = $form->testMaster;
        $detailDatas = [];
        $details = TestDetailComponent::find()->where(['form_component_id' => $id])->all();

        foreach ($details as $detail) {
            $detailDatas[$detail->id]['form'] = $detail;
            $detailDatas[$detail->id]['form'][TestDetailComponent::ATTRIBUTE_ACCESSORY] = explode(", ", $detailDatas[$detail->id]['form'][TestDetailComponent::ATTRIBUTE_ACCESSORY]);
            $attributeToRender = TestDetailComponent::getVisibilitySettingsForType($detail->compType->code);

            $detailDatas[$detail->id]['attributetorender'] = $attributeToRender;
            if ($detail->compType->code == RefTestCompType::TYPE_OTHER) {
                $detailDatas[$detail->id]['otheritem'] = $detail->testItemCompOthers ?? new TestItemCompOther();
            }
        }

        $conformities = $form->testDetailConforms;

        return $this->render('index', [
                    'model' => $form,
                    'details' => $detailDatas,
                    'master' => $master,
                    'conformities' => $conformities,
                    'compList' => RefTestCompType::getDropDownList(),
                    'accesList' => RefTestAccessory::getDropDownList(),
                    'funcList' => RefTestCompFunc::getDropDownList(),
                    'pointMeterList' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_METER),
                    'pointBusbarList' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_BUSBAR),
                    'pointParticularFnList' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_PARTICULARFN),
                    'pointProtectionModeList' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_PROTECTIONMODE),
                    'addComponentForm' => $addComponentForm,
                    'witnessList' => TestItemWitness::getTestItemWitness($master->id, TestMaster::CODE_COMPONENT)
        ]);
    }

    public function actionAddComponentForm($id) {
        $form = TestFormComponent::findOne($id);
        $model = new TestDetailComponent();

        if ($model->load(Yii::$app->request->post())) {
            $model->form_component_id = $id;

            if ($model->save()) {
                FlashHandler::success('Form Added');
                return $this->redirect(['index',
                            'id' => $id,
                            'addComponentForm' => true
                ]);
            }
        }

        return $this->renderAjax('_addComponentForm', [
                    'form' => $form,
                    'model' => $model,
                    'compList' => RefTestCompType::getDropDownList(),
                    'pointList1' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_METER),
                    'pointList2' => RefTestPoints::getDropDownList(RefTestPoints::TYPE_BUSBAR),
        ]);
    }

    public function actionSaveComponent() {
        $post = Yii::$app->request->post();
        $form = TestFormComponent::findOne($post['TestFormComponent']['id']);
        $sts = true;
        if (!empty($post['testDetailComponent'])) {
            $sts = $this->actionSaveDetailComponent($post['testDetailComponent']);
        }
        if (!empty($post['testDetailConform'])) {
            $sts = $this->actionSaveConformity($post['testDetailConform'], $form->id);
        }
        if (!empty($post['testItemComponent'])) {
            $sts = $this->actionSaveOtherComponent($post['testItemComponent']);
        }
        if ($sts) {
            FlashHandler::success('Saved');
        } else {
            FlashHandler::err('Error while processing request.');
        }
        if (!empty($post['TestFormComponent']['submitSts']) && $this->actionComponentStatus($form->id, $post['TestFormComponent']['submitSts'])) {
            FlashHandler::success('Saved');
            if ($form->status == RefTestStatus::STS_IN_TESTING) {
//                return $this->redirect(['/test/testing/index-master-detail', 'id' => $form->test_master_id]);
            }
        }
        return $this->redirect(['index', 'id' => $form->id, 'addComponentForm' => false]);
    }

    private function actionSaveDetailComponent($datas) {
        $sts = true;
        foreach ($datas as $id => $detail) {
            $model = TestDetailComponent::findOne($id);
            $model->pou = ucwords(strtolower($detail[TestDetailComponent::ATTRIBUTE_POU] ?? $model->pou));
            $model->pou_val = $detail[TestDetailComponent::ATTRIBUTE_POUVAL] ?? $model->pou_val;
            $model->function_type = $detail[TestDetailComponent::ATTRIBUTE_FUNCTIONTYPE] ?? null;
            $model->comp_name = $detail[TestDetailComponent::ATTRIBUTE_COMPNAME] ?? null;
            $model->make = $detail[TestDetailComponent::ATTRIBUTE_MAKE] ?? null;
            $model->type = $detail[TestDetailComponent::ATTRIBUTE_TYPE] ?? null;
            $model->serial_num = $detail[TestDetailComponent::ATTRIBUTE_SERIALNUM] ?? null;
            $model->prot_mode = $detail[TestDetailComponent::ATTRIBUTE_PROTECTIONMODE] ?? null;
            $model->particular_fn = $detail[TestDetailComponent::ATTRIBUTE_PARTICULARFUNCTION] ?? null;
            $model->amps = $detail[TestDetailComponent::ATTRIBUTE_AMPS] ?? null;
            $model->ratio_a = $detail[TestDetailComponent::ATTRIBUTE_RATIOA] ?? null;
            $model->ratio_b = $detail[TestDetailComponent::ATTRIBUTE_RATIOB] ?? null;
            $model->breakcap = $detail[TestDetailComponent::ATTRIBUTE_BREAKCAP] ?? null;
            $model->acc_class = $detail[TestDetailComponent::ATTRIBUTE_ACCCLASS] ?? null;
            $model->burden = $detail[TestDetailComponent::ATTRIBUTE_BURDEN] ?? null;
            $model->voltage = $detail[TestDetailComponent::ATTRIBUTE_VOLTAGE] ?? null;
            $model->setting = $detail[TestDetailComponent::ATTRIBUTE_SETTING] ?? null;
            $model->tms = $detail[TestDetailComponent::ATTRIBUTE_TMS] ?? null;
            $model->nominal_dc = $detail[TestDetailComponent::ATTRIBUTE_NOMINALDC] ?? null;
            $model->dimension_a = $detail[TestDetailComponent::ATTRIBUTE_DIMENSIONA] ?? null;
            $model->dimension_b = $detail[TestDetailComponent::ATTRIBUTE_DIMENSIONB] ?? null;
            if (isset($detail[TestDetailComponent::ATTRIBUTE_ACCESSORY]) && is_array($detail[TestDetailComponent::ATTRIBUTE_ACCESSORY])) {
                $model->accessory = implode(", ", $detail[TestDetailComponent::ATTRIBUTE_ACCESSORY]);
            } else {
                $model->accessory = null;
            }
            if ($model->getDirtyAttributes()) {
                if (!$model->update(false)) {
                    $sts = false;
                }
            }
        }
        return $sts;
    }

    private function actionSaveConformity($datas, $formId) {
        $sts = true;
        foreach ($datas as $conformity) {
            if ($conformity['conformityId']) {
                $conform = TestDetailConform::find()->where(['id' => $conformity['conformityId'], 'form_component_id' => $formId])->one();
            } else {
                $conform = new TestDetailConform();
            }

            if ($conformity['toDelete'] == 1) {
                $conform->delete();
            } else {
                $conform->form_component_id = $formId;
                $conform->non_conform = $conformity['conformityComponent'];
                $conform->remark = $conformity['conformityRemark'];
                if ($conform->isAttributeChanged('non_conform') || $conform->isAttributeChanged('remark') || $conform->isNewRecord) {
                    if (!$conform->save()) {
                        $sts = false;
                    }
                }
            }
        }
        return $sts;
    }

    private function actionSaveOtherComponent($datas) {
        $sts = true;
        foreach ($datas as $item) {
            if ($item['idOther']) {
                $otherItem = TestItemCompOther::find()->where(['id' => $item['idOther'], 'detail_component_id' => $item['idDetail']])->one();
            } else {
                $otherItem = new TestItemCompOther();
            }

            if ($item['toDelete'] == 1) {
                $otherItem->delete();
            } else {
                $otherItem->detail_component_id = $item['idDetail'];
                $otherItem->attribute = $item['attributeOther'];
                $otherItem->value = $item['valueOther'];
                if ($otherItem->isAttributeChanged('attribute') || $otherItem->isAttributeChanged('value') || $otherItem->isNewRecord) {
                    if (!$otherItem->save()) {
                        $sts = false;
                    }
                }
            }
        }
        return $sts;
    }

    public function actionAjaxAddOtherComponent($key0, $idDetail) {
        $item = new TestItemCompOther();
        return $this->renderPartial('_formItemOther', [
                    'item' => $item,
                    'key0' => $key0,
                    'detailId' => $idDetail
        ]);
    }

    public function actionAjaxAddConformityItem($key2) {
        $conformity = new TestDetailConform();
        return $this->renderPartial('_formConformityItem', [
                    'conformity' => $conformity,
                    'key2' => $key2,
        ]);
    }

    public function actionComponentStatus($id, $sts) {
        if ($sts == null) {
            return;
        }
        $model = TestFormComponent::findOne($id);
        $model->status = $sts;
        $master = $model->testMaster;

        if ($model->update(false)) {
            $master->checkMasterStatus($master->id);
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                $attendances = $master->testFormAttendance->testDetailAttendances ?? null;
                if ($attendances) {
                    foreach ($attendances as $attendance) {
                        $witness = TestItemWitness::find()->where(['test_master_id' => $master->id, 'form_type' => TestMaster::CODE_COMPONENT, 'name' => $attendance->name])->one() ?? new TestItemWitness();
                        if (!$witness->isNewRecord) {
                            continue;
                        }
                        $witness->test_master_id = $master->id;
                        $witness->form_type = TestMaster::CODE_COMPONENT;
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
            return $this->redirect(['index', 'id' => $id, 'addComponentForm' => false]);
        }
    }

    public function actionSaveWitness($id) {
        $form = TestFormComponent::findOne($id);
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

    public function actionRevertForm($id) {
        $model = TestFormComponent::findOne($id);
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

        return $this->redirect(['index', 'id' => $model->id, 'addComponentForm' => false]);
    }

    public function actionDeleteForm($id) {
        $model = TestFormComponent::findOne($id);
        $master = $model->testMaster;
        $detailsComp = $model->testDetailComponents;
        $detailsConf = $model->testDetailConforms;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($detailsComp as $detail) {
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            foreach ($detailsConf as $detail) {
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            if (!$model->delete()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            FlashHandler::success('Component form deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
        }
        return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id, 'addComponentForm' => false]);
    }

    public function actionDeleteComponent($id) {
        $model = TestDetailComponent::findOne($id);
        $detailsComp = $model->testItemCompOthers;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($detailsComp as $detail) {
                if (!$detail->delete()) {
                    $transaction->rollBack();
                }
            }
            if (!$model->delete()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            FlashHandler::success('Component deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
        }
        return $this->redirect(['index', 'id' => $model->formComponent->id, 'addComponentForm' => false]);
    }

}
