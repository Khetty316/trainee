<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQMasters */
/* @var $form yii\widgets\ActiveForm */

if ($model['projectCoordinator']['fullname'] ?? null) {
    $model->projCoordinatorFullname = $model['projectCoordinator']['fullname'];
} else {
    $model->projCoordinatorFullname = Yii::$app->user->identity->fullname;
    $model->project_coordinator = Yii::$app->user->identity->id;
}
?>

<div class="project-qmasters-form">

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
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Project Detail</legend>
        <div class="form-row">
            <div class="col-md-9">
                <div class="form-row">
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group row field-projectqmasters-quotation_no">
                            <label class="col-sm-12" for="projectqmasters-quotation_no">Quotation No.</label> 
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="" value="<?= $model->quotation_no ?>" disabled />
                                <div class="text-success" style="font-size: 8pt">* Quotation no. might varies upon save *</div>
                            </div>
                        </div>                   
                    </div>
                    <div class="col-sm-12 col-md-5">
                        <?= $form->field($model, 'company_group_code')->dropDownList($companyGroupList, ['prompt' => 'Select...']) ?>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <?php
                        $userList = common\models\User::getAutoCompleteListActiveOnly();
                        echo $form->field($model, "projCoordinatorFullname")->widget(yii\jui\AutoComplete::className(), [
                            'clientOptions' => [
                                'source' => $userList,
                                'minLength' => '1',
                                'autoFill' => true,
                                'delay' => 100,
                                'change' => new \yii\web\JsExpression("function( event, ui ) { 
                            $('#projectqmasters-project_coordinator').val((ui.item ? ui.item.id : ''));
                            $(this).val((ui.item ? ui.item.label : ''));
			     }"),
                            ],
                            'options' => ['class' => 'form-control', 'readonly' => true]
                        ])->label("Project Coordinator");
                        ?>
                        <?= $form->field($model, 'project_coordinator', ['options' => ['class' => 'hidden']])->textInput() ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'project_name')->textInput(['id' => 'myform-project-name']) ?>
                        <div class="text-center" id="loadingSpinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>                        
                        <div id="similarProjectsList" style="display: none; margin-bottom: 20px;"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'remark')->textarea(['rows' => 8]) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12 text-right">
                        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-row">
                    <div class=" pl-sm-2 pl-md-3 col-sm-12">

                    </div>
                </div>
            </div>
        </div>

    </fieldset>



    <?php //= $form->field($model, 'amount')->textInput()         ?>


    <?php //= $form->field($model, 'status')->textInput(['maxlength' => true])        ?>


    <?php //= $form->field($model, 'created_at')->textInput()        ?>

    <?php //= $form->field($model, 'created_by')->textInput()        ?>



    <?php ActiveForm::end(); ?>

</div>
<script>
    $('#myform-project-name').on('input', function () {
        var projectName = $(this).val();

        showLoadingSpinner();

        $.ajax({
            type: 'POST',
            url: '<?= \yii\helpers\Url::to(['check-similar-project']) ?>',
            data: {
                projectName: projectName
            },
            success: function (response) {
                // Clear previous list and message
                $('#similarProjectsList').empty();

                // set loading time to 0.5 seconds
                setTimeout(function () {
                    hideLoadingSpinner();
                }, 500);

                if (response.success && response.similarProjects.length > 0) {
                    var gotSimilarProject = $('<div>').addClass('alert alert-danger mt-2').text('Similar Projects Found:');
                    $('#similarProjectsList').append(gotSimilarProject);

                    response.similarProjects.forEach(function (similarProject) {
                        var listItem = $('<li>').text(similarProject.projectName.project_name + ' - ' + similarProject.similarityPercentage + '% match').css({'margin-left': '20px'});
                        $('#similarProjectsList').append(listItem);
                    });

                    $('#similarProjectsList').show();
                } else {
                    var noSimilarProject = $('<div>').addClass('alert alert-info mt-2').text('No Similar Projects Found.');
                    $('#similarProjectsList').append(noSimilarProject);
                    $('#similarProjectsList').show();
                }
            }
        });
    });

    function showLoadingSpinner() {
        $('#loadingSpinner').html('<i class="fas fa-spinner fa-spin"></i> Loading...').show();
    }

    function hideLoadingSpinner() {
        $('#loadingSpinner').hide();
    }
</script>
