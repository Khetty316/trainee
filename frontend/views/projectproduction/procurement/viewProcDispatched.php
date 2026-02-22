<?php

use yii\helpers\Html;

$this->title = $model->dispatch_no;
$this->params['breadcrumbs'][] = ['label' => 'Procurement - Dispatched List', 'url' => ['index-proc-dispatched-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= Html::encode($this->title) ?></h3>
<div class="row">
    <div class="col-12">
        <?= $this->render("_detailProcDispatched", ['model' => $model]) ?>
    </div>
</div>
<?php ?>
