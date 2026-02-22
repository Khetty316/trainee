<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefGeneralReferences;
use frontend\models\common\RefProjectQShippingMode;
use yii\helpers\Url;

$this->title = $revision->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $revision->projectQType->project->quotation_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $revision->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $revision->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $revision->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/projectqrevision/view-project-q-revision', 'id' => $revision->id]];
$this->params['breadcrumbs'][] = "Release Pdf";

$projectQMaster = $revision->projectQType->project;
$sst = RefGeneralReferences::getValue("sst_value")->value;
$company = $revision->projectQType->project->companyGroupCode;
$conSst = $company->sst_value;
$sstAmount = 0;
?>
<style>
    formHeader td{
        padding: 0px;
        margin: 0px;
        text-align: center;
    }
</style>
<div class="project-qrevisions-update">
    <div class="project-qrevisions-form">

        <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "{input}",
                'horizontalCssClasses' => [
                ],
            ],
            'options' => ['autocomplete' => 'off'],
            'id' => 'form_receiveAsset',
        ]);
        ?>
        <?= $form->field($model, 'revision_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'project_q_client_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'currency_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'discount_type', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'discount_amt', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>

        <div class="form-row">
            <div class='col-lg-10 col-sm-12'>
                <table class='table table-sm table-borderless' id='formHeader'>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="3" class='font-weight-bolder' style='font-size: 15pt'>Quotation</td>
                    </tr>
                    <tr>
                        <td>To</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'to_company', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm'])->label(false) ?>
                        </td>
                        <td>From</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'q_from', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'value' => Yii::$app->user->identity->fullname]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Attn</td><td>:</td>
                        <td>
                            <?php $names = frontend\models\client\Clients::getAttns($client->id); ?>
                            <?php if ($names): ?>
                                <?=
                                $form->field($model, 'to_pic', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-name',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'value' => $names[0],
                                    'list' => 'namesList'
                                ])
                                ?>
                            <?php else: ?>
                                <?=
                                $form->field($model, 'to_pic', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-name',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'list' => 'namesList'
                                ])->label(false)
                                ?>
                            <?php endif; ?>
                            <datalist id="namesList">
                                <?php
                                foreach ($names as $name) {
                                    echo "<option value='{$name}'>";
                                }
                                ?>
                            </datalist>
                        </td>
                        <td>Our Ref</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'quotation_no', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'readonly' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tel. No</td><td>:</td>
                        <td>
                            <?php $telNos = frontend\models\client\Clients::getTelNos($client->id); ?>
                            <?php if ($telNos): ?>
                                <?=
                                $form->field($model, 'to_tel_no', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-contact',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'value' => $telNos[0],
                                    'list' => 'telNoList'
                                ])
                                ?>
                            <?php else: ?>
                                <?=
                                $form->field($model, 'to_tel_no', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-contact',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'list' => 'telNoList'
                                ])->label(false)
                                ?>
                            <?php endif; ?>
                            <datalist id="telNoList">
                                <?php
                                foreach ($telNos as $telNo) {
                                    echo "<option value='{$telNo}'>";
                                }
                                ?>
                            </datalist>
                        </td>
                        <td>Your Ref</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'q_your_ref', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Fax No</td><td>:</td>
                        <td>
                            <?php $faxes = frontend\models\client\Clients::getFaxes($client->id); ?>
                            <?php if ($faxes): ?>
                                <?=
                                $form->field($model, 'to_fax_no', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-fax',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'value' => $faxes[0],
                                    'list' => 'faxList'
                                ])
                                ?>
                            <?php else: ?>
                                <?=
                                $form->field($model, 'to_fax_no', ['options' => ['class' => 'p-0 m-0']])->textInput([
                                    'id' => 'client-fax',
                                    'maxlength' => true,
                                    'class' => 'form-control form-control-sm',
                                    'list' => 'faxList'
                                ])->label(false)
                                ?>
                            <?php endif; ?>
                            <datalist id="faxList">
                                <?php
                                foreach ($faxes as $fax) {
                                    echo "<option value='{$fax}'>";
                                }
                                ?>
                            </datalist>
                        </td>
                        <td>Date</td><td>:</td>
                        <td>
                            <?php
                            echo $form->field($model, 'q_date', ['options' => ['class' => 'p-0 m-0']])
                                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control form-control-sm'], 'dateFormat' => 'dd/MM/yyyy'])
                            ?>
                        </td>
                    </tr>
                    <tr style='font-weight: bold;'>
                        <td>RE</td><td>:</td>       
                        <td colspan='4'>
                            <?= $form->field($model, 'proj_title', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'value' => $projectQMaster->project_name])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='6'>
                            <p>Further to your kind invitation, we are pleased to append herewith our quotation as follows :</p>
                            <?php
                            $panels = $revision->projectQPanels;
                            array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                            $totalAmount = 0;
                            $totalSST = 0;
                            $totalAmtWithSST = 0;
                            $totalAmtWithoutSST = 0;

                            $totalConsolidateUnit = 0;
                            $totalConTax = 0;
                            $totalConsolidateAmount = 0;
                            $totalConAmountWTax = 0;
                            $totalConAmountNoTax = 0;
                            $totalTKUnit = 0;
                            $totalTKTax = 0;
                            $totalTKAmountWTax = 0;
                            $totalTKAmountNoTax = 0;
                            $totalTKMUnit = 0;
                            $totalTKMAmount = 0;

                            if ($panels) {
                                $isTKTKM = ($company->code === "TKTKM");
                                if ($isTKTKM) {
//                                    echo Html::hiddenInput(Html::getInputName($model, 'with_sst'), 1);
                                    $model->with_sst = 1;
                                    echo $form->field($model, 'with_sst', ['options' => ['class' => 'float-right']])
                                            ->checkbox()
                                            ->label("Include TK Sales Tax");
                                } else {
                                    echo $form->field($model, 'with_sst', ['options' => ['class' => 'float-right']])->checkbox()->label("Include Sales Tax");
                                }
                                ?>
                                <?php //= $form->field($model, 'with_sst', ['options' => ['class' => 'float-right']])->checkbox()->label("Include Sales Tax")  ?>  
                                <?= $form->field($model, 'show_breakdown_price', ['options' => ['class' => 'mr-4 float-right']])->checkbox() ?>  
                                <?= $form->field($model, 'show_breakdown', ['options' => ['class' => 'mr-4 float-right']])->checkbox() ?>  
                                <?php
                                $model->show_panel_description = true; // Default to true, requested by customer 
                                echo $form->field($model, 'show_panel_description', ['options' => ['class' => 'mr-4 float-right']])->checkbox();
                                ?>  
                                <table class='table table-sm'>
                                    <thead>
                                        <?php if ($isTKTKM): ?>
                                            <tr class='text-center'>
                                                <th rowspan="2" class="tdnowrap b px-3 vmiddle">Item</th>
                                                <th rowspan="2" class="b px-3 vmiddle">Description</th>
                                                <th rowspan="2" class="tdnowrap b px-3 vmiddle">Quantity</th>
                                                <th rowspan="2" class="tdnowrap b px-3 vmiddle">UOM</th>
                                                <th colspan="2" class="b px-3 vtop tk-header text-center">CONSOLIDATE</th>
                                                <th colspan="3" class="b px-3 vtop tk-header text-center">TK</th>
                                                <th colspan="2" class="b px-3 vtop tkm-header text-center">TKM</th>
                                            </tr>
                                            <tr class='text-center'>
                                                <th class='tdnowrap b px-3 vtop'>Unit Price<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colSST vtop'>Unit Tax<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colNoSST vtop'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colSST vtop'>Total Amount<br/>w Tax (<?= $model['currency']['currency_sign'] ?>)</th>

                                                <th class='tdnowrap b px-3 vtop'>Unit Price<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colSST vtop'>Unit Tax<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colNoSST vtop'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 tk-tax-colSST vtop'>Total Amount<br/>w Tax (<?= $model['currency']['currency_sign'] ?>)</th>

                                                <th class='tdnowrap b px-3 vtop'>Unit Price<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 vtop'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                            </tr>
                                        <?php else: ?>
                                            <tr class='text-center'>
                                                <th class='tdnowrap b px-3 vmiddle'>Item</th>
                                                <th class='b px-3 vmiddle'>Description</th>
                                                <th class='tdnowrap b px-3 vmiddle'>Quantity</th>
                                                <th class='tdnowrap b px-3 vtop'>UOM</th>
                                                <th class='tdnowrap b px-3 vtop'>Unit Price<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 colSST vtop'>Unit Tax<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 colNoSST vtop'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                                <th class='tdnowrap b px-3 colSST vtop'>Total Amount<br/>w Tax (<?= $model['currency']['currency_sign'] ?>)</th>
                                            </tr>
                                        <?php endif; ?>
                                    </thead>

                                    <tbody class='b'>
                                        <?php if ($isTKTKM): ?>
                                            <?php
                                            foreach ($panels as $key => $panel):
                                                $_unitAmt = $panel->amount ?? 0;
                                                $_totAmt_con = ($_unitAmt * $panel->quantity);
                                                $totalAmount += $_totAmt_con;

                                                $_sstAmtCon = ($_unitAmt * $conSst / 100);
                                                $_totSstAmt_con = ($_sstAmtCon * $panel->quantity + ($_unitAmt * $panel->quantity));

                                                $by_item_price_tk = $_unitAmt / 2;
                                                $by_item_price_tkm = $_unitAmt / 2;

                                                $_sstAmtTk = ($by_item_price_tk * $sst / 100);
                                                $_totSstAmt_tk = ($_sstAmtTk * $panel->quantity + ($by_item_price_tk * $panel->quantity));
                                                $_totAmt_tk = ($by_item_price_tk * $panel->quantity);

                                                $_totAmt_tkm = ($by_item_price_tkm * $panel->quantity);

                                                // Consolidate totals
                                                $totalConsolidateUnit += $_unitAmt;
                                                $totalConTax += $_sstAmtCon * $panel->quantity; // total tax for all qty
                                                $totalConAmountWTax += $_totSstAmt_con; // total with SST 
                                                $totalConAmountNoTax += $_totAmt_con; // total without SST 
                                                // TK totals
                                                $totalTKUnit += $by_item_price_tk;
                                                $totalTKTax += $_sstAmtTk * $panel->quantity; // total tax for all qty
                                                $totalTKAmountWTax += $_totSstAmt_tk; // total with SST 
                                                $totalTKAmountNoTax += $_totAmt_tk; // total without SST 
                                                // TKM totals
                                                $totalTKMUnit += $by_item_price_tkm;
                                                $totalTKMAmount += $_totAmt_tkm;
                                                ?>
                                                <tr>
                                                    <td class='px-3 bl'><?= $key + 1 ?></td>
                                                    <td class='br bl px-3'><?= $panel->panel_description ?></td>
                                                    <td class='text-right tdnowrap px-3 br bl'><?= MyFormatter::asDecimal2($panel->quantity) ?></td>
                                                    <td class='text-right tdnowrap px-3 br bl'><?= $panel->unitCode->unit_name ?></td>

                                                    <!-- Consolidate -->
                                                    <td class='text-right br bl px-3'><span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_unitAmt) ?>
                                                        </span></td>
                                                    <td class='text-right br bl px-3 tk-tax-colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_sstAmtCon) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 tk-tax-colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totSstAmt_con) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 tk-tax-colNoSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totAmt_con) ?>
                                                        </span>
                                                    </td>

                                                    <!-- TK -->
                                                    <td class='text-right br bl px-3'><span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($by_item_price_tk) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 tk-tax-colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_sstAmtTk) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 tk-tax-colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totSstAmt_tk) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 tk-tax-colNoSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totAmt_tk) ?>
                                                        </span>
                                                    </td>

                                                    <!-- TKM -->
                                                    <td class='text-right br bl px-3'><span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($by_item_price_tkm) ?>
                                                        </span></td>
                                                    <td class='text-right br bl px-3'><span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totAmt_tkm) ?>
                                                        </span></td>
                                                </tr>
                                                <?php
                                                $items = $panel->projectQPanelItems;
                                                array_multisort(array_column($items, 'sort'), SORT_ASC, $items);
                                                $ii = 'a';
                                                foreach ($items as $key2 => $item) {
                                                    ?>
                                                    <tr class="breakdownItem">
                                                        <td class="px-3 bl"><span class="pl-3">(<?= ($ii++) ?>)</span></td>
                                                        <td class="br bl px-4"><?= nl2br(Html::encode($item['item_description'])) ?></td>

                                                        <?php if ($panel->by_item_price): ?>
                                                            <td class='text-right tdnowrap px-3 br bl'>
                                                                <span class="bdItemPrice"><?= number_format($tempItemQty = $item['quantity'] * $panel->quantity, 2) ?></span>
                                                            </td>
                                                            <td class='text-right tdnowrap px-3 br bl'>
                                                                <span class="bdItemPrice"><?= ($item['unitCode']['unit_name']) . ($tempItemQty > 1 ? "S" : "") ?></span>
                                                            </td>

                                                            <!-- Consolidate -->
                                                            <td class='text-right br bl px-3'>
                                                                <span class="bdItemPrice"><?= number_format($item['amount'], 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colSST'>
                                                                <span class="bdItemPrice"><?= number_format($item['amount'] * $conSst / 100, 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colSST'>
                                                                <span class="bdItemPrice"><?= number_format(($item['amount'] * $tempItemQty) + (($item['amount'] * $conSst / 100) * $tempItemQty), 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colNoSST'>
                                                                <span class="bdItemPrice"><?= number_format(($item['amount'] * $tempItemQty), 2) ?></span>
                                                            </td>

                                                            <!-- TK -->
                                                            <?php
                                                            $by_item_price_tk = $item['amount'] / 2;
                                                            $_sstAmtTk = ($by_item_price_tk * $sst / 100);
                                                            $_totAmt_tk = ($by_item_price_tk * $tempItemQty);
                                                            $_totAmtWithTax_tk = $_totAmt_tk + ($_sstAmtTk * $tempItemQty);
                                                            ?>
                                                            <td class='text-right br bl px-3'>
                                                                <span class="bdItemPrice"><?= number_format($by_item_price_tk, 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colSST'>
                                                                <span class="bdItemPrice"><?= number_format($_sstAmtTk, 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colSST'>
                                                                <span class="bdItemPrice"><?= number_format($_totAmtWithTax_tk, 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3 tk-tax-colNoSST'>
                                                                <span class="bdItemPrice"><?= number_format($_totAmt_tk, 2) ?></span>
                                                            </td>

                                                            <!-- TKM -->
                                                            <?php
                                                            $by_item_price_tkm = $item['amount'] / 2;
                                                            $_totAmt_tkm = ($by_item_price_tkm * $tempItemQty);
                                                            ?>
                                                            <td class='text-right br bl px-3'>
                                                                <span class="bdItemPrice"><?= number_format($by_item_price_tkm, 2) ?></span>
                                                            </td>
                                                            <td class='text-right br bl px-3'>
                                                                <span class="bdItemPrice"><?= number_format($_totAmt_tkm, 2) ?></span>
                                                            </td>

                                                        <?php else: ?>
                                                            <!-- empty columns for non-by-item panels -->
                                                            <td class='text-right tdnowrap px-3 br bl'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colSST'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colSST'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colNoSST'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colSST'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colSST'></td>
                                                            <td class='br bl text-right px-3 tk-tax-colNoSST'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                        <?php endif; ?>
                                                    </tr>

                                                    <?php
                                                }
                                                if (!empty($panel->remark)) {
                                                    ?>
                                                    <tr class="panelDescription">
                                                        <td class="bl"></td>
                                                        <td class="br bl px-4"><?= nl2br(Html::encode($panel->remark)) ?></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl tk-tax-colSST'></td>
                                                        <td class='br bl tk-tax-colNoSST'></td>
                                                        <td class='br bl tk-tax-colSST'></td>
                                                    </tr>
                                                <?php }
                                                ?>
                                            <?php endforeach; ?>

                                        <?php else: ?>
                                            <?php
                                            foreach ($panels as $key => $panel) {
                                                $_unitAmt = $panel->amount ?? 0;
                                                $_totAmt = ($_unitAmt * $panel->quantity);
                                                $_sstAmt = ($_unitAmt * $sst / 100);
                                                $_totAmtWSst = ($_sstAmt * $panel->quantity + $_totAmt);
                                                $totalAmount += $_totAmt;
                                                ?>
                                                <tr>
                                                    <td class='px-3 bl'><?= $key + 1 ?></td>
                                                    <td class='br bl px-3'>
                                                        <?= $panel->panel_description ?>
                                                    </td>
                                                    <td class='text-right tdnowrap px-3 br bl'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($panel->quantity) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right tdnowrap px-3 br bl'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3'>      
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_unitAmt) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_sstAmt) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 colNoSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totAmt) ?>
                                                        </span>
                                                    </td>
                                                    <td class='text-right br bl px-3 colSST'>
                                                        <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                            <?= MyFormatter::asDecimal2($_totAmtWSst) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php
                                                $items = $panel->projectQPanelItems;
                                                array_multisort(array_column($items, 'sort'), SORT_ASC, $items);
                                                $ii = 'a';
                                                foreach ($items as $key2 => $item) {
                                                    ?>
                                                    <tr class="breakdownItem">
                                                        <td class="px-3 bl"><span class="pl-3">(<?= ($ii++) ?>)</span></td>
                                                        <td class="br bl px-4"><?= nl2br(Html::encode($item['item_description'])) ?></td>
                                                        <?php
                                                        if ($panel->by_item_price) {
                                                            ?>
                                                            <td class='text-right tdnowrap px-3 br bl'>
                                                                <span class="bdItemPrice"><?= number_format($tempItemQty = $item['quantity'] * $panel->quantity, 2) ?></span>
                                                            </td>
                                                            <td class='text-right tdnowrap px-3 br bl'>
                                                                <span class="bdItemPrice"><?= ($item['unitCode']['unit_name']) . ($tempItemQty > 1 ? "S" : "") ?></span>
                                                            </td>
                                                            <td class='br bl text-right px-3'>
                                                                <span class="bdItemPrice"><?= number_format($item['amount'], 2) ?></span>
                                                            </td>
                                                            <td class='br bl text-right px-3  colSST'>
                                                                <span class="bdItemPrice">
                                                                    <?= number_format(($itemSstAmt = ($item['amount'] * $sst / 100)), 2) ?>
                                                                </span>
                                                            </td>
                                                            <td class='br bl px-3 text-right colNoSST'>   
                                                                <span class="bdItemPrice">
                                                                    <?= number_format(($item['amount'] * $tempItemQty), 2) ?>
                                                                </span> 
                                                            </td>
                                                            <td class='br bl px-3 text-right colSST'>    
                                                                <span class="bdItemPrice">
                                                                    <?= number_format((($itemSstAmt + $item['amount']) * $tempItemQty), 2) ?>
                                                                </span>
                                                            </td>

                                                            <?php
                                                        } else {
                                                            ?>
                                                            <td class='text-right tdnowrap px-3 br bl'></td>
                                                            <td class='br bl text-right px-3'></td>
                                                            <td class='br bl text-right colSST'></td>
                                                            <td class='br bl colNoSST'></td>
                                                            <td class='br bl colSST'></td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php
                                                }
                                                if (!empty($panel->remark)) {
                                                    ?>
                                                    <tr class="panelDescription">
                                                        <td class="bl"></td>
                                                        <td class="br bl px-4"><?= nl2br(Html::encode($panel->remark)) ?></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl'></td>
                                                        <td class='br bl colSST'></td>
                                                        <td class='br bl colNoSST'></td>
                                                        <td class='br bl colSST'></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        <?php endif; ?>
                                    </tbody>

                                    <tfoot>
                                        <?php if ($isTKTKM): ?>
                                            <tr class="b" style="font-weight:bold; text-align:right;">
                                                <td colspan="4" style="text-align:right; padding-right:1em;">
                                                    Total (RM):
                                                </td>
                                                <td></td>
                                                <td class="tk-tax-colSST"></td>
                                                <td class="tk-tax-colSST"><?= MyFormatter::asDecimal2($totalConAmountWTax) ?></td>
                                                <td class="tk-tax-colNoSST"><?= MyFormatter::asDecimal2($totalConAmountNoTax) ?></td>
                                                <td></td>
                                                <td class="tk-tax-colSST"></td>
                                                <td class="tk-tax-colSST"><?= MyFormatter::asDecimal2($totalTKAmountWTax) ?></td>
                                                <td class="tk-tax-colNoSST"><?= MyFormatter::asDecimal2($totalTKAmountNoTax) ?></td>
                                                <td></td>
                                                <td><?= MyFormatter::asDecimal2($totalTKMAmount) ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr class='colSST b' style='font-style: italic;text-align: right ;font-weight: bold'>
                                                <td colspan='6' style='text-align: right;padding-right: 1em'>
                                                    <?php
                                                    $discountTotal = 0;
                                                    $discountDisplay = "";

                                                    if ($model['discount_amt'] > 0) {
                                                        if ($model['discount_type'] == 0) {
                                                            $discountDisplay = "Discount (" . $model['currency']['currency_sign'] . ") :<br/>";
                                                            $discountTotal = $model['discount_amt'] * -1;
                                                        } else {
                                                            $discountDisplay = "Discount " . $model['discount_amt'] . "% (" . $model['currency']['currency_sign'] . ") :<br/>";
                                                            $discountTotal = $totalAmount * $model['discount_amt'] / 100 * -1;
                                                        }
                                                    }

                                                    $totalSST = ($totalAmount + $discountTotal) * $sst / 100;
                                                    $totalAmtWithSST = ($totalAmount + $discountTotal) + $totalSST;
                                                    $totalAmtWithoutSST = ($totalAmount + $discountTotal);
                                                    ?>

                                                    Sub-Total (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                    <?= $model['discount_amt'] > 0 ? $discountDisplay : "" ?>
                                                    Tax (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                    Total (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                </td>
                                                <td style='padding-right: 1em'>
                                                    <?= MyFormatter::asDecimal2($totalAmount) ?><br/>
                                                    <?php
                                                    if ($model['discount_amt'] > 0) {
                                                        echo MyFormatter::asDecimal2($discountTotal) . "<br/>";
                                                    }
                                                    ?>
                                                    <?= MyFormatter::asDecimal2($totalSST) ?><br/>
                                                    <?= MyFormatter::asDecimal2($totalAmtWithSST) ?><br/>

                                                </td>
                                            </tr>
                                            <tr class='colNoSST b' style='font-style: italic;text-align: right ;font-weight: bold'>
                                                <td colspan='5' style='text-align: right;padding-right: 1em'>
                                                    <?= $model['discount_amt'] > 0 ? "Sub-Total" : "Total" ?> (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                    <?php
                                                    if ($model['discount_amt'] > 0) {
                                                        echo $discountDisplay;
                                                        echo "Total (" . $model['currency']['currency_sign'] . ") : .<br/>";
                                                    }
                                                    ?>

                                                </td>
                                                <td style='padding-right: 1em'>
                                                    <?= MyFormatter::asDecimal2($totalAmount) ?><br/>
                                                    <?php
                                                    if ($model['discount_amt'] > 0) {
                                                        echo MyFormatter::asDecimal2($discountTotal) . "<br/>";
                                                        echo MyFormatter::asDecimal2($totalAmtWithoutSST) . "<br/>";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tfoot>
                                </table>

                            <?php } ?>
                        </td>
                    </tr>
                </table>
                <table class='table table-sm table-borderless'>
                    <tr style='font-weight: bold; text-decoration: underline'>
                        <td style='width:50%'>Material Offered</td>
                        <td style='width:50%'>Switchboard Standard</td>
                    </tr>
                    <tr>
                        <td>
                            <?= $form->field($model, 'q_material_offered')->textarea(['rows' => 6]) ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'q_switchboard_standard')->textarea(['rows' => 6]) ?>
                        </td>
                    </tr>
                </table>
                <table class='table table-sm table-borderless'>
                    <tr>
                        <td class='tdnowrap'>QUOTATION</td><td class='tdnowrap px-3 text-center'>:</td>
                        <!--<td colspan="5">-->
                        <?php
//                            $model->q_quotation .= " " . $model['currency']['currency_sign'] . " " . ($model->with_sst ? MyFormatter::asDecimal2($totalAmtWithSST) : MyFormatter::asDecimal2($totalAmtWithoutSST));
                        ?>
                        <?php //= $form->field($model, 'q_quotation', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        <!--</td>-->
                        <td colspan="5">
                            <?php
                            if ($isTKTKM) {
                                $totalAmt = $totalTKAmountWTax + $totalTKMAmount;
                            } else {
                                $totalAmt = $model->with_sst ? $totalAmtWithSST : $totalAmtWithoutSST;
                            }
                            ?>

                            <?=
                            $form->field($model, 'q_quotation', [
                                'options' => ['class' => 'p-0 m-0']
                            ])->textInput([
                                'maxlength' => true,
                                'readonly' => true,
                                'id' => 'quotation-total',
                                'value' => $model['currency']['currency_sign'] . ' ' . MyFormatter::asDecimal2($totalAmt)
                            ])
                            ?>
                        </td>

                    </tr>
                    <tr>
                        <td>DELIVERY</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td style="width:15%">
                            <?php
                            echo $form->field($model, "q_delivery_ship_mode")->widget(yii\jui\AutoComplete::className(), [
                                'clientOptions' => [
                                    'source' => RefProjectQShippingMode::getAutocompleteList(),
                                    'minLength' => '0',
                                    'autoFill' => true,
                                    'delay' => 10,
                                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                                ],
                                'options' => ['class' => 'form-control', 'placeholder' => 'Shipping Mode']
                            ]);
                            ?>
                        </td>
                        <td style="width: 1%">-</td>
                        <td style="width:20%">
                            <?= $form->field($model, 'q_delivery_destination')->textInput(['maxlength' => true, 'placeholder' => 'Destination'])->label("&nbsp;") ?>
                        </td>
                        <td style="width: 1%">-</td>
                        <td>
                            <?= $form->field($model, 'q_delivery', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>VALIDITY</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td colspan="5">  
                            <?= $form->field($model, 'q_validity', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>PAYMENT</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td colspan="5">   
                            <?= $form->field($model, 'q_payment', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='font-weight: bold'>REMARK</td><td class='tdnowrap px-3 text-center font-weight-bold'>:</td>
                        <td colspan="5"> 
                            <?= $form->field($model, 'q_remark', ['options' => ['class' => 'p-0 m-0']])->textarea(['rows' => 6]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='7'>
                            <p>We trust that our offer would meet your requirement and looking forward to receiving your favourable reply soon.</p>
                        </td>
                    </tr>

                </table>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton('Generate PDF', ['class' => 'btn btn-success submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<script>
//    $(document).ready(function () {
//        function fetchClientInfo() {
//            let clientName = $('#client-name').val();
//            if (clientName.trim() === '')
//                return;
//            $.ajax({
//                url: '<?= \yii\helpers\Url::to(['client/get-client-info']) ?>',
//                type: 'GET',
//                data: {
//                    id: '<?= $client->id ?>',
//                    name: clientName
//                },
//                success: function (response) {
//                    if (response.success) {
//                        $('#client-contact').val(response.data.contact_number || '');
//                        $('#client-fax').val(response.data.fax || '');
//                    } else {
//                        $('#client-contact').val('');
//                        $('#client-fax').val('');
//                    }
//                },
//                error: function () {
//                    console.log('Error retrieving client info.');
//                }
//            });
//        }
//        fetchClientInfo();
//        $('#client-name').on('change', fetchClientInfo);
//    });

    $(function () {
        $("#quotationpdfmasters-q_delivery_ship_mode").focus(function () {
            $(this).autocomplete("search", "");
        });
        $("#quotationpdfmasters-with_sst").click(function (e) {
            checkSST();
        });
        $("#quotationpdfmasters-show_breakdown").click(function (e) {
            checkBreakDetail();
        });
        $("#quotationpdfmasters-show_breakdown_price").click(function (e) {
            checkBreakDetailPrice();
        });
        $("#quotationpdfmasters-show_panel_description").click(function (e) {
            checkPanelDesc();
        });

        checkSST();
        checkBreakDetail();
        checkPanelDesc();

    });


//    function checkSST() { //before tktkm
//        if ($('#quotationpdfmasters-with_sst').is(":checked")) {
//            $(".colSST").show();
//            $(".colNoSST").hide();
//
//        } else {
//            $(".colSST").hide();
//            $(".colNoSST").show();
//        }
//    }

    $(document).ready(function () {
        $('#<?= Html::getInputId($model, 'with_sst') ?>').on('change', function () {
            const isChecked = $(this).is(':checked');
            const isTKTKM = <?= json_encode($isTKTKM) ?>;
            const currency = <?= json_encode($model['currency']['currency_sign']) ?>;

            const totalTKAmountWTax = <?= json_encode($totalTKAmountWTax) ?>;
            const totalTKMAmount = <?= json_encode($totalTKMAmount) ?>;
            const totalTKAmountNoTax = <?= json_encode($totalTKAmountNoTax) ?>;
            const totalAmtWithSST = <?= json_encode($totalAmtWithSST) ?>;
            const totalAmtWithoutSST = <?= json_encode($totalAmtWithoutSST) ?>;

            let total = 0;
            if (isTKTKM) {
                total = isChecked
                        ? totalTKAmountWTax + totalTKMAmount
                        : totalTKAmountNoTax + totalTKMAmount;
            } else {
                total = isChecked ? totalAmtWithSST : totalAmtWithoutSST;
            }

            $('#quotation-total').val(currency + ' ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        });
    });

    function checkSST() {
        const isTKTKM = <?= json_encode($isTKTKM) ?>; // Pass PHP value to JS

        if (isTKTKM) {
            if ($('#quotationpdfmasters-with_sst').is(":checked")) {
                // SST enabled
                $(".tk-tax-colSST").show();
                $(".tk-tax-colNoSST").hide();

                // TK header = 3 cols, TKM = 2 cols
                $(".tk-header").attr("colspan", 3);
                $(".tkm-header").attr("colspan", 2);

                // Restore TKM <td>
                $(".tkm-col").show();
            } else {
                // SST disabled
                $(".tk-tax-colSST").hide();
                $(".tk-tax-colNoSST").show();

                // TK header = 2 cols, TKM = 2 cols (but visually shifts left)
                $(".tk-header").attr("colspan", 2);
                $(".tkm-header").attr("colspan", 2);

                // Hide empty placeholder <td> if needed
                $(".tkm-col").show();
            }
        } else {
            // Non-TKTKM old behavior
            if ($('#quotationpdfmasters-with_sst').is(":checked")) {
                $(".colSST").show();
                $(".colNoSST").hide();
            } else {
                $(".colSST").hide();
                $(".colNoSST").show();
            }
        }
    }

    function checkBreakDetail() {
        if ($('#quotationpdfmasters-show_breakdown').is(":checked")) {
            $(".breakdownItem").show();
        } else {
            if ($('#quotationpdfmasters-show_breakdown_price').is(":checked")) {
                $('#quotationpdfmasters-show_breakdown_price').prop('checked', false);
            }
            $(".breakdownItem").hide();
        }
    }
    function checkBreakDetailPrice() {
        if ($('#quotationpdfmasters-show_breakdown_price').is(":checked")) {
            if (!$('#quotationpdfmasters-show_breakdown').is(":checked")) {
                $('#quotationpdfmasters-show_breakdown').prop('checked', true);
                $(".breakdownItem").show();
            }
            $(".bdItemPrice").show();
            $(".no-bdItemPrice").hide();
        } else {
            $(".bdItemPrice").hide();
            $(".no-bdItemPrice").show();
        }
    }

    function checkPanelDesc() {
        if ($('#quotationpdfmasters-show_panel_description').is(":checked")) {
            $(".panelDescription").show();
        } else {
            $(".panelDescription").hide();
        }
    }

</script>