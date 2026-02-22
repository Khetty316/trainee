<?php
use yii\helpers\Html;

$this->title = 'Claim Review - Finance';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-master-index">
    <?= $this->render('__claimNavBar', ['module' => 'finance', 'pageKey' => '1']) ?>
    <p>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-finance'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>
    <?=
    $this->render('_claimList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'module' => 'finance'
    ]);
    ?>
</div>
