<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestCustomContent */

$this->title = 'Create Test Custom Content';
$this->params['breadcrumbs'][] = ['label' => 'Test Custom Contents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-custom-content-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
