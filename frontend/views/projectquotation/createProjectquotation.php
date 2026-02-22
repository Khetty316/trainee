<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQMasters */

$this->title = 'Create Project';
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qmasters-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?=
    $this->render('_formProjectquotation', [
        'model' => $model,
        'companyGroupList' => $companyGroupList
    ])
    ?>

</div>
