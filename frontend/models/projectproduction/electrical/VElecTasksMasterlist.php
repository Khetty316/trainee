<?php

namespace frontend\models\projectproduction\electrical;

use Yii;

/**
 * This is the model class for table "v_elec_tasks_masterlist".
 *
 * @property string|null $project_production_code
 * @property string|null $project_name
 * @property string|null $project_remark
 * @property string $client_name
 * @property string|null $project_production_panel_code
 * @property string|null $panel_description
 * @property string|null $panel_remark
 * @property int|null $panel_qty
 * @property float|null $elec_assign_percent
 * @property float|null $elec_complete_percent
 * @property string|null $elec_work_status
 * @property float|null $amount
 * @property string|null $task_name
 * @property int|null $active_sts
 * @property float|null $assigned_task_qty
 * @property string|null $task_assigner
 * @property string|null $task_start_date
 * @property string|null $task_end_date
 * @property string|null $assignee
 * @property string|null $task_create_date
 */
class VElecTasksMasterlist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_elec_tasks_masterlist';
    }

    public static function primaryKey() {
        return ["project_production_code"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_remark', 'panel_remark'], 'string'],
            [['client_name'], 'required'],
            [['panel_qty', 'active_sts'], 'integer'],
            [['elec_assign_percent', 'elec_complete_percent', 'amount', 'assigned_task_qty'], 'number'],
            [['task_start_date', 'task_end_date', 'task_create_date'], 'safe'],
            [['project_production_code', 'project_name', 'client_name', 'project_production_panel_code', 'panel_description', 'task_name', 'task_assigner', 'assignee'], 'string', 'max' => 255],
            [['elec_work_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'project_production_code' => 'Project Production Code',
            'project_name' => 'Project Name',
            'project_remark' => 'Project Remark',
            'client_name' => 'Client Name',
            'project_production_panel_code' => 'Project Production Panel Code',
            'panel_description' => 'Panel Description',
            'panel_remark' => 'Panel Remark',
            'panel_qty' => 'Panel Qty',
            'elec_assign_percent' => 'Elec Assign Percent',
            'elec_complete_percent' => 'Elec Complete Percent',
            'elec_work_status' => 'Elec Work Status',
            'amount' => 'Amount',
            'task_name' => 'Task Name',
            'active_sts' => 'Active Sts',
            'assigned_task_qty' => 'Assigned Task Qty',
            'task_assigner' => 'Task Assigner',
            'task_start_date' => 'Task Start Date',
            'task_end_date' => 'Task End Date',
            'assignee' => 'Assignee',
            'task_create_date' => 'Task Create Date',
        ];
    }

}
