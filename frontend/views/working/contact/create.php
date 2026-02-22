<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\contact\ContactMaster */

$this->title = 'Create Contact Master';
$this->params['breadcrumbs'][] = ['label' => 'Contact Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
