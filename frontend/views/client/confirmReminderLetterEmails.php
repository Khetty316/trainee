<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model frontend\models\client\ClientReminderLetterEmails */
/* @var $fileName string */
$this->title = 'Confirm Reminder Letter Email';
$clientId = $model->client_id ?? Yii::$app->request->get('client_id');
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index-client']];
$this->params['breadcrumbs'][] = ['label' => $model->client->company_name, 'url' => ['view-client', 'id' => $model->client_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-form">
    <h2 class="mb-3"> Confirmation Page </h2>
    <?php $form = ActiveForm::begin(['action' => ['create-reminder-letter-emails', 'client_id' => $model->client_id, 'id' => $model->id,], 'method' => 'post']); ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Email Detail:</legend>
        <div style="display:flex; gap:20px;">
            <div style="flex:1;">
                <?= $form->field($model, 'sender')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>
            </div>
            <div style="flex:1;">
                <?= $form->field($model, 'recipient')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>
            </div>
        </div>
        <?= $form->field($model, 'Cc')->textInput(['readonly' => true, 'class' => 'form-control bg-light'])->hint('Sender email will be automatically added to Cc list') ?>
        <?= $form->field($model, 'Bcc')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>
        <?= $form->field($model, 'subject')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>
        <div class="form-group">
            <label>Email Content</label>
            <div class="border rounded p-3 bg-light"
                 style="min-height:300px; max-height:500px; overflow:auto;">
                     <?= $model->content ?>
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0"> Attachments (<?= count($pdfFiles) + count($uploadedFiles) ?>) </legend>
        <div style="border:1px solid #ddd; padding:10px; border-radius:5px;">
            <?php foreach ($pdfFiles as $pdfFile): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <div> <?= $pdfFile ?> </div>
                    <div>
                        <a href="<?= Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $pdfFile) ?>"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            View
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!empty($uploadedFiles)): ?>
                <?php foreach ($uploadedFiles as $file): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <div> <?= $file ?> </div>
                        <div>
                            <a href="<?= Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $file) ?>"
                               target="_blank"
                               class="btn btn-primary btn-sm">
                                View
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </fieldset>
    <?= Html::hiddenInput('ClientReminderLetterEmails[sender]', $model->sender) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[recipient]', $model->recipient) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[Cc]', $model->Cc) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[Bcc]', $model->Bcc) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[subject]', $model->subject) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[content]', $model->content) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[client_id]', $model->client_id) ?>
    <?= Html::hiddenInput('ClientReminderLetterEmails[id]', $model->id) ?>
    <div class="form-group text-right">
        <div class="float-left">
            <?= Html::a('Cancel', ['create-reminder-letter-emails', 'client_id' => $model->client_id, 'restore' => 1], ['class' => 'btn btn-danger']) ?>
        </div>
        <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'draft']) ?>
        <?= Html::submitButton('Process & Send', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'send']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
