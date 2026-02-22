<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypes $model */

$this->title = 'Update Ref Project Q Types: ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Ref Project Q Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->code, 'url' => ['view', 'code' => $model->code]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ref-project-qtypes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
