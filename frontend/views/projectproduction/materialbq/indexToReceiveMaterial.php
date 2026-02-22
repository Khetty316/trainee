<?php

use yii\helpers\Html;

$this->title = 'Fabrication Department';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('__fabDispatchNavBar', ['pageKey' => '1'])  ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?php
            echo $this->render("_gridViewFabDispatchList", [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
            ?>
        </div>
    </div>

</div>
