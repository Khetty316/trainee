<?php

namespace frontend\models\projectproduction;

use Yii;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;

/**
 * This is the model class for table "v_production_tasks_error".
 *
 * @property string $task_type
 * @property int $id
 * @property int $production_task_id
 * @property int $error_code
 * @property int $panel_code
 * @property string|null $task_name
 * @property string|null $description
 * @property string|null $remark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class VProductionTasksError extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_production_tasks_error';
    }

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_name', 'description', 'remark', 'panel_code', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['task_type'], 'default', 'value' => ''],
            [['error_code'], 'default', 'value' => 0],
            [['id', 'production_task_id', 'error_code', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['task_type'], 'string', 'max' => 4],
            [['task_name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'task_type' => 'Task Type',
            'id' => 'ID',
            'panel_code' => 'Panel Code',
            'production_task_id' => 'Production Task ID',
            'error_code' => 'Error Code',
            'task_name' => 'Task Name',
            'description' => 'Description',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getProdElecTask() {
        return $this->hasOne(ProductionElecTasks::class, ['id' => 'production_task_id']);
    }

    public function getProdFabTask() {
        return $this->hasOne(ProductionFabTasks::class, ['id' => 'production_task_id']);
    }

    public static function getDropDownListTaskType() {
        return \yii\helpers\ArrayHelper::map(self::find()->groupBy('task_name')->orderBy(['id' => SORT_ASC])->all(), "task_name", "task_name");
    }

}
