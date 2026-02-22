<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanels */

$this->title = 'Create Project Q Panels';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Panels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qpanels-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
