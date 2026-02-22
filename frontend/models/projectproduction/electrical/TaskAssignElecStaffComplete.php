<?php

namespace frontend\models\ProjectProduction\electrical;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_elec_staff_complete".
 *
 * @property int $id
 * @property int|null $task_assign_elec_staff_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $comment
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property TaskAssignElecStaff $taskAssignElecStaff
 * @property User $createdBy
 */
class TaskAssignElecStaffComplete extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_elec_staff_complete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_assign_elec_staff_id', 'created_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'created_at'], 'safe'],
            [['comment'], 'string', 'max' => 500],
            [['task_assign_elec_staff_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElecStaff::className(), 'targetAttribute' => ['task_assign_elec_staff_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'task_assign_elec_staff_id' => 'Task Assign Elec Staff ID',
            'quantity' => 'Quantity',
            'complete_date' => 'Complete Date',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[TaskAssignElecStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecStaff() {
        return $this->hasOne(TaskAssignElecStaff::className(), ['id' => 'task_assign_elec_staff_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }
}
