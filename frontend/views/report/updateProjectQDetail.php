<?php

use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Reconfigure Task Weight', 'url' => '/reporting/reconfigure-task-weight'];
$this->params['breadcrumbs'][] = 'Update Project Quotation Detail';
?>
<div class="project-q-update">
    <h3 class="mt-3 mb-3">Update Project Quotation Detail</h3>
    <?=
    $this->render('_formProjectQDetail', [
        'refProjectQTypes' => $refProjectQTypes
    ])
    ?>

</div>
