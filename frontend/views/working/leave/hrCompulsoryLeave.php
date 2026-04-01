<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\office\leave\LeaveMaster;
?>

<div class="leave-master-compulsory">
    <div class="mb-2">
        <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '9']) ?>

        <?=
        Html::a('Schedule Compulsory Leave <i class="fas fa-plus"></i>',
                ['/working/leavemgmt/apply-compulsory-leave'],
                ['class' => 'btn btn-success']
        );
        ?>

        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    </div>
    <?=
    $this->render('_gridCompulsoryLeave', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
    ?>

</div>