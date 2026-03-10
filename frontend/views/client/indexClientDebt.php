<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>

<legend class="w-auto px-2 m-0">Client Debt:</legend>

<?php
Pjax::begin([
    'id' => 'client-debt-grid',
    'enablePushState' => false
]);
?>

<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterOnFocusOut' => false,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
//        'client_id',
        'tk_group_code',
        'month',
        'year',
        'balance',
        'created_at',
        'created_by',
        //updated_by',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]);
?>

<?php Pjax::end(); ?>

<?php
$script = <<< JS
$('.filters input').on('keyup', function() {
    $('#client-debt-grid').yiiGridView('applyFilter');
});
JS;

$this->registerJs($script);
?>