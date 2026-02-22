<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>

<div class="">
    <?php if ($invoices) { ?>
        <table class="table table-sm table-striped table-bordered">
            <thead  class="thead-light">
                <tr>
                    <th style="width:140px">Doc Index No.</th>
                    <th style="">Invoice No.</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-center" style="width:140px">Set as Sub</th>
                    <th class="text-center" style="width:140px">Set as Main</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($invoices as $key => $inv) {
                    if ($inv->id == $mi->id) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php
                            echo Html::a($inv->index_no, "/working/mi/view?id=$inv->id", ['target' => '_blank']);
                            echo Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/mi/get-file?filename=" . urlencode($inv->filename), ['target' => "_blank", 'class' => 'm-2', 'title' => "Click to view me"]);
                            ?>
                        </td>
                        <td>
                            <?= $inv->reference_no ?>
                        </td>
                        <td class="text-right">
                            <p class="p-0 m-0">
                                <?php
                                echo $inv->currency0->currency_sign . " " . MyFormatter::asDecimal2_emptyDash($inv->getTotalAmount());
                                ?>
                            </p>
                        </td>
                        <td class="text-center">
                            <?php
                            $subSelected = false;
                            if ($inv->finalInvoice) {
                                $subSelected = true;
                                if ($inv->finalInvoice->id == $mi->id) {
                                    echo '<input type="checkbox" name="subInvoice[]" value="' . $inv->id . '" checked/>';
                                    echo Html::checkbox('subInvoiceUncheck[]', false, ['value' => $inv->id, 'class' => 'hidden']);
                                } else {
                                    echo Html::a($inv->finalInvoice->index_no, "/working/mi/view?id=" . $inv->finalInvoice->id, ['target' => '_blank']);
                                    echo Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/mi/get-file?filename=" . urlencode($inv->finalInvoice->filename), ['target' => "_blank", 'class' => 'm-2', 'title' => "Click to view me"]);
                                }
                            } else {
                                echo '<input type="checkbox" name="subInvoice[]" value="' . $inv->id . '" />';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            echo Html::checkbox('mainInvoice[]', ($mi['final_invoice'] == $inv->id ? true : false), ['value' => $inv->id, 'disabled' => $subSelected]);
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <p class="font-weight-lighter text-success">Set as Sub - Selected invoice(s) will not be included in cost calculation.</p>
        <p class="font-weight-lighter text-success">Set as Main - This invoice will not be included in cost calculation.</p>

        <?php
    } else {
        echo '<p class="text-center">(No Record)</p>';
    }
    ?>
</div>
<script>
    $(function () {
        $(".modalButtonSmall").click(function () {
            $("#myModalSmall").modal("show")
                    .find("#myModalContentSmall")
                    .load($(this).attr('value'));
        });

        $("input[name='subInvoice[]']").click(function (e) {
            var isChecked = $(this).is(":checked");
            if (isChecked) {
                $(this).parent().parent().find('input[name ="mainInvoice[]"]').prop("disabled", true);
                $(this).parent().find('input[name ="subInvoiceUncheck[]"]').prop("checked", false);
            } else {
                $(this).parent().parent().find('input[name ="mainInvoice[]"]').prop("disabled", false);
                $(this).parent().find('input[name ="subInvoiceUncheck[]"]').prop("checked", true);

            }
        });

        $("input[name='mainInvoice[]']").click(function (e) {


            var thisChecked = $(this).is(":checked");
            $(this).parent().parent().parent().find("input[name='mainInvoice[]']").prop("checked", false);
            $(this).prop("checked", thisChecked);
        });
    });





</script>