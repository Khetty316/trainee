<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\models\common\RefCompanyGroupList;

$companyGroups = \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3;
$removeUrl = Url::to(['client/remove-temp-file-ajax']);

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
        <legend class="w-auto px-2 mb-3"> Uploaded Attachments </legend>
        <?=
                $form->field($model, 'attachment')
                ->label(false)
                ->fileInput([
                    'id' => 'clientreminderletteremails-attachment',
                    'name' => 'ClientReminderLetterEmails[attachment][]',
                    'multiple' => true,
                    'accept' => '.pdf',
                ])
        ?>
        <div
            id="attachment-container"
            style="<?= empty($uploadedFiles) ? 'display:none;margin-top:15px;' : 'margin-top:15px;' ?>">
            <table
                id="attachment-table"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">No.</th>
                        <th>File Name</th>
                        <th width="180" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($uploadedFiles)): ?>
                        <?php foreach ($uploadedFiles as $i => $file): ?>
                            <tr
                                id="uploaded-file-row-<?= $i ?>"
                                data-type="uploaded"
                                data-original-name="<?= Html::encode($file) ?>">
                                <td class="text-center row-number">
                                    <?= $i + 1 ?>
                                </td>
                                <?php
                                $displayName = preg_replace('/_\(\d+\)(\.[^.]+)$/', '$1', $file);
                                ?>
                                <td>
                                    <?= Html::encode($displayName) ?>
                                </td>                              
                                <td class="text-center">
                                    <?=
                                    Html::a('View <i class="fas fa-eye"></i>', Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $file),
                                            [
                                                'class' => 'btn btn-info btn-sm',
                                                'target' => '_blank',
                                            ]
                                    )
                                    ?>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm remove-temp-file"
                                        data-file="<?= Html::encode($file) ?>"
                                        data-client="<?= $model->client_id ?>"
                                        data-row="uploaded-file-row-<?= $i ?>">
                                        Delete <i class="fas fa-minus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </fieldset>
    <!-- Letter Reminder -->
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
                <div class="letter-reminder-row border p-3 mb-3"
                     data-index="<?= $index ?>">
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
                                <?= Html::dropDownList("ReminderRows[$index][template_id]", $row['template_id'], ArrayHelper::map($templates, 'id', 'letter_name'), ['prompt' => 'Select Letter Template', 'class' => 'form-control reminder-template']) ?>
                                <div class="invalid-feedback reminder-template-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Template Content</label>
                        <?= Html::textarea("ReminderRows[$index][template_content]", $row['template_content'], ['class' => 'form-control template-content-dynamic', 'id' => 'template-content-' . $index]) ?>
                    </div>
                    <div class="reminder-row-actions">
                        <?= Html::button('Remove Row <i class="fas fa-minus-circle"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-sm remove-reminder-row',]) ?>
                    </div>
                </div>
            <?php endforeach; ?>   
        </div>
        <div class="mt-2">
            <?= Html::button('Add Row <i class="fas fa-plus-circle"></i>', ['class' => 'btn btn-primary', 'type' => 'button', 'onclick' => 'addReminderRow()',]) ?>
        </div>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">
            Generated Reminder Letters
        </legend>
        <table
            id="reminder-letter-table"
            class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="60">No.</th>
                    <th width="150">Company Group</th>
                    <th>File Name</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </fieldset>
    <div class="d-flex justify-content-end w-100 mar mb-3">
        <?= Html::submitButton('Proceed <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'preview']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<style>
    .reminder-row-actions {
        margin-top: 10px;
        padding-right: 5px;
        text-align: right;
    }
    #attachment-table td,
    #attachment-table th{
        vertical-align: middle;
    }
    #attachment-table td:last-child{
        white-space: nowrap;
    }
    #attachment-table .btn{
        min-width:70px;
        margin:0 2px;
    }
</style>
<!-- Summernote -->
<script>
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
        renderAttachmentTable();
        renderReminderLetterTable();
    });
    function initSummernote(selector) {
        $(selector).summernote({
            height: 300,
            toolbar: getSummernoteToolbar()
        });
    }
</script>
<!-- Reminder Row -->
<script>
    function addReminderRow() {
        var reminderIndex = $('.letter-reminder-row').length;
        var uniqueId = Date.now();
        var html =
                '<div class="letter-reminder-row border p-3 mb-3" data-index="' + reminderIndex + '">' +
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
                'Remove Row <i class="fas fa-minus-circle"></i>' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>';
        $('#letterReminderContainer').append(html);
        initSummernote('#template-content-' + uniqueId);
        renderReminderLetterTable();
    }
