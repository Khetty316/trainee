<?php

use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProjectMaster */
?>
<div class="project-master-view-main">
    <table class="table table-sm table-bordered table-striped">
        <tr>
            <td style="width:20%">Contract Summary</td>
            <td style="width:2%" class="text-center">:</td>
            <td class="text-right pr-md-5">RM <?= MyFormatter::asDecimal2($model->contract_sum) ?></td>
            <td style="width:20%">Commencement Date</td>
            <td style="width:2%" class="text-center">:</td>
            <td style="width:28%"><?= MyFormatter::asDate_Read($model->commencement_date) ?></td>
        </tr>
        <tr>
            <td>Total V.O.</td>
            <td class="text-center">:</td>
            <td class="text-right pr-md-5">RM <?= MyFormatter::asDecimal2($model->getVoTotal()) ?></td>
            <td>Handover Date</td>
            <td class="text-center">:</td>
            <td><?= MyFormatter::asDate_Read($model->handover_date) ?></td>
        </tr>
        <tr>
            <td>Progress Status</td>
            <td class="text-center">:</td>
            <td><?= $model->project_status ?></td>
            <td>DLP Expiry Date</td>
            <td class="text-center">:</td>
            <td><?= MyFormatter::asDate_Read($model->dlp_expiry_date) ?></td>
        </tr>
    </table>
</div>
<script>
    window.onbeforeunload = function () {
        var currentYOffset = window.pageYOffset;  // save current page postion.
        setCookie('jumpToScrollPostion', currentYOffset, 2);
    };
    $(function () {
        $(document).on('beforeSubmit', 'form', function (event) {
            $(".submitBtn").attr('disabled', true).addClass('disabled');
            $(".submitBtn").html('Submitting...');
            var currentYOffset = window.pageYOffset;  // save current page postion.
            setCookie('jumpToScrollPostion', currentYOffset, 2);
        });


        // check if we should jump to postion.
        var jumpTo = getCookie('jumpToScrollPostion');
        if (jumpTo !== "undefined" && jumpTo !== null) {
            window.scrollTo(0, jumpTo);
            eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
        }

    });
</script>