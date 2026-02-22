<?php
/* @var $this yii\web\View */

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

/* PART OF LEAVE ENTITLEMENT SUB-MODULE */

$this->title = $vEntitlement->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Leave Entitlement', 'url' => ['hr-leave-entitlement', 'selectYear' => $vEntitlement->year]];
$this->params['breadcrumbs'][] = ['label' => 'Leave Entitlement Details'];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $vEntitlement->leave_type_name . "(" . $vEntitlement->year . ")";

?>

<div class="leave-master-index">
    <div class="">
        <?=
        $this->render("_formSingleEntitleDetail", [
            'vEntitlement' => $vEntitlement,
            'entitleDetail' => $entitleDetail
        ])
        ?>
    </div>
</div>