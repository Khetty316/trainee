<?php

use yii\helpers\Html;
?>
<div class="index-defects">

    <?= $this->render('__navbarTask', ['pageKey' => '3']) ?>

    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    <?php
    echo $this->render('__gridviewDefectAll', [
        'defectLists' => $defectLists,
    ]);
    ?>

</div>
