<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanelItemsTemplate */

$this->title = 'Create Project Q Panel Items Template';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Panel Items Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qpanel-items-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
