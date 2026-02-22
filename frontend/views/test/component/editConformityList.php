<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Edit Component Conformity List';
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index-master']];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = ['label' => 'Component Check', 'url' => ["/test/component/index", 'id' => $testForm->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <div class="form-row m-3">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'w-100', 'autocomplete' => 'off'],
        ]);
        ?>
        <table class="table table-sm col-sm-12">
            <thead class="text-center">
                <tr>
                    <th class="col-6">Non-conform Component</th>
                    <th class="col-6">Remark</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                foreach ($conformityList as $key => $conformity) {
                    echo $this->render('_formConformityItem', ['form' => $form, 'key' => $key, 'conformity' => $conformity]);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12">       
                        <a class='btn btn-primary' href='javascript:addRow()'> <i class="fas fa-plus-circle"></i></a>
                            <?= Html::submitButton('Save', ['class' => 'float-right btn btn-success']) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php
        ActiveForm::end();
        ?>
    </div>
    <div>
        <br/><br/><br/>

    </div>
</div>
<script>
    var currentKey = <?= sizeof($conformityList) ?>;
    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).hide();
            $("#toDelete-" + rowNum).val("1");
        }
    }

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-conformity-item']) ?>',
            dataType: 'html',
            data: {
                key: currentKey++
            }
        }).done(function (response) {
            $("#listTBody").append(response);
        });
    }


</script>