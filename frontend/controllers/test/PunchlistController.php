<?php

namespace frontend\controllers\test;

use frontend\models\test\TestMaster;
use frontend\models\test\TestFormPunchlist;
use frontend\models\test\TestFormPunchlistSearch;
use frontend\models\test\TestDetailPunchlist;
use frontend\models\test\RefTestFormList;
use frontend\models\test\RefTestStatus;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * PunchlistController implements the CRUD actions for TestFormPunchlist model.
 */
class PunchlistController extends Controller {

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
     * Lists all TestFormPunchlist models.
     *
     * @return string
     */
    public function actionIndex($id) {
        $model = TestFormPunchlist::findOne($id);
        $master = $model->testMaster;
        $punchlists = TestDetailPunchlist::findAll(['form_punchlist_id' => $id]);
        $searchModel = new \frontend\models\test\TestDetailPunchlistSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, 'singleForm', $id);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'master' => $master,
                    'punchlists' => $punchlists,
        ]);
    }

    public function actionEditPunchlist($id) {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $punchlists = $req->post('testDetailPunchlist');
            foreach ($punchlists as $punchlist) {
                if ($punchlist['punchlistId']) {
                    $punch = TestDetailPunchlist::find()->where(['id' => $punchlist['punchlistId'], 'form_punchlist_id' => $id])->one();
                } else {
                    $punch = new TestDetailPunchlist();
                }
                if ($punchlist['toDelete'] == 1) {
                    $punch->delete();
                } else {
                    $punch->form_punchlist_id = $id;
                    $punch->test_form_code = $punchlist['punchlistForm'];
                    $punch->error_id = $punchlist['punchlistError'];
                    $punch->remark = $punchlist['punchlistRemark'];
                    $punch->rectify_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($punchlist['punchlistDate']);
                    $punch->verify_by = $punchlist['punchlistVerify'];
                    if ($punch->isNewRecord) {
                        $punch->save();
                    } else {
                        $punch->update(false);
                    }
                }
            }
            FlashHandler::success("Saved.");
            return $this->redirect(["index", 'id' => $id]);
        }
        $models = TestDetailPunchlist::find()->where(['form_punchlist_id' => $id])->all();
        $testForm = TestFormPunchlist::findOne($id);
        $master = $testForm->testMaster;
        return $this->render('editPunchlist', [
                    'punchlists' => $models,
                    'testForm' => $testForm,
                    'master' => $master,
        ]);
    }

    public function actionAjaxAddPunchlistItem($key, $masterId) {
        $punchlist = new TestDetailPunchlist();
        $master = TestMaster::findOne($masterId);
        return $this->renderPartial('_formPunchlistItem', [
                    'punchlist' => $punchlist,
                    'key' => $key,
                    'master' => $master
        ]);
    }

    public function actionEditSinglePunchlist($id) {
        $detail = TestDetailPunchlist::findOne($id);
        $master = $detail->formPunchlist->testMaster;
        $refForm = RefTestFormList::find()->where(['code' => $detail->test_form_code])->one();
        $list[$refForm->code] = $refForm->formname;

        if ($detail->load(Yii::$app->request->post())) {
            $detail->rectify_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($detail->rectify_date);
            if ($detail->save()) {
                FlashHandler::success('Saved');
                return $this->redirect(['index', 'id' => $detail->form_punchlist_id]);
            } else {
                \common\models\myTools\Mydebug::dumpFileW($detail->getError());
            }
        }
        return $this->renderAjax('_formAddPunchlist', [
                    'detail' => $detail,
                    'master' => $master,
                    'formcodeList' => $list
        ]);
    }

    public function actionAddPunchlist($id) {
        $model = TestFormPunchlist::findOne($id);
        $master = $model->testMaster;
        $detail = new TestDetailPunchlist();

        if ($detail->load(Yii::$app->request->post())) {
            $detail->form_punchlist_id = $id;
            $detail->rectify_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($detail->rectify_date);
            if ($detail->save()) {
                FlashHandler::success('Saved');
                return $this->redirect(['index', 'id' => $id]);
            }
        }
        return $this->renderAjax('_formAddPunchlist', [
                    'master' => $master,
                    'model' => $model,
                    'detail' => $detail,
                    'formcodeList' => TestMaster::getDropdownSelectedForms($master->id)
        ]);
    }

    public function actionCompletePunchlist($id) {
        $model = TestFormPunchlist::findOne($id);
        $master = $model->testMaster;

        $model->status = \frontend\models\test\RefTestStatus::STS_COMPLETE;
        if ($model->update(false)) {
            $master->checkMasterStatus($master->id);
            FlashHandler::success('Punchlist Completed');
            return $this->redirect(['/test/testing/index-master-detail', 'id' => $master->id]);
        } else {
            FlashHandler::success('Error. Please contact IT department');
            return $this->redirect(['index', 'id' => $id]);
        }
    }

    public function actionAddPunchlistFromForm($masterId, $formType) {
        $master = TestMaster::findOne($masterId);
        $refForm = RefTestFormList::find()->where(['code' => $formType])->one();
        $list[$refForm->code] = $refForm->formname;
        $model = $master->testFormPunchlist;
        $detail = new TestDetailPunchlist();

        return $this->renderAjax('_formAddPunchlist', [
                    'master' => $master,
                    'model' => $model,
                    'detail' => $detail,
                    'formcodeList' => $list,
                    'otherForm' => true
        ]);
    }

    public function actionSave() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $masterId = Yii::$app->request->post('masterId');

        $master = TestMaster::findOne($masterId);
        $model = $master->testFormPunchlist;

        $detail = new TestDetailPunchlist();

        if ($detail->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $detail->form_punchlist_id = $model->id;
            $detail->rectify_date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($detail->rectify_date);
            if ($detail->save()) {
                return ['success' => true];
            }
        } else {
            return ['success' => false];
        }
    }

    public function actionRevertForm($id) {
        $model = TestFormPunchlist::findOne($id);
        $master = $model->testMaster;

        if ($model) {
            if ($model->status === RefTestStatus::STS_COMPLETE) {
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

    public function actionDelete($id) {
        $detail = TestDetailPunchlist::findOne($id);
        $model = $detail->formPunchlist;

        if ($detail->delete()) {
            FlashHandler::success('Punchlist deleted');
            return $this->redirect(['index',
                        'id' => $model->id
            ]);
        }
    }

}
