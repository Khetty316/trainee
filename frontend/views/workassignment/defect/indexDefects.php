<?php

use yii\helpers\Html;

$this->title = 'Panel Defect Complaints';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-defects">

    <?php // echo $this->render('__navbarWorkAssignment', ['pageKey' => '3'])  ?>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

    <?php
    echo $this->render('__gridviewDefectAll', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ]);
    ?>

</div>
