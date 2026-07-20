<?php

use yii\helpers\Html;

/* @var $model frontend\models\client\ClientReminderLetterEmails */
/* @var $recipientList array */
?>

<div class="col-md-6">
    <div class="form-group">
        <label>
            Recipient <span class="text-danger">*</span>
        </label>
        <div class="recipient-dropdown">
            <button
                type="button"
                id="recipientBtn"
                class="form-control text-left">
                <?= !empty($model->recipient) ? Html::encode($model->recipient) : 'Select Recipient' ?>
            </button>
            <div id="recipientMenu" class="recipient-menu">
                <?php foreach ($recipientList as $item): ?>
                    <div
                        class="recipient-item"
                        data-email="<?= Html::encode($item['email']) ?>">
                            <?= Html::encode($item['label']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?= Html::activeHiddenInput($model, 'recipient', ['id' => 'recipient']) ?>
        </div>
    </div>
</div>

<style>
    .recipient-dropdown{
        position:relative;
    }
    #recipientBtn{
        text-align:left;
    }
    .recipient-menu{
        display:none;
        position:absolute;
        top:100%;
        left:0;
        right:0;
        background:#fff;
        border:1px solid #ced4da;
        border-top:none;
        border-radius:0 0 .25rem .25rem;
        max-height:220px;
        overflow-y:auto;
        z-index:99999;
        box-shadow:0 .5rem 1rem rgba(0,0,0,.15);
    }
    .recipient-item{
        padding:10px 15px;
        cursor:pointer;
    }
    .recipient-item:hover{
        background:#f8f9fa;
    }
</style>

<?php
$this->registerJs(<<<JS
$(document).on('click', '#recipientBtn', function (e) {
    e.stopPropagation();
    $('.recipient-menu').not($(this).siblings('.recipient-menu')).hide();
    $(this).siblings('.recipient-menu').toggle();
});
$(document).on('click', '.recipient-item', function (e) {
    e.stopPropagation();
    var email = $(this).data('email');
    $('#recipient').val(email);
    $('#recipientBtn').text(email);
    $(this).closest('.recipient-menu').hide();
});
$(document).on('click', function () {
    $('.recipient-menu').hide();
});
JS);
?>