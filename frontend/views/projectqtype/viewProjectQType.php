<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;

$this->title = $model->type0->project_type_name;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->project_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="/js/bootstrap4-toggle.min.js"></script>
<div class="project-qtypes-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">

        <div class="container col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Project Detail:</legend>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                    'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
                    'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                    'attributes' => [
                        [
                            'format' => 'raw',
                            'label' => 'Project Title',
                            'value' => function ($model) {
                                return $model->project->project_name;
                            }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Quotation No.',
                            'value' => function ($model) {
                                return $model->project->quotation_no;
                            }
                        ],
                        [
                            'attribute' => 'company_group_code',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->project['companyGroupCode']['company_name'];
                            }
                        ],
                        [
                            'attribute' => 'project_q_type_id',
                            'format' => 'raw',
                            'label' => 'Project Type',
                            'value' => function ($model) {
                                return $model->type0->project_type_name;
                            }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Order Confirmed?',
                            'value' => function ($model) {
                                return $model->is_finalized ? "Yes" : "No";
                            }
                        ],
                        'remark:ntext',
                        [
                            'label' => 'Amount',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return "<span id='totalAmtDisplay'>" . ($model->activeRevision ? $model->activeRevision->currency->currency_sign . MyFormatter::asDecimal2($model->activeRevision->amount) : "-") . "</span>";
                            }
                        ],
                    ],
                ])
                ?>
                <p class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                    <span>
                        <?=
                        Html::a(
                                "Update Remark <i class='far fa-edit'></i>",
                                '#',
                                [
                                    'title' => 'Update',
                                    'class' => 'btn btn-success mt-2',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalUpdateProjectQType',
                                ]
                        )
                        ?>
                    </span>

                    <span class="d-flex flex-wrap justify-content-end gap-2">

                        <?php if (!$model->is_finalized): ?>
                            <?=
                            Html::a(
                                    'Confirm Order <i class="fas fa-check"></i>',
                                    "javascript:void(0)",
                                    [
                                        'class' => 'btn btn-success modalButtonMedium mt-2',
                                        "value" => 'ajax-form-confirm-order?typeId=' . $model->id,
                                    ]
                            )
                            ?>
                        <?php else: ?>

                            <?php if ($model->po_file !== null): ?>
                                <?=
                                Html::a(
                                        'P.O <i class="far fa-file-pdf"></i>',
                                        ['read-po-pdf', 'id' => $model->id],
                                        [
                                            "class" => 'btn btn-dark mr-2 mt-2',
                                            'title' => 'View Purchase Order',
                                            'target' => '_blank'
                                        ]
                                )
                                ?>
                            <?php endif; ?>
                            <?=
                            Html::a(
                                    "Attachment <i class='far fa-file-pdf'></i>",
                                    '#',
                                    [
                                        'title' => 'View Attachment',
                                        'class' => 'btn btn-dark mr-2 mt-2',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalAttachments',
                                    ]
                            )
                            ?>

                            <?=
                            Html::a(
                                    'Reverse Confirmation <i class="fas fa-times"></i>',
                                    "javascript:void(0)",
                                    [
                                        'class' => 'btn btn-danger modalButtonMedium mr-2 mt-2',
                                        "value" => 'ajax-form-reverse-confirm-order?typeId=' . $model->id,
                                    ]
                            )
                            ?>

                            <?php if (empty($model->projProd)): ?>
                                <?php if ($masterId): ?>
                                    <?=
                                    Html::a(
                                            'View Project <i class="fas fa-eye"></i>',
                                            ["/production/production/view-production-main", "id" => $masterId],
                                            [
                                                'class' => 'btn btn-primary mr-2 mt-2',
                                                'style' => 'background-color: black; border: 1px solid black;',
                                            ]
                                    )
                                    ?>
                                    <?=
                                    Html::a(
                                            'Re-Push to Project <i class="fas fa-share"></i>',
                                            ["/production/production/repush-production-main", "id" => $model->id],
                                            ['class' => 'btn btn-primary mr-2 mt-2']
                                    )
                                    ?>
                                <?php else: ?>
                                    <?=
                                    Html::a(
                                            'Push to Project <i class="fas fa-share"></i>',
                                            ["/production/production/create-production-main", "id" => $model->id],
                                            ['class' => 'btn btn-primary mr-2 mt-2']
                                    )
                                    ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <?=
                                Html::a(
                                        'View Project <i class="fas fa-eye"></i>',
                                        ["/production/production/view-production-main", "id" => $model->proj_prod_id],
                                        [
                                            'class' => 'btn btn-primary mr-2 mt-2',
                                            'style' => 'background-color: black; border: 1px solid black;',
                                        ]
                                )
                                ?>
                                <?=
                                Html::a(
                                        'Re-Push to Project <i class="fas fa-share"></i>',
                                        ["/production/production/repush-production-main", "id" => $model->id],
                                        ['class' => 'btn btn-primary mr-2 mt-2']
                                )
                                ?>
                            <?php endif; ?>

                        <?php endif; ?>
                    </span>

                </p>

            </fieldset>
        </div>
        <div class="container col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Client:</legend>
                <table class="table table-sm table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th class="tdnowrap">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ((array) $model->project->projectQClients as $key => $client) {
                            ?>
                            <tr>
                                <td>
                                    <?= Html::encode($client->client->company_name) ?>
                                </td>  
                                <td class="text-center">
                                    <?php
                                    if (!$model->is_finalized) {
                                        echo Html::checkbox('setActiveClient', ($client->id == $model->active_client_id), ['value' => $client->id]);
                                    } else {
                                        echo ($client->id == $model->active_client_id) ? '<i class="fas fa-check"></i>' : "";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php //} ?>
                    </tbody>

                </table>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Revisions:</legend>
                <?php
                if (!$model->is_finalized) {
                    echo Html::a(
                            'New Revision <i class="fas fa-plus"></i>',
                            '#',
                            [
                                'title' => 'Create new revision',
                                'class' => 'btn btn-success mb-2 mt-0',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalNewRevision',
                            ]
                    );
                    echo Html::a(
                            'New Revision From Template <i class="far fa-object-ungroup"></i>',
                            '#',
                            [
                                'title' => 'Create new revision',
                                'class' => 'btn btn-success mb-2 mt-0 ml-2',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalNewRevisionFromTemplate',
                            ]
                    );
                }
                ?>
                <?php
                $revisions = $model->projectQRevisions;
                if ($revisions) {
                    array_multisort(array_column($revisions, "id"), SORT_DESC, $revisions);
                    ?> 
                    <table class="table table-sm table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Revision Name</th>
                                <th style="width: 25%" class="text-right" >Revision Amount</th>
                                <?php if (!$model->is_finalized) { ?>
                                    <th style="width: 20%" class="text-center">Action</th>
                                <?php } ?>
                                <th  style="width: 1px"  class='tdnowrap text-center'>Set Active</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($revisions as $key => $revision) {
                            ?>
                            <tr id="tr_<?= $revision->id ?>">
                                <td>
                                    <?=
                                    Html::a($revision->revision_description,
                                            ['/projectqrevision/view-project-q-revision', 'id' => $revision->id],
                                            ['title' => 'Edit', 'class' => 'mx-1 text-primary'])
                                    ?>
                                </td>
                                <td class="text-right px-2">
                                    <?= $revision->currency->currency_sign ?>&nbsp;
                                    <?= MyFormatter::asDecimal2($revision->amount) ?>
                                </td>
                                <?php
                                if (!$model->is_finalized) {
//                                    $gotpushedPanel = !empty(array_filter($revision->projectQPanels, fn($panel) => !empty($panel->projectProductionPanels)));
                                    ?>
                                    <td class="text-center">
                                        <?php // if (!$gotpushedPanel) { ?>
                                            <?=
                                            Html::a(
                                                    '<i class="far fa-clone fa-lg"></i>',
                                                    '#',
                                                    [
                                                        'title' => 'Clone',
                                                        'class' => 'mx-1 text-success',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modalCloneRevision',
                                                        'data-revisionname' => $revision->revision_description,
                                                        'data-motherrevisionid' => $revision->id
                                                    ]
                                            )
                                            ?>
                                        <?php // } else { ?>
                                            <?php
//                                            =
//                                            Html::a(
//                                                    '<i class="far fa-clone fa-lg text-muted"></i>'
//                                            )
                                            ?>
                                        <?php // } ?>
                                    </td>
                                <?php }
                                ?>
                                <td class='tdnowrap text-center'>
                                    <?php
                                    if (!$model->is_finalized) {
                                        echo Html::checkbox('setActiveToogle', ($revision->id == $model->active_revision_id), ['value' => $revision->id]);
                                    } else {
                                        echo ($revision->id == $model->active_revision_id) ? '<i class="fas fa-check"></i>' : "";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>   
                    </table>
                    <?php
                } else {
                    echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                }
                ?>
            </fieldset>
        </div>

        <div class="col-xs-12 col-xl-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Quotation Email History:</legend>

                <div id="app">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded" id="maintable">
                            <thead>
                                <tr>
                                    <th @click="sortTable('revision')" class="search-hover text-primary">Revision Name</th>
                                    <th @click="sortTable('client')" class="search-hover text-primary">Client</th>
                                    <th @click="sortTable('sentBy')" class="search-hover text-primary">Sent By</th>
                                    <th @click="sortTable('sender')" class="search-hover text-primary">Sender</th>
                                    <th @click="sortTable('recipient')" class="search-hover text-primary">Recipient</th>
                                    <th class="text-primary">CC</th>
                                    <th class="text-primary">BCC</th>
                                    <th @click="sortTable('subject')" class="search-hover text-primary">Subject</th>
                                    <th @click="sortTable('emailType')" class="search-hover text-primary">Email Type</th>
                                </tr>
                                <tr>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.revision"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.client"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.sentBy"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.sender"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.recipient"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.cc"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.bcc"></th>
                                    <th class="p-1"><input class="form-control form-control-sm" v-model="searchCriteria.subject"></th>
                                    <th class="p-1">
                                        <select class="form-control form-control-sm" v-model="searchCriteria.emailType">
                                            <option value="">All</option>
                                            <option value="webmail">Webmail</option>
                                            <option value="outlook">Outlook</option>
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="model in paginatedModels" :key="model.id">
                                    <td class="p-1">{{ model.revision }}</td>
                                    <td class="p-1">{{ model.client }}</td>
                                    <td class="p-1">{{ model.sentBy }}</td>
                                    <td class="p-1">{{ model.sender }}</td>
                                    <td class="p-1">{{ model.recipient }}</td>
                                    <td class="p-1">{{ model.cc }}</td>
                                    <td class="p-1">{{ model.bcc }}</td>
                                    <td class="p-1">{{ model.subject }}</td>
                                    <td class="p-1">{{ model.emailType }}</td>
                                </tr>
                                <tr v-if="paginatedModels.length === 0">
                                    <td colspan="9" class="text-center text-muted">No email records found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination my-3 flex justify-content-end">
                        <button class="btn btn-sm btn-primary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
                        <span class="pt-1">&nbsp;{{ currentPage }} / {{ totalPages }}&nbsp;</span>
                        <button class="btn btn-sm btn-primary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
                    </div>
                </div>
            </fieldset>
        </div>

        <?php
        $vueData = [];
        if (!empty($emailHistory)) {
            foreach ($emailHistory as $history) {
                if (!empty($history['emails_with_clients'])) {
                    foreach ($history['emails_with_clients'] as $item) {
                        $email = $item['email'];
                        if ($email->recipient !== null) {
                            $sentBy = ($email->sent_by === null) ? '-' : $email->sentBy->fullname . ' @ ' .
                                    \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($email->sent_at);

                            $vueData[] = [
                                'id' => $email->id,
                                'revision' => $history['revision_description'],
                                'client' => $item['client_name'],
                                'sentBy' => $sentBy,
                                'sender' => $email->sender ?? '-',
                                'recipient' => $email->recipient ?? '-',
                                'cc' => $email->Cc ?? '-',
                                'bcc' => $email->Bcc ?? '-',
                                'subject' => $email->subject ?? '-',
                                'emailType' => $email->email_type ?? '-',
                            ];
                        }
                    }
                }
            }
        }
        ?>

        <script>
window.models = <?= Json::encode($vueData) ?>;
window.numPerPage = 5;
        </script>
        <script src="/js/vueTable.js"></script>
    </div>
</div>

<div class="modal fade" id="modalNewRevision" tabindex="-1" role="dialog" aria-labelledby="modalNewPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $newRevision = new \frontend\models\projectquotation\ProjectQRevisions();
            $form2 = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
                'action' => 'new-revision'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalNewPanelLabel">New Revision</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form2->field($newRevision, 'project_q_type_id', ['options' => ['class' => 'hidden']])->hiddenInput(['value' => $model->id])->label(false) ?>
                <?= $form2->field($newRevision, 'revision_description')->textInput(['readonly' => true])->label("New Revision Name") ?>
                <?= $form2->field($newRevision, 'currency_id')->dropDownList($currencyList)->label("Currency") ?>
                <?= $form2->field($newRevision, 'remark')->textarea(['rows' => 6]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create <i class="fas fa-plus"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCloneRevision" tabindex="-1" role="dialog" aria-labelledby="modalCloneRevisionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $cloneRevision = new \frontend\models\projectquotation\ProjectQRevisions();
            $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off'],
                'action' => 'clone-revision-same-projecttype'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalCloneRevisionLabel">Cloning Revision</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless"> 
                    <tbody>
                        <tr>
                            <td class="tdnowrap">Clone from revision</td>
                            <td> : </td>
                            <td id="modal-cloneFromRevisionName"></td>
                        </tr>
                        <tr>
                            <td class="req tdnowrap">New revision name</td>
                            <td> : </td>
                            <td>
                                <?= Html::hiddenInput('motherRevisionId', '', ['id' => 'modal-motherRevisionId']) ?>
                                <?= Html::textInput('cloneRevisionNewName', '', ['id' => 'cloneRevisionNewName', 'class' => 'form-control', 'required' => true, 'readonly' => true]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td> : </td>
                            <td>
                                <?= Html::textarea('cloneRevisionNewRemark', '', ['id' => 'cloneRevisionNewRemark', 'class' => 'form-control', 'rows' => 6]) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Clone <i class="far fa-clone"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateProjectQType" tabindex="-1" role="dialog" aria-labelledby="modalUpdateProjectQTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $m = $model->type0;
            $form3 = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
                'action' => 'update'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateProjectQTypeLabel">Update <?= $model->type0->project_type_name ?> Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form3->field($model, 'id', ['options' => ['class' => 'hidden']])->hiddenInput()->label(false) ?>
                <?= $form3->field($model, 'remark')->textarea(['rows' => 6]) ?>
                <?php //= $form3->field($model, 'is_finalized')->checkbox()   ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Update <i class="fas fa-check"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewRevisionFromTemplate" tabindex="-1" role="dialog" aria-labelledby="modalNewRevisionFromTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $newRevision2 = new \frontend\models\projectquotation\ProjectQRevisions();
            $form4 = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
                'action' => 'new-revision-from-template'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalNewRevisionFromTemplateLabel">New Revision From Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form4->field($newRevision, 'project_q_type_id', ['options' => ['class' => 'hidden']])->hiddenInput(['value' => $model->id])->label(false) ?>
                <?= $form4->field($newRevision, 'revision_description')->textInput(['readonly' => true])->label("New Revision Name") ?>
                <?= $form4->field($newRevision, 'templateId')->dropdownList($revisionTemplateList)->label("Select template") ?>
                <?= $form4->field($newRevision, 'remark')->textarea(['rows' => 6]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create <i class="fas fa-plus"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewRevisionFromTemplate" tabindex="-1" role="dialog" aria-labelledby="modalNewRevisionFromTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form4 = ActiveForm::begin([
                'action' => 'new-revision-from-template'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalNewRevisionFromTemplateLabel">New Revision From Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form4->field($newRevision, 'project_q_type_id', ['options' => ['class' => 'hidden']])->hiddenInput(['value' => $model->id])->label(false) ?>
                <?= $form4->field($newRevision, 'revision_description')->textInput(['readonly' => true])->label("New Revision Name") ?>
                <?= $form4->field($newRevision, 'templateId')->dropdownList($revisionTemplateList)->label("Select template") ?>
                <?= $form4->field($newRevision, 'remark')->textarea(['rows' => 6]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create <i class="fas fa-plus"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAttachments" tabindex="-1" role="dialog" aria-labelledby="modalAttachmentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAttachmentsLabel">Upload Attachments for <?= $model->type0->project_type_name ?> Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
            $form5 = ActiveForm::begin([
                'options' => [
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data'
                ],
                'action' => ['save-attachments',
                    'id' => $model->id],
                'method' => 'post'
            ]);
            ?>
            <div class="modal-body">
                <?=
                $form5->field($model, 'attachments[]')->fileInput([
                    'multiple' => true, 'accept' => '.pdf', 'id' => 'attachment-input'])
                ?>
                <div class="col-sm-12 col-md-10 col-lg-12">
                    <table class="table table-bordered table-sm mt-2" id="file-table">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%;">No.</th>
                                <th>File</th>
                                <th style="width: 20%;">Uploaded By</th>
                                <th style="width: 20%;">Deleted By</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="file-table-body"></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Upload <i class="fas fa-check"></i></button> 
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script src="\js\vueTable.js"></script>
<script>
const attachmentInput = document.getElementById('attachment-input');
const fileList = document.getElementById('file-list');

$(function () {
    $('#modalNewRevision').on('show.bs.modal', function (event) {
        var modal = $(this);
        var value = "Revision " + getRevisionNumber();
        modal.find('#projectqrevisions-revision_description').val(value);
    });
    $('#modalNewRevisionFromTemplate').on('show.bs.modal', function (event) {
        var modal = $(this);
        var value = "Revision " + getRevisionNumber();
        modal.find('#projectqrevisions-revision_description').val(value);
    });

    $('#modalCloneRevision').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal        
        var modal = $(this);
        modal.find('#modal-cloneFromRevisionName').text(button.data('revisionname'));
        modal.find('#modal-motherRevisionId').val(button.data('motherrevisionid'));
        var value = "Revision " + getRevisionNumber();
        modal.find('#cloneRevisionNewName').val(value);
    });


    $("input[name='setActiveToogle']").click(function (e) {
        let isChecked = $(this).prop('checked');
        if (isChecked) {
            $("input[name='setActiveToogle']:checkbox").prop('checked', false);
            $(this).prop('checked', isChecked);
            setActiveRevision($(this).val());
        } else {
            setActiveRevision(0);
        }
    });

    $("input[name='setActiveClient']").click(function (e) {
        let isChecked = $(this).prop('checked');
        if (isChecked) {
            $("input[name='setActiveClient']:checkbox").prop('checked', false);
            $(this).prop('checked', isChecked);
            setActiveClient($(this).val());
        } else {
            setActiveClient(0);
        }
    });
});

function setActiveRevision(revisionId) {
    $.ajax({
        type: "POST",
        url: "set-active-revision-ajax",
        dataType: "json",
        data: {
            projectQTypeId: <?= $model->id ?>,
            revisionId: revisionId
        },
        success: function (data) {
            if (data.success) {
                $("#totalAmtDisplay").html(data.total);
            }
        }
    });
}

function setActiveClient(clientId) {
    $.ajax({
        type: "POST",
        url: "set-active-client-ajax",
        dataType: "json",
        data: {
            projectQTypeId: <?= $model->id ?>,
            clientId: clientId
        },
//            success: function (data) {
//                if (data.success) {
//                    $("#totalAmtDisplay").html(data.total);
//                }
//            }
    });
}

function reloadRevisionAmount() {
    $.ajax({
        type: "GET",
        url: "load-revision-amount",
        dataType: "json",
        data: {
            revisionId: <?= $model->id ?>
        },
        success: function (data) {
            $("#revisionAmountDisplay").html("RM " + data.amount);
        }
    });
}

function getRevisionNumber() {
    var number = 0;
    $.ajax({
        type: "GET",
        url: "load-revision-number-ajax",
        dataType: "json",
        async: false,
        data: {
            projQTypeId: <?= $model->id ?>
        },
        success: function (data) {
            number = data.number;
        }
    });

    return number;
}

const currentId = <?= json_encode($model->id) ?>;
var existingAttachments = <?=
            json_encode(
                    array_map(function ($a) {
                        return [
                            'id' => $a->id,
                            'name' => $a->filename,
                            'created_at' => $a->created_at,
                            'created_by' => $a->createdBy->fullname,
                            'deleted_at' => $a->deleted_at,
                            'deleted_by' => $a->deleted_by !== null ? $a->deletedBy->fullname : '-',
                        ];
                    }, $model->projectQTypesAttachments ?? [])
            )
            ?>;

let fileStore = new DataTransfer();
renderList();

attachmentInput.addEventListener('change', function (e) {
    const selected = Array.from(attachmentInput.files);

    selected.forEach(file => {
        const duplicate = Array.from(fileStore.files).some(f =>
            f.name === file.name && f.size === file.size && f.lastModified === file.lastModified
        );
        if (!duplicate)
            fileStore.items.add(file);
    });
    attachmentInput.files = fileStore.files;

//    // render the current list
    renderList();
});

function renderList() {
    const tableBody = document.getElementById('file-table-body');
    tableBody.innerHTML = '';

    existingAttachments.forEach((attachment, index) => {
        const tr = document.createElement('tr');

        // No.
        const noTd = document.createElement('td');
        noTd.textContent = index + 1;
        noTd.className = 'text-center';
        tr.appendChild(noTd);

        // File
        const fileTd = document.createElement('td');
        if (attachment.url) {
            const a = document.createElement('a');
            a.href = attachment.url;
            a.textContent = attachment.name;
            a.target = '_blank';
            fileTd.appendChild(a);
        } else {
            fileTd.textContent = attachment.name;
        }
        tr.appendChild(fileTd);

        // Uploaded At
        const uploadedTd = document.createElement('td');
        uploadedTd.textContent =
                attachment.created_at
                ? `${attachment.created_by ?? '-'} @ ${formatDateTime(attachment.created_at)}`
                : '-';
        uploadedTd.className = 'text-center';
        tr.appendChild(uploadedTd);

        // Deleted At
        const deletedTd = document.createElement('td');
        deletedTd.textContent =
                attachment.deleted_at
                ? `${attachment.deleted_by ?? '-'} @ ${formatDateTime(attachment.deleted_at)}`
                : '-';
        deletedTd.className = 'text-center text-danger';
        tr.appendChild(deletedTd);
        // Actions
        const actionTd = document.createElement('td');
        if (!attachment.deleted_at || !attachment.deleted_by) {

            actionTd.className = 'text-center';

            const basePdfUrl = <?= json_encode(\yii\helpers\Url::to(['projectqtype/read-pdf', 'id' => $model->id])) ?>;
            const readPdfUrl = basePdfUrl + '&file_name=' + encodeURIComponent(attachment.name);

            // View button
            const viewBtn = document.createElement('a');
            viewBtn.href = readPdfUrl;
            viewBtn.className = 'btn btn-sm btn-primary mr-1';
            viewBtn.target = '_blank';
            viewBtn.innerHTML = 'View';
            actionTd.appendChild(viewBtn);

            // Delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-sm btn-danger';
            deleteBtn.innerHTML = 'Delete';
            deleteBtn.dataset.id = attachment.id;
            deleteBtn.addEventListener('click', function () {
                removeExistingAttachment(attachment.id);
            });
            actionTd.appendChild(deleteBtn);
        }
        tr.appendChild(actionTd);
        tableBody.appendChild(tr);
    });

    // === Newly selected (unsaved) files ===
    Array.from(fileStore.files).forEach((file, idx) => {
        const tr = document.createElement('tr');

        // No.
        const noTd = document.createElement('td');
        noTd.textContent = existingAttachments.length + idx + 1;
        noTd.className = 'text-center';
        tr.appendChild(noTd);

        // File
        const fileTd = document.createElement('td');
        fileTd.textContent = file.name;
        tr.appendChild(fileTd);

        // Uploaded At
        const uploadedTd = document.createElement('td');
        uploadedTd.textContent = 'Pending upload';
        uploadedTd.className = 'text-center text-muted';
        tr.appendChild(uploadedTd);

        // Deleted At
        const deletedTd = document.createElement('td');
        deletedTd.textContent = '-';
        deletedTd.className = 'text-center';
        tr.appendChild(deletedTd);

        // Actions
        const actionTd = document.createElement('td');
        actionTd.className = 'text-center';

        // View button
        const viewBtn = document.createElement('a');
        const fileUrl = URL.createObjectURL(file);
        viewBtn.href = fileUrl;
        viewBtn.className = 'btn btn-sm btn-primary mr-1';
        viewBtn.target = '_blank';
        viewBtn.innerHTML = 'View';
        actionTd.appendChild(viewBtn);

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-danger';
        removeBtn.innerHTML = 'Delete';
        removeBtn.dataset.index = idx;
        removeBtn.addEventListener('click', function () {
            removeFile(parseInt(this.dataset.index, 10));
        });
        actionTd.appendChild(removeBtn);

        tr.appendChild(actionTd);
        tableBody.appendChild(tr);
    });
}

function formatDateTime(datetimeString) {
    if (!datetimeString)
        return '-';
    const date = new Date(datetimeString);
    if (isNaN(date))
        return '-';

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    const hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12;

    return `${day}/${month}/${year} ${formattedHours}:${minutes} ${ampm}`;
}

function removeFile(index) {
    const newStore = new DataTransfer();
    Array.from(fileStore.files).forEach((f, i) => {
        if (i !== index)
            newStore.items.add(f);
    });

    fileStore = newStore;
    attachmentInput.files = fileStore.files;
    renderList();
}

function removeExistingAttachment(id) {
    if (!confirm('Are you sure you want to delete this attachment?')) {
        return;
    }

    $.ajax({
        url: '<?= \yii\helpers\Url::to(['ajax-delete-attachment']) ?>?id=' + id,
        method: 'POST',
        data: {id: id},
        success: function (response) {
            if (response.success) {
                existingAttachments = existingAttachments.filter(att => att.id !== id);
                location.reload();

            } else {
                alert("Failed to delete attachment: " + (response.error || "Unknown error"));
            }
        },
        error: function () {
            alert("Server error while deleting attachment.");
        }
    });
}
</script>