<?php

use yii\helpers\Html;
?>

<?php
$key = $key ?? 0;
$month = common\models\myTools\MyCommonFunction::getMonthListArray();
?>


<div class="form-row mt-2 mb-1" id='tr_<?= $key ?>'>
    <div class="col-sm-12 col-md-3 col-xl-3 d-flex justify-content-center align-items-center mb-1">
        <?= Html::textInput("entitleDetail[$key][days]", $singleDetail->days, ['class' => 'form-control required text-right', 'type' => 'number']) ?>
        <span class="d-none d-md-block text-center">&nbspdays.</span>
    </div>

    <div class="col-sm-12 col-md-4 col-xl-4 d-flex justify-content-center align-items-center mb-1">
        <span class="d-none d-md-block text-center">&nbspFrom&nbsp</span>
        <?= Html::dropDownList("entitleDetail[$key][monthStart]", $singleDetail->month_start, $month, ['class' => 'form-control required']) ?>
    </div>

    <div class="col-sm-12 col-md-4 col-xl-4 d-flex justify-content-center align-items-center mb-1">
        <span class="d-none d-md-block text-center ">to&nbsp</span>
        <?= Html::dropDownList("entitleDetail[$key][monthEnd]", $singleDetail->month_end, $month, ['class' => 'form-control required']) ?>
    </div>

    <div class="col-sm-12 col-md-1 col-xl-1 text-center">
        <?php
        if ($key != 0) {
            echo \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2 align-middle' ></i>", "javascript:removeRow($key)");
        }
        ?>
    </div>
</div>