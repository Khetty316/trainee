<?php

use yii\helpers\Html;
?>

<?php if ($academicList) { ?>
    <table class="table table-sm table-bordered">
        <tr class="p-0 m-0">
            <td class="p-0 m-0">
                <?php
                foreach ($academicList as $key => $academic) {
                    ?>

                    <table class="table table-sm table-borderless <?= $key % 2 == 1 ? '' : 'table-active' ?> mt-0 mb-0 pt-0 pb-0">
                        <tr>
                            <td class="font-weight-bold br text-right" style="width: 30%">
                                <span class="float-left">
                                    <?php
                                    echo Html::a("<i class='far fa-edit'></i>", ['/resume/update-academic-qualification', 'id' => $academic->id], ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                                    if ($key == 0) {
                                        echo Html::tag('span', '<i class="fas fa-arrow-up"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move upwards", 'data-method' => 'post']);
                                    } else {
                                        echo Html::a('<i class="fas fa-arrow-up"></i>',
                                                'javascript:academicMove("up","' . $academic->user_id . '","' . $key . '")',
                                                ['class' => 'm-1 disabled', 'title' => "Move Upwards", 'data-method' => 'post']);
                                    }

                                    if ($key == sizeof($academicList) - 1) {
                                        echo Html::tag('span', '<i class="fas fa-arrow-down"></i>', ['class' => 'm-1 text-secondary', 'title' => "(Unable to move downwards", 'data-method' => 'post']);
                                    } else {
                                        echo Html::a('<i class="fas fa-arrow-down"></i>',
                                                'javascript:academicMove("down","' . $academic->user_id . '","' . $key . '")',
                                                ['class' => 'm-1', 'title' => "Move Downwards", 'data-method' => 'post']);
                                    }
                                    ?>
                                </span>
                                Academic Level
                            </td>
                            <td class=""><?= $academic->academic_level ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold br text-right">Course</td>
                            <td><?= $academic->academic_course . ' (' . $academic->academic_period . ')' ?></td>
                        </tr>
                        <?php
                        if ($academic->academic_honour != '') {
                            ?>
                            <tr>
                                <td class="font-weight-bold br text-right">Honour</td>
                                <td><?= $academic->academic_honour ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td class="font-weight-bold br text-right ">Institution</td>
                            <td class=""><?= $academic->academic_institution ?></td>
                        </tr>
                    </table>
                    <?php
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
} else if (!$academicList) {
    echo "<p class='text-center'> - - - - - NO RECORD - - - - - </p>";
}
?>


<script>
    function academicMove(action, userId, idxNo) {
        var url = '/resume/sort-academic-qualification-ajax?action=' + action + '&userId=' + userId + '&idxNo=' + idxNo;
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json'
        }).done(function (response) {
            if (response.data.success === true) {
                loadAcademicDiv(); // Function located at indexPersonalResume.php
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }
</script>