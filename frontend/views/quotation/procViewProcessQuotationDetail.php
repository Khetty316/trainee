<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */

$this->title = "Quotation request ID: $model->id";
$this->params['breadcrumbs'][] = ['label' => 'RFQ (Procurement)'];
if ($model->request_is_complete == 1) {
    $this->params['breadcrumbs'][] = ['label' => 'All', 'url' => ['/quotation/proc-view-quotation-list-all']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Pending List', 'url' => ['proc-view-quotation-list-pending']];
}
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quotation-masters-view">
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Quotation Detail</legend>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'id',
                    'format' => 'raw',
                    'label' => 'Quotation ID'
                ],
                [
                    'attribute' => 'requestor_id',
                    'format' => 'raw',
                    'label' => 'Requested By',
                    'value' => function($model) {
                        return $model->requestor->fullname;
                    }
                ],
                [
                    'attribute' => 'project_code',
                    'format' => 'raw',
                    'label' => 'Project',
                    'value' => function($model) {
                        return $model->project_code . " - " . $model->projectCode->project_name;
                    }
                ],
                [
                    'attribute' => 'file_reference',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->file_reference ? (Html::a(substr($model->file_reference, 15) . " <i class='far fa-file-alt fa-lg' ></i>", ['get-file', 'id' => $model->id], ['target' => '_blank'])) : " - ";
                    }
                ],
                'description:ntext',
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'label' => 'Submitted At',
                    'value' => function($model) {
                        return common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                ],
                [
                    'attribute' => '',
                    'format' => 'raw',
                    'label' => 'Request Status',
                    'value' => function($model) {
                        return $model->getStatus();
                    }
                ],
                [
                    'attribute' => 'proc_approval',
                    'format' => 'raw',
                    'label' => 'Procurement Approval Status',
                    'value' => function($model) {
//                $str = $model->proc_approval > 0 ? ()
                        return $model->proc_approval == 0 ? " - " : $model->getProcApprovalSts();
                    }
                ],
                [
                    'attribute' => 'requestor_approval',
                    'format' => 'raw',
                    'label' => 'Requestor Approval Status',
                    'value' => function($model) {
                        $remark = $model->requestor_remark ? ("<br/><b>Remark: </b><br/>" . nl2br(Html::encode($model->requestor_remark))) : "";
                        return $model->requestor_approval == 0 ? " - " : $model->getReqApprovalSts() . $remark;
                    }
                ],
                [
                    'attribute' => 'manager_approval',
                    'format' => 'raw',
                    'label' => 'Manager Approval Status',
                    'value' => function($model) {
                        $remark = $model->manager_remark ? ("<br/><b>Remark: </b><br/>" . nl2br(Html::encode($model->manager_remark))) : "";
                        return $model->manager_approval == 0 ? " - " : $model->getMgrApprovalSts() . $remark;
                    }
                ],
            ],
        ])
        ?>
    </fieldset>

