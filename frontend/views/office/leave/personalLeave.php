<?php

use yii\helpers\Html;

$this->title = 'Leave Application';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

<?= $this->render('/profile/__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '2']); ?>

<!--<h3><?= Html::encode($this->title) ?></h3>-->
<div class="justify-content-center d-flex flex-column">
    <p>
        <?php
        echo Html::a('Apply Leave <i class="fas fa-plus"></i>', 'create', ["class" => "btn btn-success"]);
        ?>
    </p>
</div>

<br/>
<h5><b>Leave Summary of year <?= date_format(date_create(), "Y") ?>:</b></h5>
<div class="justify-content-center d-flex border rounded">
    <?= $this->render('_leaveStatus', ['leaveStatus' => $leaveStatus]) ?>
</div>

<br/>
<h5><b>Leave History:</b></h5>
<div class="justify-content-center d-flex border rounded">
    <div class="w-100 overflow-auto" style="height: 300px">
        <?= $this->render('_leaveHistory', ['leaveHistory' => $leaveHistory]) ?>
    </div>
</div>

<br/>
<h5><b>Relief History:</b></h5>
<div class="justify-content-center d-flex border rounded">
    <div class="w-100 overflow-auto" style="height: 300px">
        <?php echo $this->render('_reliefHistory', ['reliefHistory' => $reliefHistory]) ?>
    </div>
</div>

<br/>
<h5><b>Employee Leave Status:</b></h5>
<div class="justify-content-center d-flex border rounded">
    <div class="w-100 justify-content-center">
        <?= $this->render('_leaveCalendar', ['data' => $data]) ?>
    </div>
</div>


