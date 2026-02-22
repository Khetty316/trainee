<?php

namespace frontend\models\projectproduction\fabrication;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use common\models\myTools\MyFormatter;
use frontend\models\projectproduction\fabrication\TaskAssignFabStaff;
use frontend\models\projectproduction\task\TaskAssignOngoingSummary;
use frontend\models\projectproduction\task\TaskAssignment;
use frontend\models\projectproduction\fabrication\TaskAssignFabComplete;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use common\models\User;
use frontend\models\ProjectProduction\fabrication\TaskAssignFabStaffComplete;

/**
 * This is the model class for table "task_assign_fab".
 *
 * @property int $id
 * @property int|null $proj_prod_panel_id
 * @property int|null $prod_fab_task_id
 * @property float|null $quantity
 * @property float|null $complete_qty
 * @property string|null $start_date
 * @property string|null $current_target_date
 * @property string|null $complete_date
 * @property string|null $comments
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $active_sts
 * @property int|null $deactivated_by
 * @property string|null $deactivated_at
 * @property int|null $complete_by
 * @property string|null $complete_at
 *
 * @property ProjectProductionPanels $projProdPanel
 * @property ProductionFabTasks $prodFabTask
 * @property TaskAssignFabComplete[] $taskAssignFabCompletes
 * @property TaskAssignFabCompleteDelete[] $taskAssignFabCompleteDeletes
 * @property TaskAssignFabStaff[] $taskAssignFabStaff
 * @property User[] $users
 * @property TaskAssignFabStaffCompleteDelete[] $taskAssignFabStaffCompleteDeletes
 */
class TaskAssignFab extends \yii\db\ActiveRecord {

    public $tempTaskName;
    public $extraComment;
    public $taskCode; // for filter purpose
    public $panelCode; // for filter purpose
    public $assignee;
    public $staffIds = [];
    public $staffIdString;
    public $addComplete;
    public $remark_update_target_date;
    public $new_target_date;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_fab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'prod_fab_task_id', 'quantity', 'complete_qty', 'start_date', 'complete_date', 'comments', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deactivated_by', 'deactivated_at', 'complete_by', 'complete_at'], 'default', 'value' => null],
            [['active_sts'], 'default', 'value' => 1],
            [['proj_prod_panel_id', 'prod_fab_task_id', 'created_by', 'updated_by', 'active_sts', 'deactivated_by', 'complete_by'], 'integer'],
            [['quantity', 'complete_qty'], 'number'],
            [['start_date', 'complete_date', 'created_at', 'updated_at', 'deactivated_at', 'complete_at', 'taskCode', 'panelCode', 'extraComment', 'staffIdString'], 'safe'],
            [['comments'], 'string'],
            [['current_target_date'], 'required'],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::class, 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['prod_fab_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionFabTasks::class, 'targetAttribute' => ['prod_fab_task_id' => 'id']],
//            [['remark_update_target_date', 'new_target_date'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'prod_fab_task_id' => 'Prod Fab Task ID',
            'quantity' => 'Quantity',
            'complete_qty' => 'Complete Qty',
            'start_date' => 'Start Date',
            'current_target_date' => 'Task Target Completion Date',
            'complete_date' => 'Complete Date',
            'comments' => 'Comments',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'active_sts' => 'Active?',
            'deactivated_by' => 'Deactivated By',
            'deactivated_at' => 'Deactivated At',
            'extraComment' => 'Add on comment',
            'complete_by' => 'Complete By',
            'complete_at' => 'Complete At',
            'new_target_date' => 'New Task Target Completion Date',
        ];
    }

    /**
     * Gets query for [[ProdFabTask]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdFabTask() {
        return $this->hasOne(ProductionFabTasks::class, ['id' => 'prod_fab_task_id']);
    }

    /**
     * Gets query for [[ProjProdPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanel() {
        return $this->hasOne(ProjectProductionPanels::class, ['id' => 'proj_prod_panel_id']);
    }

    /**
     * Gets query for [[TaskAssignFabCompletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabCompletes() {
        return $this->hasMany(TaskAssignFabComplete::class, ['task_assign_fab_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignFabCompleteDeletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabCompleteDeletes() {
        return $this->hasMany(TaskAssignFabCompleteDelete::className(), ['task_assign_fab_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignFabStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabStaff() {
        return $this->hasMany(TaskAssignFabStaff::class, ['task_assign_fab_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('task_assign_fab_staff', ['task_assign_fab_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignFabStaffCompleteDeletes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabStaffCompleteDeletes() {
        return $this->hasMany(TaskAssignFabStaffCompleteDelete::className(), ['task_assign_fab_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        if (!empty($this->complete_date)) {
            $this->complete_date = MyFormatter::fromDateRead_toDateSQL($this->complete_date);
        }
        if (!empty($this->start_date)) {
            $this->start_date = MyFormatter::fromDateRead_toDateSQL($this->start_date);
        }
        if (!empty($this->current_target_date)) {
            $this->current_target_date = MyFormatter::fromDateRead_toDateSQL($this->current_target_date);
        }
        if (!empty($this->new_target_date)) {
            $this->new_target_date = MyFormatter::fromDateRead_toDateSQL($this->new_target_date);
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave($updateUsersTaskOnHand = true) {
        $this->active_sts = 1;
        if (!$this->save()) {
            \common\models\myTools\Mydebug::dumpFileA($this->errors);
            return false;
        }

        if (!$this->saveUsers()) {
            return false;
        }

        if ($updateUsersTaskOnHand && !TaskAssignOngoingSummary::updateUserTaskOnHand($this->staffIds)) {
            return false;
        }

        return true;
    }

    /**
     *  PRIVATE FUNCTION to save assigned user(s)
     * @param type $users
     * @return boolean
     */
