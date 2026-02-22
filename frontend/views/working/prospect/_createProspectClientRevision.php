<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
?>
<div class="prospect-client-rev-create">


    <div class="prospect-client-rev-form">

        <?php
        $form = ActiveForm::begin([
                    'action' => '/working/prospect/create-client-revision-ajax',
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off', 'class' => 'form-inline'],
                    'id' => 'createProspectClientRevisionForm',
        ]);

        ?>
        <?= Html::hiddenInput('prospectDetailId', $revision->prospect_detail_id) ?>

        <div class="form-row w-100">
            <table class="table table-sm table-striped table-bordered w-100">
                <tr>
                    <th>Scope</th>
                    <th class="text-right">Amount (RM)</th>
                    <th class="text-right">Charges (%)</th>
                    <th class="text-right">Final Amount (RM)</th>
                </tr>
                <?php
                foreach ($masterScope as $key => $scope) {
                    ?>

                    <tr>
                        <td><?= $scope['scope'] ?></td>
                        <td class="text-right">
                            <?php
                            echo MyFormatter::asDecimal2($scope['amount']);
                            echo Html::hiddenInput('amt[]', $scope['amount'], ['id' => 'amt_' . $scope->id]);
                            echo Html::hiddenInput('scopeId[]', $scope['id']);
                            echo Html::hiddenInput('scope[]', $scope['scope']);
                            ?>
                        </td>
                        <td class="text-right">
                            <div class="custom-switch form-check-inline">
                                <?php echo Html::checkbox('select[]', true, ['data-id' => $scope->id, 'class' => 'select custom-control-input', 'id' => 'sel_' . $scope->id]) ?>
                                <label class="custom-control-label" for="sel_<?= $scope->id ?>">Select</label>
                            </div>
                            <?= Html::input('number', 'percent[]', '100', ['id' => 'percent_' . $scope->id, 'class' => 'form-control text-right percentage_input', 'data-id' => $scope->id]) ?> % 
                        </td>
                        <td class="text-right">
                            <?php
                            echo Html::input('number', 'finalAmt', MyFormatter::asDecimal2NoSeparator($scope['amount']),
                                    ['id' => 'finalAmt_' . $scope->id, 'class' => 'form-control text-right finalAmt', 'readonly' => 'true'])
                            ?>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="3" class="text-right">
                        <b>Total (RM):</b>
                    </td>
                    <td class="text-right bold">
                        <?= Html::input('number', 'totFinAmt', '', ['id' => 'totFinAmt', 'class' => 'text-right form-control']) ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-row">
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
            </div>
        </div>



    </div>
</div>

<div class="form-group">
    <?= Html::button('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success', 'onclick' => 'submitForm()']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>
    function submitForm() {
        var form = $("#createProspectClientRevisionForm");
        var data = form.serializeArray();
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        }).done(function (response) {
            if (response.data.success === true) {
                $('#myModal').modal('toggle');
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }


    function assignValueFromCompany(ui) {
        $('#prospectdetail-pic_contact').val(ui.item.contact_number);
        $('#prospectdetail-client_id').val(ui.item.id);
        $('#prospectdetail-pic_name').val(ui.item.contact_person);
        $('#prospectdetail-pic_email').val(ui.item.email);

    }


    $(function () {
        calculateTotalFinalAmt();
        $(".percentage_input").change(function () {
            calculateFinalAmt($(this));
        });

        $(".select").click(function () {
            var input = $('#percent_' + $(this).attr('data-id'));
            if (this.checked) {
                input.val(100);
                input.attr('disabled', false);
                $("#amt_" + $(this).attr('data-id')).attr('disabled', false);
            } else {
                input.val(0);
                input.attr('disabled', true);
                $("#amt_" + $(this).attr('data-id')).attr('disabled', true);
            }
            calculateFinalAmt($("#percent_" + $(this).attr('data-id')));
        });




    });


    function calculateFinalAmt(thisItem) {

        var id = thisItem.attr('data-id');
        var percent = $("#percent_" + id).val();
        var amt = $("#amt_" + id).val();
        $("#finalAmt_" + id).val((percent * amt / 100).toFixed(2));
        calculateTotalFinalAmt();
    }

    function calculateTotalFinalAmt() {
        var total = 0;
        jQuery("input[name='finalAmt']").each(function () {
            total += parseFloat(this.value);
        });

        $("#totFinAmt").val((total.toFixed(2)));
    }

</script>



