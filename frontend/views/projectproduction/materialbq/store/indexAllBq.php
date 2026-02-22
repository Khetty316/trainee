<?php

use yii\helpers\Html;

$this->title = 'Store Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('__storeDispatchNavBar', ['pageKey' => '2']) ?>

<div class="row">
    <div class="col-12">
        <?php
        echo $this->render("_gridViewStoreDispatch", [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'isMain' => true
        ]);
        ?>
    </div>
</div>

