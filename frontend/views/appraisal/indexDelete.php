<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\appraisal\AppraisalMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Delete Staff Appraisal";
$this->params['breadcrumbs'][] = "HR Appraisal";
$this->params['breadcrumbs'][] = ['label' => "Appraisal List", 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Appraisal - $main->index", 'url' => ['index-master', 'id' => $main->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="app">
    <div class="pl-2">
        <h3>Delete Staff Appraisal - <?= $main->index ?></h3>
        <p><?= Html::encode($main->description) ?></p>
    </div>
    <div class="row m-1 p-1">
        <div class="col pl-0">
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', "?mainId=$main->id", ['class' => 'btn btn-primary']) ?>    
        </div>
        <div class="col pr-0">
            <button class="btn btn-danger float-right" @click="confirmAndDeleteAppraisal">Delete Staff Appraisal for Selected IDs</button>
        </div>
    </div>
    <div class=" pl-0 pr-3">
        <?php
        echo $this->render('_staffList', [
            'employmentTypeList' => $employmentTypeList
        ]);
        ?>
    </div>
</div>


<script>
    window.models = <?= $users ?>;
    window.mainId = <?= $main->id ?>;
    window.csrfToken = '<?= Yii::$app->request->getCsrfToken() ?>';

</script>
<script src="\js\vueTable.js"></script>
