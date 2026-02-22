<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypes $model */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Ref Project Q Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ref-project-qtypes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'code' => $model->code], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'code' => $model->code], [
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
            'code',
            'project_type_name',
            'fab_dept_percentage',
            'elec_dept_percentage',
        ],
    ]) ?>

</div>