//    private function saveUsers() {
//        $userIds = $this->staffIds;
//        if (empty($userIds)) {
//            return false;
//        }
//        TaskAssignFabStaff::deleteAll('task_assign_fab_id=' . $this->id);
//        foreach ((array) $userIds as $userId) {
//            $model = new TaskAssignFabStaff();
//            $model->user_id = $userId;
//            $model->task_assign_fab_id = $this->id;
//            if (!$model->save()) {
//                return false;
//            }
//        }
//
//        return true;
//    }

    private function saveUsers() {
        $userIds = (array) $this->staffIds;
        if (empty($userIds)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Fetch all existing staff for this task
            $existingTaskAssignFabStaff = TaskAssignFabStaff::find()
                    ->where(['task_assign_fab_id' => $this->id])
                    ->all();

            // Normalize types (important for array_diff)
            $existingUserIds = array_map('intval', array_column($existingTaskAssignFabStaff, 'user_id'));
            $userIds = array_map('intval', $userIds);

            // Compare sets
            $newTaskAssignFabStaff = array_diff($userIds, $existingUserIds);          // New to add
            $deleteExistingTaskAssignFabStaff = array_diff($existingUserIds, $userIds); // To delete
            // Add new assignment
            $newAssignment = false;
            foreach ($newTaskAssignFabStaff as $userId) {
                $model = new TaskAssignFabStaff();
                $model->user_id = $userId;
                $model->task_assign_fab_id = $this->id;

                if (!$model->save()) {
                    throw new \Exception("Failed to save new staff assignment for user ID {$userId}");
                }

                $this->updateFabStaffCompleteQty($model);
                $newAssignment = true;
            }

            // If new staff added, revert complete date and update qty
            if ($newAssignment) {
                if (!$this->revertCompleteDate()) {
                    throw new \Exception("Failed to revert complete date value");
                }
            }

            foreach ($existingTaskAssignFabStaff as $existing) {
                $this->updateFabStaffCompleteQty($existing); // refresh existing qty
            }

            // Handle deletion
            foreach ($existingTaskAssignFabStaff as $existing) {
                if (!in_array((int) $existing->user_id, $deleteExistingTaskAssignFabStaff)) {
                    continue; // keep this user
                }

                // Check if this staff has completion records
                $hasCompletion = TaskAssignFabStaffComplete::find()
                        ->where(['task_assign_fab_staff_id' => $existing->id])
                        ->exists();

                if (!$hasCompletion) {
                    // No completion — safe to delete only within this task
                    TaskAssignFabStaff::deleteAll([
                        'user_id' => $existing->user_id,
                        'task_assign_fab_id' => $this->id, // ensure only current task’s staff are deleted
                    ]);
                }

                $this->updateFabStaffCompleteQty($existing);
            }


            $currentAssignedStaffs = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $this->id])->all();
            foreach ($currentAssignedStaffs as $current) {
                $this->updateFabStaffCompleteQty($current);
            }

            $completeQty = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $this->id])->sum('complete_qty_over_total_staff');

            if (round($completeQty, 2) == $this->quantity) {
                $this->complete_date = MyFormatter::fromDateRead_toDateSQL(date('Y-m-d'));
            }

            if (!$this->save()) {
                return false;
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("saveUsers() failed: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Deactivate the task, and recalculate the percentage
     */
    public function deactivateTask() {
        $this->active_sts = 0;
        $this->deactivated_at = new \yii\db\Expression("NOW()");
        $this->deactivated_by = Yii::$app->user->id;
        $transaction = Yii::$app->db->beginTransaction();

        // Save task status
        if (!$this->save()) {
            $transaction->rollBack();
            return false;
        }

        if (!$this->bulkUpdateThisUserTaskOnHand()) {
            $transaction->rollBack();
            return false;
        }

        if (!$this->updateTaskAndPanelCalculation()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    /**
     * PRIVATE FUNCTION
     * check through the task, up to panel when there's any update
     * check: Task - Total Assign, Task - Total Completed, Panel - Progress Percentage, Panel - Status
     * @return boolean
     */
    public function updateTaskCalculation() {

//        $task = $this->prodFabTask;
        $task = ProductionFabTasks::find()
                ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = production_fab_tasks.fab_task_code') // include join condition if needed
                ->where(['id' => $this->prod_fab_task_id])
                ->andWhere(['ref.active_sts' => 1])
                ->one();

        if (!empty($task)) {
            // Check if assign qty <= total qty in Task List
            if (!$task->checkAndUpdateAssign()) {
                \common\models\myTools\Mydebug::dumpFileA("Error at checkAndUpdateAssign");
                return false;
            }

            // Check if assign qty <= total qty in Task List
            if (!$task->checkAndUpdateComplete()) {
                \common\models\myTools\Mydebug::dumpFileA("Error at checkAndUpdateComplete");
                return false;
            }
        }

        return true;
    }

    public function updatePanelCalculation() {

        $panel = $this->projProdPanel;
        $project = $panel->projProdMaster;

        // Update assigned percentage or panels
        if (!$panel->updateFabProgressPercent()) {
            \common\models\myTools\Mydebug::dumpFileA("Error at updateFabProgressPercent");
            return false;
        }

        if (!$project->updateAvgFabProgressPercent()) {
            \common\models\myTools\Mydebug::dumpFileA("Error at updateAvgFabProgressPercent");
            return false;
        }

        if (!$panel->checkPanelFabWorkStatus()) {
            \common\models\myTools\Mydebug::dumpFileA("Error at checkPanelFabWorkStatus");
            return false;
        }

        return true;
    }

    public function updateTaskAndPanelCalculation() {

        $task = $this->prodFabTask;
        $panel = $this->projProdPanel;
        $project = $panel->projProdMaster;

        // Check if assign qty <= total qty in Task List
        if (!$task->checkAndUpdateAssign()) {
            return false;
        }

        // Check if assign qty <= total qty in Task List
        if (!$task->checkAndUpdateComplete()) {
            return false;
        }
        // Update assigned percentage or panels
        if (!$panel->updateFabProgressPercent()) {
            return false;
        }

        if (!$project->updateAvgFabProgressPercent()) {
            return false;
        }

        if (!$panel->checkPanelFabWorkStatus()) {
            return false;
        }

        return true;
    }

    /**
     * Process in Bulk
     * @param type $taskAssignElecId
     * @return boolean
     */
    public function bulkUpdateThisUserTaskOnHand() {
        $allAssignee = array_column(TaskAssignFabStaff::find()->select(['user_id'])->where(['task_assign_fab_id' => $this->id])->asArray()->all(), 'user_id');
        if (!TaskAssignOngoingSummary::updateUserTaskOnHand($allAssignee)) {
            return false;
        } else {
            return true;
        }
    }

//    public function updateCompleteQtyInAssignment() {
//        $completed = TaskAssignFabComplete::find()->where(['task_assign_fab_id' => $this->id])->sum('quantity');
//        $this->complete_qty = $completed;
//
//        return $this->update(false);
//    }

    public function updateCompleteQtyInAssignment() {
        $qtyCompletedByAllStaff = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $this->id])->sum('complete_qty_over_total_staff');
        $qtyCompletedByAllStaff2Decimal = round($qtyCompletedByAllStaff, 2);
        if ($qtyCompletedByAllStaff2Decimal > $this->quantity) {
            \common\models\myTools\Mydebug::dumpFileW($qtyCompletedByAllStaff2Decimal);
            return false;
        } else {
            $this->complete_qty = $qtyCompletedByAllStaff2Decimal;
        }

        if (!$this->save(false)) {
            \common\models\myTools\Mydebug::dumpFileW($this->getErrors());
            return false;
        }

        return true;
    }

//    public function saveCompleteTask($taskAssignId, $addVal, $addComment) {
//        $completePanel = new TaskAssignFabComplete();
//        $completePanel->task_assign_fab_id = $taskAssignId;
//        $completePanel->complete_date = date('Y-m-d');
//        $completePanel->quantity = $addVal;
//        $completePanel->comment = $addComment;
//
//        return $completePanel->save();
////        if (!$completePanel->save()) {
////            return false;
////        }
////
////        $completedTaskTrail = new TaskAssignFabCompleteTrail();
////        $completedTaskTrail->attributes = $completePanel->attributes;
////        $completedTaskTrail->task_assign_fab_complete_id = $completePanel->id;
////        $completedTaskTrail->complete_created_at = $completePanel->created_at;
////        $completedTaskTrail->complete_created_by = $completePanel->created_by;
////        
////        return $completedTaskTrail->save();
//    }

    public function saveCompleteTask($addComment, $post) {
        $allSaved = true;

        foreach ($post as $taskAssignFabStaffId => $quantity) {
            $completeByStaff = new TaskAssignFabStaffComplete();
            $completeByStaff->task_assign_fab_staff_id = $taskAssignFabStaffId;
            $completeByStaff->complete_date = date('Y-m-d');
            $completeByStaff->quantity = $quantity;
            $completeByStaff->comment = $addComment;

            if (!$completeByStaff->save()) {
                \common\models\myTools\Mydebug::dumpFileW($completeByStaff->getErrors());
                $allSaved = false;
            }
        }

        return $allSaved;
    }

    //Kindly ensure the migrateUpdateFabStaffQty function is also updated if modifications are made to this function
    public function updateFabStaffQty($taskAssignId, $post) {
        $allSaved = true;

        foreach ($post as $taskAssignFabStaffId => $quantity) {
            $taskAssignFabStaff = TaskAssignFabStaff::findOne(['id' => $taskAssignFabStaffId, 'task_assign_fab_id' => $taskAssignId]);
            if (!$taskAssignFabStaff) {
                \common\models\myTools\Mydebug::dumpFileW($taskAssignFabStaff);
                return false;
            }

            if (!$this->updateFabStaffCompleteQty($taskAssignFabStaff)) {
                $allSaved = false;
                return false;
            }
        }
        return $allSaved;
    }

    public function migrateUpdateFabStaffQty($taskAssignId, $post) {
        $allSaved = true;

        foreach ($post as $taskAssignFabStaffId) {
            $taskAssignFabStaff = TaskAssignFabStaff::findOne(['id' => $taskAssignFabStaffId, 'task_assign_fab_id' => $taskAssignId]);
            if (!$taskAssignFabStaff) {
                return false;
            }

            if (!$this->migrateUpdateFabStaffCompleteQty($taskAssignFabStaff)) {
                return false;
            }
        }
        return $allSaved;
    }

//    public function addCompletePanelFab($post) {
//        $maxVal = $this->quantity - $this->complete_qty;
//        $addVal = $post['TaskAssignFab']['addComplete'] ?? 0;
//        if ($addVal > $maxVal) {
//            \common\models\myTools\FlashHandler::err('Completion value exceeds the allowable range.');
//            return 'Completion value exceeds the allowable range.';
//        }
//
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            if (!$this->saveCompleteTask($this->id, $addVal, $post['TaskAssignFab']['extraComment'])) {
//                $transaction->rollBack();
//                \common\models\myTools\FlashHandler::err('Failed to save complete task.');
//                return 'Failed to save complete task.';
//            }
//
//            if (!$this->updateCompleteQtyInAssignment()) {
//                $transaction->rollBack();
//                \common\models\myTools\FlashHandler::err('Failed to update completion quantity.');
//                return 'Failed to update completion quantity.';
//            }
//
//            if (!$this->updateTaskCalculation()) {
//                $transaction->rollBack();
//                \common\models\myTools\FlashHandler::err('Failed to update task calculation.');
//                return 'Failed to update task calculation.';
//            }
//
//            if (!$this->bulkUpdateThisUserTaskOnHand()) {
//                $transaction->rollBack();
//                \common\models\myTools\FlashHandler::err('Failed to update user task on hand.');
//                return 'Failed to update user task on hand.';
//            }
//
//            if (!$this->updateTaskAndPanelCalculation()) {
//                $transaction->rollBack();
//                \common\models\myTools\FlashHandler::err('Failed to update task and panel calculation.');
//                return 'Failed to update task and panel calculation.';
//            }
//
//            \common\models\myTools\FlashHandler::success('Completed panels added.');
//
//            if ($this->complete_qty == $this->quantity && !$this->complete_date) {
//                if (!$this->updateCompleteDate()) {
//                    $transaction->rollBack();
//                    \common\models\myTools\FlashHandler::err('Failed to update complete date.');
//                    return 'Failed to update complete date.';
//                }
//            } else if ($this->complete_qty < $this->quantity) {
//                if (!$this->revertCompleteDate()) {
//                    $transaction->rollBack();
//                    \common\models\myTools\FlashHandler::err('Failed to revert complete date.');
//                    return 'Failed to revert complete date.';
//                }
//            }
//
//            $transaction->commit();
//            return true;
//        } catch (\Exception $exc) {
//            $transaction->rollBack();
//            \common\models\myTools\FlashHandler::err('Panels not updated.');
//            return 'Panels not updated.';
//        }
//    }

    public function addCompletePanelFab($post) {
        $maxVal = $this->quantity - $this->complete_qty;
        $totalCompletedPanelAllStaff = 0;
        $totalAssignedStaff = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $this->id])->count();
        foreach ($post['staffComplete'] as $taskAssignFabStaffId => $quantity) {
            $totalCompletedPanelAllStaff += (int) $quantity;
        }

        $addVal = round(($totalCompletedPanelAllStaff / $totalAssignedStaff), 2);
        if ($addVal > ($maxVal + 0.001)) {
            \common\models\myTools\FlashHandler::err('Completion value exceeds the allowable range.');
            return 'Completion value exceeds the allowable range.';
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->saveCompleteTask($post['TaskAssignFab']['extraComment'], $post['staffComplete'])) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to save complete task');
                return 'Failed to save complete task';
            }

            if (!$this->updateFabStaffQty($this->id, $post['staffComplete'])) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to update complete task staff');
                return 'Failed to update complete task staff';
            }

            if (!$this->updateCompleteQtyInAssignment()) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to update completion quantity.');
                return 'Failed to update completion quantity.';
            }

            if (!$this->updateTaskCalculation()) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to update task calculation.');
                return 'Failed to update task calculation.';
            }

            if (!$this->bulkUpdateThisUserTaskOnHand()) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to update user task on hand.');
                return 'Failed to update user task on hand.';
            }

            if (!$this->updateTaskAndPanelCalculation()) {
                $transaction->rollBack();
                \common\models\myTools\FlashHandler::err('Failed to update task and panel calculation.');
                return 'Failed to update task and panel calculation.';
            }

            \common\models\myTools\FlashHandler::success('Completed panels added.');

            if ($this->complete_qty == $this->quantity && !$this->complete_date) {
                if (!$this->updateCompleteDate()) {
                    $transaction->rollBack();
                    \common\models\myTools\FlashHandler::err('Failed to update complete date.');
                    return 'Failed to update complete date.';
                }
            } else if ($this->complete_qty < $this->quantity) {
                if (!$this->revertCompleteDate()) {
                    $transaction->rollBack();
                    \common\models\myTools\FlashHandler::err('Failed to revert complete date.');
                    return 'Failed to revert complete date.';
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            \common\models\myTools\Mydebug::dumpFileW($exc);
            \common\models\myTools\FlashHandler::err('Panels not updated.');
            return 'Panels not updated.';
        }
    }

    public function updateCompleteDate() {
        $this->complete_date = MyFormatter::fromDateRead_toDateSQL(date('Y-m-d'));
        $this->complete_by = Yii::$app->user->id;
        $this->complete_at = new \yii\db\Expression('NOW()');
        \common\models\myTools\FlashHandler::success('All panels completed.');
        return $this->update(false);
    }

    public function revertCompleteDate() {
        $this->complete_date = null;
        $this->complete_by = null;
        $this->complete_at = null;
        $this->update(false);
        return true;
    }

    public function updateCompleteQtyAfterRevert($taskAssignFabStaff, $useTransaction = true) {
        if ($useTransaction) {
            $transaction = Yii::$app->db->beginTransaction();
        }

        try {
            if (!$this->updateFabStaffQtyAfterRevert($taskAssignFabStaff)) {
                if ($useTransaction)
                    $transaction->rollBack();
                return false;
            }

            if (!$this->updateCompleteQtyInAssignment()) {
                if ($useTransaction)
                    $transaction->rollBack();
                return false;
            }

            if (!$this->updateTaskCalculation()) {
                if ($useTransaction)
                    $transaction->rollBack();
                return false;
            }

            if (!$this->updateTaskAndPanelCalculation()) {
                if ($useTransaction)
                    $transaction->rollBack();
                return false;
            }

            if (!$this->revertCompleteDate()) {
                if ($useTransaction)
                    $transaction->rollBack();
                return false;
            }

            if ($useTransaction) {
                $transaction->commit();
            }
            return true;
        } catch (\Exception $exc) {
            if ($useTransaction) {
                $transaction->rollBack();
            }
            return false;
        }
    }

    public function updateFabStaffQtyAfterRevert($taskAssignFabStaff) {
        if (!$taskAssignFabStaff) {
            return false;
        }

        if (!$this->updateFabStaffCompleteQty($taskAssignFabStaff)) {
            return false;
        }

        return true;
    }

    private function updateFabStaffCompleteQty($taskAssignFabStaff) {
        if (!$taskAssignFabStaff) {
            \common\models\myTools\Mydebug::dumpFileW($taskAssignFabStaff);
            return false;
        }

        $totalCompletedTask = TaskAssignFabStaffComplete::find()->where(['task_assign_fab_staff_id' => $taskAssignFabStaff->id])->sum('quantity');
        if ($totalCompletedTask > $this->quantity) {
            \common\models\myTools\Mydebug::dumpFileW($totalCompletedTask);
            return false;
        }

        $taskAssignFabStaff->complete_qty = $totalCompletedTask ?: 0;

        $totalAssignedStaff = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $taskAssignFabStaff->task_assign_fab_id])->count();
        $taskAssignFabStaff->complete_qty_over_total_staff = round(($taskAssignFabStaff->complete_qty / $totalAssignedStaff), 4);
        if ($totalCompletedTask == $this->quantity) {
            $taskAssignFabStaff->complete_date = MyFormatter::fromDateRead_toDateSQL(date('Y-m-d'));
        }

        if (!$taskAssignFabStaff->save()) {
            \common\models\myTools\Mydebug::dumpFileW($taskAssignFabStaff->getErrors());
            return false;
        }

        return true;
    }

    private function migrateUpdateFabStaffCompleteQty($taskAssignFabStaff) {
        if (!$taskAssignFabStaff) {
            \common\models\myTools\Mydebug::dumpFileW($taskAssignFabStaff);
            return false;
        }

        $totalCompletedTask = TaskAssignFabStaffComplete::find()->where(['task_assign_fab_staff_id' => $taskAssignFabStaff->id])->sum('quantity');
        if ($totalCompletedTask > $this->quantity) {
            \common\models\myTools\Mydebug::dumpFileW($totalCompletedTask);
            return false;
        }

        $taskAssignFabStaff->complete_qty = $totalCompletedTask ?: 0;

        $totalAssignedStaff = TaskAssignFabStaff::find()->where(['task_assign_fab_id' => $taskAssignFabStaff->task_assign_fab_id])->count();
        $taskAssignFabStaff->complete_qty_over_total_staff = round(($taskAssignFabStaff->complete_qty / $totalAssignedStaff), 4);
        if ($totalCompletedTask == $this->quantity && $this->complete_date !== null) {
            $taskAssignFabStaff->complete_date = $this->complete_date;
        }

        if (!$taskAssignFabStaff->save()) {
            \common\models\myTools\Mydebug::dumpFileW($taskAssignFabStaff->getErrors());
            return false;
        }

        return true;
    }

    public function updatePanelDefectStaff() {
        $taskAssignedStaffs = $this->taskAssignFabStaff;
        if (empty($taskAssignedStaffs)) {
            return true;
        }

        $currentStaffIds = array_column($taskAssignedStaffs, 'user_id');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $productionFabTasksErrors = ProductionFabTasksError::find()->where(['production_fab_task_id' => $this->prod_fab_task_id])->all();

            foreach ($productionFabTasksErrors as $error) {
                /** Remove unassigned staff */
                ProductionFabTasksErrorStaff::deleteAll([
                    'and',
                    ['production_fab_tasks_error_id' => $error->id],
                    ['not in', 'staff_id', $currentStaffIds]
                ]);

                /** Add newly assigned staff */
                foreach ($currentStaffIds as $staffId) {

                    $exists = ProductionFabTasksErrorStaff::find()->where(['production_fab_tasks_error_id' => $error->id, 'staff_id' => $staffId])->exists();
                    if (!$exists) {
                        $taskErrorStaff = new ProductionFabTasksErrorStaff([
                            'production_fab_tasks_error_id' => $error->id,
                            'staff_id' => $staffId,
                            'is_read' => 1,
                            'read_at' => null,
                        ]);

                        if (!$taskErrorStaff->save()) {
                            $transaction->rollBack();
                            return false;
                        }
                    }
                }

                /** Sync complaint status */
                $unreadCount = ProductionFabTasksErrorStaff::find()->where(['production_fab_tasks_error_id' => $error->id, 'is_read' => 1])->count();

                $newStatus = ($unreadCount == 0) ? 2 : 1;
                $error->is_read = $newStatus;
                $error->save(false);
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), 'FAB_DEFECT_UPDATE');
            return false;
        }
    }
}
