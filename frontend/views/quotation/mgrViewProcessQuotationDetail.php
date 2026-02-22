<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */

$this->title = "Quotation Request ID: $model->id";
$this->params['breadcrumbs'][] = ['label' => 'RFQ Pending List (Manager)', 'url' => ['mgr-view-quotation-list-pending']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quotation-masters-view">

    <h3><?= Html::encode($this->title) ?></h3>


    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
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
                'label' => 'Submit At',
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
//            'updated_by',
//            'updated_at',
        ],
    ])
    ?>

</div>
<?php
if ($model->requestor_approval == 1) {
    $quotationList = $model->quotationDetails;

    if ($quotationList) {
        ?> 
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
                    <?php foreach ($quotationList as $key => $quotation) { ?>
                        <tr <?= $quotation->is_selected ? "class='bg-warning font-weight-bolder'" : "" ?>  > 
                            <td class="text-center"><?php
                                $displayText = substr($quotation->filename, 15);

                                echo Html::a(substr($displayText, 0, 10) . (strlen($displayText) > 10 ? "..." : ""), ['get-file-quotations', 'id' => $quotation->id], ['target' => '_blank']);
                                ?></td> 
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
            if ($model->requestor_approval == $model::APPROVE_YES && $model->manager_approval == 0) {
                $form2 = ActiveForm::begin([
                            'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                            'action' => 'mgr-action-approve',
                            'method' => 'post',
                            'id' => 'mgrQuotationForm'
                ]);
                ?>

                <div class="form-row">
                    <div class="col-xs-12 col-md-6">
                        <?= Html::textarea('manager_remark', '', ['rows' => 6, 'placeholder' => 'Remark', 'class' => 'form-control my-2']) ?>
                    </div>
                </div>
                <div class="form-row mx-1">
                    <div class="hidden">
                        <?= $form2->field($model, 'id')->textInput()->label(false) ?>
                        <?= Html::textInput('approval', '', ['id' => 'approval']) ?>
                    </div>
                    <div class='form-group'>
                        <?= Html::a('Approve <i class="fas fa-check"></i>', "javascript:submitRequest(1)", ['class' => 'btn btn-success submitButton', 'data-confirm' => 'Approve and proceed to purchase?']) ?> 
                        <?= Html::a('Reject <i class="fas fa-times-circle"></i>', "javascript:submitRequest(0)", ['class' => 'btn btn-danger submitButton', 'data-confirm' => 'Reject?']) ?> 
                    </div>
                </div>

                <?php
                ActiveForm::end();
            }
            ?>
        </fieldset>

        <?php
    }
}
?>
<script>

    function submitRequest(response) {
        $("#approval").val(response);
        $("#mgrQuotationForm").submit();
    }

</script>
