<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
?>
<table class="table table-sm table-striped table-bordered" id="transferHistory">
    <thead class="thead-light text-center">
        <tr>
            <th>Service Date</th>
            <th>Remark</th>
            <th>Next Service Date</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($model as $key => $serviceRecord) {
            ?>
            <tr>
                <td><?= MyFormatter::asDate_Read($serviceRecord['service_date']) ?></td>
                <td class="text-wrap"><?= $serviceRecord['service_remark'] ?></td>
                <td><?= MyFormatter::asDate_Read($serviceRecord['next_service_date']) ?></td>
                <td><?= common\models\User::findOne($serviceRecord['created_by'])->fullname ?></td>
            </tr>
            <?php
        }


        if (!$model) {
            ?>
            <tr>
                <td colspan="4" class="text-center">-- No Record --</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>