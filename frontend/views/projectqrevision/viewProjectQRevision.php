<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use frontend\models\projectquotation\QuotationPdfMasters;
use frontend\models\common\RefProjectQPanelUnit;
use yii\helpers\Url;
use common\modules\auth\models\AuthItem;
use frontend\models\projectquotation\QuotationEmails;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisions */


$this->title = $model->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = $this->title;

$finalized = $model->projectQType->is_finalized;
$sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;
$company = $model->projectQType->project->companyGroupCode;
//$sst = $company->sst_value;
$amoutBeforeSST = 0;
$SSTAmount = 0;
$totalAmountBeforeSST = 0;
$totalSSTAmount = 0;
$A = QuotationPdfMasters::find()
        ->where(['revision_id' => $model->id])
        ->andWhere(['in', 'md_approval_status', [QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL, QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED]])
        ->exists();
$sentEmail = false;
$clients = $model->projectQType->project->projectQClients;
if ($clients) {
    foreach ($clients as $client) {
        $quotationIds = QuotationPdfMasters::find()
                ->select('id')
                ->where(['project_q_client_id' => $client->id, 'revision_id' => $model->id])
                ->column();

        if (!empty($quotationIds)) {
            $hasEmail = QuotationEmails::find()
                    ->where(['in', 'quotation_id', $quotationIds])
                    ->andWhere(['not', ['recipient' => null]])
                    ->andWhere(['not', ['sent_at' => null]])
                    ->andWhere(['not', ['sent_by' => null]])
                    ->exists();

            if ($hasEmail) {
                $sentEmail = true; // update flag if any email exists
                break; // no need to check further, one email is enough
            }
        }
    }
}

$isDisabled = $A || $sentEmail;
?>
<style>
    .disabled-link {
        color: gray !important;
        pointer-events: none;
        text-decoration: none;
        cursor: not-allowed;
    }

    .btn-muted {
        background-color: #e0e0e0;
        color: #6c757d;
        border: 1px solid #ccc;
        pointer-events: none;
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
<div class="project-qrevisions-view">

    <h3>
        <?= Html::encode($this->title) ?>
    </h3>
    <p class="col-xs-12 col-xl-6 pl-0">
        <?php if (!$finalized) { ?>
            <?= Html::a("Update Revision Detail <i class='far fa-edit'></i>", ['update-projectqrevision', 'id' => $model->id], ['class' => 'btn btn-success ' . ($isDisabled ? 'btn-muted' : ''),]) ?>
            <?=
            Html::a('Set As Template <i class="far fa-object-ungroup"></i>',
                    '#',
//                    ['#', 'revisionId' => $model->id, 'templateName' => 'testing first'],
                    ['class' => 'btn btn-primary float-right', 'data' => ['toggle' => 'modal', 'target' => '#modalSetTemplate']])
            ?>
            <?php
        } else {
            echo yii\jui\Sortable::widget([
            ]);
            ?>
            <?=
            Html::a('Set As Template <i class="far fa-object-ungroup"></i>',
                    '#',
//                    ['#', 'revisionId' => $model->id, 'templateName' => 'testing first'],
                    ['class' => 'btn btn-primary', 'data' => ['toggle' => 'modal', 'target' => '#modalSetTemplate']])
            ?>
        <?php } ?>
    </p>
    <div class="row">
        <div class="col-xs-12 col-xl-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Quotation Detail:</legend>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                    'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
                    'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                    'attributes' => [
                        [
                            'format' => 'raw',
                            'label' => 'Project Title',
                            'value' => function ($model) {
                                return $model->projectQType->project->project_name;
                            }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Quotation No.',
                            'value' => function ($model) {
                                return $model->projectQType->project->quotation_no;
                            }
                        ],
                        [
                            'attribute' => 'project_q_type_id',
                            'format' => 'raw',
                            'label' => 'Project Type',
                            'value' => function ($model) {
                                return $model->projectQType->type0->project_type_name;
                            }
                        ],
                        [
                            'attribute' => 'revision_description',
                            'format' => 'raw',
                            'label' => 'Revision Name'
                        ],
                        'remark:ntext',
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->currency->currency_sign . '&nbsp;<span id="revisionAmountDisplay">' . MyFormatter::asDecimal2($model->amount) . '</span>';
                            }
                        ],
                        [
                            'attribute' => 'created_by',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $createdBy = common\models\User::findOne($model->created_by);
                                return $createdBy ? ($createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at)) : NULL;
                            }
                        ],
                        [
                            'attribute' => 'updated_by',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $updatedBy = common\models\User::findOne($model->updated_by);
                                return $updatedBy ? ($updatedBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at)) : NULL;
                            }
                        ],
                    ],
                ])
                ?>
                <div class="card mt-2 border-dark bg-light">
                    <div class="card-header hoverItem border-dark btn-header-link collapsed text-center py-1" id="heading_scopes" 
                         data-toggle="collapse" data-target="#collapse_scopes" aria-expanded="false" aria-controls="collapse_scopes">
                        <span class="accordionHeader">-- More details --</span>
                    </div>
                    <div id="collapse_scopes" class="collapse" aria-labelledby="heading_scopes"  >
                        <div class="card-body p-0" style="background-color:white">
                            <?=
                            DetailView::widget([
                                'model' => $model,
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                                'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
                                'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                                'attributes' => [
                                    'q_material_offered:ntext',
                                    'q_switchboard_standard:ntext',
                                    'q_quotation',
                                    [
                                        'attribute' => 'q_delivery',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return $model->q_delivery_ship_mode . " - " . $model->q_delivery_destination . " - " . $model->q_delivery;
                                        }
                                    ],
                                    'q_validity',
                                    'q_payment',
                                    'q_remark:ntext',
                                ],
                            ])
                            ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-xs-12 col-xl-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Quotations for client:</legend>
                <?php
                if (!$finalized) {
                    ?>
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
                                                $(this).val(''); $('#clientId').val('');
                                            }
                                        }"),
                                        'delay' => 500
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
                                echo Html::textInput('projectquotationid', $model->projectQType->project->id, ['id' => 'projectQuotationId']);
                                echo "</div>";
                                ?>
                            </td>
                            <td style="width:1%">
                                <?= Html::a('Add', 'javascript:addClient()', ['class' => 'btn btn-primary ml-2']) ?>
                            </td>
                        </tr>
                    </table>   
                <?php } ?>
                <table class="table table-sm table-bordered" id="clientTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Client Name</th>
                            <th class="text-center">Select All<br/>
                                <input type="checkbox" id="selectAllQuotations" class="mx-2">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
