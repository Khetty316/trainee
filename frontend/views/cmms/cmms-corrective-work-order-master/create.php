<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsCorrectiveWorkOrderMaster */

$this->title = 'Create Cmms Corrective Work Order Master';
$this->params['breadcrumbs'][] = ['label' => 'Cmms Corrective Work Order Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-corrective-work-order-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
