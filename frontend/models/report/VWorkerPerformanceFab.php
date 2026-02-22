<?php

namespace frontend\models\report;

use Yii;

class VWorkerPerformanceFab extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_worker_performance_fab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_assign_id', 'user_id', 'panel_id', 'quantity', 'total_staff_assigned'], 'integer'],
            [['complete_date'], 'safe'],
            [['completing_qty', 'amount', 'task_weight', 'dept_weight', 'performanceAmount'], 'number'],
            [['task_code', 'project_type_name', 'project_type_code'], 'required'],
            [['project_production_code', 'project_name', 'project_production_panel_code', 'panel_description', 'task_name', 'project_type_name'], 'string', 'max' => 255],
            [['task_code', 'project_type_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'task_assign_id' => 'Task Assign ID',
            'user_id' => 'User ID',
            'complete_date' => 'Complete Date',
            'project_production_code' => 'Project Production Code',
            'project_name' => 'Project Name',
            'panel_id' => 'Panel ID',
            'project_production_panel_code' => 'Project Production Panel Code',
            'panel_description' => 'Panel Description',
            'quantity' => 'Quantity',
            'completing_qty' => 'Completing Qty',
            'total_staff_assigned' => 'Total Staff Assigned',
            'amount' => 'Amount',
            'task_name' => 'Task Name',
            'task_code' => 'Task Code',
            'task_weight' => 'Task Weight',
            'project_type_name' => 'Project Type Name',
            'project_type_code' => 'Project Type Code',
            'dept_weight' => 'Dept Weight',
            'performanceAmount' => 'performanceAmount'
        ];
    }

    public static function findCustom($userId, $startDate, $endDate) {
        $query = (new \yii\db\Query())
                ->select([
                    'task_assign_fab_staff.task_assign_fab_id as task_assign_id',
                    'task_assign_fab_staff.user_id',
                    'task_assign_fab_complete.complete_date',
                    'project_production_master.project_production_code',
                    'project_production_master.name as project_name',
                    'project_production_panels.panel_id',
                    'project_production_panels.project_production_panel_code',
                    'project_production_panels.panel_description',
                    'project_production_panels.quantity',
                    'task_assign_fab_complete.quantity as completing_qty',
                    new \yii\db\Expression("(select count(*) from `task_assign_fab_staff` where task_assign_fab_id=task_assign_fab_complete.task_assign_fab_id) as total_staff_assigned"),
                    'project_production_panels.amount',
                    'ref_proj_prod_task_fab.name as task_name',
                    'ref_proj_prod_task_fab.code as task_code',
                    'ref_proj_prod_task_fab.weight as task_weight',
                    'ref_project_q_types.project_type_name',
                    'ref_project_q_types.code as project_type_code',
                    'ref_project_q_types.fab_dept_percentage as dept_weight'
                ])
                ->from('task_assign_fab_staff')
                ->join('JOIN', 'task_assign_fab_complete', 'task_assign_fab_complete.task_assign_fab_id = task_assign_fab_staff.task_assign_fab_id')
                ->join('JOIN', 'task_assign_fab', 'task_assign_fab.id = task_assign_fab_complete.task_assign_fab_id')
                ->join('JOIN', 'production_fab_tasks', 'production_fab_tasks.id = task_assign_fab.prod_fab_task_id')
                ->join('JOIN', 'project_production_panels', 'project_production_panels.id = production_fab_tasks.proj_prod_panel_id')
                ->join('JOIN', 'ref_proj_prod_task_fab', 'ref_proj_prod_task_fab.code = production_fab_tasks.fab_task_code')
                ->join('JOIN', 'project_production_master', 'project_production_master.id = project_production_panels.proj_prod_master')
                ->join('JOIN', 'project_q_revisions', 'project_q_revisions.id = project_production_master.revision_id')
                ->join('JOIN', 'project_q_types', 'project_q_types.id = project_q_revisions.project_q_type_id')
                ->join('JOIN', 'ref_project_q_types', 'ref_project_q_types.code = project_q_types.type')
                ->where(['task_assign_fab_staff.user_id' => $userId])
                ->andWhere(['between', 'task_assign_fab_complete.complete_date', $startDate, $endDate])
                ->orderBy(['complete_date' => SORT_ASC]);

        return $query->all(); // or ->one() if expecting a single result
    }

    public static function findCustomSummarize($userId, $startDate, $endDate) {
        // Inner Query
        $subQuery = (new \yii\db\Query())
                ->select([
                    '`task_assign_fab_staff`.`task_assign_fab_id` AS `task_assign_id`',
                    '`task_assign_fab_staff`.`user_id`',
                    '`task_assign_fab_complete`.`complete_date`',
                    '`project_production_master`.`project_production_code`',
                    '`project_production_master`.`name` AS `project_name`',
                    '`project_production_panels`.`panel_id`',
                    '`project_production_panels`.`project_production_panel_code`',
                    '`project_production_panels`.`panel_description`',
                    '`project_production_panels`.`quantity`',
                    ('SUM(`task_assign_fab_complete`.`quantity`) AS `completing_qty`'),
                    new \yii\db\Expression('(SELECT COUNT(*) FROM `task_assign_fab_staff` WHERE `task_assign_fab_staff`.`task_assign_fab_id` = `task_assign_fab_complete`.`task_assign_fab_id`) AS `total_staff_assigned`'),
                    '`project_production_panels`.`amount`',
                    '`ref_proj_prod_task_fab`.`name` AS `task_name`',
                    '`ref_proj_prod_task_fab`.`code` AS `task_code`',
//                    '`ref_proj_prod_task_fab`.`weight` AS `task_weight`',
                    '`ref_project_q_types`.`project_type_name`',
                    '`ref_project_q_types`.`code` AS `project_type_code`',
//                    '`ref_project_q_types`.`fab_dept_percentage` AS `dept_weight`'
                    '`prod_fab_task_weight`.`panel_type_weight` AS `panel_weight`',
                    '`prod_fab_task_weight`.`proj_prod_panel_id` AS `proj_prod_panel_id`'
                ])
                ->from('`task_assign_fab_staff`')
                ->join('JOIN', '`task_assign_fab_complete`', '`task_assign_fab_complete`.`task_assign_fab_id` = `task_assign_fab_staff`.`task_assign_fab_id`')
                ->join('JOIN', '`task_assign_fab`', '`task_assign_fab`.`id` = `task_assign_fab_complete`.`task_assign_fab_id`')
                ->join('JOIN', '`production_fab_tasks`', '`production_fab_tasks`.`id` = `task_assign_fab`.`prod_fab_task_id`')
                ->join('JOIN', '`project_production_panels`', '`project_production_panels`.`id` = `production_fab_tasks`.`proj_prod_panel_id`')
                ->join('JOIN', '`ref_proj_prod_task_fab`', '`ref_proj_prod_task_fab`.`code` = `production_fab_tasks`.`fab_task_code`')
                ->join('JOIN', '`prod_fab_task_weight`', '`prod_fab_task_weight`.`proj_prod_panel_id` = `production_fab_tasks`.`proj_prod_panel_id`')
                ->join('JOIN', '`project_production_master`', '`project_production_master`.`id` = `project_production_panels`.`proj_prod_master`')
                ->join('JOIN', '`project_q_revisions`', '`project_q_revisions`.`id` = `project_production_master`.`revision_id`')
                ->join('JOIN', '`project_q_types`', '`project_q_types`.`id` = `project_q_revisions`.`project_q_type_id`')
                ->join('JOIN', '`ref_project_q_types`', '`ref_project_q_types`.`code` = `project_q_types`.`type`')
                ->where(['`task_assign_fab_staff`.`user_id`' => $userId])
                ->andWhere(['`task_assign_fab`.`deactivated_at`' => null])
                ->andWhere(['between', '`task_assign_fab_complete`.`complete_date`', $startDate, $endDate])
                ->groupBy(['`task_assign_fab_staff`.`task_assign_fab_id`']);

        // Outer Query
//        $query = (new \yii\db\Query())
//                ->select([
//                    '*',
//                    ('SUM(completing_qty / quantity / total_staff_assigned * task_weight / 100 * dept_weight / 100 * amount) AS performanceAmount')
//                ])
//                ->from(['bigGroup' => $subQuery])
//                ->groupBy(['bigGroup.`project_production_panel_code`']);

//        return $query->all();
        return $subQuery->all();
    }

    public static function findDptCustomSummary($userId, $startDate, $endDate) {
        $allPerformance = self::findCustomSummarize($userId, $startDate, $endDate);
        $user = \common\models\User::findOne($userId);

//        $singleUser = [];
//        $singleUser['id'] = $user->id;
//        $singleUser['fullname'] = $user->fullname;
//        $singleUser['staffId'] = $user->staff_id;
//        $singleUser["completeQty"] = 0;
//        $singleUser['totalPerformance'] = 0;

        $defaultSingleUser = [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'staffId' => $user->staff_id,
            'project_production_code' => "",
            'project_name' => "",
            'project_production_panel_code' => "",
            'panel_description' => "",
            'completing_qty' => 0,
            'quantity' => 0,
            'total_staff_assigned' => 0,
            'panel_weight' => 0,
            'amount' => 0,
            'proj_prod_panel_id' => "",
            'task_code' => "",
            'totalPerformance' => 0
        ];

        $finalData = [];

        if ($allPerformance) {
            foreach ($allPerformance as $performance) {
                $singleUser = $defaultSingleUser;
                $singleUser['project_production_code'] = $performance['project_production_code'];
                $singleUser['project_name'] = $performance['project_name'];
                $singleUser['project_production_panel_code'] = $performance['project_production_panel_code'];
                $singleUser['panel_description'] = $performance['panel_description'];
                $singleUser['completing_qty'] = $performance['completing_qty'];
                $singleUser['quantity'] = $performance['quantity'];
                $singleUser['total_staff_assigned'] = $performance['total_staff_assigned'];
                $singleUser['panel_weight'] = $performance['panel_weight'];
                $singleUser['amount'] = $performance['amount'];
                $singleUser['proj_prod_panel_id'] = $performance['proj_prod_panel_id'];
                $singleUser['task_code'] = $performance['task_code'];
                $singleUser['totalPerformance'] = 0;

                $finalData[] = $singleUser;
            }
        } else {
            $finalData[] = $defaultSingleUser;
        }
//        return $singleUser['totalPerformance'] == 0 ? null : $singleUser;
        return $finalData;
    }

    public static function findTaskCompletionAllFactoryStaff($startDate, $endDate) {
        $query = (new \yii\db\Query())
                ->select([
                    '`task_assign_fab_staff`.`task_assign_fab_id` AS `task_assign_id`',
                    '`task_assign_fab_staff`.`user_id`',
                    '`project_production_master`.`project_production_code`',
                    '`project_production_master`.`name` AS `project_name`',
                    '`project_production_panels`.`panel_id`',
                    '`project_production_panels`.`project_production_panel_code`',
                    '`project_production_panels`.`panel_description`',
                    '`project_production_panels`.`quantity`',
                    new \yii\db\Expression('(SELECT COUNT(*) FROM `task_assign_fab_staff` WHERE `task_assign_fab_staff`.`task_assign_fab_id` = `task_assign_fab`.`id`) AS `total_staff_assigned`'),
                    '`project_production_panels`.`amount`',
                    '`ref_proj_prod_task_fab`.`name` AS `task_name`',
                    '`ref_proj_prod_task_fab`.`code` AS `task_code`',
                    '`ref_project_q_types`.`project_type_name`',
                    '`ref_project_q_types`.`code` AS `project_type_code`',
                    '`prod_fab_task_weight`.`panel_type_weight` AS `panel_weight`',
                    '`prod_fab_task_weight`.`proj_prod_panel_id` AS `proj_prod_panel_id`'
                ])
                ->from('`task_assign_fab_staff`')
                ->join('JOIN', '`task_assign_fab`', '`task_assign_fab`.`id` = `task_assign_fab_staff`.`task_assign_fab_id`')
                ->join('JOIN', '`production_fab_tasks`', '`production_fab_tasks`.`id` = `task_assign_fab`.`prod_fab_task_id`')
                ->join('JOIN', '`project_production_panels`', '`project_production_panels`.`id` = `production_fab_tasks`.`proj_prod_panel_id`')
                ->join('JOIN', '`ref_proj_prod_task_fab`', '`ref_proj_prod_task_fab`.`code` = `production_fab_tasks`.`fab_task_code`')
                ->join('JOIN', '`prod_fab_task_weight`', '`prod_fab_task_weight`.`proj_prod_panel_id` = `production_fab_tasks`.`proj_prod_panel_id`')
                ->join('JOIN', '`project_production_master`', '`project_production_master`.`id` = `project_production_panels`.`proj_prod_master`')
                ->join('JOIN', '`project_q_revisions`', '`project_q_revisions`.`id` = `project_production_master`.`revision_id`')
                ->join('JOIN', '`project_q_types`', '`project_q_types`.`id` = `project_q_revisions`.`project_q_type_id`')
                ->join('JOIN', '`ref_project_q_types`', '`ref_project_q_types`.`code` = `project_q_types`.`type`')
                ->andWhere(['`task_assign_fab`.`deactivated_at`' => null])
                ->andWhere(['between', '`task_assign_fab`.`start_date`', $startDate, $endDate])
                ->groupBy(['`task_assign_fab_staff`.`task_assign_fab_id`']);

        return $query->all();
    }
}
