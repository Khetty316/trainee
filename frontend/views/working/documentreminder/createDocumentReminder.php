<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Document Reminder', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-reminder-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formDocumentReminder', [
        'model' => $model,
    ]) ?>

</div>
