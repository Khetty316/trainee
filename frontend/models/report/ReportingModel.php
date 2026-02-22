<?php

namespace frontend\models\report;

use Yii;
use yii\base\Model;
use common\models\myTools\MyFormatter;
use frontend\models\projectproduction\fabrication\ProdFabTaskWeight;
use frontend\models\projectproduction\electrical\ProdElecTaskWeight;
use frontend\models\ProjectProduction\ProjectProductionMaster;
use frontend\models\projectquotation\VProjectQuotationMaster;
use frontend\models\projectproduction\VStaffCompetedPanelRecordElectrical;
use frontend\models\projectproduction\VStaffCompetedPanelRecordFabrication;
use frontend\models\projectproduction\task\TaskAssignment;
use yii\db\Query;
use frontend\models\projectproduction\electrical\VElecTasksMasterlist;
use frontend\models\projectproduction\fabrication\VFabTasksMasterlist;

class ReportingModel extends Model {

    public $dateFrom;
    public $dateTo;
    public $userId;
    public $department;
    public $is_internalProject;
    public $monthCutOff;
    public $selectedMonth;
    public $selectedYear;

    CONST FACTORY_STAFF_PERFORMANCE_INCENTIVE = [
        ['threshold' => 20000, 'incentive' => 100],
        ['threshold' => 40000, 'incentive' => 150]
    ];
    CONST PROJECT_TYPE_OPTIONS = [
        '' => 'All Projects',
        '1' => 'Internal Projects',
        '0' => 'External Projects'
    ];

    public function rules() {
        return [
            [['dateFrom', 'dateTo'], 'string'],
            [['dateFrom', 'dateTo'], 'required'],
            [['dateFrom'], 'validateDates'],
            [['userId', 'department'], 'string'],
            [['is_internalProject', 'monthCutOff', 'selectedMonth', 'selectedYear'], 'safe']
        ];
    }

    public function validateDates() {
        $dateForm = MyFormatter::changeDateFormat_readToDB($this->dateFrom);
        $dateTo = MyFormatter::changeDateFormat_readToDB($this->dateTo);
        if (strtotime($dateTo) < strtotime($dateForm)) {
            $this->addError('dateTo', 'End date must be later than Date From');
        }
    }

    public function attributeLabels() {
        return [
            'dateFrom' => 'Date From',
            'dateTo' => 'Date To',
        ];
    }

