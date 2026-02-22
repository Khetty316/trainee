<?php

use yii\helpers\Html;
use frontend\models\projectproduction\task\TaskAssignment;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */
/* @var $form yii\widgets\ActiveForm */
// Example lists
$selectedStaffIds = $selectedStaffIds ?? [];

$userModel = new \common\models\User;
$staffListFab = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeFabrication);

$staffListElec = $userModel->getStaffList_productionAssignee(TaskAssignment::taskTypeElectrical);

$staffListFabElec = array_merge($staffListFab, $staffListElec);

$unique = [];
$staffListFabElec = array_filter($staffListFabElec, function ($staff) use (&$unique) {
    if (in_array($staff['id'], $unique)) {
        return false;
    }
    $unique[] = $staff['id'];
    return true;
});

$staffListFabElec = array_values($staffListFabElec);

// Sort alphabetically by fullname (case-insensitive)
usort($staffListFabElec, function ($a, $b) {
    return strcasecmp($a['fullname'], $b['fullname']);
});
?>

<div class="prod-ot-meal-record-master-form">

    <div class="row">
        <div class="col-3">
            <?=
                    $form->field($detail, "receipt_date", ['options' => ['class' => 'mb-0']])
                    ->input('date', [
                        'class' => 'form-control',
                        'value' => ($detail->receipt_date ? date('Y-m-d', strtotime($detail->receipt_date)) : null) ?? date('Y-m-d')
                    ])
            ?>

        </div>
        <div class="col-3">
            <?=
                    $form->field($detail, "receipt_total_amount", ['options' => ['class' => 'mb-0']])
                    ->input('number', [
                        'class' => 'form-control text-right receipt-amount',
                        'step' => 'any',
                        'min' => '0.01',
                        'value' => number_format($detail->receipt_total_amount, 2),
                    ])
            ?>
        </div>
        <div class="col-2">
            <?=
            $form->field($detail, 'total_staff')->textInput(['class' => 'form-control text-right', 'value' => ($detail->total_staff ?? 0), 'readonly' => true]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Selected Staff:</legend>

                <table class="table table-sm table-striped table-bordered" id="selectedStaffTable">
                    <thead>
                        <tr>
                            <th class="w-5">Staff ID</th>
                            <th class="w-90">Fullname</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Staff:</legend>

                <div class="input-group mb-2">
                    <input class="form-control mr-2" id="nameFilter" type="text" placeholder="Search.."/>

                    <select id="departmentSelect" class="form-control w-auto">
                        <option value="all">All</option>
                        <option value="fabrication">Fabrication</option>
                        <option value="electrical">Electrical</option>
                    </select>
                </div>

                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-sm table-striped table-bordered" id="myList">
                        <thead>
                            <tr>
                                <th class="col-10">Staff name</th>
                                <th class="tdnowrap col-1 text-center">
                                    Select All<br>
                                    <?=
                                    Html::checkbox('selection_all', false, [
                                        'id' => 'select-all-checkbox',
                                        'style' => 'transform: scale(1.5); margin:0; cursor:pointer;',
//                                        'class' => 'form-control form-control-sm'
                                    ])
                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right mb-3']) ?>
            </div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('form').on('submit', function (e) {
        const total = $('input.staff-checkbox:checked').length;

        if (total === 0) {
            e.preventDefault();
            myAlert('Please select at least one staff before saving.');
        }
    });

    const staffData = {
        fabrication: <?= json_encode($staffListFab) ?>,
        electrical: <?= json_encode($staffListElec) ?>,
        all: <?= json_encode($staffListFabElec) ?>
    };

    function populateTable(dept, filter = '') {
    const list = staffData[dept];
    const tbody = $('#myList tbody');
    tbody.empty();

    if (!list || list.length === 0) {
        tbody.append('<tr><td colspan="2" class="text-center">No staff found</td></tr>');
        return;
    }

    list.forEach(staff => {
        const name = staff.fullname ?? '';
        const id = staff.id ?? '';
        const staffId = staff.staff_id ?? '';
        const isChecked = <?= json_encode($selectedStaffIds) ?>.includes(id.toString()) ? 'checked' : '';

        if (name.toLowerCase().includes(filter.toLowerCase())) {
            tbody.append(`
                <tr>
                    <td>${name}</td>
                    <td class="text-center">
                        <input type="checkbox" class="staff-checkbox"
                               name="selectedStaff[]" 
                               value="${id}"
                               data-name="${name}" 
                               data-staffid="${staffId}"
                               ${isChecked}
                               style="transform: scale(1.5); cursor:pointer;">
                    </td>
                </tr>
            `);
        }
    });

    // add this line to always refresh left table after population
    updateSelectedStaffTable();
}

    function updateSelectedStaffTable() {
        const tbody = $('#selectedStaffTable tbody');
        tbody.empty();

        $('input.staff-checkbox:checked').each(function () {
            const id = $(this).val();              // internal ID
            const staffId = $(this).data('staffid'); // displayed staff ID
            const name = $(this).data('name');
            tbody.append(`<tr><td>${staffId}</td><td>${name}</td></tr>`);
        });

        // Update total staff count
        const total = $('input.staff-checkbox:checked').length;
        $('#prodotmealrecorddetail-total_staff').val(total);
    }

// Initial load
    populateTable('all');

// Department dropdown change
    $('#departmentSelect').on('change', function () {
        populateTable($(this).val(), $('#nameFilter').val());
        $('#select-all-checkbox').prop('checked', false);
        updateSelectedStaffTable();
    });

// Search filter
    $('#nameFilter').on('keyup', function () {
        populateTable($('#departmentSelect').val(), $(this).val());
        $('#select-all-checkbox').prop('checked', false);
        updateSelectedStaffTable();
    });

// When individual checkbox changes
    $(document).on('change', '.staff-checkbox', function () {
        updateSelectedStaffTable();

        // If any unchecked, uncheck "Select All"
        if (!$(this).is(':checked')) {
            $('#select-all-checkbox').prop('checked', false);
        }
    });

// Select All
    $(document).on('change', '#select-all-checkbox', function () {
        const isChecked = $(this).is(':checked');
        $('.staff-checkbox').prop('checked', isChecked);
        updateSelectedStaffTable();
    });
</script>


