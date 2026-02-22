<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\common\RefCurrencies */

$this->title = 'Create Ref Currencies';
$this->params['breadcrumbs'][] = ['label' => 'Ref Currencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ref-currencies-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
