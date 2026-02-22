<?php

use yii\helpers\Html;
?>
<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0">Pre-Requisition Form Details</h6>
    </div>
    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>PRF Code</th>
                    <th>Date of Material Required</th>
                    <!--<th>Total Amount (RM)</th>-->
                    <th>Item List</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" width="10%"><?= $prfMaster->prf_no ?></td>
                    <td class="text-center" width="20%"><?= Html::encode(date('d/m/Y', strtotime($prfMaster->date_of_material_required))) ?></td>
                    <!--<td class="text-right" width="20%" id="totalAmountPrf"><?= \common\models\myTools\MyFormatter::asDecimal2($prfMaster->total_amount) ?></td>-->
                    <td class="text-center">
                        <?php
//                            =
//                            Html::a(
//                                "<i class='far fa-file-alt fa-lg'></i>",
//                                ["/office/prereq-form-master/get-file", 'id' => $prfMaster->id],
//                                [
//                                    'title' => "View",
//                                    'target' => "_blank",
//                                    'data-pjax' => "0",
//                                ]
//                            )
                        ?>
                        <?=
                        Html::a(
                                "<i class='far fa-file-alt fa-lg'></i>",
                                "#",
                                [
                                    'title' => "Supporting Document",
                                    'value' => "/office/prereq-form-master/get-file?id=" . urlencode($prfMaster->id),
                                    'class' => "docModal"
                                ]
                        );
                        ?>
                        <?=
                        $this->render('/_docModal')
                        ?>  
                    </td>
                    <td>
                        <?= $prfMaster->status0->status_name ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
