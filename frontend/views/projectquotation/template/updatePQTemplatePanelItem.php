<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

//$model = frontend\models\projectquotation\ProjectQPanelsTemplate::findOne($id);
$this->title = $model->revisionTemplate->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Template', 'url' => ['indexpqrevision']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['viewpqrevision', 'id' => $model->revisionTemplate->id]];
$this->params['breadcrumbs'][] = ['label' => 'Edit', 'url' => ['update-p-q-template-revision-panel', 'id' => $model->revisionTemplate->id]];
$this->params['breadcrumbs'][] = ['label' => 'Edit Panel Detail'];
\yii\web\YiiAsset::register($this);
$panelItems = $model->projectQPanelItemsTemplates;
array_multisort(array_column($panelItems, "sort"), SORT_ASC, $panelItems);

//$finalized = $model->revision->projectQType->is_finalized;
?>

<div class="project-qpanels-view">
    <div class="row">
        <h3 class="col-xs-12 col-xl-9">
                <?= Html::encode($this->title) ?>
            <span style="font-size: medium">
                <?php ?>
            </span>
            <div class="form-check form-check-inline float-right">
                <input type="checkbox" class="custom-control-input" id="allowSort"/>
                <label class="custom-control-label" for="allowSort" style="font-size:12pt">Allow Sort</label>
            </div>
        </h3>

    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-9">

            <?php
            echo yii\jui\Sortable::widget([
            ]);
            ?>

            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th style="width: 65%" class="text-center br px-3" >Item Description</th>
                        <th style="width: 20%" class="text-center bl br px-3">Price (RM)</th>
                        <th style="width: 15%" class="text-center bl px-3">Actions</th>
                    </tr>
                </thead>
                <tbody  id="itemDisplayTable"> 
                    <?php
                    foreach ($panelItems as $key => $item) {
                        ?> 
                        <tr id="tr_<?= $item->id ?>">
                            <td class="br px-3" id="displayItemDesc_<?= $item->id ?>">
                                <?= Html::encode($item->item_description) ?>
                            </td>
                            <td class="text-right bl br px-3" id="displayItemPrice_<?= $item->id ?>">
                                <?= MyFormatter::asDecimal2($item->amount) ?>
                            </td>
                            <td class="text-center bl">
                                <?= Html::a("<i class='far fa-edit'></i>", "javascript:openModalEditItem($item->id)", ['class' => 'text-success', 'title' => "Edit", 'data-method' => 'post']) ?>
                                <?= Html::a("<i class='far fa-trash-alt'></i>", "javascript:removeItem($item->id)", ['class' => 'text-red m-1', 'title' => "Remove", 'data-method' => 'post', 'data-confirm' => 'Are you sure to remove?']) ?>
                            </td>
                        </tr> 
                    <?php }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right">Total (RM)</th>
                        <th class="text-right bl br px-3" id="panelTotal"><?= MyFormatter::asDecimal2($model->amount) ?></th>
                        <th class="text-center bl px-3"></th>
                    </tr>
                </tfoot>
            </table> 

            <table class="table table-sm table-borderless">
                <tr class="m-0 p-0">
                    <th style="width: 65%" class="text-center px-1 m-0 p-0">
                        <input type="text" class="form-control" placeholder="Item Description" id="itemDesc"/>
                        <input type="text" id="panelId" value="<?= $model->id ?>" class="hidden"/>
                    </th>
                    <th style="width: 20%" class="text-center px-1 m-0 p-0">
                        <input type="number" step="0.01" class='form-control text-right twoDecimal' placeholder="Price (RM)" id="itemPrice"/>
                    </th>
                    <th style="width: 15%" class="text-center px-1 m-0 p-0">
                        <a class="btn btn-success" href="javascript:addItem()">Add Item <i class="fas fa-plus" ></i></a>
                    </th>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" class="bt">
                        <fieldset class="form-group border p-3">
                            <legend class="w-auto px-2  m-0">Panel Remarks:</legend>
                            <?= nl2br(Html::encode($model->remark)) ?>
                        </fieldset>
                    </td>
                    <td class="text-center bt">
                        <?php
                        echo Html::a("Edit Remarks <i class='far fa-edit'></i>",
                                "#",
                                ['class' => 'btn btn-success my-3',
                                    'title' => "Edit",
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#modalEditPanelRemark',
                                    ]
                        ]);
                        ?>
                    </td>
                </tr>
            </table> 
            <div class="row">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog" aria-labelledby="modalEditItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditItemLabel">Edit Item Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="req">Description</label>
                <?= Html::textInput('editItemDesc', '', ['id' => 'editItemDesc', 'class' => 'form-control']) ?>
                <label class="req">Price</label>
                <?= Html::textInput('editItemPrice', '', ['id' => 'editItemPrice', 'class' => 'form-control twoDecimal', 'type' => 'number', 'step' => '0.01', 'autocomplete' => false]) ?>
                <div class="hidden"><?= Html::textInput('editItemId', '', ['id' => 'editItemId']) ?></div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <?= Html::a('Update', 'javascript:updateItem()', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditPanelRemark" tabindex="-1" role="dialog" aria-labelledby="modalEditPanelRemarkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'options' => ['autocomplete' => 'off'],
                        'action' => 'update-panel-template-remark'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPanelRemarkLabel">Edit Remark</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
                <?= $form->field($model, 'remark')->textarea(['class' => 'form-control', 'rows' => '8']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<script>

    $(function () {
        $(".twoDecimal").on("blur", function (e) {
            var val = round2Decimal($(this).val()).toFixed(2);
            $(this).val(val);
        });


        $("#allowSort").click(function () {
            if ($('#allowSort').is(":checked")) {
                sortableEnable();
            } else {
                sortableDisable();
            }
        });

    });

    function sortableEnable() {
        $("#itemDisplayTable").sortable({
            cursor: 'move',
            axis: 'y',
            dropOnEmpty: false,
            placeholder: "ui-state-highlight",
            stop: function (e, ui) {
                var id = ui.item[0].id;
                var id1 = $("#" + id).prev().attr('id');
                swapItem(id, id1);
            }
        });
        return false;

    }

    function sortableDisable() {
        $("#itemDisplayTable").sortable({
            disabled: true
        });

        return false;
    }

    function swapItem(moveId, previousId) {
        if (typeof (previousId) === "undefined") {
            previousId = "";
        }

        $.ajax({
            type: "POST",
            url: "sort-panel-item-template-ajax",
            dataType: "json",
            data: {
                panelId: '<?= $model->id ?>',
                moveId: moveId,
                previousId: previousId
            }
        });
    }

    function addItem() {
        if ($("#itemDesc").val() === "") {
            myAlert("Please insert Item Description");
            $("#itemDesc").focus();
            return;
        } else if ($("#itemPrice").val() === "") {
            myAlert("Please insert Item Price");
            $("#itemPrice").focus();
            return;
        }

        $.ajax({
            type: "POST",
            url: "add-template-panel-item-ajax",
            dataType: "json",
            data: {
                itemDesc: $("#itemDesc").val(),
                itemPrice: $("#itemPrice").val(),
                panelId: '<?= $model->id ?>'
            },
            success: function (data) {
                if (data.success) {
                    insertItemRow(data);
                    updatePanelAmount();
                    $("#itemDesc,#itemPrice").val('');
                    $("#itemDesc").focus();
                }
            }
        });
    }

    function removeItem(id) {
        $.ajax({
            type: "POST",
            url: "remove-panel-item-template-ajax",
            dataType: "json",
            data: {
                itemId: id
            },
            success: function (data) {
                $("#tr_" + id).remove();
                updatePanelAmount();
            }
        });
    }

    function insertItemRow(item) {
        var itemId = item.itemId;
        var itemDesc = item.itemDesc;
        var itemPrice = item.itemPrice;
        var newElement = '<tr id="tr_' + itemId + '"><td class="br px-3">' + itemDesc + '</td><td class="text-right bl br px-3">' + itemPrice + '</td><td class="text-center bl">'
                + '<a class="text-success mr-1" href="javascript:openModalEditItem(' + itemId + ')" title="Edit" data-method="post"><i class="far fa-edit"></i></a>'
                + '<a class="text-red mx-1" href="javascript:removeItem(' + itemId + ')" title="Remove" data-method="post" data-confirm="Are you sure to remove?"><i class="far fa-trash-alt"></i></a></td></tr>';
        var wrapper = $("#itemDisplayTable");
        $(wrapper).append(newElement);
    }

    function openModalEditItem(itemId) {
        $.ajax({
            type: "POST",
            url: "load-panel-item-template-ajax",
            dataType: "json",
            data: {
                itemId: itemId
            },
            success: function (data) {
                if (data.success) {
                    $("#editItemDesc").val(data.itemDesc);
                    $("#editItemPrice").val(data.itemPrice);
                    $("#editItemId").val(itemId);
                    $("#modalEditItem").modal('show');
                }
            }
        });
    }

    function updateItem() {
        if ($("#editItemDesc").val() === "") {
            myAlert("Please insert item description.");
            $("#editItemDesc").focus();
            return;
        }
        if ($("#editItemPrice").val() === "") {
            myAlert("Please insert item price.");
            $("#editItemPrice").focus();
            return;
        }


        var itemId = $("#editItemId").val();
        $.ajax({
            type: "POST",
            url: "update-panel-item-template-ajax",
            dataType: "json",
            data: {
                itemDesc: $("#editItemDesc").val(),
                itemId: $("#editItemId").val(),
                itemPrice: $("#editItemPrice").val()
            },
            success: function (data) {
                if (data.success) {
                    $("#displayItemDesc_" + itemId).html(data.itemDesc);
                    $("#displayItemPrice_" + itemId).html(data.itemPrice);
                    $("#modalEditItem").modal('hide');
                    updatePanelAmount();
                }
            }
        });
    }

    function updatePanelAmount() {
        $.ajax({
            type: "POST",
            url: "calculate-panel-template-amount",
            dataType: "json",
            data: {
                panelId: '<?= $model->id ?>'
            },
            success: function (data) {
                if (data.success) {
                    $("#panelTotal").html(data.totalAmount);
                }
            }
        });
    }

</script>