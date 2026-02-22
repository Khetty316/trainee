<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQTypes */

$this->title = 'Create Project Q Types';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qtypes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
