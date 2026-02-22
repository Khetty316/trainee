<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\Clients */

$this->title = 'Create Clients';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formClient', [
        'model' => $model,
        'contactModels' => $contactModels,
        'isUpdate' => false
    ]) ?>

</div>
