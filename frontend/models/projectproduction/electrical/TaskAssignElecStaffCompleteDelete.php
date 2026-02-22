<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_elec_staff_complete_delete".
 *
 * @property int $id
 * @property int|null $task_assign_elec_staff_complete_id
 * @property int|null $task_assign_elec_staff_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $complete_comment
 * @property string|null $revert_comment
 * @property string|null $complete_created_at
 * @property int|null $complete_created_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property User $completeCreatedBy
 * @property User $deletedBy
 * @property TaskAssignElecStaff $taskAssignElecStaff
 */
class TaskAssignElecStaffCompleteDelete extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_assign_elec_staff_complete_delete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_assign_elec_staff_complete_id', 'task_assign_elec_staff_id', 'complete_created_by', 'deleted_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'complete_created_at', 'deleted_at'], 'safe'],
            [['complete_comment', 'revert_comment'], 'string', 'max' => 500],
            [['complete_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['complete_created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['task_assign_elec_staff_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElecStaff::className(), 'targetAttribute' => ['task_assign_elec_staff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_assign_elec_staff_complete_id' => 'Task Assign Elec Staff Complete ID',
            'task_assign_elec_staff_id' => 'Task Assign Elec Staff ID',
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

    /**
     * Gets query for [[TaskAssignElecStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecStaff()
    {
        return $this->hasOne(TaskAssignElecStaff::className(), ['id' => 'task_assign_elec_staff_id']);
    }
    
    public function beforeSave($insert) {
        $this->deleted_at = new \yii\db\Expression('NOW()');
        $this->deleted_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }

    public function copyTaskAssignCompleteDetail($taskAssignCompleteDetails, $revertComment) {
        if (empty($taskAssignCompleteDetails)) {
            return false;
        }

        foreach ($taskAssignCompleteDetails as $detail) {
            $newRecord = new self();
            $newRecord->attributes = $detail->attributes;
            $newRecord->task_assign_elec_staff_complete_id = $detail->id;
            $newRecord->complete_comment = $detail->comment;
            $newRecord->revert_comment = $revertComment;
            $newRecord->complete_created_at = $detail->created_at;
            $newRecord->complete_created_by = $detail->created_by;

            if (!$newRecord->save()) {
                return false;
            }
        }

        return true;
    }
}
