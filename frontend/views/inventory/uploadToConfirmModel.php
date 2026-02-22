<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = 'Confirm and Submit Model Details';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Model', 'url' => ['model-list']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h4><?= Html::encode($this->title) ?></h4>

<?php $form = ActiveForm::begin(['action' => ['save-model-details']]); ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;">#</th>
            <th>Model Type</th>
            <th>Brand</th>
            <th>Description</th>
            <th>Group</th>
            <th>Unit Type</th>
            <th>Stock On Hand</th>
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
                    <?= Html::input('text', "InventoryModel[type][$index]", $row['name'], ['class' => 'form-control', 'required' => true]) ?>
                    <?php if (isset($errors[$index])): ?>
                        <small class="text-danger"><strong><?= $errors[$index] ?></strong></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryModel[inventory_brand_id][$index]", $row['brand'], ['class' => 'form-control', 'required' => true]) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryModel[description][$index]", $row['desc'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryModel[group][$index]", $row['group'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryModel[unit_type][$index]", $row['unitType'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryModel[total_stock_on_hand][$index]", $row['stockonhand'], ['class' => 'form-control']) ?>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm delete-row">
                        <i class="far fa-trash-alt"></i>
                    </button>
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