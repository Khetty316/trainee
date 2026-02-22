<?php

use yii\helpers\Html;

$this->title = 'Fabrication Department';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('__fabDispatchNavBar', ['pageKey' => '2']) ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->render("_gridViewFabDispatchList", [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'viewAllColumns' => true
            ]);
            ?>
        </div>
    </div>

</div>
