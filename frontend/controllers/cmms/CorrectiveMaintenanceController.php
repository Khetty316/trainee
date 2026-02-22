<?php

namespace frontend\controllers\cmms;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;
use common\modules\auth\models\AuthItem;

use frontend\models\cmms\CmmsCorrectiveWorkRequest;
use frontend\models\cmms\CmmsCorrectiveWorkRequestSearch;

class CorrectiveMaintenanceController extends \yii\web\Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [AuthItem::Module_CMMS]
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
    
    public function actionIndex()
    {
        $searchModel = new CmmsCorrectiveWorkRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCreateWorkRequest()
    {
        $workRequest = new CmmsCorrectiveWorkRequest();
        
        if (!$workRequest->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($workRequest->getErrors());
        }
        
        $machineBreakdownType = new \frontend\models\RefCorrectiveMachineBreakdownType();
        $machineBreakdownType->id = $workRequest->id;
        
        if (!$machineBreakdownType->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($machineBreakdownType->getErrors());
        }
        
        return $this->render('createWorkRequest', [
            'workRequest' => $workRequest,
        ]);
    }
    
    public function actionUpdateWorkRequest($id)
    {
        $workRequest = CmmsCorrectiveWorkRequest::findOne(['id' => $id]);
        
        if ($workRequest->load(\Yii::$app->request->post())) {
            if ($workRequest->validate()) {
                $postWorkRequest = \Yii::$app->request->post('CmmsCorrectiveWorkRequest');
                
                $workRequest->submitted_by = \Yii::$app->user->identity->name;
                $workRequest->machine_breakdown_type_id = $postWorkRequest['machine_breakdown_type_id'];
                
                if (!$workRequest->save(false)) {
                    FlashHandler::err($workRequest->getErrors());
                    return $this->render('updateWorkRequest', [
                        'workRequest' => $workRequest,
                    ]);
                }
            } else {
                FlashHandler::err($workRequest->getErrors());
                return $this->render('updateWorkRequest', [
                    'workRequest' => $workRequest,
                ]);
            }
        } 
        
        return $this->render('updateWorkRequest', [
           'workRequest' => $workRequest, 
        ]);
    }
}
