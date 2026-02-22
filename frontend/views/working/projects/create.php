<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\MasterProjects */

$this->title = 'Create Master Projects';
$this->params['breadcrumbs'][] = ['label' => 'Master Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-projects-create">

    <h3><?= Html::encode($this->title) ?></h3>



    <?php
    $form = ActiveForm::begin([
                'id' => 'newpo-form',
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
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'project_code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-8">
            <?= $form->field($model, 'project_description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-4">
            <?php
            // $form->field($model, 'project_name')->textInput(['maxlength' => true])
            echo $form->field($model, "person_in_charge")->dropDownList($userList, ['prompt' => 'Select...', 'id' => 'main_requestor'])->label('Requestor')
            ?>
        </div>
    </div>


    <?php //= $form->field($model, 'project_image')->textInput(['maxlength' => true])   ?>



    <div class="form-group">
        <?php
        if ($getAjax) {
            echo Html::submitButton('Save & Continue', ['class' => 'btn btn-success']);
        } else {
            echo Html::submitButton('Save', ['class' => 'btn btn-success']);
        }
        ?>
    </div>
    <?= yii\bootstrap4\Html::input('text', 'getAjax', $getAjax, ['style' => 'display:none']) ?>
    <?php ActiveForm::end(); ?>
</div>

<script>
<?php
if ($getAjax) {
    ?>
        $("#newpo-form").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            //            var url = form.attr('action');
            var url = '/working/projects/create-by-ajax';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function (data) {
                    data = JSON.parse(data);
    //                    alert(data['status']);
                    if (data['status'] == 'success') {
                        $('#myModalContent').html(data.msg);
                    } else {
                        alert(data.msg);
                    }
                }
            });
        });
    <?php
}
?>
</script>