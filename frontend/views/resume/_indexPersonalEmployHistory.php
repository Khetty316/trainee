<?php

use yii\helpers\Html;
?>

<table class="table table-sm table-bordered table-striped">
    <?php
    foreach ($employList as $key => $employ) {
//                        $employ = new \frontend\models\resume\ResumeEmployHistory();
        ?>
        <tr>
            <td class="font-weight-bold br bt text-right" style="width: 30%">
                <span class="float-left">
                    <?php
                    echo Html::a("<i class='far fa-edit'></i>", ['/resume/update-employment-history', 'id' => $employ->id], ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                    if ($key == 0) {
                        echo Html::tag('span', '<i class="fas fa-arrow-up"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move upwards", 'data-method' => 'post']);
                    } else {
                        echo Html::a('<i class="fas fa-arrow-up"></i>',
                                'javascript:employhistoryMove("up","' . $employ->user_id . '","' . $key . '")',
                                ['class' => 'm-1 disabled', 'title' => "Move Upwards", 'data-method' => 'post']);
                    }
                    if ($key == sizeof($employList) - 1) {
                        echo Html::tag('span', '<i class="fas fa-arrow-down"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move downwards", 'data-method' => 'post']);
                    } else {
                        echo Html::a('<i class="fas fa-arrow-down"></i>',
                                'javascript:employhistoryMove("down","' . $employ->user_id . '","' . $key . '")',
                                ['class' => 'm-1', 'title' => "Move Downwards", 'data-method' => 'post']);
                    }
                    ?>
                </span>
                <?= $employ->employ_period ?>
            </td>
            <td class="bt"><?php
                echo Html::tag('p', ucfirst($employ->employ_role) . " -", ['class' => 'font-weight-bold mb-1']);
                echo Html::tag('p', strtoupper($employ->employ_company), ['class' => 'font-weight-bold mb-1']);
                echo nl2br($employ->employ_detail);
                ?>
            </td>
        </tr>
        <?php
    }
    if (!$employList) {
        echo "<p class='text-center'> - - - - - NO RECORD - - - - - </p>";
    }
    ?>
</table>

<script>
    function employhistoryMove(action, userId, idxNo) {
        var url = '/resume/sort-employment-history-ajax?action=' + action + '&userId=' + userId + '&idxNo=' + idxNo;
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function (response) {
            if (response.data.success === true) {
                loadEmployhistoryDiv(); // Function located at indexPersonalResume.php
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }
</script>