    public function beforeValidate() {
        if (!empty($this->dateFrom) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->dateFrom)) {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $this->dateFrom);
            if ($dateObj !== false) {
                $this->dateFrom = $dateObj->format('Y-m-d');
            } else {
                $this->addError('dateFrom', 'Invalid Date Format. Expected format: dd/mm/yyyy');
            }
        }

        if (!empty($this->dateTo) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->dateTo)) {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $this->dateTo);
            if ($dateObj !== false) {
                $this->dateTo = $dateObj->format('Y-m-d');
            } else {
                $this->addError('dateTo', 'Invalid Date Format. Expected format: dd/mm/yyyy');
            }
        }

        return parent::beforeValidate();
    }

    /**
     * by Khetty, 13/5/2024
     * Calculates the performance amount for each task based on completion quantity, total quantity, total staff assigned, task weight, department weight, and amount.
     * @return array The updated $fabPerformanceList/$elecPerformanceList with the 'performanceAmount' calculated for each task.
     */
    /* No longer using, commented by Paul @ 10/03/2025
     * public function getFabPerformanceAmount($fabPerformanceList) {
      $performanceAmount = 0;
      foreach ($fabPerformanceList as $key => $data) {
      $fabTaskWeightModel = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $data['proj_prod_panel_id']])->one();
      if ($fabTaskWeightModel) {
      $fabTaskWeight = $fabTaskWeightModel->{$data["task_code"]};
      $performanceAmount = ($data["completing_qty"] / $data["quantity"] / $data["total_staff_assigned"] * $fabTaskWeight / 100 * $data["panel_weight"] / 100 * $data["amount"]);
      }
      $fabPerformanceList[$key]['performanceAmount'] = $performanceAmount;
      }
      return $fabPerformanceList;
      }
     */

    /* No longer using, commented by Paul @ 10/03/2025
      public function getElecPerformanceAmount($elecPerformanceList) {
      $performanceAmount = 0;
      foreach ($elecPerformanceList as $key => $data) {
      $elecTaskWeightModel = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $data['proj_prod_panel_id']])->one();
      if ($elecTaskWeightModel) {
      $elecTaskWeight = $elecTaskWeightModel->{$data["task_code"]};
      $performanceAmount = ($data["completing_qty"] / $data["quantity"] / $data["total_staff_assigned"] * $elecTaskWeight / 100 * $data["panel_weight"] / 100 * $data["amount"]);
      }
      $elecPerformanceList[$key]['performanceAmount'] = $performanceAmount;
      }

      return $elecPerformanceList;
      }
     */
    public function getTotalAmountTaskFab($fabTaskList) {
        $totalAmount = 0;
        foreach ($fabTaskList as $key => $data) {
            $fabTaskWeightModel = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $data['proj_prod_panel_id']])->one();
            if ($fabTaskWeightModel) {
                $fabTaskWeight = $fabTaskWeightModel->{$data["task_code"]};
                $amount = (1 / $data["total_staff_assigned"] * $fabTaskWeight / 100 * $data["panel_weight"] / 100 * $data["amount"]);
                $totalAmount += $amount;
            }
        }

        return $totalAmount;
    }

    public function getTotalAmountTaskElec($elecTaskList) {
        $totalAmount = 0;
        foreach ($elecTaskList as $key => $data) {
            $elecTaskWeightModel = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $data['proj_prod_panel_id']])->one();
            if ($elecTaskWeightModel) {
                $elecTaskWeight = $elecTaskWeightModel->{$data["task_code"]};
                $amount = (1 / $data["total_staff_assigned"] * $elecTaskWeight / 100 * $data["panel_weight"] / 100 * $data["amount"]);
                $totalAmount += $amount;
            }
        }

        return $totalAmount;
    }

    /**
     * by Khetty, 14/5/2024
     * Sums the performance amounts and merge the data based on matching proj_prod_panel_id.
     * @return array The combined data summary with proj_prod_panel_id as keys and summed performance amounts for each panel.
     */
    /* No longer using, commented by Paul @ 10/03/2025
      public function getIndividuReportSummary($details) {
      $combinedData = [];
      foreach ($details as $detail) {
      $panelId = $detail["proj_prod_panel_id"];
      if (!isset($combinedData[$panelId])) {
      $combinedData[$panelId] = [
      "user_id" => $detail["user_id"],
      "project_production_code" => $detail["project_production_code"],
      "project_name" => $detail["project_name"],
      "project_production_panel_code" => $detail["project_production_panel_code"],
      "panel_description" => $detail["panel_description"],
      "performanceAmount" => $detail["performanceAmount"]
      ];
      } else {
      $combinedData[$panelId]["performanceAmount"] += $detail["performanceAmount"];
      }
      }
      return $combinedData;
      }

      public function getTaskCompletionAmount($totalAmountTask, $reportDetail, $departmentName) {
      $amount = 0;
      foreach ($reportDetail[$departmentName] as $item) {
      $amount += round($item['performanceAmount'], 2);
      }
      $percentage = $amount / $totalAmountTask * 100;

      return [
      'amount' => $amount,
      'percentage' => $percentage
      ];
      }
     */
    /*
     * Sums the performance amounts and merge the data based on matching user ID.
     * @return array The combined data summary with user_id as key and total performance for each user.
     */

    public function getDepartmentReportSummary($details) {
        $combinedData = [];
        foreach ($details as $detail) {
            $userId = $detail["id"];
            if (!isset($combinedData[$userId])) {
                $combinedData[$userId] = [
                    "id" => $detail["id"],
                    "fullname" => $detail["fullname"],
                    "staffId" => $detail["staffId"],
                    "totalPerformance" => $detail["performanceAmount"]
                ];
            } else {
                $combinedData[$userId]["totalPerformance"] += $detail["performanceAmount"];
            }
        }
        return array_shift($combinedData);
    }

    /**
     * Initialize empty chart data structure
     */
    public function initializeChartData() {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'data' => [],
                    'backgroundColor' => [],
                ]
            ]
        ];
    }

    public function getQuotationDoneByProjectCoordinator($dateFrom, $dateTo, $id) {
        return VProjectQuotationMaster::find()
                        ->where(['between', 'v_project_quotation_master.created_at', $dateFrom . " 00:00:00", $dateTo . " 23:59:59"])
                        ->andWhere(['project_coordinator' => $id])
                        ->andWhere(['active' => 1])
                        ->count();
    }

    public function getQuotationDoneAllProjectCoordinator($dateFrom, $dateTo) {
        return VProjectQuotationMaster::find()
                        ->where(['between', 'v_project_quotation_master.created_at', $dateFrom . " 00:00:00", $dateTo . " 23:59:59"])
                        ->andWhere(['active' => 1])
                        ->count();
    }

    public function getQuotationHitsByProjectCoordinator($dateFrom, $dateTo, $id) {
        return VProjectQuotationMaster::find()
                        ->where(['between', 'v_project_quotation_master.created_at', $dateFrom . " 00:00:00", $dateTo . " 23:59:59"])
                        ->andWhere(['project_coordinator' => $id])
                        ->andWhere(['is_finalized' => 1])
                        ->andWhere(['active' => 1])
                        ->count();
    }

    public function getTasksCompletionByProjectCoordinator($dateFrom, $dateTo, $id) {
        // Get the sum of fab_complete_percent and elec_complete_percent separately
        $completionData = ProjectProductionMaster::find()
                ->select([
                    'SUM(fab_complete_percent) as sum_fab_percent',
                    'SUM(elec_complete_percent) as sum_elec_percent',
                    'COUNT(*) as row_count'
                ])
                ->where(['between', 'project_production_master.created_at', $dateFrom . " 00:00:00", $dateTo . " 23:59:59"])
                ->andWhere(['created_by' => $id])
                ->asArray()
                ->one();

        // If no data, default to 0 for the sums
        $sumFab = $completionData['sum_fab_percent'] ?? 0;
        $sumElec = $completionData['sum_elec_percent'] ?? 0;
        $rowCount = $completionData['row_count'] ?? 0;

        // Calculate the combined average
        $totalSum = $sumFab + $sumElec;
        $totalDataPoints = $rowCount * 2;  // Since there are two values per row (fab and elec)
        $combinedAverage = $totalDataPoints > 0 ? $totalSum / $totalDataPoints : 0;

        return [
            'combinedAverage' => $combinedAverage,
            'rowCount' => $rowCount
        ];
    }

    public function preparePieChartData($labelA, $labelB, $dataA, $dataB, $bgA, $bgB) {
        return [
            'labels' => [$labelA, $labelB],
            'datasets' => [
                [
                    'data' => [$dataA, $dataB],
                    'backgroundColor' => [$bgA, $bgB],
                ]
            ]
        ];
    }

