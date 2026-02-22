<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\bomdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$bomMaster = BomMaster::findOne($bomMasterId);
$this->title = 'Confirm and Submit Fault Details';
?>


<?php
$this->params['breadcrumbs'][] = ['label' => 'Fault Lists', 'url' => ['index']];
//$panel = $bomMaster->productionPanel;
//$production = $panel->projProdMaster;
//$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
//$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/production/production/view-production-main', 'id' => $production->id]];
//$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/bom/index', 'productionPanelId' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h4><?= Html::encode($this->title) ?></h4>

<?php $form = ActiveForm::begin(['action' => ['save-fault-details']]); ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;">#</th>
            <th>Asset ID</th>
            <th class="col-1">Fault Type</th>
            <th>Primary Description</th>
            <th class="col-1">Secondary Description</th>
            <th>Fault Priority</th>
            <th>Remark</th>
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
                    <?= Html::input('text', "CmmsFaultList[fault_asset_id][$index]", $row['assetId'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsFaultList[fault_type][$index]", $row['fault_type'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsFaultList[fault_primary_detail][$index]", $row['primary_description'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsFaultList[fault_secondary_detail][$index]", $row['secondary_description'], ['class' => 'form-control text-right']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsFaultList[machine_priority_id][$index]", $row['fault_priority'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "CmmsFaultList[additional_remarks][$index]", $row['remark'], ['class' => 'form-control']) ?>
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