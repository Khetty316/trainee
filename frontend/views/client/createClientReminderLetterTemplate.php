<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterEmails */

$this->title = 'Send Email Reminder';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Client Detail','url' => ['view-client', 'id' => $model->client_id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="client-reminder-letter-emails-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formClientReminderLetterEmails', ['model' => $model]) ?>

</div>
