<?php

use yii\helpers\Html;
?>
<div class="work-assignment-master-index">

    <?= $this->render('__navbarWorkAssignment', ['pageKey' => '3']) ?>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

    <?php
    echo $this->render('__gridviewFabTaskAssigned', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'toIndex' => 'all'
    ])
    ?>

</div>
