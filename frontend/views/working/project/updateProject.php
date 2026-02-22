<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProjectMaster */

$this->title = 'Update Project Master: ';
$this->params['breadcrumbs'][] = ['label' => 'Master Project', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->proj_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-master-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_formProject', [
        'model' => $model,
        'userList' => $userList,
        'clientList'=>$clientList
    ])
    ?>

</div>
