<?php

namespace frontend\controllers;

use Yii;
use frontend\models\appraisal\AppraisalMain;
use frontend\models\appraisal\AppraisalMaster;
use frontend\models\appraisal\VAppraisalMaster;
use frontend\models\appraisal\AppraisalMasterForm;
use frontend\models\appraisal\AppraisalMasterFactor;
use frontend\models\common\RefAppraisalForm;
use frontend\models\common\RefAppraisalFactor;
use frontend\models\common\RefAppraisalStatus;
use common\models\myTools\FlashHandler;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppraisalgnrlController implements the CRUD actions for AppraisalMaster model.
 */
class AppraisalgnrlController extends Controller {

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
                        'roles' => ['@']
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

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/appraisal');
    }

    public function actionIndexMain() {
        $userId = Yii::$app->user->id;
        $models = VAppraisalMaster::find()->where(['review_by' => $userId])->orderBy('created_at DESC')->groupBy('main_id')->asArray()->all();
        return $this->render('indexSuperior', [
                    'models' => json_encode($models),
                    'statusOptions' => RefAppraisalStatus::getDropDownListMain()
        ]);
    }

    public function actionIndexRating() {
        $userId = Yii::$app->user->id;
        $models = VAppraisalMaster::find()->where(['user_id' => $userId])->orderBy('created_at DESC')->groupBy('main_id')->asArray()->all();

        return $this->render('viewStaff', [
                    'modelsJson' => json_encode($models),
                    'models' => $models,
                    'statusOptions' => RefAppraisalStatus::getDropDownListMaster()
        ]);
    }

    public function actionIndexReview($id) {
        $userId = Yii::$app->user->id;
        $main = AppraisalMain::findOne($id);
        $models = VAppraisalMaster::find()->where(['review_by' => $userId, 'main_id' => $id])->orderBy('fullname ASC')->asArray()->all();
        
        return $this->render('viewSuperior', [
                    'main' => $main,
                    'modelsJson' => json_encode($models),
                    'models' => $models,
                    'statusOptions' => RefAppraisalStatus::getDropDownListMaster()
        ]);
    }

    public function actionViewAppraisal($id, $super = false) {
        $vModel = VAppraisalMaster::findOne($id);
        $model = AppraisalMaster::findOne($id);
        $main = $model->main;
        $forms = AppraisalMasterForm::getFormsAsArray($model->id);
        $factors = [];
        foreach ($forms as $key => $form) {
            $factors[$key] = AppraisalMasterFactor::getFactorAsArray($form['id']);
        }

        $view = $super ? "viewAppraisalSuperior" : "viewAppraisalStaff";
        return $this->render($view, [
                    'main' => $main,
                    'vModel' => $vModel,
                    'model' => json_encode($model),
                    'forms' => json_encode($forms),
                    'factors' => json_encode($factors),
        ]);
    }

    public function actionBeginStaffAppraisal($id, $super = false) {
        $model = AppraisalMaster::find()->where(['id' => $id])->asArray()->one();
        $modelObject = AppraisalMaster::find()->where(['id' => $id])->one();
        $main = AppraisalMain::findOne($model['main_id']);
        $forms = AppraisalMasterForm::getFormsAsArray($model['id']);
        $factors = [];
        foreach ($forms as $key => $form) {
            $factors[$key] = AppraisalMasterFactor::getFactorAsArray($form['id']);
        }
        $view = $super ? "formSuperiorReview" : "formStaffAppraisal";
        return $this->render($view, [
                    'main' => $main,
                    'modelObject' => $modelObject,
                    'model' => json_encode($model),
                    'forms' => json_encode($forms),
                    'factors' => json_encode($factors),
        ]);
    }

    public function actionProcessFactorMark() {
        $request = Yii::$app->request;
        $masterId = json_decode($request->post('master'));
        $factors = json_decode($request->post('factors'));
        $staffRemark = $request->post('staffRemark');
        $type = $request->post('type');
        $saveDraft = $request->post('saveDraft');
        $master = null;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($factors)) {
                foreach ($factors as $factorData) {
                    $factorId = $factorData->factorId;
                    $mark = $factorData->$type;
                    $factor = AppraisalMasterFactor::findOne($factorId);
                    if ($factor) {
                        $factor->$type = $mark;
                        $factor->update(false);
                        $form = AppraisalMasterForm::findOne($factor->appraisal_master_form_id);
                        $master = $form->appraisalMaster;
                        if ($saveDraft === "false") {
                            if ($type == AppraisalMaster::TYPE_RATING) {
                                $master->appraise_date = new \yii\db\Expression('NOW()');
                                $master->appraisal_sts = RefAppraisalStatus::STS_RATED_NOT_CONFIRMED;
                            } else {
                                $master->review_date = new \yii\db\Expression('NOW()');
                                $master->appraisal_sts = RefAppraisalStatus::STS_REVIEWED_NOT_CONFIRMED;
                            }
                            $master->update(false);
                        }

                        if ($this->updateAppraisalFormMark($form->id, $type)) {
                            $saveDraft ? FlashHandler::success('Appraisal Drafted') : FlashHandler::success('Appraisal Saved.');
                        }

                        $this->calculateOverall($master->id, $type);
                    }
//                if (!$saveDraft) {
//                    $this->calculateOverall($master->id, $type);
//                }
                }
            } else {
                $master = AppraisalMaster::findOne($masterId);
                if ($saveDraft === "false") {
                    if ($type == AppraisalMaster::TYPE_RATING) {
                        $master->appraise_date = new \yii\db\Expression('NOW()');
                        $master->appraisal_sts = RefAppraisalStatus::STS_RATED_NOT_CONFIRMED;
                    } else {
                        $master->review_date = new \yii\db\Expression('NOW()');
                        $master->appraisal_sts = RefAppraisalStatus::STS_REVIEWED_NOT_CONFIRMED;
                    }
                    $master->update(false);
                }
            }

