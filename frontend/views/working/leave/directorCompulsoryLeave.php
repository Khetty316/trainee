<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\office\leave\LeaveMaster;

$this->title = 'Leave Approval (Director)';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Compulsory Leave';
?>

<div class="leave-master-compulsory">
    <h3><?= Html::encode($this->title) ?></h3>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    <?=
    $this->render('_gridCompulsoryLeave', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
    ?>
</div>