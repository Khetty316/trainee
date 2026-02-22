<?php

use yii\helpers\Html;

$this->title = $model->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Template', 'url' => ['indexpqrevision']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['viewpqrevision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Edit', 'url' => ['update-p-q-template-revision-panel', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Edit Revision Detail'];
?>
<div class="project-qrevisions-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_formPQTemplateRevisionDetail', [
        'model' => $model,
        'currencyList' => $currencyList
    ])
    ?>

</div>
