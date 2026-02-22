<?php

namespace frontend\models\projectproduction\task;

use Yii;
use common\models\User;

/**
 * This is the model class for table "worker_task_categories".
 *
 * @property int $id
 * @property int $user_id
 * @property string $task_type fab vs elec
 * @property resource $task_code
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 */
class WorkerTaskCategories extends \yii\db\ActiveRecord {

    CONST fabTask = "fab";
    CONST elecTask = "elec";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'worker_task_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'task_type', 'task_code'], 'required'],
            [['user_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['task_type'], 'string', 'max' => 50],
            [['task_code'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'task_type' => 'Task Type',
            'task_code' => 'Task Code',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
