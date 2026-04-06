<?php

namespace frontend\controllers\working;

use frontend\models\report\ReportingModel;
use Yii;
use yii\filters\VerbFilter;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyFormatter;
use frontend\models\projectproduction\task\TaskAssignment;

class HrEmployeeIncentiveController extends \yii\web\Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_SystemAdmin, AuthItem::ROLE_HR_Senior],
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

    public function actionExportToExcelDepartmentPerformanceReport() {
        $reportDetail = json_decode(Yii::$app->request->post('reportDetail'));
        $totalPendingWorkAmount = Yii::$app->request->post('totalPendingWorkAmount');
        $totalPerformanceAmount = Yii::$app->request->post('totalPerformanceAmount');
        $totalIncentiveAmount = Yii::$app->request->post('totalIncentiveAmount');
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;

        return $this->renderPartial('_factoryStaffPerformanceCSV', [
                    'reportDetail' => $reportDetail,
                    'totalPendingWorkAmount' => $totalPendingWorkAmount,
                    'totalPerformanceAmount' => $totalPerformanceAmount,
                    'totalIncentiveAmount' => $totalIncentiveAmount
        ]);
    }

//    public function actionFactoryStaffPerformanceDetail() {
//        $model = new ReportingModel();
//        $model->selectedMonth = date('m');
//        $model->selectedYear = date('Y');
//        $dateFrom = date('Y') . '-' . date('m', strtotime('-1 month')) . '-23';
//        $dateTo = date('Y') . '-' . date('m') . '-22';
//        $model->dateTo = $dateTo;
//        $model->dateFrom = MyFormatter::changeDateFormat_readToDB($dateFrom);
//        $model->is_internalProject = '';
//
//        $model->department = null;
//        $reportDetail = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, $model->department);
//
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
//            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);
//            $reportDetail = $model->calculateAllDepartmentCompleteRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, $model->department);
//            $reportDetail = $model->calculateByDepartmentAssignTaskRecord($model->dateFrom, $model->dateTo, $model->is_internalProject, $model->department);
//        }
//
//        foreach ($reportDetail as &$staff) {
//            $staff['incentiveAmount'] = $model->calculateIncentiveFactoryStaff($staff['totalPerformance']);
//        }
//
//        return $this->render('factorystaff', [
//                    'model' => $model,
//                    'reportDetail' => json_encode($reportDetail)
//        ]);
//    }
// Update the action to merge both records
    public function actionFactoryStaffPerformanceDetail() {
        $model = new ReportingModel();
        $model->selectedMonth = date('m');
        $model->selectedYear = date('Y');
        $dateFrom = date('Y') . '-' . date('m', strtotime('-1 month')) . '-23';
        $dateTo = date('Y') . '-' . date('m') . '-22';
        $model->dateTo = $dateTo;
        $model->dateFrom = MyFormatter::changeDateFormat_readToDB($dateFrom);
        $model->is_internalProject = '';
        $model->department = null;

        // Get both completed and assigned records
        $completedRecords = $model->calculateAllDepartmentCompleteRecord(
                $model->dateFrom,
                $model->dateTo,
                $model->is_internalProject,
                $model->department
        );

        $assignedRecords = $model->calculateByDepartmentPendingTaskRecord(
                $model->dateFrom,
                $model->dateTo,
                $model->is_internalProject,
                $model->department
        );

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->dateFrom = MyFormatter::changeDateFormat_readToDB($model->dateFrom);
            $model->dateTo = MyFormatter::changeDateFormat_readToDB($model->dateTo);

            $completedRecords = $model->calculateAllDepartmentCompleteRecord(
                    $model->dateFrom,
                    $model->dateTo,
                    $model->is_internalProject,
                    $model->department
            );

            $assignedRecords = $model->calculateByDepartmentPendingTaskRecord(
                    $model->dateFrom,
                    $model->dateTo,
                    $model->is_internalProject,
                    $model->department
            );
        }

        // Merge completed and assigned records
        $reportDetail = $this->mergePerformanceRecords($completedRecords, $assignedRecords);

        // Calculate incentive based on completed performance
        foreach ($reportDetail as &$staff) {
            $staff['incentiveAmount'] = $model->calculateIncentiveFactoryStaff($staff['totalPerformance']);
        }

        return $this->render('factorystaff', [
                    'model' => $model,
                    'reportDetail' => json_encode($reportDetail)
        ]);
    }

    /**
     * Merge completed and assigned records by user ID
     */
    private function mergePerformanceRecords($completedRecords, $assignedRecords) {
        $merged = [];

        // Index assigned records by user ID
        $assignedByUser = [];
        foreach ($assignedRecords as $record) {
            $userId = $record['id'];
            $assignedByUser[$userId] = [
                'staffId' => $record['staffId'],
                'fullname' => $record['fullname'],
                'totalPendingWorkAmount' => $record['totalPendingWorkAmount'] ?? 0,
            ];
        }

        // Merge with completed records
        foreach ($completedRecords as $record) {
            $userId = $record['id'];
            $merged[$userId] = [
                'id' => $userId,
                'staffId' => $record['staffId'],
                'fullname' => $record['fullname'],
                'totalPerformance' => $record['totalPerformance'] ?? 0,
                'totalPendingWorkAmount' => $assignedByUser[$userId]['totalPendingWorkAmount'] ?? 0,
            ];

            unset($assignedByUser[$userId]);
        }

        // Add remaining users who have assigned tasks but no completed tasks
        foreach ($assignedByUser as $userId => $data) {
            $merged[$userId] = [
                'id' => $userId,
                'staffId' => $data['staffId'],
                'fullname' => $data['fullname'],
                'totalPerformance' => 0,
                'totalPendingWorkAmount' => $data['totalPendingWorkAmount'],
            ];
        }

        // Sort by fullname alphabetically
        usort($merged, function ($a, $b) {
            return strcasecmp($a['fullname'], $b['fullname']);
        });

        return array_values($merged);
    }

//    private function calculateIncentiveFactoryStaff($performance): string {
//        if (!is_numeric($performance) || $performance < 0) {
//            return '0.00';
//        }
//
//        $totalIncentive = 0.00;
//        foreach (ReportingModel::FACTORY_STAFF_PERFORMANCE_INCENTIVE as $tier) {
//            if ($performance >= $tier['threshold']) {
//                $totalIncentive += $tier['incentive'];
//            }
//        }
//        return number_format($totalIncentive, 2, '.', '');
//    }
}
