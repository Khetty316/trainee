<?php

namespace frontend\controllers\production;

use frontend\models\projectproduction\task\TaskAssignOngoingSummary;
use frontend\models\projectproduction\task\TaskAssignOngoingSummarySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\projectproduction\VStaffProductionAllSearch;

/**
 * TaskAssignOngoingSummaryController implements the CRUD actions for TaskAssignOngoingSummary model.
 */
class TaskAssignOngoingSummaryController extends Controller {

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
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
                ]
        );
    }

//
//    public function actionIndex() {
//        $searchModel = new TaskAssignOngoingSummarySearch();
//        $dataProvider = $searchModel->search($this->request->queryParams);
//
//        return $this->render('index', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    public function actionView($user_id) {
//        return $this->render('view', [
//                    'model' => $this->findModel($user_id),
//        ]);
//    }

    public function actionViewUserOngoingTask($userId) {
        $searchModel = new VStaffProductionAllSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, 'userOngoingTask', $userId);
        return $this->renderPartial('/projectproduction/paneltaskstatus/_ongoingTask', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

//
//    protected function findModel($user_id) {
//        if (($model = TaskAssignOngoingSummary::findOne(['user_id' => $user_id])) !== null) {
//            return $model;
//        }
//
//        throw new NotFoundHttpException('The requested page does not exist.');
//    }


    public function actionBulkUpdate() {
        $allAssignee = array_column(\common\models\User::find()->asArray()->all(), 'id');
        if (TaskAssignOngoingSummary::updateUserTaskOnHand($allAssignee)) {
            return "DONE";
        }
    }

}
