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
                <div class="table-responsive">
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
                                $isLatest = $index === 0 ? 'table-success fw-bold' : '';
                                if ($detail->recipient !== null) {
                                    $noHistory = 0;
                                    $sentBy = ($detail->sent_by === null ? '-' : ($detail->sentBy->fullname . ' @ ' . \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($detail->sent_at)));
                                    ?>
                                    <tr class=<?= $isLatest ?>>
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
                    <div class="col-sm-12 col-md-6">
                        <?= $form->field($email_model, 'sender')->textInput([
                            'maxlength' => true, 
                            'required' => true,
                            'id' => 'senderInput'
                            ]) ?>
                        
                        <?=
                            Html::a('Check', '#', ['id' => 'checkSender', 'class' => 'btn btn-success check-btn'])
                        ?>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        
                        <?= $form->field($email_model, 'recipient')->textInput([
                            'maxlength' => true, 
                            'required' => true,
                            'id' => 'recipientInput',
                            'list' => 'recipientList',
                            'placeholder' => 'Select or type recipient email',
                        ]);
                    ?>
                        <datalist id="recipientList">
                            <?php 
                                $emails = frontend\models\client\Clients::getEmails($client_id);
                                foreach ($emails as $email) {
                                    echo "<option value='{$email}'>";
                                }
                            ?>
                        </datalist>
                        
                        <?=
                            Html::a('Check', '#', ['id' => 'checkRecipient', 'class' => 'btn btn-success check-btn'])
                        ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($email_model, 'Cc')->textInput(['id' => 'CcInput']) ?>
                        <?=
                            Html::a('Check', '#', [
                                'id' => 'checkCc', 
                                'class' => 'btn btn-success check-btn',
                                'style' => 'display: none;'
                                ])
                        ?>
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <?= $form->field($email_model, 'Bcc')->textInput(['id' => 'BccInput']) ?>
                        <?=
                            Html::a('Check', '#', [
                                'id' => 'checkBcc', 
                                'class' => 'btn btn-success check-btn',
                                'style' => 'display: none;'
                                ])
                        ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($email_model, 'subject')->textarea() ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($email_model, 'content')->textarea(['rows' => 10]) ?>
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
                Html::submitButton('Send email', [
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
    
     if (recipientInput) {
        recipientInput.setAttribute('data-kw-ignore', 'true');
    }
    if (senderInput) {
        senderInput.setAttribute('data-kw-ignore', 'true');
    }
    
    $('#checkRecipient').on('click', function(e) {
        e.preventDefault();
        let recipientEmail = $('#recipientInput').val();
        $.post('<?= Url::to(['projectqrevision/check-email-exists']) ?>',
            { email_address: recipientEmail },
            function(response) {
            if (response.success) {
                recipientInput.classList.remove('is-invalid');
                recipientInput.classList.add('is-valid');
                alert('Recipient email is valid.');
            } else {
                recipientInput.classList.remove('is-valid');
                recipientInput.classList.add('is-invalid');
                alert(response.error?.type || 'Recipient email not found.');
            }
        }
        );
    });
    
    $('#checkSender').on('click', function(e) {
        e.preventDefault();
        let senderEmail = $('#senderInput').val();
        $.post('<?= Url::to(['projectqrevision/check-email-exists']) ?>',
            { email_address: senderEmail },
            function(response) {
                if (response.success) {
                    senderInput.classList.remove('is-invalid');
                    senderInput.classList.add('is-valid');
                    alert('Sender email is valid.');
                } else {
                    senderInput.classList.remove('is-valid');
                    senderInput.classList.add('is-invalid');
                    alert(response.error?.type || 'Sender email not found.');
                }
            }
        );
    });
    
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    $('#checkCc').on('click', async function(e) {
    e.preventDefault();
    const Ccinput = document.querySelector('#CcInput');
    const CcEmails = $('#CcInput').val();
    const CcArray = CcEmails.split(',').map(email => email.trim()).filter(e => e !== '');
    let allValid = true;
    for (const email of CcArray) {
        const response = await $.post('<?= Url::to(['projectqrevision/check-email-exists']) ?>', { email_address: email });
        await sleep(1000);
        if (!response.success) {
            Ccinput.classList.remove('is-valid');
            Ccinput.classList.add('is-invalid');
            allValid = false;
            alert(response.error?.type || `The email address ${email} is not found.`);
            break; 
        }
    }
    if (allValid) {
        Ccinput.classList.remove('is-invalid');
        Ccinput.classList.add('is-valid');
        alert('All the email addresses are valid.');
    }
});
const Ccinput = document.querySelector('#CcInput');
    Ccinput.addEventListener('input', function() {
        if (Ccinput.value.trim() === '') {
            Ccinput.classList.remove('is-invalid');
            Ccinput.classList.remove('is-valid');
        }
    });
    
    $('#checkBcc').on('click', async function(e) {
    e.preventDefault();
    const Bccinput = document.querySelector('#BccInput');
    const BccEmails = $('#BccInput').val();
    const BccArray = BccEmails.split(',').map(email => email.trim()).filter(e => e !== '');
    let allValid = true;
    for (const email of BccArray) {
        const response = await $.post('<?= Url::to(['projectqrevision/check-email-exists']) ?>', { email_address: email });
        await sleep(1000);
        if (!response.success) {
            Bccinput.classList.remove('is-valid');
            Bccinput.classList.add('is-invalid');
            allValid = false;
            alert(response.error?.type || `The email address ${email} is not found.`);
            break; 
        }
    }
    if (allValid) {
        Bccinput.classList.remove('is-invalid');
        Bccinput.classList.add('is-valid');
        alert('All the email addresses are valid.');
    }
});
const Bccinput = document.querySelector('#BccInput');
Bccinput.addEventListener('input', function() {
    if (Bccinput.value.trim() === '') {
        Bccinput.classList.remove('is-invalid');
        Bccinput.classList.remove('is-valid');
    }
});
    
    $('#save-btn').on('click', function(e) {
        const invalidFields = document.querySelectorAll('.is-invalid');
        if (invalidFields.length > 0) {
            e.preventDefault();
            alert('Please use a registered email address before sending.');
        }
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const $ccInput = $('#CcInput');
    const $bccInput = $('#BccInput');
    const $checkCcBtn = $('#checkCc');
    const $checkBccBtn = $('#checkBcc');

    $ccInput.on('input', function() {
        const value = $(this).val().trim();

        if (value.length > 0) {
            $checkCcBtn.show();   // instantly show
        } else {
            $checkCcBtn.hide();   // instantly hide
        }
    });
    $bccInput.on('input', function() {
        const value = $(this).val().trim();
        
        if (value.length > 0) {
            $checkBccBtn.show();
        } else {
            $checkBccBtn.hide();
        }
    });
        const input = document.getElementById('attachment-input');
        const list = document.getElementById('file-list');

        const MAX_TOTAL_SIZE = 20 * 1024 * 1024;

        var existingAttachments = <?=
                json_encode(
                        array_map(function ($a) {
                            return [
                                'id' => $a->id, // database id of attachment
                                'name' => $a->file_name, // or attribute storing filename
                            ];
                        }, $email_model->quotationEmailAttachments ?? [])
                )
                ?>;
        console.log("Existing attachments:", existingAttachments);
        // DataTransfer used to build an up-to-date FileList we can assign to input.files
        let fileStore = new DataTransfer();
        renderList();

        input.addEventListener('change', function (e) {
            // Add newly selected files into the store (avoid duplicates)
            let tempStore = new DataTransfer();
            Array.from(fileStore.files).forEach(f => tempStore.items.add(f));

            Array.from(input.files).forEach(file => {
//        const duplicate = Array.from(fileStore.files).some(f =>
                const duplicate = Array.from(tempStore.files).some(f =>
                    f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
                );
                if (!duplicate) {
//            fileStore.items.add(file);
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

            // assign the combined files back to the file input so form submission includes them
            input.files = fileStore.files;

//    // render the current list
            renderList();
        });



        function renderList() {
<?php $compareName = preg_replace("/[^a-zA-Z0-9.]/", "-", $quotation->quotation_no) . '.pdf' ?>;

            list.innerHTML = '';
            // render existing (DB) attachments
            existingAttachments.forEach((attachment, i) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center p-1';

                // Create a span for the filename instead of using textContent
                const filenameSpan = document.createElement('span');

                // either just name or clickable link
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

                // Create button wrapper
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
                li.className = 'list-group-item d-flex justify-content-between align-items-center';

                // Create a span for the filename
                const filenameSpan = document.createElement('span');
                filenameSpan.textContent = file.name;
                li.appendChild(filenameSpan);

                // Create button wrapper
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
//            headers: {
//                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
//            },
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






