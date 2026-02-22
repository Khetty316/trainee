<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = $model->got_custom_content == 0 ? 'Add Custom Content' : 'Edit Custom Content';
$this->params['breadcrumbs'][] = ['label' => "PaneAdl's Test List", 'url' => ['/test/testing/index-master']];
$this->params['breadcrumbs'][] = ['label' => $model->testMaster->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $model->testMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Functionality List', 'url' => ["/test/functionality/index", 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-custom-content-create">
    <?php
    $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
            'horizontalCssClasses' => [
                'label' => 'col-sm-12',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => '',
            ],
        ],
        'options' => ['autocomplete' => 'off'],
    ]);
    ?>

    <div class="col-12">
        <div class="row mb-2">
            <div>
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success ml-3']) ?>
            </div>
        </div>
    </div>

    <?=
    $this->render('/test-custom-content/_form', [
        'model' => $model,
        'customContents' => $customContents,
        'customContentArray' => $customContentArray
    ])
    ?>
    <?php ActiveForm::end(); ?>
</div>