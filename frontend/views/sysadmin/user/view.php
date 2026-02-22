<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'System Admin - Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">


    <div class="row">
        <div class='col-lg-1'></div>
        <div class="col-lg-10">
            <h3>
                <?php
                if ($model->status == 0) {
                    $status = "<span class='text-danger'>(Deleted)<span>";
                } else if ($model->status == 9) {
                    $status = "<span class='text-warning'>(Inactive)<span>";
                } else if ($model->status == 10) {
                    $status = "<span class='text-success'>(Active)<span>";
                }
                echo Html::encode($this->title) . " " . $status;
                ?>
            </h3>
            <p>
                <?= Html::a('Update <i class="fas fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
    </div>
    <div class="col-12">
        <?= $this->render('_detailViewUser', ['model' => $model,'isUserAdminPage'=>true]) ?>
    </div>
    <br/>
    <div class="row">
        <div class='col-lg-1'></div>
        <div class="col-lg-10">
            <h3>System Authorisation / Roles</h3>
            <p>               
                <?= Html::a('Update Auth <i class="fas fa-edit"></i>', ['update-user-auth', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            </p>
            <table class="table table-striped table-bordered table-sm">
                <tr>
                    <th>Auth Code</th><th>Auth Name</th><th>Assign Time</th>
                </tr>
                <?php
                foreach ($authAssign as $key => $auth) {
                    $authItem = $auth->itemName;
                    echo "<tr><td>" . $authItem->name . "</td><td>"
                    . $authItem->auth_fullname . "</td><td>"
                    . MyFormatter::asDateTime_ReaddmYHi($auth->created_at) . "</tr></td>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
