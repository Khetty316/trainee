<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestTemplate */

$this->title = 'Create Test Template';
$this->params['breadcrumbs'][] = ['label' => 'Test Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-template-create">

    <?= $this->render('_formTestTemplate', [
        'model' => $model,
        'formName' => $formName
    ]) ?>

</div>
