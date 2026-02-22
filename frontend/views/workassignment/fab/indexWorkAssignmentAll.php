<?php

use yii\helpers\Html;

?>
<div class="work-assignment-master-index">

    <?= $this->render('__navbarWorkAssignment', ['pageKey' => '3']) ?>

    <p>
        <?= Html::a('New Work Assignment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    $this->render("_gridviewWorkAssignment", [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ])
    ?>


</div>
