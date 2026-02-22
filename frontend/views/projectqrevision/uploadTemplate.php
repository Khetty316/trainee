<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use frontend\models\projectquotation\QuotationPdfMasters;
use frontend\models\common\RefProjectQPanelUnit;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisions */


$this->title = "Upload Excel Template";
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->revision_description, 'url' => ['view-project-q-revision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$finalized = $model->projectQType->is_finalized;
$sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;
$amoutBeforeSST = 0;
$SSTAmount = 0;
$totalAmountBeforeSST = 0;
$totalSSTAmount = 0
?>
<div class="project-qrevisions-view">

    <h3>
        <?= Html::encode($this->title) ?>
    </h3>

    <div class="row">
        <div class="col-xs-12 col-xl-6"></div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panels:</legend>


                <?php
                $panels = $model->projectQPanels;
                ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <?php
                            echo Html::a(
                                    'Download Template <i class="fas fa-download"></i>',
                                    yii\helpers\Url::to('@web/template/template-Quotation_Panel.xls'),
                                    [
                                        'class' => 'btn btn-primary mb-5 mt-0',
                                        'download' => 'template - Quotation Panel.xls',
                                        'title' => 'Download Excel Template'
                                    ]
                            );

                            $form = ActiveForm::begin([
                                        'options' => ['enctype' => 'multipart/form-data'],
                            ]);

                            echo Html::fileInput('excelTemplate', null, [
                                'accept' => '.xls',
                            ]);
                            echo "<br/>";
                            echo Html::submitButton(
                                    'Upload Excel <i class="fas fa-upload"></i>',
                                    ['class' => 'btn btn-success mb-2 mt-2']);
                            ActiveForm::end();
                            ?>
                        </div>

                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>




<script>
    $(function () {



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

</script>