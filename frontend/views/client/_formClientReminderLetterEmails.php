<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\models\common\RefCompanyGroupList;

$companyGroups = \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterEmails */
/* @var $form yii\widgets\ActiveForm */
/* @var $uploadedFiles array */

$this->title = 'New Reminder Letter Email';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->client->company_name, 'url' => ['view-client', 'id' => $model->client_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.min.js" type="text/javascript"></script>
<div class="email-form">
    <?php $form = ActiveForm::begin(['id' => 'reminder-form', 'options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]);
    ?>    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0 ">Email Detail:</legend>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'sender')->textInput(['id' => 'sender-email']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'recipient')->textInput() ?>
            </div>
        </div>
        <?= $form->field($model, 'Cc')->textInput()->hint('Sender email will be automatically added to Cc list') ?>
        <?= $form->field($model, 'Bcc')->textInput() ?>
        <?= $form->field($model, 'subject')->textInput() ?>
        <?= $form->field($model, 'content')->textarea(['rows' => 8, 'class' => 'form-control content'])->label('Email Content') ?>
        <?= $form->field($model, 'attachment')->fileInput(['multiple' => true, 'accept' => '.pdf,.doc,.docx']) ?>
        <?php if (!empty($uploadedFiles)): ?>
            <div style="border:1px solid #ddd; padding:10px; border-radius:5px; margin-top:10px;">
                <?php foreach ($uploadedFiles as $i => $file): ?>
                    <div id="uploaded-file-row-<?= $i ?>" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span><?= Html::encode($file) ?></span>
                        <div>
                            <?= Html::a('View', Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $file), ['class' => 'btn btn-primary btn-sm', 'target' => '_blank',]) ?>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm remove-temp-file"
                                data-file="<?= Html::encode($file) ?>"
                                data-client="<?= $model->client_id ?>"
                                data-row="uploaded-file-row-<?= $i ?>">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Letter Reminder:</legend>
        <div id="letterReminderContainer">
            <?php
            if (empty($reminderRows)) {
                $reminderRows = [[
                'company_group' => '',
                'template_id' => '',
                'template_content' => '',
                ]];
            }
            ?>
            <?php foreach ($reminderRows as $index => $row): ?>
                <div class="letter-reminder-row border p-3 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company Group <span class="text-danger">*</span> </label>
                                <?= Html::dropDownList("ReminderRows[$index][company_group]", $row['company_group'] ?? '', $companyGroups, ['prompt' => 'Select Company Group', 'class' => 'form-control company-group']) ?>
                                <div class="invalid-feedback company-group-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Letter Template <span class="text-danger">*</span></label>
                                <?=
                                Html::dropDownList("ReminderRows[$index][template_id]", $row['template_id'],
                                        ArrayHelper::map($templates, 'id', 'letter_name'),
                                        [
                                            'prompt' => 'Select Letter Template',
                                            'class' => 'form-control reminder-template'
                                        ]
                                )
                                ?>
                                <div class="invalid-feedback reminder-template-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Template Content</label>
                        <?= Html::textarea("ReminderRows[$index][template_content]", $row['template_content'], ['class' => 'form-control template-content-dynamic', 'id' => 'template-content-' . $index]) ?>
                    </div>
                    <div class="reminder-row-actions">
                        <?= Html::button('<i class="fas fa-minus-circle"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-sm remove-reminder-row',]) ?>
                    </div>
                </div>
            <?php endforeach; ?>   
        </div>
        <div class="mt-2">
            <?= Html::button('<i class="fas fa-plus-circle"></i>', ['class' => 'btn btn-primary', 'type' => 'button', 'onclick' => 'addReminderRow()',]) ?>
        </div>
    </fieldset>
    <div class="d-flex justify-content-end w-100 mar mb-3">
        <?= Html::submitButton('Proceed', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'preview']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .reminder-row-actions {
        margin-top: 10px;
        padding-right: 5px;
        text-align: right;
    }
</style>
<script>
    // Summernote helper
    function getSummernoteToolbar() {
        return [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['media', 'link', 'hr', 'picture']],
            ['view', ['codeview']],
            ['help', ['help']]
        ];
    }
    $(function () {
        $('.content').summernote({
            height: 500,
            toolbar: getSummernoteToolbar()
        });
        $('.template-content-dynamic').each(function () {
            if (!$(this).next('.note-editor').length) {
                initSummernote(this);
            }
        });
    });
    function initSummernote(selector) {
        $(selector).summernote({
            height: 300,
            toolbar: getSummernoteToolbar()
        });
    }
