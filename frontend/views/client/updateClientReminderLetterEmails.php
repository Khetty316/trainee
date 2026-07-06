<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\updateClientReminderLetterEmails */

$this->params['breadcrumbs'] = [];
$this->params['breadcrumbs'][] = ['label' => 'Home','url' => ['/site/index']];
$this->params['breadcrumbs'][] = ['label' => 'Clients','url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Client Details','url' => ['view-client', 'id' => $model->client_id]];
$this->params['breadcrumbs'][] = ['label' => 'Email Reminder','url' => ['view-client-reminder-letter-emails', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-reminder-letter-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formClientReminderLetterEmails', [
        'model' => $model,
        'templates' => $templates,
    ]) ?>

</div>
