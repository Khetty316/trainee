<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use common\modules\auth\models\AuthItem;

$this->title = $model->quotation_display_no;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qmasters-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="container col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Project Detail</legend>
                <p>
                    <?= Html::a('Update', ['update-projectquotation', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                    <?php
                    if (Yii::$app->user->can(AuthItem::ROLE_Director)) {
                        echo Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    ?>
                </p>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                    'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
                    'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                    'attributes' => [
                        'project_name',
                        'quotation_no',
                        [
                            'attribute' => 'company_group_code',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model['companyGroupCode']['company_name'];
                            }
                        ],
                        [
                            'attribute' => 'project_coordinator',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model['projectCoordinator']['fullname'];
                            }
                        ],
                        'status',
                        'remark:ntext',
                        [
                            'attribute' => 'created_at',
                            'label' => 'Created By',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $createdBy = common\models\User::findOne($model->created_by);
                                if ($createdBy) {
                                    return $createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                                } else {
                                    return null;
                                }
                            }
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => 'Updated By',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->updated_at) {
                                    $updatedBy = common\models\User::findOne($model->updated_by);
                                    return $updatedBy['fullname'] . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                                } else {
                                    return null;
                                }
                            }
                        ],
                    ],
                ])
                ?>
            </fieldset>
        </div>
        <div class="container col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Client:</legend>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>
                            <?php
                            $clientList = frontend\models\client\Clients::getAutocompleteList();
                            echo \yii\jui\AutoComplete::widget([
                                'clientOptions' => [
                                    'source' => $clientList,
                                    'minLength' => '1',
                                    'autoFill' => true,
                                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
                                if(ui.item){
                                    $(this).val(ui.item.value);
                                    $('#clientId').val(ui.item.id);
                                }else{
                                 $(this).val('');
                                    $('#clientId').val('');
                                }
			     }"),
                                    'delay' => 1
                                ],
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'addClientAutocomplete',
                                    'required' => true,
                                    'placeholder' => '(Search client to add)'
                                ]
                            ]);
                            echo "<div class='hidden'>";
                            echo Html::textInput('clientId', '', ['id' => 'clientId']);
                            echo Html::textInput('projectquotationid', $model->id, ['id' => 'projectQuotationId']);
                            echo "</div>";
                            ?>
                        </td>
                        <td style="width:1%">
                            <?= Html::a('Add', 'javascript:addClient()', ['class' => 'btn btn-primary ml-2']) ?>
                        </td>
                    </tr>
                </table>           
                <ul class="list-group" id="clientLists">
                    <?php if ($model->projectQClients) { ?>
                        <?php
                        foreach ($model->projectQClients as $key => $client) {

                            $theQuotation = $client->quotationPdfMasters;

                            $displayStr = "<li class='list-group-item p-2' id='client_$client->id'>" . Html::encode($client->client->company_name);
                            if (!$theQuotation) {
                                $displayStr .= Html::a("&times;", "javascript:removeClient($client->id)", ["class" => "close p-0 m-0", 'data-confirm' => 'Remove client?']);
                            } else {
                                /*    foreach ($theQuotation as $key => $quotation) {
                                  $displayStr .= Html::a('<i class="far fa-file-pdf fa-lg"></i>',
                                  ['/projectqrevision/read-pdf', 'id' => $quotation->id],
                                  ["class" => 'float-right text-red mx-3', 'title' => $quotation->quotation_no, 'target' => '_blank']);
                                  } */
                                foreach ($theQuotation as $key => $quotation) {
                                    $displayStr .= Html::a($quotation->quotation_no . ' <i class="far fa-file-pdf"></i>',
                                                    ['/projectqrevision/read-pdf', 'id' => $quotation->id],
                                                    ["class" => 'list-group-item px-3 py-0 m-0 border-0', 'title' => $quotation->quotation_no, 'target' => '_blank']);
                                }
                            }

                            $displayStr .= "</li>";
                            echo $displayStr;
                        }
                        ?>
                    <?php } ?>
                </ul>

            </fieldset>
        </div>
    </div>
    <div class="row">
        <?php
        $types = $model->projectQTypes;
        foreach ($types as $type) {
            ?>
            <div class="col-sm-12 col-md-3 col-lg-2 my-2">
                <a href="/projectqtype/view-project-q-type?id=<?= $type->id ?>" style="text-decoration: none;">
                    <div class="card hoverDarker <?= $type->projectQRevisions ? "" : "text-secondary" ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $type->type0->project_type_name ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= $type->is_finalized ? "Finalized" : "" ?></h6>
                            <h6>Amount: <?= $type->activeRevision ? ($type->activeRevision->currency->currency_sign . " " . MyFormatter::asDecimal2($type->activeRevision->amount) ) : " - " ?></h6>
                            <p><?= nl2br(Html::encode($type->remark)) ?></p>
                        </div>
                    </div>
                </a>
            </div> 
            <?php
        }
        ?>
    </div>
    <div class="row">
        <br/>
    </div>
</div>

<script>
    function addClient() {
        if ($("#clientId").val() === "") {
            myAlert("Please select a client");
            $("#addClientAutocomplete").focus();
            return;
        }

        $.ajax({
            type: "POST",
            url: "add-client-ajax",
            dataType: "json",
            data: {
                clientId: $("#clientId").val(),
                projectQuotationId: $("#projectQuotationId").val()
            },
            success: function (data) {
                if (data.success) {
                    insertClientRow(data.projQClientId);
                    $("#addClientAutocomplete,#clientId").val('');
                } else {
                    myAlert(data.msg);
                }
            }
        });
    }


    function removeClient(id) {
        $.ajax({
            type: "POST",
            url: "remove-client-ajax",
            dataType: "json",
            data: {
                projQClientId: id,
                projectQuotationId: $("#projectQuotationId").val()
            },
            success: function (data) {
                myAlert(data.msg);
                $("#client_" + id).hide();
            }
        });
    }


    function insertClientRow(projQClientId) {
        var wrapper = $("#clientLists");
        var clientName = $("#addClientAutocomplete").val();

        $(wrapper).append("<li class='list-group-item p-2'  id='client_" + projQClientId + "'>"
                + clientName + '<a href="javascript:removeClient(' + projQClientId + ')" class="close p-0 m-0" data-confirm="Remove client?">&times;</a>'
                + "</li>");

    }


</script>