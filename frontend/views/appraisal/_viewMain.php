<table class="table table-bordered table-striped table-hover m-0 mt-2 col-12 rounded">
    <thead>
        <tr class="text-primary">
            <th @click="sortTable('index')" class="search-hover col-1">Reference No.</th>
            <th @click="sortTable('description')" class="search-hover">Description</th>
            <!--<th @click="sortTable('status_name')" class="search-hover">Status</th>-->
            <th @click="sortTable('appraisal_start_date')" class="search-hover">Appraisal Start Date</th>
            <th @click="sortTable('appraisal_end_date')" class="search-hover">Appraisal End Date</th>
            <th @click="sortTable('rating_end_date')" class="search-hover">Rating End Date</th>
            <th @click="sortTable('created_at')" class="search-hover">Created At</th>
            <th>Action</th>
        </tr>
        <tr>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.index"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.description"></th>
<!--            <th class="p-1">
                <select class="form-control" v-model="searchCriteria.status_name">
                    <option value="">Select Status</option>
            <?php foreach ($statusOptions as $key => $statusName): ?>
                            <option value="<?= $key ?>"><?= $statusName ?></option>
            <?php endforeach; ?>
                </select>
            </th>-->
            <th class="p-1">
                <input type="text" id="appraisal_start_date-datepicker" class="form-control" v-model="searchCriteria.appraisal_start_date" @click="showDatePicker('appraisal_start_date')" placeholder="Select Date">
            </th>
            <th class="p-1">
                <input type="text" id="appraisal_end_date-datepicker" class="form-control" v-model="searchCriteria.appraisal_end_date" @click="showDatePicker('appraisal_end_date')" placeholder="Select Date">
            </th>
            <th class="p-1">
                <input type="text" id="rating_end_date-datepicker" class="form-control" v-model="searchCriteria.rating_end_date" @click="showDatePicker('rating_end_date')" placeholder="Select Date">
            </th>
            <th class="p-1">
                <input type="text" id="created_at-datepicker" class="form-control" v-model="searchCriteria.created_at" @click="showDatePicker('created_at')" placeholder="Select Date">
            </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="model in filteredModels" :key="model.id">
            <td class="p-1 text-center">{{ model.index }}</td>
            <td class="p-1">{{ model.description }}</td>
            <!--<td class="p-1">{{ model.status_name }}</td>-->
            <td class="p-1">{{ formatDateDMY(model.appraisal_start_date) }}</td>
            <td class="p-1">{{ formatDateDMY(model.appraisal_end_date) }}</td>
            <td class="p-1">{{ formatDateDMY(model.rating_end_date) }}</td>
            <td class="p-1">{{ formatDateDMY(model.created_at) }}</td>
            <?php if ($super) { ?>
                <td class="p-1 text-center"><a :href="'/appraisalgnrl/index-review?id=' + model.id" class="btn btn-sm btn-primary" title="Click to view">View</a></td>
            <?php } else { ?>
                <td class="p-1 text-center"><a :href="'/appraisal/index-master?id=' + model.id" class="btn btn-sm btn-primary" title="Click to view">View</a></td>
            <?php } ?>
        </tr>
    </tbody>
</table>
