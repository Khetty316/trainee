<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendance $model */
$this->title = $model->user->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Monthly Attendances', 'url' => ['index', 'year' => $model->year, 'month' => $model->month]];
$this->params['breadcrumbs'][] = "Update (" . $model->user->fullname . ")";
?>
<div class="monthly-attendance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
        'userList' => $userList,
        'monthList' => $monthList,
        'year' => $model->year,
        'month' => $model->month,
        'new' => false
    ])
    ?>

</div>
