<?php

namespace frontend\controllers\office;

use Yii;
use frontend\models\office\employeeHandbook\EhTravelAllowanceMaster;
use frontend\models\office\employeeHandbook\EhTravelAllowanceMasterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\office\employeeHandbook\EhTravelAllowanceDetail;
use frontend\models\RefTravelLocation;
use common\models\myTools\FlashHandler;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use common\modules\auth\models\AuthItem;
use yii\filters\AccessControl;

/**
 * EhTravelAllowanceController implements the CRUD actions for EhTravelAllowanceMaster model.
 */
class EhTravelAllowanceController extends Controller {

    public function getViewPath() {
        return Yii::getAlias('@frontend/views/eh-travel-allowance/');
    }

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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_Eh_Super],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new EhTravelAllowanceMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($ehId, $superUser) {
        $master = EhTravelAllowanceMaster::find()->where(['eh_master_id' => $ehId])->one();
        $details = [];
        if ($master !== null) {
            $details = EhTravelAllowanceDetail::find()->where(['eh_travel_allowance_master_id' => $master->id])->all();
        } else {
            $master = new EhTravelAllowanceMaster();
        }
        $locationList = RefTravelLocation::find()->orderBy(['order' => SORT_ASC])->all();
        $gradeList = \frontend\models\RefStaffGrade::find()->all();
        $dataMatrix = [];
        foreach ($details as $detail) {
            $dataMatrix[$detail->location_type][$detail->grade] = $detail->amount_per_day;
        }

        $eh = EmployeeHandbookMaster::findOne($ehId);
        return $this->render('view', [
                    'master' => $master,
                    'dataMatrix' => $dataMatrix,
                    'locationList' => $locationList,
                    'gradeList' => $gradeList,
                    'eh' => $eh,
                    'superUser' => $superUser
        ]);
    }

    public function actionCreate() {
        $model = new EhTravelAllowanceMaster();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionUpdate($ehId) {
        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post("Details");

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $master = EhTravelAllowanceMaster::findOne(['eh_master_id' => $ehId, 'eh_master_id' => $ehId]);

                if ($master === null) {
                    $master = new EhTravelAllowanceMaster();
                    $master->eh_master_id = $ehId;
                    if (!$master->save()) {
                        throw new \Exception('Failed to create master record');
                    }
                } else {
                    EhTravelAllowanceDetail::deleteAll(['eh_travel_allowance_master_id' => $master->id]);
                }

                if ($postData) {
                    foreach ($postData as $locationType => $grades) {
                        foreach ($grades as $grade => $amount) {
                            $detail = new EhTravelAllowanceDetail();
                            $detail->eh_travel_allowance_master_id = $master->id;
                            $detail->eh_master_id = $ehId;
                            $detail->grade = $grade;
                            $detail->location_type = $locationType;
                            $detail->amount_per_day = $amount;

                            if (!$detail->save()) {
                                throw new \Exception("Failed to save detail for grade {$grade}, location {$locationType}.");
                            }
                        }
                    }
                }

                // Commit the transaction if everything succeeds
                $transaction->commit();
                FlashHandler::success("Travel allowance updated successfully.");
            } catch (Exception $e) {
                // Rollback the transaction on any error
                $transaction->rollBack();
                FlashHandler::err($e->getMessage());
            }
        }

        return $this->redirect(['/office/employee-handbook/view', 'id' => $ehId]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = EhTravelAllowanceMaster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
