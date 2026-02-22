<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Update User: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">
    <h3><?php
        if ($model->status == 0) {
            $status = "<span class='text-danger'>(Deleted)<span>";
        } else if ($model->status == 9) {
            $status = "<span class='text-warning'>(Inactive)<span>";
        } else if ($model->status == 10) {
            $status = "<span class='text-success'>(Active)<span>";
        }

        echo Html::encode($this->title) . " " . $status;
        ?></h3>
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>

<script>
    function activateUser(id) {
        var ans = confirm("Are you sure to Activate the user?");
        if (ans) {
            $("#form-edit").attr('action', '/sysadmin/user/activateuser?id=' + id);
            $("#form-edit").submit();
        }
    }

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

</script>