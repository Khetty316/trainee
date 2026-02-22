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
use frontend\models\attendance\MonthlyAttendance;
use common\models\User;
use common\models\VUser;
use common\modules\auth\models\AuthItem;
use frontend\models\common\RefUserDesignation;
use frontend\models\common\RefUserEmploymentType;
use common\models\myTools\FlashHandler;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppraisalController implements the CRUD actions for AppraisalMaster model.
 */
class AppraisalController extends Controller {

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
                        'roles' => [AuthItem::ROLE_Director, AuthItem::ROLE_HR_Senior, AuthItem::ROLE_SystemAdmin]
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
     * Lists all AppraisalMaster models.
     * @return mixed
     */
    public function actionIndex() {
//        $models = VAppraisalMaster::find()->orderBy('created_at DESC')->groupBy('main_id')->asArray()->all();
        $models= AppraisalMain::find()->orderBy('created_at DESC')->asArray()->all();
        return $this->render('index', [
                    'models' => json_encode($models),
                    'statusOptions' => RefAppraisalStatus::getDropDownListMain()
        ]);
    }

    /**
     * Lists all AppraisalMaster models.
     * @return mixed
     */
    public function actionIndexMaster($id) {
        $main = AppraisalMain::findOne($id);
        $query = VAppraisalMaster::find()->where(['main_id' => $id])->orderBy('fullname')->asArray()->all();
        $query = $this->formatData($query);

        return $this->render('indexMaster', [
                    'users' => json_encode($query),
                    'main' => $main,
                    'statusList' => RefAppraisalStatus::getDropDownListMaster(),
                    'staffTypeList' => RefUserDesignation::getDropDownListNoDirector(),
                    'statusOptions' => RefAppraisalStatus::getDropDownListMain()
        ]);
    }

    public function actionInitiateMain() {
        $main = new AppraisalMain();
        $post = Yii::$app->request->post();

        if ($main->load($post)) {
            $main->year = date('Y');
            $main->index = AppraisalMain::getMainIndex($main->year);
            $main->status = RefAppraisalStatus::STS_MAIN_IN_PROCESS;
            $main->appraisal_start_date = MyFormatter::changeDateFormat_readToDB($post['AppraisalMain']['appraisal_start_date']);
            $main->appraisal_end_date = MyFormatter::changeDateFormat_readToDB($post['AppraisalMain']['appraisal_end_date']);
            $main->rating_end_date = MyFormatter::changeDateFormat_readToDB($post['AppraisalMain']['rating_end_date']);
            if ($main->save()) {
                $this->redirect(['index-initiate',
                    'mainId' => $main->id,
                ]);
            }
        }

        return $this->renderAjax('_formMain', [
                    'model' => $main
        ]);
    }

    /**
     * Lists Yearly AppraisalMaster models.
     * @return mixed
     */
    public function actionIndexInitiate($mainId) {
        $query = VUser::getActiveStaffList();
        $main = AppraisalMain::findOne($mainId);

        $users = $this->formatData($query);
        return $this->render('indexInitiate', [
                    'main' => $main,
                    'users' => json_encode($users),
                    'employmentTypeList' => RefUserEmploymentType::getDropDownList()
        ]);
    }

