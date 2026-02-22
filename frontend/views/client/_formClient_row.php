<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var $form yii\bootstrap4\ActiveForm
 * @var $contact frontend\models\client\ClientContact
 * @var $index integer
 */
$key = $contact->id ?? $index;
?>
<tr data-index="<?= $key ?>">
    <td>
        <?= Html::activeHiddenInput($contact, "[$key]id") ?>
        <?= Html::activeTextInput($contact, "[$key]name", ['class' => 'form-control', 'maxlength' => true]) ?>
    </td>
    <td><?= Html::activeTextInput($contact, "[$key]position", ['class' => 'form-control', 'maxlength' => true]) ?></td>
    <td><?= Html::activeTextInput($contact, "[$key]contact_number", ['class' => 'form-control', 'maxlength' => true]) ?></td>
    <td><?= Html::activeTextInput($contact, "[$key]fax", ['class' => 'form-control', 'maxlength' => true]) ?></td>
    <td>
        <?=
        Html::activeTextInput($contact, "[$key]email_address", [
            'class' => 'form-control email-input',
            'maxlength' => true,
            'id' => 'email-input-' . $key,
            'data-key' => $key
        ])
        ?>
        <div id="email-error-<?= $key ?>" class="invalid-feedback" style="display: none;"></div>
    </td>
    <td>
        <button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove()">
            <i class="fas fa-minus-circle"></i>
        </button>
    </td>
</tr>