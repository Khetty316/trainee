<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocuments */

$this->title = 'Create Hr Employee Documents';
$this->params['breadcrumbs'][] = ['label' => 'Hr Employee Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hr-employee-documents-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
