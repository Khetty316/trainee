<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\claim\ClaimEntitlementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="claim-entitlement-index">
    <?php if ($hr) { ?>
        <?= $this->render('__claimEntitlementNavBarHr', ['pageKey' => '1']) ?>
    <?php } else { ?>
        <?= $this->render('__claimEntitlementNavBarSuperior', ['pageKey' => '1']) ?>
    <?php } ?>
    
    <?= $this->render('_list', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'hr' => $hr
    ]);
    ?>


</div>
