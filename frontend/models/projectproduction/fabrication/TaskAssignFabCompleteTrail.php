<?php

namespace frontend\models\projectproduction\fabrication;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_fab_complete_trail".
 *
 * @property int $id
 * @property int $task_assign_fab_complete_id
 * @property int $task_assign_fab_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $comment
 * @property string|null $complete_created_at
 * @property int|null $complete_created_by
 *
 * @property TaskAssignFab $taskAssignFab
 * @property User $completeCreatedBy
 */
class TaskAssignFabCompleteTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_assign_fab_complete_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_assign_fab_complete_id', 'task_assign_fab_id'], 'required'],
            [['task_assign_fab_complete_id', 'task_assign_fab_id', 'complete_created_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'complete_created_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['task_assign_fab_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignFab::className(), 'targetAttribute' => ['task_assign_fab_id' => 'id']],
            [['complete_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['complete_created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_assign_fab_complete_id' => 'Task Assign Fab Complete ID',
            'task_assign_fab_id' => 'Task Assign Fab ID',
            'quantity' => 'Quantity',
            'complete_date' => 'Complete Date',
            'comment' => 'Comment',
            'complete_created_at' => 'Complete Created At',
            'complete_created_by' => 'Complete Created By',
        ];
    }

    /**
     * Gets query for [[TaskAssignFab]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFab()
    {
        return $this->hasOne(TaskAssignFab::className(), ['id' => 'task_assign_fab_id']);
    }

    /**
     * Gets query for [[CompleteCreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompleteCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'complete_created_by']);
    }
}
