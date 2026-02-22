<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\pettyCash\PettyCashRequestMaster */

?>
<div class="petty-cash-request-master-create">

    <!--<h1><?php //= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_formReplenishment', [
        'model' => $model
    ]) ?>

</div>
