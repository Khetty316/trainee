<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimsMaster */

$this->title = 'Create Claims Master';
$this->params['breadcrumbs'][] = ['label' => 'Claims Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claims-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'claimTypeList' => $claimTypeList,
        'superior' => $superior,
        'userList' => $userList
    ]) ?>

</div>
<?php
$this->registerJs(<<<JS
JS
);
?>