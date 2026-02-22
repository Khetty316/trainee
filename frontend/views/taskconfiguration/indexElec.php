<?php

use frontend\models\projectproduction\task\WorkerTaskCategories;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\projectproduction\task\WorkerTaskCategoriesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
?>
<style>
    .sticky-header {
        position: sticky!important;
        top: 60px!important;
        z-index: 50!important; /* Bootstrap's navbar is usually at 1030 */
    }
</style>
<div class="worker-task-categories-index">
    <?= $this->render('../workassignment/elec/__navbarWorkAssignment', ['pageKey' => '4']) ?>

    <p>
        <?= Html::a('Edit <i class="far fa-edit"></i>', ['update-elec'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="px-0 mb-2 col-md-7">
        <input class="form-control mr-2" id="nameFilter" type="text" placeholder="Search.."/>
    </div>
    <table class="table table-sm table-striped table-bordered col-md-7" id="myList">
        <thead class="sticky-header thead-light">
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
                    <td class="text-center col-md-2 col-sm-11"><?= !empty($mainList[$worker['id']][$tasks['code']]) ? '<i class="fas fa-check"></i>' : "" ?></td>
                    <?php
                }
                ?>
            </tr>
        <?php } ?>
        <tbody>

        </tbody>
    </table>

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