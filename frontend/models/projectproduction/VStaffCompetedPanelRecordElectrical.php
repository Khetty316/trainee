<?php

namespace frontend\models\projectproduction;

use Yii;

/**
 * This is the model class for table "v_staff_competed_panel_record_electrical".
 *
 * @property int $project_id
 * @property string|null $project_production_panel_code
 * @property int $proj_prod_master
 * @property int|null $panel_id
 * @property string|null $panel_description
 * @property string|null $complete_date
 * @property int $user_id
 * @property string|null $fullname
 * @property string|null $staff_id
 * @property string|null $project_production_code
 * @property string|null $project_name
 * @property int|null $totalStaff
 * @property float|null $qty_completed_panel
 * @property float|null $qty_total_panel
 * @property string $elec_task_code
 * @property float|null $panel_type_weight
 * @property float|null $single_task_weight
 * @property float|null $amount
 */
class VStaffCompetedPanelRecordElectrical extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_staff_competed_panel_record_electrical';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'proj_prod_master', 'panel_id', 'user_id', 'totalStaff'], 'integer'],
            [['proj_prod_master', 'elec_task_code'], 'required'],
            [['complete_date'], 'safe'],
            [['qty_completed_panel', 'qty_total_panel', 'panel_type_weight', 'single_task_weight', 'amount'], 'number'],
            [['project_production_panel_code', 'panel_description', 'fullname', 'project_production_code', 'project_name'], 'string', 'max' => 255],
            [['staff_id', 'elec_task_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'Project ID',
            'project_production_panel_code' => 'Project Production Panel Code',
            'proj_prod_master' => 'Proj Prod Master',
            'panel_id' => 'Panel ID',
            'panel_description' => 'Panel Description',
            'complete_date' => 'Complete Date',
            'user_id' => 'User ID',
            'fullname' => 'Fullname',
            'staff_id' => 'Staff ID',
            'project_production_code' => 'Project Production Code',
            'project_name' => 'Project Name',
            'totalStaff' => 'Total Staff',
            'qty_completed_panel' => 'Qty Completed Panel',
            'qty_total_panel' => 'Qty Total Panel',
            'elec_task_code' => 'Elec Task Code',
            'panel_type_weight' => 'Panel Type Weight',
            'single_task_weight' => 'Single Task Weight',
            'amount' => 'Amount',
        ];
    }
}
