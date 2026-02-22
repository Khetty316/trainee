<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\projectproduction\task\TaskAssignment;
?>
<style>
    .no-wrap {
        white-space: nowrap;
        overflow: visible;
    }
    .search-hover:hover {
        cursor: pointer;
        background-color: #f0f0f0;
    }
</style>

<div id="app">
    <div class="defect-list-index">
        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded" id="maintable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th @click="sortTable('task_type')" class="search-hover text-primary" style="width: 120px;">Task Type</th>
                    <th @click="sortTable('panel_code')" class="search-hover text-primary">Panel Code</th>
                    <th @click="sortTable('task_name')" class="search-hover text-primary">Task Name</th>
                    <th @click="sortTable('description')" class="search-hover text-primary">Description</th>
                    <th @click="sortTable('remark')" class="search-hover text-primary">Remark</th>
                    <th @click="sortTable('created_by')" class="search-hover text-primary">Complaint By</th>
                    <th @click="sortTable('created_at')" class="search-hover text-primary no-wrap">Complaint At</th>
                    <th @click="sortTable('is_read')" class="search-hover text-primary">Is Read?</th>
                    <th @click="sortTable('read_at')" class="search-hover text-primary no-wrap">Read At</th>
                    <th class="text-center" style="width: 80px;">Action</th>
                </tr>
                <tr>
                    <th></th>
                    <th class="p-1">
                        <select class="form-control" v-model="searchCriteria.task_type">
                            <option value="">All</option>
                            <option value="elec">Electrical</option>
                            <option value="fab">Fabrication</option>
                        </select>
                    </th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.panel_code"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.task_name"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.description"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.remark"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.created_by"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.created_at"></th>
                    <th class="p-1">
                        <select class="form-control" v-model="searchCriteria.is_read">
                            <option value=""></option>
                            <option value="1">No</option>
                            <option value="2">Yes</option>
                        </select>
                    </th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.read_at"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(model, index) in paginatedModels" :key="model.id" :class="{ 'text-danger': model.is_read == 1 }">
                    <td class="p-1 text-center">{{ (currentPage - 1) * numPerPage + index + 1 }}</td>
                    <td class="p-1">{{ model.task_type == 'elec' ? 'Electrical' : 'Fabrication' }}</td>
                    <td class="p-1">{{ model.panel_code }}</td>
                    <td class="p-1">{{ model.task_name }}</td>
                    <td class="p-1" style="white-space: pre-wrap;">{{ model.description }}</td>
                    <td class="p-1" style="white-space: pre-wrap;">{{ model.remark }}</td>
                    <td class="p-1">{{ model.created_by }}</td>
                    <td class="p-1 no-wrap">{{ model.created_at }}</td>
                    <td class="p-1 text-center">
                        <i v-if="model.is_read == 1" class="far fa-times-circle text-danger"></i>
                        <i v-else class="far fa-check-circle text-success"></i>
                    </td>
                    <td class="p-1 no-wrap">{{ model.read_at }}</td>
                    <td class="p-1 text-center no-wrap">
                        <a v-if="model.task_type == 'elec'" 
                           href="javascript:void(0)" 
                           :value="'<?= Url::to(['ajax-task-elec-defect-detail']) ?>?id=' + model.task_assign_id + '&complaintId=' + model.id" 
                           :class="model.is_read == 1 ? 'btn btn-sm btn-warning modalButton' : 'btn btn-sm btn-primary modalButton'"
                           title="View Details">
                            {{ model.is_read == 1 ? 'Mark as Read' : 'View Detail' }}
                        </a>
                        <a v-else 
                           href="javascript:void(0)" 
                           :value="'<?= Url::to(['ajax-task-fab-defect-detail']) ?>?id=' + model.task_assign_id + '&complaintId=' + model.id" 
                           :class="model.is_read == 1 ? 'btn btn-sm btn-warning modalButton' : 'btn btn-sm btn-primary modalButton'"
                           title="View Details">
                            {{ model.is_read == 1 ? 'Mark as Read' : 'View' }}
                        </a>
                    </td>
                </tr>         
                <tr v-if="paginatedModels.length === 0">
                    <td colspan="11" class="text-center p-3">No defects found</td>
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
    window.models = <?= $defectLists ?>;
    window.numPerPage = 15;
</script>
<script src="/js/vueTable.js"></script>