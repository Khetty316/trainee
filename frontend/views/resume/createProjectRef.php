<?php

use yii\helpers\Html;

$this->title = 'New Project References';
$this->params['breadcrumbs'][] = ['label' => 'My Resume', 'url' => ['index-personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-employ-history-create">
    <h3><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_formResumeProjectRef', [
        'model' => $model,
    ]) ?>
</div>
