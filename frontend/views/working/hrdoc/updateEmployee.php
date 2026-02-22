<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocuments */

$this->title = 'Update Employee Documents: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'HR Employee Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hr-employee-documents-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formEmployee', [
        'model' => $model,
    ]) ?>

</div>
