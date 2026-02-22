<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\bom\bomdetails */

$this->title = 'Create Bomdetails';
$this->params['breadcrumbs'][] = ['label' => 'Bomdetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bomdetails-create">

    <?=
    $this->render('_form', [
        'model' => $model,
            'modelBrandList' => $modelBrandList,
            'isLegacy' => $isLegacy,
    ])
    ?>

</div>
<script>
    $(document).ready(function () {
        $('#myModal').on('shown.bs.modal', function () {
            $('#bomdetails-model_type').focus();
        });
    });
</script>