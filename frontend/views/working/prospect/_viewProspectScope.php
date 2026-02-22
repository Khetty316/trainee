<?php

use yii\helpers\Html;
?>

<div class="">

    <?php
//    if(sizeof($scopes)>0){
    ?>


    <table class="table table-sm table-striped table-bordered">
        <thead  class="thead-light">
            <tr>
                <th style="width:70%">Scope</th><th class="text-right">Amount (RM)</th><th></th>
            </tr>
        </thead>
        <?php
        foreach ($scopes as $key => $scope) {
            ?>
            <tr>
                <td><?= $scope['scope'] ?>   <?php
                    if ($scope->attachment) {
                        echo Html::a("<i class='far fa-file-alt' ></i>", "/working/prospect/get-file-scope?filename=" . urlencode($scope->attachment)
                                . "&projCode=NPL" . $scope->master_prospect, ['target' => "_blank", 'class' => 'm-0 float-right p-0', 'title' => "Click to view"]);
                    }
                    ?>
                </td>
                <td class="text-right"><?= common\models\myTools\MyFormatter::asDecimal2($scope['amount']) ?></td>
                <td class="text-center">
                    <p class="p-0 m-0">
                        <?php
                        echo Html::a('<i class="far fa-edit text-primary pr-2"></i>', "javascript:", ['title' => 'Edit', "value" => \yii\helpers\Url::to('/working/prospect/create-scope-ajax?id=' . $scope->id), "class" => "modalButtonSmall"]);
                        if (!$scope->prospectDetailRevisionScopes) {
                            echo Html::a('<i class="far fa-trash-alt text-danger"></i>', "javascript:deleteScope(" . $scope->id . ")", ['title' => 'Delete', 'data-confirm' => "Remove this scope?"]);
                        }else{
                            echo Html::a('<i class="far fa-trash-alt text-secondary"></i>', "javascript:alert('Unable to delete, as it is involved in one of the revision')", ['title' => 'Unable Delete']);
                        }
                        ?>&nbsp;
                    </p>
                </td>
            </tr>
            <?php
        }
        if (sizeof($scopes) == 0) {
            echo '<tr><td colspan="3" class="text-center text-danger">(NO RECORD FOUND)</td></tr>';
        }
        ?>
    </table>
</div>
<script>
    $(function () {
        $(".modalButtonSmall").click(function () {
            $("#myModalSmall").modal("show")
                    .find("#myModalContentSmall")
                    .load($(this).attr('value'));
        });
    });

    function deleteScope(id) {
        var url = '/working/prospect/delete-scope-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            }
        }).done(function (response) {
            console.log("HELLO!!");
            console.log(response);


            console.log("HELLO!!2");
            if (response.data.success === true) {
                reloadScopesDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }



</script>