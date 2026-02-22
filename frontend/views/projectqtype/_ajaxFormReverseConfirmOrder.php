<?php

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = "TITLE";
?>
<?php
$form = ActiveForm::begin();
?>
<div class="modal-body">
    <?php
    $revision = $projectQType->activeRevision ?? null;
    $client = $projectQType->activeClient->client ?? null;
    ?>
    <table class="table table-bordered table-sm">
        <tr>
            <td class="tdnowrap">Revision:&nbsp;&nbsp;&nbsp;</td>
            <td>
                <?php
                if ($revision) {
                    echo $revision->revision_description . "<br/>" . $revision->currency->currency_sign . MyFormatter::asDecimal2($revision->amount);
                } else {
                    echo "<span class='text-danger'>Please select a revision!</span>";
                }
                ?>
            </td>
        </tr>
        <?php ?>
        <tr>
            <td>Client:</td>
            <td>
                <?php
                if ($client) {
                    echo Html::encode($client->company_name);
                } else {
                    echo "<span class='text-danger '>(No Client)</span>";
                }
                ?>
            </td>
        </tr>
    </table>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <?php
    echo $form->field($projectQType, 'is_finalized', ['options' => ['class' => 'hidden']])->textInput(['value' => '0'])->label(false);
    echo Html::submitButton("Reverse Confirmation", ['class' => ' btn btn-warning submitButton']);
    ?>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(function () {
        $(this).find(".modal-header").html("<h4 class='text-warning'>Reverse Order Confirmation</h4>");
    })
</script>