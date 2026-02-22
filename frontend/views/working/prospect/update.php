<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProspectMaster */

$this->title = 'Update Prospect Master: ' . $model->proj_code;
$this->params['breadcrumbs'][] = ['label' => 'Prospect Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->proj_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prospect-master-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