</script>
<script>
    // Reminder row
    function addReminderRow() {
        var reminderIndex = $('.letter-reminder-row').length;
        var uniqueId = Date.now();
        var html =
                '<div class="letter-reminder-row border p-3 mb-3">' +
                '<div class="row">' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label>Company Group <span class="text-danger">*</span></label>' +
                '<select class="form-control company-group" ' +
                'name="ReminderRows[' + reminderIndex + '][company_group]">' +
                '<option value="">Select Company Group</option>' +
<?php foreach ($companyGroups as $key => $value): ?>
            '<option value="<?= $key ?>"><?= addslashes($value) ?></option>' +
<?php endforeach; ?>
        '</select>' +
                '<div class="invalid-feedback company-group-error"></div>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label>Letter Template <span class="text-danger">*</span></label>' +
                '<select class="form-control reminder-template" ' +
                'name="ReminderRows[' + reminderIndex + '][template_id]">' +
                '<option value="">Select Letter Template</option>' +
<?php foreach ($templates as $template): ?>
            '<option value="<?= $template->id ?>"><?= addslashes($template->letter_name) ?></option>' +
<?php endforeach; ?>
        '</select>' +
                '<div class="invalid-feedback reminder-template-error"></div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<label>Template Content</label>' +
                '<textarea ' +
                'class="form-control template-content-dynamic" ' +
                'id="template-content-' + uniqueId + '" ' +
                'name="ReminderRows[' + reminderIndex + '][template_content]">' +
                '</textarea>' +
                '<div class="reminder-row-actions">' +
                '<button type="button" class="btn btn-danger btn-sm remove-reminder-row">' +
                '<i class="fas fa-minus-circle"></i>' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>';
        $('#letterReminderContainer').append(html);
        initSummernote('#template-content-' + uniqueId);
    }
</script>
<script>
    // Template loading
    const reminderTemplateUrl = '<?= Url::to(['client/get-reminder-letter-content']) ?>';
    $(document).on('change', '.reminder-template', function () {
        var templateId = $(this).val();
        var currentRow = $(this).closest('.letter-reminder-row');
        if (!templateId) {
            currentRow.find('.template-content-dynamic').summernote('code', '');
            return;
        }
        $.get(reminderTemplateUrl, {id: templateId}, function (data) {
            currentRow.find('.template-content-dynamic').summernote('code', data);
        });
    });
</script>
<script>
    // Form submit
    $('#reminder-form').on('submit', function (e) {
        var valid = true;
        // Save Summernote content
        $('.template-content-dynamic').each(function () {
            $(this).val($(this).summernote('code'));
        });
        // Company Group
        $('.company-group').removeClass('is-invalid');
        $('.company-group-error').text('');
        $('.company-group').each(function () {
            if ($(this).val() === '') {
                valid = false;
                $(this).addClass('is-invalid');
                $(this)
                        .closest('.form-group')
                        .find('.company-group-error')
                        .text('Company Group cannot be blank.');
            }
        });
        // Letter Template
        $('.reminder-template').removeClass('is-invalid');
        $('.reminder-template-error').text('');
        $('.reminder-template').each(function () {
            if ($(this).val() === '') {
                valid = false;
                $(this).addClass('is-invalid');
                $(this)
                        .closest('.form-group')
                        .find('.reminder-template-error')
                        .text('Letter Template cannot be blank.');
            }
        });
        if (!valid) {
            e.preventDefault();
        }
    });
    $(document).on('change', '.company-group', function () {
        $(this).removeClass('is-invalid');
        $(this)
                .closest('.form-group')
                .find('.company-group-error')
                .text('');
    });
    $(document).on('change', '.reminder-template', function () {
        $(this).removeClass('is-invalid');
        $(this)
                .closest('.form-group')
                .find('.reminder-template-error')
                .text('');
    });
</script>

<?php
$this->registerJs(<<<JS
$(document).on('click', '.remove-reminder-row', function () {
    if ($('.letter-reminder-row').length <= 1) {
        return;
    }
    $(this).closest('.letter-reminder-row').remove();
});
JS);
?>

<?php
$removeUrl = Url::to(['client/remove-temp-file-ajax']);
$this->registerJs(<<<JS
$(document).on('click','.remove-temp-file',function(){
    if(!confirm('Remove this attachment?')){
        return;
    }
    var btn=$(this);
    $.ajax({
        url:'{$removeUrl}',
        type:'POST',
        data:{
            file:btn.data('file'),
            client_id:btn.data('client'),
            _csrf:yii.getCsrfToken()
        },
        success:function(res){
            if(res.success){
                $('#'+btn.data('row')).remove();
            }else{
                alert('Unable to remove attachment.');
            }
        },
        error:function(){
            alert('Server error.');
        }
    });
});
JS);
?>