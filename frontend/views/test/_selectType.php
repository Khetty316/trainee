<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMain;
?>

<div class="initiate-panel">

    <div class="row vmiddle">
        <div class="col-6 vmiddle">
            <?= Html::a(TestMain::TEST_ITP_TITLE, ['/test/testing/index-master', 'id' => $panel->id, 'type' => TestMain::TEST_ITP_TITLE], ['class' => 'btn btn-success w-100 h-100 pt-3']) ?>
        </div>
        <div class="col-6">
            <?= Html::a(TestMain::TEST_FAT_TITLE, ['/test/testing/index-master', 'id' => $panel->id, 'type' => TestMain::TEST_FAT_TITLE], ['class' => 'btn btn-success  h-100 vmiddle']) ?>
        </div>
    </div>

</div>