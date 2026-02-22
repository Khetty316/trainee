<table class="table table-bordered table-striped table-hover m-0 mt-2 col-12 rounded">
    <thead>
        <tr class="text-primary">
            <th @click="sortTable('staff_id')" class="search-hover">Staff Id</th>
            <th @click="sortTable('fullname')" class="search-hover col-2">Fullname</th>
            <th @click="sortTable('avail_workday')" class="search-hover">Working Days</th>
            <th @click="sortTable('perfect')" class="search-hover">Perfect</th>
            <th @click="sortTable('total_days')" class="search-hover">Total Days</th>
            <th @click="sortTable('total_present')" class="search-hover">Total Present</th>
            <th @click="sortTable('workday_present')" class="search-hover">WP</th>
            <th @click="sortTable('unpaid_leave_present')" class="search-hover">OP</th>
            <th @click="sortTable('rest_holiday_present')" class="search-hover">R/HP</th>
            <th @click="sortTable('absent')" class="search-hover">AB</th>
            <th @click="sortTable('leave_taken')" class="search-hover">LV</th>
            <th @click="sortTable('late_in')" class="search-hover">LI</th>
            <th @click="sortTable('early_out')" class="search-hover">EO</th>
            <th @click="sortTable('miss_punch')" class="search-hover">MP</th>
            <th @click="sortTable('short')" class="search-hover">Short</th>
            <!--<th @click="sortTable('sche')" class="search-hover">Sche</th>-->
            <th @click="sortTable('workday')" class="search-hover">Workday</th>
            <th @click="sortTable('workday_ot')" class="search-hover">Workday OT</th>
            <th @click="sortTable('holiday')" class="search-hover">Holiday</th>
            <th @click="sortTable('holiday_ot')" class="search-hover">Holiday OT</th>
            <th @click="sortTable('restday')" class="search-hover">Restday</th>
            <th @click="sortTable('restday_ot')" class="search-hover">Restday OT</th>
            <th @click="sortTable('unpaid_leave')" class="search-hover">Unpaid Leave</th>
            <th @click="sortTable('unpaid_leave_ot')" class="search-hover">Unpaid Leave OT</th>
        </tr>
        <tr>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.staff_id"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.avail_workday"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.perfect"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.total_days"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.total_present"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.workday_present"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.unpaid_leave_present"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.rest_holiday_present"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.absent"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.leave_taken"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.late_in"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.early_out"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.miss_punch"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.short"></th>
            <!--<th class="p-1"><input class="form-control" v-model="searchCriteria.sche"></th>-->
            <th class="p-1"><input class="form-control" v-model="searchCriteria.workday"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.workday_ot"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.holiday"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.holiday_ot"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.restday"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.restday_ot"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.unpaid_leave"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.unpaid_leave_ot"></th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="model in filteredModels" :key="model.id" class="text-right">
            <td class="p-1 text-center">{{ model.staff_id }}</td>
            <td class="p-1 text-left">
                <a :value="'/attendance/view?id=' + model.id" class="modalButton search-hover" title="Click to view" data-modaltitle="Attendance Details">{{ model.fullname }}</a>
            </td>
            <td class="p-1">{{ model.avail_workday }}</td>
            <td class="p-1">{{ model.perfect }}</td>
            <td class="p-1">{{ model.total_days }}</td>
            <td class="p-1">{{ model.total_present }}</td>
            <td class="p-1">{{ model.workday_present }}</td>
            <td class="p-1">{{ model.unpaid_leave_present }}</td>
            <td class="p-1">{{ model.rest_holiday_present }}</td>
            <td class="p-1">{{ model.absent }}</td>
            <td class="p-1">{{ model.leave_taken }}</td>
            <td class="p-1">{{ model.late_in }}</td>
            <td class="p-1">{{ model.early_out }}</td>
            <td class="p-1">{{ model.miss_punch }}</td>
            <td class="p-1">{{ model.short }}</td>
            <!--<td class="p-1">{{ model.sche }}</td>-->
            <td class="p-1">{{ model.workday }}</td>
            <td class="p-1">{{ model.workday_ot }}</td>
            <td class="p-1">{{ model.holiday }}</td>
            <td class="p-1">{{ model.holiday_ot }}</td>
            <td class="p-1">{{ model.restday }}</td>
            <td class="p-1">{{ model.restday_ot }}</td>
            <td class="p-1">{{ model.unpaid_leave }}</td>
            <td class="p-1">{{ model.unpaid_leave_ot }}</td>
        </tr>
    </tbody>
</table>