//                        $clients = $model->projectQType->project->projectQClients;
                        if ($clients) {
                            foreach ($clients as $key => $client) {
                                $theQuotation = QuotationPdfMasters::find()->where(['project_q_client_id' => $client->id, 'revision_id' => $model->id])->all();
                                if ($theQuotation) {
                                    $emailRecord = QuotationEmails::find()
                                            ->where([
                                                'quotation_id' => $theQuotation[0]->id,
                                                'client_id' => $client->id,
                                            ])
                                            ->andWhere(['not', ['recipient' => null]])
                                            ->andWhere(['not', ['sent_at' => null]])
                                            ->andWhere(['not', ['sent_by' => null]])
                                            ->exists();
                                } else {
                                    $emailRecord = false;
                                }
                                ?>

                                <tr id="client_<?= $client->id ?>">
                                    <td>
                                        <?= Html::encode($client->client->company_name) ?>
                                        <?php if ($theQuotation): ?>
                                            <?php
                                            $userName = Yii::$app->user->identity->fullname;
                                            $emailBodyPlain = "Good day,\n\nAttached herewith is the quotation.\n\nThank You\nBest Regards\n\n" . $userName . "\n\nTenaga Kenari Sdn Bhd\nLot 3203, Block 1,\nSamajaya Free Industrial Zone,\n93450 Kuching, Sarawak, Malaysia\nTel : +6016-966 2278\n     : +6016-970 2278\nEmail: tkenari@gmail.com";

                                            $emailBodyEncoded = "Good day,%0D%0A%0D%0AAttached herewith is the quotation.%0D%0A%0D%0AThank You%0D%0ABest Regards%0D%0A%0D%0A" . urlencode($userName) . "%0D%0A%0D%0ATenaga Kenari Sdn Bhd%0D%0ALot 3203, Block 1,%0D%0ASamajaya Free Industrial Zone,%0D%0A93450 Kuching, Sarawak, Malaysia%0D%0ATel : +6016-966 2278%0D%0A     : +6016-970 2278%0D%0AEmail: tkenari@gmail.com";

                                            $clientEmail = !empty($email_model->sender) ? $email_model->sender : 'project@tenagakenari.com.my';
                                            $subject = 'Quotation - ' . $theQuotation[0]->quotation_no;

                                            $outlookLink = 'mailto:' . urlencode($clientEmail) .
                                                    '?subject=' . rawurlencode($subject) .
                                                    '&body=' . $emailBodyEncoded;

                                            $webmailLink = 'https://rc.ds.network/?_task=mail&_action=compose&_to=' . urlencode($clientEmail) . '&_subject=' . urlencode($subject) . '&_body=' . urlencode($emailBodyPlain);

                                            echo '<div class="btn-group dropup float-right">';
                                            echo Html::button('Email 
                                                ', [
                                                'class' => 'btn btn-primary btn-sm dropdown-toggle pt-0 pb-0',
                                                'data-toggle' => 'dropdown',
                                                'aria-haspopup' => 'true',
                                                'aria-expanded' => 'false',
                                                'title' => 'Send quotation to client'
                                            ]);
                                            echo '<ul class="dropdown-menu dropdown-menu-right modern-dropdown">';
                                            echo '<li class="dropdown-header">
                                                    <i class="fa fa-paper-plane text-primary"></i> 
                                                    <strong>Send Options</strong>
                                                  </li>';
                                            echo '<li class="divider"></li>';
                                            echo '<li>' . Html::a('
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-wrapper outlook">
                                                                <i class="fa fa-envelope"></i>
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="option-title">Outlook</div>
                                                            </div>
                                                        </div>',
                                                    [
                                                        'create-email-form',
                                                        'id' => $theQuotation[0]->id,
                                                        'client_id' => $client->id,
                                                        'email_type' => 'outlook',
                                                        'clientEmail' => $clientEmail,
                                                        'subject' => $subject,
                                                        'content' => $emailBodyPlain,
                                                    ],
                                                    [
                                                        'title' => 'Open in Outlook',
                                                        'class' => 'modern-dropdown-item'
                                                    ]) . '</li>';

                                            echo '<li>' . Html::a('
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-wrapper webmail">
                                                                <i class="fa fa-globe"></i>
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="option-title">Webmail</div>
                                                            </div>
                                                        </div>',
                                                    [
                                                        'create-email-form',
                                                        'id' => $theQuotation[0]->id,
                                                        'client_id' => $client->id,
                                                        'email_type' => 'webmail',
                                                        'clientEmail' => $clientEmail,
                                                        'subject' => $subject,
                                                        'content' => $emailBodyPlain,
                                                    ],
                                                    [
                                                        'title' => 'Open in Webmail',
                                                        'class' => 'modern-dropdown-item'
                                                    ]) . '</li>';
                                            echo '</ul>';
                                            echo '</div>';

                                            echo '<style>
.modern-dropdown {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    padding: 5px 0;
    min-width: 220px;
    margin-top: 5px;
}
.modern-dropdown-item {
    padding: 10px 10px;
    display: block;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
}
.modern-dropdown-item:hover {
    background-color: #f8f9fa;
    color: #333;
    text-decoration: none;
    transform: translateX(2px);
}
.icon-wrapper {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}
.icon-wrapper.outlook {
    background: linear-gradient(45deg, #0078d4, #106ebe);
}
.icon-wrapper.webmail {
    background: linear-gradient(45deg, #17a2b8, #138496);
}
.option-title {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}
</style>';
                                            ?>
                                            <?php if (!$finalized): ?>
                                                <?php if ((Yii::$app->user->can(AuthItem::ROLE_Director) || Yii::$app->user->id == $theQuotation[0]->created_by) && $theQuotation[0]->md_approval_status !== QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED && !$emailRecord): ?>
                                                    <?=
                                                    Html::a('<i class="far fa-trash-alt fa-lg"></i>',
                                                            ['delete-quotation-pdf', 'id' => $theQuotation[0]->id, 'projQClientId' => $client->id],
                                                            ["class" => 'text-danger mx-1 float-right', 'title' => 'Remove quotation', 'data-confirm' => 'Remove quotation?', 'data-method' => 'post'])
                                                    ?>
                                                <?php endif; ?>
                                                <?php if ((Yii::$app->user->can(AuthItem::ROLE_Director) || Yii::$app->user->id == $theQuotation[0]->created_by) && !$emailRecord): ?>

                                                    <?=
                                                    Html::a('<i class="far fa-check-circle fa-lg"></i>',
                                                            ['generate-quotation-pdf', 'revisionId' => $model->id, 'projQClientId' => $client->id],
                                                            ["class" => 'text-warning mx-1 float-right', 'title' => 'Re-generate quotation in pdf', 'data-confirm' => 'This will replace the existing quotation. Continue?'])
                                                    ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?=
                                            Html::a('<i class="far fa-file-pdf fa-lg"></i>',
                                                    ['read-pdf', 'id' => $theQuotation[0]->id],
                                                    ["class" => 'text-red mx-1 float-right', 'title' => 'View Quotation', 'target' => '_blank'])
                                            ?>

                                        <?php else: ?>
                                            <?php if (!$finalized): ?>
                                                <?=
                                                Html::a('<i class="far fa-check-circle fa-lg"></i>',
                                                        ['generate-quotation-pdf', 'revisionId' => $model->id, 'projQClientId' => $client->id],
                                                        ["class" => 'text-primary float-right', 'title' => 'Generate quotation in pdf']
                                                )
                                                ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>   
                                    <td class="text-center">
                                        <?php if ($theQuotation && $theQuotation[0]->md_approval_status == 0) { ?>
                                            <input type="checkbox" class="quotation-select-checkbox mx-2" value="<?= $theQuotation[0]->id ?>">
                                        <?php } else if ($theQuotation && $theQuotation[0]->md_approval_status == QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL) { ?>
                                            <span>Waiting for Director's Approval</span>
                                        <?php } else if ($theQuotation && $theQuotation[0]->md_approval_status == QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED) { ?>
                                            <span class="text-success">Approved by <?= $theQuotation[0]->mdUser->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($theQuotation[0]->md_approval_date) ?></span>
                                        <?php } ?>
                                    </td>   
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center text-muted"><em>No clients available</em></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <?=
                Html::a('Submit for Director Approval <i class="fas fa-signature me-2"></i>', 'javascript:validateAndGetApproval()',
                        ["class" => "btn btn-success float-right"])
                ?>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-6"></div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panels:</legend>
                <div class="form-check form-check-inline float-right pb-2">
                    <?php
                    if (!$finalized) {
                        ?>
                        <div class=" custom-control custom-checkbox float-right vmiddle mr-3">
                            <input type="checkbox" class="custom-control-input" id="allowSort" <?= $isDisabled ? 'disabled' : ''; ?>/>
                            <label class="custom-control-label" for="allowSort" >Allow Sort</label>
                        </div>  
                    <?php } ?>
                    <?php
                    echo Html::a(
                            'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
                            ['export-to-csv', 'revisionId' => $model->id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-primary mr-3'
                            ]
                    );
                    ?>
                    <?php
                    if (!$finalized) {
                        ?>
                        <?=
                        Html::a('Delete Selected Panel <i class="fas fa-trash"></i>', 'javascript:validateAndDeletePanel()',
                                ["class" => "btn btn-danger float-right " . ($isDisabled ? 'btn-muted' : '')])
                        ?>
                    <?php } ?>
                </div>

                <?php
                $panels = $model->projectQPanels;

                if ($panels) {
                    array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                    ?> 
                    <table class="table table-sm table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="tdnowrap text-center">Item</th>
                                <th>Panel's Type</th>
                                <th>Panel's Name</th>
                                <th class="tdnowrap text-center">Quantity</th>
                                <th class="text-right" >Unit Price (<?= $model->currency->currency_sign ?>)</th>
                                <th class="text-right" >Amount w/o Tax</th>
                                <th class="text-right">
                                    <div class="custom-control custom-checkbox">
                                        <?php
//                                        if (!$finalized) {
//                                            $companyCode = $model->projectQType->project->company_group_code;
//                                            $isTKproject = false;
//                                            if ($companyCode === "TK") {
//                                                $isTKproject = true;
//                                            }
                                        ?>
        <!--                                            <input type="checkbox" class="custom-control-input" id="withSST" <?php //= ($model->with_sst || $isTKproject) ? "Checked" : ""       ?>/>
                                            <label class="custom-control-label" for="withSST" >Tax</label>-->
                                        <?php
//                                        } else {
//                                            echo "Tax";
//                                        }
                                        ?>
                                        <?php
                                        if (!$finalized) {
//                                            $defaultUnCheckAndDisabled = ($company->code === "TKTKM" ? 'disabled' : '');
//                                            if ($defaultUnCheckAndDisabled) {
//                                                $model->with_sst = 0;
//                                            }
                                            // Disable checkbox if company is TKTKM
                                            $defaultDisabled = ($company->code === "TKTKM");
                                            if ($defaultDisabled) {
                                                $model->with_sst = 0;
                                            }

                                            // Determine if checkbox should be disabled
                                            $isDisable = $defaultDisabled || $isDisabled;
                                            ?>
                                            <input type="checkbox" class="custom-control-input" id="withSST" <?= ($model->with_sst) ? "Checked" : "" ?> <?= $isDisable ? 'disabled' : '' ?>/>
                                            <label class="custom-control-label" for="withSST" >Tax</label>
                                            <?php
                                        } else {
                                            echo "Tax";
                                        }
                                        ?>
                                    </div>
                                </th>
                                <th class="text-right" >
                                    Amount with Tax
                                </th>
                                <?php if (!$finalized) { ?>
                                    <th class="text-center tdnowrap">Action</th>
                                    <th class="text-center tdnowrap">Select All                 
                                        <br><input type="checkbox" id="selectAllPanels" class="mx-2" <?= $isDisabled ? 'disabled' : '' ?>>
                                    </th>

                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody id="itemDisplayTable">
                            <?php
                            foreach ($panels as $key => $panel) {
                                $amoutBeforeSST = $panel->amount * $panel->quantity;
                                $SSTAmount = $sst / 100 * $amoutBeforeSST;
                                $totalAmountBeforeSST += $amoutBeforeSST;
                                $totalSSTAmount += $SSTAmount;
                                $gotTaskAssignElec = $panel->projectProductionPanels;
                                $gotTaskAssignFab = $panel->projectProductionPanels;
                                ?>
                                <tr id="tr_<?= $panel->id ?>">
                                    <td class="text-right px-2"><?= $key + 1 ?></td>
                                    <td class="px-2 col-1 tdnowrap"><?= $panel->panelType->project_type_name ?? '' ?></td>
                                    <td style=""> 
                                        <?=
                                        Html::a($panel->panel_description,
                                                ['/projectqpanel/view-project-q-panel', 'id' => $panel->id, 'isDisabled' => $isDisabled],
                                                ['title' => 'View', 'class' => 'mx-1 text-primary no-text-deco'])
                                        ?>

                                        <?= $panel->remark ? ("<br/><span class='font-weight-light'>" . nl2br(Html::encode($panel->remark)) . "</span>") : "" ?>       
                                    </td>
                                    <td class="text-right px-3 tdnowrap">
                                        <?php //= MyFormatter::asDecimal2($panel->quantity) . " " . $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "")      ?>
                                        <?= MyFormatter::asDecimal2($panel->quantity) . " " . (($panel->unitCode && $panel->unitCode->unit_name) ? $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") : "") ?>
                                    </td>
                                    <td class="text-right px-2 tdnowrap"><?= MyFormatter::asDecimal2($panel->amount ?? 0) ?></td>
                                    <td class="text-right px-2 tdnowrap"><?= MyFormatter::asDecimal2($amoutBeforeSST) ?></td>
                                    <td class="text-right px-2 <?= $model->with_sst ? "" : "bg-secondary" ?> isColSST tdnowrap" ><?= MyFormatter::asDecimal2($SSTAmount) ?></td>
                                    <td class="text-right px-2 <?= $model->with_sst ? "" : "bg-secondary" ?> isColSST tdnowrap"><?= MyFormatter::asDecimal2($amoutBeforeSST + $SSTAmount) ?></td>
                                    <?php if (!$finalized) { ?>
                                        <td class="text-center tdnowrap">
                                            <?=
                                            Html::a(
                                                    '<i class="far fa-clone fa-lg"></i>',
                                                    '#',
                                                    ['title' => 'Clone', 'class' => 'mx-1 text-success ' . ($isDisabled ? 'disabled-link' : ''),
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modalClonePanel',
                                                        'data-panelname' => $panel->panel_description,
                                                        'data-motherpanelid' => $panel->id]
                                            )
                                            ?>

                                            <?php
                                            echo Html::a("<i class='far fa-edit fa-lg'></i>",
                                                    'javascript:void(0)',
                                                    [
                                                        'value' => '/projectqpanel/update?panelId=' . $panel->id,
                                                        'title' => 'Edit',
                                                        'class' => 'text-success mx-1 editPanelBtn ' . ($isDisabled ? 'disabled-link' : ''),]);
                                            ?>

                                        </td>
                                        <td class="text-center tdnowrap">
                                            <?php if (!$gotTaskAssignElec || !$gotTaskAssignFab) { ?>
                                                <input type="checkbox" class="panel-select-checkbox mx-2" <?= $isDisabled ? 'disabled' : '' ?> value="<?= $panel->id ?>" data-panel-name="<?= Html::encode($panel->panel_description) ?>">
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php
                            }
                            ?>        
                        </tbody>
                    </table>

                    <?php
                } else {
                    echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                }
                if (!$finalized) {
                    ?>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <?php
                                if (!$finalized) {
                                    echo Html::a(
                                            'New Panel <i class="fas fa-plus"></i>',
                                            'javascript:void(0)',
                                            ['value' => 'new-panel?revisionId=' . $model->id,
                                                'id' => 'newPanelBtn',
                                                'title' => 'Create new panel',
                                                'class' => 'btn btn-success mb-2 mt-0 ' . ($isDisabled ? 'btn-muted' : '')]);
                                    echo Html::a(
                                            'Upload Excel <i class="fas fa-upload"></i>',
                                            ['upload-template', 'revisionid' => $model->id],
                                            [
                                                'title' => 'Upload Excel Template',
                                                'class' => 'btn btn-success mb-2 mt-0 ml-2 ' . ($isDisabled ? 'btn-muted' : '')]);
                                }
                                ?>
                            </div>
                            <div class="col text-right">

                                <?php
                                echo Html::a("Edit Discount <i class='far fa-edit'></i>",
                                        'javascript:void(0)',
                                        ['class' => 'btn btn-success mx-2 ' . ($isDisabled ? 'btn-muted' : ''),
                                            'title' => "Edit",
                                            'data' => [
                                                'toggle' => 'modal',
                                                'target' => '#modalEditDiscount',
                                            ],
                                ]);

                                if ($model->discount_amt > 0) {
                                    echo "Discount: " . ($model->discount_type == 0 ? ($model->currency->currency_sign . " ") : "") . MyFormatter::asDecimal2($model->discount_amt) . ($model->discount_type == 1 ? " %" : "");
                                } else {
                                    echo " -- No Discount --";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </fieldset>
        </div>
    </div>
</div>
<!--Clone Panel-->
<div class="modal fade" id="modalClonePanel" tabindex="-1" role="dialog" aria-labelledby="modalClonePanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off'],
                'action' => 'clone-panel-same-revision'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalClonePanelLabel">Cloning Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless"> 
                    <tbody>
                        <tr>
                            <td class="tdnowrap">Clone from panel</td>
                            <td> : </td>
                            <td id="modal-cloneFromPanelName"></td>
                        </tr>
                        <tr>
                            <td class="req tdnowrap">New panel name</td>
                            <td> : </td>
                            <td>
                                <?= Html::hiddenInput('motherPanelId', '', ['id' => 'modal-motherPanelId']) ?>
                                <?= Html::textInput('clonePanelNewName', '', ['id' => 'clonePanelNewName', 'class' => 'form-control', 'required' => true]) ?>
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

<!--New Panel-->
<div class="modal fade" id="modalNewPanel" tabindex="-1" role="dialog" aria-labelledby="modalNewPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNewPanelLabel">New Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class='modelContents'>

            </div>
        </div>
    </div>
</div>

<!--Update Panel-->
<div class="modal fade" id="modalUpdateProjectQPanel" tabindex="-1" role="dialog" aria-labelledby="modalUpdateProjectQPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateProjectQPanelLabel">Update Panel Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class='modelContents'>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSetTemplate" tabindex="-1" role="dialog" aria-labelledby="modalSetTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form4 = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
                'action' => '/projectqrevision/set-revision-as-template'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalSetTemplateLabel">Set revision as template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form4->field($model, 'id', ['options' => ['class' => 'hidden']])->hiddenInput()->label(false) ?>
                <?= $form4->field($model, 'revision_description')->textInput(['value' => ''])->label("Template Name") ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create Template <i class="fas fa-check"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDiscount" tabindex="-1" role="dialog" aria-labelledby="modalEditDiscountLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <?php
            $form5 = ActiveForm::begin([
                'options' => ['autocomplete' => 'off'],
                'action' => '/projectqrevision/update-revision-discount'
            ]);
            ?>

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditDiscountLabel">Edit Discount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form5->field($model, 'id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
                <div class="row">
                    <div class="col-xl-12">
                        <?= $form5->field($model, 'discount_amt')->textInput(['class' => 'text-right form-control', 'type' => 'number', 'step' => '0.01'])->label(false) ?>
                    </div>
                    <div class="col-xl-12">
                        <?= $form5->field($model, 'discount_type')->dropDownList(['0' => 'Amount ' . ($model->currency->currency_sign), '1' => 'Percentage (%)'])->label(false) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('openSentUrl')): ?>
    <script>
        window.open("<?= Yii::$app->session->getFlash('openSentUrl') ?>", "_blank");
    </script>
<?php endif; ?>
<script>
    $('#selectAllQuotations').on('change', function () {
        $('.quotation-select-checkbox').prop('checked', this.checked);
    });

    $('.quotation-select-checkbox').on('change', function () {
        let allChecked = $('.quotation-select-checkbox').length === $('.quotation-select-checkbox:checked').length;
        $('#selectAllQuotations').prop('checked', allChecked);
    });

    function validateAndGetApproval() {
        let checkedQuotations = [];

        $(".quotation-select-checkbox:checked").each(function () {
            checkedQuotations.push($(this).val());
        });

        if (checkedQuotations.length <= 0) {
            myAlert("No quotation is selected");
        } else if (confirm("Submit for Managing Director approval?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['get-approval-quotation-ajax', 'id' => $model->id]) ?>',
                type: 'POST',
                data: {
                    quotationIds: checkedQuotations
                },
                success: function (response) {
                    console.log('Success response:', response);
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Ajax error:', error);
                    myAlert("Error submitting quotations");
                }
            });
        }
    }

    // Select/Deselect all panels
    $('#selectAllPanels').on('change', function () {
        $('.panel-select-checkbox').prop('checked', this.checked);
    });

// Auto-update "Select All" checkbox if user unchecks any panel
    $('.panel-select-checkbox').on('change', function () {
        let allChecked = $('.panel-select-checkbox').length === $('.panel-select-checkbox:checked').length;
        $('#selectAllPanels').prop('checked', allChecked);
    });


    $(function () {
        $('#modalClonePanel').on('show.bs.modal', function (event) {
            $("#clonePanelNewName").val('');
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-cloneFromPanelName').text(button.data('panelname'));
            modal.find('#modal-motherPanelId').val(button.data('motherpanelid'));
        });

        $("#newPanelBtn").click(function () {
            $("#modalNewPanel").modal("show")
                    .find(".modelContents")
                    .load($(this).attr('value'));
        });

        $(".editPanelBtn").click(function () {
            $("#modalUpdateProjectQPanel").modal("show")
                    .find(".modelContents")
                    .load($(this).attr('value'));
        });

        $("#allowSort").click(function () {
            if ($('#allowSort').is(":checked")) {
                sortableEnable();
            } else {
                sortableDisable();
            }
        });

//        $(document).ready(function () {
//            // Only run if checkbox exists (when not finalized)
//            if ($("#withSST").length > 0) {
//                var initialState = $("#withSST").is(':checked');
//                console.log("SST initially set to:", initialState);
//
//                // Apply initial styling and control
//                if (initialState) {
//                    $(".isColSST").removeClass('bg-secondary');
//                    controlSST(<?= $model->id ?>, 1);
//                } else {
//                    $(".isColSST").addClass('bg-secondary');
//                    controlSST(<?= $model->id ?>, 0);
//                }
//            }
//        });
//
//        $("#withSST").click(function () {
//            var isChecked = $(this).is(':checked');
//            console.log("SST checkbox toggled to:", isChecked);
//
//            if (isChecked) {
//                $(".isColSST").removeClass('bg-secondary');
//                controlSST(<?= $model->id ?>, 1);
//            } else {
//                $(".isColSST").addClass('bg-secondary');
//                controlSST(<?= $model->id ?>, 0);
//            }
//        });
//
//        function getCurrentSSTState() {
//            if ($("#withSST").length > 0) {
//                return $("#withSST").is(':checked');
//            }
//            return null;
//        }

        $("#withSST").click(function () {
            if ($('#withSST').is(":checked")) {
                $(".isColSST").removeClass('bg-secondary');
                controlSST(<?= $model->id ?>, 1);
            } else {
                $(".isColSST").addClass('bg-secondary');
                controlSST(<?= $model->id ?>, 0);

            }
        });

        $(document).on('beforeSubmit', 'form', function (event) {
            var currentYOffset = window.pageYOffset;  // save current page postion.
            setCookie('jumpToScrollPostion', currentYOffset, 2);
        });

        // check if we should jump to postion.
        var jumpTo = getCookie('jumpToScrollPostion');
        if (jumpTo !== "undefined" && jumpTo !== null) {
            window.scrollTo(0, jumpTo);
            eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
        }
    });

    function validateAndDeletePanel() {
        let checkedPanels = [];

        $(".panel-select-checkbox:checked").each(function () {
            checkedPanels.push($(this).val());
        });

        if (checkedPanels.length <= 0) {
            myAlert("No panel is selected");
        } else if (confirm("Delete selected panels?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['remove-panel-ajax', 'id' => $model->id]) ?>',
                type: 'POST',
                data: {
                    panelIds: checkedPanels
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    myAlert("Error deleting panels");
                }
            });
        }
    }


    function sortableEnable() {
        $("#itemDisplayTable").sortable({
            disabled: false,
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
            url: "sort-panels-ajax",
            dataType: "json",
            data: {
                revisionId: '<?= $model->id ?>',
                moveId: moveId,
                previousId: previousId
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-panel')) {
            e.preventDefault();
            const link = e.target.closest('.remove-panel');

            if (confirm("Are you sure to remove?")) {
                const panelId = link.getAttribute('data-panel-id');
                removePanel(panelId);
            }
        }
    });

//    function removePanel(panelId) {
//        $.ajax({
//            type: "POST",
//            url: "remove-panel-ajax",
//            dataType: "json",
//            data: {
//                panelId: panelId
//            },
//            success: function (data) {
//                if (data.success) {
//                    reloadRevisionAmount();
//                    $("#tr_" + panelId).remove();
//                } else {
//                    myAlert("Fail to remove. Contact IT Support");
//                }
//            }
//        });
//    }

    function controlSST(revisionId, enableFlag) {
        $.ajax({
            type: "POST",
            url: "control-sst-ajax",
            dataType: "json",
            data: {
                revisionId: revisionId,
                enableFlag: enableFlag
            },
            success: function (data) {
                if (data.success) {
                    reloadRevisionAmount();
                } else {
                    console.log("Fail to remove. Contact IT Support");
//                    myAlert("Fail to remove. Contact IT Support");
                }
            }
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
                $("#revisionAmountDisplay").html(data.amount);
            }
        });
    }

    function addClient() {
        if ($("#clientId").val() === "") {
            myAlert("Please select a client");
            $("#addClientAutocomplete").focus();
            return;
        }
        $.ajax({
            type: "POST",
            url: "/projectquotation/add-client-ajax",
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

//    function insertClientRow(projQClientId) {
//        var wrapper = $("#clientLists");
//        var clientName = $("#addClientAutocomplete").val();
//
//        $(wrapper).append("<li class='list-group-item p-2' id='client_" + projQClientId + "'>"
//                + clientName
//                + "<a class='float-right text-primary' href='/projectqrevision/generate-quotation-pdf?revisionId=<?= $model->id ?>&projQClientId=" + projQClientId + "' title='Generate quotation in pdf'><i class='far fa-check-circle fa-lg'></i></a>"
//                + "</li>");
//
//    }
    function insertClientRow(projQClientId) {
        var clientName = $("#addClientAutocomplete").val();
        var newRow = "<tr id='client_" + projQClientId + "'>" +
                "<td>" + clientName +
                "<a class='float-right text-primary' href='/projectqrevision/generate-quotation-pdf?revisionId=<?= $model->id ?>&projQClientId=" + projQClientId + "' title='Generate quotation in pdf'>" +
                "<i class='far fa-check-circle fa-lg'></i>" +
                "</a>" +
                "</td>" +
                "</tr>";

        $("#clientTable tbody").append(newRow); // Assuming your table has id="clientTable"
    }
</script>