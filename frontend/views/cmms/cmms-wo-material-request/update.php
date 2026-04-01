<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsWoMaterialRequestMaster */

$this->title = 'Update Cmms Wo Material Request Master: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cmms Wo Material Request Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cmms-wo-material-request-master-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBrandList' => $modelBrandList,
        'isLegacy' => $isLegacy,

    ]) ?>

</div>
<script>
    $(document).ready(function () {
        $('#myModal').on('shown.bs.modal', function () {
            $('#bomdetails-model_type').focus();
        });
    });
</script>