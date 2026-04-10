<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

if ($moduleIndex === 'execStock') {
    $pageName = 'Inventory Master - Executive';
    $url = 'inventory/inventory/model-list?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Inventory Master - Assistant';
    $url = 'inventory/inventory/model-list?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Inventory Master - Project Coordinator';
    $url = 'inventory/inventory/model-list?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Inventory Master - Head of Maintenance';
    $url = 'inventory/inventory/model-list?type=maintenanceHeadStock';
}

$this->title = 'Add New Model';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Model List', 'url' => [$url]];
$this->params['breadcrumbs'][] = $this->title;
?>

<fieldset class="form-group border p-3">
    <legend class="w-auto px-2  m-0 ">Upload By Template:</legend>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                ]);
                echo Html::a(
                        'Download Template <i class="fas fa-download"></i>',
                        yii\helpers\Url::to('@web/template/template-inventory-model.xls'),
                        [
                            'class' => 'btn btn-primary mb-3 mt-0',
                            'download' => 'template-inventory-model-list.xls',
                            'title' => 'Download Excel Template'
                        ]
                );
                ?>
            </div>
            <div class="col-md-12">
                <?php
                echo Html::fileInput('excelTemplate', null, [
                    'accept' => '.xls', 'required' => true,
                ]);

                echo Html::submitButton(
                        'Upload Excel <i class="fas fa-upload"></i>',
                        ['class' => 'btn btn-success mb-2 mt-1']);
                ActiveForm::end();
                ?>
            </div>


        </div>
    </div>
</fieldset>
