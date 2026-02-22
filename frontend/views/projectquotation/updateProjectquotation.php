<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQMasters */

$this->title = 'Update : ' . $model->quotation_no;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->quotation_display_no, 'url' => ['view-projectquotation', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
//
//
//$this->title = $model->quotation_no;
//$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qmasters-update">

    <h3><?= Html::encode($this->title) ?></h3>

<?=
$this->render('_formProjectquotation', [
    'model' => $model,
    'companyGroupList' => $companyGroupList
])
?>

</div>
