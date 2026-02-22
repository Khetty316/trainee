<?php

use yii\helpers\Html;

$this->title = 'My Claims - Personal';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-master-index">
    <?= $this->render('__claimNavBar', ['module' => 'personal', 'pageKey' => '2']) ?>

    <p>
        <?= Html::a('Create a Claim', ['create-claim'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-personal'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

    <?=
    $this->render('_claimList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'module' => 'personal'
    ]);
    ?>
</div>
