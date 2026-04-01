<?php
use common\models\myTools\MyFormatter;
?>
<div>
    <div class="table-responsive">
        <div class="card mt-2 bg-light">
            <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                 id="heading_dispatch2" 
                 data-toggle="collapse" 
                 data-target="#collapse_dispatch2" 
                 aria-expanded="true" 
                 aria-controls="collapse_dispatch2">
                <span class="p-0 m-0 accordionHeader">Dispatch</span>
            </div>
            <div id="collapse_dispatch2" class="collapse show" aria-labelledby="heading_dispatch2">
                <div class="card-body p-1" style="background-color:white">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Model Type</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Dispatch Quantity</th>
                                <th>Remark</th>
                                <th>Dispatch By</th>
                                <th>Status</th>
                                <th>Status Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($confirmedDispatch)) {
                                foreach ($confirmedDispatch as $key => $dispatch):
                                    ?>
                                    <?php
                                    $detail = frontend\models\cmms\CmmsWoMaterialRequestDetails::findOne($dispatch['request_detail_id']);
                                    $createdBy = \common\models\User::findOne($dispatch['trial_created_by']);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $key + 1 ?></td> 
                                        <td><?= $detail->model_type ?></td>
                                        <td><?= $detail->brand ?></td>
                                        <td><?= $detail->descriptions ?></td>
                                        <td><?= $dispatch['dispatch_qty'] ?></td>
                                        <td><?= $dispatch['remark'] ?></td>
                                        <td><?= $createdBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($dispatch['trial_created_at'])?></td>
                                        <td><span class="text-success">Has Been Acknowledged</span></td>
                                        <td><?= MyFormatter::asDateTime_ReaddmYHi($dispatch['status_updated_at']) ?></td>    
                                    </tr>
                                    <?php
                                endforeach;
                            } else {
                                ?>
                                <tr><td colspan="9"><div class="empty">No results found.</div></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!--        <div class="card mt-2 bg-light">
            <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                 id="heading_adjust2" 
                 data-toggle="collapse" 
                 data-target="#collapse_adjust2" 
                 aria-expanded="true" 
                 aria-controls="collapse_adjust2">
                <span class="p-0 m-0 accordionHeader">Adjustment</span>
            </div>
            <div id="collapse_adjust2" class="collapse show" aria-labelledby="heading_adjust2">
                <div class="card-body p-1" style="background-color:white">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Model Type</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Adjusted Quantity</th>
                                <th>Remark</th>
                                <th>Adjusted By</th>
                                <th>Status</th>
                                <th>Status Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//                            if (!empty($confirmedAdjust)) {
//                                foreach ($confirmedAdjust as $key => $adjust):
                                    ?>
                                    <?php
//                                    $detail = frontend\models\cmms\CmmsWoMaterialRequestDetails::findOne($adjust['request_detail_id']);
//                                    $createdBy = \common\models\User::findOne($adjust['trial_created_by']);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php //= $key + 1 ?></td> 
                                        <td><?php //= $detail->model_type ?></td>
                                        <td><?php //= $detail->brand ?></td>
                                        <td><?php //= $detail->descriptions ?></td>
                                        <td><?php //= $adjust['dispatch_qty'] ?></td>
                                        <td><?php //= $adjust['remark'] ?></td>
                                        <td><?php //= $createdBy->fullname ?> @ <?php //= MyFormatter::asDateTime_ReaddmYHi($adjust['trial_created_at']) ?></td>
                                        <td><span class="text-success">Has Been Acknowledged</span></td>
                                        <td><?php //= MyFormatter::asDateTime_ReaddmYHi($adjust['status_updated_at']) ?></td>                 
                                    </tr>
                                    <?php
//                                endforeach;
//                            } else {
                                ?>
                                <tr><td colspan="9"><div class="empty">No results found.</div></td></tr>
                            <?php // } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>-->
        <div class="card mt-2 bg-light">
            <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                 id="heading_return2" 
                 data-toggle="collapse" 
                 data-target="#collapse_return2" 
                 aria-expanded="true" 
                 aria-controls="collapse_return2">
                <span class="p-0 m-0 accordionHeader">Return</span>
            </div>

            <div id="collapse_return2" class="collapse show" aria-labelledby="heading_return2">
                <div class="card-body p-1" style="background-color:white">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Model Type</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Return Quantity</th>
                                <th>Remark</th>
                                <th>Dispatch By</th>
                                <th>Status</th>
                                <th>Status Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($confirmedReturn)) {
                                foreach ($confirmedReturn as $key => $return):
                                    ?>
                                    <?php
                                    $detail = frontend\models\cmms\CmmsWoMaterialRequestDetails::findOne($return['request_detail_id']);
                                    $createdBy = \common\models\User::findOne($return['trial_created_by']);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $key + 1 ?></td> 
                                        <td><?= $detail->model_type ?></td>
                                        <td><?= $detail->brand ?></td>
                                        <td><?= $detail->descriptions ?></td>
                                        <td><?= $return['dispatch_qty'] ?></td>
                                        <td><?= $return['remark'] ?></td>
                                        <td><?= $createdBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($return['trial_created_at']) ?></td>
                                        <td><span class="text-success">Has Been Acknowledged</span></td>
                                        <td><?= MyFormatter::asDateTime_ReaddmYHi($return['status_updated_at']) ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                            } else {
                                ?>
                                <tr><td colspan="9"><div class="empty">No results found.</div></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


