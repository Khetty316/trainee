<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\testkit\CovidTestkitInventory */
$this->title = 'Covid-19 Test-Kit Stock In';
$this->params['breadcrumbs'][] = ['label' => 'Covid Testkit Inventories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="covid-testkit-inventory-create">

    <h3><?= Html::encode($this->title) ?></h3>


    <div class="covid-testkit-inventory-form">

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
                    'id' => 'myForm',
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
        ]);
        ?>
        <div class="form-group row">
            <div class="col-xs-12 col-md-3">
                <?php
                echo $form->field($model, 'brand')->textInput(['maxlength' => true]);

                $brandList = frontend\models\covid\testkit\CovidTestkitInventory::find()
                        ->select(['brand', 'brand as id', 'brand as label'])
                        ->distinct()
                        ->orderBy(['brand' => SORT_ASC])
                        ->asArray()
                        ->all();

                $form->field($model, "brand")->widget(yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $brandList,
                        'minLength' => '1',
                        'autoFill' => true,
                        'delay' => 1,
                    ],
                    'options' => ['class' => 'form-control']
                ]);
                ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12 col-md-3">
                <?= $form->field($model, 'record_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Date") ?> 
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12 col-md-3">
                <?= $form->field($model, 'remark')->textInput()->label("Remark/Reason") ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-xs-12 col-md-3">
                <?= $form->field($model, 'total_movement')->textInput(["type" => "number"])->label("Total") ?>
            </div>
        </div>



        <?php //= $form->field($model, 'giving_to')->textInput()   ?>

        <?php //= $form->field($model, 'confirm_status')->textInput()   ?>

        <?php //= $form->field($model, 'created_at')->textInput()   ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
