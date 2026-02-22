<?php

namespace frontend\controllers;

use Yii;
use common\modules\auth\models\AuthItem;

class ToolsController extends \yii\web\Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [AuthItem::ROLE_SystemAdmin]
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionHashpassword() {
        $string = Yii::$app->request->get("string");
        echo Yii::$app->security->generatePasswordHash($string);
    }

    public function actionGeneraterandomstring() {
        echo Yii::$app->security->generateRandomString();
    }

    public function actionGetfiles() {
        return false;
        $filePath = 'uploads/testing';
        if (file_exists($filePath)) {
            $files = \yii\helpers\FileHelper::findFiles($filePath);
            $str = "";
            foreach ($files as $key => $thefile) {
//                $thefile = str_replace("\\", "", "$thefile");
//                $this->files[$key] = $thefile;
                $thefile = str_replace('\\', '/', $thefile);
                $path_parts = pathinfo($thefile);
                $str .= $path_parts['basename'] . "<br/>";

                $mi = \frontend\models\working\mi\MasterIncomings::find()->where("index_no = '" . $path_parts['filename'] . "'")->one();
                if ($mi) {
                    echo $mi->index_no;
                    $mi->filename = $path_parts['basename'];
                    $mi->update(false);
                }
            }
        }
        return $str;
    }

    /**
     * Created on 03/01/2024
     * To update table "task_assign_elec", and update table "task_assign_elec_complete"
     * Run for 1 time only
     */
    public function actionMigrateTaskCompleteElec() {
        $taskList = \frontend\models\projectproduction\electrical\TaskAssignElec::find()
                ->leftJoin('task_assign_elec_complete', 'task_assign_elec.id = task_assign_elec_complete.task_assign_elec_id')
                ->where('task_assign_elec.complete_date IS NOT NULL')
                ->andWhere('task_assign_elec.deactivated_by IS NULL')
                ->andWhere('task_assign_elec_complete.id IS NULL')
                ->all();

        foreach ($taskList as $task) {
            $task->complete_qty = $task->quantity;
            $task->update();
            $completeTask = new \frontend\models\projectproduction\electrical\TaskAssignElecComplete();
            $completeTask->task_assign_elec_id = $task->id;
            $completeTask->quantity = $task->complete_qty;
            $completeTask->complete_date = $task->complete_date;
            $completeTask->save();
        }
    }

    /**
     * Created on 03/01/2024
     * To update table "task_assign_fab", and update table "task_assign_fab_complete"
     * Run for 1 time only
     */
    public function actionMigrateTaskCompleteFab() {

        $taskList = \frontend\models\projectproduction\fabrication\TaskAssignFab::find()
                ->leftJoin('task_assign_fab_complete', 'task_assign_fab.id = task_assign_fab_complete.task_assign_fab_id')
                ->where('task_assign_fab.complete_date IS NOT NULL')
                ->andWhere('task_assign_fab.deactivated_by IS NULL')
                ->andWhere('task_assign_fab_complete.id IS NULL')
                ->all();

        foreach ($taskList as $task) {
            $task->complete_qty = $task->quantity;
            $task->update();
            $completeTask = new \frontend\models\projectproduction\fabrication\TaskAssignFabComplete();
            $completeTask->task_assign_fab_id = $task->id;
            $completeTask->quantity = $task->complete_qty;
            $completeTask->complete_date = $task->complete_date;
            $completeTask->save();
        }
    }

    /**
     * Delete busbar
     */
    public function actionDeleteUnwantedBusbar() {

        $taskList = \frontend\models\ProjectProduction\electrical\ProductionElecTasks::find()
                ->join("INNER JOIN", "project_production_panels", "production_elec_tasks.`proj_prod_panel_id`=project_production_panels.id and finalized_by is null")
                ->where(["elec_task_code" => "busbar"])
                ->all();

        foreach ($taskList as $task) {
            $task->delete();
        }
    }

    /**
     * Delete busbar
     */
    public function actionRecalibrateWorkerrole($type = "") {
        if (empty($type)) {
            return "need something";
        }
        $assignedStaffList = null;
        $workerRoleAssignlist = \frontend\models\projectproduction\task\WorkerTaskCategories::find()
                ->where(['task_type' => $type])
                ->asArray()
                ->all();

        $manipulatedWorkerRole = null;
        foreach ($workerRoleAssignlist as $workerRole) {
            $manipulatedWorkerRole[$workerRole['user_id']][$workerRole['task_code']] = 1;
        }

        if ($type == "fab") {
            $assignedStaffList = (new \yii\db\Query())
                            ->select([
                                'c.fab_task_code as task_code',
                                'a.user_id',
                                'a.id as task_assign_staff_id'
                            ])
                            ->from(['a' => 'task_assign_fab_staff'])
                            ->join('JOIN', ['b' => 'task_assign_fab'], 'a.task_assign_fab_id = b.id')
                            ->join('JOIN', ['c' => 'production_fab_tasks'], 'b.prod_fab_task_id = c.id')->all();

            foreach ($assignedStaffList as $assignedStaff) {
                if (isset($manipulatedWorkerRole[$assignedStaff['user_id']][$assignedStaff['task_code']])) {
                    
                } else {
                    \frontend\models\ProjectProduction\fabrication\TaskAssignFabStaff::findOne($assignedStaff['task_assign_staff_id'])->delete();
                }
            }
        } else if ($type == "elec") {
            $assignedStaffList = (new \yii\db\Query())
                            ->select([
                                'c.elec_task_code as task_code',
                                'a.user_id',
                                'a.id as task_assign_staff_id'
                            ])
                            ->from(['a' => 'task_assign_elec_staff'])
                            ->join('JOIN', ['b' => 'task_assign_elec'], 'a.task_assign_elec_id = b.id')
                            ->join('JOIN', ['c' => 'production_elec_tasks'], 'b.prod_elec_task_id = c.id')->all();

            foreach ($assignedStaffList as $assignedStaff) {
                if (isset($manipulatedWorkerRole[$assignedStaff['user_id']][$assignedStaff['task_code']])) {
                    
                } else {
                    \frontend\models\ProjectProduction\electrical\TaskAssignElecStaff::findOne($assignedStaff['task_assign_staff_id'])->delete();
                }
            }
        }

        return "Cleared";

//
//        foreach ($taskList as $task) {
//            $task->delete();
//        }
    }

    public function actionReload() {
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
        $model = new \frontend\models\projectquotation\ProjectQPanels();
        $model->panel_type = "inv";
        return $model->panel_type;
    }

}
