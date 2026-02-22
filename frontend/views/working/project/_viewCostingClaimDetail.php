<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>

<div class="">
    <?php if ($claimDetail) { ?>
        <h3><?= $claimDetail[0]['claim_name'] ?> :</h3>
        <table class="table table-sm table-striped table-bordered">
            <thead  class="thead-light">
                <tr>
                    <th class="text-center">Claimant</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Detail</th>
                    <th class="text-center">Amount</th>

                </tr>
            </thead>
            <?php
            foreach ($claimDetail as $key => $detail) {
                ?>
                <tr>
                    <td><?= $detail->fullname ?></td>
                    <td class='text-center'><?= MyFormatter::asDate_Read($detail->date1) . ($detail->date2 ? " - " . MyFormatter::asDate_Read($detail->date2) : "") ?></td>
                    <td><?= $detail->detail ?></td>
                    <td class='text-right'>RM <?= MyFormatter::asDecimal2($detail->amount) ?></td>

                </tr>
                <?php
            }
            ?>
        </table>

        <?php
    } else {
        echo '<p class="text-center">(No Record)</p>';
    }
    ?>
</div>
