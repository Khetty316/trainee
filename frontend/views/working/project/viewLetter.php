<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

//$model=new \frontend\models\working\project\ProjectMaster();
$incomingLetters = frontend\models\working\project\ProjectLetters::find()->where('project_id = ' . $model->id)->andWhere('letter_type="incoming"')->all();
$outgoingLetters = frontend\models\working\project\ProjectLetters::find()->where('project_id = ' . $model->id)->andWhere('letter_type="outgoing"')->all();
?>
<style>
    .borderTable th{
        border: 1px solid black!important;
    }

</style>
<div class="project-master-view">
    <?= $this->render('__ProjectNavBar', ['pageKey' => '2', 'id' => $model->id, 'projectCode' => $model->proj_code, 'model' => $model]); ?>

    <fieldset class="border border-dark p-3">
        <legend class="w-auto pl-2 pr-2 text-primary">Incoming</legend>
        <div class="form-row">
            <?php if ($incomingLetters) { ?>
                <table class="table table-striped table-sm table-bordered borderTable">
                    <thead  class="thead-light">
                        <tr class="text-center text-primary">
                            <th style="width:40%">Description</th>
                            <th style="width:40%">File</th>
                            <th style="width:8%">Date</th>
                            <th style="width:2%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($incomingLetters as $key => $letter) {
                            ?>
                            <tr>
                                <td><?= $letter->description?$letter->description:'-' ?></td>
                                <td><?= ($letter->file ? Html::a(substr($letter->file, 15), ['get-file-letter', 'id' => $letter->id, 'type' => 'submit'], ['target' => '_blank']) : "&nbsp;- ") ?></td>
                                <td class="text-center"><?= $letter->date?MyFormatter::asDate_Read($letter->date):'-' ?></td>
                                <td class="text-right br"><?php
                                    echo Html::a('<i class="fas fa-edit"></i>',
                                            "javascript:",
                                            [
                                                'class' => 'text-success ml-2 mr-2',
                                                'title' => 'Edit',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#model_Letter',
                                                'data-letter_id' => $letter->id,
                                                'data-letter_type' => $letter->letter_type,
                                                'data-file' => $letter->file ? substr($letter->file, 15) : "",
                                                'data-date' => MyFormatter::asDate_Read($letter->date),
                                                'data-description' => $letter->description
                                    ]);
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100 p-0 m-0']);
            }
            ?>
            <p>
                <?php
                echo Html::a('Incoming Letter <i class="fas fa-plus"></i>',
                        "javascript:",
                        [
                            'class' => 'btn btn-success btn-sm',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_Letter',
                            'data-letter_type' => 'incoming',
                        ]
                );
                ?>
            </p>
        </div>
    </fieldset>
    <br/>
    <fieldset class="border border-dark p-3">
        <legend class="w-auto pl-2 pr-2 text-primary">Outgoing</legend>
        <div class="form-row">
            <?php if ($outgoingLetters) { ?>
                <table class="table table-striped table-sm table-bordered borderTable">
                    <thead  class="thead-light">
                        <tr class="text-center text-primary">
                            <th style="width:40%">Description</th>
                            <th style="width:40%">File</th>
                            <th style="width:8%">Date</th>
                            <th style="width:2%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($outgoingLetters as $key => $letter) {
                            ?>
                            <tr>
                                <td><?= $letter->description?$letter->description:'-' ?></td>
                                <td><?= ($letter->file ? Html::a(substr($letter->file, 15), ['get-file-letter', 'id' => $letter->id], ['target' => '_blank']) : "&nbsp;- ") ?></td>
                                <td class="text-center"><?= $letter->date?MyFormatter::asDate_Read($letter->date):'-' ?></td>
                                <td class="text-right br"><?php
                                    echo Html::a('<i class="fas fa-edit"></i>',
                                            "javascript:",
                                            [
                                                'class' => 'text-success ml-2 mr-2',
                                                'title' => 'Edit',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#model_Letter',
                                                'data-letter_id' => $letter->id,
                                                'data-letter_type' => $letter->letter_type,
                                                'data-file' => $letter->file ? substr($letter->file, 15) : "",
                                                'data-date' => MyFormatter::asDate_Read($letter->date),
                                                'data-description' => $letter->description
                                    ]);
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100 p-0 m-0']);
            }
            ?>
            <p>
                <?php
                echo Html::a('Outgoing Letter <i class="fas fa-plus"></i>',
                        "javascript:",
                        [
                            'class' => 'btn btn-success btn-sm',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_Letter',
                            'data-letter_type' => 'outgoing',
                        ]
                );
                ?>
            </p>
        </div>
    </fieldset>
</div>


<!-- MODAL -->



<?php $modalName = "Letter"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $formVO = ActiveForm::begin([
                        'action' => '/working/project/create-letter',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectLetters[project_id]' value='<?= $model->id ?>' class='hidden'/> 
                                <input type='text' name='ProjectLetters[id]' value='' class='hidden' id="letter_id"/> 
                                <input type='text' name='ProjectLetters[letter_type]' value='' class='hidden' id="letter_type"/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>:</td>
                            <td>               
                                <input type="text" class="form-control" name="ProjectLetters[description]" id="letter_description" />
                            </td>
                        </tr>
                        <tr>
                            <td>Letter<req/></td>
                    <td>:</td>
                    <td>               
                        <span id="letter_currentFile"></span>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input customFile" id="customFile<?= $modalName ?>" name='ProjectLetters[scannedFile]' required/>
                            <label class="custom-file-label customFileLabel" for="customFile<?= $modalName ?>" >Choose file</label>
                        </div>
                    </td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>             
                            <?=
                            \yii\jui\DatePicker::widget([
                                'name' => 'ProjectLetters[date]',
                                'language' => 'en',
                                'dateFormat' => 'php:d/m/Y',
                                'options' => ['class' => 'form-control'],
                                'id' => 'letter_date'
                            ])
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>

    $(function () {
        $('.customFile').on('change', function () {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.customFileLabel').html(fileName);
        });


        $('#model_Letter').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#letter_id').val(button.data('letter_id'));
            modal.find('#letter_type').val(button.data('letter_type'));
            modal.find('#letter_currentFile').html(button.data('file'));
            modal.find('#letter_date').val(button.data('date'));
            modal.find('#letter_description').val(button.data('description'));

            if (button.data('letter_type') === "incoming") {
                modal.find('#modalLabel_Letter').html("Incoming Letter");
            } else {
                modal.find('#modalLabel_Letter').html("Outgoing Letter");
            }
//            alert(button.data('letter_id'));
            if (button.data('letter_id') === undefined) {
                $("#customFileLetter").attr("required");
            } else {
                $("#customFileLetter").removeAttr("required");
            }


        });




    });
</script>