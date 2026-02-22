<?php

use yii\helpers\Html;
use frontend\models\test\TestMain;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestFormList;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestTemplate */
$array = frontend\models\test\RefTestFormList::getDropDownList();
$mergeArray = array_merge($array, [TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE, TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE]);

$this->title = 'Update Test Template';
$this->params['breadcrumbs'][] = ['label' => 'Test Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $mergeArray[$model->formcode], 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-template-update">

    <?=
    $this->render('_formTestTemplate', [
        'model' => $model,
        'formName' => $formName
    ])
    ?>

</div>
