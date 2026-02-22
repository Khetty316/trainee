<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\RefGeneralStatus;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\pettyCash\PettyCashRequestMaster */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$canModifyAttachment = (
        $model->status != RefGeneralStatus::STATUS_Completed &&
        $module === 'personal'
);
?>

<div class="petty-cash-request-master-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'receipt-form',
        'options' => [
            'autocomplete' => 'off',
            'enctype' => 'multipart/form-data'
        ],
        'action' => ['save-attachments',
            'id' => $postForm->id],
        'method' => 'post'
    ]);
    ?>

    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-6">
            <?=
                    $form->field($postForm, "receipt_amount")
                    ->input('number', [
                        'class' => 'form-control text-right receipt-amount',
                        'step' => 'any',
                        'min' => '0.01',
                        'value' => number_format($postForm->receipt_amount, 2),
                        'required' => true,
                        'readonly' => !$canModifyAttachment
                    ])
                    ->label()
            ?>
            <?php if ($canModifyAttachment): ?>
                <?=
                $form->field($postForm, 'attachments[]')->fileInput([
                    'multiple' => true,
                    'accept' => '.pdf',
                    'id' => 'attachment-input',
                    'required' => true
                ])
                ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-12 col-md-10 col-lg-12">
        <table class="table table-bordered table-sm mt-2" id="file-table">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th>File</th>
                    <th style="width: 20%;">Uploaded By</th>
                    <th style="width: 20%;">Deleted By</th>
                    <th style="width: 15%;">Action</th>
                </tr>
            </thead>
            <tbody id="file-table-body">
                <?php if (!empty($attachments)): ?>
                    <?php foreach ($attachments as $index => $attachment): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= Html::encode(basename($attachment->file_name)) ?></td>
                            <td><?= Html::encode($attachment->uploadedBy->fullname ?? 'Unknown') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No attachments uploaded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($canModifyAttachment) { ?>
        <div class="form-group mt-5 mb-5">
            <?=
            Html::a("Save",
                    ['return-receipt', 'id' => $model->id],
                    [
                        'class' => 'btn btn-success float-right',
                        'data-method' => 'post',
                    ]
            )
            ?>

            <?php if (isset($postForm->id)) { ?>
                <?=
                Html::a("Cancel Submission",
                        ['cancel-return-receipt', 'id' => $postForm->id],
                        [
                            'class' => 'btn btn-danger float-right mr-2',
                            'data-confirm' => 'Are you sure you want to cancel this return receipt?',
                            'data-method' => 'post',
                        ]
                )
                ?>
            <?php } ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
<script>
    {
        const canDeleteAttachment = <?= $canModifyAttachment ? 'true' : 'false' ?>;
        const attachmentInput = document.getElementById('attachment-input');
        const fileList = document.getElementById('file-list');

        var existingAttachments = <?=
    json_encode(
            array_map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->file_name,
                    'uploaded_by' => $a->uploaded_by !== null ? User::findOne($a->uploaded_by)->fullname : '-',
                    'deleted_at' => $a->deleted_at,
                    'deleted_by' => $a->deleted_by !== null ? User::findOne($a->deleted_by)->fullname : '-',
                ];
            }, $postForm->pettyCashRequestPostAttachments ?? [])
    )
    ?>;

        let fileStore = new DataTransfer();
        renderList();

        attachmentInput.addEventListener('change', function (e) {
            const selected = Array.from(attachmentInput.files);

            selected.forEach(file => {
                const duplicate = Array.from(fileStore.files).some(f =>
                    f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
                );
                if (!duplicate)
                    fileStore.items.add(file);
            });
            attachmentInput.files = fileStore.files;

//    // render the current list
            renderList();
        });

        function renderList() {
            const tableBody = document.getElementById('file-table-body');
            tableBody.innerHTML = '';

            existingAttachments.forEach((attachment, index) => {
                const tr = document.createElement('tr');

                // No.
                const noTd = document.createElement('td');
                noTd.textContent = index + 1;
                noTd.className = 'text-center';
                tr.appendChild(noTd);

                // File
                const fileTd = document.createElement('td');
                if (attachment.url) {
                    const a = document.createElement('a');
                    a.href = attachment.url;
                    a.textContent = attachment.name;
                    a.target = '_blank';
                    fileTd.appendChild(a);
                } else {
                    fileTd.textContent = attachment.name;
                }
                tr.appendChild(fileTd);

                // Uploaded At
                const uploadedTd = document.createElement('td');
                uploadedTd.textContent = attachment.uploaded_by;
                uploadedTd.className = 'text-center';
                tr.appendChild(uploadedTd);

                // Deleted At
                const deletedTd = document.createElement('td');
                deletedTd.textContent =
                        attachment.deleted_at
                        ? `${attachment.deleted_by ?? '-'} @ ${formatDateTime(attachment.deleted_at)}`
                        : '-';
                deletedTd.className = 'text-center text-danger';
                tr.appendChild(deletedTd);
                // Actions
                const actionTd = document.createElement('td');
                if (!attachment.deleted_at || !attachment.deleted_by) {

                    actionTd.className = 'text-center';

                    const basePdfUrl = <?= json_encode(\yii\helpers\Url::to(['office/petty-cash/read-pdf', 'id' => $model->id])) ?>;
                    const readPdfUrl = basePdfUrl + '&file_name=' + encodeURIComponent(attachment.name);

                    // View button
                    const viewBtn = document.createElement('a');
                    viewBtn.href = readPdfUrl;
                    viewBtn.className = 'btn btn-sm btn-primary mr-1';
                    viewBtn.target = '_blank';
                    viewBtn.innerHTML = 'View';
                    actionTd.appendChild(viewBtn);

                    // Delete button
                    if (canDeleteAttachment) {
                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.className = 'btn btn-sm btn-danger';
                        deleteBtn.innerHTML = 'Delete';
                        deleteBtn.dataset.id = attachment.id;
                        deleteBtn.addEventListener('click', function () {
                            removeExistingAttachment(attachment.id);
                        });
                        actionTd.appendChild(deleteBtn);
                    }
                }
                tr.appendChild(actionTd);
                tableBody.appendChild(tr);
            });

            // === Newly selected (unsaved) files ===
            Array.from(fileStore.files).forEach((file, idx) => {
                const tr = document.createElement('tr');

                // No.
                const noTd = document.createElement('td');
                noTd.textContent = existingAttachments.length + idx + 1;
                noTd.className = 'text-center';
                tr.appendChild(noTd);

                // File
                const fileTd = document.createElement('td');
                fileTd.textContent = file.name;
                tr.appendChild(fileTd);

                // Uploaded At
                const uploadedTd = document.createElement('td');
                uploadedTd.textContent = 'Pending upload';
                uploadedTd.className = 'text-center text-muted';
                tr.appendChild(uploadedTd);

                // Deleted At
                const deletedTd = document.createElement('td');
                deletedTd.textContent = '-';
                deletedTd.className = 'text-center';
                tr.appendChild(deletedTd);

                // Actions
                const actionTd = document.createElement('td');
                actionTd.className = 'text-center';

                // View button
                const viewBtn = document.createElement('a');
                const fileUrl = URL.createObjectURL(file);
                viewBtn.href = fileUrl;
                viewBtn.className = 'btn btn-sm btn-primary mr-1';
                viewBtn.target = '_blank';
                viewBtn.innerHTML = 'View';
                actionTd.appendChild(viewBtn);

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger';
                removeBtn.innerHTML = 'Remove';
                removeBtn.dataset.index = idx;
                removeBtn.addEventListener('click', function () {
                    removeFile(parseInt(this.dataset.index, 10));
                });
                actionTd.appendChild(removeBtn);

                tr.appendChild(actionTd);
                tableBody.appendChild(tr);
            });
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
                        location.reload();

                    } else {
                        alert("Failed to delete attachment: " + (response.error || "Unknown error"));
                    }
                },
                error: function () {
                    alert("Server error while deleting attachment.");
                }
            });
        }

        function formatDateTime(datetimeString) {
            if (!datetimeString)
                return '-';
            const date = new Date(datetimeString);
            if (isNaN(date))
                return '-';

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            const hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;

            return `${day}/${month}/${year} ${formattedHours}:${minutes} ${ampm}`;
        }

        function removeFile(index) {
            const newStore = new DataTransfer();
            Array.from(fileStore.files).forEach((f, i) => {
                if (i !== index)
                    newStore.items.add(f);
            });

            fileStore = newStore;
            attachmentInput.files = fileStore.files;
            renderList();
        }
    }

    $(document).off('submit', '#receipt-form').on('submit', '#receipt-form', function (e) {
        const files = document.getElementById('attachment-input')?.files || [];
        const amount = document.querySelector('.receipt-amount')?.value || '';
        const existingCount = (typeof existingAttachments !== 'undefined') ? existingAttachments.length : 0;

        // Validate amount
        if (!amount || parseFloat(amount) <= 0) {
            alert('Please enter a valid receipt amount.');
            e.preventDefault();
            return false;
        }

        // Validate attachments
        if (files.length === 0 && existingCount === 0) {
            alert('Please upload at least one PDF attachment.');
            e.preventDefault();
            return false;
        }
    });

</script>
