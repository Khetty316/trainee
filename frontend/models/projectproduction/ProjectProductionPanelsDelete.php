<?php

namespace frontend\models\projectproduction;

use Yii;

/**
 * This is the model class for table "project_production_panels_delete".
 *
 * @property int $id
 * @property int $project_production_panels_id
 * @property string|null $project_production_panel_code
 * @property int $proj_prod_master
 * @property int|null $panel_id
 * @property string|null $panel_description
 * @property string|null $remark
 * @property float|null $amount
 * @property int $sort
 * @property int|null $quantity
 * @property string|null $unit_code
 * @property string|null $finalized_at
 * @property int|null $finalized_by
 * @property string|null $item_dispatch_status
 * @property string|null $design_completed_at
 * @property int|null $design_completed_by
 * @property string|null $material_completed_at
 * @property int|null $material_completed_by
 * @property float|null $fab_assign_percent
 * @property float|null $fab_complete_percent
 * @property string|null $fab_completed_at
 * @property int|null $fab_completed_by
 * @property string|null $fab_work_status
 * @property int|null $fab_dispatch_wire_quantity
 * @property float|null $elec_assign_percent
 * @property float|null $elec_complete_percent
 * @property string|null $elec_completed_at
 * @property int|null $elec_completed_by
 * @property string|null $elec_work_status
 * @property string|null $filename
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 */
class ProjectProductionPanelsDelete extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_production_panels_delete';
    }
    
    public function beforeSave($insert) {

            $this->deleted_at = new \yii\db\Expression('NOW()');
            $this->deleted_by = Yii::$app->user->id;

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_production_panels_id', 'proj_prod_master'], 'required'],
            [['project_production_panels_id', 'proj_prod_master', 'panel_id', 'sort', 'quantity', 'finalized_by', 'design_completed_by', 'material_completed_by', 'fab_completed_by', 'fab_dispatch_wire_quantity', 'elec_completed_by', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['remark'], 'string'],
            [['amount', 'fab_assign_percent', 'fab_complete_percent', 'elec_assign_percent', 'elec_complete_percent'], 'number'],
            [['finalized_at', 'design_completed_at', 'material_completed_at', 'fab_completed_at', 'elec_completed_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['project_production_panel_code', 'panel_description', 'filename'], 'string', 'max' => 255],
            [['unit_code', 'item_dispatch_status', 'fab_work_status', 'elec_work_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_production_panels_id' => 'Project Production Panels ID',
            'project_production_panel_code' => 'Project Production Panel Code',
            'proj_prod_master' => 'Proj Prod Master',
            'panel_id' => 'Panel ID',
            'panel_description' => 'Panel Description',
            'remark' => 'Remark',
            'amount' => 'Amount',
            'sort' => 'Sort',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'finalized_at' => 'Finalized At',
            'finalized_by' => 'Finalized By',
            'item_dispatch_status' => 'Item Dispatch Status',
            'design_completed_at' => 'Design Completed At',
            'design_completed_by' => 'Design Completed By',
            'material_completed_at' => 'Material Completed At',
            'material_completed_by' => 'Material Completed By',
            'fab_assign_percent' => 'Fab Assign Percent',
            'fab_complete_percent' => 'Fab Complete Percent',
            'fab_completed_at' => 'Fab Completed At',
            'fab_completed_by' => 'Fab Completed By',
            'fab_work_status' => 'Fab Work Status',
            'fab_dispatch_wire_quantity' => 'Fab Dispatch Wire Quantity',
            'elec_assign_percent' => 'Elec Assign Percent',
            'elec_complete_percent' => 'Elec Complete Percent',
            'elec_completed_at' => 'Elec Completed At',
            'elec_completed_by' => 'Elec Completed By',
            'elec_work_status' => 'Elec Work Status',
            'filename' => 'Filename',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
        ];
    }
    
}
