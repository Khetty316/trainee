<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanelsTemplate */

$this->title = 'Create Project Q Panels Template';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Panels Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qpanels-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
