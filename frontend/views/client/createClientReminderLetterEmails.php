<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterEmails */
/* @var $reminderRows array */

$this->title = 'New Reminder Letter Email';
$this->registerCss("
    .note-editable p {
        display: block !important;
        margin: 0 !important;
    }
    .note-editable br {
        display: block;
        margin-bottom: 5px;
    }
");
?>
<div class="client-reminder-letter-emails-create">
    <h3><?= Html::encode($this->title) ?></h3>
    <?=
    $this->render('_formClientReminderLetterEmails', [
        'model' => $model,
        'templates' => $templates,
        'uploadedFiles' => $uploadedFiles,
        'pdfFiles' => $pdfFiles,
        'reminderRows' => $reminderRows ?? [],
    ])
    ?>
</div>
