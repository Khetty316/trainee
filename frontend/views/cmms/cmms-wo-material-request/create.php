<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsWoMaterialRequestMaster */

$this->title = 'Create Cmms Wo Material Request Master';
$this->params['breadcrumbs'][] = ['label' => 'Cmms Wo Material Request Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-wo-material-request-master-create">

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