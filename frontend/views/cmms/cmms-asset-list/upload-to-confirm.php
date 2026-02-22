<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\bomdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$bomMaster = BomMaster::findOne($bomMasterId);
$this->title = 'Confirm and Submit Asset Details';
?>


<?php
$this->params['breadcrumbs'][] = ['label' => 'Asset Lists', 'url' => ['index']];
//$panel = $bomMaster->productionPanel;
//$production = $panel->projProdMaster;
//$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
//$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/production/production/view-production-main', 'id' => $production->id]];
//$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/bom/index', 'productionPanelId' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h4><?= Html::encode($this->title) ?></h4>

<?php $form = ActiveForm::begin(['action' => ['save-asset-details']]); ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;">#</th>
            <th>Asset ID</th>
            <th class="col-1">Area</th>
            <th>Section</th>
            <th class="col-1">Asset Name</th>
            <th>Manufacturer</th>
            <th>Serial no.</th>
            <th>Date of purchase</th>
            <th>Date of installation</th>
            <th style="width: 1%;">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($buffer as $index => $row): ?>
            <tr>

                <td class="text-right px-2 pt-1">
                    <?= $index + 1 ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[asset_id][$index]", $row['assetId'], ['class' => 'form-control', 'required' => true]) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[area][$index]", $row['area'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[section][$index]", $row['section'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[name][$index]", $row['name'], ['class' => 'form-control text-right']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[manufacturer][$index]", $row['manufacturer'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsAssetList[serial_no][$index]", $row['serial_no'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?php
                        $v = $row['date_of_purchase'] ?? null;

                        if (is_numeric($v)) {
                            $v = ExcelDate::excelToDateTimeObject((float)$v)->format('Y-m-d');
                        } elseif ($v) {
                            // DB datetime string
                            $v = substr((string)$v, 0, 10); // 'YYYY-MM-DD'
                        } else {
                            $v = null;
                        }
                    ?>
                    <?= Html::input('date', "CmmsAssetList[date_of_purchase][$index]", $v, ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?php 
                        $v2 = $row['date_of_installation'] ?? null;
                  
                        if (is_numeric($v2)) {
                            $v2 = ExcelDate::excelToDateTimeObject((float)$v2)->format('Y-m-d');
                        } elseif ($v) {
                            // DB datetime string
                            $v2 = substr((string)$v2, 0, 10); // 'YYYY-MM-DD'
                        } else {
                            $v2 = null;
                        }
                    ?>
                    <?= Html::input('date', "CmmsAssetList[date_of_installation][$index]", $v2, ['class' => 'form-control']) ?>
                </td>
                <td class="text-center" >
                    <button type="button" class="btn btn-danger btn-sm delete-row"><i class="far fa-trash-alt"></i></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="form-group">
    <?= Html::submitButton('Save to Database', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $('td').addClass('p-0');

        $('.table').on('click', '.delete-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>