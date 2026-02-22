<?php

namespace frontend\models\projectproduction;

use Yii;

/**
 * This is the model class for table "v_staff_production_all".
 *
 * @property string $task_type
 * @property int $id
 * @property int|null $user_id
 * @property int|null $task_assign_elec_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $assignee_fullname
 * @property string|null $assigner_fullname
 * @property int $taskAssignment_id
 * @property float|null $assigned_qty
 * @property float|null $assigned_complete_qty
 * @property float|null $assigned_complete_qty_individual
 * @property string|null $assigned_start_date
 * @property string|null $assigned_complete_date
 * @property string|null $assigned_complete_date_individual
 * @property string|null $assigned_comments
 * @property int|null $assigned_active_status
 * @property string|null $deactivated_at
 * @property int|null $deactivated_by
 * @property string|null $deactivated_by_fullname
 * @property int $taskToDo_id
 * @property float|null $taskToDo_total_qty
 * @property float|null $taskToDo_assigned_qty
 * @property string|null $task_name
 * @property int|null $task_sort
 * @property int $panel_id
 * @property string|null $panel_code
 * @property string|null $panel_description
 * @property int|null $panel_qty
 * @property int $panel_sort
 * @property string|null $panel_unit_code
 * @property string|null $panel_unit_name
 * @property string|null $panel_remark
 * @property string|null $panel_finalized_at
 * @property int|null $panel_finalized_by
 * @property float|null $panel_elec_assign_percent
 * @property float|null $panel_elec_complete_percent
 * @property string|null $panel_elec_completed_at
 * @property int|null $panel_elec_completed_by
 */
class VStaffProductionAll extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_staff_production_all';
    }

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'task_assign_elec_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'assignee_fullname', 'assigner_fullname', 'assigned_qty', 'assigned_complete_qty', 'assigned_start_date', 'assigned_complete_date', 'assigned_comments', 'assigned_active_status', 'deactivated_at', 'deactivated_by', 'deactivated_by_fullname', 'taskToDo_total_qty', 'taskToDo_assigned_qty', 'task_name', 'task_sort', 'panel_code', 'panel_description', 'panel_qty', 'panel_unit_code', 'panel_unit_name', 'panel_remark', 'panel_finalized_at', 'panel_finalized_by', 'panel_elec_assign_percent', 'panel_elec_complete_percent', 'panel_elec_completed_at', 'panel_elec_completed_by'], 'default', 'value' => null],
            [['task_type'], 'default', 'value' => ''],
            [['panel_sort'], 'default', 'value' => 0],
            [['id', 'user_id', 'task_assign_elec_id', 'created_by', 'updated_by', 'taskAssignment_id', 'assigned_active_status', 'deactivated_by', 'taskToDo_id', 'task_sort', 'panel_id', 'panel_qty', 'panel_sort', 'panel_finalized_by', 'panel_elec_completed_by'], 'integer'],
            [['created_at', 'updated_at', 'assigned_start_date', 'assigned_complete_date', 'deactivated_at', 'panel_finalized_at', 'panel_elec_completed_at'], 'safe'],
            [['assigned_qty', 'assigned_complete_qty', 'taskToDo_total_qty', 'taskToDo_assigned_qty', 'panel_elec_assign_percent', 'panel_elec_complete_percent'], 'number'],
            [['assigned_comments', 'panel_remark'], 'string'],
            [['task_type'], 'string', 'max' => 4],
            [['assignee_fullname', 'assigner_fullname', 'deactivated_by_fullname', 'task_name', 'panel_code', 'panel_description', 'panel_unit_name'], 'string', 'max' => 255],
            [['panel_unit_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'task_type' => 'Task Type',
            'id' => 'ID',
            'user_id' => 'User ID',
            'task_assign_elec_id' => 'Task Assign Elec ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'assignee_fullname' => 'Assignee Fullname',
            'assigner_fullname' => 'Assigner Fullname',
            'taskAssignment_id' => 'Task Assignment ID',
            'assigned_qty' => 'Assigned Qty',
            'assigned_complete_qty' => 'Assigned Complete Qty',
            'assigned_complete_qty_individual' => 'Assigned Complete Qty Individual',
            'assigned_start_date' => 'Assigned Start Date',
            'assigned_complete_date' => 'Assigned Complete Date',
            'assigned_complete_date_individual' => 'Assigned Complete Date Individual',
            'assigned_comments' => 'Assigned Comments',
            'assigned_active_status' => 'Assigned Active Status',
            'deactivated_at' => 'Deactivated At',
            'deactivated_by' => 'Deactivated By',
            'deactivated_by_fullname' => 'Deactivated By Fullname',
            'taskToDo_id' => 'Task To Do ID',
            'taskToDo_total_qty' => 'Task To Do Total Qty',
            'taskToDo_assigned_qty' => 'Task To Do Assigned Qty',
            'task_name' => 'Task Name',
            'task_sort' => 'Task Sort',
            'panel_id' => 'Panel ID',
            'panel_code' => 'Panel Code',
            'panel_description' => 'Panel Description',
            'panel_qty' => 'Panel Qty',
            'panel_sort' => 'Panel Sort',
            'panel_unit_code' => 'Panel Unit Code',
            'panel_unit_name' => 'Panel Unit Name',
            'panel_remark' => 'Panel Remark',
            'panel_finalized_at' => 'Panel Finalized At',
            'panel_finalized_by' => 'Panel Finalized By',
            'panel_elec_assign_percent' => 'Panel Elec Assign Percent',
            'panel_elec_complete_percent' => 'Panel Elec Complete Percent',
            'panel_elec_completed_at' => 'Panel Elec Completed At',
            'panel_elec_completed_by' => 'Panel Elec Completed By',
        ];
    }

    /**
     * Gets query for [[TaskAssignFabCompletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabCompletes() {
        return $this->hasMany(fabrication\TaskAssignFabComplete::class, ['task_assign_fab_id' => 'task_assign_elec_id']);
    }

    /**
     * Gets query for [[TaskAssignElecCompletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecCompletes() {
        return $this->hasMany(electrical\TaskAssignElecComplete::class, ['task_assign_elec_id' => 'task_assign_elec_id']);
    }

}
