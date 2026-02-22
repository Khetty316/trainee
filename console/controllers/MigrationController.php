<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use frontend\models\projectproduction\electrical\TaskAssignElec;
use frontend\models\projectproduction\fabrication\TaskAssignFab;

class MigrationController extends Controller {

    public function actionOverallUpdateElec() {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $query = TaskAssignElec::find()->where(['active_sts' => 1]);

        foreach ($query->batch(50) as $taskAssignBatch) {
            foreach ($taskAssignBatch as $taskAssign) {
                $taskAssign->updateTaskCalculation();
                $taskAssign->updatePanelCalculation();

                unset($taskAssign); // free memory
            }
            echo "Batch done at " . date('H:i:s') . "\n";

            Yii::$app->db->close();
            Yii::$app->db->open();
            gc_collect_cycles();
        }

        echo "DONE\n";
    }

    public function actionOverallUpdateFab() {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $query = TaskAssignFab::find()->where(['active_sts' => 1]);

        foreach ($query->batch(50) as $taskAssignBatch) {
            foreach ($taskAssignBatch as $taskAssign) {
                $taskAssign->updateTaskCalculation();
                $taskAssign->updatePanelCalculation();

                unset($taskAssign); // free memory
            }

            echo "Batch done at " . date('H:i:s') . "\n";

            Yii::$app->db->close();
            Yii::$app->db->open();
            gc_collect_cycles();
        }

        echo "DONE\n";
    }
}
