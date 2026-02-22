<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_elec_complete_trail".
 *
 * @property int $id
 * @property int $task_assign_elec_complete_id
 * @property int $task_assign_elec_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $comment
 * @property string|null $complete_created_at
 * @property int|null $complete_created_by
 *
 * @property TaskAssignElec $taskAssignElec
 * @property User $completeCreatedBy
 */
class TaskAssignElecCompleteTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_assign_elec_complete_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_assign_elec_complete_id', 'task_assign_elec_id'], 'required'],
            [['task_assign_elec_complete_id', 'task_assign_elec_id', 'complete_created_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'complete_created_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['task_assign_elec_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElec::className(), 'targetAttribute' => ['task_assign_elec_id' => 'id']],
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
            'task_assign_elec_complete_id' => 'Task Assign Elec Complete ID',
            'task_assign_elec_id' => 'Task Assign Elec ID',
            'quantity' => 'Quantity',
            'complete_date' => 'Complete Date',
            'comment' => 'Comment',
            'complete_created_at' => 'Complete Created At',
            'complete_created_by' => 'Complete Created By',
        ];
    }

    /**
     * Gets query for [[TaskAssignElec]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElec()
    {
        return $this->hasOne(TaskAssignElec::className(), ['id' => 'task_assign_elec_id']);
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
