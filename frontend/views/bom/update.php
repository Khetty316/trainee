<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\bom\bomdetails */

$this->title = 'Update Bomdetails: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bomdetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bomdetails-update">
    <?=
    $this->render('_form', [
        'model' => $model,
        'modelBrandList' => $modelBrandList,
        'isLegacy' => $isLegacy,
    ])
    ?>

</div>
