<?php

use yii\helpers\Html;

$this->title = 'Update Employment History';
$this->params['breadcrumbs'][] = ['label' => 'My Resume', 'url' => ['index-personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-employ-history-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <?=
    Html::a('Delete <i class="far fa-trash-alt"></i>', ['delete-employment-history', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ])
    ?>
    <?=
    $this->render('_formResumeEmployHistory', [
        'model' => $model,
    ])
    ?>

</div>
