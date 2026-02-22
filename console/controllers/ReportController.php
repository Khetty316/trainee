<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use DateTime;
use yii\db\Query;
use frontend\models\projectproduction\task\TaskAssignment;
use common\models\FactoryStaffPerformanceReports;

class ReportController extends Controller {

    /**
     * Main entry for cron
     * Example: php yii report/generate factorystaffperformance
     */
    public function actionGenerate($type = 'factorystaffperformance') {
        switch ($type) {
            case 'factorystaffperformance':
                $this->actionFactoryStaffPerformance();
                break;

//            case 'OfficeStaffPerformance':
//                $this->actionOfficeStaffPerformance(); 
//                break;
//
//            default:
//                $this->stdout("Unknown report type: {$type}\n");
//                return Controller::EXIT_CODE_ERROR;
        }

        $this->stdout("Cron job for {$type} generated successfully.\n");
        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionFactoryStaffPerformance() {
        $startTime = microtime(true);
        date_default_timezone_set('Asia/Kuching');
        echo "=== Starting performance report generation at " . date('Y-m-d H:i:s') . " ===\n";

        try {
            $periods = $this->generateMonthlyPeriods();
            $successCount = 0;
            $errorCount = 0;

            foreach ($periods as $periodKey => $period) {
                echo "Processing period: {$periodKey} ({$period['dateFrom']} to {$period['dateTo']})\n";

                $projectTypes = ['' => 'all', '1' => 'internal', '0' => 'external'];

                foreach ($projectTypes as $isInternal => $suffix) {
                    try {
                        $this->calculateAndStoreReports(
                                $period['dateFrom'],
                                $period['dateTo'],
                                $isInternal,
                                $periodKey,
                                $suffix
                        );

                        $successCount++;
                        echo "  ✓ {$periodKey} - {$suffix} completed\n";
                    } catch (\Exception $e) {
                        $errorCount++;
                        echo "  ✗ {$periodKey} - {$suffix} failed: " . $e->getMessage() . "\n";
                        Yii::error("Performance report failed for {$periodKey} - {$suffix}: " . $e->getMessage());
                    }
                }
            }

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            echo "=== Job Completed ===\n";
            echo "Success: {$successCount}, Errors: {$errorCount}\n";
            echo "Execution time: {$executionTime} seconds\n";

            Yii::info("Performance reports job completed. Success: {$successCount}, Errors: {$errorCount}, Time: {$executionTime}s");
        } catch (\Exception $e) {
            Yii::error('Performance report job failed: ' . $e->getMessage());
            throw $e;
        }
    }

//    private function generateMonthlyPeriodsAll() {
//        $periods = [];
//
//        $periodStart = new DateTime('2020-12-23');
//        $periodEnd = new DateTime('2021-01-22');
//
//        $periodKey = "month_2020_12";
//        $periods[$periodKey] = [
//            'dateFrom' => $periodStart->format('Y-m-d'),
//            'dateTo' => $periodEnd->format('Y-m-d'),
//            'description' => $periodStart->format('M Y') . ' (' . $periodStart->format('d/m/Y') . ' to ' . $periodEnd->format('d/m/Y') . ')'
//        ];
//
//        return $periods;
//    }
//    private function generateMonthlyPeriods()
//    {
//        $periods = [];
//        $startYear = 2020;
//        $startMonth = 12;
//        $startDay = 23;
//
//        $currentDate = new DateTime();
//        $currentYear = (int) $currentDate->format('Y');
//        $currentMonth = (int) $currentDate->format('n');
//
//        $year = $startYear;
//        $month = $startMonth;
//        $day = $startDay;
//
//        while ($year < $currentYear || ($year == $currentYear && $month <= $currentMonth)) {
//            $periodStart = new DateTime();
//            $periodStart->setDate($year, $month, $day);
//
//            $nextMonth = $month + 1;
//            $nextYear = $year;
//            if ($nextMonth > 12) {
//                $nextMonth = 1;
//                $nextYear++;
//            }
//
//            $periodEnd = new DateTime();
//            $periodEnd->setDate($nextYear, $nextMonth, $day - 1);
//
//            if ($periodEnd > $currentDate) {
//                $periodEnd = $currentDate;
//            }
//
//            $periodKey = sprintf("month_%04d_%02d", $year, $month);
//
//            $periods[$periodKey] = [
//                'dateFrom' => $periodStart->format('Y-m-d'),
//                'dateTo' => $periodEnd->format('Y-m-d'),
//                'description' => $periodStart->format('M Y') . ' (' . $periodStart->format('d/m/Y') . ' to ' . $periodEnd->format('d/m/Y') . ')'
//            ];
//
//            $month++;
//            if ($month > 12) {
//                $month = 1;
//                $year++;
//            }
//
//            if ($periodStart > $currentDate) {
//                break;
//            }
//        }
//
//        return $periods;
//    }

    private function generateMonthlyPeriods() {
        $periods = [];
        $startYear = 2020;
        $startMonth = 12;
        $dayFrom = 23;
        $dayTo = 22;

        $currentDate = new DateTime();
        $currentYear = (int) $currentDate->format('Y');
        $currentMonth = (int) $currentDate->format('n');
        $currentDay = (int) $currentDate->format('j');

        $year = $startYear;
        $month = $startMonth;

        // If current day is before the 23rd, we're still in the previous period
        // So we need to generate up to current month
        // If current day is 23rd or after, we're in the current period
        $lastMonth = $currentMonth;

        while ($year < $currentYear || ($year == $currentYear && $month <= $lastMonth)) {
            $periodStart = new DateTime();
            $periodStart->setDate($year, $month, $dayFrom);

            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }

            // Period always ends on the 22nd of next month (no cutting)
            $periodEnd = new DateTime();
            $periodEnd->setDate($nextYear, $nextMonth, $dayTo);

            $periodKey = sprintf("month_%04d_%02d", $nextYear, $nextMonth);
            $periods[$periodKey] = [
                'dateFrom' => $periodStart->format('Y-m-d'),
                'dateTo' => $periodEnd->format('Y-m-d'),
                'description' => $periodStart->format('M Y') . ' (' .
                $periodStart->format('d/m/Y') . ' to ' .
                $periodEnd->format('d/m/Y') . ')'
            ];

            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        return $periods;
    }

    private function calculateAndStoreReports($dateFrom, $dateTo, $isInternal, $periodKey, $internalSuffix) {
        $fabOverall = $this->getTopPerformanceFactoryStaffOverall($dateFrom, $dateTo, $isInternal, TaskAssignment::taskTypeFabrication);
        $fabByTask = $this->getTopPerformanceFactoryStaffByTaskCode($dateFrom, $dateTo, $isInternal, TaskAssignment::taskTypeFabrication);
        $elecOverall = $this->getTopPerformanceFactoryStaffOverall($dateFrom, $dateTo, $isInternal, TaskAssignment::taskTypeElectrical);
        $elecByTask = $this->getTopPerformanceFactoryStaffByTaskCode($dateFrom, $dateTo, $isInternal, TaskAssignment::taskTypeElectrical);

        $cacheKey = "performance_report_{$periodKey}_{$internalSuffix}";

        $reportData = [
            'topFabStaffOverall' => $fabOverall,
            'topFabricationStaffByTask' => $fabByTask,
            'topElecStaffOverall' => $elecOverall,
            'topElectricalStaffByTask' => $elecByTask,
            'generated_at' => date('Y-m-d H:i:s'),
            'period' => $periodKey,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'isInternal' => $isInternal
        ];

        $this->storeInDatabase($cacheKey, $reportData, $periodKey, $dateFrom, $dateTo, $isInternal);
    }

    private function storeInDatabase($cacheKey, $reportData, $periodType, $dateFrom, $dateTo, $isInternal) {
        $cache = FactoryStaffPerformanceReports::find()
                ->where(['cache_key' => $cacheKey])
                ->one();

        if (!$cache) {
            $cache = new FactoryStaffPerformanceReports();
            $cache->cache_key = $cacheKey;
        }

        $cache->period_type = $periodType;
        $cache->date_from = $dateFrom;
        $cache->date_to = $dateTo;
        $cache->is_internal_project = $isInternal;
        $cache->setReportData($reportData);

        if (!$cache->save()) {
            throw new \Exception('Failed to save cache: ' . json_encode($cache->errors));
        }
    }

    private function getTopPerformanceFactoryStaffOverall($dateFrom, $dateTo, $isInternal, $department) {
        $model = new \frontend\models\report\ReportingModel();
        $reportDetail = $this->calculateAllDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department);

        if (empty($reportDetail)) {
            return [];
        }

        $totalSum = array_sum(array_column($reportDetail, 'totalPerformance'));

        usort($reportDetail, function ($a, $b) {
            return floatval($b['totalPerformance']) <=> floatval($a['totalPerformance']);
        });

        $result = array_slice($reportDetail, 0, 3);

        foreach ($result as $index => &$staff) {
            $staff['rank'] = $index + 1;
            $staff['percentage'] = ($totalSum > 0) ? round((floatval($staff['totalPerformance']) / $totalSum) * 100, 4) : 0;
            $staff['formattedValue'] = number_format(floatval($staff['totalPerformance']), 4);
        }

        return $result;
    }

