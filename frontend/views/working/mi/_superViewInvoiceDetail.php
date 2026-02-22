<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>

<div class="">
    <?php if ($invoiceDetail) { ?>
        <table class="table table-sm table-striped table-bordered">
            <thead  class="thead-light">
                <tr>
                    <th class="text-center">Invoice Type</th>
                    <th class="text-center">Document Idx No</th>
                    <th class="text-center">ProForma?</th>
                    <th class="text-center">Particular</th>
                    <th class="text-center">P.O. Number</th>
                    <th class="text-center">Invoice No.</th>
                    <th  class="text-center">Amount</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($invoiceDetail as $key => $invoice) {
                    ?>
                    <tr>
                        <td><?= $invoice['doc_type_name'] ?></td>
                        <td><?= $invoice['index_no'] ?></td>
                        <td class="text-center"><?= $invoice['isPerforma'] ? "Yes" : "No" ?></td>
                        <td><?= $invoice['particular'] ?></td>
                        <td><?= $invoice['po_number'] ?></td>
                        <td><?= $invoice['reference_no'] ?></td>
                        <td class='text-right'><?= $invoice['currency_sign'] . ' ' . MyFormatter::asDecimal2($invoice['amount']) ?></td>
                        <td>
                            <?php
                            echo Html::a('<i class="fas fa-edit"></i>',
                                    "javascript:",
                                    [
                                        'class' => 'text-success',
                                        'title' => 'Edit',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
                                        'data-mi_id' => $invoice['mi_id'],
                                        'data-idxno' => $invoice['index_no'],
                                        'data-po_number' => $invoice['po_number'],
                                        'data-po_id' => $invoice['po_id'],
                                        'data-particular' => $invoice['particular'],
                                        'data-reference_no' => $invoice['reference_no'],
                                        'data-currency_sign' => $invoice['currency_sign'],
                                        'data-amount' => MyFormatter::asDecimal2($invoice['amount']),
                            ])
                            ?>
                        </td>   
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <?php
    } else {
        echo '<p class="text-center">(No Record)</p>';
    }
    ?>
</div>
