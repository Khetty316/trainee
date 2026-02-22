<?php

use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Reconfigure Task Weight', 'url' => '/reporting/reconfigure-task-weight'];
$this->params['breadcrumbs'][] = 'Update Task Weight';
?>
<div class="task-weight">
    <h3 class="mt-3 mb-3">Update Task Weight</h3>
    <?=
    $this->render('_formTaskWeight', [
        'refFabTask' => $refFabTask,
        'refElecTask' => $refElecTask
    ])
    ?>

</div>
