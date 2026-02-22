<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendance $model */
$this->title = 'Create Monthly Attendance';
$this->params['breadcrumbs'][] = ['label' => 'Monthly Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monthly-attendance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
        'new' => true
    ])
    ?>

</div>