    private function calculateAllDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department) {
        echo "\n isInternal = $isInternal\n";
        // Helper function for reuse
        $buildQuery = function ($view, $taskCode, $refTable, $department) use ($dateFrom, $dateTo, $isInternal) {
            $query = (new \yii\db\Query())
                    ->select([
                        'worker_task_categories.user_id AS id',
                        'MAX(user.staff_id) AS staffId',
                        'MAX(user.fullname) AS fullname',
                        'ROUND(COALESCE(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 0), 4) AS totalPerformance'
                    ])
                    ->from('worker_task_categories')
                    ->leftJoin('user', 'user.id = worker_task_categories.user_id')
                    ->leftJoin("$view v",
                            "v.user_id = worker_task_categories.user_id 
                AND v.$taskCode = worker_task_categories.task_code
                AND complete_date BETWEEN :dateFrom AND :dateTo"
                    )
                    ->leftJoin("$refTable ref", "ref.code = v.$taskCode AND ref.active_sts = 1")
                    ->where(['worker_task_categories.task_type' => $department])
                    ->andWhere(['!=', 'user.employment_type', 'contract'])
                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                    ->groupBy(['worker_task_categories.user_id'])
                    ->addParams([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);

//            if ($isInternal !== '') {
//                $query->addParams([':internalProject' => $isInternal]);
//            }

            if ($isInternal == '0' || $isInternal == '1') {
                $query->andWhere(['internal_project' => $isInternal]);
            } else {
                $query->andWhere(['internal_project' => [0, 1]]);
            }

            return $query;
        };

        // CASE 1: If department is specified — run normally
        if (!empty($department)) {
            $isFab = $department == TaskAssignment::taskTypeFabrication;

            $view = $isFab ? 'v_staff_competed_panel_record_fabrication' : 'v_staff_competed_panel_record_electrical';
            $taskCode = $isFab ? 'fab_task_code' : 'elec_task_code';
            $refTable = $isFab ? 'ref_proj_prod_task_fab' : 'ref_proj_prod_task_elec';

            $result = $buildQuery($view, $taskCode, $refTable, $department)->all();

            // Sort alphabetically by fullname (A → Z)
            usort($result, function ($a, $b) {
                return strcasecmp($a['fullname'], $b['fullname']);
            });

            return $result;
        }


        // CASE 2: If department is NULL — combine fabrication + electrical
        $fabQuery = $buildQuery(
                'v_staff_competed_panel_record_fabrication',
                'fab_task_code',
                'ref_proj_prod_task_fab',
                TaskAssignment::taskTypeFabrication
                )->all();

        $elecQuery = $buildQuery(
                'v_staff_competed_panel_record_electrical',
                'elec_task_code',
                'ref_proj_prod_task_elec',
                TaskAssignment::taskTypeElectrical
                )->all();

        // Combine both results
        $combined = [];

        foreach (array_merge($fabQuery, $elecQuery) as $row) {
            $id = $row['id'];
            if (!isset($combined[$id])) {
                $combined[$id] = $row;
            } else {
                $combined[$id]['totalPerformance'] += $row['totalPerformance'];
            }
        }

        // ✅ Sort by fullname alphabetically (A → Z)
        usort($combined, function ($a, $b) {
            return strcasecmp($a['fullname'], $b['fullname']);
        });

        return array_values($combined);
    }

    //exclude inactive task, show all worker (active)
    private function calculateByDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department) {
        $query = new Query();

        $isFab = $department == TaskAssignment::taskTypeFabrication;
        $view = $isFab ? 'v_staff_competed_panel_record_fabrication' : 'v_staff_competed_panel_record_electrical';
        $taskCode = $isFab ? 'fab_task_code' : 'elec_task_code';
        $refTable = $isFab ? 'ref_proj_prod_task_fab' : 'ref_proj_prod_task_elec';

        // JOIN without any internal filter inside
        $query->select([
                    'worker_task_categories.user_id AS id',
                    'MAX(user.staff_id) AS staffId',
                    'MAX(user.fullname) AS fullname',
                    "worker_task_categories.task_code AS {$taskCode}",
                    'ROUND(COALESCE(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel 
                * panel_type_weight / 100 * single_task_weight / 100), 0), 4) AS totalPerformance'
                ])
                ->from('worker_task_categories')
                ->leftJoin('user', 'user.id = worker_task_categories.user_id')
                ->leftJoin("$view v",
                        "v.user_id = worker_task_categories.user_id 
             AND v.$taskCode = worker_task_categories.task_code
             AND complete_date BETWEEN :dateFrom AND :dateTo"
                )
                ->leftJoin("$refTable ref", "ref.code = v.$taskCode AND ref.active_sts = 1")
                ->where(['worker_task_categories.task_type' => $department])
                ->andWhere(['!=', 'user.employment_type', 'contract'])
                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                ->groupBy(['worker_task_categories.user_id', 'worker_task_categories.task_code'])
                ->orderBy(['totalPerformance' => SORT_DESC])
                ->addParams([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);

        // ONLY filter if user selected 0 or 1
        if ($isInternal == '0' || $isInternal == '1') {
            $query->andWhere(['v.internal_project' => $isInternal]);
        } else {
            $query->andWhere(['v.internal_project' => [0, 1]]);
        }

        return $query->all();
    }

    private function getTopPerformanceFactoryStaffByTaskCode($dateFrom, $dateTo, $isInternal, $department) {
        $reportDetail = $this->calculateByDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department);

        if (empty($reportDetail)) {
            return [];
        }

        $taskCodeColumn = $department == TaskAssignment::taskTypeFabrication ? 'fab_task_code' : 'elec_task_code';
        $groupedByTaskCode = [];

        foreach ($reportDetail as $staff) {
            $taskCode = $staff[$taskCodeColumn];
            if (!isset($groupedByTaskCode[$taskCode])) {
                $groupedByTaskCode[$taskCode] = [];
            }
            $groupedByTaskCode[$taskCode][] = $staff;
        }

        $result = [];
        foreach ($groupedByTaskCode as $taskCode => $staffList) {
            $totalPerformanceSum = array_sum(array_column($staffList, 'totalPerformance'));

            usort($staffList, function ($a, $b) {
                return $b['totalPerformance'] <=> $a['totalPerformance'];
            });

            foreach ($staffList as &$staff) {
                $staff['percentage'] = $totalPerformanceSum > 0 ? round(($staff['totalPerformance'] / $totalPerformanceSum) * 100, 4) : 0;
                $staff['amount'] = $staff['totalPerformance'];
            }

            $result[$taskCode] = $staffList;
        }

        return $result;
    }
}
