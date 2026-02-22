<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EmployeeHandbookMaster */

$this->title = 'Update: ' . $model->name;
?>
<div class="employee-handbook-master-update">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
