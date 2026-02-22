<?php

use yii\helpers\Html;

$this->title = 'Store Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('__storeDispatchNavBar', ['pageKey' => '3']) ?>

<div class="row">
    <div class="col-12">
        <?php
        echo $this->render("_gridViewStoreDispatched", [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        ?>
    </div>
</div>