    /**
     * Creates a new AppraisalMaster model, new AppraisalMasterForm models, new AppraisalMasterFactors models.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionInitiateStaffAppraisal() {
        $request = Yii::$app->request;
        $mainId = $request->post('mainId');

        $main = AppraisalMain::findOne($mainId);
        $months = MyCommonFunction::findMonthInBetween($main->appraisal_start_date, $main->appraisal_end_date);
        $years = MyCommonFunction::findYearInBetween($main->appraisal_start_date, $main->appraisal_end_date);
        $selectedIds = array_map('intval', explode(',', trim($request->post('selectedIds'), "[]")));
        $userAttendanceErr = '';
        $userAttendanceErrArr = [];

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($selectedIds as $key => $selectedId) {
                $user = User::findOne($selectedId);
                $model = AppraisalMaster::findOne(['main_id' => $mainId, 'user_id' => $selectedId]) ?? new AppraisalMaster();
                $model->main_id = $mainId;
                $model->user_id = $selectedId;
                $model->appraisal_sts = RefAppraisalStatus::STS_WAIT_RATING;
                $model->appraise_by = $selectedId;
                $model->appraise_date = null;
                $model->review_by = $user->superior_id;
                $model->review_date = null;

                if (!$model->save()) {
                    throw new \Exception('Error while processing Appraisal Master. Please contact IT Department');
                }

                foreach (RefAppraisalForm::FORM_LIST as $formId) {
                    $factors = RefAppraisalFactor::getSingleFormFactors($formId);
                    $form = new AppraisalMasterForm();
                    $form->appraisal_master_id = $model->id;
                    $form->form_id = $formId;
                    $form->fullmark = count($factors) * RefAppraisalFactor::MAX_MARK_PER_FACTOR;

                    if (!$form->save()) {
                        throw new \Exception('Error while processing Apraisal Form. Please contact IT Department');
                    }

                    foreach ($factors as $factor) {
                        $formFactor = new AppraisalMasterFactor();
                        $formFactor->appraisal_master_form_id = $form->id;
                        $formFactor->factor_id = $factor->id;

                        if (!$formFactor->save()) {
                            throw new \Exception('Error while processing Appraisal Factors. Please contact IT Department');
                        } else if (in_array($formFactor->factor_id, RefAppraisalFactor::MANUAL_PROCESSED_FACTORS)) {
                            if (!$this->processFactorManually($model->user_id, $formFactor->id, $months, $years)) {
                                $userAttendanceErrArr[] = $model->id;
                            }
                        }
                    }
                }
            }
            $transaction->commit();
            AppraisalMain::updateStatuses($mainId);

            if ($userAttendanceErrArr) {
                foreach ($userAttendanceErrArr as $masterId) {
                    $master = AppraisalMaster::findOne(['id' => $masterId]);
                    if ($master) {
                        $userAttendanceErr .= '<br/>' . $master->user->fullname;
                        $forms = AppraisalMasterForm::findAll(['appraisal_master_id' => $master->id]);
                        foreach ($forms as $form) {
                            AppraisalMasterFactor::deleteAll(['appraisal_master_form_id' => $form->id]);
                            $form->delete();
                        }
                        $master->delete();
                    }
                }
                FlashHandler::err("Staff appraisal initiated but these user/s attendance are missing for appraisal month/s." . $userAttendanceErr);
            } else {
                FlashHandler::success("Staff appraisal initiated.");
            }
            return json_encode(['success' => true, 'mainId' => $mainId]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHandler::err("Error occurred during processing.");
            return false;
        }
    }

    public function actionRecalculateManualFactor($mainId) {
        $main = AppraisalMain::findOne($mainId);
        $masters = $main->appraisalMasters;
        $months = MyCommonFunction::findMonthInBetween($main->appraisal_start_date, $main->appraisal_end_date);
        $years = MyCommonFunction::findYearInBetween($main->appraisal_start_date, $main->appraisal_end_date);
        foreach ($masters as $master) {
            $forms = $master->appraisalMasterForms;
            foreach ($forms as $form) {
                $factors = $form->appraisalMasterFactors;
                foreach ($factors as $factor) {
                    if (in_array($factor->factor_id, RefAppraisalFactor::MANUAL_PROCESSED_FACTORS)) {
                        if (!$this->processFactorManually($master->user_id, $factor->id, $months, $years)) {
                            return 'ERROR FACTOR UPDATE!';
                        }
                    }
                }
            }
        }
        $url = \yii\helpers\Url::to(['appraisalgnrl/recalculate-overall', 'mainId' => $mainId]);
        return $this->redirect($url);
    }

    public function actionViewAppraisal($id) {
        $vModel = VAppraisalMaster::findOne($id);
        $model = AppraisalMaster::findOne($id);
        $main = $model->main;
        $forms = AppraisalMasterForm::getFormsAsArray($model->id);
        $factors = [];
        foreach ($forms as $key => $form) {
            $factors[$key] = AppraisalMasterFactor::getFactorAsArray($form['id']);
        }

        return $this->render('viewAppraisalMgmt', [
                    'main' => $main,
                    'vModel' => $vModel,
                    'model' => json_encode($model),
                    'forms' => json_encode($forms),
                    'factors' => json_encode($factors),
        ]);
    }

    public function actionAppraisalAddStaff($mainId) {
        $vUserNotInAppraisal = VUser::find()
                ->select('v_user.*')
                ->leftJoin('v_appraisal_master b', 'b.user_id = v_user.id AND b.main_id = :mainId', [':mainId' => $mainId])
                ->where(['v_user.status' => User::STATUS_ACTIVE])
                ->andWhere(['or', ['<>', 'b.staff_id', ''], ['b.staff_id' => null]])
                ->andWhere(['<>', 'v_user.staff_id', "(NONE)"])
                ->andWhere(['<>', 'v_user.staff_id', '-'])
                ->andWhere(['b.id' => null])
                ->orderBy(['fullname' => SORT_ASC])
                ->asArray()
                ->all();

        $main = AppraisalMain::findOne($mainId);

        $users = $this->formatData($vUserNotInAppraisal);
        return $this->render('indexInitiate', [
                    'main' => $main,
                    'users' => json_encode($users),
                    'employmentTypeList' => RefUserEmploymentType::getDropDownList()
        ]);
    }

    public function actionAppraisalDeleteStaff($mainId) {
        $vUserInAppraisal = VUser::find()
                ->select('b.*')
                ->innerJoin('v_appraisal_master b', 'b.user_id = v_user.id AND b.main_id = :mainId', [':mainId' => $mainId])
                ->where(['v_user.status' => User::STATUS_ACTIVE])
                ->andWhere(['or', ['<>', 'b.staff_id', ''], ['b.staff_id' => null]])
                ->andWhere(['<>', 'v_user.staff_id', "(NONE)"])
                ->andWhere(['<>', 'v_user.staff_id', '-'])
                ->orderBy(['fullname' => SORT_ASC])
                ->asArray()
                ->all();

        $main = AppraisalMain::findOne($mainId);

        $users = $this->formatData($vUserInAppraisal);
        return $this->render('indexDelete', [
                    'main' => $main,
                    'users' => json_encode($users),
                    'employmentTypeList' => RefUserEmploymentType::getDropDownList()
        ]);
    }

    /**
     * Deletes AppraisalMaster model and its respective AppraisalMasterForm models and AppraisalMasterFactors models.
     * If deletion is successful, the browser will be redirected to the 'index-master' page.
     * @return boolean
     */
    public function actionDeleteStaffAppraisal() {
        $request = Yii::$app->request;
        $mainId = $request->post('mainId');
        $masterIds = array_map('intval', explode(',', trim($request->post('selectedIds'), "[]")));

        foreach ($masterIds as $masterId) {
            $master = AppraisalMaster::findOne(['id' => $masterId]);

            if ($master) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $forms = AppraisalMasterForm::findAll(['appraisal_master_id' => $master->id]);

                    foreach ($forms as $form) {
                        AppraisalMasterFactor::deleteAll(['appraisal_master_form_id' => $form->id]);
                        $form->delete();
                    }

                    $master->delete();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    FlashHandler::err($e->getMessage());
                    return json_encode(['success' => false]);
                }
            }
        }
        AppraisalMain::updateStatuses($mainId);

        FlashHandler::success("Staff/s appraisal deleted.");
        return json_encode(['success' => true, 'mainId' => $mainId]);
    }

    private function formatData($query) {
        $typeMappings = [
            'prod' => 'Production',
            'office' => 'Office',
            'exec' => 'Executive',
        ];

        foreach ($query as &$user) {
            $user['fullname'] = $user['fullname'] ? ucwords(strtolower($user['fullname'])) : $user['fullname'];
            $user['staff_type'] = $typeMappings[$user['staff_type']] ?? $user['staff_type'];
        }

        return $query;
    }

    private function processFactorManually($userId, $factorId, $months, $years) {
        $factor = AppraisalMasterFactor::findOne($factorId);

        $absent = $leaveTaken = $lateIn = $earlyOut = $totalPresent = $totalDays = $workdayOt = $totalASLeave = $result = $availWorkDays = 0;

        $monthsInYear = $this->organizeYearsMonths($years, $months);

        foreach ($monthsInYear as $year => $monthArray) {
            foreach ($monthArray as $month) {
                $monthlyAttendance = MonthlyAttendance::findOne(['user_id' => $userId, 'month' => $month, 'year' => $year]);
                if (!$monthlyAttendance) {
                    return false;
                }
                $absent += $monthlyAttendance->absent;
                $leaveTaken += $monthlyAttendance->leave_taken;
                $lateIn += $monthlyAttendance->late_in;
                $earlyOut += $monthlyAttendance->early_out;
                $totalPresent += $monthlyAttendance->total_present;
                $totalDays += $monthlyAttendance->total_days;
                $workdayOt += $monthlyAttendance->workday_ot;
                $availWorkDays += $monthlyAttendance->avail_workday;

                $totalASLeave += \frontend\models\working\leavemgmt\VLeaveMonthlySummaryGroup::find()
                        ->where(['leave_confirm_year' => $year, 'leave_confirm_month' => $month, 'user_id' => $userId])
                        ->sum('days');
            }
        }

        if ($factor->factor_id == RefAppraisalFactor::FACTOR_ATTENDANCE) {
            $result = round(($totalPresent / ($availWorkDays - $totalASLeave)) * 5);
        }
        if ($factor->factor_id == RefAppraisalFactor::FACTOR_PUNCTUALITY) {
            $sum = round(5 - ((($absent + $leaveTaken + $lateIn + $earlyOut - $totalASLeave) / ($availWorkDays - $totalASLeave)) * 5));
            $result = $sum < 0.5 ? 1 : $sum;
        }
        if ($factor->factor_id == RefAppraisalFactor::FACTOR_OVERTIME) {
            $ot = $workdayOt / ($availWorkDays * 8);

            if ($ot >= 0.2) {
                $result = 5;
            } elseif ($ot >= 0.15) {
                $result = 4;
            } elseif ($ot >= 0.1) {
                $result = 3;
            } elseif ($ot >= 0.05) {
                $result = 2;
            } else {
                $result = 1;
            }
        }

        $factor->rating = $result;
        $factor->review = $result;

        return $factor->update(false);
    }

    private function organizeYearsMonths($years, $months) {
        $result = [];

        // If there's only one year, assign all months to it
        if (count($years) == 1) {
            $result[$years[0]] = $months;
        } else {
            $currentYear = $years[0];
            $result[$currentYear] = [];

            foreach ($months as $month) {
                // Check if month is in the range 12 to 01
                if ($month == 1 && end($result[$currentYear]) == 12) {
                    $currentYear++;
                    $result[$currentYear] = [];
                }

                $result[$currentYear][] = $month;
            }
        }

        return $result;
    }

    public function actionExportToCsv($mainId) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $model = AppraisalMain::findOne($mainId);
        $master = VAppraisalMaster::find()->where(['main_id' => $mainId])->orderBy(['fullname' => SORT_ASC])->all();
        return $this->renderPartial('_appraisalSummaryCSV', [
                    'model' => $model,
                    'masters' => $master,
        ]);
    }

}