</div>
<?php if ($model->proc_approval == 0) { ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Process...</legend>

        <?php
        $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-12',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-6',
                        ],
                    ],
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                    'action' => 'proc-action-add-quotation-files',
                    'method' => 'post',
                    'id' => 'form_upload'
        ]);
        ?>

        <div class="hidden">
            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        </div>

        <div class="form-row mx-1">
            <div class='form-group'>
                <div class="col-sm-12 pb-3">
                    <?= $form->field($model, 'scannedFiles[]')->fileInput(['multiple' => true, 'required' => true, 'class' => 'form-control'])->label(false) ?>   
                    <span class="font-weight-lighter text-success">** Allow multiple files</span>
                </div>
            </div>

            <div class='form-group'>
                <?= Html::submitButton('Upload Quotations <i class="fa fa-upload"></i>', ['class' => 'btn btn-success ml-3 submitButton']) ?> 
            </div>

        </div>

        <?php
        ActiveForm::end();
        $quotationDetails = $model->quotationDetails;
        if ($quotationDetails) {
            $form2 = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-12',
                                'offset' => 'col-sm-offset-4',
                                'wrapper' => 'col-sm-6',
                            ],
                        ],
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                        'action' => 'proc-action-forward-to-requestor',
                        'method' => 'post',
                        'id' => 'form_quotations'
            ]);
            ?>
            <div class="hidden">
                <?= $form2->field($model, 'id')->hiddenInput()->label(false) ?>
            </div>
            <table class='table table-sm table-bordered table-striped'>
                <thead class="thead-light text-center">
                    <tr>
                        <th style="width:20%" class="px-2">Reference</th>
                        <th>Remark</th>
                        <th>Supplier</th>
                        <th style="width:1%" class="px-2">Upload By</th>
                        <th style="width:1%" class="px-2">Select</th>
                        <th style="width:1%" class="px-2">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotationDetails as $key => $quotation) { ?>
                        <tr id="<?= "file_$quotation->id" ?>"> 
                            <td class="align-middle"><?= Html::a(substr($quotation->filename, 15), ['get-file-quotations', 'id' => $quotation->id], ['target' => '_blank']) ?></td>
                            <td>
                                <?= Html::textInput("remark[$quotation->id]", $quotation->remark, ['class' => 'form-control']) ?>
                            </td>
                            <td>
                                <?php
                                $supplierList = yii\helpers\ArrayHelper::toArray((new \yii\db\Query())
                                                        ->select(['distinct trim(supplier_name) as supplier_name'])
                                                        ->from('quotation_details')
                                                        ->where("supplier_name IS NOT NULL AND supplier_name <>''")
                                                        ->orderBy(["supplier_name" => "SORT_ASC"])
                                                        ->all());
                                $list = [];
                                foreach ($supplierList as $key => $supplier) {
                                    array_push($list, $supplier['supplier_name']);
                                }

                                echo yii\jui\AutoComplete::widget([
                                    'name' => "supplier[$quotation->id]",
                                    'value' => $quotation->supplier_name,
                                    'clientOptions' => [
                                        'source' => $list,
                                    ],
                                    'options' => ['class' => 'form-control']
                                ]);
                                ?>
                            </td>
                            <td style="white-space:nowrap;" class="px-2 align-middle"><?= $quotation->getCreatedBy()->fullname ?></td>
                            <td class="text-center align-middle m-0 p-0">
                                <?= Html::radio('selectQuotation', $quotation->is_selected, ['required' => true, 'value' => $quotation->id]) ?>
                            </td>
                            <td class="text-center align-middle ">
                                <?= Html::a('<i class="far fa-trash-alt fa-lg text-danger"></i>', "javascript:remove('$quotation->id')", ['data-confirm' => 'Remove this quotation?']) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="form-row mx-1">
                <div class='form-group'>
                    <?= Html::hiddenInput('isSaveOnly', '0', ['id' => 'isSaveOnly']) ?>
                    <?= Html::a('Save Only <i class="far fa-save"></i>', "javascript:saveOnly()", ['class' => 'btn btn-success submitButton']) ?> 
                    <?= Html::a('Save & Forward to requestor <i class="far fa-share-square"></i>', "javascript:forwardToRequestor()", ['class' => 'btn btn-success submitButton']) ?> 
                </div>
            </div>

            <?php
            ActiveForm::end();
        }
        ?>
    </fieldset> 
<?php } else { ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Quotations:</legend>
        <table class='table table-sm table-bordered table-striped'>
            <thead class="thead-light text-center">
                <tr>
                    <th class="px-2">Reference</th>
                    <th>Remark</th>
                    <th>Supplier</th>
                    <th class="px-2 d-none d-md-table-cell">Upload By</th>
                    <th class="px-2">

                        <span class="d-none d-md-inline">Selected </span><i class='fas fa-check'></i>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quotationDetails = $model->quotationDetails;
                foreach ($quotationDetails as $key => $quotation) {
                    ?>
                    <tr <?= $quotation->is_selected ? "class='bg-warning font-weight-bolder'" : "" ?>  > 
                        <td class="text-center"><?php
            $displayText = substr($quotation->filename, 15);
            echo Html::a(substr($displayText, 0, 10) . (strlen($displayText) > 10 ? "..." : ""), ['get-file-quotations', 'id' => $quotation->id], ['target' => '_blank']);
                    ?>
                        </td> 
                        <td><?= Html::encode($quotation->remark) ?></td>
                        <td><?= Html::encode($quotation->supplier_name) ?></td>
                        <td class="px-2 d-none d-md-table-cell"><?= $quotation->getCreatedBy()->fullname ?></td>
                        <td class="text-center align-middle m-0 p-0">
                            <?= $quotation->is_selected ? " <i class='fas fa-check'></i>" : "" ?>
                        </td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
        if ($model->request_is_complete == 0 && $model->manager_approval == 1) {
            echo Html::a('Upload PO <i class="fa fa-upload"></i>', ["/working/po/create", "quotationId" => $model->id], ['class' => 'btn btn-success']);
        }
        ?>

    </fieldset> 
<?php } ?>

<script>
    function remove(id) {
        $.ajax({
            type: "POST",
            url: "/quotation/proc-action-remove-quotation-ajax",
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                myAlert(data.msg);
                $("#file_" + id).hide();
                $('.submitButton').attr('disabled', false).removeClass('disabled');
            }
        });
    }


    function forwardToRequestor() {
        var form = $("#form_quotations");

        if (typeof $("input[name='selectQuotation']:checked").val() === "undefined") {
            myAlert('Please select a quotation');
            return false;
        }
        var ans = confirm("Forward to requestor?");
        if (ans) {
            form.submit();
        }
    }

    function saveOnly() {
        var form = $("#form_quotations");
        $("#isSaveOnly").val(1);

        var ans = confirm("Save?");
        if (ans) {
            form.submit();
        }
    }

</script>
