<?php

use yii\bootstrap4\Html;
use frontend\models\common\RefAppraisalStatus;
?>
<div id="app">
    <div class="row m-0 p-0">
        <?php
        if ($staff) {
            echo Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']);
        } else {
            echo Html::a('Reset Filter <i class="fas fa-search-minus"></i>', "?id=$main->id", ['class' => 'btn btn-primary']);
        }
        ?>
    </div>
    <div>
        <table class="table table-bordered table-striped table-hover m-0 mt-2 col-12 rounded">
            <thead>
                <tr class="text-primary">
                    <?php if (!$staff) { ?>
                        <th @click="sortTable('staff_id')" class="col-1 search-hover">Staff ID</th>
                        <th @click="sortTable('fullname')" class="search-hover">Staff Appraised</th>
                    <?php } else { ?>
                        <th @click="sortTable('index')" class="col-1 search-hover">Reference No.</th>
                        <th @click="sortTable('description')" class="search-hover">Description</th>
                    <?php } ?>
                    <th @click="sortTable('appraisal_sts_name')" class="search-hover">Status</th>
                    <th @click="sortTable('staff_remark')" class="search-hover">Staff Remark</th>
                    <th @click="sortTable('overall_rating')" class="search-hover">Overall Rating</th>
                    <th @click="sortTable('overall_review')" class="search-hover">Overall Review</th>
                    <th @click="sortTable('created_at')" class="search-hover">Date</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <?php if (!$staff) { ?>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.staff_id"></th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
                    <?php } else { ?>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.index"></th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.description"></th>
                    <?php } ?>
                    <th class="p-1">
                        <select class="form-control" v-model="searchCriteria.appraisal_sts_name">
                            <option value="">Select Status</option>
                            <?php foreach ($statusOptions as $key => $statusName): ?>
                                <option value="<?= $key ?>"><?= $statusName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.staff_remark"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.overall_rating"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.overall_review"></th>
                    <th class="p-1">
                        <input type="text" id="created_at-datepicker" class="form-control" v-model="searchCriteria.created_at" @click="showDatePicker('created_at')" placeholder="Select Date">
                    </th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="model in filteredModels" :key="model.id">
                    <?php if (!$staff) { ?>
                        <td class="p-1">{{ model.staff_id }}</td>
                        <td class="p-1">{{ model.fullname }}</td>
                    <?php } else { ?>
                        <td class="p-1">{{ model.index }}</td>
                        <td class="p-1">{{ model.description }}</td>
                    <?php } ?>
                    <td class="p-1">{{ model.appraisal_sts_name }}</td>
                    <td class="p-1">{{ model.staff_remark }}</td>
                    <td class="p-1">{{ model.overall_rating }}</td>
                    <td class="p-1">{{ model.overall_review }}</td>
                    <td class="p-1">{{ formatDateDMY(model.created_at) }}</td>
                    <td class="p-1 text-center">
                        <?php if ($staff) { ?>

                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_COMPLETE ?> || model.appraisal_sts == <?= RefAppraisalStatus::STS_WAIT_REVIEW ?>">
                                <a :href="'/appraisalgnrl/view-appraisal?id=' + model.id" class="btn btn-sm btn-primary mr-1">View</a>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_RATED_NOT_CONFIRMED ?>">
                                <a :href="'/appraisalgnrl/begin-staff-appraisal?id=' + model.id" class="btn btn-sm btn-success mr-1">Update</a>
                                <a :href="'/appraisalgnrl/confirm-appraisal?id=' + model.id" class="btn btn-sm btn-success">Confirm</a>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_WAIT_RATING ?>">
                                <a :href="'/appraisalgnrl/begin-staff-appraisal?id=' + model.id" class="btn btn-sm btn-success">Begin</a>
                            </div>

                        <?php } else { ?>

                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_COMPLETE ?>">
                                <a :href="'/appraisalgnrl/view-appraisal?id=' + model.id + '&super=true'" class="btn btn-sm btn-primary mr-1">View</a>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_WAIT_RATING ?>">
                                <span class="text-warning bold">Not Rated</span>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_RATED_NOT_CONFIRMED ?>">
                                <span class="text-warning bold">Not Confirmed</span>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_WAIT_REVIEW ?>">
                                <a :href="'/appraisalgnrl/begin-staff-appraisal?id=' + model.id + '&super=true'" class="btn btn-sm btn-success">Begin</a>
                            </div>
                            <div v-if="model.appraisal_sts == <?= RefAppraisalStatus::STS_REVIEWED_NOT_CONFIRMED ?>">
                                <a :href="'/appraisalgnrl/begin-staff-appraisal?id=' + model.id + '&super=true'" class="btn btn-sm btn-success mr-1">Update</a>
                                <a :href="'/appraisalgnrl/confirm-appraisal-review?id=' + model.id" class="btn btn-sm btn-success" data-confirm="Confirm review?">Confirm</a>
                            </div>

                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>