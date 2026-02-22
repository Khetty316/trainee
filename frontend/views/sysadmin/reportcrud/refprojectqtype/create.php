<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypes $model */

$this->title = 'Create Ref Project Q Types';
$this->params['breadcrumbs'][] = ['label' => 'Ref Project Q Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ref-project-qtypes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
