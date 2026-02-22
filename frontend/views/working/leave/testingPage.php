<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

?>
<link rel="stylesheet" href="/css/jquery.dataTables.min.css">
<script src="/js/jquery.dataTables.min.js"></script>
<div class="leave-master-index">
    <table class="display compact table-striped table-bordered" id="searchTable">
        <thead>
            <tr>
                <?php
                $headers = yii\helpers\ArrayHelper::toArray($users[0]);
                foreach ($headers as $key => $headers) {
                    echo "<th>" . $key . "</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($users as $key => $user) {
                echo "<tr>";
                foreach ($user as $key => $attribute) {
                    echo "<td>" . $attribute . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<script>
    $(function () {
        $('#searchTable').DataTable({
            searching: false,
            info: false,
            paging: false
        });
        initiateFilterTable($("#searchTable"));
    });




</script>