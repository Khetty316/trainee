<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - New Item'];
?>
<div class="po-create">
    <?= $this->render('_purchasingNavBar', ['module' => "newItem", 'pageKey' => '4']) ?>
    <p>        
        <?= Html::a('Reset filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>
    <?=
    $this->render('_poList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'page' => "new"
    ])
    ?>

</div>
