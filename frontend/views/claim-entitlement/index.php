<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\claim\ClaimEntitlementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="claim-entitlement-index">
    <?php if ($hr) { ?>
        <?= $this->render('__claimEntitlementNavBarHr', ['pageKey' => '2']) ?>
    <?php } else { ?>
        <?= $this->render('__claimEntitlementNavBarSuperior', ['pageKey' => '2']) ?>
    <?php } ?>
    
    <?= $this->render('_list', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'hr' => $hr
    ]);
    ?>

</div>
