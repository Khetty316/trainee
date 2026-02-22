<?php

namespace frontend\models\projectproduction\task;

use frontend\models\projectproduction\electrical\VElecStaffProduction;
use frontend\models\projectproduction\fabrication\VFabStaffProduction;
use Yii;
use common\models\User;

/**
 * This is the model class for table "task_assign_ongoing_summary".
 *
 * @property int $user_id
 * @property float|null $total_task_onhand
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 */
class TaskAssignOngoingSummary extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_ongoing_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'updated_by'], 'integer'],
            [['total_task_onhand'], 'number'],
            [['updated_at'], 'safe'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_id' => 'User ID',
            'total_task_onhand' => 'Total Task Onhand',
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

    public function beforeSave($insert) {
        $this->updated_at = new \yii\db\Expression('NOW()');
        $this->updated_by = Yii::$app->user->id;
        return parent::beforeSave($insert);
    }

    /**
     * Calculate the total ongoing tasks
     * @param type $userId
     */
    static public function calculateStaffOngoingWorkload($userId) {

        $model = (TaskAssignOngoingSummary::findOne(['user_id' => $userId])) ?? (new TaskAssignOngoingSummary());

        $elecTasks = VElecStaffProduction::find()->select(["v_elec_staff_production.*", 'panelPerPerson' => '(v_elec_staff_production.assigned_qty - IFNULL(v_elec_staff_production.assigned_complete_qty,0)) / COUNT(*)',])
                ->join("INNER JOIN", "task_assign_elec_staff", "v_elec_staff_production.task_assign_elec_id=task_assign_elec_staff.task_assign_elec_id")
                ->where(['v_elec_staff_production.user_id' => $userId, 'v_elec_staff_production.assigned_active_status' => 1, 'v_elec_staff_production.assigned_complete_date' => null])
                ->groupBy(['v_elec_staff_production.id'])
                ->sum('panelPerPerson');

        $fabTasks = VFabStaffProduction::find()->select(['v_fab_staff_production.*', 'panelPerPerson' => '(v_fab_staff_production.assigned_qty - IFNULL(v_fab_staff_production.assigned_complete_qty,0)) / COUNT(*)',])
                ->join('INNER JOIN', 'task_assign_fab_staff', 'v_fab_staff_production.task_assign_fab_id = task_assign_fab_staff.task_assign_fab_id')
                ->andWhere(['v_fab_staff_production.user_id' => $userId, 'v_fab_staff_production.assigned_active_status' => 1, 'v_fab_staff_production.assigned_complete_date' => null])
                ->groupBy('v_fab_staff_production.id')
                ->sum('panelPerPerson');

        $totalTasks = $elecTasks + $fabTasks;
        $model->total_task_onhand = $totalTasks;

        if ($model->isNewRecord && $model->total_task_onhand > 0) {
            $model->user_id = $userId;
            return $model->save();
        } else if (!$model->isNewRecord) {
            $model->update();
            return true;
        } else {
            return true;
        }
    }

    /**
     * Update each assignee's ongoing task
     * @param type $users (Array)
     * @return boolean
     */
    static function updateUserTaskOnHand($users) {
        if (empty($users)) {
            return false;
        }

        foreach ((array) $users as $userId) {
            if (!TaskAssignOngoingSummary::calculateStaffOngoingWorkload($userId)) {
                return false;
            }
        }
        return true;
    }
}
