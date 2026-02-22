<?php

use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->revision_description, 'url' => ['/projectqrevision/view-project-q-revision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-qrevisions-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_formProjectQRevision', [
        'model' => $model,
        'currencyList' => $currencyList
    ])
    ?>

</div>
