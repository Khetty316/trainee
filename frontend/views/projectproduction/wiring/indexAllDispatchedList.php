<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
?>
<?= $this->render('__navBarWiringItem', ['pageKey' => '2']) ?>
<div class="row">
    <div class="col-12">
        <?php
        echo $this->render("_gridViewProcDispatchList", [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'viewAllColumns' => true
        ]);
        ?>
    </div>
</div>  