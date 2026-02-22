<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_tasks".
 *
 * @property int $task_id
 * @property string $task_name
 * @property string|null $task_description
 * @property string|null $response_name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 * @property MiWorklist[] $miWorklists
 * @property RefMiMatrices[] $refMiMatrices
 */
class RefMiTasks extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_mi_tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['task_name', 'task_description', 'response_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'task_id' => 'Task ID',
            'task_name' => 'Task Name',
            'task_description' => 'Task Description',
            'response_name' => 'Response Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[MasterIncomings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterIncomings() {
        return $this->hasMany(MasterIncomings::className(), ['current_step_task_id' => 'task_id']);
    }

    /**
     * Gets query for [[MiWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiWorklists() {
        return $this->hasMany(MiWorklist::className(), ['task_id' => 'task_id']);
    }

    /**
     * Gets query for [[RefMiMatrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefMiMatrices() {
        return $this->hasMany(RefMiMatrices::className(), ['task_id' => 'task_id']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiTasks::find()->orderBy(['task_description'=>SORT_ASC])->all(), "task_id", "task_description");
    }

}
