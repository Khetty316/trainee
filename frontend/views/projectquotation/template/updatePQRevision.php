<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisionsTemplate */

$this->title = 'Update Quotation Template: ' . $model->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Template', 'url' => ['indexpqrevision']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['viewpqrevision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-qrevisions-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_formPQRevision', [
        'model' => $model,
        'currencyList' => $currencyList
    ])
    ?>

</div>
