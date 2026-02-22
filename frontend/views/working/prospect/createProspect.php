<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProspectMaster */

$this->title = 'Create Prospect Master';
$this->params['breadcrumbs'][] = ['label' => 'Prospect Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