</script>
<!-- Reminder Template AJAX -->
<script>
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
<!-- Form Validation -->
<script>
    $('#reminder-form').on('submit', function (e) {
        var valid = true;
        $('.template-content-dynamic').each(function () {
            $(this).val($(this).summernote('code'));
        });
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
    $(document).on('change', '.reminder-template', function () {
        $(this).removeClass('is-invalid');
        $(this)
                .closest('.form-group')
                .find('.reminder-template-error')
                .text('');
    });
</script>
<?php
$clientName = preg_replace('/[^A-Za-z0-9]+/', '_', $model->client->company_name);
$month = date('F');
$year = date('Y');
?>
<!-- Attachment Handling -->
<script>
    let dt = new DataTransfer();
    $(document).on('change', '#clientreminderletteremails-attachment', function () {
        let input = this;
        let newDt = new DataTransfer();
        Array.from(dt.files).forEach(function (file) {
            newDt.items.add(file);
        });
        Array.from(input.files).forEach(function (file) {
            let extension = file.name.split('.').pop().toLowerCase();
            if (extension !== 'pdf') {
                alert('Only PDF files are allowed.');
                return;
            }
            let exists = Array.from(newDt.files).some(function (f) {
                return f.name === file.name &&
                        f.size === file.size;
            });
            if (!exists) {
                newDt.items.add(file);
            }
        });
        dt = newDt;
        input.files = dt.files;
        
        renderAttachmentTable();
    });
    function renderAttachmentTable() {
        let tbody = $('#attachment-table tbody');
        tbody.find('tr[data-type="selected"]').remove();
        let uploadedCount = tbody.find('tr[data-type="uploaded"]').length;
        if (uploadedCount === 0 && dt.files.length === 0) {
            $('#attachment-container').hide();
            return;
        }
        $('#attachment-container').show();
        Array.from(dt.files).forEach(function (file, index) {
            let dot = file.name.lastIndexOf('.');
            let baseName = dot >= 0
                    ? file.name.substring(0, dot)
                    : file.name;
            let extension = dot >= 0
                    ? file.name.substring(dot)
                    : '';
            let displayName = baseName +
                    '_<?= $clientName ?>' +
                    '_<?= $month ?>' +
                    '_<?= $year ?>' +
                    extension;
            tbody.append(
                    '<tr data-type="selected">' +
                    '<td class="text-center row-number">0</td>' +
                    '<td>' + displayName + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-info btn-sm view-selected-file" data-index="' + index + '">View  <i class="fas fa-eye"></i></button> ' +
                    '<button type="button" class="btn btn-danger btn-sm remove-selected-file" data-index="' + index + '">Delete <i class="fas fa-minus-circle"></i></button>' +
                    '</td>' +
                    '</tr>'
                    );
        });
        refreshNumbers();
    }
    $(document).on('click', '.remove-selected-file', function () {
        let removeIndex = parseInt($(this).data('index'));
        let newDt = new DataTransfer();

        Array.from(dt.files).forEach(function (file, index) {
            if (index !== removeIndex) {
                newDt.items.add(file);
            }
        });
        dt = newDt;
        $('#clientreminderletteremails-attachment')[0].files = dt.files;
        renderAttachmentTable();
    });
    $(document).on('click', '.view-selected-file', function () {
        let index = parseInt($(this).data('index'));
        let file = dt.files[index];
        if (!file) {
            return;
        }
        let url = URL.createObjectURL(file);
        window.open(url, '_blank');
    });
    function refreshNumbers() {
        $('#attachment-table tbody tr').each(function (index) {
            $(this).find('.row-number').text(index + 1);

        });
    }
</script>
<!-- Reminder Letter Preview -->
<script>
    function renderReminderLetterTable() {
        let tbody = $('#reminder-letter-table tbody');
        tbody.empty();
        $('.letter-reminder-row').each(function (index) {
            let companyGroup = $(this).find('.company-group').val();
            if (!companyGroup) {
                return;
            }
            let clientName = '<?= $clientName ?>';
            let month = '<?= $month ?>';
            let year = '<?= $year ?>';
            let fileName =
                    'Reminder_Letter_' +
                    companyGroup +
                    '_' +
                    clientName +
                    '_' +
                    month +
                    '_' +
                    year +
                    '.pdf';
            tbody.append(
                    '<tr>' +
                    '<td class="text-center">' + (tbody.children().length + 1) + '</td>' +
                    '<td>' + companyGroup + '</td>' +
                    '<td>' + fileName + '</td>' +
                    '</tr>'
                    );
        });
    }
    $(document).on('change', '.company-group', function () {
        renderReminderLetterTable();
    });
</script>
<script>
    $(document).on('click', '.remove-reminder-row', function () {
        if ($('.letter-reminder-row').length <= 1) {
            return;
        }
        $(this).closest('.letter-reminder-row').remove();
        renderReminderLetterTable();
    });
</script>
<?php
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
        refreshNumbers();
        if($('#attachment-table tbody tr').length===0){
            $('#attachment-container').hide();
        }
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
