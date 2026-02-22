<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Update Authorisation: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update Authorisation';
?>
<div class="user-update">
    <div class="row">
        <div class='col-lg-1'></div>
        <div class="col-lg-10">
            <h3><?= Html::encode($this->title) ?></h3>

            <table class="table table-striped table-bordered table-sm">
                <tr>
                    <th>Auth Code</th><th>Auth Name</th><th>Assign Time</th><th>Action</th>
                </tr>
                <?php
                foreach ($authAssign as $key => $auth) {
                    $authItem = $auth->itemName;
                    $deleteBtn = Html::a('<i class="far fa-trash-alt text-danger"></i>', ["delete-user-auth", 'item_name' => $auth->item_name, 'user_id' => $model->id], ['data-confirm' => 'Remove this role?', 'data-method' => 'post']);
                    echo "<tr><td>" . $authItem->name . "</td><td>"
                    . $authItem->auth_fullname . "</td><td>"
                    . MyFormatter::asDateTime_ReaddmYHi($auth->created_at) . "</td><td>"
                    . $deleteBtn . "</tr></td>";
                }
                ?>
            </table>
        </div>
    </div>
    <?php
    $form = ActiveForm::begin([
//                    'action' => '/working/mi/procinsertgrn',
//                    'method' => 'post',
                    'id' => 'auth-form',
                'options' => ['autocomplete' => 'off']
    ]);
    ?>
    <div class="row">

        <div class='col-lg-1'></div>
        <div class="col-lg-5">
            <?php
            echo \common\models\myTools\MyCommonFunction::myDropDown($authList, "AuthAssignment[item_name]", 'form-control', 'theRole');
            echo Html::input('text', 'AuthAssignment[user_id]', $model->id, ['class' => 'form-control hidden']);
            ?>
        </div>
        <div class="col-lg-1">
            <?php
            echo Html::a("Add", 'javascript:addAuth()', ['class' => 'btn btn-success']);
            ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>

</div>

<script>
    function deactivateUser(id) {
        var ans = confirm("Are you sure to Deactivate the user?");
        if (ans) {
            $("#form-edit").attr('action', '/sysadmin/user/deactivateuser?id=' + id);
            $("#form-edit").submit();
        }
    }

    function deleteUser() {
        var ans = confirm("Are you sure to Delete the user?");
        if (ans) {
            $("#form-edit").attr('action', '/sysadmin/user/deleteuser?id=<?= $model->id ?>');
            $("#form-edit").submit();
        }
    }


    function addAuth() {
        var ans = confirm("Are you sure to add this Authorization / Role to <?= $model->fullname ?>?");
        if (ans) {
//            $("#form-edit").attr('action', '/sysadmin/user/addAuth?id=<?= $model->id ?>');
            $("#auth-form").submit();
        }
    }

</script>