<?php

use yii\bootstrap4\Html;

$this->title = "Equipment List";
$this->params['breadcrumbs'][] = "Preventive Maintenance";
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .no-wrap {
        white-space: nowrap;
        overflow: visible;
    }
</style>

<h3>Computerised Maintenance Management System</h3>
<div>
    <?=
    Html::a('Add Equipment <i class="fa fa-plus"></i>', "javascript:", [
        'title' => "Add a new equipment",
        "value" => yii\helpers\Url::to(['add-equipment']),
        "class" => "modalButton btn btn-success",
        'data-modaltitle' => "Add Table"
    ]);
    ?>
</div>
<div id="app">

    <div class="test-main-index">
        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded" id="maintable">
            <thead>
                <tr>
                    <th @click="sortTable('equipment_code')" class="search-hover text-primary col-3">Equipment Code</th>
                    <th @click="sortTable('equipment_description')" class="search-hover text-primary">Equipment Description</th>
                    <th @click="sortTable('remark')" class="search-hover text-primary">Remark</th>
                    <th @click="sortTable('next_service_date')" class="search-hover text-primary">Next Service Date</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.equipment_code"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.equipment_description"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.remark"></th>
                    <th class="p-1">
                        <input type="text" id="next_service_date-datepicker" class="form-control" v-model="searchCriteria.next_service_date" @click="showDatePicker('next_service_date')" placeholder="Select Date">
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="model in paginatedModels" :key="model.id">
                    <td class="p-1">{{ model.equipment_code }}</td>
                    <td class="p-1">{{ model.equipment_description }}</td>
                    <td class="p-1">{{ model.remark }}</td>
                    <td class="p-1">{{ formatDateDMY(model.next_service_date) }}</td>
                    <td class="p-1 text-center">
                        <a href="javascript:void(0);" class="modalButtonSingle btn btn-sm btn-success" style="display:none;" :id="'updateEquip-'+model.id"  :value="'/maintenance/update?id='+model.id">Update</a>
                        <a href="javascript:void(0);" class="modalButtonSingle btn btn-sm btn-primary" title="Click to View" :value="'/maintenance/view?id='+model.id" data-modaltitle="Equipment Details">View</a>
                        <a href="javascript:void(0);" class="modalButtonSingle btn btn-sm btn-warning" style="display:none;" :id="'duplicateEquip-'+model.id"  :value="'/maintenance/duplicate?id='+model.id" data-modaltitle="Duplicate Equipment">Duplicate</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination my-3 flex justify-content-end">
            <button class="btn btn-sm btn-primary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
            <span class="pt-1">&nbsp;{{ currentPage }} / {{ totalPages }}&nbsp;</span>
            <button class="btn btn-sm btn-primary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>

    </div>
</div>

<script>
    window.models = <?= $modelsArray ?>;
    window.numPerPage = 20;

    function closeAndUpdate(modelId) {
        $('.close').click();
        setTimeout(function () {
            $('#updateEquip-' + modelId).click();
        }, 500);
    }

    function closeAndDuplicate(modelId) {
        $('.close').click();
        setTimeout(function () {
            $('#duplicateEquip-' + modelId).click();
        }, 500);
    }

    $(document).on('click', '.modalButtonSingle', function () {
        if ($(this).attr('data-modaltitle')) {
            $('#myModal').find('p.modal-title').text($(this).attr('data-modaltitle'));
        }
        $('#myModal').modal('show').find('#myModalContent').load($(this).attr('value'));
    });



</script>
<script src="\js\vueTable.js"></script>
