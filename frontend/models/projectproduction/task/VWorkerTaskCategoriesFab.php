<?php

namespace frontend\models\projectproduction\task;

use Yii;

/**
 * This is the model class for table "v_worker_task_categories_fab".
 *
 * @property string|null $staff_id
 * @property string|null $fullname
 * @property int $user_id
 * @property string $task_type fab vs elec
 * @property resource $task_code
 * @property string|null $name
 */
class VWorkerTaskCategoriesFab extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_worker_task_categories_fab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'task_type', 'task_code'], 'required'],
            [['user_id'], 'integer'],
            [['staff_id', 'task_code'], 'string', 'max' => 10],
            [['fullname', 'name'], 'string', 'max' => 255],
            [['task_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'staff_id' => 'Staff ID',
            'fullname' => 'Fullname',
            'user_id' => 'User ID',
            'task_type' => 'Task Type',
            'task_code' => 'Task Code',
            'name' => 'Name',
        ];
    }

}
