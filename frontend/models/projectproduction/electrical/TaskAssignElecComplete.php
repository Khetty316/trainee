<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use common\models\User;
use frontend\models\projectproduction\electrical\TaskAssignElec;

/**
 * This is the model class for table "task_assign_elec_complete".
 *
 * @property int $id
 * @property int|null $task_assign_elec_id
 * @property float|null $quantity
 * @property string|null $complete_date
 * @property string|null $comment
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property TaskAssignElec $taskAssignElec
 */
class TaskAssignElecComplete extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_elec_complete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['complete_date', 'comment', 'created_at', 'created_by'], 'default', 'value' => null],
            [['quantity', 'task_assign_elec_id'], 'required'],
            [['task_assign_elec_id', 'created_by'], 'integer'],
            [['quantity'], 'number'],
            [['complete_date', 'created_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['task_assign_elec_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElec::class, 'targetAttribute' => ['task_assign_elec_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'task_assign_elec_id' => 'Task Assign Elec ID',
            'quantity' => 'Quantity',
            'complete_date' => 'Complete Date',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[TaskAssignElec]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElec() {
        return $this->hasOne(TaskAssignElec::class, ['id' => 'task_assign_elec_id']);
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
