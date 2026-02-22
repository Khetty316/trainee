<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestCustomContent */

$this->title = 'Update Test Custom Content: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Test Custom Contents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="test-custom-content-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
