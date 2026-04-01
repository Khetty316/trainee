<?php
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
    $url = 'inventory/inventory/supplier-list?type=execStock';
    $url2 = 'inventory/inventory/add-by-template-supplier?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
    $url = 'inventory/inventory/supplier-list?type=assistStock';
    $url2 = 'inventory/inventory/add-by-template-supplier?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
    $url = 'inventory/inventory/supplier-list?type=projcoorStock';
    $url2 = 'inventory/inventory/add-by-template-supplier?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
    $url = 'inventory/inventory/supplier-list?type=maintenanceHeadStock';
    $url2 = 'inventory/inventory/add-by-template-supplier?type=maintenanceHeadStock';
}

$this->title = 'Confirm and Submit Supplier Details';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Supplier List', 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => 'Upload Template', 'url' => [$url2]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h4><?= Html::encode($this->title) ?></h4>
<?php $form = ActiveForm::begin(['action' => ['save-supplier-details', 'type' => $moduleIndex]]); ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;">#</th>
            <th>Name</th>
            <th>Address 1</th>
            <th>Address 2</th>
            <th>Address 3</th>
            <th>Address 4</th>
            <th>Contact Name</th>
            <th>Contact Number</th>
            <th>Contact Email</th>
            <th>Contact Fax</th>
            <th>Agent Terms</th>
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
                    <?= Html::input('text', "InventorySupplier[name][$index]", $row['name'], ['class' => 'form-control', 'required' => true]) ?>
                    <?php if (isset($errors[$index])): ?>
                        <small class="text-danger"><strong><?= $errors[$index] ?></strong></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[address1][$index]", $row['addr1'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[address2][$index]", $row['addr2'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[address3][$index]", $row['addr3'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[address4][$index]", $row['addr4'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[contact_name][$index]", $row['ctcName'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[contact_number][$index]", $row['ctcNo'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[contact_email][$index]", $row['ctcEmail'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[contact_fax][$index]", $row['ctcFax'], ['class' => 'form-control']) ?>
                </td>
                <td>
                    <?= Html::input('text', "InventorySupplier[agent_terms][$index]", $row['agentTerms'], ['class' => 'form-control']) ?>
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