<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsCorrectiveWorkOrderMaster */
?>
<!--<div class="cmms-corrective-work-order-master-update">-->
    <?= $this->renderAjax('_corrective_form', [
        'model' => $model,
        'assignedPICs' => $assignedPICs,
        'moduleStatus' => $moduleStatus
    ]) ?>

<!--</div>-->
