<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\email_model\common\RefProjectQShippingMode;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $email_model frontend\email_model\projectquotation\ProjectQRevisions */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Email: ' . $quotation->quotation_no . ' (' . ucfirst($email_model->email_type) . ')';
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->revision_description, 'url' => ['/projectqrevision/view-project-q-revision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .history {
        background-color: #f5f5f5;
    }
    .is-valid {
        border-color: #28a745 !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
</style>
<div class="project-qrevisions-form">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 history">
            <div class="">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2  m-0 ">Email History:</legend>
                    <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Sent By </th>
                                    <th>Sender</th>
                                    <th>Recipient</th>
                                    <th>Cc</th>
                                    <th>Bcc</th>
                                    <th>Subject</th>
                                    <th>Email Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noHistory = 1;
                                foreach ($emailHistory as $index => $detail) {
                                    $isLatest = $index == 0 ? "table-success fw-bold" : "";

                                    if ($detail->recipient !== null) {
                                        $noHistory = 0;
                                        $sentBy = ($detail->sent_by === null ? '-' : ($detail->sentBy->fullname . ' @ ' .
                                                \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($detail->sent_at))
                                        );
                                        ?>
                                        <tr class="<?= $isLatest ?>">
                                            <td><?= $sentBy ?></td>
                                            <td><?= $detail->sender ?></td>
                                            <td><?= $detail->recipient ?></td>
                                            <td><?= $detail->Cc ?></td>
                                            <td><?= $detail->Bcc ?></td>
                                            <td><?= $detail->subject ?></td>
                                            <td><?= $detail->email_type ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                if ($noHistory) {
                                    ?> 
                                    <tr>
                                        <td colspan="7">No email records found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
            <?php
            $form = ActiveForm::begin([
                'options' => [
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data'
                ],
                'action' => ['update-quotation-email',
                    'id' => $email_model->id,
                    'email_type' => $email_model->email_type],
                'method' => 'post'
            ]);
            ?>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">New Email Form:</legend>


                <div class="form-row">
                    <div class="col-sm-12 col-md-12 d-flex justify-content-between align-items-center mb-3">
                        <small class="form-text text-info m-0">
                            Note: Sender email will be automatically added to Cc list
                        </small>

                        <?=
                        Html::a(
                                'User Manual <i class="fas fa-book"></i>',
                                ['user-manual-sending-email'],
                                ['class' => 'btn btn-warning', 'title' => 'View User Manual', 'target' => '_blank']
                        )
                        ?>
                    </div>
                    <div class="col-sm-12 col-md-6">


                        <?=
                        $form->field($email_model, 'sender')->textInput([
                            'maxlength' => true,
                            'required' => true,
                            'id' => 'senderInput'
                        ])
                        ?>

                        <div id="senderError" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <?php
                        $projectqclient = frontend\models\projectquotation\ProjectQClients::findOne($client_id);
                        $emails = frontend\models\client\Clients::getEmails($projectqclient->client_id);

//                            foreach ($emails as $email) {
//                                echo "<option value='{$email}'>";
//                            }
                        ?>
                        <?=
                        $form->field($email_model, 'recipient')->textInput([
                            'maxlength' => true,
                            'required' => true,
                            'id' => 'recipientInput',
                            'list' => 'recipientList',
                            'placeholder' => 'Select or type recipient email',
                            'value' => $emails[0] ?? null
                        ])
                        ?>

                        <datalist id="recipientList">
                            <?php
                            $projectqclient = frontend\models\projectquotation\ProjectQClients::findOne($client_id);
                            $emails = frontend\models\client\Clients::getEmails($projectqclient->client_id);

                            foreach ($emails as $email) {
                                echo "<option value='{$email}'>";
                            }
                            ?>
                        </datalist>

                        <div id="recipientError" class="invalid-feedback" style="display: none;"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">

                        <?= $form->field($email_model, 'Cc')->textInput(['id' => 'CcInput']) ?>


                        <div id="CcError" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="col-sm-12 col-md-5">
                        <?= $form->field($email_model, 'Bcc')->textInput(['id' => 'BccInput']) ?>

                        <div id="BccError" class="invalid-feedback" style="display: none;"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($email_model, 'subject')->textarea() ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($email_model, 'content')->textarea(['rows' => 20]) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-5">
                        <?=
                        $form->field($email_model, 'attachments[]')->fileInput([
                            'multiple' => true, 'accept' => '.pdf', 'id' => 'attachment-input'])
                        ?>
                    </div>   
                    <div class="col-sm-12 col-md-10">
                        <ul id="file-list" class="list-group mt-2"></ul>
                    </div>
                </div>
                <?=
                Html::a('Cancel',
                        ['view-project-q-revision', 'id' => $model->id],
                        ['class' => 'float-right btn btn-danger mr-2']
                )
                ?>
                <?=
                Html::submitButton('Process & Send', [
                    'id' => 'save-btn',
                    'class' => 'float-right btn btn-success mr-1']
                )
                ?>

                <?php ActiveForm::end(); ?>
            </fieldset>
        </div>
        </fieldset>
    </div>
</div>

</div>
<script>
    const readPdfUrl = <?= json_encode(\yii\helpers\Url::to(['projectqrevision/read-pdf'])) ?>;
    const currentId = <?= json_encode($id) ?>;

    const recipientInput = document.getElementById('recipientInput');
    const senderInput = document.getElementById('senderInput');
    const CcInput = document.getElementById('CcInput');
    const BccInput = document.getElementById('BccInput');

    if (recipientInput) {
        recipientInput.setAttribute('data-kw-ignore', 'true');
    }
    if (senderInput) {
        senderInput.setAttribute('data-kw-ignore', 'true');
    }

    // Helper function to show error message
    function showError(inputElement, errorElementId, message) {
        const errorElement = document.getElementById(errorElementId);
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    // Helper function to show success
    function showSuccess(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
        errorElement.style.display = 'none';
    }

    // Helper function to clear validation
    function clearValidation(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        inputElement.classList.remove('is-invalid', 'is-valid');
        errorElement.style.display = 'none';
    }

    // Validate single email
    async function validateEmail(email) {
        try {
            const response = await $.post('<?= Url::to(['projectqrevision/check-email-exists']) ?>', {
                email_address: email
            });
            return response;
        } catch (error) {
            return {
                success: false,
                error: {type: 'Server error. Please try again.'}
            };
        }
    }

    // Validate multiple emails (for Cc/Bcc)
    async function validateMultipleEmails(emailString) {
        if (!emailString || emailString.trim() === '') {
            return {success: true}; // Empty is valid (optional field)
        }

        const emailArray = emailString.split(',').map(email => email.trim()).filter(e => e !== '');

        for (const email of emailArray) {
            const result = await validateEmail(email);
            if (!result.success) {
                return {
                    success: false,
                    error: {type: `${email}: ${result.error?.type || 'Invalid email'}`}
                };
            }
        }

        return {success: true};
    }

    // Clear validation when user types
    if (senderInput) {
        senderInput.addEventListener('input', function () {
            clearValidation(senderInput, 'senderError');
        });
    }

    if (recipientInput) {
        recipientInput.addEventListener('input', function () {
            clearValidation(recipientInput, 'recipientError');
        });
    }

    if (CcInput) {
        CcInput.addEventListener('input', function () {
            clearValidation(CcInput, 'CcError');
        });
    }

    if (BccInput) {
        BccInput.addEventListener('input', function () {
            clearValidation(BccInput, 'BccError');
        });
    }

    // Validate all emails on form submit
    $('#save-btn').on('click', async function (e) {
        e.preventDefault();

        let isValid = true;

        // Show loading state
        const originalText = $(this).text();
        $(this).prop('disabled', true).text('Validating...');

        // Validate Sender
        const senderEmail = $('#senderInput').val();
        if (senderEmail && senderEmail.trim()) {
            const senderResult = await validateEmail(senderEmail);
            if (senderResult.success) {
                showSuccess(senderInput, 'senderError');
            } else {
                showError(senderInput, 'senderError', senderResult.error?.type || 'Invalid sender email');
                isValid = false;
            }
        } else {
            showError(senderInput, 'senderError', 'Sender email is required');
            isValid = false;
        }

        // Validate Recipient
        const recipientEmail = $('#recipientInput').val();
        if (recipientEmail && recipientEmail.trim()) {
            const recipientResult = await validateEmail(recipientEmail);
            if (recipientResult.success) {
                showSuccess(recipientInput, 'recipientError');
            } else {
                showError(recipientInput, 'recipientError', recipientResult.error?.type || 'Invalid recipient email');
                isValid = false;
            }
        } else {
            showError(recipientInput, 'recipientError', 'Recipient email is required');
            isValid = false;
        }

        // Validate Cc (optional)
        const ccEmails = $('#CcInput').val();
        if (ccEmails && ccEmails.trim()) {
            const ccResult = await validateMultipleEmails(ccEmails);
            if (ccResult.success) {
                showSuccess(CcInput, 'CcError');
            } else {
                showError(CcInput, 'CcError', ccResult.error?.type || 'Invalid Cc email(s)');
                isValid = false;
            }
        }

        // Validate Bcc (optional)
        const bccEmails = $('#BccInput').val();
        if (bccEmails && bccEmails.trim()) {
            const bccResult = await validateMultipleEmails(bccEmails);
            if (bccResult.success) {
                showSuccess(BccInput, 'BccError');
            } else {
                showError(BccInput, 'BccError', bccResult.error?.type || 'Invalid Bcc email(s)');
                isValid = false;
            }
        }

        // Restore button state
        $(this).prop('disabled', false).text(originalText);

        // If all valid, submit the form
        if (isValid) {
            // Submit the form
            $(this).closest('form').off('submit').submit();
        } else {
            alert('Please fix the invalid email addresses before sending.');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('attachment-input');
        const list = document.getElementById('file-list');

        const MAX_TOTAL_SIZE = 20 * 1024 * 1024;

        var existingAttachments = <?=
                json_encode(
                        array_map(function ($a) {
                            return [
                                'id' => $a->id,
                                'name' => $a->file_name,
                            ];
                        }, $email_model->quotationEmailAttachments ?? [])
                )
                ?>;
        console.log("Existing attachments:", existingAttachments);

        let fileStore = new DataTransfer();
        renderList();

        input.addEventListener('change', function (e) {
            let tempStore = new DataTransfer();
            Array.from(fileStore.files).forEach(f => tempStore.items.add(f));

            Array.from(input.files).forEach(file => {
                const duplicate = Array.from(tempStore.files).some(f =>
                    f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
                );
                if (!duplicate) {
                    tempStore.items.add(file);
                }
            });

            let totalSize = 0;
            Array.from(tempStore.files).forEach(f => totalSize += f.size);

            if (totalSize > MAX_TOTAL_SIZE) {
                alert("Total size exceeded 20MB.");
            } else {
                fileStore = tempStore;
            }

            input.files = fileStore.files;
            renderList();
        });

        function renderList() {
<?php $compareName = preg_replace("/[^a-zA-Z0-9.]/", "-", $quotation->quotation_no) . '.pdf' ?>;

            list.innerHTML = '';

            existingAttachments.forEach((attachment, i) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center p-1';

                const filenameSpan = document.createElement('span');

                if (attachment.url) {
                    const a = document.createElement('a');
                    a.href = attachment.url;
                    a.textContent = attachment.name;
                    a.target = '_blank';
                    filenameSpan.appendChild(a);
                } else {
                    filenameSpan.textContent = attachment.name;
                }
                li.appendChild(filenameSpan);

                const buttonGroup = document.createElement('div');

                const viewAttachmentBtn = document.createElement('a');
                viewAttachmentBtn.href = `${readPdfUrl}?id=${currentId}`;
                viewAttachmentBtn.className = 'btn btn-sm btn-primary mr-1';
                viewAttachmentBtn.target = '_blank';
                viewAttachmentBtn.innerHTML = 'View';
                buttonGroup.appendChild(viewAttachmentBtn);

                const removeAttachmentBtn = document.createElement('button');
                removeAttachmentBtn.type = 'button';
                removeAttachmentBtn.className = 'btn btn-sm btn-danger ms-2';
                removeAttachmentBtn.textContent = 'Remove';
                removeAttachmentBtn.dataset.id = attachment.id;
                removeAttachmentBtn.addEventListener('click', function () {
                    removeExistingAttachment(attachment.id);
                });
                buttonGroup.appendChild(removeAttachmentBtn);

                li.appendChild(buttonGroup);
                list.appendChild(li);
            });

            Array.from(fileStore.files).forEach((file, idx) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center p-1';

                const filenameSpan = document.createElement('span');
                filenameSpan.textContent = file.name;
                li.appendChild(filenameSpan);

                const buttonGroup = document.createElement('div');

                const viewBtn = document.createElement('a');
                if (file.name === <?= json_encode($compareName) ?>) {
                    viewBtn.href = `${readPdfUrl}?id=${currentId}`;
                } else {
                    const fileUrl = URL.createObjectURL(file);
                    viewBtn.href = fileUrl;
                }
                viewBtn.className = 'btn btn-sm btn-primary mr-1';
                viewBtn.target = '_blank';
                viewBtn.innerHTML = 'View';
                buttonGroup.appendChild(viewBtn);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger ms-2';
                removeBtn.textContent = 'Remove';
                removeBtn.dataset.index = idx;
                removeBtn.addEventListener('click', function () {
                    removeFile(parseInt(this.dataset.index, 10));
                });
                buttonGroup.appendChild(removeBtn);

                li.appendChild(buttonGroup);
                list.appendChild(li);
            });
        }

        function removeFile(index) {
            if (!confirm('Are you sure you want to delete this attachment?')) {
                return;
            }
            const newStore = new DataTransfer();
            Array.from(fileStore.files).forEach((f, i) => {
                if (i !== index)
                    newStore.items.add(f);
            });

            fileStore = newStore;
            input.files = fileStore.files;
            renderList();
        }

        function removeExistingAttachment(id) {
            if (!confirm('Are you sure you want to delete this attachment?')) {
                return;
            }
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['ajax-delete-attachment']) ?>?id=' + id,
                method: 'POST',
                data: {id: id},
                success: function (response) {
                    if (response.success) {
                        existingAttachments = existingAttachments.filter(att => att.id !== id);
                        renderList();
                    } else {
                        alert("Failed to delete attachment: " + (response.error || "Unknown error"));
                    }
                },
                error: function () {
                    alert("Server error while deleting attachment.");
                }
            });
        }
    });
</script>






