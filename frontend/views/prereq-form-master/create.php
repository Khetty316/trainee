<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Pre-Requisition Form - Personal', 'url' => ['personal-pending-approval']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prereq-form-master-create">

    <!--<h5><?php //= Html::encode($this->title)  ?></h5>-->

    <?=
    $this->render('_form', [
        'master' => $master,
        'items' => $items,
        'vmodel' => $vmodel,
        'isUpdate' => $isUpdate,
        'isView' => $isView,
        'moduleIndex' => $moduleIndex,
        'worklists' => $worklists,
        'hasSuperiorUpdate' => $hasSuperiorUpdate,
        'departmentList' => $departmentList,
//        'supplierList' => $supplierList,
//        'brandList' => $brandList,
//        'modelList' => $modelList
    ])
    ?>

</div>
