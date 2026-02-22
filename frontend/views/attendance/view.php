<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendance $model */
$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Monthly Attendances', 'url' => ['index', 'year' => $model->year, 'month' => $model->month]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="monthly-attendance-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <div class="row">
        <div class="col-md-6">
            <?=
            DetailView::widget(
                    [
                        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                        'template' => "<tr><th style='p-1'>{label}</th><td>{value}</td></tr>",
                        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                        'model' => $model,
                        'attributes' => [
                            'month',
                            'year',
                            'total_days',
                            'total_present',
                            'workday_present',
                            'unpaid_leave_present',
                            'rest_holiday_present',
                            'absent',
                            'leave_taken',
                            'late_in',
                            'early_out',
                            'miss_punch',
                            'short',
                            'sche',
                            'workday',
                            'workday_ot',
                            'holiday',
                            'holiday_ot',
                            'restday',
                            'restday_ot',
                            'unpaid_leave',
                            'unpaid_leave_ot',
                        ],
                    ])
            ?>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Action</legend>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?=
                Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ])
                ?>
            </fieldset>
        </div>

    </div>
</div>
