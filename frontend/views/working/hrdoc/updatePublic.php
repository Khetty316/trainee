<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */

$this->title = 'Update: ' . $model->description;
$this->params['breadcrumbs'][] = ['label' => 'Document Reminder', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->description, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="document-reminder-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_formPublic', [
        'model' => $model,
    ])
    ?>

</div>
