<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->params['breadcrumbs'][] = ['label' => 'Fabrication Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Task Configuration', 'url' => ['/task-configuration/index-fab']];
$this->params['breadcrumbs'][] = 'Update';
?>
<style>
    .sticky-header {
        position: sticky!important;
        top: 60px!important;
        z-index: 50!important; /* Bootstrap's navbar is usually at 1030 */
    }
</style>
<div class="worker-task-categories-update">

    <?php $form = ActiveForm::begin(); ?>
    <div class="px-0 mb-2 col-md-7">
        <input class="form-control mr-2" id="nameFilter" type="text" placeholder="Search.."/>
    </div>
    <table class="table table-sm table-striped table-bordered col-md-7"  id="myList">
        <?php
        /*
          'workerList' => $workerList,
          'workTasks' => $workTasks,
          'mainList' => $mainList
         *          */
        ?>
        <thead class="thead-light sticky-header">
            <tr>
                <th>Staff</th>
                <?php
                foreach ($workTasks as $tasks) {
                    ?>
                    <th class="text-center"><?= $tasks['name'] ?></th>
                    <?php
                }
                ?>
            </tr>
        </thead>
        <?php foreach ($workerList as $worker) { ?>
            <tr>
                <td class="tdnowrap"><?= $worker['fullname'] ?></td>
                <?php
                foreach ($workTasks as $tasks) {
                    ?>
                    <td class="text-center col-md-2 col-sm-1">
                        <?php //= !empty($mainList[$worker['id']][$tasks['code']]) ? true : "" ?>
                        <?= Html::checkbox("taskAllow[{$worker['id']}][{$tasks['code']}]", ($mainList[$worker['id']][$tasks['code']] ?? false),['class'=>'form-control']) ?>
                    </td>
                    <?php
                }
                ?>
            </tr>
        <?php } ?>
        <tbody>

        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    $(function () {
        $("#nameFilter").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myList tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>