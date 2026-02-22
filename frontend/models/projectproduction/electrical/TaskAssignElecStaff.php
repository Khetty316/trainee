<?php

namespace frontend\models\projectproduction\electrical;
namespace frontend\models\ProjectProduction\electrical;

use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_elec_staff".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $task_assign_elec_id
 * @property float|null $complete_qty
 * @property float|null $complete_qty_over_total_staff
 * @property string|null $complete_date
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 * @property TaskAssignElec $taskAssignElec
 * @property TaskAssignElecStaffComplete[] $taskAssignElecStaffCompletes
 * @property TaskAssignElecStaffCompleteDelete[] $taskAssignElecStaffCompleteDeletes
 */
class TaskAssignElecStaff extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_elec_staff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'task_assign_elec_id', 'created_by', 'updated_by'], 'integer'],
            [['complete_qty', 'complete_qty_over_total_staff'], 'number'],
            [['complete_date', 'created_at', 'updated_at'], 'safe'],
            [['user_id', 'task_assign_elec_id'], 'unique', 'targetAttribute' => ['user_id', 'task_assign_elec_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['task_assign_elec_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskAssignElec::className(), 'targetAttribute' => ['task_assign_elec_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'task_assign_elec_id' => 'Task Assign Elec ID',
            'complete_qty' => 'Complete Qty',
            'complete_qty_over_total_staff' => 'Complete Qty Over Total Staff',
            'complete_date' => 'Complete Date',
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
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[TaskAssignElec]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElec() {
        return $this->hasOne(TaskAssignElec::className(), ['id' => 'task_assign_elec_id']);
    }

    /**
     * Gets query for [[TaskAssignElecStaffCompletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecStaffCompletes() {
        return $this->hasMany(TaskAssignElecStaffComplete::className(), ['task_assign_elec_staff_id' => 'id']);
    }
    
    /**
     * Gets query for [[TaskAssignElecStaffCompleteDeletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecStaffCompleteDeletes()
    {
        return $this->hasMany(TaskAssignElecStaffCompleteDelete::className(), ['task_assign_elec_staff_id' => 'id']);
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