//    public function calculatePersonalCompleteRecord($model, $department) {
//        $query = new Query();
//
//        if ($department == TaskAssignment::taskTypeFabrication) {
//            $query->select([
//                        'v_staff_competed_panel_record_fabrication.*',
//                        'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
//                    ])
//                    ->from('v_staff_competed_panel_record_fabrication')
//                    ->innerJoin('user', 'user.id = v_staff_competed_panel_record_fabrication.user_id')
//                    ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
//                    ->andWhere(['v_staff_competed_panel_record_fabrication.user_id' => $model->userId])
//                    ->andWhere(['!=', 'user.employment_type', 'contract'])
//                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE]);
//
//            if ($model->is_internalProject !== "") {
//                $query->andWhere(['v_staff_competed_panel_record_fabrication.internal_project' => $model->is_internalProject]);
//            }
//
//            return $query->all();
//        }
//
//        if ($department == TaskAssignment::taskTypeElectrical) {
//            $query->select([
//                        'v_staff_competed_panel_record_electrical.*',
//                        'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
//                    ])
//                    ->from('v_staff_competed_panel_record_electrical')
//                    ->innerJoin('user', 'user.id = v_staff_competed_panel_record_electrical.user_id')
//                    ->where(['between', 'complete_date', $model->dateFrom, $model->dateTo])
//                    ->andWhere(['v_staff_competed_panel_record_electrical.user_id' => $model->userId])
//                    ->andWhere(['!=', 'user.employment_type', 'contract'])
//                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE]);
//
//            if ($model->is_internalProject !== "") {
//                $query->andWhere(['v_staff_competed_panel_record_electrical.internal_project' => $model->is_internalProject]);
//            }
//
//            return $query->all();
//        }
//    }

    public function calculatePersonalCompleteRecord($model, $department) {
        $query = new Query();

        if ($department == TaskAssignment::taskTypeFabrication) {
            $query->select([
                        'v_staff_competed_panel_record_fabrication.*',
//                'ROUND((amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 2) AS performanceAmount'
                        'ROUND(COALESCE(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 4) AS performanceAmount'
                    ])
                    ->from('worker_task_categories')
                    ->innerJoin('user', 'user.id = worker_task_categories.user_id')
                    ->leftJoin(
                            'v_staff_competed_panel_record_fabrication',
                            'v_staff_competed_panel_record_fabrication.user_id = worker_task_categories.user_id 
                AND v_staff_competed_panel_record_fabrication.fab_task_code = worker_task_categories.task_code
                AND complete_date BETWEEN :dateFrom AND :dateTo' .
                            ($model->is_internalProject !== '' ? ' AND v_staff_competed_panel_record_fabrication.internal_project = :internalProject' : '')
                    )
                    ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = v_staff_competed_panel_record_fabrication.fab_task_code')
                    ->where([
                        'worker_task_categories.task_type' => TaskAssignment::taskTypeFabrication,
                        'ref.active_sts' => 1,
                        'worker_task_categories.user_id' => $model->userId
                    ])
                    ->andWhere(['!=', 'user.employment_type', 'contract'])
                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                    ->addParams([':dateFrom' => $model->dateFrom, ':dateTo' => $model->dateTo]);

            if ($model->is_internalProject !== '') {
                $query->addParams([':internalProject' => $model->is_internalProject]);
            }

            return $query->all();
        }

        if ($department == TaskAssignment::taskTypeElectrical) {
            $query->select([
                        'v_staff_competed_panel_record_electrical.*',
                        'ROUND(COALESCE(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 4) AS performanceAmount'
                    ])
                    ->from('worker_task_categories')
                    ->innerJoin('user', 'user.id = worker_task_categories.user_id')
                    ->leftJoin(
                            'v_staff_competed_panel_record_electrical',
                            'v_staff_competed_panel_record_electrical.user_id = worker_task_categories.user_id 
                AND v_staff_competed_panel_record_electrical.elec_task_code = worker_task_categories.task_code
                AND complete_date BETWEEN :dateFrom AND :dateTo' .
                            ($model->is_internalProject !== '' ? ' AND v_staff_competed_panel_record_electrical.internal_project = :internalProject' : '')
                    )
                    ->leftJoin('ref_proj_prod_task_elec ref', 'ref.code = v_staff_competed_panel_record_electrical.elec_task_code')
                    ->where([
                        'worker_task_categories.task_type' => TaskAssignment::taskTypeElectrical,
                        'ref.active_sts' => 1,
                        'worker_task_categories.user_id' => $model->userId
                    ])
                    ->andWhere(['!=', 'user.employment_type', 'contract'])
                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                    ->addParams([':dateFrom' => $model->dateFrom, ':dateTo' => $model->dateTo]);

            if ($model->is_internalProject !== '') {
                $query->addParams([':internalProject' => $model->is_internalProject]);
            }

            return $query->all();
        }
    }

    //excluded inactive staff and contract employement type and inactive task
    // run-cront.bat, hr - incentive, report - performance
    // show all worker from worker table
    public function calculateAllDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department) {
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
                AND complete_date BETWEEN :dateFrom AND :dateTo" .
                            ($isInternal !== '' ? ' AND v.internal_project = :internalProject' : '')
                    )
                    ->leftJoin("$refTable ref", "ref.code = v.$taskCode AND ref.active_sts = 1")
                    ->where(['worker_task_categories.task_type' => $department])
                    ->andWhere(['!=', 'user.employment_type', 'contract'])
                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                    ->groupBy(['worker_task_categories.user_id'])
                    ->addParams([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);

            if ($isInternal !== '') {
                $query->addParams([':internalProject' => $isInternal]);
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

//    public function calculateAllDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department) {
//        $query = new Query();
//
//        $isFab = $department == TaskAssignment::taskTypeFabrication;
//
//        // Choose table/view names dynamically
//        $view = $isFab ? 'v_staff_competed_panel_record_fabrication' : 'v_staff_competed_panel_record_electrical';
//        $taskCode = $isFab ? 'fab_task_code' : 'elec_task_code';
//        $refTable = $isFab ? 'ref_proj_prod_task_fab' : 'ref_proj_prod_task_elec';
//
//        $query->select([
//                    'worker_task_categories.user_id AS id',
//                    'MAX(user.staff_id) AS staffId',
//                    'MAX(user.fullname) AS fullname',
//                    // If no record exists, totalPerformance = 0.0000
//                    'ROUND(COALESCE(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 0), 4) AS totalPerformance'
//                ])
//                ->from('worker_task_categories')
//                ->leftJoin('user', 'user.id = worker_task_categories.user_id')
//                ->leftJoin("$view v",
//                        "v.user_id = worker_task_categories.user_id 
//            AND v.$taskCode = worker_task_categories.task_code
//            AND complete_date BETWEEN :dateFrom AND :dateTo" .
//                        ($isInternal !== '' ? ' AND v.internal_project = :internalProject' : '')
//                )
//                ->leftJoin("$refTable ref", "ref.code = v.$taskCode AND ref.active_sts = 1")
//                ->where(['worker_task_categories.task_type' => $department])
//                // Move ref.active_sts filter into JOIN instead of WHERE 
//                // so workers without tasks are not excluded
//                ->andWhere(['!=', 'user.employment_type', 'contract'])
//                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
//                ->groupBy(['worker_task_categories.user_id'])
//                ->addParams([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);
//
//        if ($isInternal !== '') {
//            $query->addParams([':internalProject' => $isInternal]);
//        }
//
//        return $query->all();
//    }
    // show only who completed task
//    public function calculateAllDepartmentCompleteRecord($dateFrom, $dateTo, $isInternal, $department) {
//        $query = new Query();
//        if ($department == TaskAssignment::taskTypeFabrication) {
//            $query->select([
//                        'worker_task_categories.user_id as id',
//                        'MAX(user.staff_id) as staffId',
//                        'MAX(user.fullname) as fullname',
//                        'ROUND(COALESCE(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 0), 4) AS totalPerformance'
//                    ])
//                    ->from('worker_task_categories')
//                    ->leftJoin('user', 'user.id = worker_task_categories.user_id')
//                    ->leftJoin('v_staff_competed_panel_record_fabrication',
//                            'v_staff_competed_panel_record_fabrication.user_id = worker_task_categories.user_id 
//                    AND v_staff_competed_panel_record_fabrication.fab_task_code = worker_task_categories.task_code
//                    AND complete_date BETWEEN :dateFrom AND :dateTo' .
//                            ($isInternal !== '' ? ' AND v_staff_competed_panel_record_fabrication.internal_project = :internalProject' : ''))
//                    ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = v_staff_competed_panel_record_fabrication.fab_task_code')
//                    ->where(['worker_task_categories.task_type' => TaskAssignment::taskTypeFabrication, 'ref.active_sts' => 1])
//                    ->groupBy(['worker_task_categories.user_id']);
//        } else {
//            $query->select([
//                        'worker_task_categories.user_id as id',
//                        'MAX(user.staff_id) as staffId',
//                        'MAX(user.fullname) as fullname',
//                        'ROUND(COALESCE(SUM(amount / totalStaff * qty_completed_panel_individual / qty_total_panel * panel_type_weight / 100 * single_task_weight / 100), 0), 4) AS totalPerformance'
//                    ])
//                    ->from('worker_task_categories')
//                    ->leftJoin('user', 'user.id = worker_task_categories.user_id')
//                    ->leftJoin('v_staff_competed_panel_record_electrical',
//                            'v_staff_competed_panel_record_electrical.user_id = worker_task_categories.user_id 
//                    AND v_staff_competed_panel_record_electrical.elec_task_code = worker_task_categories.task_code
//                    AND complete_date BETWEEN :dateFrom AND :dateTo' .
//                            ($isInternal !== '' ? ' AND v_staff_competed_panel_record_electrical.internal_project = :internalProject' : ''))
//                    ->leftJoin('ref_proj_prod_task_elec ref', 'ref.code = v_staff_competed_panel_record_electrical.elec_task_code')
//                    ->where(['worker_task_categories.task_type' => TaskAssignment::taskTypeElectrical, 'ref.active_sts' => 1])
//                    ->groupBy(['worker_task_categories.user_id']);
//        }
//
//        $query->andWhere(['!=', 'user.employment_type', 'contract'])
//                ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
//                ->addParams([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);
//
//        if ($isInternal !== '') {
//            $query->addParams([':internalProject' => $isInternal]);
//        }
//
//        return $query->all();
//    }

    public function calculateIncentiveFactoryStaff($performance): string {
        if (!is_numeric($performance) || $performance < 0) {
            return '0.00';
        }

        $totalIncentive = 0.00;
        foreach (ReportingModel::FACTORY_STAFF_PERFORMANCE_INCENTIVE as $tier) {
            if ($performance >= $tier['threshold']) {
                $totalIncentive += $tier['incentive'];
            }
        }
        return number_format($totalIncentive, 2, '.', '');
    }

    public function calculateByDepartmentPendingTaskRecord($dateFrom, $dateTo, $isInternal, $department) {
        $buildQuery = function ($view, $taskCode, $refTable, $dept) use ($dateFrom, $dateTo, $isInternal) {
            // 1) Build a subquery that aggregates pending amount per user_id + task_code
            $pendingSub = (new Query())
                    ->select([
                        'v.user_id AS user_id',
                        "v.{$taskCode} AS task_code",
                        // sum of pending expression grouped per user+task
                        'ROUND(COALESCE(SUM(
                    CASE
                        WHEN v.project_id IS NOT NULL THEN
                            amount / totalStaff * panel_type_weight / 100 * single_task_weight / 100
                        ELSE 0
                    END
                ), 0), 4) AS pendingAmount'
                    ])
                    ->from(["v" => $view])
                    ->leftJoin(["ref" => $refTable], "ref.code = v.{$taskCode} AND ref.active_sts = 1")
                    ->where(['v.complete_date' => null])
                    ->andWhere(['<=', 'v.start_date', $dateTo])
                    ->groupBy(['v.user_id', "v.{$taskCode}"]);

            // apply internal filter to the subquery if needed
            if ($isInternal == '0' || $isInternal == '1') {
                $pendingSub->andWhere(['v.internal_project' => $isInternal]);
            }

            // 2) Main query: join worker_task_categories with user and aggregated pending subquery
            $query = (new Query())
                    ->select([
                        'worker_task_categories.user_id AS id',
                        'MAX(user.staff_id) AS staffId',
                        'MAX(user.fullname) AS fullname',
                        // Sum aggregated pending amounts (null -> 0)
                        'ROUND(COALESCE(SUM(p.pendingAmount), 0), 4) AS totalPendingWorkAmount',
                    ])
                    ->from('worker_task_categories')
                    ->leftJoin('user', 'user.id = worker_task_categories.user_id')
                    // join aggregated pending amounts per user+task
                    ->leftJoin(['p' => $pendingSub],
                            "p.user_id = worker_task_categories.user_id AND p.task_code = worker_task_categories.task_code")
                    ->where(['worker_task_categories.task_type' => $dept])
                    ->andWhere(['!=', 'user.employment_type', 'contract'])
                    ->andWhere(['user.status' => \common\models\User::STATUS_ACTIVE])
                    ->groupBy(['worker_task_categories.user_id']);

            return $query;
        };

        // if department specified
        if (!empty($department)) {
            $isFab = $department == TaskAssignment::taskTypeFabrication;
            $view = $isFab ? 'v_staff_competed_panel_record_fabrication' : 'v_staff_competed_panel_record_electrical';
            $taskCode = $isFab ? 'fab_task_code' : 'elec_task_code';
            $refTable = $isFab ? 'ref_proj_prod_task_fab' : 'ref_proj_prod_task_elec';

            $result = $buildQuery($view, $taskCode, $refTable, $department)->all();

            usort($result, function ($a, $b) {
                return strcasecmp($a['fullname'], $b['fullname']);
            });

            return $result;
        }

        // combine fab + elec
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

        // combine by user id
        $combined = [];
        foreach (array_merge($fabQuery, $elecQuery) as $row) {
            $id = $row['id'];
            if (!isset($combined[$id])) {
                $combined[$id] = $row;
            } else {
                $combined[$id]['totalPendingWorkAmount'] += $row['totalPendingWorkAmount'];
            }
        }

        usort($combined, function ($a, $b) {
            return strcasecmp($a['fullname'], $b['fullname']);
        });

        return array_values($combined);
    }

    public function getDepartmentTaskCompletion($model, $dateFrom, $dateTo) {
        // ELECTRICAL
        $elecQuery = VElecTasksMasterlist::find()
                ->select(['project_production_panel_code']);

        $elecPercentQuery = VElecTasksMasterlist::find()
                ->select(['project_production_panel_code', 'elec_complete_percent']);

        if ($model->is_internalProject !== '') {
            $elecQuery->andWhere(['internal_project' => $model->is_internalProject]);
            $elecPercentQuery->andWhere(['internal_project' => $model->is_internalProject]);
        }

        $tasksElecCreated = $elecQuery
                        ->andWhere(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->count() * 100;

        $tasksElecComplete = $elecPercentQuery
                        ->andWhere(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->sum('elec_complete_percent') ?: 0;

        $incompleteElec = max(0, $tasksElecCreated - $tasksElecComplete);
        $completeElec = $tasksElecComplete;

        $electricalData = [
            'labels' => ['Incomplete', 'Complete'],
            'datasets' => [[
            'data' => [$incompleteElec, $completeElec],
            'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]]
        ];

        // FABRICATION
        $fabCreated = VFabTasksMasterlist::find()
                        ->select(['project_production_panel_code'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->count() * 100;

        $fabComplete = VFabTasksMasterlist::find()
                        ->select(['project_production_panel_code', 'fab_complete_percent'])
                        ->where(['between', 'task_create_date', $dateFrom, $dateTo])
                        ->distinct()
                        ->sum('fab_complete_percent') ?: 0;

        $incompleteFab = max(0, $fabCreated - $fabComplete);
        $completeFab = $fabComplete;

        $fabricationData = [
            'labels' => ['Incomplete', 'Complete'],
            'datasets' => [[
            'data' => [$incompleteFab, $completeFab],
            'backgroundColor' => ['#FF6384', '#36A2EB'],
                ]]
        ];

        return [
            'electricalData' => json_encode($electricalData),
            'fabricationData' => json_encode($fabricationData)
        ];
    }
}
