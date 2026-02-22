<table class="table table-bordered table-striped table-hover m-2 col-12 rounded">
    <thead>
        <tr class="text-primary">
            <th @click="sortTable('staff_id')" class="search-hover">Staff ID</th>
            <th @click="sortTable('fullname')" class="search-hover">Fullname</th>
            <th @click="sortTable('date_of_join')" class="search-hover">Date of Join</th>
            <th @click="sortTable('staff_type')" class="search-hover">Staff Type</th>
            <th @click="sortTable('employment_type')" class="search-hover">Employment Type</th>
            <th class="text-dark">Checkbox</th>
        </tr>
        <tr>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.staff_id" type="number"></th>
            <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
            <th class="p-1">
                <input type="text" id="date_of_join-datepicker" class="form-control" v-model="searchCriteria.date_of_join" @click="showDatePicker('date_of_join')" placeholder="Select Date">
            </th>
            <th class="p-1">
                <select class="form-control" v-model="searchCriteria.staff_type">
                    <option value="">All</option>
                    <option value="prod">Production</option>
                    <option value="exec">Executive</option>
                    <option value="office">Office</option>
                </select>
            </th>
            <th class="p-1">
                <select class="form-control" v-model="searchCriteria.employment_type">
                    <option value="">All</option>
                    <?php foreach ($employmentTypeList as $key => $typeName): ?>
                        <option value="<?= $key ?>"><?= $typeName ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <th class='text-center p-1' style="vertical-align: middle;"><input class="big-checkbox" type="checkbox" v-model="selectAll" @change="selectAllItems"></th>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr v-for="user in filteredModels" :key="user.id">
            <td class="p-1">{{ user.staff_id }}</td>
            <td class="p-1">{{ user.fullname }}</td>
            <td class="p-1">{{ formatDateDMY(user.date_of_join) }}</td>
            <td class="p-1">{{ user.staff_type }}</td>
            <td class="p-1">{{ user.employment_type }}</td>
            <td class="text-center p-1">
                <input class="big-checkbox" type="checkbox" v-model="user.isSelected" @change="updateSelectedIds(user)">
            </td>
        </tr>
    </tbody>
</table>
