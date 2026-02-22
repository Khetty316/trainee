<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisions */

$this->title = 'Create Project Q Revisions';
$this->params['breadcrumbs'][] = ['label' => 'Project Q Revisions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qrevisions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formProjectQRevision', [
        'model' => $model,
    ]) ?>

</div>
