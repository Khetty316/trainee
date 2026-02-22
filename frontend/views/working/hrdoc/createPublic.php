<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */

$this->title = 'Add Public Documents';
$this->params['breadcrumbs'][] = ['label' => 'HR Public Document', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-reminder-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_formPublic', [
        'model' => $model,
    ])
    ?>

</div>
