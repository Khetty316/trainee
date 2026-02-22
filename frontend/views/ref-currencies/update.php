<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\common\RefCurrencies */

$this->title = 'Update Ref Currencies: ' . $model->currency_id;
$this->params['breadcrumbs'][] = ['label' => 'Ref Currencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->currency_id, 'url' => ['view', 'id' => $model->currency_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ref-currencies-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
