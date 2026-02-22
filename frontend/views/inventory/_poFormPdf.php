<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;

?>

<!DOCTYPE html>

<html>
    <head>
        <style>
            body {
                font-family: Times, sans-serif;
                font-size: 10pt;
            }

        </style>
    </head>
    <body>
        <div class="content-section">
            <!-- Items Table -->
            <table width="100%" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; border-bottom:1px solid #000;">ITEM NO.</td>
                        <td style="border-top:1px solid #000; border-bottom:1px solid #000;">DESCRIPTION</td>
                        <td style="border-top:1px solid #000; border-bottom:1px solid #000;">QTY</td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">U.PRICE</td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">DIS.</td>
                        <td align="right" style="border-top:1px solid #000; border-bottom:1px solid #000;">AMOUNT</td>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td align="center"><?= $i + 1 ?></td>
                            <td align="center"><?= $item->inventoryDetail->code ?></td>
                            <td>
                                <?= Html::encode($item->brand->name ?? '') ?>,
                                <?= Html::encode($item->model_description) ?><br>
                                MODEL: <?= Html::encode($item->model_type) ?>
                            </td>
                            <td align="center"><?= $item->order_qty . " " . $item->unit_type ?></td>
                            <td align="right"><?= number_format($item->unit_price, 2) ?></td>
                            <td align="right"><?= number_format($item->discount_amt, 2) ?></td>
                            <td align="right"><?= number_format($item->total_price, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Comment Section -->
            <?php if ($po->comment): ?>
                <div class="comment-section">
                    <div style="margin-top: 50px; margin-left: 200px; white-space: pre-wrap;"><?= Html::encode($po->comment) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </body>
