<?php

use yii\helpers\Html;
?>

<table class="table table-sm table-bordered table-striped">
    <?php
    foreach ($projectList as $key => $project) {
        ?>
        <tr>
            <td class="font-weight-bold br bt text-right" style="width: 30%">
                <span class="float-left">
                    <?php
                    echo Html::a("<i class='far fa-edit'></i>", ['/resume/update-project-ref', 'id' => $project->id], ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                    if ($key == 0) {
                        echo Html::tag('span', '<i class="fas fa-arrow-up"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move upwards", 'data-method' => 'post']);
                    } else {
                        echo Html::a('<i class="fas fa-arrow-up"></i>',
                                'javascript:projectRefMove("up","' . $project->user_id . '","' . $key . '")',
                                ['class' => 'm-1 disabled', 'title' => "Move Upwards", 'data-method' => 'post']);
                    }

                    if ($key == sizeof($projectList) - 1) {
                        echo Html::tag('span', '<i class="fas fa-arrow-down"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move downwards", 'data-method' => 'post']);
                    } else {
                        echo Html::a('<i class="fas fa-arrow-down"></i>',
                                'javascript:projectRefMove("down","' . $project->user_id . '","' . $key . '")',
                                ['class' => 'm-1', 'title' => "Move Downwards", 'data-method' => 'post']);
                    }
                    ?>
                </span>
            </td>
            <td class="bt">
                <?= $project->project_detail ?>
            </td>
        </tr>
        <?php
    }
    if (!$projectList) {
        echo "<p class='text-center'> - - - - - NO RECORD - - - - - </p>";
    }
    ?>
</table>

<script>
    function projectRefMove(action, userId, idxNo) {
        var url = '/resume/sort-project-ref-ajax?action=' + action + '&userId=' + userId + '&idxNo=' + idxNo;
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function (response) {
            if (response.data.success === true) {
                loadProjectRefDiv(); // Function located at indexPersonalResume.php
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }
</script>