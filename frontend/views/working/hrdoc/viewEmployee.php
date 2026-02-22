<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocuments */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'HR Employee Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hr-employee-documents-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'hr_doctype',
            'employee_id',
            'filename',
            'active_sts',
            'is_read',
            'read_at',
            'created_at',
            'created_by',
        ],
    ]) ?>

</div>
