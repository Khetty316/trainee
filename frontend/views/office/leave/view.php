<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\leave\LeaveMaster */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Leave Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="leave-master-view">

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
            'requestor_id',
            'leave_type',
            'superior_id',
            'reason:ntext',
            'start_date',
            'start_section',
            'end_date',
            'end_section',
            'total_days',
            'leave_status',
            'leave_confirm_year',
            'leave_confirm_month',
            'days_annual',
            'days_unpaid',
            'days_sick',
            'days_others',
            'confirm_flag',
        ],
    ]) ?>

</div>
