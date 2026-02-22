<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\Clients */

$this->title = 'Update Clients: ' . $model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->company_name, 'url' => ['view-client', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="clients-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formClient', [
        'model' => $model,
        'contactModels' => $contactModels,
        'isUpdate' => true
    ]) ?>

</div>
