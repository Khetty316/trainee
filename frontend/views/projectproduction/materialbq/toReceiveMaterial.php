<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProdDispatchStatus;

$panel = $bqMaster->projProdPanel;
$this->title = Html::encode($bqMaster->bq_no);
if ($model->status == RefProdDispatchStatus::STS_Dispatched) {
    $this->params['breadcrumbs'][] = ['label' => 'Fabrication Department - Pending', 'url' => ['/production/material-bq/index-to-receive-material']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Fabrication Department - Dispatched List', 'url' => ['/production/material-bq/index-all-dispatched-list']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-5 order-md-2">
        <?= $this->render("_detailViewProjProdDetail", ['projProdMaster' => $panel->projProdMaster, 'panel' => $panel]) ?>
    </div> 
    <div class="col-md-7 order-md-1">
        <?= $this->render("store/_ajaxViewStoreDispatch", ['model' => $model]) ?>
        <div class="container-fluid text-right">
            <?php
            if ($model->status == RefProdDispatchStatus::STS_Dispatched) {
                $form = ActiveForm::begin(['id' => 'myForm']);
                echo Html::textInput("acceptStatus", '', ['class' => 'hidden', 'id' => 'acceptStatus']);
                echo Html::a("Receive <i class='fas fa-check'></i>", "javascript:submitForm(1)", ['class' => 'btn btn-success mx-2']);
                echo Html::a("Reject <i class='fas fa-times'></i>", "javascript:submitForm(0)", ['class' => 'btn btn-danger']);
                ActiveForm::end();
            }
            ?>
        </div>
    </div>
</div>



<script>
    function submitForm(response) {
        let ans;
        if (response === 1) {
            ans = confirm("Receive?");
        } else if (response === 0) {
            ans = confirm("Reject?");
        } else {
            return;
        }
        if (ans) {
            $("#acceptStatus").val(response);
            $("#myForm").submit();
        }
    }

</script>
