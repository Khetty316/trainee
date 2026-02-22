<?php

use yii\helpers\Html;

$this->title = 'Work Travel Requisition';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

<?= $this->render('/profile/__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '5']); ?>

<!--<h3><?= Html::encode($this->title) ?></h3>-->
<div class="justify-content-center d-flex flex-column">
    <p>
        <?php
        echo Html::a('Apply Work Travelling <i class="fas fa-plus"></i>', 'apply-work-travel', ["class" => "btn btn-success"]);
        ?>
    </p>
</div>

<br/>
<h5><b>Work Traveling History:</b></h5>
<div class="justify-content-center d-flex border rounded">
    <div class="w-100 overflow-auto" style="height: 300px">
        <?= $this->render('_leaveHistory', ['leaveHistory' => $leaveHistory, 'formType' => $formType]) ?>
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


