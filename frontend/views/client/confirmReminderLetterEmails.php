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
    <h2 class="mb-3"> Confirmation </h2>
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
    <!-- Attachments -->
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">
            Uploaded Attachments (<?= count($uploadedFiles) ?>)
        </legend>
        <?php if (!empty($uploadedFiles)): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No.</th>
                        <th>File Name</th>
                        <th width="180" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uploadedFiles as $i => $file): ?>
                        <tr>
                            <td class="text-center">
                                <?= $i + 1 ?>
                            </td>
                            <?php
                            $displayName = preg_replace('/_\(\d+\)(\.[^.]+)$/', '$1', $file);
                            ?>
                            <td>
                                <?= Html::encode($displayName) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $file) ?>"
                                   target="_blank"
                                   class="btn btn-info btn-sm">
                                    View <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted mb-0">
                No uploaded attachments.
            </p>
        <?php endif; ?>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">
            Generated Reminder Letters (<?= count($pdfFiles) ?>)
        </legend>
        <?php if (!empty($pdfFiles)): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No.</th>
                        <th width="150">Company Group</th>
                        <th>File Name</th>
                        <th width="180" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pdfFiles as $i => $pdfFile): ?>
                        <tr>
                            <td class="text-center">
                                <?= $i + 1 ?>
                            </td>
                            <td>
                                <?= Html::encode($reminderRows[$i]['company_group'] ?? '-') ?>
                            </td>
                            <td>
                                <?= Html::encode($pdfFile['file_name']) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $pdfFile['file_name']) ?>"
                                   target="_blank"
                                   class="btn btn-info btn-sm">
                                    View <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted mb-0">
                No reminder letters generated.
            </p>
        <?php endif; ?>
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
            <?= Html::a('Cancel <i class="fas fa-times"></i>', ['create-reminder-letter-emails', 'client_id' => $model->client_id, 'restore' => 1], ['class' => 'btn btn-danger']) ?>
        </div>
        <?= Html::submitButton('Save as Draft <i class="fas fa-save"></i>', ['class' => 'btn btn-secondary', 'name' => 'action', 'value' => 'draft']) ?>
        <?= Html::submitButton('Process & Send <i class="fas fa-check"></i>', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'send']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
