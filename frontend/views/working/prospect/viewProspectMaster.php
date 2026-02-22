<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProspectMaster */

$this->title = $model->proj_code;
$this->params['breadcrumbs'][] = ['label' => 'Prospect Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    table.detail-view th {
        width: 30%;
    }

    table.detail-view td {
        width: 70%;
    }
</style>
<div class="prospect-master-view">
    <div class="container p-0 ">
        <div class=" justify-content-center">
            <h3><?= Html::encode($this->title) ?></h3>
            <div class="card mt-2 border-dark  bg-light">
                <div class="card-header hoverItem border-dark btn-header-link collapsed" id="heading_PM" 
                     data-toggle="collapse" data-target="#collapse_PM" aria-expanded="true" aria-controls="collapse_PM">
                    <span class="accordionHeader">-- PROSPECT DETAIL --</span>
                </div>
                <div id="collapse_PM" class="collapse" aria-labelledby="heading_PM"  >
                    <div class="card-body" style="background-color:white">
                        <p>
                            <?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                        </p>

                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                            'attributes' => [
                                'proj_code',
                                'title_short',
                                'title_long',
                                [
                                    'attribute' => 'due_date',
                                    'value' => function($model) {
                                        return MyFormatter::asDate_Read($model->due_date);
                                    }
                                ],
                                [
                                    'attribute' => 'area',
                                    'value' => function($model) {
                                        return $model['area0']['area_name'];
                                    }
                                ],
                                [
                                    'attribute' => 'staff_pic',
                                    'label' => 'NPL Person In Charge',
                                    'value' => function($model) {
                                        return $model['staffPic']['fullname'];
                                    },
                                    'headerOptions' => ['style' => 'width:100%'],
                                ],
                                'other_pic',
                                [
                                    'attribute' => 'project_type',
                                    'value' => function($model) {
                                        return $model->projectType->project_type_name;
                                    },
                                ],
                                [
                                    'attribute' => 'files',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        if (sizeof($model->files) <= 0) {
                                            return " - ";
                                        }
                                        
                                        $displayStr = "<ul class='list-group'>";
                                        foreach ($model->files as $file) {
                                            $displayStr .= Html::a($file, "/working/prospect/get-file-prospect?filename=" . urlencode($file)
                                                            . "&projCode=" . $model->proj_code, ['target' => "_blank", 'class' => 'list-group-item', 'title' => "Click to view"]);
                                        }
                                        $displayStr .= "</ul>";

                                        return $displayStr;
                                    }
                                ],
                                [
                                    'attribute' => 'created_by',
                                    'value' => function($model) {
                                        return $model['createdBy']['fullname'] . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                                    }
                                ],
                                [
                                    'attribute' => 'updated_by',
                                    'value' => function($model) {
                                        return $model['updatedBy']['fullname'] . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                                    },
                                ],
                            ],
                        ])
                        ?>
                    </div>
                </div>
            </div>


            <div class="card mt-2 border-dark bg-light">
                <div class="card-header hoverItem border-dark btn-header-link collapsed" id="heading_scopes" 
                     data-toggle="collapse" data-target="#collapse_scopes" aria-expanded="false" aria-controls="collapse_scopes">
                    <span class="accordionHeader">-- SCOPES --</span>
                </div>
                <div id="collapse_scopes" class="collapse" aria-labelledby="heading_scopes"  >
                    <div class="card-body" style="background-color:white">
                        <p>
                            <?php
                            $url = '/working/prospect/create-scope-ajax?master_prospect=' . $model->id;
                            echo Html::a('Scope <i class="fas fa-plus"></i>', "javascript:", ["value" => \yii\helpers\Url::to($url), "class" => "modalButtonSmall_ btn btn-primary btn-sm"]);
                            ?>
                        </p>
                        <div id="div_scopes_ajax"></div>
                    </div>
                </div>
            </div>


            <div class="card mt-2 border-dark bg-light">
                <div class="card-header hoverItem border-dark btn-header-link collapsed" id="heading_clients" 
                     data-toggle="collapse" data-target="#collapse_clients" aria-expanded="false" aria-controls="collapse_clients">
                    <span class="accordionHeader">-- CLIENTS --</span>
                </div>
                <div id="collapse_clients" class="collapse" aria-labelledby="heading_clients"  >
                    <div class="card-body" style="background-color:white">
                        <div class="pb-3">
                            <?php
                            $url = '/working/prospect/create-client-ajax?prospect_master=' . $model->id;
                            echo Html::a('Client <i class="fas fa-plus"></i>', "javascript:", ["value" => $url, "class" => "modalButton_ btn btn-primary btn-sm mr-3"]);
                            ?>
                            <div class="custom-switch form-check-inline">
                                <input type="checkbox" class="custom-control-input" id="showClientDetail">
                                <label class="custom-control-label" for="showClientDetail">Show Client P.I.C Detail</label>
                            </div>
                        </div>
                        <div id="div_prospect_client_ajax"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(function () {
        reloadScopesDiv();
        reloadClientDiv();

        $(".modalButton_").click(function () {
            $("#myModal").modal("show")
                    .find("#myModalContent")
                    .load($(this).attr('value'));
        });

        $(".modalButtonSmall_").click(function () {
            $("#myModalSmall").modal("show")
                    .find("#myModalContentSmall")
                    .load($(this).attr('value'));
        });


        $("#showClientDetail").change(function () {
            reloadClientDiv();
        });

    });


    function reloadScopesDiv() {
        $("#div_scopes_ajax").load('<?= \yii\helpers\Url::to('/working/prospect/get-scope-list-ajax?master_prospect=' . $model->id) ?>');
    }
    function reloadClientDiv() {
        var detail = ($("#showClientDetail").is(':checked'));
//        $("#div_prospect_client_ajax").load('<?= \yii\helpers\Url::to('/working/prospect/get-client-list-ajax?master_prospect=' . $model->id) ?>');
        $("#div_prospect_client_ajax").load('/working/prospect/get-client-list-ajax?master_prospect=<?= $model->id ?>&showDetail=' + detail);
    }
</script>