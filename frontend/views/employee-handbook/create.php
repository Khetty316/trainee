<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EmployeeHandbookMaster */

$this->title = 'Create Employee Handbook';
?>
<div class="employee-handbook-master-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