//            $id = $master->id ?? $masterId;
//            $this->calculateOverall($id, $type);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err('An error occurred: ' . $e->getMessage());
        }

        if ($masterId) {
            $mastera = AppraisalMaster::findOne($masterId);
            $mastera->staff_remark = $staffRemark;
            $mastera->update(false);
        }
    }

    public function updateAppraisalFormMark($formId, $type) {
        $form = AppraisalMasterForm::findOne($formId);
        $subtotalMark = AppraisalMasterFactor::find()->where(['appraisal_master_form_id' => $formId])->sum("$type");

        $smName = "subtotal_" . $type;
        $form->$smName = $subtotalMark;

        $finalSubtotalMark = ($subtotalMark * 5) / ($form->fullmark);
        $fsmName = "final_subtotal_" . $type;
        $form->$fsmName = $finalSubtotalMark;
        return $form->update(false);
    }

    public function actionConfirmAppraisal($id, $sts = false) {
        $vModel = VAppraisalMaster::findOne($id);
        $model = AppraisalMaster::findOne($id);
        $main = $model->main;
        $forms = AppraisalMasterForm::getFormsAsArray($model->id);
        $factors = [];
        foreach ($forms as $key => $form) {
            $factors[$key] = AppraisalMasterFactor::getFactorAsArray($form['id']);
        }

        if ($sts) {
            if ($model->appraisal_sts != RefAppraisalStatus::STS_RATED_NOT_CONFIRMED && $model->appraisal_sts != RefAppraisalStatus::STS_WAIT_RATING) {
                FlashHandler::err('Error occured');
            } else {
                $model->appraisal_sts = RefAppraisalStatus::STS_WAIT_REVIEW;
                $model->update(false);
                FlashHandler::success('Rating Confirmed');
            }
            AppraisalMain::updateStatuses($main->id);

            return $this->redirect(['index-rating']);
        }

        return $this->render('confirmAppraisal', [
                    'main' => $main,
                    'vModel' => $vModel,
                    'model' => json_encode($model),
                    'forms' => json_encode($forms),
                    'factors' => json_encode($factors),
        ]);
    }

    public function actionConfirmAppraisalReview($id) {
        $model = AppraisalMaster::findOne($id);
        $mainId = $model->main->id;
        if ($model->appraisal_sts != RefAppraisalStatus::STS_REVIEWED_NOT_CONFIRMED  && $model->appraisal_sts != RefAppraisalStatus::STS_WAIT_REVIEW) {
            FlashHandler::err('Error occured');
        } else {
            $model->appraisal_sts = RefAppraisalStatus::STS_COMPLETE;
            $model->update(false);
            FlashHandler::success('Review Confirmed');
        }
        AppraisalMain::updateStatuses($mainId);

        return $this->redirect(['index-review', 'id' => $mainId]);
    }

    public function calculateOverall($id, $type) {
        $master = AppraisalMaster::findOne($id);
        $attributeName = "overall_" . $type;
        $forms = $master->appraisalMasterForms;
        $fsmName = "final_subtotal_" . $type;

        $total = array_reduce($forms, function ($carry, $form) use ($fsmName) {
            return empty($form->$fsmName) ? false : $carry + $form->$fsmName;
        }, 0);

        if ($total !== false) {
            $master->$attributeName = $total;
            $master->update(false);
        }
    }

    public function actionRecalculateOverall($mainId) {
        $main = AppraisalMain::findOne($mainId);
        $masters = $main->appraisalMasters;
        $types = [AppraisalMaster::TYPE_RATING, AppraisalMaster::TYPE_REVIEW];

        foreach ($types as $type) {
            foreach ($masters as $master) {
                foreach ($masters as $master) {
                    $forms = $master->appraisalMasterForms;
                    foreach ($forms as $form) {
                        $this->updateAppraisalFormMark($form->id, $type);
                    }
                    $this->calculateOverall($master->id, $type);
                }
            }
        }
        return 'SUCCESS!';
    }
}
