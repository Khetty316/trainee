<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_elec_complete_delete".
 *
 * @property int $id
 * @property int $task_assign_elec_complete_id
 * @property int $task_assign_elec_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $complete_comment
 * @property string|null $revert_comment
 * @property string|null $complete_created_at
 * @property int|null $complete_created_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property TaskAssignElec $taskAssignElec
 * @property User $completeCreatedBy
 * @property User $deletedBy
 */
class TaskAssignElecCompleteDelete extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_assign_elec_complete_delete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_assign_elec_complete_id', 'task_assign_elec_id'], 'required'],
            [['task_assign_elec_complete_id', 'task_assign_elec_id', 'complete_created_by', 'deleted_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'complete_created_at', 'deleted_at'], 'safe'],
            [['complete_comment', 'revert_comment'], 'string', 'max' => 255],
            [['task_assign_elec_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElec::className(), 'targetAttribute' => ['task_assign_elec_id' => 'id']],
            [['complete_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['complete_created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
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
            'complete_comment' => 'Complete Comment',
            'revert_comment' => 'Revert Comment',
            'complete_created_at' => 'Complete Created At',
            'complete_created_by' => 'Complete Created By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
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

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }
    
    public function beforeSave($insert) {
        $this->deleted_at = new \yii\db\Expression('NOW()');
        $this->deleted_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
    
    public function copyTaskAssignCompleteDetail($taskAssignCompleteDetail, $revertComment) {
        $this->attributes = $taskAssignCompleteDetail->attributes;
        $this->task_assign_elec_complete_id = $taskAssignCompleteDetail->id;
        $this->complete_comment = $taskAssignCompleteDetail->comment;
        $this->revert_comment = $revertComment;
        $this->complete_created_at = $taskAssignCompleteDetail->created_at;
        $this->complete_created_by = $taskAssignCompleteDetail->created_by;
        if(!$this->save()){
            return false;
        }
        
        return true;
    }
}
