<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisionsTemplate */

$this->title = 'Create Project Q Revisions Template';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Revisions Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qrevisions-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formProjectQRevisionTemplate', [
        'model' => $model,
    ]) ?>

</div>
